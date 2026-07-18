<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Services\WeatherService;
use App\Services\WorldBankService;
use App\Services\ExchangeRateService;
use App\Services\NewsSentimentService;
use App\Services\RiskScoringService;
use Illuminate\Support\Facades\DB;

class ComparisonController extends Controller
{
    protected $countryService;
    protected $weatherService;
    protected $worldBankService;
    protected $exchangeRateService;
    protected $newsSentimentService;
    protected $riskScoringService;

    public function __construct(
        CountryService $countryService, 
        WeatherService $weatherService,
        WorldBankService $worldBankService,
        ExchangeRateService $exchangeRateService,
        NewsSentimentService $newsSentimentService,
        RiskScoringService $riskScoringService
    ) {
        $this->countryService = $countryService;
        $this->weatherService = $weatherService;
        $this->worldBankService = $worldBankService;
        $this->exchangeRateService = $exchangeRateService;
        $this->newsSentimentService = $newsSentimentService;
        $this->riskScoringService = $riskScoringService;
    }

    public function index(Request $request)
    {
        $country1Name = $request->query('country1', 'Germany');
        $country2Name = $request->query('country2', 'Australia');

        $countriesList = DB::table('countries')->orderBy('name', 'asc')->pluck('name');

        $data1 = $this->getCountryComparisonData($country1Name);
        $data2 = $this->getCountryComparisonData($country2Name);

        // Jika salah satu data negara tidak ditemukan sama sekali di service API
        if (!$data1['country'] && !$data2['country']) {
            session()->now('error', 'Gagal memuat data perbandingan untuk kedua negara. Silakan coba kombinasi lain.');
        } elseif (!$data1['country']) {
            session()->now('error', "Gagal memuat data untuk negara: {$country1Name}.");
        } elseif (!$data2['country']) {
            session()->now('error', "Gagal memuat data untuk negara: {$country2Name}.");
        }

        return view('analytics.comparison', compact('countriesList', 'country1Name', 'country2Name', 'data1', 'data2'));
    }

    private function getCountryComparisonData($countryName)
    {
        $result = $this->countryService->getCountry($countryName);

        $country = null;
        $weather = null;
        $economy = null;
        $exchangeRate = null;
        $news = [];
        $riskData = null;

        if ($result && !empty($result['data']['objects'])) {
            $country = $result['data']['objects'][0];
            
            // 1. Ekstrak Cuaca
            if (isset($country['coordinates']['lat']) && isset($country['coordinates']['lng'])) {
                $lat = number_format((float) $country['coordinates']['lat'], 6, '.', '');
                $lng = number_format((float) $country['coordinates']['lng'], 6, '.', '');
                $weather = $this->weatherService->getCurrentWeather($lat, $lng);
            }

            // 2. Ekstrak Ekonomi
            if (isset($country['codes']['alpha_2'])) {
                $economy = $this->worldBankService->getEconomyData($country['codes']['alpha_2']);
            }

            // 3. Ekstrak Kurs
            if (isset($country['currencies'][0]['code'])) {
                $currencyCode = $country['currencies'][0]['code'];
                $exchangeRate = $this->exchangeRateService->getExchangeRate($currencyCode);
            }

            // 4. Ekstrak Berita
            $cName = $country['names']['common'] ?? $countryName;
            $news = $this->newsSentimentService->getNewsWithSentiment($cName);
            
            // 5. Kalkulasi Risk Engine
            $riskData = $this->riskScoringService->calculateRisk($weather, $economy, $news);
        }

        return [
            'country' => $country,
            'weather' => $weather,
            'economy' => $economy,
            'exchangeRate' => $exchangeRate,
            'riskData' => $riskData,
        ];
    }
}

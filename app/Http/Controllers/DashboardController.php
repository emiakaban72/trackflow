<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\ApiService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $selectedCountryName = $request->input('country', 'Germany');
        $countries = Country::orderBy('name', 'asc')->pluck('name')->toArray();

        return view('dashboard', [
            'countries' => $countries,
            'selectedCountry' => $selectedCountryName,
        ]);
    }

    public function getCountryDetail(Request $request)
    {
        $countryName = $request->input('country', 'Germany');
        
        $countryData = Country::where('name', $countryName)->first();
        $lat = $countryData ? $countryData->lat : 51.0;
        $lng = $countryData ? $countryData->lng : 9.0;

        // 1. TEMBAK API NEGARA (REST Countries)
        $countryApi = $this->apiService->getCountryInfo($countryName);
        
        $currencyCode = 'N/A';
        $region = 'N/A';
        $iso3 = null;

        // Murni membaca dari API, TANPA hardcode/fallback
        if (is_array($countryApi)) {
            if (isset($countryApi['currencies'])) {
                $currencyCode = array_key_first($countryApi['currencies']);
            }
            $region = $countryApi['region'] ?? 'N/A';
            $iso3 = $countryApi['cca3'] ?? null;
        }

        // 2. TEMBAK API KURS (ExchangeRate)
        // Hanya jalan jika API Negara berhasil memberikan $currencyCode
        $exchangeRateText = 'N/A';
        if ($currencyCode !== 'N/A') {
            $exchangeData = $this->apiService->getLiveExchangeRate($currencyCode);
            if ($exchangeData && isset($exchangeData['rates']['IDR'])) {
                $exchangeRateText = 'Rp ' . number_format($exchangeData['rates']['IDR'], 2, ',', '.');
            }
        }

        // 3. TEMBAK API INFLASI (World Bank)
        // Hanya jalan jika API Negara berhasil memberikan $iso3
        $inflationValue = 'N/A';
        if ($iso3) {
            $inflationRaw = $this->apiService->getInflationDataByIso($iso3);
            if ($inflationRaw !== null) {
                $inflationValue = round($inflationRaw, 2);
            }
        }

        // 4. TEMBAK API CUACA (Open-Meteo) - Pakai koordinat DB lokal
        $weather = $this->apiService->getWeatherData($lat, $lng);
        $temperature = $weather['current_weather']['temperature'] ?? '--';

        // Gabungkan teks
        $currencyDisplay = $currencyCode;
        if ($exchangeRateText !== 'N/A') {
            $currencyDisplay .= ' (' . $exchangeRateText . ')';
        }

        return response()->json([
            'country' => $countryName,
            'currency_code' => $currencyDisplay,
            'region' => $region,
            'inflation_rate' => $inflationValue,
            'temperature' => $temperature,
        ]);
    }
}
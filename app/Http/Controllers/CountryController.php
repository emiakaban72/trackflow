<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Services\WeatherService;
use App\Services\WorldBankService;
use App\Services\ExchangeRateService; // 1. Panggil service baru ini
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    protected $countryService;
    protected $weatherService;
    protected $worldBankService;
    protected $exchangeRateService; // 2. Daftarkan properti

    public function __construct(
        CountryService $countryService, 
        WeatherService $weatherService,
        WorldBankService $worldBankService,
        ExchangeRateService $exchangeRateService // 3. Masukkan ke constructor
    ) {
        $this->countryService = $countryService;
        $this->weatherService = $weatherService;
        $this->worldBankService = $worldBankService;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function index()
    {
        $countriesList = DB::table('countries')->orderBy('name', 'asc')->pluck('name');
        $result = $this->countryService->getCountry('Indonesia');
        
        $country = null;
        $weather = null;
        $economy = null;
        $exchangeRate = null; // 4. Siapkan wadah untuk kurs

        if ($result && !empty($result['data']['objects'])) {
            $country = $result['data']['objects'][0];
            
            // ... (Kode Cuaca dan Ekonomi tetap sama seperti sebelumnya) ...
            if (isset($country['coordinates']['lat']) && isset($country['coordinates']['lng'])) {
                $lat = number_format((float) $country['coordinates']['lat'], 6, '.', '');
                $lng = number_format((float) $country['coordinates']['lng'], 6, '.', '');
                $weather = $this->weatherService->getCurrentWeather($lat, $lng);
            }

            if (isset($country['codes']['alpha_2'])) {
                $economy = $this->worldBankService->getEconomyData($country['codes']['alpha_2']);
            }

            // 5. Eksekusi API Kurs Mata Uang (Ambil kode mata uang, misal: IDR)
            if (isset($country['currencies'][0]['code'])) {
                $currencyCode = $country['currencies'][0]['code'];
                $exchangeRate = $this->exchangeRateService->getExchangeRate($currencyCode);
            }
        }

        // Jangan lupa tambahkan $exchangeRate ke compact()
        return view('dashboard', compact('country', 'countriesList', 'weather', 'economy', 'exchangeRate'));
    }

    public function search(Request $request)
    {
        // ... (Lakukan hal yang persis sama di dalam fungsi search() juga) ...
        $request->validate(['country' => 'required']);
        $countriesList = DB::table('countries')->orderBy('name', 'asc')->pluck('name');
        $result = $this->countryService->getCountry($request->country);

        if (!$result || empty($result['data']['objects'])) {
            return back()->with('error', 'Negara tidak ditemukan');
        }

        $country = $result['data']['objects'][0];
        $weather = null;
        $economy = null;
        $exchangeRate = null; // Wadah kurs

        // ... (Kode Cuaca dan Ekonomi di search) ...
        if (isset($country['coordinates']['lat']) && isset($country['coordinates']['lng'])) {
            $lat = number_format((float) $country['coordinates']['lat'], 6, '.', '');
            $lng = number_format((float) $country['coordinates']['lng'], 6, '.', '');
            $weather = $this->weatherService->getCurrentWeather($lat, $lng);
        }

        if (isset($country['codes']['alpha_2'])) {
            $economy = $this->worldBankService->getEconomyData($country['codes']['alpha_2']);
        }

        // Eksekusi API Kurs Mata Uang
        if (isset($country['currencies'][0]['code'])) {
            $currencyCode = $country['currencies'][0]['code'];
            $exchangeRate = $this->exchangeRateService->getExchangeRate($currencyCode);
        }

        // Jangan lupa tambahkan $exchangeRate ke compact()
        return view('dashboard', compact('country', 'countriesList', 'weather', 'economy', 'exchangeRate'));
    }
}
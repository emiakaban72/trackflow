<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ApiService
{
    // Fungsi kurir murni menggunakan PHP Stream untuk menggantikan cURL Laragon yang bermasalah
    private function fetchLiveApi($url)
    {
        $streamContext = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
            "http" => [
                "timeout" => 15,
                "ignore_errors" => true,
                "header" => "User-Agent: SupplySync-App/1.0\r\n" .
                            "Accept: application/json\r\n"
            ]
        ]);

        try {
            $response = file_get_contents($url, false, $streamContext);
            return $response ? json_decode($response, true) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // 1. Open-Meteo API (Cuaca)
    public function getWeatherData($lat, $lng)
    {
        return Cache::remember("weather_stream_{$lat}_{$lng}", 3600, function () use ($lat, $lng) {
            $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current_weather=true";
            return $this->fetchLiveApi($url) ?? [];
        });
    }

    // 2. REST Countries API (Negara, Mata Uang, Wilayah)
    public function getCountryInfo($countryName)
    {
        $cacheKey = "country_stream_" . Str::slug($countryName);
        return Cache::remember($cacheKey, 86400, function () use ($countryName) {
            $url = "https://restcountries.com/v3.1/name/" . urlencode($countryName) . "?fullText=true";
            $data = $this->fetchLiveApi($url);
            return (is_array($data) && isset($data[0])) ? $data[0] : null;
        });
    }

    // 3. ExchangeRate API (Kurs Nilai Tukar)
    public function getLiveExchangeRate($baseCurrency)
    {
        return Cache::remember("exchange_stream_{$baseCurrency}", 43200, function () use ($baseCurrency) {
            $url = "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}";
            return $this->fetchLiveApi($url);
        });
    }

    // 4. World Bank API (Data Inflasi)
    public function getInflationDataByIso($isoCode)
    {
        $cacheKey = "inflation_stream_" . strtolower($isoCode);
        return Cache::remember($cacheKey, 86400, function () use ($isoCode) {
            $url = "https://api.worldbank.org/v2/country/{$isoCode}/indicator/FP.CPI.TOTL.ZG?format=json";
            $data = $this->fetchLiveApi($url);
            
            if (is_array($data) && isset($data[1]) && is_array($data[1])) {
                foreach ($data[1] as $item) {
                    if (isset($item['value']) && $item['value'] !== null) {
                        return $item['value'];
                    }
                }
            }
            return null;
        });
    }
}
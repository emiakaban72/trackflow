<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiService
{
    // Mengambil data cuaca dari Open-Meteo
    public function getWeatherData($lat, $lng)
    {
        return Cache::remember("weather_{$lat}_{$lng}", 3600, function () use ($lat, $lng) {
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lat,
                'longitude' => $lng,
                'current_weather' => true,
            ]);
            return $response->json();
        });
    }

    // Mengambil berita dari GNews
    public function getNews($query)
    {
        return Cache::remember("news_{$query}", 7200, function () use ($query) {
            // Kita akan tambahkan API KEY GNews nanti
            $response = Http::get("https://gnews.io/api/v4/search", [
                'q' => $query,
                'apikey' => 'GANTI_DENGAN_API_KEY_MU', 
                'lang' => 'en'
            ]);
            return $response->json();
        });
    }
}
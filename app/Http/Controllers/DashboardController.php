<?php

namespace App\Http\Controllers;

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
        // Default negara
        $country = $request->input('country', 'Germany');
        
        // Simulasi koordinat untuk API Cuaca (Bisa ditambah nanti berdasarkan daftar negara)
        $coords = [
            'Germany' => ['lat' => 52.52, 'lng' => 13.40],
            'Indonesia' => ['lat' => -6.20, 'lng' => 106.81],
        ];

        $lat = $coords[$country]['lat'] ?? 0;
        $lng = $coords[$country]['lng'] ?? 0;

        // Ambil data dari Service
        $weather = $this->apiService->getWeatherData($lat, $lng);
        
        return view('dashboard', [
            'weather' => $weather,
            'selectedCountry' => $country
        ]);
    }
}
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\MapController;
use App\Models\Port; // Jangan lupa panggil model Port di atas
use App\Models\Country;
use App\Services\WeatherService;
use App\Http\Controllers\MarketIntelligenceController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\VisualizationController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\FavoriteController;



// ==========================================
// RUTE AUTENTIKASI (PUBLIK)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// RUTE TERPROTEKSI (WAJIB LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/', [CountryController::class, 'index'])->name('home');
    Route::get('/country', [CountryController::class, 'index'])->name('country.index');
    Route::post('/country', [CountryController::class, 'search'])->name('country.search');

    // Halaman Peta
    Route::get('/geospatial/map', [MapController::class, 'index'])->name('map.index');

    // Market Intelligence
    Route::get('/market/currency', [MarketIntelligenceController::class, 'currency'])->name('market.currency');
    Route::get('/market/news', [MarketIntelligenceController::class, 'news'])->name('market.news');

    // Analytics & Risk
    Route::get('/analytics/risk', [RiskController::class, 'index'])->name('analytics.risk');
    Route::get('/analytics/visualization', [VisualizationController::class, 'index'])->name('analytics.visualization');
    Route::get('/analytics/comparison', [ComparisonController::class, 'index'])->name('analytics.comparison');

    // Favorit
    Route::get('/favorite', [FavoriteController::class, 'index'])->name('favorite.index');
    Route::post('/favorite/toggle', [FavoriteController::class, 'toggle'])->name('favorite.toggle');
});



// ==========================================
// RUTE KHUSUS ADMIN (DIGEMBOK MIDDLEWARE AUTH & PREFIX /admin)
// ==========================================
Route::middleware(['auth', PreventBackHistory::class, AdminOnly::class])->prefix('admin')->group(function () {    
    
    // Rute Dasbor Admin (/admin/dashboard)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Rute Manajemen Pengguna
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store'); 
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Rute Manajemen Pelabuhan
    Route::get('/ports', [AdminController::class, 'ports'])->name('admin.ports');
    Route::post('/ports', [AdminController::class, 'storePort'])->name('admin.ports.store');
    Route::put('/ports/{id}', [AdminController::class, 'updatePort'])->name('admin.ports.update');
    Route::delete('/ports/{id}', [AdminController::class, 'deletePort'])->name('admin.ports.delete');

    // Rute Manajemen Artikel
    Route::get('/articles', [AdminController::class, 'articles'])->name('admin.articles');
    Route::post('/articles', [AdminController::class, 'storeArticle'])->name('admin.articles.store');
    Route::put('/articles/{id}', [AdminController::class, 'updateArticle'])->name('admin.articles.update');
    Route::delete('/articles/{id}', [AdminController::class, 'deleteArticle'])->name('admin.articles.delete');
    
});



// 2. Rute REST API untuk menyedot data pelabuhan
// Tambahkan '/api' di sini agar cocok dengan perintah fetch() di Javascript
Route::get('/api/ports', function () {
    return response()->json(
        Port::select('ports.name', 'ports.code', 'ports.lat', 'ports.lng', 'countries.name as country_name')
            ->join('countries', 'ports.country_id', '=', 'countries.id')
            ->get()
    );
});


// Endpoint untuk mengambil titik koordinat negara di peta
Route::get('/api/countries', function () {
    return response()->json(Country::select('name', 'lat', 'lng')->get());
});

// Endpoint untuk menyedot cuaca satelit secara Real-Time via WeatherService
Route::get('/api/weather/live', function (Request $request, WeatherService $weatherService) {
    $request->validate(['lat' => 'required', 'lng' => 'required']);
    
    $weather = $weatherService->getCurrentWeather($request->lat, $request->lng);
    return response()->json($weather);
});

// Endpoint REST API Tambahan sesuai Spesifikasi Tugas Akhir
Route::get('/api/risk', function (Request $request, App\Services\CountryService $countryService, WeatherService $weatherService, App\Services\WorldBankService $worldBankService, App\Services\NewsSentimentService $newsSentimentService, App\Services\RiskScoringService $riskScoringService) {
    $countryName = $request->query('country', 'Indonesia');
    $result = $countryService->getCountry($countryName);
    if ($result && !empty($result['data']['objects'])) {
        $country = $result['data']['objects'][0];
        $weather = null;
        if (isset($country['coordinates']['lat']) && isset($country['coordinates']['lng'])) {
            $weather = $weatherService->getCurrentWeather($country['coordinates']['lat'], $country['coordinates']['lng']);
        }
        $economy = isset($country['codes']['alpha_2']) ? $worldBankService->getEconomyData($country['codes']['alpha_2']) : null;
        $news = $newsSentimentService->getNewsWithSentiment($country['names']['common'] ?? $countryName);
        $riskData = $riskScoringService->calculateRisk($weather, $economy, $news);
        return response()->json([
            'country' => $countryName,
            'risk_data' => $riskData
        ]);
    }
    return response()->json(['error' => 'Country not found'], 404);
});

Route::get('/api/news', function (Request $request, App\Services\NewsSentimentService $newsSentimentService) {
    $countryName = $request->query('country', 'Indonesia');
    $news = $newsSentimentService->getNewsWithSentiment($countryName);
    return response()->json([
        'country' => $countryName,
        'news' => $news
    ]);
});

Route::get('/api/currency', function (Request $request, App\Services\ExchangeRateService $exchangeRateService) {
    $currencyCode = $request->query('code', 'IDR');
    $rate = $exchangeRateService->getExchangeRate($currencyCode);
    return response()->json([
        'currency' => $currencyCode,
        'rate_vs_usd' => $rate
    ]);
});


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Watchlist;
use App\Services\CountryService;
use App\Services\WeatherService;
use App\Services\RiskScoringService;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    protected $countryService;
    protected $weatherService;
    protected $riskScoringService;

    public function __construct(
        CountryService $countryService,
        WeatherService $weatherService,
        RiskScoringService $riskScoringService
    ) {
        $this->countryService = $countryService;
        $this->weatherService = $weatherService;
        $this->riskScoringService = $riskScoringService;
    }

    public function index()
    {
        $favoriteNames = [];

        if (Auth::check()) {
            $favoriteNames = Watchlist::where('user_id', Auth::id())
                ->join('countries', 'watchlists.country_id', '=', 'countries.id')
                ->pluck('countries.name')
                ->toArray();
        } else {
            $favoriteNames = session('favorite_countries', []);
        }

        $favoritesData = [];

        foreach ($favoriteNames as $name) {
            $result = $this->countryService->getCountry($name);
            if ($result && !empty($result['data']['objects'])) {
                $country = $result['data']['objects'][0];
                
                $weather = null;
                if (isset($country['coordinates']['lat']) && isset($country['coordinates']['lng'])) {
                    $lat = number_format((float) $country['coordinates']['lat'], 6, '.', '');
                    $lng = number_format((float) $country['coordinates']['lng'], 6, '.', '');
                    $weather = $this->weatherService->getCurrentWeather($lat, $lng);
                }

                // Hitung risk score
                $riskData = $this->riskScoringService->calculateRisk($weather, null, []);
                $riskData['weather_temp'] = $weather['current']['temperature_2m'] ?? null;

                $favoritesData[] = [
                    'name' => $name,
                    'flag' => $country['flag']['url_png'] ?? null,
                    'capital' => $country['capitals'][0]['name'] ?? ($country['capitals'][0] ?? 'N/A'),
                    'region' => $country['region'] ?? 'N/A',
                    'population' => $country['population'] ?? 0,
                    'risk' => $riskData
                ];
            }
        }

        return view('analytics.favorite', compact('favoritesData'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string'
        ]);

        $countryName = $request->country_name;
        $country = Country::where('name', $countryName)->first();

        if (!$country) {
            return back()->with('error', "Negara {$countryName} tidak ditemukan di database kami.");
        }

        if (Auth::check()) {
            $existing = Watchlist::where('user_id', Auth::id())
                ->where('country_id', $country->id)
                ->first();

            if ($existing) {
                $existing->delete();
                $message = "Berhasil menghapus {$countryName} dari favorit.";
            } else {
                Watchlist::create([
                    'user_id' => Auth::id(),
                    'country_id' => $country->id
                ]);
                $message = "Berhasil menambahkan {$countryName} ke favorit.";
            }
        } else {
            $favorites = session('favorite_countries', []);

            if (in_array($countryName, $favorites, true)) {
                $favorites = array_diff($favorites, [$countryName]);
                $message = "Berhasil menghapus {$countryName} dari favorit.";
            } else {
                $favorites[] = $countryName;
                $message = "Berhasil menambahkan {$countryName} ke favorit.";
            }

            session(['favorite_countries' => array_values($favorites)]);
        }

        return back()->with('success', $message);
    }
}

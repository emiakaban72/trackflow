<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class WorldBankService
{
    public function getEconomyData($iso2Code)
    {
        $cacheKey = "economy_wb_full_{$iso2Code}";
        $cached = Cache::get($cacheKey);
        
        // Jika sudah ada cache dan semua datanya valid (tidak ada '--'), langsung gunakan
        if ($cached && !in_array('--', $cached, true)) {
            return $cached;
        }

        // Jika ada cache dengan '--', kita beri toleransi waktu tertentu (misal 5 menit) sebelum mencoba fetch ulang
        $lastAttemptKey = "economy_wb_attempt_{$iso2Code}";
        if ($cached && Cache::get($lastAttemptKey)) {
            return $cached;
        }

        $indicators = [
            'gdp' => 'NY.GDP.MKTP.CD',
            'inflation' => 'FP.CPI.TOTL.ZG',
            'population' => 'SP.POP.TOTL',
            'exports' => 'NE.EXP.GNFS.CD',
            'imports' => 'NE.IMP.GNFS.CD'
        ];

        $results = [
            'gdp' => '--',
            'inflation' => '--',
            'population' => '--',
            'exports' => '--',
            'imports' => '--'
        ];

        $hasFailure = false;

        foreach ($indicators as $key => $indicator) {
            $url = "https://api.worldbank.org/v2/country/{$iso2Code}/indicator/{$indicator}?format=json&mrnev=1";
            $data = $this->fetchNative($url);

            if (isset($data[1][0]['value'])) {
                $val = $data[1][0]['value'];
                
                if ($key === 'inflation') {
                    $results[$key] = number_format($val, 1);
                } elseif ($key === 'population') {
                    $results[$key] = number_format($val, 0, ',', '.');
                } else {
                    $results[$key] = $this->formatMoney($val);
                }
            } else {
                $hasFailure = true;
            }
        }

        // Offline Fallback Data jika API benar-benar gagal atau lambat
        if ($hasFailure) {
            $fallbacks = [
                'ID' => ['gdp' => '$1.37 T', 'inflation' => '2.6', 'population' => '277.530.000', 'exports' => '$247.08 B', 'imports' => '$231.45 B'],
                'DE' => ['gdp' => '$4.45 T', 'inflation' => '5.9', 'population' => '84.400.000', 'exports' => '$1.65 T', 'imports' => '$1.45 T'],
                'AU' => ['gdp' => '$1.72 T', 'inflation' => '5.6', 'population' => '26.400.000', 'exports' => '$340.00 B', 'imports' => '$290.00 B'],
                'FR' => ['gdp' => '$3.03 T', 'inflation' => '4.9', 'population' => '68.000.000', 'exports' => '$1.05 T', 'imports' => '$1.11 T'],
                'US' => ['gdp' => '$27.36 T', 'inflation' => '4.1', 'population' => '334.900.000', 'exports' => '$3.05 T', 'imports' => '$3.83 T'],
                'CN' => ['gdp' => '$17.79 T', 'inflation' => '0.2', 'population' => '1.409.000.000', 'exports' => '$3.38 T', 'imports' => '$2.56 T'],
                'JP' => ['gdp' => '$4.21 T', 'inflation' => '3.2', 'population' => '125.400.000', 'exports' => '$920.00 B', 'imports' => '$870.00 B'],
                'GB' => ['gdp' => '$3.33 T', 'inflation' => '7.3', 'population' => '67.000.000', 'exports' => '$1.02 T', 'imports' => '$1.10 T'],
                'IN' => ['gdp' => '$3.55 T', 'inflation' => '5.7', 'population' => '1.428.000.000', 'exports' => '$450.00 B', 'imports' => '$570.00 B'],
                'SG' => ['gdp' => '$501.00 B', 'inflation' => '4.8', 'population' => '5.900.000', 'exports' => '$515.00 B', 'imports' => '$460.00 B']
            ];

            if (isset($fallbacks[strtoupper($iso2Code)])) {
                $fb = $fallbacks[strtoupper($iso2Code)];
                foreach ($results as $k => $v) {
                    if ($v === '--') {
                        $results[$k] = $fb[$k];
                    }
                }
                $hasFailure = false; // Karena berhasil ditutupi data fallback
            }
        }

        if ($hasFailure) {
            Cache::put($cacheKey, $results, 300);
            Cache::put($lastAttemptKey, true, 300);
        } else {
            Cache::put($cacheKey, $results, 86400);
        }

        return $results;
    }

    private function formatMoney($val)
    {
        if ($val >= 1000000000000) {
            return '$' . number_format($val / 1000000000000, 2) . ' T';
        } elseif ($val >= 1000000000) {
            return '$' . number_format($val / 1000000000, 2) . ' B';
        } elseif ($val >= 1000000) {
            return '$' . number_format($val / 1000000, 2) . ' M';
        } else {
            return '$' . number_format($val);
        }
    }

    private function fetchNative($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            return json_decode($response, true);
        }
        return null;
    }
}
@extends('layouts.app')

@section('content')
<style>
    /* Efek Kapsul Perbandingan */
    .comparison-form-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }

    .vs-badge {
        background-color: var(--matcha-500);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.85rem;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(234, 100, 22, 0.25);
    }

    .compare-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background-color: #ffffff;
    }
    .compare-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    /* Tabel Perbandingan Modern */
    .comparison-table th {
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
    }
    .comparison-table td {
        padding: 16px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }
    .comparison-table tr:hover td {
        background-color: #fffbeb;
    }

    .metric-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #0f172a;
    }
    .metric-sub {
        font-size: 0.75rem;
        color: #64748b;
        display: block;
        font-weight: 400;
    }

    .comparison-value {
        font-weight: 700;
        font-size: 1rem;
    }

    /* Bar perbandingan visual */
    .comparison-bar-container {
        height: 6px;
        background-color: #f1f5f9;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 6px;
    }
    .comparison-bar {
        height: 100%;
        border-radius: 3px;
    }

    .btn-compare {
        background-color: var(--matcha-500);
        color: white !important;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        border-radius: 8px;
    }
    .btn-compare:hover {
        background-color: var(--matcha-700);
        transform: scale(1.02);
    }
    .btn-compare:active {
        transform: scale(0.98);
    }
</style>

<div class="d-flex flex-column h-100" style="animation: fadeIn 0.4s ease-out;">
    
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">Country Comparison Engine</h4>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                <i class="fa-solid fa-scale-balanced me-1"></i>Bandingkan risiko supply chain, indikator makroekonomi, dan cuaca antar negara secara berdampingan.
            </p>
        </div>
    </div>

    <!-- FORM SELEKSI NEGARA -->
    <div class="comparison-form-card p-4 mb-4">
        <form action="{{ route('analytics.comparison') }}" method="GET">
            <div class="row align-items-center g-3">
                <!-- Negara 1 -->
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold text-uppercase">Negara Pertama</label>
                    <select name="country1" class="form-select select2-country" style="width: 100%;">
                        @foreach($countriesList as $c)
                            <option value="{{ $c }}" {{ $country1Name == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- VS Badge -->
                <div class="col-md-2 d-flex justify-content-center align-items-center pt-3">
                    <div class="vs-badge">VS</div>
                </div>

                <!-- Negara 2 -->
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold text-uppercase">Negara Kedua</label>
                    <select name="country2" class="form-select select2-country" style="width: 100%;">
                        @foreach($countriesList as $c)
                            <option value="{{ $c }}" {{ $country2Name == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Submit -->
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn btn-compare px-5 py-2 shadow-sm">
                        <i class="fa-solid fa-scale-balanced me-2"></i>Mulai Perbandingan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- JIKA ADA ERROR SESSION -->
    @if(session('error'))
        <div class="alert alert-danger mb-4 border-0 shadow-sm d-flex align-items-center py-2 px-3" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; border-radius: 8px; color: #991b1b;">
            <i class="fa-solid fa-triangle-exclamation me-3 fs-5"></i>
            <div style="font-size: 0.85rem;">{{ session('error') }}</div>
        </div>
    @endif

    <!-- PANEL PERBANDINGAN BERDAMPINGAN -->
    <div class="row g-4 mb-4">
        <!-- Negara 1 Card -->
        <div class="col-md-6">
            <div class="compare-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if(isset($data1['country']['flag']['url_png']))
                        <img src="{{ $data1['country']['flag']['url_png'] }}" width="60" class="rounded border shadow-sm" alt="Flag">
                    @else
                        <div class="bg-light rounded border d-flex justify-content-center align-items-center" style="width: 60px; height: 40px;">
                            <i class="fa-solid fa-flag text-secondary"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $data1['country']['names']['common'] ?? $country1Name }}</h4>
                        <span class="text-muted small"><i class="fa-solid fa-building-columns me-1"></i>Capital: {{ $data1['country']['capitals'][0]['name'] ?? ($data1['country']['capitals'][0] ?? 'N/A') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-secondary border"><i class="fa-solid fa-earth-americas me-1"></i> {{ $data1['country']['region'] ?? 'N/A' }}</span>
                    <span class="badge bg-light text-secondary border"><i class="fa-solid fa-users me-1"></i> {{ isset($data1['country']['population']) ? number_format($data1['country']['population']) : '--' }}</span>
                </div>
            </div>
        </div>

        <!-- Negara 2 Card -->
        <div class="col-md-6">
            <div class="compare-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if(isset($data2['country']['flag']['url_png']))
                        <img src="{{ $data2['country']['flag']['url_png'] }}" width="60" class="rounded border shadow-sm" alt="Flag">
                    @else
                        <div class="bg-light rounded border d-flex justify-content-center align-items-center" style="width: 60px; height: 40px;">
                            <i class="fa-solid fa-flag text-secondary"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $data2['country']['names']['common'] ?? $country2Name }}</h4>
                        <span class="text-muted small"><i class="fa-solid fa-building-columns me-1"></i>Capital: {{ $data2['country']['capitals'][0]['name'] ?? ($data2['country']['capitals'][0] ?? 'N/A') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-secondary border"><i class="fa-solid fa-earth-americas me-1"></i> {{ $data2['country']['region'] ?? 'N/A' }}</span>
                    <span class="badge bg-light text-secondary border"><i class="fa-solid fa-users me-1"></i> {{ isset($data2['country']['population']) ? number_format($data2['country']['population']) : '--' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL COMPARISON GRID -->
    <div class="card-corporate overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table comparison-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;">Indikator Analitik</th>
                        <th style="width: 35%;">{{ $data1['country']['names']['common'] ?? $country1Name }}</th>
                        <th style="width: 35%;">{{ $data2['country']['names']['common'] ?? $country2Name }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- PILAR 1: RISK SCORE -->
                    <tr class="bg-light/30">
                        <td colspan="3" class="fw-bold text-uppercase text-secondary py-2" style="font-size: 0.7rem; letter-spacing: 1px;">
                            <i class="fa-solid fa-shield-halved me-1"></i> Risk Assessment
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">Risk Scoring Engine</span>
                            <span class="metric-sub">Kalkulasi akhir algoritma model risiko</span>
                        </td>
                        <td>
                            @if(isset($data1['riskData']))
                                <div class="d-flex align-items-center gap-2">
                                    <span class="comparison-value text-{{ $data1['riskData']['color'] }}">{{ $data1['riskData']['score'] }}</span>
                                    <span class="badge bg-{{ $data1['riskData']['color'] }}-subtle text-{{ $data1['riskData']['color'] }} border border-{{ $data1['riskData']['color'] }}-subtle" style="font-size: 0.7rem;">{{ $data1['riskData']['status'] }}</span>
                                </div>
                                <div class="comparison-bar-container">
                                    <div class="comparison-bar bg-{{ $data1['riskData']['color'] }}" style="width: {{ $data1['riskData']['score'] }}%"></div>
                                </div>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($data2['riskData']))
                                <div class="d-flex align-items-center gap-2">
                                    <span class="comparison-value text-{{ $data2['riskData']['color'] }}">{{ $data2['riskData']['score'] }}</span>
                                    <span class="badge bg-{{ $data2['riskData']['color'] }}-subtle text-{{ $data2['riskData']['color'] }} border border-{{ $data2['riskData']['color'] }}-subtle" style="font-size: 0.7rem;">{{ $data2['riskData']['status'] }}</span>
                                </div>
                                <div class="comparison-bar-container">
                                    <div class="comparison-bar bg-{{ $data2['riskData']['color'] }}" style="width: {{ $data2['riskData']['score'] }}%"></div>
                                </div>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>

                    <!-- PILAR 2: GDP & ECONOMY -->
                    <tr class="bg-light/30">
                        <td colspan="3" class="fw-bold text-uppercase text-secondary py-2" style="font-size: 0.7rem; letter-spacing: 1px;">
                            <i class="fa-solid fa-landmark me-1"></i> Macroeconomics (World Bank)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">GDP (Gross Domestic Product)</span>
                            <span class="metric-sub">Kekuatan pasar dan kapasitas ekonomi</span>
                        </td>
                        <td>
                            <span class="comparison-value text-dark">{{ $data1['economy']['gdp'] ?? '--' }}</span>
                        </td>
                        <td>
                            <span class="comparison-value text-dark">{{ $data2['economy']['gdp'] ?? '--' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">Inflation Rate</span>
                            <span class="metric-sub">Tingkat inflasi tahunan konsumen (%)</span>
                        </td>
                        <td>
                            @if(isset($data1['economy']['inflation']) && $data1['economy']['inflation'] !== '--')
                                <span class="comparison-value {{ (float)$data1['economy']['inflation'] > 5.0 ? 'text-danger' : 'text-success' }}">{{ $data1['economy']['inflation'] }}%</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($data2['economy']['inflation']) && $data2['economy']['inflation'] !== '--')
                                <span class="comparison-value {{ (float)$data2['economy']['inflation'] > 5.0 ? 'text-danger' : 'text-success' }}">{{ $data2['economy']['inflation'] }}%</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">Ekspor / Impor</span>
                            <span class="metric-sub">Neraca perdagangan barang & jasa tahunan</span>
                        </td>
                        <td>
                            <div class="small">
                                <span class="text-success fw-bold"><i class="fa-solid fa-circle-arrow-up me-1"></i>Exp:</span> {{ $data1['economy']['exports'] ?? '--' }}<br>
                                <span class="text-warning fw-bold"><i class="fa-solid fa-circle-arrow-down me-1"></i>Imp:</span> {{ $data1['economy']['imports'] ?? '--' }}
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <span class="text-success fw-bold"><i class="fa-solid fa-circle-arrow-up me-1"></i>Exp:</span> {{ $data2['economy']['exports'] ?? '--' }}<br>
                                <span class="text-warning fw-bold"><i class="fa-solid fa-circle-arrow-down me-1"></i>Imp:</span> {{ $data2['economy']['imports'] ?? '--' }}
                            </div>
                        </td>
                    </tr>

                    <!-- PILAR 3: WEATHER -->
                    <tr class="bg-light/30">
                        <td colspan="3" class="fw-bold text-uppercase text-secondary py-2" style="font-size: 0.7rem; letter-spacing: 1px;">
                            <i class="fa-solid fa-cloud-bolt me-1"></i> Real-time Weather (Open-Meteo)
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">Temperature & Apparent</span>
                            <span class="metric-sub">Suhu aktual dan suhu terasa di kulit</span>
                        </td>
                        <td>
                            @if(isset($data1['weather']['current']))
                                <span class="comparison-value text-dark">{{ $data1['weather']['current']['temperature_2m'] }} °C</span>
                                <span class="metric-sub">Feels like: {{ $data1['weather']['current']['apparent_temperature'] }} °C</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($data2['weather']['current']))
                                <span class="comparison-value text-dark">{{ $data2['weather']['current']['temperature_2m'] }} °C</span>
                                <span class="metric-sub">Feels like: {{ $data2['weather']['current']['apparent_temperature'] }} °C</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">Kondisi Cuaca & Angin</span>
                            <span class="metric-sub">Parameter cuaca dan kecepatan angin</span>
                        </td>
                        <td>
                            @if(isset($data1['weather']['current']))
                                @php
                                    $wmo1 = $data1['weather']['current']['weather_code'] ?? 0;
                                    $cond1 = 'Cerah / Berawan';
                                    if ($wmo1 >= 95) $cond1 = 'Badai Petir';
                                    elseif ($wmo1 >= 80) $cond1 = 'Hujan Deras';
                                    elseif ($wmo1 >= 51) $cond1 = 'Hujan Ringan';
                                @endphp
                                <span class="comparison-value text-dark">{{ $cond1 }}</span>
                                <span class="metric-sub"><i class="fa-solid fa-wind me-1"></i>Angin: {{ $data1['weather']['current']['wind_speed_10m'] }} km/h</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($data2['weather']['current']))
                                @php
                                    $wmo2 = $data2['weather']['current']['weather_code'] ?? 0;
                                    $cond2 = 'Cerah / Berawan';
                                    if ($wmo2 >= 95) $cond2 = 'Badai Petir';
                                    elseif ($wmo2 >= 80) $cond2 = 'Hujan Deras';
                                    elseif ($wmo2 >= 51) $cond2 = 'Hujan Ringan';
                                @endphp
                                <span class="comparison-value text-dark">{{ $cond2 }}</span>
                                <span class="metric-sub"><i class="fa-solid fa-wind me-1"></i>Angin: {{ $data2['weather']['current']['wind_speed_10m'] }} km/h</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>

                    <!-- PILAR 4: CURRENCY -->
                    <tr class="bg-light/30">
                        <td colspan="3" class="fw-bold text-uppercase text-secondary py-2" style="font-size: 0.7rem; letter-spacing: 1px;">
                            <i class="fa-solid fa-coins me-1"></i> Foreign Exchange
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="metric-label">Mata Uang & Nilai Tukar</span>
                            <span class="metric-sub">Nilai tukar mata uang lokal terhadap 1 USD</span>
                        </td>
                        <td>
                            @if(isset($data1['country']['currencies'][0]))
                                <span class="comparison-value text-dark">{{ $data1['exchangeRate'] ?? '--' }}</span>
                                <span class="metric-sub">Currency: {{ $data1['country']['currencies'][0]['name'] ?? 'N/A' }} (<b>{{ $data1['country']['currencies'][0]['code'] }}</b>)</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($data2['country']['currencies'][0]))
                                <span class="comparison-value text-dark">{{ $data2['exchangeRate'] ?? '--' }}</span>
                                <span class="metric-sub">Currency: {{ $data2['country']['currencies'][0]['name'] ?? 'N/A' }} (<b>{{ $data2['country']['currencies'][0]['code'] }}</b>)</span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

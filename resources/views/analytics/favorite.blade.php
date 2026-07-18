@extends('layouts.app')

@section('content')
<style>
    .favorite-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background-color: #ffffff;
    }
    .favorite-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        border-color: #ffd8a8;
    }

    .btn-dashboard {
        background-color: var(--matcha-500);
        color: white !important;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        border-radius: 8px;
    }
    .btn-dashboard:hover {
        background-color: var(--matcha-700);
    }

    .empty-state {
        background: #ffffff;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3rem;
        color: #ffedd5;
        background-color: #fff7ed;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
    }
</style>

<div class="d-flex flex-column h-100" style="animation: fadeIn 0.4s ease-out;">
    
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">Favorite Monitoring List</h4>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                <i class="fa-solid fa-star text-warning me-1"></i>Daftar negara pantauan utama Anda untuk melihat metrik risiko dan cuaca secara cepat.
            </p>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm d-flex align-items-center py-2 px-3" style="background-color: #f0fdf4; border-left: 4px solid #22c55e !important; border-radius: 8px; color: #166534;" role="alert">
            <i class="fa-solid fa-circle-check me-3 fs-5"></i>
            <div style="font-size: 0.85rem;">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem; padding: 12px;"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm d-flex align-items-center py-2 px-3" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; border-radius: 8px; color: #991b1b;" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-3 fs-5"></i>
            <div style="font-size: 0.85rem;">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem; padding: 12px;"></button>
        </div>
    @endif

    <!-- GRID NEGARA FAVORIT -->
    @if(count($favoritesData) > 0)
        <div class="row g-4 mb-4">
            @foreach($favoritesData as $data)
                <div class="col-md-4">
                    <div class="favorite-card p-4 h-100 d-flex flex-column justify-content-between">
                        
                        <!-- Bagian Atas: Profil -->
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($data['flag'])
                                    <img src="{{ $data['flag'] }}" width="50" class="rounded border shadow-sm" alt="Flag">
                                @else
                                    <div class="bg-light rounded border d-flex justify-content-center align-items-center" style="width: 50px; height: 33px;">
                                        <i class="fa-solid fa-flag text-secondary"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $data['name'] }}</h5>
                                    <span class="text-muted small"><i class="fa-solid fa-building-columns me-1"></i>{{ $data['capital'] }}</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-4">
                                <span class="badge bg-light text-secondary border"><i class="fa-solid fa-earth-americas me-1"></i> {{ $data['region'] }}</span>
                                <span class="badge bg-light text-secondary border"><i class="fa-solid fa-users me-1"></i> {{ number_format($data['population']) }}</span>
                            </div>

                            <!-- Bagian Tengah: Risiko & Cuaca -->
                            <div class="border-top pt-3 mb-4">
                                <!-- Skor Risiko -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted fw-semibold">Risk Index:</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-{{ $data['risk']['color'] }}">{{ $data['risk']['score'] }}</span>
                                        <span class="badge bg-{{ $data['risk']['color'] }}-subtle text-{{ $data['risk']['color'] }} border border-{{ $data['risk']['color'] }}-subtle" style="font-size: 0.65rem;">{{ $data['risk']['status'] }}</span>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 6px; background-color: #f1f5f9; border-radius: 3px;">
                                    <div class="progress-bar bg-{{ $data['risk']['color'] }}" role="progressbar" style="width: {{ $data['risk']['score'] }}%; border-radius: 3px;"></div>
                                </div>

                                <!-- Cuaca -->
                                <div class="d-flex justify-content-between align-items-center small">
                                    <span class="text-muted fw-semibold">Weather:</span>
                                    @if(isset($data['risk']['weather_temp']))
                                        <span class="text-dark fw-bold">
                                            <i class="fa-solid fa-temperature-half text-warning me-1"></i>{{ $data['risk']['weather_temp'] ?? $data['risk']['weather'] }} °C
                                        </span>
                                    @else
                                        <span class="text-dark fw-bold">--</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Bagian Bawah: Tombol Aksi -->
                        <div class="d-flex gap-2">
                            <!-- Buka Dashboard -->
                            <a href="{{ route('country.index', ['country' => $data['name']]) }}" class="btn btn-dashboard flex-grow-1 btn-sm shadow-sm py-2">
                                <i class="fa-solid fa-chart-line me-1"></i> Dashboard
                            </a>

                            <!-- Hapus dari Favorit -->
                            <form action="{{ route('favorite.toggle') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="country_name" value="{{ $data['name'] }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm py-2 px-3" title="Hapus dari Favorit">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- EMPTY STATE -->
        <div class="empty-state my-auto">
            <div class="empty-icon">
                <i class="fa-solid fa-star text-warning"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Favorite Monitoring List Kosong</h5>
            <p class="text-muted mx-auto mb-4" style="max-width: 400px; font-size: 0.9rem;">
                Anda belum menambahkan negara ke dalam daftar pantauan favorit. Kunjungi dashboard utama untuk menandai negara pilihan Anda.
            </p>
            <a href="/country" class="btn btn-dashboard px-4 py-2 shadow">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Cari Negara di Dashboard
            </a>
        </div>
    @endif

</div>
@endsection

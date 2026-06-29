@extends('layouts.app')

@section('content')
    <!-- Menggunakan d-flex flex-column h-100 agar konten menyesuaikan tinggi layar 100% -->
    <div class="d-flex flex-column h-100">

        <!-- Header & Pemilih Negara (flex-shrink-0 agar tingginya tidak menyusut) -->
        <div class="d-flex justify-content-between align-items-end mb-3 flex-shrink-0">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Global Country Dashboard</h3>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Pemantauan rantai pasok dan skor risiko secara
                    real-time.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted fw-bold" style="font-size: 0.85rem;">PILIH NEGARA:</span>
                <select class="form-select shadow-sm border-0" style="width: 200px;">
                    <option selected>Indonesia</option>
                    <option>China</option>
                    <option>Indonesia</option>
                    <option>Australia</option>
                </select>
                <button class="btn bg-matcha shadow-sm text-white px-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>

        <!-- Baris Atas: Indikator Utama (4 Kartu) -->
        <div class="row g-3 mb-3 flex-shrink-0">
            <!-- FITUR 2: Risk Scoring Engine -->
            <div class="col-md-3">
                <div class="card-corporate p-3 h-100 border-start border-4"
                    style="border-color: var(--matcha-500) !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.70rem;">Total Risk Score</span>
                        <i class="fa-solid fa-shield-halved text-matcha fs-6"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">22 <span class="fs-6 text-muted fw-normal">/ Low</span></h2>
                    <p class="text-success mt-1 mb-0" style="font-size: 0.70rem; font-weight: 600;">
                        <i class="fa-solid fa-check-circle me-1"></i> Aman untuk logistik
                    </p>
                </div>
            </div>

            <!-- FITUR 1: Cuaca Saat Ini -->
            <div class="col-md-3">
                <div class="card-corporate p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.70rem;">Current Weather</span>
                        <i class="fa-solid fa-cloud-rain text-secondary fs-6"></i>
                    </div>
                    <!-- Menampilkan suhu dari data API -->
                    <h2 class="fw-bold text-dark mb-0">
                        {{ $weather['current_weather']['temperature'] ?? '--' }}°C
                    </h2>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.70rem;">
                        Negara: <span class="fw-bold">{{ $selectedCountry }}</span>
                    </p>
                </div>
            </div>

            <!-- FITUR 1: Inflasi -->
            <div class="col-md-3">
                <div class="card-corporate p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.70rem;">Inflation Rate</span>
                        <i class="fa-solid fa-arrow-trend-up text-secondary fs-6"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">2.4<span class="fs-6 text-muted fw-normal">%</span></h2>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.70rem;">
                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i> Syncing API...
                    </p>
                </div>
            </div>

            <!-- FITUR 1: Mata Uang & GDP -->
            <div class="col-md-3">
                <div class="card-corporate p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.70rem;">Currency & GDP</span>
                        <i class="fa-solid fa-coins text-secondary fs-6"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-0">EUR</h2>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.70rem;">
                        GDP: <span class="fw-bold">Wait...</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Area Bawah: Visualisasi (flex-grow-1 agar memenuhi sisa layar yang ada) -->
        <div class="row g-3 flex-grow-1 pb-1">

            <!-- Grafik Tren -->
            <div class="col-md-8 h-100">
                <div class="card-corporate p-4 h-100 d-flex flex-column">
                    <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-chart-area me-2 text-matcha"></i> Risk &
                        Inflation Trends</h6>
                    <!-- Wadah grafik ini akan otomatis merenggang (flex-grow-1) -->
                    <div class="bg-light rounded d-flex align-items-center justify-content-center flex-grow-1"
                        style="border: 1px dashed #cbd5e1; min-height: 150px;">
                        <span class="text-muted" style="font-size: 0.85rem;"><i class="fa-solid fa-code me-2"></i>Area ini
                            akan diisi grafik Chart.js</span>
                    </div>
                </div>
            </div>

            <!-- Sentimen Berita -->
            <div class="col-md-4 h-100">
                <!-- overflow-hidden di sini mencegah kartu membesar melebihi layar -->
                <div class="card-corporate p-4 h-100 d-flex flex-column overflow-hidden">
                    <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-brain me-2 text-matcha"></i> News Sentiment
                        Analysis</h6>

                    <!-- Jika isi berita terlalu banyak, area dalam ini saja yang bisa di-scroll tipis -->
                    <div class="flex-grow-1 overflow-auto pe-2" style="scrollbar-width: thin;">
                        <div class="p-3 mb-3 rounded" style="background-color: #fff1f2; border-left: 4px solid #ef4444;">
                            <p class="mb-1 fw-bold text-dark" style="font-size: 0.80rem;">"Inflation increases due to port
                                delays"</p>
                            <span class="badge bg-danger text-white">Negative (3)</span>
                        </div>

                        <div class="p-3 rounded"
                            style="background-color: var(--matcha-50); border-left: 4px solid var(--matcha-500);">
                            <p class="mb-1 fw-bold text-dark" style="font-size: 0.80rem;">"Logistics profit stable this
                                quarter"</p>
                            <span class="badge bg-matcha">Positive (2)</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
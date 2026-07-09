@extends('layouts.app')

@section('content')
<!-- LEAFLET CSS & MARKER CLUSTER CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

<!-- KUSTOMISASI UI PETA & LAYOUT -->
<style>
    /* Mengunci halaman agar padat, tidak scroll, dan menyisakan margin bawah */
    .map-container {
        position: relative;
        /* Kalkulasi: Tinggi layar penuh dikurangi ruang untuk header, padding atas, dan margin bawah */
        height: calc(100vh - 150px); 
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        margin-bottom: 20px; /* Margin bawah agar tidak mentok */
    }

    /* Panel Pencarian Mengambang di kiri atas */
    .map-search-box {
        position: absolute;
        top: 20px;
        left: 60px; /* Diberi jarak agar tidak menabrak tombol Zoom */
        z-index: 1000;
        width: 320px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.8);
    }

    /* Panel Kontrol Layer di kanan atas */
    .map-control-panel {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 12px 15px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.8);
        width: 220px;
    }

    /* Animasi Radar Cuaca (Lingkaran Merah Berkedip) */
    .radar-pulse {
        width: 50px;
        height: 50px;
        background: rgba(239, 68, 68, 0.2);
        border: 2px solid rgba(239, 68, 68, 0.8);
        border-radius: 50%;
        animation: radar 1.5s infinite ease-out;
    }

    @keyframes radar {
        0% { transform: scale(0.3); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }
</style>

<!-- HEADER (COMPACT) -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark" style="font-size: 1.2rem;"><i class="fa-solid fa-satellite-dish me-2" style="color: var(--matcha-500);"></i>Geospatial Intelligence</h4>
        <p class="text-secondary mb-0" style="font-size: 0.8rem;">Pemantauan Real-Time 4.000+ Pelabuhan & Anomali Cuaca</p>
    </div>
</div>

<!-- WADAH PETA FULL SCREEN -->
<div class="map-container">
    <!-- Div Peta Asli -->
    <div id="geospatialMap" style="width: 100%; height: 100%;"></div>

    <!-- Fitur Search Bar Keren di Atas Peta -->
    <div class="map-search-box p-2 d-flex align-items-center gap-2">
        <i class="fa-solid fa-magnifying-glass text-muted ms-2"></i>
        <input type="text" id="mapSearchInput" class="form-control border-0 bg-transparent shadow-none" placeholder="Cari pelabuhan atau negara..." style="font-size: 0.85rem; font-weight: 500;">
        <button class="btn btn-sm text-white rounded px-2 py-1" style="background-color: var(--matcha-500); font-size: 0.75rem;">Cari</button>
    </div>

    <!-- Panel Kontrol Layar (Mengambang di kanan) -->
    <div class="map-control-panel">
        <h6 class="fw-bold mb-2 border-bottom pb-2" style="font-size: 0.8rem;"><i class="fa-solid fa-layer-group me-2"></i>Map Layers</h6>
        
        <div class="form-check form-switch mb-1">
            <input class="form-check-input" type="checkbox" id="togglePorts" checked>
            <label class="form-check-label fw-bold" for="togglePorts" style="font-size: 0.75rem; color: #1e293b;">
                <i class="fa-solid fa-anchor text-primary me-1"></i> Port Clusters
            </label>
        </div>
        
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="toggleWeather" checked>
            <label class="form-check-label fw-bold" for="toggleWeather" style="font-size: 0.75rem; color: #1e293b;">
                <i class="fa-solid fa-circle-radiation text-danger me-1"></i> Weather Threats
            </label>
        </div>
    </div>
</div>

<!-- LEAFLET JS & MARKER CLUSTER JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. INISIALISASI PETA
        const map = L.map('geospatialMap', {
            center: [2.5, 112.5], // Center di tengah Asia/Indonesia
            zoom: 4,
            zoomControl: false 
        });

        // Pindahkan tombol zoom ke bawah kiri (di bawah kolom pencarian)
        L.control.zoom({ position: 'bottomleft' }).addTo(map);

        // Tile Layer yang bersih (Voyager)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap contributors © CARTO',
            maxZoom: 18
        }).addTo(map);

        // 2. LAYER GROUPS
        const weatherLayer = L.layerGroup().addTo(map);
        
        // KUNCI PROFESIONAL: MarkerClusterGroup untuk menyatukan ribuan titik pelabuhan!
        const portCluster = L.markerClusterGroup({
            maxClusterRadius: 50,
            iconCreateFunction: function(cluster) {
                return L.divIcon({ 
                    html: `<div style="background-color: var(--matcha-500); color: white; width: 30px; height: 30px; display:flex; align-items:center; justify-content:center; border-radius: 50%; font-weight: bold; border: 2px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.2);">${cluster.getChildCount()}</div>`, 
                    className: 'my-cluster-icon', 
                    iconSize: L.point(30, 30) 
                });
            }
        });
        map.addLayer(portCluster);

        // 3. GENERATE PELABUHAN BANYAK SECARA OTOMATIS (Simulasi 50 Pelabuhan untuk test Cluster)
        const portIcon = L.divIcon({
            className: 'custom-port-icon',
            html: `<div style="background-color: #3b82f6; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>`,
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        // Kita tebar 50 pelabuhan secara acak di sekitar Asia sebagai demonstrasi clustering
        for (let i = 0; i < 50; i++) {
            let randLat = 2.5 + (Math.random() - 0.5) * 30; // Sekitar Asia
            let randLng = 112.5 + (Math.random() - 0.5) * 40;
            
            let marker = L.marker([randLat, randLng], {icon: portIcon})
                .bindPopup(`<strong style="font-size:0.85rem;">Port Cluster Demo #${i+1}</strong><br><small class="text-muted">Status: Operational</small>`);
            
            portCluster.addLayer(marker);
        }

        // 4. ANIMASI RADAR CUACA (Ini adalah indikator ancaman cuaca/Weather Threat)
        const weatherIcon = L.divIcon({
            className: 'radar-pulse',
            iconSize: [50, 50],
            iconAnchor: [25, 25]
        });

        // Contoh: Anomali cuaca di Laut Filipina
        const stormMarker = L.marker([15.0, 125.0], {icon: weatherIcon})
            .bindPopup(`<strong class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Typhoon Warning</strong><br><small>Wind: 120 km/h | Avoid Area</small>`);
        weatherLayer.addLayer(stormMarker);

        // 5. LOGIKA TOMBOL TOGGLE (ON/OFF)
        document.getElementById('togglePorts').addEventListener('change', function(e) {
            if (e.target.checked) map.addLayer(portCluster);
            else map.removeLayer(portCluster);
        });

        document.getElementById('toggleWeather').addEventListener('change', function(e) {
            if (e.target.checked) map.addLayer(weatherLayer);
            else map.removeLayer(weatherLayer);
        });

    });
</script>
@endsection
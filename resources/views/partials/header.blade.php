<!-- HEADER & FORM PENCARIAN -->
<div class="d-flex justify-content-between align-items-end mb-3 flex-shrink-0">
    <div>
        <h3 class="mb-1 fw-bold text-dark">Global Country Dashboard</h3>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Pusat komando pemantauan risiko rantai pasok global.</p>
    </div>

    <div class="d-flex gap-2 align-items-center">
        <span class="text-muted fw-bold" style="font-size: 0.85rem;"><i class="fa-solid fa-filter me-1"></i> PILIH NEGARA:</span>
        
        <form action="{{ route('country.search') }}" method="POST" class="input-group shadow-sm rounded" style="width: 380px;">
            @csrf
            <!-- Dropdown Select2 -->
            <!-- Dropdown Select2 -->
            <select name="country" class="form-select select2-country border-0">
                @php
                    $selected = request('country', isset($country) ? ($country['names']['common'] ?? 'Indonesia') : 'Indonesia');
                    // Proteksi: Jika $countriesList tidak dikirim oleh controller, gunakan array kosong
                    $list = $countriesList ?? []; 
                @endphp
                
                @forelse($list as $c)
                    <option value="{{ $c }}" {{ $selected == $c ? 'selected' : '' }}>
                        {{ $c }}
                    </option>
                @empty
                    <option value="Indonesia">Indonesia</option>
                @endforelse
            </select>
            <button type="submit" id="btn-monitor" class="btn text-white fw-bold px-4 border-0" style="background-color: var(--matcha-500);">
                <i class="fa-solid fa-magnifying-glass me-2"></i> Pantau
            </button>
        </form>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger mb-3 border-0 shadow-sm rounded-3">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
    </div>
@endif
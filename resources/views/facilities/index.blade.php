
@extends('layouts.app')

@section('title', 'Fasilitas')

@section('content')
@php
    $totalFacilities = $items->total();
@endphp

<style>
    .facility-page {
        color: #183e60;
    }

    /* HERO */
    .facility-hero {
        position: relative;
        overflow: hidden;
        min-height: 220px;
        padding: 35px 38px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
        border-radius: 22px;
        background:
            linear-gradient(100deg, rgba(13, 48, 78, .96), rgba(35, 109, 169, .87)),
            url("{{ asset('images/facilities/kelas.jpg') }}") center/cover;
        box-shadow: 0 14px 35px rgba(24, 62, 96, .13);
        margin-bottom: 28px;
    }

    .facility-hero::after {
        content: "";
        position: absolute;
        width: 270px;
        height: 270px;
        right: 8%;
        top: -145px;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 50%;
        box-shadow: 0 0 0 35px rgba(255,255,255,.035),
                    0 0 0 70px rgba(255,255,255,.025);
    }

    .facility-hero-content {
        position: relative;
        z-index: 2;
        max-width: 590px;
    }

    .facility-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 13px;
        border: 1px solid rgba(255,255,255,.25);
        background: rgba(255,255,255,.10);
        border-radius: 30px;
        color: #d9efff;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.3px;
        margin-bottom: 17px;
    }

    .facility-hero h2 {
        color: white;
        font-size: clamp(24px, 3vw, 34px);
        font-weight: 800;
        letter-spacing: -.8px;
        margin: 0 0 10px;
    }

    .facility-hero p {
        color: rgba(255,255,255,.78);
        font-size: 13px;
        line-height: 1.8;
        margin: 0;
    }

    .facility-hero-action {
        position: relative;
        z-index: 2;
        flex-shrink: 0;
    }

    .facility-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 13px 20px;
        border-radius: 11px;
        color: #236da9;
        background: white;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        box-shadow: 0 8px 22px rgba(0,0,0,.13);
        transition: .25s ease;
    }

    .facility-add-btn:hover {
        color: #183e60;
        background: #eaf5ff;
        transform: translateY(-3px);
    }

    /* SECTION HEADING */
    .facility-section-heading {
        display: flex;
        justify-content: space-between;
        align-items: end;
        gap: 15px;
        margin-bottom: 18px;
    }

    .facility-section-heading h3 {
        color: #183e60;
        font-size: 18px;
        font-weight: 800;
        margin: 0 0 5px;
    }

    .facility-section-heading p {
        color: #8799a9;
        font-size: 12px;
        margin: 0;
    }

    .facility-total {
        color: #368bd0;
        background: #eaf5ff;
        padding: 8px 13px;
        border-radius: 9px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* TOOLBAR */
    .facility-toolbar {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        background: white;
        border: 1px solid #e9f0f6;
        border-radius: 15px;
        box-shadow: 0 5px 18px rgba(24,62,96,.035);
        margin-bottom: 22px;
    }

    .facility-search {
        position: relative;
        flex: 1;
    }

    .facility-search span {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #91a6b8;
        font-size: 17px;
    }

    .facility-search input {
        width: 100%;
        height: 43px;
        padding: 0 14px 0 42px;
        border: 1px solid #e5edf4;
        border-radius: 10px;
        outline: none;
        color: #183e60;
        background: #fafdff;
        font-size: 12px;
        transition: .2s;
    }

    .facility-search input:focus {
        border-color: #75b8e8;
        box-shadow: 0 0 0 3px rgba(54,139,208,.09);
    }

    .facility-filter {
        height: 43px;
        min-width: 165px;
        padding: 0 13px;
        border: 1px solid #e5edf4;
        border-radius: 10px;
        background: #fafdff;
        color: #536d83;
        font-size: 12px;
        outline: none;
        cursor: pointer;
    }

    /* FACILITY CARDS */
    .facility-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .facility-item {
        min-width: 0;
        overflow: hidden;
        background: white;
        border: 1px solid #e9f0f6;
        border-radius: 17px;
        box-shadow: 0 6px 22px rgba(24,62,96,.045);
        transition: transform .3s ease, box-shadow .3s ease;
        animation: facilityAppear .5s ease both;
    }

    .facility-item:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 32px rgba(35,109,169,.12);
    }

    @keyframes facilityAppear {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .facility-image {
        position: relative;
        height: 175px;
        overflow: hidden;
        background: linear-gradient(135deg, #d9edff, #f0f8ff);
    }

    .facility-image img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        transition: transform .5s ease;
    }

    .facility-item:hover .facility-image img {
        transform: scale(1.07);
    }

    .facility-image-placeholder {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: #368bd0;
        font-size: 42px;
        font-weight: 800;
        background:
            radial-gradient(circle at 20% 20%, white, transparent 45%),
            linear-gradient(135deg, #dcefff, #f1f9ff);
    }

    .facility-code {
        position: absolute;
        left: 13px;
        top: 13px;
        padding: 6px 10px;
        color: #183e60;
        background: rgba(255,255,255,.92);
        border-radius: 7px;
        font-size: 10px;
        font-weight: 800;
        box-shadow: 0 3px 10px rgba(0,0,0,.07);
    }

    .facility-card-body {
        padding: 17px;
    }

    .facility-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 13px;
    }

    .facility-card-top h4 {
        color: #183e60;
        font-size: 15px;
        font-weight: 800;
        line-height: 1.5;
        margin: 0;
    }

    .facility-status {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
        text-transform: capitalize;
    }

    .facility-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-tersedia {
        color: #15966a;
        background: #e7f8f0;
    }

    .status-dipinjam {
        color: #bd841b;
        background: #fff6df;
    }

    .status-pemeliharaan {
        color: #d17a27;
        background: #fff0e3;
    }

    .status-tidak-dapat-digunakan {
        color: #d14f5b;
        background: #fff0f1;
    }

    .status-neutral {
        color: #71869a;
        background: #edf2f6;
    }

    .facility-detail-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 11px;
        padding: 13px 0;
        border-top: 1px solid #edf2f6;
        border-bottom: 1px solid #edf2f6;
    }

    .facility-detail-label {
        display: block;
        color: #98a8b6;
        font-size: 10px;
        margin-bottom: 5px;
    }

    .facility-detail-value {
        display: block;
        color: #536d83;
        font-size: 11px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .facility-condition {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 13px;
    }

    .facility-condition span:first-child {
        color: #91a2b0;
        font-size: 10px;
    }

    .facility-condition strong {
        color: #536d83;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .facility-card-actions {
        display: flex;
        gap: 8px;
        margin-top: 15px;
    }

    .facility-edit-btn,
    .facility-delete-btn {
        flex: 1;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        padding: 9px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        transition: .2s;
    }

    .facility-edit-btn {
        color: #287dbb;
        border: 1px solid #d7eafa;
        background: #f1f8ff;
        text-decoration: none;
    }

    .facility-edit-btn:hover {
        color: white;
        background: #368bd0;
        border-color: #368bd0;
    }

    .facility-delete-btn {
        color: #d45460;
        border: 1px solid #f4dfe1;
        background: #fff7f7;
    }

    .facility-delete-btn:hover {
        color: white;
        background: #d45460;
        border-color: #d45460;
    }

    /* EMPTY STATE */
    .facility-empty {
        grid-column: 1 / -1;
        padding: 55px 20px;
        text-align: center;
        background: white;
        border: 1px dashed #d7e6f2;
        border-radius: 17px;
    }

    .facility-empty-icon {
        width: 65px;
        height: 65px;
        display: grid;
        place-items: center;
        margin: 0 auto 16px;
        color: #368bd0;
        background: #eaf5ff;
        border-radius: 20px;
        font-size: 27px;
    }

    .facility-empty h4 {
        color: #183e60;
        font-size: 16px;
        font-weight: 800;
    }

    .facility-empty p {
        color: #91a2b0;
        font-size: 12px;
        margin: 7px 0 0;
    }

    .facility-pagination {
        margin-top: 25px;
    }

    .facility-pagination nav {
        display: flex;
        justify-content: center;
    }

    .facility-pagination .pagination {
        gap: 5px;
    }

    .facility-pagination .page-link {
        border: 1px solid #e5edf4;
        border-radius: 8px !important;
        color: #368bd0;
        font-size: 12px;
        box-shadow: none;
    }

    .facility-pagination .active .page-link {
        color: white;
        background: #368bd0;
        border-color: #368bd0;
    }

    @media(max-width: 1100px) {
        .facility-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 650px) {
        .facility-hero {
            min-height: 230px;
            padding: 25px 22px;
            align-items: flex-start;
            flex-direction: column;
        }

        .facility-hero-action {
            width: 100%;
        }

        .facility-add-btn {
            width: 100%;
        }

        .facility-grid {
            grid-template-columns: 1fr;
        }

        .facility-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .facility-filter {
            width: 100%;
        }

        .facility-section-heading {
            align-items: flex-start;
        }
    }
</style>

<div class="facility-page">

    {{-- HERO --}}
    <section class="facility-hero">
        <div class="facility-hero-content">
            <div class="facility-hero-tag">
                <span>✦</span>
                SISTEM INFORMASI FASILITAS KAMPUS
            </div>

            <h2>Fasilitas Kampus</h2>

            <p>
                Temukan dan pantau informasi sarana serta prasarana kampus
                dalam satu sistem yang terorganisir dan mudah diakses.
            </p>
        </div>

        @if(auth()->user()->isAdmin())
            <div class="facility-hero-action">
                <a href="/facilities/create" class="facility-add-btn">
                    <span style="font-size:18px;">＋</span>
                    Tambah Fasilitas
                </a>
            </div>
        @endif
    </section>

    {{-- JUDUL DAN JUMLAH --}}
    <div class="facility-section-heading">
        <div>
            <h3>Daftar Fasilitas</h3>
            <p>Informasi fasilitas yang terdaftar dalam sistem SIMFAS.</p>
        </div>

        <div class="facility-total">
            {{ $totalFacilities }} Fasilitas
        </div>
    </div>

    {{-- PENCARIAN DAN FILTER --}}
    <div class="facility-toolbar">
        <div class="facility-search">
            <span>⌕</span>
            <input
                type="text"
                id="facilitySearch"
                placeholder="Cari nama, kode, atau lokasi fasilitas..."
                autocomplete="off"
            >
        </div>

        <select id="facilityStatusFilter" class="facility-filter">
            <option value="">Semua Status</option>
            <option value="tersedia">Tersedia</option>
            <option value="dipinjam">Dipinjam</option>
            <option value="pemeliharaan">Pemeliharaan</option>
            <option value="tidak_dapat_digunakan">Tidak dapat digunakan</option>
        </select>
    </div>

    {{-- KARTU FASILITAS --}}
    <div class="facility-grid" id="facilityGrid">

        @forelse($items as $f)

            @php
                $facilityName = strtolower($f->name ?? '');

                // Prioritaskan foto yang diunggah pengguna.
                if ($f->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($f->image_path)) {
                    $imageUrl = asset('storage/' . $f->image_path);
                } else {
                    // Gunakan foto bawaan jika belum ada foto upload.
                    $imagePath = 'images/facilities/peralatan.jpg';

                    if (
                        str_contains($facilityName, 'kelas') ||
                        str_contains($facilityName, 'ruang belajar')
                    ) {
                        $imagePath = 'images/facilities/kelas.jpg';
                    } elseif (
                        str_contains($facilityName, 'laboratorium') ||
                        str_contains($facilityName, 'lab')
                    ) {
                        $imagePath = 'images/facilities/laboratorium.jpg';
                    } elseif (str_contains($facilityName, 'proyektor')) {
                        $imagePath = 'images/facilities/proyektor.jpg';
                    } elseif (str_contains($facilityName, 'aula')) {
                        $imagePath = 'images/facilities/aula.jpg';
                    } elseif (
                        str_contains($facilityName, 'sound') ||
                        str_contains($facilityName, 'speaker')
                    ) {
                        $imagePath = 'images/facilities/sound-system.jpg';
                    }

                    $imageUrl = file_exists(public_path($imagePath))
                        ? asset($imagePath)
                        : null;
                }

                $status = strtolower($f->status ?? '');
                $statusLabel = ucfirst(str_replace('_', ' ', $status));

                $statusClass = match($status) {
                    'tersedia' => 'status-tersedia',
                    'dipinjam' => 'status-dipinjam',
                    'pemeliharaan' => 'status-pemeliharaan',
                    'tidak_dapat_digunakan' => 'status-tidak-dapat-digunakan',
                    default => 'status-neutral',
                };
            @endphp

            <article
                class="facility-item"
                data-search="{{ strtolower(($f->code ?? '') . ' ' . ($f->name ?? '') . ' ' . ($f->location ?? '') . ' ' . ($f->condition ?? '')) }}"
                data-status="{{ $status }}"
            >

                <div class="facility-image">

                    @if($imageUrl)
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $f->name }}"
                            loading="lazy"
                        >
                    @else
                        <div class="facility-image-placeholder">
                            {{ strtoupper(substr($f->name ?? 'F', 0, 1)) }}
                        </div>
                    @endif

                    <span class="facility-code">
                        {{ $f->code }}
                    </span>
                </div>

                <div class="facility-card-body">

                    <div class="facility-card-top">
                        <h4>{{ $f->name }}</h4>

                        <span class="facility-status {{ $statusClass }}">
                            {{ $statusLabel ?: 'Belum diatur' }}
                        </span>
                    </div>

                    <div class="facility-detail-list">
                        <div>
                            <span class="facility-detail-label">Lokasi</span>
                            <span class="facility-detail-value">
                                {{ $f->location ?: '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="facility-detail-label">Jumlah Unit</span>
                            <span class="facility-detail-value">
                                {{ $f->units }} Unit
                            </span>
                        </div>
                    </div>

                    <div class="facility-condition">
                        <span>Kondisi fasilitas</span>
                        <strong>{{ $f->condition ?: '-' }}</strong>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <div class="facility-card-actions">

                            <a
                                href="/facilities/{{ $f->id }}/edit"
                                class="facility-edit-btn"
                            >
                                ✎ Ubah
                            </a>

                            <form
                                method="POST"
                                action="/facilities/{{ $f->id }}"
                                style="flex:1; margin:0;"
                                onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="facility-delete-btn"
                                    style="width:100%; cursor:pointer;"
                                >
                                    × Hapus
                                </button>
                            </form>

                        </div>
                    @endif

                </div>
            </article>

        @empty

            <div class="facility-empty">
                <div class="facility-empty-icon">▧</div>
                <h4>Belum Ada Fasilitas</h4>
                <p>Data fasilitas kampus belum tersedia di dalam sistem.</p>
            </div>

        @endforelse

        <div class="facility-empty" id="facilityNoResults" style="display:none;">
            <div class="facility-empty-icon">⌕</div>
            <h4>Fasilitas Tidak Ditemukan</h4>
            <p>Coba gunakan kata kunci atau filter status yang berbeda.</p>
        </div>

    </div>

    {{-- PAGINATION --}}
    @if($items->hasPages())
        <div class="facility-pagination">
            {{ $items->links() }}
        </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('facilitySearch');
    const statusFilter = document.getElementById('facilityStatusFilter');
    const cards = document.querySelectorAll('.facility-item');
    const noResults = document.getElementById('facilityNoResults');

    function filterFacilities() {
        const keyword = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        let visibleCount = 0;

        cards.forEach(card => {
            const searchData = card.dataset.search || '';
            const status = card.dataset.status || '';

            const matchesKeyword = searchData.includes(keyword);
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesKeyword && matchesStatus) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display =
                cards.length > 0 && visibleCount === 0 ? 'block' : 'none';
        }
    }

    searchInput.addEventListener('input', filterFacilities);
    statusFilter.addEventListener('change', filterFacilities);
});
</script>

@endsection


@extends('layouts.app')

@section('title', 'Analisis SAW')

@section('content')
@php
    $criterionCount = $criteria->count();
    $facilityCount = $all->count();
    $rankedCount = count($V);
    $winnerId = $rankedCount ? array_key_first($V) : null;
    $winner = $winnerId ? $facilities->firstWhere('id', $winnerId) : null;
    $weightPercent = round($sum * 100, 2);
    $rank = 1;
@endphp

<style>
    .saw-page {
        --saw-blue: #368bd0;
        --saw-dark: #183e60;
        --saw-soft: #eaf5ff;
        --saw-text: #334e68;
        --saw-muted: #8293a7;
        color: var(--saw-text);
        animation: sawEnter .65s ease both;
    }

    @keyframes sawEnter {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .saw-hero {
        position: relative;
        overflow: hidden;
        padding: 32px;
        border-radius: 24px;
        color: white;
        background: linear-gradient(120deg,#183e60 0%,#236da9 55%,#368bd0 100%);
        box-shadow: 0 15px 35px rgba(35,109,169,.17);
    }

    .saw-hero::before,
    .saw-hero::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        border: 35px solid rgba(255,255,255,.07);
        pointer-events: none;
    }

    .saw-hero::before {
        width: 250px;
        height: 250px;
        right: -70px;
        top: -125px;
    }

    .saw-hero::after {
        width: 160px;
        height: 160px;
        right: 125px;
        bottom: -115px;
    }

    .saw-hero-content {
        position: relative;
        z-index: 1;
    }

    .saw-eyebrow {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        opacity: .75;
    }

    .saw-hero h2 {
        font-weight: 800;
        letter-spacing: -.7px;
        margin: 10px 0;
    }

    .saw-hero p {
        max-width: 620px;
        color: rgba(255,255,255,.78);
        margin-bottom: 0;
        line-height: 1.7;
    }

    .saw-hero-icon {
        width: 75px;
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 35px;
        border-radius: 22px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.2);
        backdrop-filter: blur(8px);
    }

    .saw-stat {
        background: white;
        border: 1px solid #eaf0f6;
        border-radius: 18px;
        padding: 21px;
        height: 100%;
        transition: .25s ease;
        box-shadow: 0 5px 18px rgba(24,62,96,.035);
    }

    .saw-stat:hover {
        transform: translateY(-5px);
        box-shadow: 0 13px 28px rgba(24,62,96,.09);
    }

    .saw-stat-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: var(--saw-soft);
        color: var(--saw-blue);
        font-size: 20px;
        margin-bottom: 15px;
    }

    .saw-stat-label {
        color: var(--saw-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .saw-stat-number {
        color: var(--saw-dark);
        font-size: 25px;
        font-weight: 800;
        margin-top: 4px;
    }

    .saw-section {
        background: white;
        border: 1px solid #eaf0f6;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 22px;
        box-shadow: 0 5px 20px rgba(24,62,96,.035);
        scroll-margin-top: 90px;
    }

    .saw-section-heading {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        margin-bottom: 22px;
    }

    .saw-section-icon {
        flex-shrink: 0;
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: var(--saw-soft);
        color: var(--saw-blue);
        font-size: 19px;
    }

    .saw-section-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--saw-dark);
        margin: 0 0 5px;
    }

    .saw-section-desc {
        font-size: 12px;
        color: var(--saw-muted);
        margin: 0;
        line-height: 1.6;
    }

    .saw-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin: 22px 0;
    }

    .saw-nav a {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 15px;
        border-radius: 11px;
        color: #55718c;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #e5edf5;
        background: white;
        transition: .2s ease;
    }

    .saw-nav a:hover {
        background: var(--saw-soft);
        border-color: #b9dafa;
        color: var(--saw-blue);
        transform: translateY(-2px);
    }

    .saw-winner {
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 160px;
        padding: 23px;
        border-radius: 19px;
        background: linear-gradient(135deg,#eff8ff,#e4f1ff);
        border: 1px solid #d7eaff;
    }

    .saw-winner::after {
        content: "🏆";
        position: absolute;
        right: 15px;
        bottom: 5px;
        font-size: 75px;
        opacity: .13;
        transform: rotate(-12deg);
    }

    .saw-winner-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        color: #368bd0;
    }

    .saw-winner-name {
        color: #183e60;
        font-size: 22px;
        font-weight: 800;
        margin: 10px 0 5px;
    }

    .saw-winner-score {
        color: #368bd0;
        font-size: 14px;
        font-weight: 800;
    }

    .saw-weight-card {
        padding: 17px;
        height: 100%;
        border: 1px solid #e8eef5;
        border-radius: 15px;
        background: #fbfdff;
        transition: .2s ease;
    }

    .saw-weight-card:focus-within {
        border-color: #8dc5f2;
        box-shadow: 0 0 0 3px rgba(54,139,208,.09);
    }

    .saw-weight-name {
        color: var(--saw-dark);
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .saw-type {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .saw-type-benefit {
        background: #e5f7ed;
        color: #218653;
    }

    .saw-type-cost {
        background: #fff1e8;
        color: #c46b32;
    }

    .saw-weight-input {
        font-size: 17px;
        font-weight: 800;
        color: var(--saw-dark);
        border-radius: 10px;
        border-color: #dce7f0;
        margin-top: 12px;
    }

    .saw-weight-total {
        background: #f1f8ff;
        border: 1px solid #dceeff;
        border-radius: 14px;
        padding: 16px;
    }

    .saw-progress {
        height: 8px;
        background: #dceaf6;
        border-radius: 20px;
        overflow: hidden;
        margin-top: 11px;
    }

    .saw-progress-fill {
        height: 100%;
        width: 0;
        background: linear-gradient(90deg,#368bd0,#63b2ed);
        border-radius: inherit;
        transition: width .3s ease;
    }

    .saw-score-input {
        border-radius: 11px;
        border-color: #dce7f0;
        padding: 12px;
    }

    .saw-score-input:focus,
    .saw-weight-input:focus {
        border-color: #72b5ed;
        box-shadow: 0 0 0 3px rgba(54,139,208,.1);
    }

    .saw-primary-btn {
        border: 0;
        border-radius: 11px;
        padding: 12px 20px;
        color: white;
        font-weight: 800;
        background: linear-gradient(110deg,#236da9,#368bd0);
        box-shadow: 0 5px 13px rgba(54,139,208,.2);
        transition: .2s ease;
    }

    .saw-primary-btn:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 9px 20px rgba(54,139,208,.27);
    }

    .saw-table {
        min-width: 620px;
        margin-bottom: 0;
    }

    .saw-table thead th {
        background: #f3f8fc;
        color: #607b95;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 14px;
        border-bottom: 1px solid #e5edf5;
        white-space: nowrap;
    }

    .saw-table tbody td {
        padding: 14px;
        font-size: 13px;
        vertical-align: middle;
        border-color: #edf2f7;
    }

    .saw-table tbody tr {
        transition: background .2s ease;
    }

    .saw-table tbody tr:hover {
        background: #f5faff;
    }

    .saw-rank {
        width: 30px;
        height: 30px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 9px;
        background: #edf4fa;
        color: #42617e;
        font-weight: 800;
    }

    .saw-rank.first {
        background: #fff2ce;
        color: #b37a0b;
    }

    .saw-score {
        color: #236da9;
        font-weight: 800;
    }

    .saw-empty {
        text-align: center;
        padding: 35px 20px;
        color: var(--saw-muted);
    }

    .saw-empty-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .saw-explain {
        background: #f2f8fd;
        border: 1px solid #dfedfa;
        padding: 17px;
        border-radius: 14px;
        color: #56718b;
        font-size: 12px;
        line-height: 1.8;
    }

    .saw-step {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 13px;
    }

    .saw-step:last-child {
        margin-bottom: 0;
    }

    .saw-step-number {
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
        color: white;
        background: var(--saw-blue);
        font-size: 11px;
        font-weight: 800;
    }

    @media(max-width:767px) {
        .saw-hero { padding: 23px; }
        .saw-section { padding: 19px; }
        .saw-hero-icon { display: none; }
        .saw-stat-number { font-size: 22px; }
    }
</style>

<div class="saw-page">

    {{-- HERO --}}
    <div class="saw-hero mb-4">
        <div class="saw-hero-content d-flex justify-content-between align-items-center gap-3">
            <div>
                <div class="saw-eyebrow">DECISION SUPPORT SYSTEM</div>
                <h2>Analisis Prioritas Fasilitas</h2>
                <p>
                    Gunakan metode Simple Additive Weighting (SAW)
                    untuk membantu menentukan fasilitas yang paling
                    membutuhkan prioritas pemeliharaan.
                </p>
                <div class="mt-4">
                    <a href="#saw-ranking" class="btn btn-light btn-sm px-3 py-2 fw-bold">
                        Lihat Hasil Ranking ↓
                    </a>
                </div>
            </div>
            <div class="saw-hero-icon">📊</div>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="saw-stat">
                <div class="saw-stat-icon">🏢</div>
                <div class="saw-stat-label">Total Fasilitas</div>
                <div class="saw-stat-number">{{ $facilityCount }}</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="saw-stat">
                <div class="saw-stat-icon">📋</div>
                <div class="saw-stat-label">Jumlah Kriteria</div>
                <div class="saw-stat-number">{{ $criterionCount }}</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="saw-stat">
                <div class="saw-stat-icon">⚖️</div>
                <div class="saw-stat-label">Total Bobot</div>
                <div class="saw-stat-number">{{ $weightPercent }}%</div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="saw-stat">
                <div class="saw-stat-icon">🏆</div>
                <div class="saw-stat-label">Fasilitas Dinilai</div>
                <div class="saw-stat-number">{{ $rankedCount }}</div>
            </div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <div class="saw-nav">
        <a href="#saw-ranking">🏆 Hasil Ranking</a>
        <a href="#saw-weights">⚖️ Bobot Kriteria</a>
        <a href="#saw-input">✏️ Input Penilaian</a>
        <a href="#saw-matrix">📊 Matriks SAW</a>
        <a href="#saw-guide">💡 Panduan</a>
    </div>

    {{-- TOP RESULT --}}
    <div class="row g-3 mb-4" id="saw-ranking">
        <div class="col-lg-7">
            <div class="saw-winner">
                <div class="saw-winner-label">🏆 HASIL TERBAIK SAAT INI</div>

                @if($winner)
                    <div class="saw-winner-name">{{ $winner->name }}</div>
                    <div class="saw-winner-score">
                        Nilai preferensi: {{ number_format($V[$winnerId], 4) }}
                    </div>
                    <div class="small text-muted mt-3">
                        Peringkat pertama berdasarkan nilai akhir perhitungan SAW.
                    </div>
                @else
                    <div class="saw-winner-name">Belum ada hasil</div>
                    <div class="small text-muted mt-2">
                        Masukkan penilaian fasilitas terlebih dahulu untuk melihat rekomendasi.
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="saw-section h-100 mb-0">
                <div class="saw-section-title mb-2">Apa arti hasil ini?</div>
                <p class="saw-section-desc" style="line-height:1.8">
                    Sistem menghitung nilai setiap fasilitas berdasarkan
                    kriteria dan bobot yang telah ditentukan.
                    Nilai akhir yang lebih tinggi menunjukkan prioritas
                    pemeliharaan yang lebih tinggi menurut model SAW ini.
                </p>
                <div class="mt-3 small text-muted">
                    <strong>Catatan:</strong> Hasil merupakan rekomendasi
                    pendukung keputusan. Keputusan akhir tetap pada Admin.
                </div>
            </div>
        </div>
    </div>

    {{-- WEIGHTS --}}
    <section class="saw-section" id="saw-weights">
        <div class="saw-section-heading">
            <div class="saw-section-icon">⚖️</div>
            <div>
                <h5 class="saw-section-title">Pengaturan Bobot Kriteria</h5>
                <p class="saw-section-desc">
                    Atur seberapa penting setiap kriteria dalam menentukan prioritas.
                    Total seluruh bobot wajib 100%.
                </p>
            </div>
        </div>

        <form method="POST" action="/saw/weights" id="sawWeightForm">
            @csrf

            <div class="row g-3">
                @foreach($criteria as $c)
                    <div class="col-md-6 col-xl-4">
                        <div class="saw-weight-card">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <div class="saw-weight-name">{{ $c->name }}</div>
                                <span class="saw-type {{ $c->type === 'benefit' ? 'saw-type-benefit' : 'saw-type-cost' }}">
                                    {{ $c->type }}
                                </span>
                            </div>

                            <div class="small text-muted">
                                @if($c->type === 'benefit')
                                    Semakin tinggi nilai, semakin baik.
                                @else
                                    Semakin rendah nilai, semakin baik.
                                @endif
                            </div>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="1"
                                name="weights[{{ $c->id }}]"
                                value="{{ old('weights.'.$c->id, $c->weight) }}"
                                class="form-control saw-weight-input saw-weight-field"
                                required
                            >

                            <div class="small text-muted mt-2">
                                Bobot dalam desimal (contoh: 0.25 = 25%)
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="saw-weight-total mt-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="fw-bold text-dark">Total bobot yang diatur</div>
                        <div class="small text-muted">Target keseluruhan adalah 1.00 atau 100%</div>
                    </div>
                    <div class="fw-bold fs-5" id="sawWeightDisplay">
                        {{ number_format($weightPercent, 2) }}%
                    </div>
                </div>

                <div class="saw-progress">
                    <div class="saw-progress-fill" id="sawWeightProgress"
                         style="width: {{ min(100, max(0, $weightPercent)) }}%"></div>
                </div>

                <div class="small mt-2" id="sawWeightMessage">
                    @if(abs($sum - 1) < 0.0001)
                        <span class="text-success">✓ Total bobot sudah sesuai.</span>
                    @else
                        <span class="text-danger">Total bobot harus tepat 100% sebelum disimpan.</span>
                    @endif
                </div>
            </div>

            <button type="submit" class="saw-primary-btn mt-3">
                💾 Simpan Bobot Kriteria
            </button>
        </form>
    </section>

    {{-- SCORE INPUT --}}
    <section class="saw-section" id="saw-input">
        <div class="saw-section-heading">
            <div class="saw-section-icon">✏️</div>
            <div>
                <h5 class="saw-section-title">Input Penilaian Fasilitas</h5>
                <p class="saw-section-desc">
                    Pilih fasilitas, kemudian berikan nilai 1 sampai 5
                    untuk setiap kriteria.
                </p>
            </div>
        </div>

        @if($all->count())
            <form method="POST" action="/saw/score">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold small text-dark">
                        Pilih fasilitas yang akan dinilai
                    </label>
                    <select name="facility_id" class="form-select saw-score-input" required>
                        <option value="">-- Pilih fasilitas --</option>
                        @foreach($all as $f)
                            <option value="{{ $f->id }}" {{ old('facility_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }} — {{ $f->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3">
                    @foreach($criteria as $c)
                        <div class="col-md-6 col-xl-4">
                            <div class="saw-weight-card">
                                <div class="saw-weight-name">{{ $c->name }}</div>
                                <div class="small text-muted mb-3">
                                    {{ $c->type === 'benefit' ? 'Benefit: nilai tinggi lebih baik' : 'Cost: nilai rendah lebih baik' }}
                                </div>

                                <label class="form-label small fw-bold">
                                    Nilai penilaian (1–5)
                                </label>

                                <select
                                    name="values[{{ $c->id }}]"
                                    class="form-select saw-score-input"
                                    required
                                >
                                    <option value="">Pilih nilai</option>
                                    @for($score = 1; $score <= 5; $score++)
                                        <option value="{{ $score }}"
                                            {{ old('values.'.$c->id) == $score ? 'selected' : '' }}>
                                            {{ $score }} — {{ [
                                                1 => 'Sangat rendah',
                                                2 => 'Rendah',
                                                3 => 'Sedang',
                                                4 => 'Tinggi',
                                                5 => 'Sangat tinggi'
                                            ][$score] }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="saw-explain mt-3">
                    <strong>Petunjuk:</strong> Berikan nilai berdasarkan kondisi
                    nyata fasilitas dan definisi setiap kriteria. Untuk kriteria
                    bertipe cost, sistem akan membalik perbandingan saat normalisasi.
                    Pastikan skala penilaian diterapkan secara konsisten.
                </div>

                <button type="submit" class="saw-primary-btn mt-3">
                    ✓ Simpan Penilaian Fasilitas
                </button>
            </form>
        @else
            <div class="saw-empty">
                <div class="saw-empty-icon">🏢</div>
                <strong>Belum ada fasilitas</strong>
                <p class="small mb-0 mt-2">
                    Tambahkan fasilitas terlebih dahulu sebelum melakukan penilaian.
                </p>
            </div>
        @endif
    </section>

    {{-- RANKING --}}
    <section class="saw-section">
        <div class="saw-section-heading">
            <div class="saw-section-icon">🏆</div>
            <div>
                <h5 class="saw-section-title">Hasil Perankingan Fasilitas</h5>
                <p class="saw-section-desc">
                    Urutan prioritas berdasarkan nilai preferensi SAW.
                    Semakin tinggi nilai, semakin tinggi prioritasnya.
                </p>
            </div>
        </div>

        @if($rankedCount)
            <div class="table-responsive">
                <table class="table saw-table">
                    <thead>
                        <tr>
                            <th>Ranking</th>
                            <th>Nama Fasilitas</th>
                            <th>Nilai Akhir (V)</th>
                            <th>Visual Skor</th>
                            <th>Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($V as $fid => $v)
                            @php
                                $facility = $facilities->firstWhere('id', $fid);
                                $percentage = max(0, min(100, $v * 100));
                            @endphp
                            <tr>
                                <td>
                                    <span class="saw-rank {{ $rank === 1 ? 'first' : '' }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ $facility?->name ?? 'Fasilitas tidak ditemukan' }}
                                </td>
                                <td class="saw-score">
                                    {{ number_format($v, 4) }}
                                </td>
                                <td style="min-width:130px">
                                    <div class="saw-progress mt-0">
                                        <div class="saw-progress-fill"
                                             style="width:{{ $percentage }}%"></div>
                                    </div>
                                </td>
                                <td>
                                    @if($rank === 1)
                                        <span class="badge bg-success">Prioritas utama</span>
                                    @elseif($rank <= 3)
                                        <span class="badge bg-primary">Prioritas berikutnya</span>
                                    @else
                                        <span class="badge bg-light text-dark">Urutan {{ $rank }}</span>
                                    @endif
                                </td>
                            </tr>
                            @php $rank++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="saw-empty">
                <div class="saw-empty-icon">📊</div>
                <strong>Belum ada hasil perankingan</strong>
                <p class="small mb-0 mt-2">
                    Masukkan penilaian fasilitas untuk mulai menghasilkan ranking.
                </p>
            </div>
        @endif
    </section>

    {{-- MATRICES --}}
    <section class="saw-section" id="saw-matrix">
        <div class="saw-section-heading">
            <div class="saw-section-icon">📐</div>
            <div>
                <h5 class="saw-section-title">Matriks Perhitungan SAW</h5>
                <p class="saw-section-desc">
                    Lihat data asli dan hasil normalisasi yang digunakan dalam perhitungan.
                </p>
            </div>
        </div>

        @if($rankedCount)
            @php
                $tables = [
                    [
                        'title' => 'Matriks Keputusan (X)',
                        'description' => 'Nilai awal dari setiap fasilitas untuk setiap kriteria.',
                        'data' => $X,
                    ],
                    [
                        'title' => 'Matriks Normalisasi (R)',
                        'description' => 'Nilai yang sudah disesuaikan menggunakan rumus benefit atau cost.',
                        'data' => $R,
                    ],
                ];
            @endphp

            @foreach($tables as $table)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-1">{{ $table['title'] }}</h6>
                    <p class="small text-muted mb-3">{{ $table['description'] }}</p>

                    <div class="table-responsive border rounded-3">
                        <table class="table saw-table">
                            <thead>
                                <tr>
                                    <th>Fasilitas</th>
                                    @foreach($criteria as $c)
                                        <th>{{ $c->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facilities as $f)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $f->name }}</td>
                                        @foreach($criteria as $c)
                                            <td>
                                                {{ number_format($table['data'][$f->id][$c->id] ?? 0, 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="saw-empty">
                Matriks akan muncul setelah tersedia data penilaian fasilitas.
            </div>
        @endif
    </section>

    {{-- GUIDE --}}
    <section class="saw-section" id="saw-guide">
        <div class="saw-section-heading">
            <div class="saw-section-icon">💡</div>
            <div>
                <h5 class="saw-section-title">Bagaimana Sistem SAW Bekerja?</h5>
                <p class="saw-section-desc">
                    Panduan singkat untuk memahami proses pengambilan keputusan.
                </p>
            </div>
        </div>

        <div class="saw-explain">
            <div class="saw-step">
                <div class="saw-step-number">1</div>
                <div>
                    <strong>Menentukan kriteria dan bobot</strong><br>
                    Tentukan faktor penilaian dan tingkat kepentingannya.
                    Seluruh bobot harus berjumlah 100%.
                </div>
            </div>

            <div class="saw-step">
                <div class="saw-step-number">2</div>
                <div>
                    <strong>Memberikan nilai fasilitas</strong><br>
                    Setiap fasilitas diberi nilai 1 sampai 5 untuk semua kriteria.
                </div>
            </div>

            <div class="saw-step">
                <div class="saw-step-number">3</div>
                <div>
                    <strong>Melakukan normalisasi</strong><br>
                    Sistem menyesuaikan nilai berdasarkan jenis kriteria:
                    benefit atau cost.
                </div>
            </div>

            <div class="saw-step">
                <div class="saw-step-number">4</div>
                <div>
                    <strong>Menghitung nilai akhir</strong><br>
                    Nilai normalisasi dikalikan bobot masing-masing kriteria,
                    lalu seluruh hasilnya dijumlahkan.
                </div>
            </div>

            <div class="saw-step">
                <div class="saw-step-number">5</div>
                <div>
                    <strong>Menentukan ranking</strong><br>
                    Fasilitas dengan nilai akhir tertinggi menempati ranking pertama.
                </div>
            </div>
        </div>

        <div class="mt-3 p-3 rounded-3" style="background:#f8fafc">
            <div class="fw-bold text-dark mb-2">Rumus nilai akhir</div>
            <div class="text-center py-2">
                <strong style="font-size:19px;color:#236da9">
                    Vᵢ = Σ (wⱼ × rᵢⱼ)
                </strong>
            </div>
            <div class="small text-muted">
                V = nilai akhir, w = bobot kriteria, dan r = nilai normalisasi.
            </div>
        </div>
    </section>

    <div class="text-center small text-muted pb-3">
        SIMFAS Kampus · Sistem Pendukung Keputusan Metode SAW
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fields = document.querySelectorAll('.saw-weight-field');
    const display = document.getElementById('sawWeightDisplay');
    const progress = document.getElementById('sawWeightProgress');
    const message = document.getElementById('sawWeightMessage');

    function updateWeightTotal() {
        let total = 0;

        fields.forEach(function (field) {
            total += parseFloat(field.value) || 0;
        });

        const percent = total * 100;
        display.textContent = percent.toFixed(2) + '%';
        progress.style.width = Math.min(100, Math.max(0, percent)) + '%';

        if (Math.abs(total - 1) < 0.0001) {
            message.innerHTML = '<span class="text-success">✓ Total bobot sudah sesuai.</span>';
        } else {
            message.innerHTML = '<span class="text-danger">Total bobot harus tepat 100% sebelum disimpan.</span>';
        }
    }

    fields.forEach(function (field) {
        field.addEventListener('input', updateWeightTotal);
    });

    updateWeightTotal();

    const form = document.getElementById('sawWeightForm');

    if (form) {
        form.addEventListener('submit', function (event) {
            let total = 0;

            fields.forEach(function (field) {
                total += parseFloat(field.value) || 0;
            });

            if (Math.abs(total - 1) > 0.0001) {
                event.preventDefault();
                alert('Total bobot harus tepat 100% atau 1.00. Silakan periksa kembali.');
            }
        });
    }
});
</script>
@endsection

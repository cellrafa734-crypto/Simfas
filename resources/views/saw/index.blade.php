@extends('layouts.app')

@section('title', 'Prioritas SAW')

@section('content')
@php
    // Hanya mengolah data yang sudah dikirim controller (tidak mengubah rumus SAW).
    $criterionCount = $criteria->count();
    $totalFacilities = $all->count();
    $rankedCount = count($V);
    $unscoredCount = max(0, $totalFacilities - $facilities->count());
    $winnerId = $rankedCount ? array_key_first($V) : null;
    $winner = $winnerId ? $facilities->firstWhere('id', $winnerId) : null;
    $weightPercent = round($sum * 100, 2);
    $weightOk = abs($sum - 1) < 0.0001;
    $rank = 1;
    $scoreLabels = [1 => 'Sangat rendah', 2 => 'Rendah', 3 => 'Sedang', 4 => 'Tinggi', 5 => 'Sangat tinggi'];
    $typeLabel = ['benefit' => 'Benefit', 'cost' => 'Cost'];
@endphp

<style>
    .saw-hero {
        position: relative;
        overflow: hidden;
        padding: 32px;
        border-radius: var(--radius-lg);
        color: #fff;
        background:
            radial-gradient(120% 90% at 100% 0%, rgba(96, 165, 250, .35), transparent 60%),
            linear-gradient(120deg, var(--navy-900) 0%, var(--navy-700) 60%, var(--blue-600) 100%);
        box-shadow: var(--shadow-md);
    }
    .saw-hero h2 { color: #fff; font-weight: 800; margin: 8px 0 10px; }
    .saw-hero p { max-width: 640px; margin: 0; color: rgba(255, 255, 255, .8); line-height: 1.7; }
    .saw-eyebrow { font-size: .68rem; font-weight: 800; letter-spacing: .16em; opacity: .75; }

    .saw-stat { height: 100%; padding: 18px 20px; }
    .saw-stat-label { color: var(--muted); font-size: .75rem; font-weight: 700; }
    .saw-stat-number { color: var(--navy-900); font-size: 1.55rem; font-weight: 800; margin-top: 2px; }
    .saw-stat-number.is-bad { color: #b91c1c; }
    .saw-stat-note { color: var(--muted); font-size: .72rem; margin-top: 2px; }

    .saw-nav { display: flex; flex-wrap: wrap; gap: 8px; margin: 20px 0; }
    .saw-nav a {
        padding: 9px 14px;
        border: 1px solid var(--line);
        border-radius: var(--radius-md);
        background: #fff;
        color: var(--ink-soft);
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
        transition: background-color .2s, border-color .2s, color .2s;
    }
    .saw-nav a:hover { background: var(--blue-50); border-color: #bcd3f7; color: var(--blue-700); }

    .saw-block { scroll-margin-top: calc(var(--header-h) + 16px); margin-bottom: 22px; }
    .saw-block .card-body { padding: 1.5rem; }
    .saw-title { font-size: 1.05rem; font-weight: 800; color: var(--navy-900); margin: 0 0 4px; }
    .saw-desc { color: var(--muted); font-size: .8rem; line-height: 1.6; margin: 0 0 18px; }

    .saw-winner {
        height: 100%;
        padding: 22px;
        border: 1px solid var(--blue-100);
        border-radius: var(--radius-lg);
        background: linear-gradient(135deg, var(--blue-50), #e4efff);
    }
    .saw-winner-label { color: var(--blue-700); font-size: .7rem; font-weight: 800; letter-spacing: .1em; }
    .saw-winner-name { color: var(--navy-900); font-size: 1.4rem; font-weight: 800; margin: 8px 0 4px; overflow-wrap: anywhere; }
    .saw-winner-score { color: var(--blue-600); font-weight: 800; }

    .saw-field-card {
        height: 100%;
        padding: 16px;
        border: 1px solid var(--line);
        border-radius: var(--radius-md);
        background: #fbfdff;
        transition: border-color .2s, box-shadow .2s;
    }
    .saw-field-card:focus-within { border-color: var(--blue-400); box-shadow: 0 0 0 4px rgba(59, 130, 246, .12); }
    .saw-field-name { color: var(--navy-900); font-size: .85rem; font-weight: 800; }
    .saw-hint { color: var(--muted); font-size: .75rem; }

    .saw-type {
        flex: none;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .saw-type-benefit { background: #e5f7ed; color: #166534; }
    .saw-type-cost { background: #fff1e8; color: #9a4a14; }

    .saw-weight-input { font-size: 1.05rem; font-weight: 800; color: var(--navy-900); }

    .saw-total {
        padding: 16px;
        border: 1px solid var(--blue-100);
        border-radius: var(--radius-md);
        background: var(--blue-50);
    }
    .saw-bar { height: 8px; margin-top: 10px; overflow: hidden; border-radius: 99px; background: var(--blue-100); }
    .saw-bar-fill {
        height: 100%;
        width: 0;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--blue-500), var(--blue-400));
        transition: width .3s ease, background-color .3s;
    }
    .saw-bar-fill.is-bad { background: #ef4444; }

    .saw-rank {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: var(--radius-sm);
        background: #edf2fb;
        color: var(--ink-soft);
        font-weight: 800;
    }
    .saw-rank.first { background: #fff2ce; color: #8a5a00; }
    .saw-score { color: var(--blue-700); font-weight: 800; }

    .saw-empty { padding: 30px 16px; text-align: center; color: var(--muted); }
    .saw-info {
        padding: 14px 16px;
        border-radius: var(--radius-md);
        background: #f4f8fe;
        border: 1px solid var(--line);
        color: var(--ink-soft);
        font-size: .8rem;
        line-height: 1.7;
    }

    .saw-table-box { border: 1px solid var(--line); border-radius: var(--radius-lg); overflow: hidden; }
    .saw-table-box .table-responsive { border-radius: 0; }

    .saw-step { display: flex; gap: 12px; margin-bottom: 12px; }
    .saw-step:last-child { margin-bottom: 0; }
    .saw-step-number {
        flex: none;
        display: grid;
        place-items: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: var(--blue-600);
        color: #fff;
        font-size: .72rem;
        font-weight: 800;
    }

    @media (max-width: 767.98px) {
        .saw-hero { padding: 22px; }
        .saw-block .card-body { padding: 1.1rem; }
        .saw-stat-number { font-size: 1.3rem; }
        .saw-winner-name { font-size: 1.2rem; }
        .saw-primary-block-btn { width: 100%; }
    }
</style>

<div class="saw-page">

    {{-- PEMBUKA --}}
    <div class="saw-hero mb-4">
        <div class="saw-eyebrow">SISTEM PENDUKUNG KEPUTUSAN</div>
        <h2>Analisis Prioritas Pemeliharaan Fasilitas</h2>
        <p>
            Metode Simple Additive Weighting (SAW) membantu admin menentukan fasilitas
            yang paling perlu diprioritaskan untuk dirawat, berdasarkan kriteria dan bobot.
        </p>
        <a href="#saw-ranking" class="btn btn-light btn-sm px-3 mt-3 fw-bold">Lihat hasil ranking ↓</a>
    </div>

    {{-- RINGKASAN --}}
    <div class="row g-3 mt-1">
        <div class="col-6 col-xl-3">
            <div class="card saw-stat">
                <div class="saw-stat-label">Total Fasilitas</div>
                <div class="saw-stat-number">{{ $totalFacilities }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card saw-stat">
                <div class="saw-stat-label">Jumlah Kriteria</div>
                <div class="saw-stat-number">{{ $criterionCount }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card saw-stat">
                <div class="saw-stat-label">Total Bobot</div>
                <div class="saw-stat-number {{ $weightOk ? '' : 'is-bad' }}">{{ $weightPercent }}%</div>
                <div class="saw-stat-note">{{ $weightOk ? 'Sudah sesuai (100%)' : 'Harus tepat 100%' }}</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card saw-stat">
                <div class="saw-stat-label">Fasilitas Sudah Dinilai</div>
                <div class="saw-stat-number">{{ $rankedCount }}</div>
                @if($unscoredCount > 0)
                    <div class="saw-stat-note">{{ $unscoredCount }} fasilitas belum dinilai</div>
                @endif
            </div>
        </div>
    </div>

    <nav class="saw-nav" aria-label="Pintasan bagian halaman">
        <a href="#saw-ranking">Hasil Ranking</a>
        <a href="#saw-weights">Bobot Kriteria</a>
        <a href="#saw-input">Input Penilaian</a>
        <a href="#saw-matrix">Matriks X dan R</a>
        <a href="#saw-guide">Panduan</a>
    </nav>

    @unless($weightOk)
        <div class="alert alert-warning" role="alert">
            Total bobot saat ini {{ $weightPercent }}%, belum 100%. Perbaiki di bagian
            <a href="#saw-weights" class="alert-link">Bobot Kriteria</a>
            agar hasil ranking akurat.
        </div>
    @endunless

    {{-- HASIL TERBAIK --}}
    <div class="row g-3 mb-4" id="saw-ranking">
        <div class="col-lg-7">
            <div class="saw-winner">
                <div class="saw-winner-label">PRIORITAS TERTINGGI SAAT INI</div>
                @if($winner)
                    <div class="saw-winner-name">{{ $winner->name }}</div>
                    <div class="saw-winner-score">Nilai preferensi: {{ number_format($V[$winnerId], 4) }}</div>
                    <div class="saw-hint mt-3">Peringkat pertama berdasarkan nilai akhir perhitungan SAW.</div>
                @else
                    <div class="saw-winner-name">Belum ada hasil</div>
                    <div class="saw-hint mt-2">Isi penilaian fasilitas terlebih dahulu untuk melihat rekomendasi.</div>
                @endif
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <div class="saw-title">Apa arti hasil ini?</div>
                    <p class="saw-desc mb-2" style="line-height:1.8">
                        Nilai akhir yang lebih tinggi berarti prioritas pemeliharaan yang lebih tinggi
                        menurut model SAW ini.
                    </p>
                    <div class="saw-hint">
                        <strong>Catatan:</strong> hasil hanyalah rekomendasi pendukung keputusan.
                        Keputusan akhir tetap ada pada admin.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOBOT --}}
    <section class="card saw-block" id="saw-weights">
        <div class="card-body">
            <h5 class="saw-title">Pengaturan Bobot Kriteria</h5>
            <p class="saw-desc">
                Atur seberapa penting tiap kriteria. Isi dengan desimal (contoh: 0.25 = 25%).
                Jumlah seluruh bobot harus tepat 1 (100%).
            </p>

            @if($criterionCount)
                <form method="POST" action="/saw/weights" id="sawWeightForm" novalidate>
                    @csrf

                    <div class="row g-3">
                        @foreach($criteria as $c)
                            <div class="col-md-6 col-xl-4">
                                <div class="saw-field-card">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <label for="weight-{{ $c->id }}" class="saw-field-name mb-0">{{ $c->name }}</label>
                                        <span class="saw-type {{ $c->type === 'benefit' ? 'saw-type-benefit' : 'saw-type-cost' }}">
                                            {{ $typeLabel[$c->type] ?? $c->type }}
                                        </span>
                                    </div>
                                    <div class="saw-hint mt-1">
                                        {{ $c->type === 'benefit' ? 'Semakin tinggi nilai, semakin diprioritaskan.' : 'Semakin rendah nilai, semakin diprioritaskan.' }}
                                    </div>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="1"
                                        inputmode="decimal"
                                        id="weight-{{ $c->id }}"
                                        name="weights[{{ $c->id }}]"
                                        value="{{ old('weights.'.$c->id, $c->weight) }}"
                                        class="form-control saw-weight-input saw-weight-field mt-3"
                                        required
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="saw-total mt-3" aria-live="polite">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-bold" style="color:var(--navy-900)">Total bobot yang diatur</div>
                                <div class="saw-hint">Target: 1.00 atau 100%</div>
                            </div>
                            <div class="fw-bold fs-5" id="sawWeightDisplay">{{ number_format($weightPercent, 2) }}%</div>
                        </div>
                        <div class="saw-bar">
                            <div class="saw-bar-fill {{ $weightOk ? '' : 'is-bad' }}" id="sawWeightProgress"
                                 style="width: {{ min(100, max(0, $weightPercent)) }}%"></div>
                        </div>
                        <div class="small mt-2" id="sawWeightMessage">
                            @if($weightOk)
                                <span class="text-success">Total bobot sudah sesuai.</span>
                            @else
                                <span class="text-danger">Total bobot harus tepat 100% sebelum disimpan.</span>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3 saw-primary-block-btn">Simpan Bobot Kriteria</button>
                </form>
            @else
                <div class="saw-empty">
                    <strong>Belum ada kriteria</strong>
                    <p class="small mb-0 mt-2">Data kriteria belum tersedia. Jalankan <code>php artisan migrate --seed</code>.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- INPUT NILAI --}}
    <section class="card saw-block" id="saw-input">
        <div class="card-body">
            <h5 class="saw-title">Input Penilaian Fasilitas</h5>
            <p class="saw-desc">Pilih fasilitas, lalu beri nilai 1 sampai 5 untuk setiap kriteria. Semua kriteria wajib diisi.</p>

            @if($totalFacilities && $criterionCount)
                <form method="POST" action="/saw/score">
                    @csrf

                    <div class="mb-4">
                        <label for="sawFacility" class="form-label fw-bold">Fasilitas yang dinilai</label>
                        <select id="sawFacility" name="facility_id" class="form-select" required>
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
                                <div class="saw-field-card">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <label for="value-{{ $c->id }}" class="saw-field-name mb-0">{{ $c->name }}</label>
                                        <span class="saw-type {{ $c->type === 'benefit' ? 'saw-type-benefit' : 'saw-type-cost' }}">
                                            {{ $typeLabel[$c->type] ?? $c->type }}
                                        </span>
                                    </div>
                                    <div class="saw-hint mt-1 mb-3">
                                        {{ $c->type === 'benefit' ? 'Nilai tinggi = lebih diprioritaskan.' : 'Nilai rendah = lebih diprioritaskan.' }}
                                    </div>
                                    <select id="value-{{ $c->id }}" name="values[{{ $c->id }}]" class="form-select" required>
                                        <option value="">Pilih nilai (1–5)</option>
                                        @for($score = 1; $score <= 5; $score++)
                                            <option value="{{ $score }}" {{ old('values.'.$c->id) == $score ? 'selected' : '' }}>
                                                {{ $score }} — {{ $scoreLabels[$score] }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="saw-info mt-3">
                        <strong>Petunjuk:</strong> beri nilai sesuai kondisi nyata fasilitas. Untuk kriteria
                        bertipe cost (misalnya biaya perbaikan), sistem membalik perbandingan saat normalisasi,
                        jadi tetap isi dengan skala yang sama (1 = sangat rendah, 5 = sangat tinggi).
                        Menyimpan ulang fasilitas yang sama akan menimpa nilai lamanya.
                    </div>

                    <button type="submit" class="btn btn-primary mt-3 saw-primary-block-btn">Simpan Penilaian Fasilitas</button>
                </form>
            @elseif(!$totalFacilities)
                <div class="saw-empty">
                    <strong>Belum ada fasilitas</strong>
                    <p class="small mb-0 mt-2">Tambahkan fasilitas di menu Fasilitas sebelum melakukan penilaian.</p>
                </div>
            @else
                <div class="saw-empty">
                    <strong>Belum ada kriteria</strong>
                    <p class="small mb-0 mt-2">Penilaian belum bisa dilakukan karena data kriteria belum tersedia.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- RANKING V --}}
    <section class="card saw-block">
        <div class="card-body">
            <h5 class="saw-title">Hasil Perankingan (V)</h5>
            <p class="saw-desc">Urutan prioritas dari nilai preferensi tertinggi ke terendah.</p>

            @if($rankedCount)
                <div class="saw-table-box">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Nama Fasilitas</th>
                                    <th>Nilai Akhir (V)</th>
                                    <th>Grafik</th>
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
                                        <td><span class="saw-rank {{ $rank === 1 ? 'first' : '' }}">{{ $rank }}</span></td>
                                        <td class="fw-bold" style="color:var(--navy-900)">{{ $facility?->name ?? 'Fasilitas tidak ditemukan' }}</td>
                                        <td class="saw-score">{{ number_format($v, 4) }}</td>
                                        <td style="min-width:140px">
                                            <div class="saw-bar mt-0"><div class="saw-bar-fill" style="width:{{ $percentage }}%"></div></div>
                                        </td>
                                        <td>
                                            @if($rank === 1)
                                                <span class="badge bg-success">Prioritas utama</span>
                                            @elseif($rank <= 3)
                                                <span class="badge bg-primary">Prioritas berikutnya</span>
                                            @else
                                                <span class="badge bg-secondary">Urutan {{ $rank }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php $rank++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="saw-empty">
                    <strong>Belum ada hasil perankingan</strong>
                    <p class="small mb-0 mt-2">Isi penilaian minimal satu fasilitas untuk mulai menghasilkan ranking.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- MATRIKS X DAN R --}}
    <section class="card saw-block" id="saw-matrix">
        <div class="card-body">
            <h5 class="saw-title">Matriks Perhitungan SAW</h5>
            <p class="saw-desc">Data asli (X) dan hasil normalisasinya (R) yang dipakai untuk menghitung ranking.</p>

            @if($rankedCount)
                @php
                    $tables = [
                        ['title' => 'Matriks Keputusan (X)', 'description' => 'Nilai awal tiap fasilitas pada setiap kriteria.', 'data' => $X, 'decimals' => 0],
                        ['title' => 'Matriks Normalisasi (R)', 'description' => 'Nilai setelah disesuaikan dengan rumus benefit atau cost.', 'data' => $R, 'decimals' => 4],
                    ];
                @endphp

                @foreach($tables as $table)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-1" style="color:var(--navy-900)">{{ $table['title'] }}</h6>
                        <p class="saw-hint mb-2">{{ $table['description'] }}</p>
                        <div class="saw-table-box">
                            <div class="table-responsive">
                                <table class="table align-middle">
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
                                                <td class="fw-bold" style="color:var(--navy-900)">{{ $f->name }}</td>
                                                @foreach($criteria as $c)
                                                    <td>{{ number_format($table['data'][$f->id][$c->id] ?? 0, $table['decimals']) }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="saw-empty">Matriks akan muncul setelah ada data penilaian fasilitas.</div>
            @endif
        </div>
    </section>

    {{-- PANDUAN --}}
    <section class="card saw-block" id="saw-guide">
        <div class="card-body">
            <h5 class="saw-title">Bagaimana SAW Bekerja?</h5>
            <p class="saw-desc">Ringkasan langkah perhitungan.</p>

            <div class="saw-info">
                <div class="saw-step">
                    <div class="saw-step-number">1</div>
                    <div><strong>Tentukan kriteria dan bobot.</strong><br>Seluruh bobot berjumlah 100%.</div>
                </div>
                <div class="saw-step">
                    <div class="saw-step-number">2</div>
                    <div><strong>Beri nilai fasilitas.</strong><br>Setiap fasilitas dinilai 1 sampai 5 untuk semua kriteria.</div>
                </div>
                <div class="saw-step">
                    <div class="saw-step-number">3</div>
                    <div><strong>Normalisasi.</strong><br>Nilai disesuaikan menurut jenis kriteria: benefit atau cost.</div>
                </div>
                <div class="saw-step">
                    <div class="saw-step-number">4</div>
                    <div><strong>Hitung nilai akhir.</strong><br>Nilai normalisasi dikali bobot, lalu semuanya dijumlahkan.</div>
                </div>
                <div class="saw-step">
                    <div class="saw-step-number">5</div>
                    <div><strong>Tentukan ranking.</strong><br>Nilai akhir tertinggi menempati ranking pertama.</div>
                </div>
            </div>

            <div class="text-center mt-3 py-2" style="background:#f6f9fe;border-radius:var(--radius-md)">
                <strong style="font-size:1.1rem;color:var(--blue-700)">Vᵢ = Σ (wⱼ × rᵢⱼ)</strong>
                <div class="saw-hint mt-1">V = nilai akhir, w = bobot kriteria, r = nilai normalisasi</div>
            </div>
        </div>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('sawWeightForm');
    if (!form) return;

    var fields = form.querySelectorAll('.saw-weight-field');
    var display = document.getElementById('sawWeightDisplay');
    var progress = document.getElementById('sawWeightProgress');
    var message = document.getElementById('sawWeightMessage');

    function total() {
        var t = 0;
        fields.forEach(function (f) { t += parseFloat(f.value) || 0; });
        return Math.round(t * 10000) / 10000;
    }

    function update() {
        var t = total();
        var ok = Math.abs(t - 1) < 0.0001;
        display.textContent = (t * 100).toFixed(2) + '%';
        progress.style.width = Math.min(100, Math.max(0, t * 100)) + '%';
        progress.classList.toggle('is-bad', !ok);
        message.innerHTML = ok
            ? '<span class="text-success">Total bobot sudah sesuai.</span>'
            : '<span class="text-danger">Total bobot harus tepat 100% sebelum disimpan (sekarang ' + (t * 100).toFixed(2) + '%).</span>';
    }

    fields.forEach(function (f) { f.addEventListener('input', update); });
    update();

    form.addEventListener('submit', function (event) {
        var empty = false;
        fields.forEach(function (f) { if (f.value === '') empty = true; });

        if (empty || Math.abs(total() - 1) > 0.0001) {
            event.preventDefault();
            message.innerHTML = '<span class="text-danger fw-bold">' +
                (empty ? 'Semua bobot wajib diisi.' : 'Total bobot harus tepat 100% (1.00). Periksa kembali angkanya.') +
                '</span>';
            message.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection

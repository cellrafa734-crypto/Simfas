@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    $isPetugas = $user->role === 'petugas';

    $hasBorrowingData = collect($monthlyBorrowings)->sum() > 0;
    $hasFacilityData = collect($facilityStatusValues)->sum() > 0;

    /* Ikon SVG (garis) - menggantikan simbol teks agar tampil rapi di semua perangkat */
    $icons = [
        'grid'     => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'check'    => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'tool'     => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/>',
        'clock'    => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'file'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'user'     => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'trend'    => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'bars'     => '<line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/>',
        'pie'      => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>',
        'activity' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'info'     => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
        'chevron'  => '<polyline points="9 18 15 12 9 6"/>',
        'building' => '<path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1M14 9h1M9 13h1M14 13h1M10 21v-4h4v4"/>',
    ];
    $svg = fn ($key, $class = '') => '<svg class="dash-svg ' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$key] ?? $icons['file']) . '</svg>';

    $statConfig = [
        'Total fasilitas'      => ['icon' => 'building', 'color' => 'blue'],
        'Tersedia'             => ['icon' => 'check',    'color' => 'green'],
        'Dalam pemeliharaan'   => ['icon' => 'tool',     'color' => 'orange'],
        'Menunggu persetujuan' => ['icon' => 'clock',    'color' => 'purple'],
        'Total pengajuan'      => ['icon' => 'file',     'color' => 'navy'],
        'Pengajuan saya'       => ['icon' => 'user',     'color' => 'blue'],
    ];

    /* Sorotan tahun ini: dihitung dari data yang sudah ada (tanpa query baru) */
    $monthlyArr   = collect($monthlyBorrowings)->values();
    $labelsArr    = collect($monthlyLabels)->values();
    $totalYear    = (int) $monthlyArr->sum();
    $activeMonths = $monthlyArr->filter(fn ($v) => $v > 0)->count();
    $peakIndex    = $totalYear > 0 ? $monthlyArr->search($monthlyArr->max()) : null;
    $peakLabel    = $peakIndex !== null ? ($labelsArr[$peakIndex] ?? '-') : '-';
    $peakValue    = $peakIndex !== null ? (int) $monthlyArr->max() : 0;
    $avgMonthly   = $activeMonths > 0 ? round($totalYear / $activeMonths, 1) : 0;

    $totalFac  = $stats['Total fasilitas'] ?? collect($facilityStatusValues)->sum();
    $available = $stats['Tersedia'] ?? null;
    $availPct  = ($available !== null && $totalFac > 0) ? (int) round($available / $totalFac * 100) : null;
@endphp

<style>
    .sim-dashboard {
        --primary: #368bd0;
        --primary-dark: #236da9;
        --navy: #183e60;
        --sky: #eaf5ff;
        --pale: #f4f9fd;
        --text: #435b70;
        --muted: #7e94a7;
        --line: #e4edf5;
        --gap: 20px;
        color: var(--navy);
        padding-bottom: 30px;
    }

    .sim-dashboard *, .sim-dashboard *::before, .sim-dashboard *::after { box-sizing: border-box; }

    .sim-dashboard .dash-svg { width: 1em; height: 1em; flex: none; display: block; }

    /* ===== Latar gradasi biru muda ===== */
    .dash-stage {
        width: 100%;
        max-width: 100%;
        padding: 24px;
        border-radius: 24px;
        background: linear-gradient(135deg, #e6f3fd 0%, #cfe6f8 55%, #b9dbf4 100%);
    }

    .dash-content { display: grid; gap: var(--gap); }

    /* Kartu dasar */
    .dash-card {
        border: 1px solid var(--line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(24,62,96,.08);
    }

    /* ===== Hero ===== */
    .dash-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        padding: 30px 34px;
        border-radius: 18px;
        color: #fff;
        background: linear-gradient(115deg, #183e60 0%, #236da9 58%, #4f9fdc 100%);
        box-shadow: 0 14px 30px rgba(35,109,169,.22);
    }

    .dash-hero::before,
    .dash-hero::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 50%;
        pointer-events: none;
    }
    .dash-hero::before { width: 300px; height: 300px; right: 12%; top: -190px; }
    .dash-hero::after  { width: 240px; height: 240px; right: -70px; bottom: -150px; }

    .dash-hero-content { position: relative; z-index: 1; max-width: 620px; }

    .dash-hero h2 {
        margin: 0 0 8px;
        color: #fff !important;
        font-size: clamp(1.35rem, 2.6vw, 1.75rem);
        font-weight: 800;
        letter-spacing: -.4px;
        line-height: 1.25;
    }

    .dash-hero p {
        margin: 0;
        color: rgba(255,255,255,.85);
        font-size: .9rem;
        line-height: 1.7;
    }

    .dash-hero-art {
        position: relative;
        z-index: 1;
        display: grid;
        place-items: center;
        flex: none;
        width: 84px;
        height: 84px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 24px;
        background: rgba(255,255,255,.14);
        font-size: 2.4rem;
        animation: dashFloat 5s ease-in-out infinite;
    }

    /* ===== Judul bagian ===== */
    .dash-section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 12px;
        margin: 6px 2px -4px;
    }
    .dash-section-title h5 { margin: 0; color: var(--navy); font-size: 1.05rem; font-weight: 800; }
    .dash-section-title p  { margin: 4px 0 0; color: #5d7387; font-size: .8rem; }

    .dash-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 12px;
        border-radius: 30px;
        background: rgba(255,255,255,.75);
        color: var(--primary-dark);
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .dash-label i { width: 7px; height: 7px; border-radius: 50%; background: var(--primary); animation: dashPulse 2s ease-in-out infinite; }

    /* ===== Kartu statistik: rata & selalu penuh satu baris ===== */
    .dash-stats { display: flex; flex-wrap: wrap; gap: 16px; }

    .dash-stat {
        position: relative;
        flex: 1 1 200px;
        min-width: 0;
        overflow: hidden;
        padding: 20px 20px 22px;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .dash-stat:hover { transform: translateY(-3px); box-shadow: 0 16px 30px rgba(54,139,208,.16); }

    .dash-stat-icon {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        font-size: 1.3rem;
    }
    .dash-stat-icon.blue   { color: #2f84cb; background: #eaf5ff; }
    .dash-stat-icon.green  { color: #15966a; background: #e7f7ef; }
    .dash-stat-icon.orange { color: #d1851f; background: #fff3e2; }
    .dash-stat-icon.purple { color: #7a5fc0; background: #f1ecff; }
    .dash-stat-icon.navy   { color: #183e60; background: #e9f0f6; }

    .dash-stat-number {
        margin-top: 18px;
        color: var(--navy);
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
    }
    .dash-stat-name { margin-top: 8px; color: #637b8f; font-size: .85rem; font-weight: 600; }

    .dash-stat-bar {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        height: 4px;
        background: var(--primary);
        transform-origin: left;
        animation: dashGrow 1s .3s cubic-bezier(.2,.8,.2,1) both;
    }
    .dash-stat-bar.green  { background: #15966a; }
    .dash-stat-bar.orange { background: #e6a13c; }
    .dash-stat-bar.purple { background: #8166c5; }
    .dash-stat-bar.navy   { background: #183e60; }

    /* ===== Sorotan tahun ini: satu kartu, empat bagian ===== */
    .dash-strip { display: grid; grid-template-columns: repeat(4, 1fr); }

    .dash-strip-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px;
        min-width: 0;
    }
    .dash-strip-item + .dash-strip-item { border-left: 1px solid var(--line); }

    .dash-strip-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: var(--sky);
        color: var(--primary);
        font-size: 1.2rem;
    }
    .dash-strip-item strong { display: block; color: var(--navy); font-size: 1.2rem; font-weight: 800; line-height: 1.2; }
    .dash-strip-item span.cap { display: block; margin-top: 2px; color: var(--muted); font-size: .78rem; font-weight: 600; }

    .dash-ring { position: relative; flex: none; width: 48px; height: 48px; }
    .dash-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .dash-ring circle { fill: none; stroke-width: 3.6; }
    .dash-ring .track { stroke: var(--sky); }
    .dash-ring .value {
        stroke: var(--primary);
        stroke-linecap: round;
        stroke-dasharray: 100;
        stroke-dashoffset: var(--offset);
        animation: dashRing 1.3s .35s cubic-bezier(.2,.8,.2,1) both;
    }
    .dash-ring b { position: absolute; inset: 0; display: grid; place-items: center; color: var(--primary-dark); font-size: .72rem; font-weight: 800; }

    /* ===== Grafik: dua kolom sejajar ===== */
    .dash-charts { display: grid; grid-template-columns: minmax(0, 7fr) minmax(0, 5fr); gap: var(--gap); }

    .dash-panel { padding: 22px 24px; }

    .dash-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
    .dash-panel-head h6 { margin: 0; color: var(--navy); font-size: .98rem; font-weight: 800; }
    .dash-panel-head p  { margin: 5px 0 0; color: var(--muted); font-size: .8rem; }

    .dash-panel-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: var(--sky);
        color: var(--primary);
        font-size: 1.1rem;
    }

    .dash-chart { position: relative; width: 100%; height: 270px; margin-top: 18px; }

    .dash-empty-chart,
    .dash-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--muted);
    }
    .dash-empty-chart {
        height: 270px;
        margin-top: 18px;
        padding: 20px;
        border: 1px dashed #cfe0ee;
        border-radius: 14px;
        background: #f8fbfe;
    }
    .dash-empty-chart .dash-svg, .dash-empty .dash-svg { font-size: 2rem; color: #a9c2d6; margin-bottom: 10px; }
    .dash-empty-chart strong, .dash-empty strong { color: var(--text); font-size: .9rem; margin-bottom: 4px; }
    .dash-empty-chart small, .dash-empty small { font-size: .8rem; }
    .dash-empty { padding: 44px 15px; }

    /* ===== Tabel ===== */
    .dash-table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 14px; }
    .dash-table { width: 100%; min-width: 560px; margin: 0; border-collapse: separate; border-spacing: 0; }

    .dash-table th {
        padding: 13px 16px;
        background: #f3f8fc;
        color: #62798c;
        font-size: .78rem;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
        border-bottom: 1px solid var(--line);
    }
    .dash-table td {
        padding: 14px 16px;
        color: #556e82;
        font-size: .85rem;
        border-bottom: 1px solid #edf1f5;
        vertical-align: middle;
    }
    .dash-table tbody tr { transition: background .2s ease; }
    .dash-table tbody tr:last-child td { border-bottom: 0; }
    .dash-table tbody tr:hover { background: #eef7fe; }

    .dash-person { display: flex; align-items: center; gap: 10px; color: var(--navy); font-weight: 700; }
    .dash-avatar {
        display: grid;
        place-items: center;
        flex: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d9ecfb, #b7d9f3);
        color: var(--primary-dark);
        font-size: .75rem;
        font-weight: 800;
    }

    .dash-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 11px;
        border-radius: 30px;
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .dash-status::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .dash-status.menunggu  { color: #a16a13; background: #fff5df; }
    .dash-status.disetujui { color: #19815a; background: #eaf8f1; }
    .dash-status.ditolak   { color: #c34c4c; background: #fff0ef; }
    .dash-status.dipinjam  { color: #2679b5; background: #eaf5ff; }
    .dash-status.selesai   { color: #65788a; background: #eff3f6; }
    .dash-status.default   { color: #65788a; background: #eff3f6; }
    .dash-status.menunggu::before, .dash-status.dipinjam::before { animation: dashPulse 1.8s ease-in-out infinite; }

    .dash-view-all {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--primary-dark);
        font-size: .82rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }
    .dash-view-all:hover { color: var(--navy); }
    .dash-view-all .dash-svg { font-size: 1rem; transition: transform .2s ease; }
    .dash-view-all:hover .dash-svg { transform: translateX(3px); }

    .dash-petugas-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 16px 20px;
        font-size: .85rem;
        color: #426b89;
    }
    .dash-petugas-note > span { display: inline-flex; align-items: center; gap: 10px; }
    .dash-petugas-note > span .dash-svg { font-size: 1.2rem; color: var(--primary); }
    .dash-petugas-note a {
        padding: 9px 16px;
        border-radius: 10px;
        background: var(--primary);
        color: #fff;
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease;
    }
    .dash-petugas-note a:hover { background: var(--primary-dark); color: #fff; }

    /* ===== Animasi masuk ===== */
    .dash-reveal { animation: dashUp .6s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 70ms); }

    @keyframes dashUp    { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
    @keyframes dashGrow  { from { transform: scaleX(0); } to { transform: scaleX(1); } }
    @keyframes dashRing  { from { stroke-dashoffset: 100; } }
    @keyframes dashFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    @keyframes dashPulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }

    @media (prefers-reduced-motion: reduce) {
        .sim-dashboard *, .sim-dashboard *::before, .sim-dashboard *::after { animation: none !important; transition: none !important; }
    }

    /* ===== Responsif ===== */
    @media (max-width: 1199.98px) {
        .dash-strip { grid-template-columns: repeat(2, 1fr); }
        .dash-strip-item:nth-child(3) { border-left: 0; }
        .dash-strip-item:nth-child(n+3) { border-top: 1px solid var(--line); }
    }

    @media (max-width: 991.98px) {
        .dash-charts { grid-template-columns: minmax(0, 1fr); }
    }

    @media (max-width: 767.98px) {
        .dash-stage { padding: 14px; border-radius: 18px; }
        .dash-hero { padding: 24px 20px; }
        .dash-hero-art { width: 64px; height: 64px; font-size: 1.9rem; border-radius: 18px; }
        .dash-stat { flex-basis: calc(50% - 8px); padding: 16px 16px 20px; }
        .dash-stat-number { font-size: 1.7rem; }
        .dash-panel { padding: 18px; }
        .dash-chart, .dash-empty-chart { height: 235px; }
    }

    @media (max-width: 575.98px) {
        .dash-hero-art { display: none; }
        .dash-strip { grid-template-columns: minmax(0, 1fr); }
        .dash-strip-item + .dash-strip-item { border-left: 0; border-top: 1px solid var(--line); }
        .dash-section-title { align-items: flex-start; }
    }
</style>

<div class="sim-dashboard">
  <div class="dash-stage">
    <div class="dash-content">

        {{-- Judul halaman & tanggal sudah ada di header layout, jadi tidak diulang di sini --}}
        <section class="dash-hero dash-reveal" style="--i:0">
            <div class="dash-hero-content">
                <h2>Selamat datang, {{ $user->name }}!</h2>
                <p>
                    @if($isAdmin)
                        Pantau dan kelola fasilitas kampus, pengajuan peminjaman,
                        serta aktivitas sistem melalui satu dashboard.
                    @elseif($isPetugas)
                        Pantau informasi fasilitas kampus dan aktivitas peminjaman
                        melalui dashboard SIMFAS.
                    @else
                        Temukan fasilitas kampus dan pantau pengajuan peminjamanmu
                        dengan lebih mudah melalui SIMFAS.
                    @endif
                </p>
            </div>
            <div class="dash-hero-art" aria-hidden="true">{!! $svg('building') !!}</div>
        </section>

        <div class="dash-section-title">
            <div>
                <h5>Ringkasan Statistik</h5>
                <p>Data fasilitas dan peminjaman yang tercatat dalam sistem.</p>
            </div>
            <span class="dash-label"><i></i> Data sistem</span>
        </div>

        <div class="dash-stats">
            @foreach($stats as $label => $value)
                @php
                    $config = $statConfig[$label] ?? ['icon' => 'file', 'color' => 'blue'];
                @endphp
                <article class="dash-card dash-stat dash-reveal" style="--i:{{ $loop->index + 1 }}">
                    <div class="dash-stat-icon {{ $config['color'] }}">{!! $svg($config['icon']) !!}</div>
                    <div class="dash-stat-number" data-count="{{ (int) $value }}">{{ number_format($value) }}</div>
                    <div class="dash-stat-name">{{ $label }}</div>
                    <div class="dash-stat-bar {{ $config['color'] }}"></div>
                </article>
            @endforeach
        </div>

        <section class="dash-card dash-strip dash-reveal" style="--i:6">
            <div class="dash-strip-item">
                <div class="dash-strip-icon">{!! $svg('trend') !!}</div>
                <div>
                    <strong data-count="{{ $totalYear }}">{{ number_format($totalYear) }}</strong>
                    <span class="cap">Pengajuan tahun {{ now()->year }}</span>
                </div>
            </div>
            <div class="dash-strip-item">
                <div class="dash-strip-icon">{!! $svg('calendar') !!}</div>
                <div>
                    <strong>{{ $peakLabel }}</strong>
                    <span class="cap">Bulan teramai ({{ $peakValue }} pengajuan)</span>
                </div>
            </div>
            <div class="dash-strip-item">
                <div class="dash-strip-icon">{!! $svg('bars') !!}</div>
                <div>
                    <strong>{{ $avgMonthly }}</strong>
                    <span class="cap">Rata-rata per bulan aktif</span>
                </div>
            </div>
            <div class="dash-strip-item">
                @if($availPct !== null)
                    <div class="dash-ring" style="--offset: {{ 100 - $availPct }};">
                        <svg viewBox="0 0 36 36" aria-hidden="true">
                            <circle class="track" cx="18" cy="18" r="15.915" pathLength="100"></circle>
                            <circle class="value" cx="18" cy="18" r="15.915" pathLength="100"></circle>
                        </svg>
                        <b>{{ $availPct }}%</b>
                    </div>
                    <div>
                        <strong>Ketersediaan</strong>
                        <span class="cap">Fasilitas siap dipakai</span>
                    </div>
                @else
                    <div class="dash-strip-icon">{!! $svg('building') !!}</div>
                    <div>
                        <strong data-count="{{ (int) $totalFac }}">{{ number_format($totalFac) }}</strong>
                        <span class="cap">Total fasilitas tercatat</span>
                    </div>
                @endif
            </div>
        </section>

        <div class="dash-section-title">
            <div>
                <h5>Analisis Aktivitas</h5>
                <p>Visualisasi tren peminjaman dan kondisi fasilitas kampus.</p>
            </div>
        </div>

        <div class="dash-charts">
            <section class="dash-card dash-panel dash-reveal" style="--i:7">
                <div class="dash-panel-head">
                    <div>
                        <h6>Tren Peminjaman</h6>
                        <p>Jumlah pengajuan per bulan pada tahun {{ now()->year }}.</p>
                    </div>
                    <span class="dash-panel-icon">{!! $svg('activity') !!}</span>
                </div>

                @if($hasBorrowingData)
                    <div class="dash-chart">
                        <canvas id="borrowingChart"></canvas>
                    </div>
                @else
                    <div class="dash-empty-chart">
                        {!! $svg('activity') !!}
                        <strong>Belum ada aktivitas peminjaman</strong>
                        <small>Grafik akan menampilkan data setelah terdapat pengajuan.</small>
                    </div>
                @endif
            </section>

            <section class="dash-card dash-panel dash-reveal" style="--i:8">
                <div class="dash-panel-head">
                    <div>
                        <h6>Distribusi Fasilitas</h6>
                        <p>Komposisi berdasarkan status fasilitas.</p>
                    </div>
                    <span class="dash-panel-icon">{!! $svg('pie') !!}</span>
                </div>

                @if($hasFacilityData)
                    <div class="dash-chart">
                        <canvas id="facilityChart"></canvas>
                    </div>
                @else
                    <div class="dash-empty-chart">
                        {!! $svg('pie') !!}
                        <strong>Belum ada data fasilitas</strong>
                        <small>Distribusi akan muncul setelah data fasilitas tersedia.</small>
                    </div>
                @endif
            </section>
        </div>

        <section class="dash-card dash-panel dash-reveal" style="--i:9">
            <div class="dash-panel-head" style="margin-bottom:18px;">
                <div>
                    <h6>Aktivitas Peminjaman Terbaru</h6>
                    <p>
                        @if($isAdmin)
                            Pengajuan terbaru dari seluruh pengguna.
                        @else
                            Riwayat pengajuan peminjaman milikmu.
                        @endif
                    </p>
                </div>

                @unless($isPetugas)
                    <a href="{{ route('borrowings.index') }}" class="dash-view-all">
                        Lihat semua {!! $svg('chevron') !!}
                    </a>
                @endunless
            </div>

            @if($recentBorrowings->count())
                <div class="dash-table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                @if($isAdmin)
                                    <th>Pengguna</th>
                                @endif
                                <th>Fasilitas</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrowings as $index => $borrowing)
                                @php
                                    $status = strtolower((string) $borrowing->status);

                                    $statusClass = match($status) {
                                        'menunggu' => 'menunggu',
                                        'disetujui' => 'disetujui',
                                        'ditolak' => 'ditolak',
                                        'dipinjam', 'sedang_digunakan' => 'dipinjam',
                                        'selesai' => 'selesai',
                                        default => 'default',
                                    };

                                    $statusLabel = [
                                        'menunggu' => 'Menunggu',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        'dipinjam' => 'Sedang Dipinjam',
                                        'sedang_digunakan' => 'Sedang Digunakan',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Dibatalkan',
                                    ][$status] ?? ucfirst(str_replace('_', ' ', $status));

                                    $personName = $borrowing->user->name ?? 'Pengguna';
                                @endphp

                                <tr>
                                    <td style="color:#9aabba;font-weight:700;">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </td>

                                    @if($isAdmin)
                                        <td>
                                            <div class="dash-person">
                                                <span class="dash-avatar">
                                                    {{ mb_strtoupper(mb_substr($personName, 0, 1)) }}
                                                </span>
                                                {{ $personName }}
                                            </div>
                                        </td>
                                    @endif

                                    <td>
                                        <strong style="color:#31546f;">
                                            {{ $borrowing->facility->name ?? $borrowing->facility->nama ?? 'Fasilitas' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $borrowing->created_at?->format('d M Y') ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="dash-status {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="dash-empty">
                    {!! $svg('file') !!}
                    <strong>Belum ada aktivitas peminjaman</strong>
                    <small>Data pengajuan akan ditampilkan di bagian ini.</small>
                </div>
            @endif
        </section>

        @if($isPetugas)
            <div class="dash-card dash-petugas-note dash-reveal" style="--i:10">
                <span>{!! $svg('info') !!} Anda dapat melihat daftar dan kondisi fasilitas melalui menu Fasilitas.</span>
                <a href="{{ route('facilities.index') }}">Buka Daftar Fasilitas</a>
            </div>
        @endif

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---- Angka naik (count-up) ---- */
    if (!reduceMotion) {
        document.querySelectorAll('[data-count]').forEach(function (el) {
            const target = parseInt(el.dataset.count, 10) || 0;
            if (target === 0) return;
            const start = performance.now();
            const duration = 1200;
            (function tick(now) {
                const p = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(target * eased).toLocaleString('en-US');
                if (p < 1) requestAnimationFrame(tick);
            })(start);
        });
    }

    /* ---- Grafik ---- */
    const borrowingCanvas = document.getElementById('borrowingChart');
    const facilityCanvas = document.getElementById('facilityChart');
    const chartFont = { family: 'Inter, sans-serif' };

    if (borrowingCanvas) {
        const ctx = borrowingCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 270);
        gradient.addColorStop(0, 'rgba(54,139,208,.30)');
        gradient.addColorStop(1, 'rgba(54,139,208,0)');

        new Chart(borrowingCanvas, {
            type: 'line',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: @json($monthlyBorrowings),
                    borderColor: '#368bd0',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: .42,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#368bd0',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: reduceMotion ? 0 : 1400, easing: 'easeOutQuart' },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#183e60', padding: 12, cornerRadius: 9 }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: chartFont, color: '#8ba0b3' },
                        grid: { color: '#edf2f7' }
                    },
                    x: {
                        ticks: { font: chartFont, color: '#8ba0b3' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    if (facilityCanvas) {
        const facilityValues = @json($facilityStatusValues);
        const facilityTotal = facilityValues.reduce(function (a, b) { return a + Number(b); }, 0);

        /* Total di tengah donat */
        const centerText = {
            id: 'centerText',
            afterDraw: function (chart) {
                const meta = chart.getDatasetMeta(0).data[0];
                if (!meta) return;
                const c = chart.ctx;
                c.save();
                c.textAlign = 'center';
                c.textBaseline = 'middle';
                c.fillStyle = '#183e60';
                c.font = '800 26px Inter, sans-serif';
                c.fillText(facilityTotal.toLocaleString('en-US'), meta.x, meta.y - 8);
                c.fillStyle = '#7e94a7';
                c.font = '600 12px Inter, sans-serif';
                c.fillText('Fasilitas', meta.x, meta.y + 14);
                c.restore();
            }
        };

        new Chart(facilityCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($facilityStatusLabels),
                datasets: [{
                    data: facilityValues,
                    backgroundColor: ['#368bd0', '#78b5e8', '#f2b65d', '#e77b7b'],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                animation: { animateRotate: true, duration: reduceMotion ? 0 : 1400 },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            font: { family: 'Inter, sans-serif', size: 12 },
                            color: '#637c90'
                        }
                    },
                    tooltip: { backgroundColor: '#183e60', padding: 12, cornerRadius: 9 }
                }
            },
            plugins: [centerText]
        });
    }
});
</script>

@endsection

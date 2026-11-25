@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    $isPetugas = $user->role === 'petugas';

    $hasBorrowingData = collect($monthlyBorrowings)->sum() > 0;
    $hasFacilityData = collect($facilityStatusValues)->sum() > 0;

    $statConfig = [
        'Total fasilitas' => ['icon' => '▦', 'color' => 'blue'],
        'Tersedia' => ['icon' => '✓', 'color' => 'green'],
        'Dalam pemeliharaan' => ['icon' => '⚙', 'color' => 'orange'],
        'Menunggu persetujuan' => ['icon' => '◷', 'color' => 'purple'],
        'Total pengajuan' => ['icon' => '▤', 'color' => 'navy'],
        'Pengajuan saya' => ['icon' => '▣', 'color' => 'blue'],
    ];

    /* ---- Insight turunan (dihitung dari data yang sudah ada, tanpa query baru) ---- */
    $monthlyArr = collect($monthlyBorrowings)->values();
    $labelsArr  = collect($monthlyLabels)->values();
    $totalYear  = (int) $monthlyArr->sum();
    $activeMonths = $monthlyArr->filter(fn ($v) => $v > 0)->count();
    $peakIndex  = $totalYear > 0 ? $monthlyArr->search($monthlyArr->max()) : null;
    $peakLabel  = $peakIndex !== null ? ($labelsArr[$peakIndex] ?? '-') : '-';
    $peakValue  = $peakIndex !== null ? (int) $monthlyArr->max() : 0;
    $avgMonthly = $activeMonths > 0 ? round($totalYear / $activeMonths, 1) : 0;

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
        --muted: #8ba0b3;
        --line: #e4edf5;
        color: var(--navy);
        padding-bottom: 35px;
    }

    .sim-dashboard * { box-sizing: border-box; }

    /* ===== Panggung (latar gradasi biru muda seperti referensi) ===== */
    .dash-stage {
        position: relative;
        overflow: hidden;
        padding: 26px;
        border-radius: 28px;
        background:
            radial-gradient(circle at 12% 8%, rgba(255,255,255,.9) 0, rgba(255,255,255,0) 38%),
            linear-gradient(135deg, #d9ecfb 0%, #bcdcf5 48%, #9fcdf0 100%);
    }

    .dash-stage::before,
    .dash-stage::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(255,255,255,.55);
        border-radius: 50%;
        pointer-events: none;
    }
    .dash-stage::before { width: 520px; height: 520px; left: -190px; bottom: -330px; }
    .dash-stage::after  { width: 380px; height: 380px; right: -120px; top: -230px; }

    /* ===== Cangkang putih besar ===== */
    .dash-shell {
        position: relative;
        z-index: 1;
        display: flex;
        gap: 22px;
        padding: 22px;
        border-radius: 24px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 25px 60px rgba(24,62,96,.16);
    }

    /* ===== Rel ikon mini (navigasi cepat di dalam dashboard) ===== */
    .dash-rail {
        position: sticky;
        top: 20px;
        align-self: flex-start;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 12px 9px;
        border-radius: 18px;
        background: linear-gradient(180deg, #368bd0, #236da9);
        box-shadow: 0 10px 22px rgba(35,109,169,.28);
    }

    .dash-rail a {
        position: relative;
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        color: rgba(255,255,255,.75);
        font-size: 1.1rem;
        text-decoration: none;
        transition: background .2s ease, color .2s ease, transform .2s ease;
    }

    .dash-rail a:hover { background: rgba(255,255,255,.16); color: #fff; }
    .dash-rail a.active { background: #fff; color: var(--primary-dark); transform: scale(1.06); }

    .dash-rail a::after {
        content: attr(data-tip);
        position: absolute;
        left: calc(100% + 12px);
        top: 50%;
        transform: translateY(-50%) translateX(-4px);
        padding: 6px 10px;
        border-radius: 8px;
        background: var(--navy);
        color: #fff;
        font-size: .7rem;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
        z-index: 5;
    }
    .dash-rail a:hover::after { opacity: 1; transform: translateY(-50%) translateX(0); }

    .dash-main { flex: 1; min-width: 0; }

    /* ===== Heading ===== */
    .dash-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .dash-heading h1 {
        margin: 0;
        color: var(--navy);
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .dash-heading p { margin: 7px 0 0; color: var(--muted); font-size: .86rem; }

    .dash-date {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 11px 15px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: var(--pale);
        color: var(--text);
        font-size: .78rem;
        font-weight: 700;
    }

    /* ===== Hero ===== */
    .dash-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 170px;
        padding: 28px 32px;
        margin-bottom: 22px;
        border-radius: 20px;
        color: white;
        background: linear-gradient(115deg, #183e60 0%, #236da9 55%, #55a5e2 100%);
        box-shadow: 0 14px 30px rgba(35,109,169,.2);
    }

    .dash-hero::before,
    .dash-hero::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 50%;
        pointer-events: none;
    }
    .dash-hero::before { width: 300px; height: 300px; right: 9%; top: -205px; }
    .dash-hero::after  { width: 230px; height: 230px; right: -65px; bottom: -165px; }

    .dash-hero-content { position: relative; z-index: 2; max-width: 650px; }

    .dash-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        margin-bottom: 14px;
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 30px;
        background: rgba(255,255,255,.12);
        color: #eaf5ff;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: 1.2px;
    }

    .dash-hero h2 {
        margin: 0 0 10px;
        color: white !important;
        font-size: clamp(1.4rem, 3vw, 1.9rem);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .dash-hero p {
        max-width: 520px;
        margin: 0;
        color: rgba(255,255,255,.82);
        font-size: .86rem;
        line-height: 1.8;
    }

    .dash-hero-art {
        position: relative;
        z-index: 1;
        display: grid;
        place-items: center;
        width: 105px;
        height: 105px;
        margin-right: 24px;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 28px;
        background: rgba(255,255,255,.12);
        color: white;
        font-size: 3.6rem;
        transform: rotate(-7deg);
        animation: dashFloat 5s ease-in-out infinite;
    }

    /* ===== Judul seksi ===== */
    .dash-section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 12px;
        margin: 28px 0 14px;
        scroll-margin-top: 20px;
    }

    .dash-section-title h5 { margin: 0; color: var(--navy); font-size: 1.02rem; font-weight: 800; }
    .dash-section-title p { margin: 5px 0 0; color: var(--muted); font-size: .76rem; }

    .dash-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 30px;
        background: var(--sky);
        color: var(--primary-dark);
        font-size: .7rem;
        font-weight: 800;
    }

    .dash-label::first-letter { animation: dashPulse 2s ease-in-out infinite; }

    /* ===== Kartu statistik ===== */
    .dash-stat {
        position: relative;
        height: 100%;
        min-height: 142px;
        overflow: hidden;
        padding: 19px 20px;
        border: 1px solid var(--line);
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, var(--pale) 100%);
        box-shadow: 0 6px 20px rgba(24,62,96,.06);
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }

    .dash-stat:hover {
        transform: translateY(-4px);
        border-color: #cfe3f4;
        box-shadow: 0 16px 30px rgba(54,139,208,.15);
    }

    .dash-stat-top { display: flex; justify-content: space-between; align-items: center; }

    .dash-stat-icon {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 1.2rem;
        font-weight: 800;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.7), 0 4px 10px rgba(24,62,96,.08);
    }

    .dash-stat-icon.blue   { color: #368bd0; background: #eaf5ff; }
    .dash-stat-icon.green  { color: #15966a; background: #eaf8f1; }
    .dash-stat-icon.orange { color: #d58a25; background: #fff5e8; }
    .dash-stat-icon.purple { color: #8166c5; background: #f3efff; }
    .dash-stat-icon.navy   { color: #183e60; background: #edf3f8; }

    .dash-stat-mark { color: #c8d9e7; font-size: 1.1rem; transition: transform .25s ease, color .25s ease; }
    .dash-stat:hover .dash-stat-mark { transform: translate(3px,-3px); color: var(--primary); }

    .dash-stat-number {
        margin-top: 16px;
        color: var(--navy);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
    }

    .dash-stat-name { margin-top: 9px; color: #8095a8; font-size: .77rem; font-weight: 600; }

    .dash-stat-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary);
        transform-origin: left;
        animation: dashGrow 1s .3s cubic-bezier(.2,.8,.2,1) both;
    }
    .dash-stat-bar.green  { background: #15966a; }
    .dash-stat-bar.orange { background: #e6a13c; }
    .dash-stat-bar.purple { background: #8166c5; }
    .dash-stat-bar.navy   { background: #183e60; }

    /* ===== Kartu insight (tambahan) ===== */
    .dash-insight {
        display: flex;
        align-items: center;
        gap: 14px;
        height: 100%;
        padding: 16px 18px;
        border: 1px solid #d6e8f6;
        border-radius: 18px;
        background: linear-gradient(135deg, #f0f8fe 0%, #e3f1fc 100%);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .dash-insight:hover { transform: translateY(-3px); box-shadow: 0 12px 24px rgba(54,139,208,.14); }

    .dash-insight-icon {
        display: grid;
        place-items: center;
        flex: none;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #fff;
        color: var(--primary);
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(54,139,208,.14);
    }

    .dash-insight strong { display: block; color: var(--navy); font-size: 1.15rem; font-weight: 800; line-height: 1.15; }
    .dash-insight span { color: var(--muted); font-size: .72rem; font-weight: 600; }

    .dash-ring { position: relative; flex: none; width: 56px; height: 56px; }
    .dash-ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .dash-ring circle { fill: none; stroke-width: 6; }
    .dash-ring .track { stroke: #fff; }
    .dash-ring .value {
        stroke: var(--primary);
        stroke-linecap: round;
        stroke-dasharray: 100;
        stroke-dashoffset: var(--offset);
        animation: dashRing 1.3s .35s cubic-bezier(.2,.8,.2,1) both;
    }
    .dash-ring b {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        color: var(--primary-dark);
        font-size: .68rem;
        font-weight: 800;
    }

    /* ===== Panel grafik ===== */
    .dash-panel {
        height: 100%;
        padding: 22px;
        border: 1px solid var(--line);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 6px 22px rgba(24,62,96,.06);
    }

    .dash-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
    .dash-panel-head h6 { margin: 0; color: var(--navy); font-size: .92rem; font-weight: 800; }
    .dash-panel-head p { margin: 6px 0 0; color: var(--muted); font-size: .73rem; }

    .dash-panel-icon {
        display: grid;
        place-items: center;
        width: 37px;
        height: 37px;
        border-radius: 12px;
        background: var(--sky);
        color: var(--primary);
        font-size: 1.1rem;
    }

    .dash-chart { position: relative; width: 100%; height: 265px; margin-top: 20px; }

    .dash-empty-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 265px;
        margin-top: 20px;
        padding: 20px;
        border: 1px dashed #cfe0ee;
        border-radius: 16px;
        background: #f8fbfe;
        text-align: center;
        color: var(--muted);
    }
    .dash-empty-chart strong { margin: 10px 0 5px; color: var(--text); font-size: .85rem; }

    /* ===== Tabel aktivitas ===== */
    .dash-activity { margin-top: 22px; scroll-margin-top: 20px; }

    .dash-table-wrap { overflow-x: auto; border: 1px solid var(--line); border-radius: 14px; }

    .dash-table { width: 100%; min-width: 560px; margin: 0; border-collapse: separate; border-spacing: 0; }

    .dash-table th {
        padding: 14px 15px;
        background: #f3f8fc;
        color: #7b91a4;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .3px;
        white-space: nowrap;
        border-bottom: 1px solid var(--line);
    }

    .dash-table td {
        padding: 15px;
        color: #637c90;
        font-size: .77rem;
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
        width: 33px;
        height: 33px;
        flex: none;
        border-radius: 50%;
        background: linear-gradient(135deg, #d9ecfb, #b7d9f3);
        color: var(--primary-dark);
        font-size: .73rem;
        font-weight: 800;
    }

    .dash-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 30px;
        font-size: .67rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .dash-status::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .dash-status.menunggu  { color: #a16a13; background: #fff5df; }
    .dash-status.disetujui { color: #19815a; background: #eaf8f1; }
    .dash-status.ditolak   { color: #c34c4c; background: #fff0ef; }
    .dash-status.dipinjam  { color: #2679b5; background: #eaf5ff; }
    .dash-status.selesai   { color: #65788a; background: #eff3f6; }
    .dash-status.default   { color: #65788a; background: #eff3f6; }

    .dash-status.menunggu::before,
    .dash-status.dipinjam::before { animation: dashPulse 1.8s ease-in-out infinite; }

    .dash-view-all {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--primary-dark);
        font-size: .76rem;
        font-weight: 800;
        text-decoration: none;
    }
    .dash-view-all:hover { color: var(--navy); }

    .dash-empty { padding: 45px 15px; text-align: center; color: var(--muted); }
    .dash-empty .empty-icon { display: block; margin-bottom: 10px; color: #b5c9d9; font-size: 2rem; }
    .dash-empty strong { display: block; color: var(--text); font-size: .85rem; }

    .dash-petugas-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 22px;
        padding: 17px 20px;
        border: 1px solid #d9eaf7;
        border-radius: 16px;
        background: #f0f8fe;
        color: #426b89;
        font-size: .8rem;
    }

    .dash-petugas-note a {
        padding: 8px 14px;
        border-radius: 10px;
        background: var(--primary);
        color: white;
        font-size: .73rem;
        font-weight: 700;
        text-decoration: none;
        transition: background .2s ease, transform .2s ease;
    }
    .dash-petugas-note a:hover { background: var(--primary-dark); color: white; transform: translateY(-1px); }

    /* ===== Animasi masuk (satu rangkaian saat halaman dibuka) ===== */
    .dash-reveal { animation: dashUp .6s cubic-bezier(.2,.8,.2,1) both; animation-delay: calc(var(--i, 0) * 70ms); }

    @keyframes dashUp    { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
    @keyframes dashGrow  { from { transform: scaleX(0); } to { transform: scaleX(1); } }
    @keyframes dashRing  { from { stroke-dashoffset: 100; } }
    @keyframes dashFloat { 0%,100% { transform: rotate(-7deg) translateY(0); } 50% { transform: rotate(-4deg) translateY(-8px); } }
    @keyframes dashPulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }

    @media (prefers-reduced-motion: reduce) {
        .sim-dashboard *, .sim-dashboard *::before, .sim-dashboard *::after {
            animation: none !important;
            transition: none !important;
        }
    }

    /* ===== Responsif ===== */
    @media (max-width: 991.98px) {
        .dash-stage { padding: 14px; border-radius: 22px; }
        .dash-shell { flex-direction: column; padding: 16px; gap: 16px; }
        .dash-rail {
            position: static;
            flex-direction: row;
            justify-content: center;
            align-self: stretch;
            padding: 8px;
        }
        .dash-rail a::after { display: none; }
    }

    @media (max-width: 767.98px) {
        .dash-hero { padding: 24px 20px; }
        .dash-hero-art { width: 75px; height: 75px; margin-right: 0; font-size: 2.6rem; }
        .dash-stat { min-height: 132px; padding: 16px; }
        .dash-stat-number { font-size: 1.7rem; }
        .dash-panel { padding: 18px; }
        .dash-chart, .dash-empty-chart { height: 235px; }
        .dash-date { font-size: .7rem; }
    }

    @media (max-width: 480px) {
        .dash-hero-art { display: none; }
        .dash-heading { align-items: flex-start; }
        .dash-stat-name { font-size: .7rem; }
        .dash-section-title { align-items: flex-start; }
        .dash-stage { padding: 10px; }
        .dash-shell { padding: 12px; }
    }
</style>

<div class="sim-dashboard">
  <div class="dash-stage">
    <div class="dash-shell">

        {{-- Rel ikon mini: loncat ke bagian dashboard (sidebar utama tidak diubah) --}}
        <nav class="dash-rail" aria-label="Navigasi cepat dashboard">
            <a href="#sec-stat" data-tip="Statistik" class="active">▦</a>
            <a href="#sec-chart" data-tip="Analisis">⌁</a>
            <a href="#sec-activity" data-tip="Aktivitas">▤</a>
        </nav>

        <div class="dash-main">

            <div class="dash-heading dash-reveal" style="--i:0">
                <div>
                    <h1>Dashboard</h1>
                    <p>Ringkasan informasi dan aktivitas SIMFAS Kampus.</p>
                </div>
                <div class="dash-date">
                    <span>▦</span>
                    {{ now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <section class="dash-hero dash-reveal" style="--i:1">
                <div class="dash-hero-content">
                    <div class="dash-hero-tag">✦ SIMFAS KAMPUS</div>
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
                <div class="dash-hero-art" aria-hidden="true">▦</div>
            </section>

            <div class="dash-section-title" id="sec-stat">
                <div>
                    <h5>Ringkasan Statistik</h5>
                    <p>Data fasilitas dan peminjaman yang tercatat dalam sistem.</p>
                </div>
                <span class="dash-label">● Data sistem</span>
            </div>

            <div class="row g-3">
                @foreach($stats as $label => $value)
                    @php
                        $config = $statConfig[$label] ?? ['icon' => '▤', 'color' => 'blue'];
                    @endphp
                    <div class="col-6 col-xl-4">
                        <article class="dash-stat dash-reveal" style="--i:{{ $loop->index + 2 }}">
                            <div class="dash-stat-top">
                                <div class="dash-stat-icon {{ $config['color'] }}">
                                    {{ $config['icon'] }}
                                </div>
                                <span class="dash-stat-mark">↗</span>
                            </div>
                            <div class="dash-stat-number" data-count="{{ (int) $value }}">{{ number_format($value) }}</div>
                            <div class="dash-stat-name">{{ $label }}</div>
                            <div class="dash-stat-bar {{ $config['color'] }}"></div>
                        </article>
                    </div>
                @endforeach
            </div>

            {{-- Insight tambahan (dihitung dari data yang sama) --}}
            <div class="row g-3 mt-0">
                <div class="col-6 col-xl-3">
                    <div class="dash-insight dash-reveal" style="--i:8">
                        <div class="dash-insight-icon">⌁</div>
                        <div>
                            <strong data-count="{{ $totalYear }}">{{ number_format($totalYear) }}</strong>
                            <span>Pengajuan tahun {{ now()->year }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="dash-insight dash-reveal" style="--i:9">
                        <div class="dash-insight-icon">★</div>
                        <div>
                            <strong>{{ $peakLabel }}</strong>
                            <span>Bulan teramai ({{ $peakValue }} pengajuan)</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="dash-insight dash-reveal" style="--i:10">
                        <div class="dash-insight-icon">∅</div>
                        <div>
                            <strong>{{ $avgMonthly }}</strong>
                            <span>Rata-rata per bulan aktif</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl-3">
                    <div class="dash-insight dash-reveal" style="--i:11">
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
                                <span>Fasilitas siap dipakai</span>
                            </div>
                        @else
                            <div class="dash-insight-icon">▦</div>
                            <div>
                                <strong data-count="{{ (int) $totalFac }}">{{ number_format($totalFac) }}</strong>
                                <span>Total fasilitas tercatat</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="dash-section-title" id="sec-chart">
                <div>
                    <h5>Analisis Aktivitas</h5>
                    <p>Visualisasi tren peminjaman dan kondisi fasilitas kampus.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-xl-7">
                    <section class="dash-panel dash-reveal" style="--i:12">
                        <div class="dash-panel-head">
                            <div>
                                <h6>Tren Peminjaman</h6>
                                <p>Jumlah pengajuan per bulan pada tahun {{ now()->year }}.</p>
                            </div>
                            <span class="dash-panel-icon">⌁</span>
                        </div>

                        @if($hasBorrowingData)
                            <div class="dash-chart">
                                <canvas id="borrowingChart"></canvas>
                            </div>
                        @else
                            <div class="dash-empty-chart">
                                <span style="font-size:2rem;">⌁</span>
                                <strong>Belum ada aktivitas peminjaman</strong>
                                <small>Grafik akan menampilkan data setelah terdapat pengajuan.</small>
                            </div>
                        @endif
                    </section>
                </div>

                <div class="col-12 col-xl-5">
                    <section class="dash-panel dash-reveal" style="--i:13">
                        <div class="dash-panel-head">
                            <div>
                                <h6>Distribusi Fasilitas</h6>
                                <p>Komposisi berdasarkan status fasilitas.</p>
                            </div>
                            <span class="dash-panel-icon">◉</span>
                        </div>

                        @if($hasFacilityData)
                            <div class="dash-chart">
                                <canvas id="facilityChart"></canvas>
                            </div>
                        @else
                            <div class="dash-empty-chart">
                                <span style="font-size:2rem;">◉</span>
                                <strong>Belum ada data fasilitas</strong>
                                <small>Distribusi akan muncul setelah data fasilitas tersedia.</small>
                            </div>
                        @endif
                    </section>
                </div>
            </div>

            <section class="dash-panel dash-activity dash-reveal" id="sec-activity" style="--i:14">
                <div class="dash-panel-head" style="margin-bottom:20px;">
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
                            Lihat semua →
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
                        <span class="empty-icon">▤</span>
                        <strong>Belum ada aktivitas peminjaman</strong>
                        <small>Data pengajuan akan ditampilkan di bagian ini.</small>
                    </div>
                @endif
            </section>

            @if($isPetugas)
                <div class="dash-petugas-note dash-reveal" style="--i:15">
                    <span>ⓘ Anda dapat melihat daftar dan kondisi fasilitas melalui menu Fasilitas.</span>
                    <a href="{{ route('facilities.index') }}">Buka Daftar Fasilitas →</a>
                </div>
            @endif

        </div>
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

    /* ---- Rel ikon: tandai bagian yang sedang dilihat ---- */
    const railLinks = document.querySelectorAll('.dash-rail a');
    railLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            const target = document.querySelector(link.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
        });
    });

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                railLinks.forEach(function (l) {
                    l.classList.toggle('active', l.getAttribute('href') === '#' + entry.target.id);
                });
            });
        }, { rootMargin: '-20% 0px -65% 0px' });

        ['sec-stat', 'sec-chart', 'sec-activity'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) observer.observe(el);
        });
    }

    /* ---- Grafik ---- */
    const borrowingCanvas = document.getElementById('borrowingChart');
    const facilityCanvas = document.getElementById('facilityChart');

    const chartFont = { family: 'Inter, sans-serif' };

    if (borrowingCanvas) {
        const ctx = borrowingCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 265);
        gradient.addColorStop(0, 'rgba(54,139,208,.32)');
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

        /* Teks total di tengah donat */
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
                c.fillStyle = '#8ba0b3';
                c.font = '600 11px Inter, sans-serif';
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
                            font: { family: 'Inter, sans-serif', size: 11 },
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

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    $isPetugas = $user->role === 'petugas';

    // Ikon SVG inline (layout tidak memuat Bootstrap Icons, jadi tidak perlu library baru)
    $iconPaths = [
        'building'  => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4M10 10h4M10 14h4M10 18h4"/>',
        'check'     => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        'tools'     => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'clock'     => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'clipboard' => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/>',
        'file'      => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4M10 9H8M16 13H8M16 17H8"/>',
        'arrow-up'  => '<path d="M7 17 17 7M7 7h10v10"/>',
        'grid'      => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
        'activity'  => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
        'arrow-right' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'inbox'     => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        'info'      => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
        'bars'      => '<path d="M12 20V10M18 20V4M6 20v-4"/>',
        'pie'       => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>',
    ];
    $icon = fn (string $name) => new \Illuminate\Support\HtmlString(
        '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . ($iconPaths[$name] ?? $iconPaths['bars']) . '</svg>'
    );

    // Apakah grafik punya data? (bila semua nol, tampilkan keterangan kosong)
    $hasBorrowingData = collect($monthlyBorrowings)->sum() > 0;
    $hasFacilityData  = collect($facilityStatusValues)->sum() > 0;

    $statStyles = [
        'Total fasilitas' => ['icon' => 'building', 'color' => 'blue'],
        'Tersedia' => ['icon' => 'check', 'color' => 'green'],
        'Dalam pemeliharaan' => ['icon' => 'tools', 'color' => 'orange'],
        'Menunggu persetujuan' => ['icon' => 'clock', 'color' => 'purple'],
        'Total pengajuan' => ['icon' => 'clipboard', 'color' => 'navy'],
        'Pengajuan saya' => ['icon' => 'file', 'color' => 'blue'],
    ];
@endphp

<style>
    .dashboard-page {
        --dash-primary: #368bd0;
        --dash-navy: #183e60;
        --dash-muted: #8295a8;
        --dash-line: #e8eef4;
        --dash-bg: #f5f9fc;
        color: var(--dash-navy);
        padding-bottom: 24px;
    }

    .dashboard-page .ico {
        width: 1.1em;
        height: 1.1em;
        flex: none;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
        vertical-align: middle;
    }

    /* HERO */
    .dash-hero {
        position: relative;
        overflow: hidden;
        padding: 34px 36px;
        margin-bottom: 30px;
        border-radius: 22px;
        color: #fff;
        background: linear-gradient(115deg, #183e60 0%, #236da9 58%, #368bd0 100%);
        box-shadow: 0 14px 35px rgba(35, 109, 169, .16);
    }

    .dash-hero::before,
    .dash-hero::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(255,255,255,.13);
        border-radius: 50%;
        pointer-events: none;
    }

    .dash-hero::before {
        width: 310px;
        height: 310px;
        right: 8%;
        top: -205px;
    }

    .dash-hero::after {
        width: 250px;
        height: 250px;
        right: -75px;
        bottom: -175px;
    }

    .dash-hero-content {
        position: relative;
        z-index: 2;
        max-width: 680px;
    }

    .dash-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        margin-bottom: 17px;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 30px;
        background: rgba(255,255,255,.1);
        color: #eaf5ff;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .11em;
    }

    .dash-hero h2 {
        margin: 0 0 10px;
        color: #fff !important;
        font-size: clamp(1.5rem, 3vw, 2.15rem);
        font-weight: 800;
        letter-spacing: -.045em;
    }

    .dash-hero p {
        max-width: 560px;
        margin: 0;
        color: rgba(255,255,255,.78);
        font-size: .88rem;
        line-height: 1.8;
    }

    .dash-hero-art {
        position: absolute;
        right: 7%;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,.13);
        font-size: 7rem;
    }

    /* SECTION HEADING */
    .dash-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin: 30px 0 16px;
    }

    .dash-section-heading h5 {
        margin: 0;
        color: var(--dash-navy);
        font-size: 1.02rem;
        font-weight: 800;
        letter-spacing: -.025em;
    }

    .dash-section-heading p {
        margin: 5px 0 0;
        color: var(--dash-muted);
        font-size: .77rem;
    }

    .dash-period {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border: 1px solid var(--dash-line);
        border-radius: 30px;
        background: #fff;
        color: #647d91;
        font-size: .7rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* STAT CARDS */
    .dash-stat-card {
        position: relative;
        height: 100%;
        min-height: 158px;
        overflow: hidden;
        padding: 21px;
        border: 1px solid var(--dash-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 3px 14px rgba(24,62,96,.035);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .dash-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 13px 28px rgba(24,62,96,.09);
    }

    .dash-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dash-stat-icon {
        display: grid;
        place-items: center;
        width: 45px;
        height: 45px;
        border-radius: 14px;
        font-size: 1.25rem;
    }

    .dash-stat-icon.blue { color: #368bd0; background: #eaf5ff; }
    .dash-stat-icon.green { color: #15966a; background: #eaf8f1; }
    .dash-stat-icon.orange { color: #d58a25; background: #fff5e8; }
    .dash-stat-icon.purple { color: #8166c5; background: #f3efff; }
    .dash-stat-icon.navy { color: #183e60; background: #edf3f8; }

    .dash-stat-decoration {
        color: #dce6ee;
        font-size: 1rem;
    }

    .dash-stat-value {
        margin-top: 20px;
        color: var(--dash-navy);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.055em;
    }

    .dash-stat-label {
        margin-top: 9px;
        color: #8092a4;
        font-size: .77rem;
        font-weight: 600;
    }

    .dash-stat-line {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        height: 3px;
        background: #368bd0;
    }

    .dash-stat-line.green { background: #15966a; }
    .dash-stat-line.orange { background: #e6a13c; }
    .dash-stat-line.purple { background: #8166c5; }
    .dash-stat-line.navy { background: #183e60; }

    /* PANELS */
    .dash-panel {
        height: 100%;
        padding: 23px;
        border: 1px solid var(--dash-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 3px 14px rgba(24,62,96,.035);
    }

    .dash-panel-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .dash-panel-title {
        margin: 0;
        color: var(--dash-navy);
        font-size: .92rem;
        font-weight: 800;
    }

    .dash-panel-subtitle {
        margin: 6px 0 0;
        color: var(--dash-muted);
        font-size: .74rem;
    }

    .dash-panel-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border-radius: 11px;
        background: #edf6fd;
        color: var(--dash-primary);
    }

    .dash-chart {
        position: relative;
        width: 100%;
        height: 270px;
        margin-top: 22px;
    }

    .dash-chart-empty {
        display: flex;
        height: 270px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 9px;
        margin-top: 22px;
        padding: 20px;
        border: 1px dashed #d9e5ee;
        border-radius: 14px;
        background: #fafcfe;
        text-align: center;
        color: #8ca0b1;
    }

    .dash-chart-empty .ico {
        font-size: 2rem;
        color: #b5c9d9;
    }

    .dash-chart-empty strong {
        color: #617d92;
        font-size: .83rem;
    }

    .dash-chart-empty small {
        font-size: .72rem;
    }

    /* ACTIVITY */
    .dash-activity {
        margin-top: 30px;
    }

    .dash-activity-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
    }

    .dash-view-all {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--dash-primary);
        font-size: .77rem;
        font-weight: 800;
        text-decoration: none;
        transition: gap .2s ease;
    }

    .dash-view-all:hover {
        gap: 11px;
        color: var(--dash-navy);
    }

    .dash-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--dash-line);
        border-radius: 13px;
    }

    .dash-table {
        width: 100%;
        min-width: 560px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .dash-table th {
        padding: 14px 15px;
        background: #f6f9fc;
        color: #8295a8;
        font-size: .67rem;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
        white-space: nowrap;
        border-bottom: 1px solid var(--dash-line);
    }

    .dash-table td {
        padding: 15px;
        color: #637c90;
        font-size: .77rem;
        border-bottom: 1px solid #edf1f5;
        vertical-align: middle;
    }

    .dash-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .dash-table tbody tr {
        transition: background .2s ease;
    }

    .dash-table tbody tr:hover {
        background: #f8fbfe;
    }

    .dash-number {
        color: #a0afbd;
        font-weight: 700;
    }

    .dash-person {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--dash-navy);
        font-weight: 700;
    }

    .dash-person-avatar {
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        flex: none;
        border-radius: 10px;
        background: #eaf5ff;
        color: #368bd0;
        font-size: .72rem;
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

    .dash-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .dash-status.menunggu { color: #a16a13; background: #fff5df; }
    .dash-status.disetujui { color: #19815a; background: #eaf8f1; }
    .dash-status.ditolak { color: #c34c4c; background: #fff0ef; }
    .dash-status.dipinjam { color: #2679b5; background: #eaf5ff; }
    .dash-status.selesai { color: #65788a; background: #eff3f6; }
    .dash-status.default { color: #65788a; background: #eff3f6; }

    .dash-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 45px 15px;
        text-align: center;
        color: #91a3b3;
    }

    .dash-empty .ico {
        margin-bottom: 12px;
        color: #b6c9d8;
        font-size: 2.2rem;
    }

    .dash-empty strong {
        color: #607c91;
        font-size: .85rem;
    }

    .dash-empty small {
        margin-top: 5px;
        font-size: .73rem;
    }

    /* PETUGAS */
    .dash-petugas-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 22px;
        padding: 17px 20px;
        border: 1px solid #d9eaf7;
        border-radius: 15px;
        background: #f0f8fe;
        color: #426b89;
    }

    .dash-petugas-note .note-text {
        display: flex;
        align-items: center;
        gap: 11px;
        font-size: .8rem;
    }

    .dash-petugas-note .note-text .ico {
        color: #368bd0;
        font-size: 1.2rem;
    }

    .dash-petugas-note a {
        padding: 8px 14px;
        border-radius: 9px;
        background: #368bd0;
        color: #fff;
        font-size: .73rem;
        font-weight: 700;
        text-decoration: none;
    }

    .dash-petugas-note a:hover {
        background: #236da9;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .dash-hero { padding: 27px 23px; }
        .dash-hero-art { right: 12px; font-size: 4.5rem; opacity: .6; }
        .dash-stat-card { min-height: 145px; padding: 17px; }
        .dash-stat-value { font-size: 1.7rem; }
        .dash-panel { padding: 18px; }
        .dash-chart, .dash-chart-empty { height: 240px; }
        .dash-section-heading { align-items: flex-start; }
    }

    @media (max-width: 420px) {
        .dash-period { font-size: .63rem; }
        .dash-hero h2 { font-size: 1.45rem; }
        .dash-stat-label { font-size: .7rem; }
    }
</style>

<div class="dashboard-page">

    {{-- HERO --}}
    <section class="dash-hero">
        <div class="dash-hero-content">
            <div class="dash-eyebrow">
                {!! $icon('grid') !!}
                SIMFAS KAMPUS
            </div>

            <h2>Selamat datang, {{ $user->name }}!</h2>

            <p>
                @if($isAdmin)
                    Kelola dan pantau fasilitas kampus, pengajuan peminjaman,
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

        <div class="dash-hero-art" aria-hidden="true">
            {!! $icon('building') !!}
        </div>
    </section>

    {{-- STATISTIK --}}
    <div class="dash-section-heading">
        <div>
            <h5>Ringkasan Statistik</h5>
            <p>Ikhtisar data fasilitas dan peminjaman</p>
        </div>
        <span class="dash-period">
            {!! $icon('activity') !!}
            Data sistem
        </span>
    </div>

    <div class="row g-3">
        @foreach($stats as $label => $value)
            @php
                $style = $statStyles[$label] ?? ['icon' => 'bars', 'color' => 'blue'];
            @endphp

            <div class="col-6 col-xl-4">
                <article class="dash-stat-card">
                    <div class="dash-stat-top">
                        <div class="dash-stat-icon {{ $style['color'] }}">
                            {!! $icon($style['icon']) !!}
                        </div>
                        <span class="dash-stat-decoration">
                            {!! $icon('arrow-up') !!}
                        </span>
                    </div>

                    <div class="dash-stat-value">{{ number_format($value) }}</div>
                    <div class="dash-stat-label">{{ $label }}</div>
                    <div class="dash-stat-line {{ $style['color'] }}"></div>
                </article>
            </div>
        @endforeach
    </div>

    {{-- GRAFIK --}}
    <div class="dash-section-heading">
        <div>
            <h5>Analisis Aktivitas</h5>
            <p>Visualisasi peminjaman dan kondisi fasilitas kampus</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <section class="dash-panel">
                <div class="dash-panel-heading">
                    <div>
                        <h6 class="dash-panel-title">Tren Peminjaman</h6>
                        <p class="dash-panel-subtitle">
                            Jumlah pengajuan berdasarkan bulan tahun {{ now()->year }}
                        </p>
                    </div>
                    <span class="dash-panel-icon">{!! $icon('activity') !!}</span>
                </div>

                @if($hasBorrowingData)
                    <div class="dash-chart">
                        <canvas id="borrowingChart"></canvas>
                    </div>
                @else
                    <div class="dash-chart-empty">
                        {!! $icon('bars') !!}
                        <strong>Belum ada aktivitas peminjaman</strong>
                        <small>Grafik akan menampilkan data setelah terdapat pengajuan.</small>
                    </div>
                @endif
            </section>
        </div>

        <div class="col-12 col-xl-5">
            <section class="dash-panel">
                <div class="dash-panel-heading">
                    <div>
                        <h6 class="dash-panel-title">Distribusi Fasilitas</h6>
                        <p class="dash-panel-subtitle">Berdasarkan status fasilitas</p>
                    </div>
                    <span class="dash-panel-icon">{!! $icon('pie') !!}</span>
                </div>

                @if($hasFacilityData)
                    <div class="dash-chart">
                        <canvas id="facilityChart"></canvas>
                    </div>
                @else
                    <div class="dash-chart-empty">
                        {!! $icon('pie') !!}
                        <strong>Belum ada data fasilitas</strong>
                        <small>Distribusi akan muncul setelah fasilitas tersedia.</small>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- AKTIVITAS TERBARU --}}
    <section class="dash-panel dash-activity">
        <div class="dash-activity-header">
            <div>
                <h6 class="dash-panel-title">Aktivitas Peminjaman Terbaru</h6>
                <p class="dash-panel-subtitle">
                    @if($isAdmin)
                        Pengajuan terbaru dari seluruh pengguna
                    @else
                        Riwayat pengajuan peminjaman milikmu
                    @endif
                </p>
            </div>

            @unless($isPetugas)
                <a href="{{ route('borrowings.index') }}" class="dash-view-all">
                    Lihat semua {!! $icon('arrow-right') !!}
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
                                $personInitial = mb_strtoupper(mb_substr($personName, 0, 1));
                            @endphp

                            <tr>
                                <td><span class="dash-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span></td>

                                @if($isAdmin)
                                    <td>
                                        <div class="dash-person">
                                            <span class="dash-person-avatar">{{ $personInitial }}</span>
                                            {{ $personName }}
                                        </div>
                                    </td>
                                @endif

                                <td>
                                    <strong style="color:#31546f;font-weight:700;">
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
                {!! $icon('inbox') !!}
                <strong>Belum ada aktivitas peminjaman</strong>
                <small>Data pengajuan akan ditampilkan di bagian ini.</small>
            </div>
        @endif
    </section>

    @if($isPetugas)
        <div class="dash-petugas-note">
            <div class="note-text">
                {!! $icon('info') !!}
                <span>Anda dapat melihat daftar dan kondisi fasilitas melalui menu Fasilitas.</span>
            </div>
            <a href="{{ route('facilities.index') }}">Buka Daftar Fasilitas</a>
        </div>
    @endif

</div>

{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const borrowingCanvas = document.getElementById('borrowingChart');
    const facilityCanvas = document.getElementById('facilityChart');

    const chartFont = {
        family: 'Inter, sans-serif'
    };

    if (borrowingCanvas) {
        new Chart(borrowingCanvas, {
            type: 'line',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: @json($monthlyBorrowings),
                    borderColor: '#368bd0',
                    backgroundColor: 'rgba(54, 139, 208, 0.12)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#368bd0',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: chartFont },
                        grid: { color: '#edf1f7' }
                    },
                    x: {
                        ticks: { font: chartFont },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    if (facilityCanvas) {
        new Chart(facilityCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($facilityStatusLabels),
                datasets: [{
                    data: @json($facilityStatusValues),
                    backgroundColor: [
                        '#368bd0',
                        '#78b5e8',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 17,
                            font: {
                                family: 'Inter, sans-serif',
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection

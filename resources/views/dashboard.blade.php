
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

    .sim-dashboard .dash-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 24px;
    }

    .dash-heading h1 {
        margin: 0;
        color: var(--navy);
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .dash-heading p {
        margin: 7px 0 0;
        color: var(--muted);
        font-size: .86rem;
    }

    .dash-date {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 11px 15px;
        border: 1px solid var(--line);
        border-radius: 12px;
        background: white;
        color: var(--text);
        font-size: .78rem;
        font-weight: 700;
    }

    .dash-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 205px;
        padding: 32px 36px;
        margin-bottom: 28px;
        border-radius: 22px;
        color: white;
        background: linear-gradient(115deg, #183e60 0%, #236da9 55%, #55a5e2 100%);
        box-shadow: 0 15px 35px rgba(35,109,169,.16);
    }

    .dash-hero::before,
    .dash-hero::after {
        content: "";
        position: absolute;
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 50%;
        pointer-events: none;
    }

    .dash-hero::before {
        width: 300px;
        height: 300px;
        right: 9%;
        top: -205px;
    }

    .dash-hero::after {
        width: 230px;
        height: 230px;
        right: -65px;
        bottom: -165px;
    }

    .dash-hero-content {
        position: relative;
        z-index: 2;
        max-width: 650px;
    }

    .dash-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        margin-bottom: 17px;
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
        font-size: clamp(1.45rem, 3vw, 2rem);
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .dash-hero p {
        max-width: 520px;
        margin: 0;
        color: rgba(255,255,255,.8);
        font-size: .86rem;
        line-height: 1.8;
    }

    .dash-hero-art {
        position: relative;
        z-index: 1;
        display: grid;
        place-items: center;
        width: 115px;
        height: 115px;
        margin-right: 30px;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 30px;
        background: rgba(255,255,255,.12);
        color: white;
        font-size: 4rem;
        transform: rotate(-7deg);
    }

    .dash-section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 12px;
        margin: 30px 0 16px;
    }

    .dash-section-title h5 {
        margin: 0;
        color: var(--navy);
        font-size: 1.02rem;
        font-weight: 800;
    }

    .dash-section-title p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: .76rem;
    }

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

    .dash-stat {
        position: relative;
        height: 100%;
        min-height: 155px;
        overflow: hidden;
        padding: 21px;
        border: 1px solid var(--line);
        border-radius: 17px;
        background: white;
        box-shadow: 0 4px 18px rgba(24,62,96,.035);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .dash-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 13px 28px rgba(24,62,96,.09);
    }

    .dash-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dash-stat-icon {
        display: grid;
        place-items: center;
        width: 43px;
        height: 43px;
        border-radius: 13px;
        font-size: 1.25rem;
        font-weight: 800;
    }

    .dash-stat-icon.blue { color: #368bd0; background: #eaf5ff; }
    .dash-stat-icon.green { color: #15966a; background: #eaf8f1; }
    .dash-stat-icon.orange { color: #d58a25; background: #fff5e8; }
    .dash-stat-icon.purple { color: #8166c5; background: #f3efff; }
    .dash-stat-icon.navy { color: #183e60; background: #edf3f8; }

    .dash-stat-mark {
        color: #c8d9e7;
        font-size: 1.1rem;
    }

    .dash-stat-number {
        margin-top: 20px;
        color: var(--navy);
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
    }

    .dash-stat-name {
        margin-top: 9px;
        color: #8095a8;
        font-size: .77rem;
        font-weight: 600;
    }

    .dash-stat-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary);
    }

    .dash-stat-bar.green { background: #15966a; }
    .dash-stat-bar.orange { background: #e6a13c; }
    .dash-stat-bar.purple { background: #8166c5; }
    .dash-stat-bar.navy { background: #183e60; }

    .dash-panel {
        height: 100%;
        padding: 23px;
        border: 1px solid var(--line);
        border-radius: 18px;
        background: white;
        box-shadow: 0 4px 18px rgba(24,62,96,.035);
    }

    .dash-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .dash-panel-head h6 {
        margin: 0;
        color: var(--navy);
        font-size: .92rem;
        font-weight: 800;
    }

    .dash-panel-head p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: .73rem;
    }

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

    .dash-chart {
        position: relative;
        width: 100%;
        height: 265px;
        margin-top: 20px;
    }

    .dash-empty-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 265px;
        margin-top: 20px;
        padding: 20px;
        border: 1px dashed #d9e5ee;
        border-radius: 14px;
        background: #fafcfe;
        text-align: center;
        color: var(--muted);
    }

    .dash-empty-chart strong {
        margin: 10px 0 5px;
        color: var(--text);
        font-size: .85rem;
    }

    .dash-activity {
        margin-top: 28px;
    }

    .dash-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--line);
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
        background: #f3f8fc;
        color: #8196a9;
        font-size: .67rem;
        font-weight: 800;
        letter-spacing: .7px;
        text-transform: uppercase;
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

    .dash-table tbody tr:last-child td { border-bottom: 0; }
    .dash-table tbody tr:hover { background: #f8fbfe; }

    .dash-person {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--navy);
        font-weight: 700;
    }

    .dash-avatar {
        display: grid;
        place-items: center;
        width: 33px;
        height: 33px;
        flex: none;
        border-radius: 11px;
        background: var(--sky);
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

    .dash-empty {
        padding: 45px 15px;
        text-align: center;
        color: var(--muted);
    }

    .dash-empty .empty-icon {
        display: block;
        margin-bottom: 10px;
        color: #b5c9d9;
        font-size: 2rem;
    }

    .dash-empty strong {
        display: block;
        color: var(--text);
        font-size: .85rem;
    }

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
        font-size: .8rem;
    }

    .dash-petugas-note a {
        padding: 8px 14px;
        border-radius: 9px;
        background: var(--primary);
        color: white;
        font-size: .73rem;
        font-weight: 700;
        text-decoration: none;
    }

    .dash-petugas-note a:hover {
        background: var(--primary-dark);
        color: white;
    }

    @media (max-width: 767.98px) {
        .dash-hero { padding: 26px 22px; }
        .dash-hero-art { width: 75px; height: 75px; margin-right: 0; font-size: 2.6rem; }
        .dash-stat { min-height: 140px; padding: 17px; }
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
    }
</style>

<div class="sim-dashboard">

    <div class="dash-heading">
        <div>
            <h1>Dashboard</h1>
            <p>Ringkasan informasi dan aktivitas SIMFAS Kampus.</p>
        </div>
        <div class="dash-date">
            <span>▦</span>
            {{ now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <section class="dash-hero">
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

    <div class="dash-section-title">
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
                <article class="dash-stat">
                    <div class="dash-stat-top">
                        <div class="dash-stat-icon {{ $config['color'] }}">
                            {{ $config['icon'] }}
                        </div>
                        <span class="dash-stat-mark">↗</span>
                    </div>
                    <div class="dash-stat-number">{{ number_format($value) }}</div>
                    <div class="dash-stat-name">{{ $label }}</div>
                    <div class="dash-stat-bar {{ $config['color'] }}"></div>
                </article>
            </div>
        @endforeach
    </div>

    <div class="dash-section-title">
        <div>
            <h5>Analisis Aktivitas</h5>
            <p>Visualisasi tren peminjaman dan kondisi fasilitas kampus.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <section class="dash-panel">
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
            <section class="dash-panel">
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

    <section class="dash-panel dash-activity">
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
        <div class="dash-petugas-note">
            <span>ⓘ Anda dapat melihat daftar dan kondisi fasilitas melalui menu Fasilitas.</span>
            <a href="{{ route('facilities.index') }}">Buka Daftar Fasilitas →</a>
        </div>
    @endif

</div>

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
                    label: 'Jumlah Pengajuan',
                    data: @json($monthlyBorrowings),
                    borderColor: '#368bd0',
                    backgroundColor: 'rgba(54,139,208,.13)',
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
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#183e60',
                        padding: 12,
                        cornerRadius: 9
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: chartFont,
                            color: '#8ba0b3'
                        },
                        grid: { color: '#edf2f7' }
                    },
                    x: {
                        ticks: {
                            font: chartFont,
                            color: '#8ba0b3'
                        },
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
                        '#f2b65d',
                        '#e77b7b'
                    ],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            font: {
                                family: 'Inter, sans-serif',
                                size: 11
                            },
                            color: '#637c90'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#183e60',
                        padding: 12,
                        cornerRadius: 9
                    }
                }
            }
        });
    }
});
</script>

@endsection

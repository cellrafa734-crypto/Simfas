
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';

    $statStyles = [
        'Total fasilitas' => ['icon' => 'bi-building', 'color' => 'blue'],
        'Tersedia' => ['icon' => 'bi-check-circle', 'color' => 'green'],
        'Dalam pemeliharaan' => ['icon' => 'bi-tools', 'color' => 'orange'],
        'Menunggu persetujuan' => ['icon' => 'bi-clock-history', 'color' => 'purple'],
        'Total pengajuan' => ['icon' => 'bi-clipboard-data', 'color' => 'navy'],
        'Pengajuan saya' => ['icon' => 'bi-file-earmark-text', 'color' => 'blue'],
    ];
@endphp

<style>
    /* DASHBOARD SIMFAS */

    .dashboard-page {
        color: #10244a;
    }

    .welcome-panel {
        position: relative;
        overflow: hidden;
        padding: 30px 32px;
        margin-bottom: 25px;
        border-radius: 18px;
        color: white;
        background: linear-gradient(115deg, #0a1f44 0%, #123b79 65%, #2563eb 100%);
        box-shadow: 0 12px 28px rgba(10, 31, 68, .16);
    }

    .welcome-panel::before,
    .welcome-panel::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
        pointer-events: none;
    }

    .welcome-panel::before {
        width: 240px;
        height: 240px;
        right: 8%;
        top: -145px;
    }

    .welcome-panel::after {
        width: 180px;
        height: 180px;
        right: -35px;
        bottom: -115px;
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }

    .welcome-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 11px;
        margin-bottom: 13px;
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 30px;
        background: rgba(255,255,255,.10);
        color: #dbeafe;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .welcome-title {
        margin: 0 0 8px;
        color: #fff !important;
        font-size: clamp(1.35rem, 3vw, 1.9rem);
        font-weight: 800;
        letter-spacing: -.035em;
    }

    .welcome-description {
        max-width: 580px;
        margin: 0;
        color: rgba(255,255,255,.76);
        font-size: .9rem;
        line-height: 1.7;
    }

    .welcome-decoration {
        position: absolute;
        right: 45px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,.15);
        font-size: 6rem;
    }

    /* STATISTIK */

    .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 15px;
    }

    .section-heading h5 {
        margin: 0;
        color: #0a1f44;
        font-size: 1rem;
        font-weight: 800;
    }

    .section-caption {
        margin: 4px 0 0;
        color: #7b8ba5;
        font-size: .78rem;
    }

    .stat-card {
        position: relative;
        height: 100%;
        overflow: hidden;
        padding: 19px;
        border: 1px solid #e2e9f4;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(10,31,68,.035);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(10,31,68,.09);
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .stat-icon {
        display: grid;
        place-items: center;
        width: 43px;
        height: 43px;
        border-radius: 12px;
        font-size: 1.2rem;
    }

    .stat-icon.blue { color: #2563eb; background: #eff6ff; }
    .stat-icon.green { color: #15945b; background: #eafaf2; }
    .stat-icon.orange { color: #d97706; background: #fff7e8; }
    .stat-icon.purple { color: #7c3aed; background: #f4efff; }
    .stat-icon.navy { color: #0e2a5c; background: #eaf0fa; }

    .stat-value {
        margin-top: 17px;
        color: #0a1f44;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .stat-label {
        margin-top: 8px;
        color: #71829e;
        font-size: .8rem;
        font-weight: 600;
    }

    .stat-indicator {
        position: absolute;
        right: 0;
        bottom: 0;
        width: 65px;
        height: 4px;
        border-radius: 5px 0 0 0;
        background: #2563eb;
    }

    .stat-indicator.green { background: #16a36a; }
    .stat-indicator.orange { background: #f59e0b; }
    .stat-indicator.purple { background: #8b5cf6; }
    .stat-indicator.navy { background: #0e2a5c; }

    /* PANEL */

    .dashboard-panel {
        height: 100%;
        padding: 21px;
        border: 1px solid #e2e9f4;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 3px 12px rgba(10,31,68,.035);
    }

    .panel-title {
        margin: 0;
        color: #0a1f44;
        font-size: .95rem;
        font-weight: 800;
    }

    .panel-subtitle {
        margin-top: 5px;
        color: #8291a9;
        font-size: .76rem;
    }

    .chart-container {
        position: relative;
        width: 100%;
        height: 275px;
        margin-top: 20px;
    }

    .table-dashboard {
        margin-top: 18px;
    }

    .table-dashboard th {
        padding: 13px 12px !important;
        background: #f5f8fd !important;
        color: #71829e !important;
        font-size: .68rem !important;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .table-dashboard td {
        padding: 14px 12px !important;
        font-size: .8rem;
    }

    .table-dashboard tbody tr:hover {
        background: #f5f8ff;
    }

    .person-name {
        color: #18345e;
        font-weight: 700;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 30px;
        font-size: .68rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-menunggu { color: #a16207; background: #fef3c7; }
    .status-disetujui { color: #166534; background: #dcfce7; }
    .status-ditolak { color: #991b1b; background: #fee2e2; }
    .status-dipinjam { color: #1d4ed8; background: #dbeafe; }
    .status-selesai { color: #475569; background: #e2e8f0; }
    .status-default { color: #475569; background: #f1f5f9; }

    .empty-state {
        padding: 35px 15px;
        text-align: center;
        color: #8a9ab2;
    }

    .empty-state i {
        display: block;
        margin-bottom: 10px;
        font-size: 2rem;
        color: #b8c8df;
    }

    .dashboard-link {
        color: #2563eb;
        font-size: .78rem;
        font-weight: 700;
        text-decoration: none;
    }

    .dashboard-link:hover {
        color: #0a1f44;
    }

    @media (max-width: 575px) {
        .welcome-panel { padding: 24px 21px; }
        .welcome-decoration { right: 10px; font-size: 4rem; opacity: .5; }
        .stat-card { padding: 16px; }
        .stat-value { font-size: 1.7rem; }
        .dashboard-panel { padding: 16px; }
    }
</style>

<div class="dashboard-page">

    {{-- SAMBUTAN --}}
    <section class="welcome-panel">
        <div class="welcome-content">
            <div class="welcome-label">
                <i class="bi bi-grid-1x2-fill"></i>
                SIMFAS KAMPUS
            </div>

            <h2 class="welcome-title">
                Selamat datang, {{ $user->name }}!
            </h2>

            <p class="welcome-description">
                @if($isAdmin)
                    Selamat bekerja. Pantau pengelolaan fasilitas,
                    pengajuan peminjaman, dan aktivitas sistem kampus
                @else
                    Selamat datang di Sistem Informasi Manajemen Fasilitas Kampus.
                    Temukan fasilitas dan pantau pengajuan peminjamanmu dengan mudah.
                @endif
            </p>
        </div>

        <i class="bi bi-buildings welcome-decoration"></i>
    </section>

    {{-- STATISTIK --}}
    <div class="section-heading">
        <div>
            <h5>Ringkasan Statistik</h5>
            <p class="section-caption">Informasi terkini sistem fasilitas kampus</p>
        </div>
        <span class="badge rounded-pill text-bg-light border">
            <i class="bi bi-activity text-primary me-1"></i>
            Ringkasan
        </span>
    </div>

    <div class="row g-3 mb-4">
        @foreach($stats as $label => $value)
            @php
                $style = $statStyles[$label] ?? [
                    'icon' => 'bi-bar-chart',
                    'color' => 'blue'
                ];
            @endphp

            <div class="col-6 col-xl-4">
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon {{ $style['color'] }}">
                            <i class="bi {{ $style['icon'] }}"></i>
                        </div>
                        <i class="bi bi-arrow-up-right text-secondary opacity-50"></i>
                    </div>

                    <div class="stat-value">{{ number_format($value) }}</div>
                    <div class="stat-label">{{ $label }}</div>

                    <div class="stat-indicator {{ $style['color'] }}"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- GRAFIK --}}
    <div class="section-heading mt-4">
        <div>
            <h5>Analisis Aktivitas</h5>
            <p class="section-caption">Visualisasi data peminjaman dan fasilitas</p>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-12 col-xl-7">
            <div class="dashboard-panel">
                <h6 class="panel-title">Statistik Peminjaman</h6>
                <p class="panel-subtitle">Jumlah pengajuan berdasarkan bulan tahun ini</p>

                <div class="chart-container">
                    <canvas id="borrowingChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="dashboard-panel">
                <h6 class="panel-title">Status Fasilitas</h6>
                <p class="panel-subtitle">Distribusi kondisi fasilitas kampus</p>

                <div class="chart-container">
                    <canvas id="facilityChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- AKTIVITAS TERBARU --}}
    <div class="dashboard-panel mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="panel-title">Aktivitas Peminjaman Terbaru</h6>
                <p class="panel-subtitle">
                    @if($isAdmin)
                        Daftar pengajuan peminjaman terbaru dari pengguna
                    @else
                        Riwayat pengajuan peminjaman milikmu
                    @endif
                </p>
            </div>

            <a href="/borrowings" class="dashboard-link">
                Lihat semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        @if($recentBorrowings->count())
            <div class="table-responsive">
                <table class="table table-dashboard align-middle">
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
                                    'menunggu' => 'status-menunggu',
                                    'disetujui' => 'status-disetujui',
                                    'ditolak' => 'status-ditolak',
                                    'dipinjam' => 'status-dipinjam',
                                    'selesai' => 'status-selesai',
                                    default => 'status-default',
                                };
                            @endphp

                            <tr>
                                <td class="text-secondary">{{ $index + 1 }}</td>

                                @if($isAdmin)
                                    <td>
                                        <span class="person-name">
                                            {{ $borrowing->user->name ?? 'Pengguna' }}
                                        </span>
                                    </td>
                                @endif

                                <td>
                                    <span class="person-name">
                                        {{ $borrowing->facility->name ?? $borrowing->facility->nama ?? 'Fasilitas' }}
                                    </span>
                                </td>

                                <td class="text-secondary">
                                    {{ $borrowing->created_at?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        <i class="bi bi-circle-fill" style="font-size:6px"></i>
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <div class="fw-semibold">Belum ada aktivitas peminjaman</div>
                <small>Data pengajuan akan muncul di sini.</small>
            </div>
        @endif
    </div>

    @if($user->role === 'petugas')
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Modul tugas pemeliharaan petugas belum diimplementasikan pada versi ini.
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
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.10)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
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
                        '#2563eb',
                        '#60a5fa',
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


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
    /* DASHBOARD SIMFAS */

    .dashboard-page {
        color: #10244a;
    }

    .ico {
        width: 1em;
        height: 1em;
        flex: none;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
        vertical-align: -.125em;
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

    .empty-state .ico {
        display: block;
        margin: 0 auto 10px;
        font-size: 2rem;
        color: #b8c8df;
    }

    .chart-empty {
        height: 275px;
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px dashed #d3deee;
        border-radius: 12px;
        background: #fafcff;
        text-align: center;
        padding: 20px;
    }

    .petugas-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .petugas-note .note-text {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
        flex: 1 1 260px;
    }

    .petugas-note .note-text .ico { margin-top: 3px; font-size: 1.1rem; }

    .table-dashboard {
        min-width: 480px;
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
        .chart-container, .chart-empty { height: 240px; }
    }

    /* Tabel dashboard: lebih ringkas di layar kecil (menimpa aturan umum layout) */
    @media (max-width: 767.98px) {
        .dashboard-page .table-dashboard { min-width: 480px; }
        .dashboard-page .table-dashboard tbody td { min-width: 0; }
    }
</style>

<div class="dashboard-page">

    {{-- SAMBUTAN --}}
    <section class="welcome-panel">
        <div class="welcome-content">
            <div class="welcome-label">
                {!! $icon('grid') !!}
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

        <span class="welcome-decoration" aria-hidden="true">{!! $icon('building') !!}</span>
    </section>

    {{-- STATISTIK --}}
    <div class="section-heading">
        <div>
            <h5>Ringkasan Statistik</h5>
            <p class="section-caption">Informasi terkini sistem fasilitas kampus</p>
        </div>
        <span class="badge rounded-pill text-bg-light border">
            <span class="text-primary me-1">{!! $icon('activity') !!}</span>
            Ringkasan
        </span>
    </div>

    <div class="row g-3 mb-4">
        @foreach($stats as $label => $value)
            @php
                $style = $statStyles[$label] ?? [
                    'icon' => 'bars',
                    'color' => 'blue'
                ];
            @endphp

            <div class="col-6 col-xl-4">
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon {{ $style['color'] }}">
                            {!! $icon($style['icon']) !!}
                        </div>
                        <span class="text-secondary opacity-50">{!! $icon('arrow-up') !!}</span>
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

                @if($hasBorrowingData)
                    <div class="chart-container">
                        <canvas id="borrowingChart"></canvas>
                    </div>
                @else
                    <div class="chart-empty empty-state">
                        {!! $icon('bars') !!}
                        <div class="fw-semibold">Belum ada data peminjaman tahun ini</div>
                        <small>Grafik akan muncul setelah ada pengajuan peminjaman.</small>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="dashboard-panel">
                <h6 class="panel-title">Status Fasilitas</h6>
                <p class="panel-subtitle">Distribusi kondisi fasilitas kampus</p>

                @if($hasFacilityData)
                    <div class="chart-container">
                        <canvas id="facilityChart"></canvas>
                    </div>
                @else
                    <div class="chart-empty empty-state">
                        {!! $icon('pie') !!}
                        <div class="fw-semibold">Belum ada data fasilitas</div>
                        <small>Grafik akan muncul setelah fasilitas ditambahkan.</small>
                    </div>
                @endif
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

            @unless($isPetugas)
                <a href="/borrowings" class="dashboard-link">
                    Lihat semua <span class="ms-1">{!! $icon('arrow-right') !!}</span>
                </a>
            @endunless
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
                                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
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
                {!! $icon('inbox') !!}
                <div class="fw-semibold">Belum ada aktivitas peminjaman</div>
                <small>Data pengajuan akan muncul di sini.</small>
            </div>
        @endif
    </div>

    @if($isPetugas)
        <div class="alert alert-info petugas-note" role="status">
            <div class="note-text">
                {!! $icon('info') !!}
                <span>Anda dapat melihat daftar dan kondisi fasilitas pada menu Fasilitas.</span>
            </div>
            <a href="/facilities" class="btn btn-primary btn-sm">Buka Daftar Fasilitas</a>
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


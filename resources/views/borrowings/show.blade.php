
@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
@php
    $status = $borrowing->status;

    $statusLabels = [
        'menunggu' => 'Menunggu Persetujuan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    $statusColors = [
        'menunggu' => 'warning',
        'disetujui' => 'success',
        'ditolak' => 'danger',
        'dibatalkan' => 'secondary',
    ];

    $isAdmin = auth()->user()->role === 'admin';
    $isOwner = $borrowing->user_id === auth()->id();

    // Ikon SVG inline (tanpa emoji dan tanpa library tambahan)
    $svg = fn (string $d) => new \Illuminate\Support\HtmlString(
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>'
    );
    $icons = [
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'building' => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4M10 10h4M10 14h4M10 18h4"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'note' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4M10 13H8M16 17H8"/>',
        'back' => '<path d="m15 18-6-6 6-6"/>',
    ];

    $mins = $borrowing->start_at->diffInMinutes($borrowing->end_at);
    $duration = trim(
        (intdiv($mins, 1440) ? intdiv($mins, 1440) . ' hari ' : '') .
        (intdiv($mins % 1440, 60) ? intdiv($mins % 1440, 60) . ' jam ' : '') .
        ($mins % 60 ? ($mins % 60) . ' menit' : '')
    ) ?: '-';
    $fmtDate = fn ($d) => $d->locale('id')->translatedFormat('d F Y');
@endphp

<style>
    .detail-page {
        --blue: #368bd0;
        --navy: #183e60;
        --muted: #718096;
        color: #243b53;
        animation: detailEnter .65s ease both;
    }

    @keyframes detailEnter {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .detail-hero {
        position: relative;
        overflow: hidden;
        padding: 30px;
        border-radius: 22px;
        color: white;
        background: linear-gradient(120deg, #183e60, #236da9, #368bd0);
        box-shadow: 0 12px 30px rgba(35, 109, 169, .16);
    }

    .detail-hero::after {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        right: -65px;
        top: -100px;
        border: 35px solid rgba(255,255,255,.08);
        border-radius: 50%;
        pointer-events: none;
    }

    .detail-hero h3 {
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .detail-card {
        background: #fff;
        border: 1px solid #eaf0f6;
        border-radius: 19px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 5px 20px rgba(24,62,96,.045);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .detail-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .detail-value {
        color: var(--navy);
        font-weight: 600;
        font-size: 15px;
        overflow-wrap: anywhere;
    }

    /* Baris informasi: label di kiri (redup), nilai di kanan (tegas) */
    .info-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 4px 16px;
        align-items: baseline;
        padding: 12px 0;
        border-top: 1px solid #eef2f7;
    }

    .info-row:first-of-type { border-top: 0; padding-top: 0; }
    .info-row:last-child { padding-bottom: 0; }

    .detail-value.is-primary { font-size: 18px; font-weight: 800; letter-spacing: -.2px; }
    .detail-value.is-plain { color: #475569; font-weight: 500; }

    .detail-value .sub {
        display: block;
        margin-top: 2px;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    .chip {
        display: inline-block;
        padding: 3px 11px;
        border-radius: 8px;
        background: #eef3f9;
        color: #334e68;
        font-size: 13px;
        font-weight: 700;
    }

    .chip.mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .3px; }
    .chip.ok { background: #dcfce7; color: #166534; }
    .chip.bad { background: #fee2e2; color: #991b1b; }

    .purpose-box {
        padding: 14px 16px;
        border-left: 4px solid #368bd0;
        border-radius: 4px 12px 12px 4px;
        background: #f6f9fc;
        color: #334e68;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    .detail-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #eaf5ff;
        color: var(--blue);
        font-size: 20px;
        margin-bottom: 14px;
    }

    .detail-section-title {
        color: var(--navy);
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .detail-timeline {
        position: relative;
        padding-left: 34px;
    }

    .detail-timeline::before {
        content: "";
        position: absolute;
        left: 10px;
        top: 10px;
        bottom: 12px;
        width: 2px;
        background: #e4edf5;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 27px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: -34px;
        top: 2px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #eaf5ff;
        border: 5px solid #368bd0;
        z-index: 1;
    }

    .timeline-item.inactive .timeline-dot {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .timeline-item.active .timeline-dot {
        box-shadow: 0 0 0 5px rgba(54,139,208,.12);
        animation: pulseDot 1.8s infinite;
    }

    @keyframes pulseDot {
        50% { box-shadow: 0 0 0 9px rgba(54,139,208,0); }
    }

    .detail-btn {
        border-radius: 11px;
        padding: 10px 17px;
        font-weight: 700;
        transition: .2s ease;
    }

    .detail-btn:hover {
        transform: translateY(-2px);
    }

    .detail-back {
        color: white;
        text-decoration: none;
        font-weight: 600;
        opacity: .9;
    }

    .detail-back:hover {
        color: white;
        opacity: 1;
    }

    @media(max-width: 767px) {
        .detail-hero { padding: 23px; }
        .detail-card { padding: 19px; }
        .info-row { grid-template-columns: 1fr; }
    }
</style>

<div class="detail-page">

    <div class="detail-hero mb-4">
        <a href="{{ route('borrowings.index') }}" class="detail-back d-inline-flex align-items-center gap-1">
            {!! $svg($icons['back']) !!} Kembali ke Peminjaman
        </a>

        <div class="row align-items-center mt-4">
            <div class="col-md-8">
                <div class="small opacity-75 mb-2">DETAIL PENGAJUAN PEMINJAMAN</div>
                <h3 class="mb-2">Informasi Peminjaman</h3>
                <div class="opacity-75">
                    Nomor pengajuan #{{ str_pad($borrowing->id, 5, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} px-3 py-2 fs-6">
                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">{!! $svg($icons['user']) !!}</div>
                <h5 class="detail-section-title">Informasi Pemohon</h5>

                <div class="info-row">
                    <div class="detail-label">Nama Pemohon</div>
                    <div class="detail-value is-primary">{{ $borrowing->user->name }}</div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value is-plain">{{ $borrowing->user->email }}</div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Peran Akun</div>
                    <div class="detail-value"><span class="chip text-capitalize">{{ $borrowing->user->role }}</span></div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Diajukan Pada</div>
                    <div class="detail-value is-plain">{{ $fmtDate($borrowing->created_at) }}, {{ $borrowing->created_at->format('H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">{!! $svg($icons['building']) !!}</div>
                <h5 class="detail-section-title">Informasi Fasilitas</h5>

                <div class="info-row">
                    <div class="detail-label">Nama Fasilitas</div>
                    <div class="detail-value is-primary">{{ $borrowing->facility->name }}</div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Kode Fasilitas</div>
                    <div class="detail-value"><span class="chip mono">{{ $borrowing->facility->code }}</span></div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Lokasi</div>
                    <div class="detail-value is-plain">{{ $borrowing->facility->location }}</div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Total Unit</div>
                    <div class="detail-value is-plain">{{ $borrowing->facility->units }} unit</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">{!! $svg($icons['calendar']) !!}</div>
                <h5 class="detail-section-title">Jadwal Peminjaman</h5>

                <div class="info-row">
                    <div class="detail-label">Mulai</div>
                    <div class="detail-value">
                        {{ $fmtDate($borrowing->start_at) }}
                        <span class="sub">Pukul {{ $borrowing->start_at->format('H:i') }} WIB</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Selesai</div>
                    <div class="detail-value">
                        {{ $fmtDate($borrowing->end_at) }}
                        <span class="sub">Pukul {{ $borrowing->end_at->format('H:i') }} WIB</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Durasi</div>
                    <div class="detail-value is-plain">{{ $duration }}</div>
                </div>
                <div class="info-row">
                    <div class="detail-label">Unit Diminta</div>
                    <div class="detail-value">
                        {{ $borrowing->units }} unit
                        @if($availableUnits !== null)
                            <span class="chip {{ $availableUnits >= $borrowing->units ? 'ok' : 'bad' }} ms-2">
                                Tersisa {{ $availableUnits }} unit pada jadwal ini
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">{!! $svg($icons['note']) !!}</div>
                <h5 class="detail-section-title">Tujuan Peminjaman</h5>

                <div class="purpose-box">{{ $borrowing->purpose }}</div>

                @if($borrowing->reject_reason)
                    <div class="alert alert-danger mt-3 mb-0">
                        <strong>Alasan penolakan:</strong><br>
                        {{ $borrowing->reject_reason }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    <div class="detail-card mb-4">
        <h5 class="detail-section-title">Perjalanan Pengajuan</h5>

        <div class="detail-timeline">

            <div class="timeline-item active">
                <span class="timeline-dot"></span>
                <div class="detail-value">Pengajuan dibuat</div>
                <div class="small text-muted">
                    Permohonan peminjaman telah tercatat dalam sistem.
                </div>
            </div>

            @if($status === 'menunggu')
                <div class="timeline-item active">
                    <span class="timeline-dot"></span>
                    <div class="detail-value">Menunggu pemeriksaan Admin</div>
                    <div class="small text-muted">
                        Pengajuan sedang menunggu keputusan Admin.
                    </div>
                </div>
            @elseif($status === 'disetujui')
                <div class="timeline-item active">
                    <span class="timeline-dot"></span>
                    <div class="detail-value text-success">Peminjaman disetujui</div>
                    <div class="small text-muted">
                        Admin telah menyetujui permohonan peminjaman.
                    </div>
                </div>
            @elseif($status === 'ditolak')
                <div class="timeline-item active">
                    <span class="timeline-dot"></span>
                    <div class="detail-value text-danger">Pengajuan ditolak</div>
                    <div class="small text-muted">
                        Permohonan tidak dapat disetujui oleh Admin.
                    </div>
                </div>
            @elseif($status === 'dibatalkan')
                <div class="timeline-item active">
                    <span class="timeline-dot"></span>
                    <div class="detail-value text-secondary">Pengajuan dibatalkan</div>
                    <div class="small text-muted">
                        Pemohon telah membatalkan permohonannya.
                    </div>
                </div>
            @endif

        </div>
    </div>

    @if(($isAdmin && $status === 'menunggu') || ($isOwner && $status === 'menunggu'))
        <div class="detail-card">
            <h5 class="detail-section-title">Tindakan Pengajuan</h5>

            @if($availableUnits !== null && $availableUnits < $borrowing->units)
                <div class="alert alert-warning">
                    Unit pada jadwal ini tidak mencukupi ({{ $availableUnits }} tersisa, diminta {{ $borrowing->units }}).
                    Sistem akan menolak persetujuan sampai bentrok jadwal teratasi.
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2">

                @if($isAdmin)
                    <form method="POST" action="/borrowings/{{ $borrowing->id }}/approve">
                        @csrf
                        <button class="btn btn-success detail-btn"
                                onclick="return confirm('Setujui pengajuan peminjaman ini?')">
                            Setujui Pengajuan
                        </button>
                    </form>

                    <form method="POST"
                          action="/borrowings/{{ $borrowing->id }}/reject"
                          class="d-flex flex-wrap gap-2">
                        @csrf
                        <input type="text"
                               name="reject_reason"
                               class="form-control"
                               placeholder="Masukkan alasan penolakan"
                               required
                               maxlength="255"
                               style="max-width:300px">

                        <button class="btn btn-danger detail-btn"
                                onclick="return confirm('Tolak pengajuan ini?')">
                            Tolak Pengajuan
                        </button>
                    </form>
                @elseif($isOwner)
                    <form method="POST" action="/borrowings/{{ $borrowing->id }}/cancel">
                        @csrf
                        <button class="btn btn-outline-danger detail-btn"
                                onclick="return confirm('Batalkan pengajuan ini?')">
                            Batalkan Pengajuan
                        </button>
                    </form>
                @endif

            </div>
        </div>
    @endif

</div>
@endsection

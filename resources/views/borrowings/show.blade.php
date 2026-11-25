
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

    .detail-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 13px 28px rgba(24,62,96,.09);
    }

    .detail-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
        margin-bottom: 7px;
    }

    .detail-value {
        color: var(--navy);
        font-weight: 700;
        font-size: 15px;
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
        margin-bottom: 16px;
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
    }
</style>

<div class="detail-page">

    <div class="detail-hero mb-4">
        <a href="{{ route('borrowings.index') }}" class="detail-back">
            ← Kembali ke Peminjaman
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
                <div class="detail-icon">👤</div>
                <h5 class="detail-section-title">Informasi Pemohon</h5>

                <div class="mb-3">
                    <div class="detail-label">Nama Pemohon</div>
                    <div class="detail-value">{{ $borrowing->user->name }}</div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">{{ $borrowing->user->email }}</div>
                </div>

                <div>
                    <div class="detail-label">Peran Akun</div>
                    <div class="detail-value text-capitalize">
                        {{ $borrowing->user->role }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">🏫</div>
                <h5 class="detail-section-title">Informasi Fasilitas</h5>

                <div class="mb-3">
                    <div class="detail-label">Nama Fasilitas</div>
                    <div class="detail-value">{{ $borrowing->facility->name }}</div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Kode Fasilitas</div>
                    <div class="detail-value">{{ $borrowing->facility->code }}</div>
                </div>

                <div>
                    <div class="detail-label">Lokasi</div>
                    <div class="detail-value">{{ $borrowing->facility->location }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">📅</div>
                <h5 class="detail-section-title">Jadwal Peminjaman</h5>

                <div class="mb-3">
                    <div class="detail-label">Tanggal Mulai</div>
                    <div class="detail-value">
                        {{ $borrowing->start_at->format('d F Y, H:i') }} WIB
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">Tanggal Selesai</div>
                    <div class="detail-value">
                        {{ $borrowing->end_at->format('d F Y, H:i') }} WIB
                    </div>
                </div>

                <div>
                    <div class="detail-label">Jumlah Unit</div>
                    <div class="detail-value">{{ $borrowing->units }} unit</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-icon">📝</div>
                <h5 class="detail-section-title">Tujuan Peminjaman</h5>

                <div class="detail-value" style="line-height:1.8;font-weight:500">
                    {{ $borrowing->purpose }}
                </div>

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

            <div class="d-flex flex-wrap gap-2">

                @if($isAdmin)
                    <form method="POST" action="/borrowings/{{ $borrowing->id }}/approve">
                        @csrf
                        <button class="btn btn-success detail-btn"
                                onclick="return confirm('Setujui pengajuan peminjaman ini?')">
                            ✓ Setujui Pengajuan
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

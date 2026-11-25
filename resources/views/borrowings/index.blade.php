
@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
@php
    $isAdmin = auth()->user()->isAdmin();

    $statusConfig = [
        'menunggu' => [
            'label' => 'Menunggu',
            'class' => 'bor-status-pending',
            'dot' => 'bor-dot-pending',
        ],
        'disetujui' => [
            'label' => 'Disetujui',
            'class' => 'bor-status-approved',
            'dot' => 'bor-dot-approved',
        ],
        'ditolak' => [
            'label' => 'Ditolak',
            'class' => 'bor-status-rejected',
            'dot' => 'bor-dot-rejected',
        ],
        'dibatalkan' => [
            'label' => 'Dibatalkan',
            'class' => 'bor-status-cancelled',
            'dot' => 'bor-dot-cancelled',
        ],
    ];
@endphp

<style>
    .bor-page {
        color: #183e60;
        animation: borFadeUp .65s ease both;
    }

    @keyframes borFadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .bor-page * {
        box-sizing: border-box;
    }

    /* PAGE HEADER */
    .bor-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin: 5px 0 25px;
    }

    .bor-heading h2 {
        color: #183e60;
        font-size: 27px;
        font-weight: 800;
        letter-spacing: -.7px;
        margin: 0 0 7px;
    }

    .bor-heading p {
        color: #8294a5;
        font-size: 13px;
        margin: 0;
        line-height: 1.7;
    }

    .bor-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 12px 19px;
        border-radius: 11px;
        background: linear-gradient(135deg, #368bd0, #236da9);
        color: white !important;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none !important;
        box-shadow: 0 7px 17px rgba(54,139,208,.2);
        transition: .25s ease;
        white-space: nowrap;
    }

    .bor-add-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 11px 22px rgba(54,139,208,.28);
    }

    .bor-add-icon {
        font-size: 19px;
        line-height: 1;
    }

    /* STATISTICS */
    .bor-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 27px;
    }

    .bor-stat-card {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 15px;
        min-height: 112px;
        padding: 20px 22px;
        background: white;
        border: 1px solid #edf2f7;
        border-radius: 17px;
        box-shadow: 0 5px 20px rgba(24,62,96,.045);
        transition: .25s ease;
    }

    .bor-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(24,62,96,.09);
    }

    .bor-stat-icon {
        flex: 0 0 49px;
        width: 49px;
        height: 49px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        font-size: 22px;
    }

    .bor-icon-blue {
        color: #368bd0;
        background: #eaf5ff;
    }

    .bor-icon-yellow {
        color: #d69b24;
        background: #fff6df;
    }

    .bor-icon-green {
        color: #21976b;
        background: #e6f8ef;
    }

    .bor-stat-info {
        min-width: 0;
    }

    .bor-stat-info span {
        display: block;
        color: #8798a8;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .bor-stat-info strong {
        display: block;
        color: #183e60;
        font-size: 27px;
        line-height: 1.1;
        font-weight: 800;
    }

    .bor-stat-decoration {
        position: absolute;
        width: 85px;
        height: 85px;
        right: -27px;
        bottom: -39px;
        border-radius: 50%;
        background: rgba(54,139,208,.045);
    }

    /* SECTION HEADING */
    .bor-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 15px;
    }

    .bor-section-heading h3 {
        margin: 0 0 5px;
        color: #183e60;
        font-size: 17px;
        font-weight: 800;
    }

    .bor-section-heading p {
        margin: 0;
        color: #91a0ad;
        font-size: 11px;
    }

    .bor-total-label {
        padding: 8px 12px;
        border-radius: 9px;
        color: #368bd0;
        background: #eaf5ff;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* TOOLBAR */
    .bor-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 13px;
        margin-bottom: 15px;
    }

    .bor-search {
        position: relative;
        flex: 1;
        max-width: 440px;
    }

    .bor-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9aabba;
        font-size: 17px;
        pointer-events: none;
    }

    .bor-search input {
        width: 100%;
        height: 43px;
        padding: 0 15px 0 42px;
        border: 1px solid #e7edf3;
        border-radius: 11px;
        outline: none;
        color: #34536d;
        background: white;
        font-size: 12px;
        transition: .2s ease;
    }

    .bor-search input:focus {
        border-color: #75b6e9;
        box-shadow: 0 0 0 3px rgba(54,139,208,.09);
    }

    .bor-search input::placeholder {
        color: #a7b3bf;
    }

    .bor-filter {
        width: 190px;
        height: 43px;
        padding: 0 13px;
        border: 1px solid #e7edf3;
        border-radius: 11px;
        outline: none;
        color: #526e84;
        background: white;
        font-size: 12px;
        cursor: pointer;
    }

    .bor-filter:focus {
        border-color: #75b6e9;
        box-shadow: 0 0 0 3px rgba(54,139,208,.09);
    }

    /* TABLE CARD */
    .bor-table-card {
        overflow: hidden;
        background: white;
        border: 1px solid #edf1f5;
        border-radius: 17px;
        box-shadow: 0 6px 24px rgba(24,62,96,.045);
    }

    .bor-table-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 18px 21px;
        border-bottom: 1px solid #edf1f5;
    }

    .bor-table-top strong {
        color: #244b6b;
        font-size: 13px;
        font-weight: 800;
    }

    .bor-table-top span {
        color: #99a8b5;
        font-size: 11px;
    }

    .bor-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .bor-table {
        width: 100%;
        min-width: 1050px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .bor-table thead th {
        padding: 15px 16px;
        color: #8193a3;
        background: #f7fafd;
        border-bottom: 1px solid #edf1f5;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .45px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .bor-table tbody td {
        padding: 16px;
        color: #526b80;
        border-bottom: 1px solid #f0f3f6;
        font-size: 12px;
        vertical-align: middle;
    }

    .bor-table tbody tr {
        transition: background .2s ease;
    }

    .bor-table tbody tr:hover {
        background: #f5faff;
    }

    .bor-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .bor-person {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 145px;
    }

    .bor-avatar {
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 11px;
        color: #368bd0;
        background: #eaf5ff;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .bor-person-name {
        color: #244b6b;
        font-size: 12px;
        font-weight: 700;
    }

    .bor-facility-name {
        color: #315773;
        font-weight: 700;
    }

    .bor-date {
        display: block;
        color: #365a75;
        font-weight: 700;
        white-space: nowrap;
    }

    .bor-time {
        display: block;
        color: #9aa9b6;
        font-size: 10px;
        margin-top: 4px;
    }

    .bor-purpose {
        display: block;
        max-width: 190px;
        color: #72889a;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    /* STATUS BADGES */
    .bor-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 30px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .bor-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .bor-status-pending {
        color: #b77d13;
        background: #fff5dc;
    }

    .bor-dot-pending { background: #e8ae39; }

    .bor-status-approved {
        color: #16845c;
        background: #e5f7ee;
    }

    .bor-dot-approved { background: #26a875; }

    .bor-status-rejected {
        color: #c44e58;
        background: #ffebed;
    }

    .bor-dot-rejected { background: #df6570; }

    .bor-status-cancelled {
        color: #758391;
        background: #edf1f4;
    }

    .bor-dot-cancelled { background: #98a5b1; }

    .bor-reject-reason {
        max-width: 160px;
        margin-top: 7px;
        color: #c44e58;
        font-size: 10px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    /* ACTIONS */
    .bor-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 180px;
    }

    .bor-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 31px;
        padding: 7px 10px;
        border: 0;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
        white-space: nowrap;
    }

    .bor-action-btn:hover {
        transform: translateY(-2px);
        filter: brightness(.97);
    }

    .bor-btn-approve {
        color: #16845c;
        background: #e5f7ee;
    }

    .bor-btn-reject {
        color: #c44e58;
        background: #ffebed;
    }

    .bor-btn-cancel {
        color: #c44e58;
        background: #fff0f1;
    }

    .bor-reject-form {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 145px;
    }

    .bor-reject-form input {
        width: 145px;
        height: 31px;
        padding: 0 9px;
        border: 1px solid #f0dadd;
        border-radius: 7px;
        outline: none;
        font-size: 10px;
    }

    .bor-reject-form input:focus {
        border-color: #df8790;
    }

    .bor-reject-form button {
        align-self: flex-start;
    }

    .bor-no-action {
        color: #b0bbc4;
        font-size: 11px;
    }

    /* EMPTY STATE */
    .bor-empty {
        padding: 65px 20px;
        text-align: center;
    }

    .bor-empty-icon {
        width: 75px;
        height: 75px;
        display: grid;
        place-items: center;
        margin: 0 auto 18px;
        border-radius: 24px;
        color: #65a8dc;
        background: #edf7ff;
        font-size: 32px;
    }

    .bor-empty h4 {
        margin: 0 0 8px;
        color: #315773;
        font-size: 16px;
        font-weight: 800;
    }

    .bor-empty p {
        max-width: 350px;
        margin: 0 auto 19px;
        color: #91a2b0;
        font-size: 12px;
        line-height: 1.8;
    }

    .bor-empty-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 16px;
        border-radius: 9px;
        color: white !important;
        background: #368bd0;
        text-decoration: none !important;
        font-size: 11px;
        font-weight: 700;
    }

    .bor-no-results {
        display: none;
        padding: 35px 15px;
        color: #91a2b0;
        text-align: center;
        font-size: 12px;
    }

    /* PAGINATION */
    .bor-pagination {
        margin-top: 22px;
    }

    .bor-pagination nav {
        display: flex;
        justify-content: center;
    }

    .bor-pagination .pagination {
        gap: 5px;
    }

    .bor-pagination .page-link {
        border: 1px solid #e5edf4;
        border-radius: 8px !important;
        color: #368bd0;
        font-size: 12px;
        box-shadow: none;
    }

    .bor-pagination .active .page-link {
        color: white;
        background: #368bd0;
        border-color: #368bd0;
    }

    /* RESPONSIVE */
    @media (max-width: 800px) {
        .bor-stats {
            gap: 10px;
        }

        .bor-stat-card {
            padding: 15px;
            gap: 10px;
        }

        .bor-stat-icon {
            width: 40px;
            height: 40px;
            flex-basis: 40px;
            font-size: 18px;
        }

        .bor-stat-info strong {
            font-size: 23px;
        }
    }

    @media (max-width: 600px) {
        .bor-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .bor-add-btn {
            width: 100%;
        }

        .bor-stats {
            grid-template-columns: 1fr;
        }

        .bor-stat-card {
            min-height: 88px;
        }

        .bor-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .bor-search {
            max-width: none;
        }

        .bor-filter {
            width: 100%;
        }

        .bor-section-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .bor-page,
        .bor-stat-card,
        .bor-table tbody tr,
        .bor-action-btn {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div class="bor-page">

    {{-- PAGE HEADER --}}
    <section class="bor-heading">
        <div>
            <h2>Peminjaman</h2>
            <p>
                Kelola dan pantau seluruh pengajuan peminjaman
                fasilitas kampus dalam satu tempat.
            </p>
        </div>

        @if(!$isAdmin)
            <a href="/borrowings/create" class="bor-add-btn">
                <span class="bor-add-icon">＋</span>
                Ajukan Peminjaman
            </a>
        @endif
    </section>

    {{-- STATISTICS --}}
    <section class="bor-stats">

        <div class="bor-stat-card">
            <div class="bor-stat-icon bor-icon-blue">▤</div>
            <div class="bor-stat-info">
                <span>Total Pengajuan</span>
                <strong>{{ $totalBorrowings }}</strong>
            </div>
            <div class="bor-stat-decoration"></div>
        </div>

        <div class="bor-stat-card">
            <div class="bor-stat-icon bor-icon-yellow">◷</div>
            <div class="bor-stat-info">
                <span>Menunggu Persetujuan</span>
                <strong>{{ $pendingBorrowings }}</strong>
            </div>
            <div class="bor-stat-decoration"></div>
        </div>

        <div class="bor-stat-card">
            <div class="bor-stat-icon bor-icon-green">✓</div>
            <div class="bor-stat-info">
                <span>Disetujui</span>
                <strong>{{ $approvedBorrowings }}</strong>
            </div>
            <div class="bor-stat-decoration"></div>
        </div>

    </section>

    {{-- SECTION TITLE --}}
    <div class="bor-section-heading">
        <div>
            <h3>Daftar Pengajuan</h3>
            <p>
                @if($isAdmin)
                    Pantau dan proses pengajuan peminjaman dari seluruh pengguna.
                @else
                    Pantau status pengajuan peminjaman fasilitas kamu.
                @endif
            </p>
        </div>

        <span class="bor-total-label">
            {{ $items->total() }} Pengajuan
        </span>
    </div>

    {{-- SEARCH AND FILTER --}}
    <div class="bor-toolbar">

        <div class="bor-search">
            <span class="bor-search-icon">⌕</span>
            <input
                type="text"
                id="borSearch"
                placeholder="Cari pemohon, fasilitas, atau tujuan..."
                autocomplete="off"
            >
        </div>

        <select id="borStatusFilter" class="bor-filter">
            <option value="">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="disetujui">Disetujui</option>
            <option value="ditolak">Ditolak</option>
            <option value="dibatalkan">Dibatalkan</option>
        </select>

    </div>

    {{-- TABLE --}}
    <section class="bor-table-card">

        <div class="bor-table-top">
            <strong>Riwayat Peminjaman</strong>
            <span>Data pengajuan fasilitas kampus</span>
        </div>

        <div class="bor-table-wrap">

            <table class="bor-table">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Fasilitas</th>
                        <th>Unit</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="borTableBody">

                    @forelse($items as $b)

                        @php
                            $status = $b->status ?? '';
                            $statusInfo = $statusConfig[$status] ?? [
                                'label' => ucfirst($status),
                                'class' => 'bor-status-cancelled',
                                'dot' => 'bor-dot-cancelled',
                            ];

                            $userName = $b->user->name ?? 'Pengguna';
                            $facilityName = $b->facility->name ?? 'Fasilitas tidak tersedia';

                            $searchText = strtolower(
                                $userName . ' ' .
                                $facilityName . ' ' .
                                ($b->purpose ?? '') . ' ' .
                                $status
                            );
                        @endphp

                        <tr
                            class="bor-row"
                            data-search="{{ $searchText }}"
                            data-status="{{ $status }}"
                        >

                            {{-- PEMOHON --}}
                            <td>
                                <div class="bor-person">
                                    <div class="bor-avatar">
                                        {{ substr($userName, 0, 1) }}
                                    </div>
                                    <span class="bor-person-name">
                                        {{ $userName }}
                                    </span>
                                </div>
                            </td>

                            {{-- FASILITAS --}}
                            <td>
                                <span class="bor-facility-name">
                                    {{ $facilityName }}
                                </span>
                            </td>

                            {{-- UNIT --}}
                            <td>
                                <strong>{{ $b->units }}</strong>
                            </td>

                            {{-- MULAI --}}
                            <td>
                                <span class="bor-date">
                                    {{ $b->start_at->format('d/m/Y') }}
                                </span>
                                <span class="bor-time">
                                    {{ $b->start_at->format('H:i') }} WIB
                                </span>
                            </td>

                            {{-- SELESAI --}}
                            <td>
                                <span class="bor-date">
                                    {{ $b->end_at->format('d/m/Y') }}
                                </span>
                                <span class="bor-time">
                                    {{ $b->end_at->format('H:i') }} WIB
                                </span>
                            </td>

                            {{-- TUJUAN --}}
                            <td>
                                <span class="bor-purpose">
                                    {{ $b->purpose }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="bor-status {{ $statusInfo['class'] }}">
                                    <span class="bor-status-dot {{ $statusInfo['dot'] }}"></span>
                                    {{ $statusInfo['label'] }}
                                </span>

                                @if($b->reject_reason)
                                    <div class="bor-reject-reason">
                                        Alasan: {{ $b->reject_reason }}
                                    </div>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="bor-actions">
                                    <a href="{{ route('borrowings.show', $b->id) }}"
                                       class="btn btn-sm btn-outline-primary mb-1">
                                        Detail
                                    </a>

                                    @if($isAdmin && $b->status === 'menunggu')

                                        <form
                                            method="POST"
                                            action="/borrowings/{{ $b->id }}/approve"
                                            onsubmit="return confirm('Setujui pengajuan peminjaman ini?')"
                                        >
                                            @csrf
                                            <button type="submit" class="bor-action-btn bor-btn-approve">
                                                ✓ Setujui
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="/borrowings/{{ $b->id }}/reject"
                                            class="bor-reject-form"
                                            onsubmit="return confirm('Tolak pengajuan peminjaman ini?')"
                                        >
                                            @csrf

                                            <input
                                                type="text"
                                                name="reject_reason"
                                                placeholder="Alasan penolakan..."
                                                maxlength="255"
                                                required
                                            >

                                            <button type="submit" class="bor-action-btn bor-btn-reject">
                                                ✕ Tolak
                                            </button>
                                        </form>

                                    @elseif($b->user_id === auth()->id() && $b->status === 'menunggu')

                                        <form
                                            method="POST"
                                            action="/borrowings/{{ $b->id }}/cancel"
                                            onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')"
                                        >
                                            @csrf
                                            <button type="submit" class="bor-action-btn bor-btn-cancel">
                                                Batalkan
                                            </button>
                                        </form>

                                    @else

                                        <span class="bor-no-action">—</span>

                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8">
                                <div class="bor-empty">
                                    <div class="bor-empty-icon">▤</div>

                                    <h4>Belum Ada Pengajuan</h4>

                                    <p>
                                        Belum ada data peminjaman fasilitas yang tercatat.
                                        Pengajuan baru akan muncul di halaman ini.
                                    </p>

                                    @if(!$isAdmin)
                                        <a href="/borrowings/create" class="bor-empty-link">
                                            ＋ Ajukan Peminjaman
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

            <div class="bor-no-results" id="borNoResults">
                <div style="font-size:27px; margin-bottom:8px;">⌕</div>
                <strong>Data tidak ditemukan</strong>
                <div style="margin-top:5px;">
                    Coba gunakan kata kunci atau filter status yang berbeda.
                </div>
            </div>

        </div>
    </section>

    {{-- PAGINATION --}}
    @if($items->hasPages())
        <div class="bor-pagination">
            {{ $items->links() }}
        </div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('borSearch');
    const statusFilter = document.getElementById('borStatusFilter');
    const rows = document.querySelectorAll('.bor-row');
    const noResults = document.getElementById('borNoResults');

    function filterBorrowings() {
        const keyword = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        let visibleCount = 0;

        rows.forEach(function (row) {
            const searchData = row.dataset.search || '';
            const status = row.dataset.status || '';

            const matchesSearch = searchData.includes(keyword);
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display =
                rows.length > 0 && visibleCount === 0 ? 'block' : 'none';
        }
    }

    searchInput.addEventListener('input', filterBorrowings);
    statusFilter.addEventListener('change', filterBorrowings);

});
</script>

@endsection

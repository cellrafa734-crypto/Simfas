@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
@php
    $isAdmin = auth()->user()->isAdmin();

    $statusConfig = [
        'menunggu'         => ['Menunggu', 'st-wait', 'menunggu'],
        'disetujui'        => ['Disetujui', 'st-ok', 'disetujui'],
        'sedang_digunakan' => ['Sedang digunakan', 'st-ok', 'disetujui'],
        'selesai'          => ['Selesai', 'st-done', 'riwayat'],
        'ditolak'          => ['Ditolak', 'st-no', 'riwayat'],
        'dibatalkan'       => ['Dibatalkan', 'st-done', 'riwayat'],
    ];

    $fmt = fn ($d, $f) => $d->copy()->locale('id')->translatedFormat($f);
@endphp

<style>
    .sf-bp { display: grid; gap: 20px; max-width: 1100px; }
    .sf-bp h2, .sf-bp h4 { margin: 0; }

    .sf-bp-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .sf-bp-head h2 { font-size: 1.6rem; letter-spacing: -.02em; }
    .sf-bp-head p { margin: 4px 0 0; color: var(--muted); }

    .sf-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 11px 18px; border-radius: var(--radius-md); font-weight: 700; font-size: .92rem;
        text-decoration: none; border: 0; cursor: pointer; color: #fff;
        background: linear-gradient(135deg, var(--blue-600), var(--blue-700));
        box-shadow: 0 8px 20px rgba(37, 99, 235, .25);
        transition: transform .2s var(--ease), box-shadow .2s var(--ease);
    }
    .sf-btn:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 12px 26px rgba(37, 99, 235, .34); }

    /* Tab + pencarian */
    .sf-bar { display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
    .sf-tabs { display: inline-flex; gap: 4px; padding: 4px; background: #e9eff9; border-radius: 14px; flex-wrap: wrap; }
    .sf-tab {
        display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border: 0; border-radius: 11px;
        background: transparent; color: var(--ink-soft); font-weight: 600; font-size: .88rem; cursor: pointer;
        transition: all .2s var(--ease);
    }
    .sf-tab:hover { color: var(--blue-700); }
    .sf-tab.on { background: var(--surface); color: var(--navy-900); box-shadow: var(--shadow-xs); }
    .sf-pill { min-width: 20px; padding: 1px 7px; border-radius: 999px; background: #f59e0b; color: #fff; font-size: .72rem; font-weight: 700; text-align: center; }

    .sf-find { position: relative; flex: 1; min-width: 200px; max-width: 340px; }
    .sf-find svg { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; stroke: var(--muted); fill: none; stroke-width: 2; stroke-linecap: round; }
    .sf-find input {
        width: 100%; padding: 10px 14px 10px 38px; border: 1px solid var(--line); border-radius: var(--radius-md);
        background: var(--surface); font-size: .9rem;
    }
    .sf-find input:focus { outline: none; border-color: var(--blue-500); box-shadow: 0 0 0 4px rgba(59, 130, 246, .15); }

    /* Daftar kartu */
    .sf-items { display: grid; gap: 12px; }
    .sf-item {
        display: grid; grid-template-columns: 64px 1fr auto; gap: 16px; align-items: center;
        padding: 16px 18px; background: var(--surface); border: 1px solid var(--line);
        border-radius: var(--radius-lg); box-shadow: var(--shadow-xs);
        transition: transform .22s var(--ease), box-shadow .22s var(--ease), border-color .22s;
        animation: sfIn .4s var(--ease) backwards;
    }
    .sf-item:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); border-color: #bcd2f5; }
    @keyframes sfIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    .sf-date { width: 64px; padding: 8px 0; border-radius: 14px; background: var(--blue-50); text-align: center; line-height: 1.1; }
    .sf-date b { display: block; font-size: 1.5rem; color: var(--navy-900); }
    .sf-date span { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--blue-600); }

    .sf-main { min-width: 0; display: grid; gap: 3px; }
    .sf-main h4 { font-size: 1.02rem; }
    .sf-meta { color: var(--ink-soft); font-size: .88rem; }
    .sf-purpose { color: var(--muted); font-size: .86rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sf-who { font-size: .8rem; font-weight: 700; color: var(--blue-700); }
    .sf-reason { margin-top: 6px; padding: 7px 11px; border-radius: 10px; background: #fef2f2; border: 1px solid #fbcaca; color: #991b1b; font-size: .83rem; }

    .sf-side { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
    .sf-st { padding: 5px 12px; border-radius: 999px; font-size: .78rem; font-weight: 700; white-space: nowrap; }
    .st-wait { background: #fffbeb; color: #92400e; }
    .st-ok { background: #ecfdf3; color: #166534; }
    .st-no { background: #fef2f2; color: #991b1b; }
    .st-done { background: #eef2f7; color: #475569; }

    .sf-acts { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; align-items: center; }
    .sf-acts form { margin: 0; }
    .sf-a {
        padding: 7px 14px; border-radius: 10px; font-size: .83rem; font-weight: 700; cursor: pointer;
        text-decoration: none; border: 1px solid var(--blue-100); background: var(--blue-50); color: var(--blue-700);
        transition: all .18s;
    }
    .sf-a:hover { background: var(--blue-600); border-color: var(--blue-600); color: #fff; }
    .sf-a.ok { background: #16a34a; border-color: #16a34a; color: #fff; }
    .sf-a.ok:hover { background: #15803d; }
    .sf-a.bad { background: #fff; border-color: #fbcaca; color: #b91c1c; }
    .sf-a.bad:hover { background: #dc2626; border-color: #dc2626; color: #fff; }

    /* Form tolak yang muncul saat dibutuhkan */
    .sf-rej { position: relative; }
    .sf-rej summary { list-style: none; }
    .sf-rej summary::-webkit-details-marker { display: none; }
    .sf-rej[open] summary { background: #dc2626; border-color: #dc2626; color: #fff; }
    .sf-rej form { display: flex; gap: 6px; margin-top: 8px; }
    .sf-rej input { padding: 7px 11px; border: 1px solid #fbcaca; border-radius: 10px; font-size: .84rem; width: 210px; max-width: 100%; }
    .sf-rej input:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, .15); }

    .sf-empty { text-align: center; padding: 48px 20px; background: var(--surface); border: 1px dashed #c3d3ea; border-radius: var(--radius-lg); color: var(--muted); }
    .sf-empty h4 { color: var(--navy-900); margin-bottom: 6px; }
    .sf-empty .sf-btn { margin-top: 14px; }

    @media (prefers-reduced-motion: reduce) { .sf-item { animation: none; } }
    @media (max-width: 720px) {
        .sf-item { grid-template-columns: 56px 1fr; }
        .sf-date { width: 56px; }
        .sf-side { grid-column: 1 / -1; flex-direction: row; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .sf-find { max-width: none; }
        .sf-btn.sf-new { width: 100%; }
    }
</style>

<div class="sf-bp">

    <section class="sf-bp-head">
        <div>
            <h2>Peminjaman</h2>
            <p>
                @if($isAdmin)
                    Proses pengajuan dari mahasiswa dan organisasi.
                @else
                    Pantau status pengajuanmu di satu tempat.
                @endif
            </p>
        </div>

        @unless($isAdmin)
            <a href="/borrowings/create" class="sf-btn sf-new">＋ Ajukan Peminjaman</a>
        @endunless
    </section>

    {{-- TAB STATUS + PENCARIAN --}}
    <section class="sf-bar">
        <div class="sf-tabs" id="sfTabs" role="tablist">
            <button type="button" class="sf-tab on" data-group="">Semua</button>
            <button type="button" class="sf-tab" data-group="menunggu">
                Menunggu
                @if($pendingBorrowings > 0)<span class="sf-pill">{{ $pendingBorrowings }}</span>@endif
            </button>
            <button type="button" class="sf-tab" data-group="disetujui">Disetujui</button>
            <button type="button" class="sf-tab" data-group="riwayat">Riwayat</button>
        </div>

        <div class="sf-find">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" id="sfFind" placeholder="Cari fasilitas atau kegiatan..." aria-label="Cari peminjaman" autocomplete="off">
        </div>
    </section>

    {{-- DAFTAR --}}
    <section class="sf-items" id="sfItems">

        @forelse($items as $b)
            @php
                $conf = $statusConfig[$b->status] ?? [ucfirst($b->status), 'st-done', 'riwayat'];
                $userName = $b->user->name ?? 'Pengguna';
                $facilityName = $b->facility->name ?? 'Fasilitas tidak tersedia';
                $sameDay = $b->start_at->isSameDay($b->end_at);
            @endphp

            <article class="sf-item"
                     style="animation-delay: {{ min($loop->index, 8) * 35 }}ms"
                     data-group="{{ $conf[2] }}"
                     data-search="{{ strtolower($userName . ' ' . $facilityName . ' ' . ($b->purpose ?? '') . ' ' . $conf[0]) }}">

                <div class="sf-date" aria-hidden="true">
                    <b>{{ $b->start_at->format('d') }}</b>
                    <span>{{ $fmt($b->start_at, 'M') }}</span>
                </div>

                <div class="sf-main">
                    @if($isAdmin)
                        <span class="sf-who">{{ $userName }}</span>
                    @endif
                    <h4>{{ $facilityName }} · {{ $b->units }} unit</h4>
                    <span class="sf-meta">
                        @if($sameDay)
                            {{ $fmt($b->start_at, 'l, d F Y') }} · {{ $b->start_at->format('H:i') }}–{{ $b->end_at->format('H:i') }} WIB
                        @else
                            {{ $fmt($b->start_at, 'd M, H:i') }} → {{ $fmt($b->end_at, 'd M Y, H:i') }} WIB
                        @endif
                    </span>
                    <span class="sf-purpose">{{ $b->purpose }}</span>

                    @if($b->reject_reason)
                        <div class="sf-reason">Alasan: {{ $b->reject_reason }}</div>
                    @endif
                </div>

                <div class="sf-side">
                    <span class="sf-st {{ $conf[1] }}">{{ $conf[0] }}</span>

                    <div class="sf-acts">
                        <a href="{{ route('borrowings.show', $b->id) }}" class="sf-a">Detail</a>

                        @if($isAdmin && $b->status === 'menunggu')
                            <form method="POST" action="/borrowings/{{ $b->id }}/approve"
                                  onsubmit="return confirm('Setujui pengajuan peminjaman ini?')">
                                @csrf
                                <button type="submit" class="sf-a ok">Setujui</button>
                            </form>

                            <details class="sf-rej">
                                <summary class="sf-a bad">Tolak</summary>
                                <form method="POST" action="/borrowings/{{ $b->id }}/reject"
                                      onsubmit="return confirm('Tolak pengajuan peminjaman ini?')">
                                    @csrf
                                    <input type="text" name="reject_reason" placeholder="Alasan penolakan..." maxlength="255" required>
                                    <button type="submit" class="sf-a bad">Kirim</button>
                                </form>
                            </details>

                        @elseif($b->user_id === auth()->id() && $b->status === 'menunggu')
                            <form method="POST" action="/borrowings/{{ $b->id }}/cancel"
                                  onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                @csrf
                                <button type="submit" class="sf-a bad">Batalkan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>

        @empty
            <div class="sf-empty">
                <h4>Belum ada pengajuan</h4>
                <p>Pengajuan peminjaman fasilitas akan muncul di sini.</p>
                @unless($isAdmin)
                    <a href="/facilities" class="sf-btn">Cari fasilitas untuk dipinjam</a>
                @endunless
            </div>
        @endforelse

        <div class="sf-empty" id="sfNone" style="display:none;">
            <h4>Tidak ada yang cocok</h4>
            <p>Coba ganti tab atau kata kunci pencarian.</p>
        </div>
    </section>

    @if($items->hasPages())
        <div>{{ $items->links() }}</div>
    @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tabs = document.getElementById('sfTabs');
    var find = document.getElementById('sfFind');
    var rows = document.querySelectorAll('.sf-item');
    var none = document.getElementById('sfNone');
    var group = '';

    function apply() {
        var q = find.value.toLowerCase().trim();
        var shown = 0;

        rows.forEach(function (r) {
            var ok = (!group || r.dataset.group === group) && (r.dataset.search || '').includes(q);
            r.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });

        if (none) none.style.display = rows.length > 0 && shown === 0 ? '' : 'none';
    }

    tabs.addEventListener('click', function (e) {
        var btn = e.target.closest('.sf-tab');
        if (!btn) return;
        tabs.querySelectorAll('.sf-tab').forEach(function (b) { b.classList.remove('on'); });
        btn.classList.add('on');
        group = btn.dataset.group || '';
        apply();
    });

    find.addEventListener('input', apply);
});
</script>

@endsection

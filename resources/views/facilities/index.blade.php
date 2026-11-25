@extends('layouts.app')

@section('title', 'Fasilitas')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $canBorrow = in_array($user->role, ['mahasiswa', 'organisasi'], true);

    $statusLabels = [
        'tersedia'              => 'Tersedia',
        'dipinjam'              => 'Sedang dipinjam',
        'pemeliharaan'          => 'Pemeliharaan',
        'tidak_dapat_digunakan' => 'Tidak dapat digunakan',
    ];

    $conditionLabels = [
        'baik'         => 'Baik',
        'rusak_ringan' => 'Rusak ringan',
        'rusak_sedang' => 'Rusak sedang',
        'rusak_berat'  => 'Rusak berat',
    ];

    $categories = $items->pluck('category')->filter()->unique()->sort()->values();

    $facImg = function ($f) {
        if ($f->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($f->image_path)) {
            return asset('storage/' . $f->image_path);
        }
        $n = strtolower($f->name ?? '');
        $map = [
            'ruang belajar' => 'kelas', 'kelas' => 'kelas',
            'laboratorium' => 'laboratorium', 'lab' => 'laboratorium',
            'proyektor' => 'proyektor', 'aula' => 'aula',
            'sound' => 'sound-system', 'speaker' => 'sound-system',
        ];
        foreach ($map as $kw => $file) {
            if (str_contains($n, $kw) && file_exists(public_path("images/facilities/$file.jpg"))) {
                return asset("images/facilities/$file.jpg");
            }
        }
        return null;
    };
@endphp

<style>
    .sf-fp { display: grid; gap: 20px; }
    .sf-fp h2, .sf-fp h4 { margin: 0; }

    .sf-fp-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .sf-fp-head h2 { font-size: 1.6rem; letter-spacing: -.02em; }
    .sf-fp-head p { margin: 4px 0 0; color: var(--muted); }

    .sf-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 11px 18px; border-radius: var(--radius-md); font-weight: 700; font-size: .92rem;
        text-decoration: none; border: 0; cursor: pointer;
        background: linear-gradient(135deg, var(--blue-600), var(--blue-700)); color: #fff;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .25);
        transition: transform .2s var(--ease), box-shadow .2s var(--ease);
    }
    .sf-btn:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 12px 26px rgba(37, 99, 235, .34); }

    /* Pencarian + filter */
    .sf-tools { display: grid; gap: 12px; }
    .sf-search { position: relative; }
    .sf-search svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; stroke: var(--muted); fill: none; stroke-width: 2; stroke-linecap: round; }
    .sf-search input {
        width: 100%; padding: 13px 16px 13px 42px; border: 1px solid var(--line); border-radius: var(--radius-md);
        background: var(--surface); font-size: .95rem; color: var(--ink);
        transition: border-color .2s, box-shadow .2s;
    }
    .sf-search input:focus { outline: none; border-color: var(--blue-500); box-shadow: 0 0 0 4px rgba(59, 130, 246, .15); }

    .sf-chips { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .sf-chip-btn {
        padding: 7px 14px; border-radius: 999px; border: 1px solid var(--line); background: var(--surface);
        color: var(--ink-soft); font-size: .85rem; font-weight: 600; cursor: pointer;
        transition: all .18s var(--ease);
    }
    .sf-chip-btn:hover { border-color: var(--blue-400); color: var(--blue-700); }
    .sf-chip-btn.on { background: var(--navy-900); border-color: var(--navy-900); color: #fff; }
    .sf-chip-label { font-size: .78rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-right: 4px; }
    .sf-count { font-size: .88rem; color: var(--muted); }

    /* Kartu */
    .sf-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 18px; }
    .sf-card {
        display: flex; flex-direction: column; background: var(--surface); border: 1px solid var(--line);
        border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-xs);
        transition: transform .25s var(--ease), box-shadow .25s var(--ease), border-color .25s;
        animation: sfIn .45s var(--ease) backwards;
    }
    .sf-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: #bcd2f5; }
    @keyframes sfIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }

    .sf-photo { position: relative; aspect-ratio: 16 / 10; background: linear-gradient(135deg, var(--blue-50), #e2ecfb); overflow: hidden; }
    .sf-photo img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s var(--ease); }
    .sf-card:hover .sf-photo img { transform: scale(1.05); }
    .sf-ph { position: absolute; inset: 0; display: grid; place-items: center; color: #8fb0e6; }
    .sf-ph svg { width: 38px; height: 38px; stroke: currentColor; fill: none; stroke-width: 1.6; }

    .sf-badge {
        position: absolute; top: 12px; left: 12px; display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 11px; border-radius: 999px; font-size: .75rem; font-weight: 700;
        background: rgba(255, 255, 255, .95); box-shadow: 0 2px 8px rgba(10, 31, 68, .15);
    }
    .sf-badge i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .s-tersedia { color: #166534; } .s-tersedia i { background: #22c55e; }
    .s-dipinjam { color: #1d4ed8; } .s-dipinjam i { background: #3b82f6; }
    .s-pemeliharaan { color: #92400e; } .s-pemeliharaan i { background: #f59e0b; }
    .s-tidak_dapat_digunakan { color: #991b1b; } .s-tidak_dapat_digunakan i { background: #ef4444; }

    .sf-body { display: flex; flex-direction: column; gap: 10px; padding: 16px; flex: 1; }
    .sf-body h4 { font-size: 1.05rem; }
    .sf-info { display: grid; gap: 4px; color: var(--ink-soft); font-size: .88rem; }
    .sf-info span { display: flex; align-items: center; gap: 7px; }
    .sf-info svg { width: 15px; height: 15px; stroke: var(--muted); fill: none; stroke-width: 2; flex: none; }
    .sf-desc { margin: 0; color: var(--muted); font-size: .85rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .sf-warn { align-self: flex-start; padding: 3px 10px; border-radius: 999px; background: #fffbeb; border: 1px solid #fde7a2; color: #92400e; font-size: .76rem; font-weight: 600; }

    .sf-foot { margin-top: auto; padding-top: 6px; display: flex; gap: 8px; }
    .sf-foot .sf-btn { flex: 1; }
    .sf-off { flex: 1; text-align: center; padding: 11px; border-radius: var(--radius-md); background: #eef2f7; color: #64748b; font-weight: 600; font-size: .88rem; }
    .sf-edit, .sf-del {
        flex: 1; padding: 10px; border-radius: var(--radius-md); font-weight: 700; font-size: .86rem;
        text-align: center; text-decoration: none; cursor: pointer; border: 1px solid;
    }
    .sf-edit { background: var(--blue-50); border-color: var(--blue-100); color: var(--blue-700); }
    .sf-edit:hover { background: var(--blue-600); color: #fff; }
    .sf-del { background: #fff; border-color: #fbcaca; color: #b91c1c; width: 100%; }
    .sf-del:hover { background: #dc2626; border-color: #dc2626; color: #fff; }

    .sf-empty { grid-column: 1 / -1; text-align: center; padding: 48px 20px; background: var(--surface); border: 1px dashed #c3d3ea; border-radius: var(--radius-lg); color: var(--muted); }
    .sf-empty h4 { color: var(--navy-900); margin-bottom: 6px; }
    .sf-reset { margin-top: 10px; background: none; border: 0; color: var(--blue-600); font-weight: 700; cursor: pointer; }

    @media (prefers-reduced-motion: reduce) { .sf-card { animation: none; } }
    @media (max-width: 560px) { .sf-btn.sf-add { width: 100%; } }
</style>

<div class="sf-fp">

    <section class="sf-fp-head">
        <div>
            <h2>Fasilitas</h2>
            <p>
                @if($isAdmin)
                    Kelola ruang dan peralatan kampus.
                @else
                    Pilih ruang atau peralatan, lalu ajukan peminjaman langsung dari sini.
                @endif
            </p>
        </div>

        @if($isAdmin)
            <a href="/facilities/create" class="sf-btn sf-add">＋ Tambah Fasilitas</a>
        @endif
    </section>

    {{-- PENCARIAN + FILTER CEPAT --}}
    <section class="sf-tools">
        <div class="sf-search">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" id="sfSearch" placeholder="Cari nama, kode, atau lokasi..." aria-label="Cari fasilitas" autocomplete="off">
        </div>

        <div class="sf-chips" id="sfStatusChips">
            <span class="sf-chip-label">Status</span>
            <button type="button" class="sf-chip-btn on" data-status="">Semua</button>
            <button type="button" class="sf-chip-btn" data-status="tersedia">Tersedia</button>
            <button type="button" class="sf-chip-btn" data-status="dipinjam">Sedang dipinjam</button>
            @if($isAdmin)
                <button type="button" class="sf-chip-btn" data-status="pemeliharaan">Pemeliharaan</button>
                <button type="button" class="sf-chip-btn" data-status="tidak_dapat_digunakan">Tidak dapat digunakan</button>
            @endif
        </div>

        @if($categories->count() > 1)
            <div class="sf-chips" id="sfCatChips">
                <span class="sf-chip-label">Jenis</span>
                <button type="button" class="sf-chip-btn on" data-cat="">Semua</button>
                @foreach($categories as $cat)
                    <button type="button" class="sf-chip-btn" data-cat="{{ strtolower($cat) }}">{{ ucfirst($cat) }}</button>
                @endforeach
            </div>
        @endif

        <div class="sf-count" id="sfCount" aria-live="polite">{{ $items->total() }} fasilitas</div>
    </section>

    {{-- DAFTAR --}}
    <section class="sf-cards" id="sfCards">

        @forelse($items as $f)
            @php
                $status = strtolower($f->status ?? '');
                $img = $facImg($f);
                $borrowable = in_array($status, ['tersedia', 'dipinjam'], true);
            @endphp

            <article class="sf-card"
                     style="animation-delay: {{ min($loop->index, 8) * 40 }}ms"
                     data-search="{{ strtolower(($f->code ?? '') . ' ' . ($f->name ?? '') . ' ' . ($f->location ?? '') . ' ' . ($f->category ?? '')) }}"
                     data-status="{{ $status }}"
                     data-cat="{{ strtolower($f->category ?? '') }}">

                <div class="sf-photo">
                    <div class="sf-ph" @if($img) hidden @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="1.6"/><path d="m21 15-5-5L5 21"/></svg>
                    </div>
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $f->name }}" loading="lazy"
                             onerror="this.hidden=true;this.previousElementSibling.hidden=false;">
                    @endif
                    <span class="sf-badge s-{{ $status }}"><i></i>{{ $statusLabels[$status] ?? 'Belum diatur' }}</span>
                </div>

                <div class="sf-body">
                    <h4>{{ $f->name }}</h4>

                    <div class="sf-info">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $f->location ?: '-' }}
                        </span>
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8 12 3 3 8l9 5 9-5Z"/><path d="m3 8 0 8 9 5 9-5 0-8"/></svg>
                            {{ $f->units }} unit
                        </span>
                    </div>

                    @if($f->description)
                        <p class="sf-desc">{{ $f->description }}</p>
                    @endif

                    {{-- Kondisi hanya ditampilkan bila perlu diperhatikan --}}
                    @if($f->condition && $f->condition !== 'baik')
                        <span class="sf-warn">Kondisi: {{ $conditionLabels[$f->condition] ?? ucfirst(str_replace('_', ' ', $f->condition)) }}</span>
                    @endif

                    <div class="sf-foot">
                        @if($isAdmin)
                            <a href="/facilities/{{ $f->id }}/edit" class="sf-edit">Ubah</a>
                            <form method="POST" action="/facilities/{{ $f->id }}" style="flex:1;margin:0;"
                                  data-confirm="Yakin ingin menghapus fasilitas &quot;{{ $f->name }}&quot;? Tindakan ini tidak dapat dibatalkan.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sf-del">Hapus</button>
                            </form>
                        @elseif($canBorrow)
                            @if($borrowable)
                                <a href="/borrowings/create?facility={{ $f->id }}" class="sf-btn">Pinjam</a>
                            @else
                                <span class="sf-off">Belum bisa dipinjam</span>
                            @endif
                        @endif
                    </div>
                </div>
            </article>

        @empty
            <div class="sf-empty">
                <h4>Belum ada fasilitas</h4>
                <p>Data fasilitas kampus belum tersedia.</p>
            </div>
        @endforelse

        <div class="sf-empty" id="sfNone" style="display:none;">
            <h4>Tidak ada yang cocok</h4>
            <p>Coba kata kunci lain atau ubah filter.</p>
            <button type="button" class="sf-reset" id="sfReset">Reset filter</button>
        </div>
    </section>

    @if($items->hasPages())
        <div>{{ $items->links() }}</div>
    @endif

</div>

<script>
// Konfirmasi sebelum menghapus fasilitas
document.addEventListener('submit', function (event) {
    var message = event.target.dataset ? event.target.dataset.confirm : null;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('sfSearch');
    var cards = document.querySelectorAll('.sf-card');
    var none = document.getElementById('sfNone');
    var count = document.getElementById('sfCount');
    var state = { status: '', cat: '' };

    function apply() {
        var q = search.value.toLowerCase().trim();
        var shown = 0;

        cards.forEach(function (c) {
            var ok = (c.dataset.search || '').includes(q)
                && (!state.status || c.dataset.status === state.status)
                && (!state.cat || c.dataset.cat === state.cat);
            c.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });

        count.textContent = shown + ' fasilitas';
        if (none) none.style.display = cards.length > 0 && shown === 0 ? '' : 'none';
    }

    function bindChips(wrapId, key, attr) {
        var wrap = document.getElementById(wrapId);
        if (!wrap) return;
        wrap.addEventListener('click', function (e) {
            var btn = e.target.closest('.sf-chip-btn');
            if (!btn) return;
            wrap.querySelectorAll('.sf-chip-btn').forEach(function (b) { b.classList.remove('on'); });
            btn.classList.add('on');
            state[key] = btn.dataset[attr] || '';
            apply();
        });
    }

    bindChips('sfStatusChips', 'status', 'status');
    bindChips('sfCatChips', 'cat', 'cat');
    search.addEventListener('input', apply);

    var reset = document.getElementById('sfReset');
    if (reset) reset.addEventListener('click', function () {
        search.value = '';
        state = { status: '', cat: '' };
        document.querySelectorAll('.sf-chips').forEach(function (w) {
            w.querySelectorAll('.sf-chip-btn').forEach(function (b, i) { b.classList.toggle('on', i === 0); });
        });
        apply();
    });
});
</script>

@endsection

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    $isPetugas = $user->role === 'petugas';
@endphp

@if($isAdmin || $isPetugas)
    {{-- Admin & petugas: tampilan lama dipertahankan apa adanya --}}
    @include('partials.dashboard-admin')
@else

@php
    /*
    | Dashboard mahasiswa & organisasi.
    | Fokus: apa yang perlu dilakukan sekarang, bukan statistik.
    */
    $firstName = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->first();

    $mine = fn () => \App\Models\Borrowing::with('facility')->where('user_id', $user->id);

    $upcoming = $mine()
        ->whereIn('status', ['disetujui', 'sedang_digunakan'])
        ->where('end_at', '>=', now())
        ->orderBy('start_at')
        ->take(3)
        ->get();

    $pendingCount = $mine()->where('status', 'menunggu')->count();

    $rejected = $mine()
        ->where('status', 'ditolak')
        ->where('updated_at', '>=', now()->subDays(7))
        ->latest('updated_at')
        ->take(2)
        ->get();

    $quick = \App\Models\Facility::where('status', 'tersedia')->orderBy('name')->take(4)->get();

    $recent = $recentBorrowings->take(5);

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

    $statusInfo = [
        'menunggu'         => ['Menunggu', 'sf-st-wait'],
        'disetujui'        => ['Disetujui', 'sf-st-ok'],
        'sedang_digunakan' => ['Sedang digunakan', 'sf-st-ok'],
        'selesai'          => ['Selesai', 'sf-st-done'],
        'ditolak'          => ['Ditolak', 'sf-st-no'],
        'dibatalkan'       => ['Dibatalkan', 'sf-st-done'],
    ];

    $dayLabel = function ($b) {
        if ($b->start_at <= now()) return 'Sedang berlangsung';
        $diff = (int) now()->startOfDay()->diffInDays($b->start_at->copy()->startOfDay(), false);
        return match (true) {
            $diff <= 0 => 'Hari ini',
            $diff === 1 => 'Besok',
            default => $diff . ' hari lagi',
        };
    };

    $fmt = fn ($d, $f) => $d->copy()->locale('id')->translatedFormat($f);
    $next = $upcoming->first();
@endphp

<style>
    .sf-home { display: grid; gap: 22px; max-width: 1180px; }
    .sf-home h2, .sf-home h3, .sf-home h4 { margin: 0; }

    .sf-top { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .sf-top h2 { font-size: 1.6rem; letter-spacing: -.02em; }
    .sf-top p { margin: 4px 0 0; color: var(--muted); }

    .sf-cta {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 20px; border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--blue-600), var(--blue-700));
        color: #fff; font-weight: 700; text-decoration: none;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .28);
        transition: transform .2s var(--ease), box-shadow .2s var(--ease);
    }
    .sf-cta:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 12px 26px rgba(37, 99, 235, .36); }
    .sf-cta svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2.4; stroke-linecap: round; }

    .sf-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 18px; }

    /* Kartu jadwal berikutnya */
    .sf-next {
        position: relative; overflow: hidden; border-radius: var(--radius-lg);
        padding: 26px; color: #fff;
        background: radial-gradient(420px 220px at 100% 0%, rgba(96, 165, 250, .35), transparent 70%),
                    linear-gradient(135deg, var(--navy-900), var(--navy-700));
        box-shadow: var(--shadow-md);
        display: flex; flex-direction: column; gap: 14px; min-height: 230px;
    }
    .sf-next-tag { font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: rgba(191, 219, 254, .9); }
    .sf-next h3 { color: #fff; font-size: 1.55rem; letter-spacing: -.02em; }
    .sf-next-meta { display: grid; gap: 6px; color: rgba(219, 234, 254, .92); font-size: .93rem; }
    .sf-chip {
        align-self: flex-start; padding: 5px 12px; border-radius: 999px;
        background: rgba(255, 255, 255, .16); border: 1px solid rgba(255, 255, 255, .25);
        font-size: .78rem; font-weight: 700;
    }
    .sf-next-foot { margin-top: auto; display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }
    .sf-next-link { color: #fff; font-weight: 600; text-decoration: none; border-bottom: 1px solid rgba(255, 255, 255, .5); }
    .sf-next-link:hover { color: #fff; border-color: #fff; }
    .sf-more { display: grid; gap: 4px; font-size: .84rem; color: rgba(219, 234, 254, .85); padding-top: 10px; border-top: 1px solid rgba(255, 255, 255, .18); }

    .sf-next.is-empty {
        background: var(--surface); color: var(--ink); border: 1px dashed #c3d3ea; box-shadow: none;
        align-items: flex-start; justify-content: center;
    }
    .sf-next.is-empty h3 { color: var(--navy-900); font-size: 1.25rem; }
    .sf-next.is-empty p { margin: 0; color: var(--muted); max-width: 420px; }

    /* Perlu perhatian */
    .sf-box { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 20px; box-shadow: var(--shadow-xs); }
    .sf-box h4 { font-size: 1rem; margin-bottom: 12px; }
    .sf-note {
        display: flex; gap: 12px; align-items: flex-start; padding: 12px 14px;
        border-radius: var(--radius-md); text-decoration: none; color: var(--ink);
        transition: transform .2s var(--ease), box-shadow .2s var(--ease);
    }
    .sf-note + .sf-note { margin-top: 10px; }
    .sf-note:hover { color: var(--ink); transform: translateX(3px); }
    .sf-note b { display: block; font-size: .92rem; }
    .sf-note small { color: var(--ink-soft); }
    .sf-note.wait { background: #fffbeb; border: 1px solid #fde7a2; }
    .sf-note.no { background: #fef2f2; border: 1px solid #fbcaca; }
    .sf-note.fine { background: #ecfdf3; border: 1px solid #b8ebcd; }
    .sf-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 6px; flex: none; }
    .wait .sf-dot { background: #f59e0b; }
    .no .sf-dot { background: #ef4444; }
    .fine .sf-dot { background: #22c55e; }

    /* Pinjam cepat */
    .sf-sec-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .sf-sec-head h3 { font-size: 1.15rem; }
    .sf-sec-head a { font-weight: 600; text-decoration: none; font-size: .9rem; }
    .sf-quick { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 14px; }
    .sf-fac {
        display: flex; gap: 14px; align-items: center; padding: 12px;
        background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg);
        transition: transform .22s var(--ease), box-shadow .22s var(--ease), border-color .22s;
    }
    .sf-fac:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: #bcd2f5; }
    .sf-thumb { width: 64px; height: 64px; border-radius: 12px; flex: none; background: var(--blue-50); overflow: hidden; display: grid; place-items: center; color: var(--blue-500); }
    .sf-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .sf-thumb svg { width: 26px; height: 26px; stroke: currentColor; fill: none; stroke-width: 1.8; }
    .sf-fac-info { min-width: 0; flex: 1; }
    .sf-fac-info b { display: block; font-size: .94rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sf-fac-info small { color: var(--muted); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sf-pinjam {
        padding: 7px 14px; border-radius: 10px; font-weight: 700; font-size: .84rem; text-decoration: none;
        background: var(--blue-50); color: var(--blue-700); border: 1px solid var(--blue-100); flex: none;
        transition: background .2s, color .2s;
    }
    .sf-pinjam:hover { background: var(--blue-600); color: #fff; }

    /* Riwayat singkat */
    .sf-list { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg); overflow: hidden; }
    .sf-row {
        display: flex; align-items: center; gap: 14px; padding: 14px 18px; text-decoration: none; color: var(--ink);
        transition: background .18s;
    }
    .sf-row + .sf-row { border-top: 1px solid var(--line); }
    .sf-row:hover { background: var(--blue-50); color: var(--ink); }
    .sf-row-main { flex: 1; min-width: 0; }
    .sf-row-main b { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sf-row-main small { color: var(--muted); }
    .sf-st { padding: 4px 11px; border-radius: 999px; font-size: .76rem; font-weight: 700; white-space: nowrap; }
    .sf-st-wait { background: #fffbeb; color: #92400e; }
    .sf-st-ok { background: #ecfdf3; color: #166534; }
    .sf-st-no { background: #fef2f2; color: #991b1b; }
    .sf-st-done { background: #eef2f7; color: #475569; }
    .sf-empty { padding: 28px; text-align: center; color: var(--muted); }

    /* Animasi masuk bertahap */
    .sf-home > * { animation: sfUp .5s var(--ease) backwards; }
    .sf-home > *:nth-child(2) { animation-delay: .06s; }
    .sf-home > *:nth-child(3) { animation-delay: .12s; }
    .sf-home > *:nth-child(4) { animation-delay: .18s; }
    @keyframes sfUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) { .sf-home > * { animation: none; } }

    @media (max-width: 900px) { .sf-grid { grid-template-columns: 1fr; } }
    @media (max-width: 560px) {
        .sf-cta { width: 100%; justify-content: center; }
        .sf-row { flex-wrap: wrap; }
    }
</style>

<div class="sf-home">

    {{-- SAPAAN + TOMBOL UTAMA --}}
    <section class="sf-top">
        <div>
            <h2>Halo, {{ $firstName }}</h2>
            <p><span data-today>{{ $todayText ?? now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</span> · Mau pinjam apa hari ini?</p>
        </div>
        <a href="/borrowings/create" class="sf-cta">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Ajukan Peminjaman
        </a>
    </section>

    {{-- JADWAL BERIKUTNYA + PERLU PERHATIAN --}}
    <section class="sf-grid">

        @if($next)
            <div class="sf-next">
                <span class="sf-next-tag">Peminjaman berikutnya</span>
                <h3>{{ $next->facility->name ?? 'Fasilitas' }}</h3>
                <span class="sf-chip">{{ $dayLabel($next) }}</span>
                <div class="sf-next-meta">
                    <span>{{ $fmt($next->start_at, 'l, d F') }} · {{ $next->start_at->format('H:i') }}–{{ $next->end_at->format('H:i') }} WIB</span>
                    <span>{{ $next->facility->location ?? '-' }} · {{ $next->units }} unit</span>
                    <span>{{ $next->purpose }}</span>
                </div>
                <div class="sf-next-foot">
                    <a class="sf-next-link" href="{{ route('borrowings.show', $next->id) }}">Lihat detail</a>
                </div>

                @if($upcoming->count() > 1)
                    <div class="sf-more">
                        @foreach($upcoming->skip(1) as $u)
                            <span>Selanjutnya: {{ $u->facility->name ?? 'Fasilitas' }} · {{ $fmt($u->start_at, 'd M, H:i') }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="sf-next is-empty">
                <h3>Belum ada jadwal peminjaman</h3>
                <p>Cari fasilitas yang kamu butuhkan, pilih waktunya, lalu kirim pengajuan. Jadwal yang disetujui akan muncul di sini.</p>
                <a href="/facilities" class="sf-cta">Cari fasilitas</a>
            </div>
        @endif

        <div class="sf-box">
            <h4>Perlu perhatianmu</h4>

            @if($pendingCount > 0)
                <a href="/borrowings" class="sf-note wait">
                    <span class="sf-dot"></span>
                    <span><b>{{ $pendingCount }} pengajuan menunggu</b><small>Admin belum memproses. Kamu masih bisa membatalkannya.</small></span>
                </a>
            @endif

            @foreach($rejected as $r)
                <a href="{{ route('borrowings.show', $r->id) }}" class="sf-note no">
                    <span class="sf-dot"></span>
                    <span>
                        <b>{{ $r->facility->name ?? 'Pengajuan' }} ditolak</b>
                        <small>{{ $r->reject_reason ?: 'Lihat detail untuk alasannya.' }}</small>
                    </span>
                </a>
            @endforeach

            @if($pendingCount === 0 && $rejected->isEmpty())
                <div class="sf-note fine">
                    <span class="sf-dot"></span>
                    <span><b>Semua beres</b><small>Tidak ada pengajuan yang perlu ditindaklanjuti.</small></span>
                </div>
            @endif
        </div>
    </section>

    {{-- PINJAM CEPAT --}}
    <section>
        <div class="sf-sec-head">
            <h3>Siap dipinjam</h3>
            <a href="/facilities">Lihat semua fasilitas →</a>
        </div>

        @if($quick->count())
            <div class="sf-quick">
                @foreach($quick as $f)
                    @php $img = $facImg($f); @endphp
                    <div class="sf-fac">
                        <div class="sf-thumb">
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $f->name }}" loading="lazy" onerror="this.remove()">
                            @else
                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="1.6"/><path d="m21 15-5-5L5 21"/></svg>
                            @endif
                        </div>
                        <div class="sf-fac-info">
                            <b>{{ $f->name }}</b>
                            <small>{{ $f->location }} · {{ $f->units }} unit</small>
                        </div>
                        <a class="sf-pinjam" href="/borrowings/create?facility={{ $f->id }}">Pinjam</a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sf-list"><div class="sf-empty">Belum ada fasilitas yang tersedia saat ini.</div></div>
        @endif
    </section>

    {{-- RIWAYAT SINGKAT --}}
    <section>
        <div class="sf-sec-head">
            <h3>Pengajuan terakhir</h3>
            <a href="/borrowings">Semua peminjaman →</a>
        </div>

        <div class="sf-list">
            @forelse($recent as $b)
                @php
                    $info = $statusInfo[$b->status] ?? [ucfirst($b->status), 'sf-st-done'];
                    $label = $info[0];
                    $cls = $info[1];
                @endphp
                <a class="sf-row" href="{{ route('borrowings.show', $b->id) }}">
                    <div class="sf-row-main">
                        <b>{{ $b->facility->name ?? 'Fasilitas' }}</b>
                        <small>{{ $fmt($b->start_at, 'D, d M · H:i') }} · {{ $b->purpose }}</small>
                    </div>
                    <span class="sf-st {{ $cls }}">{{ $label }}</span>
                </a>
            @empty
                <div class="sf-empty">Belum ada pengajuan. Mulai dengan menekan “Ajukan Peminjaman”.</div>
            @endforelse
        </div>
    </section>

</div>

@endif
@endsection

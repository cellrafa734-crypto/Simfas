@php
    /*
    |--------------------------------------------------------------------------
    | Data tampilan layout (hanya membaca data; tidak mengubah logika aplikasi)
    |--------------------------------------------------------------------------
    */
    $user = auth()->user();

    // Judul halaman: pakai @section('title') bila view menyediakannya,
    // jika tidak, tentukan otomatis dari URL (semua URL lama tetap sama).
    $pageTitle = trim($__env->yieldContent('title'));
    if ($pageTitle === '') {
        $pageTitle = match (true) {
            request()->is('dashboard')          => 'Dashboard',
            request()->is('facilities/create')  => 'Tambah Fasilitas',
            request()->is('facilities/*/edit')  => 'Ubah Fasilitas',
            request()->is('facilities*')        => 'Fasilitas',
            request()->is('borrowings/create')  => 'Ajukan Peminjaman',
            request()->is('borrowings*')        => 'Peminjaman',
            request()->is('saw*')               => 'Prioritas SAW',
            default                             => 'SIMFAS Kampus',
        };
    }

    if ($user) {
        $roleLabel = [
            'admin'      => 'Administrator',
            'mahasiswa'  => 'Mahasiswa',
            'organisasi' => 'Organisasi',
            'petugas'    => 'Petugas',
        ][$user->role] ?? ucfirst((string) $user->role);

        $initials = collect(preg_split('/\s+/', trim((string) $user->name)))
            ->filter()
            ->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('') ?: 'U';
    }

    // Teks awal tanggal (diganti otomatis oleh JavaScript sesuai zona waktu perangkat).
    $todayText = now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y');

    // Penentu menu aktif.
    $cur = fn (string ...$patterns) => request()->is(...$patterns);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a1f44">
    <title>{{ $pageTitle }} — SIMFAS Kampus</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* =====================================================================
           1. DESIGN TOKENS
           Satu sumber untuk warna, radius, bayangan, dan kecepatan animasi.
           Radius: kontainer (card, modal, tabel) = 16px | kontrol (input, tombol) = 12px
           ===================================================================== */
        :root {
            --navy-950: #183e60;
            --navy-900: #183e60;
            --navy-800: #236da9;
            --navy-700: #368bd0;

            --blue-700: #236da9;
            --blue-600: #368bd0;
            --blue-500: #368bd0;
            --blue-400: #78b5e8;
            --blue-100: #d9edfc;
            --blue-50:  #eaf5ff;

            --ink:      #183e60;
            --ink-soft: #435b70;
            --muted:    #7890a8;
            --line:     #dceaf5;
            --surface:  #ffffff;
            --bg:       #f4f9fd;

            --radius-lg: 16px;   /* rounded-2xl : card, modal, tabel          */
            --radius-md: 12px;   /* rounded-xl  : input, tombol, alert, menu  */
            --radius-sm: 10px;   /* rounded-lg  : tombol kecil, pagination    */

            --shadow-xs: 0 1px 2px rgba(10, 31, 68, .05);
            --shadow-sm: 0 1px 2px rgba(10, 31, 68, .04), 0 6px 20px rgba(10, 31, 68, .06);
            --shadow-md: 0 2px 4px rgba(10, 31, 68, .05), 0 12px 30px rgba(10, 31, 68, .10);
            --shadow-lg: 0 24px 60px rgba(10, 31, 68, .22);

            --ease: cubic-bezier(.22, .8, .24, 1);

            --sidebar-w: 272px;
            --header-h: 74px;

            /* Jembatan ke Bootstrap 5 agar komponen bawaan ikut berwarna biru */
            --bs-body-font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            --bs-body-color: var(--ink);
            --bs-body-bg: var(--bg);
            --bs-secondary-color: var(--muted);
            --bs-primary: #2563eb;
            --bs-primary-rgb: 37, 99, 235;
            --bs-link-color: #2563eb;
            --bs-link-color-rgb: 37, 99, 235;
            --bs-link-hover-color: #1d4ed8;
            --bs-link-hover-color-rgb: 29, 78, 216;
            --bs-border-color: var(--line);
            --bs-border-radius: 12px;
            --bs-border-radius-sm: 10px;
            --bs-border-radius-lg: 16px;
            --bs-border-radius-xl: 20px;
        }

        /* =====================================================================
           2. DASAR
           ===================================================================== */
        *, *::before, *::after { box-sizing: border-box; }

        html { -webkit-text-size-adjust: 100%; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--bs-body-font-family);
            font-size: .9375rem;
            color: var(--ink);
            background:
                radial-gradient(900px 420px at 100% 0%, rgba(59, 130, 246, .08), transparent 70%),
                radial-gradient(700px 380px at 0% 100%, rgba(14, 42, 92, .05), transparent 70%),
                var(--bg);
            -webkit-font-smoothing: antialiased;
        }

        body.sidebar-open { overflow: hidden; }

        :where(.page-content) h1,
        :where(.page-content) h2,
        :where(.page-content) h3,
        :where(.page-content) h4,
        :where(.page-content) h5,
        :where(.page-content) h6 {
            color: var(--navy-900);
            font-weight: 700;
            letter-spacing: -.01em;
        }

        a:focus-visible,
        button:focus-visible {
            outline: 2px solid var(--blue-500);
            outline-offset: 2px;
        }

        .skip-link {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 2000;
            padding: 9px 16px;
            border-radius: var(--radius-md);
            background: var(--navy-900);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            transform: translateY(-160%);
            transition: transform .25s var(--ease);
        }
        .skip-link:focus { transform: translateY(0); color: #fff; }

        /* =====================================================================
           3. SIDEBAR (biru navy)
           ===================================================================== */
        .simfas-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1050;
            width: var(--sidebar-w);
            height: 100vh;
            height: 100dvh;
            display: flex;
            flex-direction: column;
            padding: 22px 16px max(18px, env(safe-area-inset-bottom));
            color: #435b70;
            background:
                radial-gradient(120% 55% at 0% 0%, rgba(59, 130, 246, .30), transparent 60%),
                linear-gradient(180deg, var(--navy-900) 0%, var(--navy-950) 100%);
            box-shadow: 6px 0 30px rgba(6, 20, 46, .18);
            transition: transform .38s var(--ease), visibility 0s linear 0s;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 2px 6px 20px;
            margin-bottom: 4px;
            border-bottom: 1px solid #e3edf5;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
            color: #fff;
            text-decoration: none;
        }
        .brand-link:hover { color: #fff; }

        .brand-logo {
            width: 44px;
            height: 44px;
            flex: none;
            display: grid;
            place-items: center;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--blue-400), var(--blue-700));
            box-shadow: 0 8px 20px rgba(37, 99, 235, .45), inset 0 1px 0 rgba(255, 255, 255, .35);
            transition: transform .3s var(--ease);
        }
        .brand-link:hover .brand-logo { transform: rotate(-6deg) scale(1.05); }

        .brand-logo svg {
            width: 24px;
            height: 24px;
            fill: none;
            stroke: #fff;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .brand-name {
            display: block;
            white-space: nowrap;
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: .01em;
            line-height: 1.15;
        }
        .brand-sub {
            display: block;
            margin-top: 4px;
            font-size: .625rem;
            font-weight: 600;
            letter-spacing: .16em;
            white-space: nowrap;
            text-transform: uppercase;
            color: #7890a8;
        }

        .sidebar-close {
            display: none;
            width: 36px;
            height: 36px;
            flex: none;
            place-items: center;
            border: 0;
            border-radius: var(--radius-sm);
            background: #eaf5ff;
            color: #fff;
            cursor: pointer;
            transition: background-color .2s;
        }
        .sidebar-close:hover { background: rgba(255, 255, 255, .16); }
        .sidebar-close svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; }

        .sidebar-nav {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            margin: 0 -6px;
            padding: 0 6px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, .18) transparent;
        }

        .nav-caption {
            margin: 22px 12px 10px;
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: #8aa0b5;
        }
        .sidebar-brand + .sidebar-nav .nav-caption:first-child { margin-top: 18px; }

        .side-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            margin-bottom: 6px;
            padding: 11px 14px;
            border: 0;
            border-radius: var(--radius-md);
            background: transparent;
            color: #526b82;
            font-family: inherit;
            font-size: .875rem;
            font-weight: 600;
            line-height: 1.2;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
            transition: background-color .25s var(--ease), color .25s, transform .25s var(--ease), box-shadow .25s;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex: none;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform .25s var(--ease);
        }

        .side-link:hover {
            color: #fff;
            background: #eaf5ff;
            transform: translateX(3px);
        }
        .side-link:hover .nav-icon { transform: scale(1.12); }

        /* Menu aktif: latar biru + penanda di tepi kiri sidebar */
        .side-link.active,
        .side-link.active:hover {
            color: #fff;
            background: linear-gradient(100deg, #368bd0, #236da9);
            box-shadow: 0 10px 22px rgba(37, 99, 235, .38), inset 0 1px 0 rgba(255, 255, 255, .22);
            transform: none;
        }
        .side-link.active::before {
            content: "";
            position: absolute;
            left: -16px;
            top: 22%;
            width: 4px;
            height: 56%;
            border-radius: 0 6px 6px 0;
            background: var(--blue-400);
            box-shadow: 0 0 12px rgba(96, 165, 250, .8);
        }

        .sidebar-footer {
            margin-top: 12px;
            padding-top: 14px;
            border-top: 1px solid #e3edf5;
        }

        .sidebar-user {
            display: none;
            align-items: center;
            gap: 11px;
            margin-bottom: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            background: #f4f9fd;
        }
        .sidebar-user .user-avatar { width: 36px; height: 36px; font-size: .8rem; box-shadow: 0 0 0 2px rgba(255, 255, 255, .22); }
        .sidebar-user-name { font-size: .8rem; font-weight: 700; color: #fff; line-height: 1.2; overflow-wrap: anywhere; }
        .sidebar-user-role { font-size: .7rem; color: #7890a8; margin-top: 2px; }

        .side-link.logout { margin-bottom: 0; color: rgba(214, 227, 250, .85); }
        .side-link.logout:hover { background: rgba(255, 255, 255, .1); }
        .side-link.logout:hover .nav-icon { transform: translateX(3px); }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(6, 20, 46, .55);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s var(--ease), visibility 0s linear .3s;
        }
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
            transition-delay: 0s;
        }

        /* =====================================================================
           4. AREA UTAMA + HEADER
           ===================================================================== */
        .simfas-main {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
        }
        .simfas-main.has-sidebar { margin-left: var(--sidebar-w); }

        .top-header {
            position: sticky;
            top: 0;
            z-index: 900;
            min-height: var(--header-h);
            padding: 0 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: rgba(255, 255, 255, .82);
            backdrop-filter: saturate(160%) blur(16px);
            -webkit-backdrop-filter: saturate(160%) blur(16px);
            border-bottom: 1px solid var(--line);
            box-shadow: var(--shadow-xs);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .mobile-menu-btn {
            display: none;
            width: 42px;
            height: 42px;
            flex: none;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            background: #fff;
            color: var(--navy-800);
            box-shadow: var(--shadow-xs);
            cursor: pointer;
            transition: background-color .2s, transform .2s var(--ease), border-color .2s;
        }
        .mobile-menu-btn:hover { background: var(--blue-50); border-color: #bcd3f7; }
        .mobile-menu-btn:active { transform: scale(.94); }
        .mobile-menu-btn svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; }

        .header-title-wrap { min-width: 0; }

        .header-title {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -.02em;
            line-height: 1.2;
            color: var(--navy-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .title-date {
            display: none;
            margin-top: 2px;
            font-size: .72rem;
            font-weight: 500;
            color: var(--muted);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: none;
        }

        .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            background: #fff;
            color: var(--ink-soft);
            font-size: .8rem;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: var(--shadow-xs);
        }
        .date-chip svg { width: 16px; height: 16px; stroke: var(--blue-600); fill: none; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }

        .header-divider { width: 1px; height: 30px; background: var(--line); }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            flex: none;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .02em;
            background: linear-gradient(135deg, var(--navy-700), var(--blue-500));
            box-shadow: 0 6px 14px rgba(37, 99, 235, .28), 0 0 0 3px #fff;
        }

        .profile-text { min-width: 0; line-height: 1.2; }
        .profile-name {
            max-width: 190px;
            font-size: .85rem;
            font-weight: 700;
            color: var(--navy-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-role {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 9px;
            border-radius: 999px;
            background: var(--blue-100);
            color: var(--blue-700);
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .page-content {
            flex: 1;
            width: 100%;
            max-width: 1640px;
            margin: 0 auto;
            padding: 30px 36px 56px;
            /* "backwards": transform dilepas setelah animasi selesai */
            animation: pageEnter .55s var(--ease) backwards;
            transition: opacity .18s ease, transform .18s ease;
        }

        .simfas-main:not(.has-sidebar) .page-content { padding-top: 40px; }

        @keyframes pageEnter {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Transisi keluar saat berpindah halaman */
        body.is-leaving .page-content {
            opacity: 0;
            transform: translateY(-6px);
        }

        /* =====================================================================
           5. KOMPONEN GLOBAL (otomatis dipakai semua halaman)
           ===================================================================== */

        /* ---- Card ---- */
        .card {
            --bs-card-border-radius: var(--radius-lg);
            --bs-card-inner-border-radius: calc(var(--radius-lg) - 1px);
            --bs-card-border-color: var(--line);
            --bs-card-bg: var(--surface);
            --bs-card-cap-bg: transparent;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .3s var(--ease), border-color .3s;
        }
        .card:hover { box-shadow: var(--shadow-md); border-color: #d3dff0; }
        .card:has(> .table-responsive) { overflow: hidden; }
        .card-body { padding: 1.5rem; }

        /* ---- Tombol + efek kilau (shimmer) ---- */
        .btn {
            --bs-btn-border-radius: var(--radius-md);
            position: relative;
            overflow: hidden;
            isolation: isolate;
            font-weight: 600;
            transition: transform .22s var(--ease), box-shadow .22s var(--ease),
                        background-color .22s, border-color .22s, color .22s, filter .22s;
        }
        .btn-sm { --bs-btn-border-radius: var(--radius-sm); }

        .btn::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 55%;
            z-index: 1;
            pointer-events: none;
            opacity: 0;
            transform: translateX(-130%) skewX(-22deg);
            background: linear-gradient(100deg, transparent 0%, rgba(255, 255, 255, .45) 50%, transparent 100%);
            background: linear-gradient(100deg, transparent 0%, color-mix(in srgb, currentColor 40%, transparent) 50%, transparent 100%);
        }
        .btn:hover::after,
        .btn:focus-visible::after {
            opacity: 1;
            animation: shimmer .8s var(--ease) forwards;
        }
        @keyframes shimmer {
            from { transform: translateX(-130%) skewX(-22deg); }
            to   { transform: translateX(300%)  skewX(-22deg); }
        }

        .btn:hover:not(:disabled):not(.disabled) { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(10, 31, 68, .16); }
        .btn:active:not(:disabled):not(.disabled) { transform: translateY(0); box-shadow: none; }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--blue-600);
            --bs-btn-border-color: var(--blue-600);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--blue-700);
            --bs-btn-hover-border-color: var(--blue-700);
            --bs-btn-active-bg: var(--blue-700);
            --bs-btn-active-border-color: var(--blue-700);
            --bs-btn-focus-shadow-rgb: 59, 130, 246;
            background-image: linear-gradient(135deg, var(--blue-500), var(--blue-700));
            border-color: transparent;
            box-shadow: 0 6px 16px rgba(37, 99, 235, .28);
        }
        .btn-primary:hover:not(:disabled):not(.disabled) { box-shadow: 0 10px 22px rgba(37, 99, 235, .38); filter: brightness(1.06); }

        .btn-outline-secondary {
            --bs-btn-color: var(--ink-soft);
            --bs-btn-border-color: #cdd9ea;
            --bs-btn-hover-color: var(--blue-700);
            --bs-btn-hover-bg: var(--blue-50);
            --bs-btn-hover-border-color: #9fc0f2;
            --bs-btn-active-color: var(--blue-700);
            --bs-btn-active-bg: var(--blue-100);
            --bs-btn-active-border-color: #9fc0f2;
        }

        /* ---- Form ---- */
        :where(.page-content) label {
            display: inline-block;
            margin-bottom: .4rem;
            font-size: .82rem;
            font-weight: 600;
            color: var(--ink-soft);
        }

        .form-control,
        .form-select {
            padding: .62rem .85rem;
            border: 1px solid #d3deee;
            border-radius: var(--radius-md);
            background-color: #fbfdff;
            color: var(--ink);
            transition: border-color .22s, box-shadow .22s, background-color .22s;
        }
        .form-control:hover,
        .form-select:hover { border-color: #a9c3ea; }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--blue-500);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, .16);
        }
        .form-control-sm,
        .form-select-sm { padding: .4rem .65rem; border-radius: var(--radius-sm); }
        .form-control::placeholder { color: #9aabc4; }

        /* ---- Alert ---- */
        .alert {
            --bs-alert-border-radius: var(--radius-md);
            border-radius: var(--radius-md);
            border-width: 1px;
            box-shadow: var(--shadow-xs);
            animation: alertIn .45s var(--ease) backwards;
        }
        .alert-success { background: #ecfdf3; border-color: #b8ebcd; color: #14532d; }
        .alert-danger  { background: #fef2f2; border-color: #fbcaca; color: #991b1b; }
        .alert-warning { background: #fffbeb; border-color: #fde7a2; color: #854d0e; }
        .alert-info    { background: var(--blue-50); border-color: #bfdbfe; color: #1e3a8a; }
        @keyframes alertIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ---- Badge ---- */
        .badge { border-radius: 999px; padding: .45em .8em; font-weight: 600; letter-spacing: .01em; }
        .badge.bg-secondary { background-color: #e6efff !important; color: #1e40af !important; }

        /* ---- Modal & dropdown (disiapkan untuk tahap berikutnya) ---- */
        .modal-content {
            border: 0;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
        }
        .modal { --bs-modal-border-radius: var(--radius-lg); }
        .modal-backdrop { --bs-backdrop-bg: var(--navy-900); }
        .dropdown-menu {
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }

        /* ---- Tabel + hover lembut ---- */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--ink);
            --bs-table-border-color: var(--line);
            --bs-table-hover-bg: #f0f6ff;
            --bs-table-hover-color: var(--ink);
            margin-bottom: 0;
            vertical-align: middle;
        }
        .table > :not(caption) > * > * {
            padding: .9rem 1.1rem;
            transition: box-shadow .25s ease, color .25s ease;
        }
        .table thead th {
            background: #f6f9fe;
            border-bottom: 1px solid var(--line);
            color: #55698b;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .table tbody tr:last-child > * { border-bottom-width: 0; }
        /* Kolom terakhir (tombol aksi) selalu satu baris agar tidak terpotong/bertumpuk */
        .table tbody td:last-child { white-space: nowrap; }
        .table .form-control-sm.w-auto { width: 150px !important; vertical-align: middle; }
        /* Hover baris aktif di SEMUA tabel .table (tanpa perlu class table-hover) */
        .table > tbody > tr:hover > * {
            --bs-table-color-state: var(--bs-table-hover-color);
            --bs-table-bg-state: var(--bs-table-hover-bg);
        }
        .table-sm > :not(caption) > * > * { padding: .6rem .85rem; }

        .table-responsive {
            border-radius: var(--radius-lg);
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-x: contain;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: #c3d3ea transparent;
        }
        .table-responsive::-webkit-scrollbar { height: 8px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #c3d3ea; border-radius: 99px; }
        .table-responsive::-webkit-scrollbar-track { background: transparent; }

        .scroll-hint {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 7px 12px;
            background: var(--blue-50);
            border-bottom: 1px solid var(--line);
            color: var(--blue-700);
            font-size: .72rem;
            font-weight: 600;
        }

        /* ---- Pagination ----
           Project ini memakai view pagination Tailwind bawaan Laravel, padahal
           yang dimuat hanya Bootstrap. Blok ini merapikannya tanpa mengubah
           AppServiceProvider. */
        .page-content nav[role="navigation"] { margin-top: 1.25rem; }
        .page-content nav[role="navigation"] > div:first-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .page-content nav[role="navigation"] > div:last-child { display: none; }
        .page-content nav[role="navigation"] p { margin: 0; color: var(--muted); font-size: .85rem; }
        .page-content nav[role="navigation"] svg { width: 1.1rem; height: 1.1rem; }
        .page-content nav[role="navigation"] > div:last-child > div:last-child > span { display: inline-flex; flex-wrap: wrap; gap: 4px; }

        .page-content nav[role="navigation"] a,
        .page-content nav[role="navigation"] [aria-disabled="true"] > span,
        .page-content nav[role="navigation"] [aria-current="page"] > span,
        .page-content nav[role="navigation"] > div:first-child > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 .8rem;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            background: #fff;
            color: var(--ink-soft);
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color .2s, border-color .2s, color .2s, transform .2s var(--ease);
        }
        .page-content nav[role="navigation"] a:hover {
            background: var(--blue-50);
            border-color: #b4cdf4;
            color: var(--blue-700);
            transform: translateY(-1px);
        }
        .page-content nav[role="navigation"] [aria-current="page"] > span {
            border-color: transparent;
            background: linear-gradient(135deg, var(--blue-500), var(--blue-700));
            color: #fff;
            box-shadow: 0 6px 14px rgba(37, 99, 235, .3);
        }
        .page-content nav[role="navigation"] [aria-disabled="true"][aria-label] > span,
        .page-content nav[role="navigation"] > div:first-child > span { opacity: .5; cursor: not-allowed; }

        /* Cadangan bila nanti beralih ke Paginator::useBootstrapFive() */
        .pagination { --bs-pagination-border-radius: var(--radius-sm); gap: 4px; }
        .page-link { border-radius: var(--radius-sm) !important; border-color: var(--line); color: var(--ink-soft); font-weight: 600; }
        .page-item.active .page-link { background: var(--blue-600); border-color: var(--blue-600); color: #fff; }

        /* =====================================================================
           6. RESPONSIF
           ===================================================================== */
        @media (min-width: 640px) {
            .page-content nav[role="navigation"] > div:first-child { display: none; }
            .page-content nav[role="navigation"] > div:last-child {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
            }
        }

        /* Tablet & HP: sidebar menjadi drawer */
        @media (max-width: 991.98px) {
            .simfas-sidebar {
                width: min(300px, 86vw);
                transform: translateX(-105%);
                visibility: hidden;
                /* visibility baru disembunyikan setelah animasi geser selesai */
                transition: transform .38s var(--ease), visibility 0s linear .38s;
            }
            .simfas-sidebar.show {
                transform: translateX(0);
                visibility: visible;
                transition-delay: 0s;
            }

            .sidebar-close { display: grid; }
            .sidebar-user { display: flex; }
            .simfas-main.has-sidebar { margin-left: 0; }
            .mobile-menu-btn { display: grid; }

            .top-header { padding: 0 20px; }
            .page-content { padding: 24px 20px 44px; }
        }

        @media (max-width: 767.98px) {
            :root { --header-h: 68px; }

            .date-chip,
            .header-divider { display: none; }
            .title-date { display: block; }
            .header-title { font-size: 1.1rem; }
            .profile-name { max-width: 120px; }

            /* Tabel: scroll horizontal mulus, kolom tidak menyempit/terpotong */
            .table { min-width: 720px; }
            .table > :not(caption) > * > * { padding: .8rem .95rem; }
            .table tbody td { min-width: 130px; }
            .table tbody td:last-child { min-width: 0; }
            .table-sm { min-width: 0; }
            .table-sm tbody td { min-width: 0; }
            .scroll-hint.is-needed { display: flex; }
            .scroll-hint.is-done { display: none; }
        }

        @media (max-width: 575.98px) {
            .top-header { padding: 0 14px; gap: 10px; }
            .page-content { padding: 20px 14px 40px; }
            .card-body { padding: 1.15rem; }
            .header-right { gap: 10px; }
            .header-profile { gap: 8px; }
            .user-avatar { width: 38px; height: 38px; }
            .profile-name { max-width: 92px; font-size: .78rem; }
            .profile-role { padding: 2px 7px; font-size: .6rem; }
            .title-date { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        }

        /* Layar sangat kecil (<360px): hanya avatar di header (identitas tetap ada di drawer) */
        @media (max-width: 359.98px) {
            .profile-text { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body>
<a class="skip-link" href="#mainContent">Lewati ke konten utama</a>

@auth
{{-- ===================== SIDEBAR ===================== --}}
<aside class="simfas-sidebar" id="simfasSidebar" aria-label="Navigasi utama">

    <div class="sidebar-brand">
        <a href="/dashboard" class="brand-link" aria-label="SIMFAS Kampus — ke Dashboard">
            <span class="brand-logo" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M3 10l9-6 9 6"/>
                    <path d="M5 10v8M9.7 10v8M14.3 10v8M19 10v8"/>
                    <path d="M3 21h18"/>
                </svg>
            </span>
            <span>
                <span class="brand-name">SIMFAS Kampus</span>
                <span class="brand-sub">Manajemen Fasilitas</span>
            </span>
        </a>
        <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">
            <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Menu">

        <div class="nav-caption">Menu Utama</div>

        <a href="/dashboard"
           class="side-link {{ $cur('dashboard') ? 'active' : '' }}"
           @if($cur('dashboard')) aria-current="page" @endif>
            <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="3" width="8" height="8" rx="2"/>
                <rect x="13" y="3" width="8" height="5" rx="2"/>
                <rect x="13" y="10" width="8" height="11" rx="2"/>
                <rect x="3" y="13" width="8" height="8" rx="2"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="/facilities"
           class="side-link {{ $cur('facilities*') ? 'active' : '' }}"
           @if($cur('facilities*')) aria-current="page" @endif>
            <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 21h18M5 21V7l7-4 7 4v14"/>
                <path d="M9 21v-5h6v5M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
            </svg>
            <span>Fasilitas</span>
        </a>

        @if(in_array($user->role, ['admin', 'mahasiswa', 'organisasi']))
        <a href="/borrowings"
           class="side-link {{ $cur('borrowings*') ? 'active' : '' }}"
           @if($cur('borrowings*')) aria-current="page" @endif>
            <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="4" width="18" height="17" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18M8 14h3M8 17h7"/>
            </svg>
            <span>Peminjaman</span>
        </a>
        @endif

        @if($user->role === 'admin')
        <div class="nav-caption">Administrator</div>

        <a href="/saw"
           class="side-link {{ $cur('saw*') ? 'active' : '' }}"
           @if($cur('saw*')) aria-current="page" @endif>
            <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M4 19V5M4 19h17"/>
                <path d="m7 15 4-4 3 2 6-7"/>
            </svg>
            <span>Prioritas SAW</span>
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        {{-- Identitas pengguna di drawer (hanya tampil di layar kecil) --}}
        <div class="sidebar-user">
            <div class="user-avatar" aria-hidden="true">{{ $initials }}</div>
            <div>
                <div class="sidebar-user-name">{{ $user->name }}</div>
                <div class="sidebar-user-role">{{ $roleLabel }}</div>
            </div>
        </div>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="side-link logout">
                <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10 17l5-5-5-5M15 12H3"/>
                    <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
@endauth

{{-- ===================== AREA UTAMA ===================== --}}
<div class="simfas-main @auth has-sidebar @endauth">

    @auth
    <header class="top-header">
        <div class="header-left">
            <button type="button" class="mobile-menu-btn" id="mobileMenuBtn"
                    aria-label="Buka menu" aria-controls="simfasSidebar" aria-expanded="false">
                <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
            </button>

            <div class="header-title-wrap">
                <h1 class="header-title">{{ $pageTitle }}</h1>
                <div class="title-date" data-today>{{ $todayText }}</div>
            </div>
        </div>

        <div class="header-right">
            <div class="date-chip">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
                <span data-today>{{ $todayText }}</span>
            </div>

            <div class="header-divider" aria-hidden="true"></div>

            <div class="header-profile">
                <div class="user-avatar" aria-hidden="true">{{ $initials }}</div>
                <div class="profile-text">
                    <div class="profile-name" title="{{ $user->name }}">{{ $user->name }}</div>
                    <span class="profile-role">{{ $roleLabel }}</span>
                </div>
            </div>
        </div>
    </header>
    @endauth

    <main class="page-content" id="mainContent">

        @if(session('ok'))
            <div class="alert alert-success" role="alert">{{ session('ok') }}</div>
        @endif

        @if(session('err'))
            <div class="alert alert-danger" role="alert">{{ session('err') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Tanggal saat ini (zona waktu perangkat) ---------- */
    var todayText = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    }).format(new Date());
    document.querySelectorAll('[data-today]').forEach(function (el) {
        el.textContent = todayText;
    });

    /* ---------- Sidebar drawer (layar kecil) ---------- */
    var sidebar = document.getElementById('simfasSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var openBtn = document.getElementById('mobileMenuBtn');
    var closeBtn = document.getElementById('sidebarClose');

    function setSidebar(open) {
        if (!sidebar) return;
        sidebar.classList.toggle('show', open);
        if (overlay) overlay.classList.toggle('show', open);
        document.body.classList.toggle('sidebar-open', open);
        if (openBtn) {
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            openBtn.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        }
        if (open && closeBtn) closeBtn.focus({ preventScroll: true });
        if (!open && openBtn && document.activeElement && sidebar.contains(document.activeElement)) {
            openBtn.focus({ preventScroll: true });
        }
    }

    if (openBtn) openBtn.addEventListener('click', function () {
        setSidebar(!sidebar.classList.contains('show'));
    });
    if (closeBtn) closeBtn.addEventListener('click', function () { setSidebar(false); });
    if (overlay) overlay.addEventListener('click', function () { setSidebar(false); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) setSidebar(false);
    });

    window.matchMedia('(min-width: 992px)').addEventListener('change', function (e) {
        if (e.matches) setSidebar(false);
    });

    /* ---------- Transisi halus saat berpindah halaman ---------- */
    document.addEventListener('click', function (e) {
        if (reduceMotion || e.defaultPrevented || e.button !== 0) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        var a = e.target.closest('a[href]');
        if (!a || (a.target && a.target !== '_self') || a.hasAttribute('download')) return;

        var raw = a.getAttribute('href');
        if (!raw || raw.charAt(0) === '#') return;

        var url;
        try { url = new URL(a.href, window.location.href); } catch (err) { return; }
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search) return;

        e.preventDefault();
        document.body.classList.add('is-leaving');
        setTimeout(function () { window.location.href = a.href; }, 170);
    });

    // Tombol "kembali" browser (bfcache): pastikan halaman tampil normal
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            document.body.classList.remove('is-leaving');
            setSidebar(false);
        }
    });

    /* ---------- Tabel responsif ---------- */
    // 1) Bungkus tabel yang belum punya .table-responsive agar bisa di-scroll horizontal
    document.querySelectorAll('.page-content table.table').forEach(function (table) {
        if (table.closest('.table-responsive')) return;
        var wrap = document.createElement('div');
        wrap.className = 'table-responsive';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    });

    // 2) Petunjuk "geser" di layar kecil, hanya bila tabel memang melebihi lebar layar
    var hints = [];
    document.querySelectorAll('.page-content .table-responsive').forEach(function (wrap) {
        var hint = document.createElement('div');
        hint.className = 'scroll-hint';
        hint.setAttribute('aria-hidden', 'true');
        hint.textContent = 'Geser ke samping untuk melihat kolom lainnya \u2192';
        wrap.parentNode.insertBefore(hint, wrap);
        hints.push({ wrap: wrap, hint: hint });

        wrap.addEventListener('scroll', function () {
            if (wrap.scrollLeft > 12) hint.classList.add('is-done');
        }, { passive: true });
    });

    function refreshHints() {
        hints.forEach(function (h) {
            var overflow = h.wrap.scrollWidth > h.wrap.clientWidth + 4;
            h.hint.classList.toggle('is-needed', overflow);
        });
    }
    refreshHints();
    window.addEventListener('resize', refreshHints);
    window.addEventListener('load', refreshHints);
})();
</script>

</body>
</html>
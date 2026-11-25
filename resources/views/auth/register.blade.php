<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#edf6ff">
    <title>Registrasi Mahasiswa | SIMFAS Kampus</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #183b5b;
            background: #edf6ff;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(ellipse at 5% 5%, #c8e7ff, transparent 40%),
                radial-gradient(ellipse at 95% 95%, #d8f3ff, transparent 40%),
                linear-gradient(135deg, #edf6ff, #fff 55%, #eaf5ff);
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(3px);
            animation: blobMove 18s ease-in-out infinite alternate;
        }

        .blob.one {
            width: 390px;
            height: 390px;
            top: -130px;
            left: -100px;
            background: radial-gradient(circle, #ffffffcc, #8ecaff55 65%, transparent);
        }

        .blob.two {
            width: 350px;
            height: 350px;
            bottom: -150px;
            left: 35%;
            background: radial-gradient(circle, #ffffffaa, #7bc5ff55 70%, transparent);
            animation-delay: -6s;
        }

        .blob.three {
            width: 300px;
            height: 300px;
            top: 20%;
            right: -100px;
            background: radial-gradient(circle, #ffffffbb, #9de5ed66 70%, transparent);
            animation-delay: -9s;
        }

        @keyframes blobMove {
            0% { transform: translate(0,0) scale(1); }
            50% { transform: translate(35px,-25px) scale(1.12); }
            100% { transform: translate(-20px,30px) scale(.94); }
        }

        .visual-panel {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding: 45px clamp(30px, 6vw, 90px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            animation: slideRight 1s cubic-bezier(.2,.8,.2,1) both;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -1px;
            color: #174b78;
        }

        .brand-icon {
            width: 43px;
            height: 43px;
            border-radius: 14px;
            background: linear-gradient(135deg, #3999e8, #76c9ed);
            display: grid;
            place-items: center;
            color: white;
            box-shadow: 0 9px 25px #318bd544;
        }

        .brand-icon svg { width: 25px; }

        .visual-content {
            max-width: 590px;
            margin: 35px auto;
            width: 100%;
        }

        .welcome-label {
            display: inline-flex;
            background: #ffffffa8;
            border: 1px solid white;
            border-radius: 50px;
            padding: 9px 15px;
            color: #3189d6;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.4px;
            animation: fadeUp .8s .2s both;
        }

        .visual-content h1 {
            font-size: clamp(35px, 4vw, 56px);
            line-height: 1.17;
            letter-spacing: -2.5px;
            margin: 25px 0 17px;
            color: #19466d;
            animation: fadeUp .9s .35s both;
        }

        .visual-content h1 span { color: #3995df; }

        .visual-content p {
            font-size: 14px;
            line-height: 1.9;
            color: #6e879e;
            max-width: 470px;
            animation: fadeUp .9s .5s both;
        }

        .campus-card {
            margin-top: 35px;
            min-height: 260px;
            border-radius: 30px;
            background: linear-gradient(135deg, #ffffffb8, #d8edff99);
            border: 1px solid #ffffffd9;
            box-shadow: 0 25px 70px #428ac51c, inset 0 1px 0 white;
            backdrop-filter: blur(18px);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            animation: fadeUp 1s .65s both, floatCard 6s ease-in-out infinite;
        }

        .campus-card::before {
            content: "";
            position: absolute;
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: #ffffff85;
            top: -120px;
            left: -50px;
        }

        .campus-card svg {
            width: min(75%, 390px);
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 20px 20px #327db52b);
            transition: transform .25s ease-out;
        }

        .glass-badge {
            position: absolute;
            z-index: 2;
            bottom: 20px;
            right: 22px;
            padding: 12px 17px;
            background: #ffffffd9;
            border: 1px solid white;
            border-radius: 15px;
            color: #287ebc;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 10px 25px #367bb51c;
            animation: badgeFloat 4s ease-in-out infinite;
        }

        .visual-footer {
            font-size: 12px;
            color: #8299ad;
        }

        .register-panel {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px clamp(25px, 6vw, 85px);
            position: relative;
            z-index: 2;
            animation: slideLeft 1s cubic-bezier(.2,.8,.2,1) both;
        }

        .register-card {
            width: 100%;
            max-width: 460px;
            position: relative;
            overflow: hidden;
            padding: 38px;
            border-radius: 28px;
            background: #ffffffb8;
            border: 1px solid white;
            box-shadow: 0 25px 80px #315e8520, inset 0 1px 0 white;
            backdrop-filter: blur(20px);
        }

        .register-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(
                350px circle at var(--mouse-x, 50%) var(--mouse-y, 0%),
                #78bfff25,
                transparent 70%
            );
        }

        .register-card > * { position: relative; }

        .heading { margin-bottom: 24px; }

        .small-label {
            color: #368ed1;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .heading h2 {
            margin: 11px 0 8px;
            font-size: 29px;
            letter-spacing: -1px;
            color: #19466d;
        }

        .heading p {
            margin: 0;
            color: #7b90a4;
            font-size: 12px;
            line-height: 1.7;
        }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #355976;
        }

        .input-wrap {
            height: 51px;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 0 14px;
            border-radius: 13px;
            border: 1px solid #dce8f2;
            background: #ffffffc9;
            transition: .3s ease;
        }

        .input-wrap:focus-within {
            border-color: #58a9ee;
            box-shadow: 0 0 0 4px #55a8ef20, 0 8px 25px #55a8ef12;
            background: white;
        }

        .input-wrap svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: #7c9bb5;
            fill: none;
            stroke-width: 1.8;
        }

        .input-wrap input {
            width: 100%;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            font: inherit;
            color: #244968;
            font-size: 12px;
        }

        .input-wrap input::placeholder { color: #a4b4c2; }

        .toggle-password {
            border: 0;
            background: transparent;
            color: #7b98b2;
            cursor: pointer;
            padding: 5px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .toggle-password:hover { color: #2788d5; }

        .register-button {
            width: 100%;
            min-height: 53px;
            margin-top: 8px;
            border: 0;
            border-radius: 14px;
            color: white;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            background: linear-gradient(110deg, #267dc2, #43a2e9, #287fc5);
            box-shadow: 0 12px 25px #318bd53b;
            transition: box-shadow .3s, filter .3s;
        }

        .register-button:hover {
            box-shadow: 0 16px 35px #318bd566;
            filter: brightness(1.06);
        }

        .register-button::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 70%;
            height: 100%;
            transform: skewX(-20deg);
            background: linear-gradient(90deg, transparent, #ffffff66, transparent);
            animation: shimmer 3.5s infinite;
        }

        .register-button:disabled {
            opacity: .8;
            cursor: wait;
        }

        .button-content {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .spinner {
            display: none;
            width: 17px;
            height: 17px;
            border: 2px solid #ffffff65;
            border-top-color: white;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        .register-button.loading .spinner { display: inline-block; }

        .login-link {
            text-align: center;
            margin-top: 21px;
            font-size: 12px;
            color: #8195a7;
        }

        .login-link a {
            color: #2587d2;
            font-weight: 800;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }

        .info-note {
            margin-top: 20px;
            padding: 12px 14px;
            border-radius: 13px;
            background: #edf7ff;
            border: 1px solid #dceeff;
            color: #63839f;
            font-size: 10px;
            line-height: 1.7;
        }

        .error-message {
            background: #fff0f0;
            border: 1px solid #ffd6d6;
            color: #bd4545;
            border-radius: 12px;
            padding: 11px 13px;
            font-size: 11px;
            margin-bottom: 17px;
            line-height: 1.7;
        }

        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-45px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideLeft {
            from { opacity: 0; transform: translateX(45px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes floatCard {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-9px); }
        }

        @keyframes badgeFloat {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-7px); }
        }

        @keyframes shimmer {
            0% { left: -100%; }
            55%,100% { left: 140%; }
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        @media(max-width: 950px) {
            .page { grid-template-columns: 1fr; }
            .visual-panel {
                min-height: auto;
                padding: 28px 30px 10px;
            }
            .visual-content {
                margin: 40px auto 20px;
                max-width: 650px;
            }
            .visual-content h1 { font-size: 40px; }
            .campus-card { min-height: 210px; }
            .visual-footer { display: none; }
            .register-panel {
                min-height: auto;
                padding: 20px 25px 55px;
            }
            .register-card { max-width: 540px; }
        }

        @media(max-width: 520px) {
            .visual-panel { padding: 22px 22px 8px; }
            .brand { font-size: 20px; }
            .visual-content { margin: 35px auto 15px; }
            .visual-content h1 {
                font-size: 34px;
                letter-spacing: -1.5px;
            }
            .visual-content p { font-size: 12px; }
            .campus-card {
                min-height: 175px;
                border-radius: 22px;
                margin-top: 20px;
            }
            .glass-badge {
                right: 12px;
                bottom: 12px;
                font-size: 9px;
            }
            .register-panel { padding: 18px 17px 40px; }
            .register-card { padding: 27px 21px; border-radius: 22px; }
            .heading h2 { font-size: 26px; }
        }

        @media(prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
<div class="page">
    <div class="blob one"></div>
    <div class="blob two"></div>
    <div class="blob three"></div>

    <section class="visual-panel">
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
                </svg>
            </div>
            SIMFAS Kampus
        </div>

        <div class="visual-content">
            <span class="welcome-label">✦ BERGABUNG DENGAN SIMFAS</span>

            <h1>
                Mulai Kelola
                <span>Fasilitas Kampus</span>
                Bersama Kami.
            </h1>

            <p>
                Buat akun mahasiswa untuk mengakses informasi fasilitas,
                mengajukan peminjaman sarana, dan menikmati layanan
                SIMFAS Kampus secara lebih mudah.
            </p>

            <div class="campus-card" id="campusCard">
                <svg id="campusArt" viewBox="0 0 460 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="230" cy="229" rx="175" ry="17" fill="#8ABFE9" opacity=".22"/>
                    <path d="M70 216V105L230 34L390 105V216H70Z" fill="#D5EBFC" stroke="#80BCEB" stroke-width="3"/>
                    <path d="M91 108L230 48L369 108" stroke="#4F9BD7" stroke-width="8" stroke-linecap="round"/>
                    <path d="M116 110V215M344 110V215" stroke="#8DBDE0" stroke-width="3"/>
                    <rect x="181" y="133" width="98" height="83" rx="5" fill="#8FC4EE"/>
                    <rect x="194" y="145" width="30" height="29" rx="3" fill="#F4FBFF"/>
                    <rect x="236" y="145" width="30" height="29" rx="3" fill="#F4FBFF"/>
                    <rect x="194" y="185" width="30" height="31" rx="3" fill="#F4FBFF"/>
                    <rect x="236" y="185" width="30" height="31" rx="3" fill="#F4FBFF"/>
                    <rect x="105" y="130" width="52" height="42" rx="5" fill="#fff" stroke="#8ABFE9" stroke-width="3"/>
                    <rect x="303" y="130" width="52" height="42" rx="5" fill="#fff" stroke="#8ABFE9" stroke-width="3"/>
                    <path d="M65 218H395" stroke="#438DC9" stroke-width="5" stroke-linecap="round"/>
                    <path d="M217 34L230 12L243 34" fill="#53A6E7"/>
                    <path d="M230 12V-1" stroke="#438DC9" stroke-width="3"/>
                    <path d="M230 2L258 9L230 17" fill="#65B5E9"/>
                    <circle cx="91" cy="83" r="15" fill="#fff" opacity=".8"/>
                    <circle cx="365" cy="75" r="10" fill="#fff" opacity=".8"/>
                </svg>
                <div class="glass-badge">✦ Akun Mahasiswa</div>
            </div>
        </div>

        <div class="visual-footer">
            © {{ date('Y') }} SIMFAS Kampus · Sistem Informasi Fasilitas Kampus
        </div>
    </section>

    <main class="register-panel">
        <div class="register-card" id="registerCard">
            <div class="heading">
                <span class="small-label">PORTAL PENDAFTARAN</span>
                <h2>Buat Akun Baru</h2>
                <p>Lengkapi informasi berikut untuk membuat akun mahasiswa SIMFAS.</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>⚠ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/register" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/>
                        </svg>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            placeholder="Masukkan nama lengkap"
                            value="{{ old('name') }}"
                            maxlength="100"
                            autocomplete="name"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="5" width="18" height="14" rx="3"/>
                            <path d="m4 7 8 6 8-6"/>
                        </svg>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Masukkan email aktif"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24">
                            <rect x="4" y="10" width="16" height="11" rx="3"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/>
                        </svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <button type="button" class="toggle-password" data-target="password">Lihat</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24">
                            <rect x="4" y="10" width="16" height="11" rx="3"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3M9 16l2 2 4-4"/>
                        </svg>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <button type="button" class="toggle-password" data-target="password_confirmation">Lihat</button>
                    </div>
                </div>

                <button type="submit" class="register-button" id="registerButton">
                    <span class="button-content">
                        <span class="spinner"></span>
                        <span id="buttonText">Daftar Sekarang →</span>
                    </span>
                </button>
            </form>

            <div class="login-link">
                Sudah memiliki akun?
                <a href="/login">Masuk di sini</a>
            </div>

            <div class="info-note">
                ✦ Pendaftaran melalui halaman ini akan membuat akun dengan akses
                <strong>Mahasiswa</strong>. Hak akses lainnya dikelola oleh sistem.
            </div>
        </div>
    </main>
</div>

<script>
    // SHOW / HIDE PASSWORD
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const hidden = input.type === 'password';

            input.type = hidden ? 'text' : 'password';
            this.textContent = hidden ? 'Sembunyi' : 'Lihat';
        });
    });

    // VALIDASI KECOCOKAN PASSWORD DI BROWSER
    const registerForm = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');

    registerForm.addEventListener('submit', function (event) {
        if (password.value !== confirmation.value) {
            event.preventDefault();
            confirmation.setCustomValidity('Konfirmasi password belum sama.');
            confirmation.reportValidity();
            confirmation.focus();
            return;
        }

        confirmation.setCustomValidity('');
        const button = document.getElementById('registerButton');
        button.classList.add('loading');
        button.disabled = true;
        document.getElementById('buttonText').textContent = 'Membuat akun...';
    });

    confirmation.addEventListener('input', function () {
        this.setCustomValidity('');
    });

    // SPOTLIGHT KURSOR
    const card = document.getElementById('registerCard');

    card.addEventListener('pointermove', function (event) {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--mouse-x', (event.clientX - rect.left) + 'px');
        card.style.setProperty('--mouse-y', (event.clientY - rect.top) + 'px');
    });

    // PARALLAX ILUSTRASI
    const campusCard = document.getElementById('campusCard');
    const campusArt = document.getElementById('campusArt');

    campusCard.addEventListener('pointermove', function (event) {
        if (event.pointerType === 'touch') return;

        const rect = campusCard.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - .5;
        const y = (event.clientY - rect.top) / rect.height - .5;

        campusArt.style.transform =
            `translate(${x * 12}px, ${y * 10}px) rotateY(${x * 7}deg) rotateX(${-y * 6}deg)`;
    });

    campusCard.addEventListener('pointerleave', function () {
        campusArt.style.transform = 'translate(0,0) rotateY(0) rotateX(0)';
    });
</script>
</body>
</html>

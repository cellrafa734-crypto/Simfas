<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#edf6ff">
    <title>Login Portal | SIMFAS Kampus</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --blue: #3189d6;
            --blue-dark: #174b78;
            --text: #183b5b;
            --muted: #71869b;
            --white: #ffffff;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: #edf6ff;
            overflow-x: hidden;
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.08fr .92fr;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(ellipse at 5% 5%, #c8e7ff 0, transparent 40%),
                radial-gradient(ellipse at 95% 95%, #d8f3ff 0, transparent 40%),
                linear-gradient(135deg, #edf6ff, #ffffff 55%, #eaf5ff);
        }

        /* BACKGROUND BLOBS */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            pointer-events: none;
            animation: blobMove 18s ease-in-out infinite alternate;
        }

        .blob.one {
            width: 390px;
            height: 390px;
            background: radial-gradient(circle at 35% 30%, #ffffffcc, #8ecaff55 65%, transparent);
            top: -130px;
            left: -100px;
        }

        .blob.two {
            width: 330px;
            height: 330px;
            background: radial-gradient(circle at 40% 35%, #ffffffaa, #7bc5ff55 70%, transparent);
            bottom: -130px;
            left: 35%;
            animation-delay: -5s;
        }

        .blob.three {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle at 40% 35%, #ffffffbb, #9de5ed66 70%, transparent);
            right: -100px;
            top: 20%;
            animation-delay: -9s;
        }

        @keyframes blobMove {
            0% { transform: translate(0,0) scale(1); }
            50% { transform: translate(35px,-25px) scale(1.12); }
            100% { transform: translate(-20px,30px) scale(.94); }
        }

        /* LEFT PANEL */
        .visual-panel {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding: 45px clamp(30px, 6vw, 90px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            animation: slideRight 1s cubic-bezier(.2,.8,.2,1) both;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--blue-dark);
        }

        .brand-icon {
            width: 43px;
            height: 43px;
            border-radius: 14px;
            background: linear-gradient(135deg, #3999e8, #76c9ed);
            display: grid;
            place-items: center;
            color: white;
            box-shadow: 0 9px 25px #3189d644;
        }

        .brand-icon svg { width: 25px; }

        .visual-content {
            max-width: 590px;
            margin: 35px auto;
            width: 100%;
        }

        .welcome-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffffa8;
            border: 1px solid #ffffff;
            border-radius: 50px;
            padding: 9px 15px;
            color: #3189d6;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.5px;
            animation: fadeUp .8s .2s both;
        }

        .visual-content h1 {
            font-size: clamp(35px, 4vw, 57px);
            line-height: 1.16;
            letter-spacing: -2.5px;
            margin: 25px 0 17px;
            color: #19466d;
            animation: fadeUp .9s .35s both;
        }

        .visual-content h1 span {
            color: #3995df;
        }

        .visual-content > p {
            font-size: 15px;
            line-height: 1.9;
            color: #6e879e;
            max-width: 480px;
            animation: fadeUp .9s .5s both;
        }

        /* CAMPUS ILLUSTRATION CARD */
        .campus-card {
            margin-top: 35px;
            position: relative;
            min-height: 260px;
            border-radius: 30px;
            background: linear-gradient(135deg, #ffffffb8, #d8edff99);
            border: 1px solid #ffffffd9;
            box-shadow: 0 25px 70px #428ac51c, inset 0 1px 0 #ffffff;
            backdrop-filter: blur(18px);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            animation: fadeUp 1s .65s both, floatCard 6s ease-in-out infinite;
            transform-style: preserve-3d;
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

        .campus-card::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: #83caff30;
            right: -60px;
            bottom: -90px;
        }

        .campus-art {
            width: min(75%, 390px);
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 20px 20px #327db52b);
            transition: transform .25s ease-out;
        }

        .glass-badge {
            position: absolute;
            z-index: 3;
            bottom: 20px;
            right: 22px;
            padding: 12px 17px;
            background: #ffffffd9;
            border: 1px solid #ffffff;
            border-radius: 15px;
            color: #287ebc;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 10px 25px #367bb51c;
            backdrop-filter: blur(12px);
            animation: badgeFloat 4s ease-in-out infinite;
        }

        .visual-footer {
            font-size: 12px;
            color: #8299ad;
            animation: fadeUp 1s .8s both;
        }

        /* RIGHT PANEL */
        .login-panel {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px clamp(25px, 6vw, 85px);
            position: relative;
            z-index: 2;
            animation: slideLeft 1s cubic-bezier(.2,.8,.2,1) both;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            position: relative;
            overflow: hidden;
            padding: 42px;
            border-radius: 28px;
            background: #ffffffb8;
            border: 1px solid #ffffff;
            box-shadow: 0 25px 80px #315e8520, inset 0 1px 0 #ffffff;
            backdrop-filter: blur(20px);
        }

        .login-card::before {
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

        .login-card > * { position: relative; }

        .login-heading {
            margin-bottom: 30px;
        }

        .login-heading .small-label {
            color: #368ed1;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .login-heading h2 {
            margin: 12px 0 9px;
            font-size: 31px;
            letter-spacing: -1px;
            color: #19466d;
        }

        .login-heading p {
            margin: 0;
            color: #7b90a4;
            font-size: 13px;
            line-height: 1.7;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            margin-bottom: 9px;
            font-size: 12px;
            font-weight: 700;
            color: #355976;
        }

        .input-wrap {
            height: 55px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 15px;
            border-radius: 14px;
            border: 1px solid #dce8f2;
            background: #ffffffc9;
            transition: .3s ease;
        }

        .input-wrap:focus-within {
            border-color: #58a9ee;
            box-shadow: 0 0 0 4px #55a8ef20, 0 8px 25px #55a8ef12;
            background: #ffffff;
        }

        .input-wrap svg {
            width: 19px;
            height: 19px;
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
            font-size: 13px;
        }

        .input-wrap input::placeholder { color: #a4b4c2; }

        .toggle-password {
            border: 0;
            background: transparent;
            color: #7b98b2;
            cursor: pointer;
            padding: 5px;
            font-size: 11px;
            font-weight: 700;
        }

        .toggle-password:hover { color: #2788d5; }

        .login-button {
            width: 100%;
            min-height: 55px;
            margin-top: 8px;
            border: 0;
            border-radius: 14px;
            color: white;
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .2px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            background: linear-gradient(110deg, #267dc2, #43a2e9, #287fc5);
            box-shadow: 0 12px 25px #318bd53b;
            transition: box-shadow .3s, filter .3s;
        }

        .login-button:hover {
            box-shadow: 0 16px 35px #318bd566;
            filter: brightness(1.06);
        }

        .login-button::before {
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

        .login-button:disabled {
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

        .login-button.loading .spinner { display: inline-block; }
        .login-button.loading .button-text { opacity: .9; }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #8195a7;
        }

        .register-link a {
            color: #2587d2;
            font-weight: 800;
            text-decoration: none;
        }

        .register-link a:hover { text-decoration: underline; }

        .role-title {
            margin: 30px 0 12px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #8ba0b2;
            text-align: center;
        }

        .roles {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .role-chip {
            border: 1px solid #dceaf5;
            background: #f5faff;
            border-radius: 50px;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: 700;
            color: #58809f;
            transition: .25s;
        }

        .role-chip:hover {
            background: #e4f3ff;
            border-color: #a9d7fa;
            transform: translateY(-3px);
        }

        .error-message {
            background: #fff0f0;
            border: 1px solid #ffd6d6;
            color: #bd4545;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 12px;
            margin-bottom: 18px;
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

        @media (max-width: 950px) {
            .page { grid-template-columns: 1fr; }
            .visual-panel {
                min-height: auto;
                padding: 28px 30px 15px;
            }
            .visual-content {
                margin: 55px auto 25px;
                max-width: 650px;
            }
            .visual-content h1 { font-size: 42px; }
            .campus-card { min-height: 220px; }
            .visual-footer { display: none; }
            .login-panel {
                min-height: auto;
                padding: 25px 25px 60px;
            }
            .login-card { max-width: 540px; }
        }

        @media (max-width: 520px) {
            .visual-panel { padding: 23px 22px 10px; }
            .brand { font-size: 20px; }
            .visual-content { margin: 40px auto 15px; }
            .visual-content h1 {
                font-size: 35px;
                letter-spacing: -1.5px;
            }
            .visual-content > p { font-size: 13px; }
            .campus-card {
                min-height: 190px;
                border-radius: 23px;
                margin-top: 25px;
            }
            .glass-badge {
                right: 12px;
                bottom: 12px;
                font-size: 10px;
            }
            .login-panel { padding: 20px 18px 45px; }
            .login-card { padding: 29px 23px; border-radius: 23px; }
            .login-heading h2 { font-size: 27px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
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

    <!-- PANEL KIRI -->
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
            <span class="welcome-label">PORTAL SISTEM INFORMASI KAMPUS</span>

            <h1>
                Selamat Datang di
                <span>SIMFAS Kampus.</span>
            </h1>

            <p>
                Satu portal untuk mengelola fasilitas kampus,
                mengajukan peminjaman, dan memantau kebutuhan
                sarana secara lebih mudah dan terorganisir.
            </p>

            <div class="campus-card" id="campusCard">
                <svg class="campus-art" id="campusArt" viewBox="0 0 460 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="230" cy="229" rx="175" ry="17" fill="#8ABFE9" opacity=".22"/>

                    <path d="M70 216V105L230 34L390 105V216H70Z" fill="#D5EBFC" stroke="#80BCEB" stroke-width="3"/>
                    <path d="M91 108L230 48L369 108" stroke="#4F9BD7" stroke-width="8" stroke-linecap="round"/>
                    <path d="M116 110V215M344 110V215" stroke="#8DBDE0" stroke-width="3"/>

                    <rect x="181" y="133" width="98" height="83" rx="5" fill="#8FC4EE"/>
                    <rect x="194" y="145" width="30" height="29" rx="3" fill="#F4FBFF"/>
                    <rect x="236" y="145" width="30" height="29" rx="3" fill="#F4FBFF"/>
                    <rect x="194" y="185" width="30" height="31" rx="3" fill="#F4FBFF"/>
                    <rect x="236" y="185" width="30" height="31" rx="3" fill="#F4FBFF"/>

                    <rect x="105" y="130" width="52" height="42" rx="5" fill="#FFFFFF" stroke="#8ABFE9" stroke-width="3"/>
                    <rect x="303" y="130" width="52" height="42" rx="5" fill="#FFFFFF" stroke="#8ABFE9" stroke-width="3"/>

                    <path d="M65 218H395" stroke="#438DC9" stroke-width="5" stroke-linecap="round"/>
                    <path d="M217 34L230 12L243 34" fill="#53A6E7"/>
                    <path d="M230 12V-1" stroke="#438DC9" stroke-width="3"/>
                    <path d="M230 2L258 9L230 17" fill="#65B5E9"/>

                    <circle cx="91" cy="83" r="15" fill="#FFFFFF" opacity=".8"/>
                    <circle cx="365" cy="75" r="10" fill="#FFFFFF" opacity=".8"/>
                    <path d="M55 195L39 181M405 195L421 181" stroke="#75B4E5" stroke-width="4" stroke-linecap="round"/>
                </svg>

                <div class="glass-badge">Sistem Fasilitas Terintegrasi</div>
            </div>
        </div>

        <div class="visual-footer">
            © {{ date('Y') }} SIMFAS Kampus · Sistem Informasi Fasilitas Kampus
        </div>
    </section>

    <!-- PANEL KANAN -->
    <main class="login-panel">
        <div class="login-card" id="loginCard">
            <div class="login-heading">
                <span class="small-label">PORTAL PENGGUNA</span>
                <h2>Masuk Aplikasi</h2>
                <p>Silakan masuk menggunakan akun SIMFAS Kampus Anda.</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/login" id="loginForm">
                @csrf

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
                            placeholder="Masukkan email Anda"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            required
                            autofocus
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
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-password" id="togglePassword">
                            Lihat
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-button" id="loginButton">
                    <span class="button-content">
                        <span class="spinner"></span>
                        <span class="button-text" id="buttonText">Masuk Aplikasi →</span>
                    </span>
                </button>
            </form>

            <div class="register-link">
                Belum mempunyai akun?
                <a href="/register">Registrasi mahasiswa</a>
            </div>

            <div class="role-title">AKSES PENGGUNA SIMFAS</div>
            <div class="roles">
                <span class="role-chip">Admin</span>
                <span class="role-chip">Mahasiswa</span>
                <span class="role-chip">Organisasi</span>
                <span class="role-chip">Petugas</span>
            </div>
        </div>
    </main>
</div>

<script>
    // SHOW / HIDE PASSWORD
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        this.textContent = isHidden ? 'Sembunyi' : 'Lihat';
    });

    // LOADING SAAT FORM DIKIRIM
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const buttonText = document.getElementById('buttonText');

    loginForm.addEventListener('submit', function () {
        if (!loginForm.checkValidity()) return;

        loginButton.classList.add('loading');
        loginButton.disabled = true;
        buttonText.textContent = 'Memeriksa akun...';
    });

    // SPOTLIGHT MENGIKUTI KURSOR
    const loginCard = document.getElementById('loginCard');

    loginCard.addEventListener('pointermove', function (event) {
        const rect = loginCard.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        loginCard.style.setProperty('--mouse-x', x + 'px');
        loginCard.style.setProperty('--mouse-y', y + 'px');
    });

    // PARALLAX ILUSTRASI KAMPUS
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

    // MAGNETIC BUTTON
    const magneticButton = document.getElementById('loginButton');

    magneticButton.addEventListener('pointermove', function (event) {
        if (event.pointerType === 'touch' || this.disabled) return;

        const rect = this.getBoundingClientRect();
        const x = event.clientX - rect.left - rect.width / 2;
        const y = event.clientY - rect.top - rect.height / 2;

        this.style.transform = `translate(${x * .08}px, ${y * .12}px)`;
    });

    magneticButton.addEventListener('pointerleave', function () {
        this.style.transform = 'translate(0,0)';
    });
</script>
</body>
</html>

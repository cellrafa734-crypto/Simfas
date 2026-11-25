
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#eaf5ff">

    <title>SIMFAS Kampus | Sistem Informasi Fasilitas Kampus</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #368bd0;
            --primary-dark: #236da9;
            --navy: #183e60;
            --sky: #eaf5ff;
            --text: #435b70;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: #fff;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        /* NAVBAR */

        .navbar {
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            padding: 17px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(220,235,248,.8);
            transition: .3s;
        }

        .navbar.scrolled {
            padding-top: 11px;
            padding-bottom: 11px;
            box-shadow: 0 5px 25px rgba(36,91,135,.09);
        }

        .logo {
            font-size: 23px;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: -1px;
        }

        .logo span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .nav-links a {
            color: #536d83;
            font-size: 14px;
            font-weight: 600;
            transition: .25s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links .nav-cta {
            color: white;
            padding: 12px 22px;
            border-radius: 30px;
            background: var(--primary);
            box-shadow: 0 5px 16px rgba(54,139,208,.2);
        }

        .nav-cta:hover {
            color: white !important;
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .menu-toggle {
            display: none;
            border: 0;
            background: none;
            color: var(--navy);
            font-size: 27px;
            cursor: pointer;
        }

        /* HERO */

        .hero {
            min-height: 720px;
            min-height: 100svh;
            position: relative;
            display: flex;
            align-items: center;
            padding: 130px 7% 90px;
            overflow: hidden;
            background: linear-gradient(135deg, #b9e0ff, #edf7ff);
        }

        .hero-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            animation: videoFloat 24s ease-in-out infinite alternate;
            transform: scale(1.04);
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(90deg,
                    rgba(13,49,78,.78) 0%,
                    rgba(20,68,105,.59) 55%,
                    rgba(31,92,137,.30) 100%),
                linear-gradient(0deg, rgba(16,55,88,.30), transparent);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 760px;
            color: white;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 17px;
            border: 1px solid rgba(255,255,255,.38);
            background: rgba(255,255,255,.16);
            backdrop-filter: blur(12px);
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            animation: slideUp .8s both;
        }

        .hero h1 {
            font-size: clamp(38px, 6vw, 70px);
            line-height: 1.13;
            letter-spacing: -2.8px;
            font-weight: 800;
            margin: 28px 0 22px;
            animation: slideUp .9s .15s both;
        }

        .hero h1 span {
            color: #a9dcff;
        }

        .hero-description {
            max-width: 650px;
            font-size: 16px;
            line-height: 1.9;
            color: rgba(255,255,255,.88);
            animation: slideUp .9s .3s both;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 35px;
            animation: slideUp .9s .45s both;
        }

        .btn-main,
        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 25px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            transition: .3s;
        }

        .btn-main {
            color: var(--primary-dark);
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,.13);
        }

        .btn-main:hover {
            color: var(--primary-dark);
            transform: translateY(-5px);
            box-shadow: 0 13px 30px rgba(0,0,0,.2);
        }

        .btn-outline {
            color: white;
            border: 1px solid rgba(255,255,255,.7);
            background: rgba(255,255,255,.09);
            backdrop-filter: blur(8px);
        }

        .btn-outline:hover {
            background: white;
            color: var(--navy);
            transform: translateY(-4px);
        }

        .hero-note {
            margin-top: 45px;
            color: rgba(255,255,255,.8);
            font-size: 12px;
            animation: fadeIn 1.5s .6s both;
        }

        .hero-decoration {
            position: absolute;
            z-index: 1;
            width: 330px;
            height: 330px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.17);
            right: -100px;
            bottom: -100px;
            animation: sway 9s ease-in-out infinite alternate;
        }

        .hero-decoration::before {
            content: "";
            position: absolute;
            inset: 35px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 50%;
        }

        /* GENERAL SECTIONS */

        .section {
            padding: 100px 7%;
        }

        .section-heading {
            max-width: 650px;
            margin: 0 auto 55px;
            text-align: center;
        }

        .eyebrow {
            display: inline-block;
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 13px;
        }

        .section-heading h2 {
            color: var(--navy);
            font-size: clamp(27px, 4vw, 40px);
            font-weight: 800;
            letter-spacing: -1.3px;
            margin-bottom: 15px;
        }

        .section-heading p {
            color: #778b9d;
            font-size: 14px;
            line-height: 1.9;
        }

        /* ABOUT */

        .about {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 65px;
            background: white;
        }

        .about-visual {
            min-height: 360px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,#d7edff,#f2f9ff);
            border-radius: 30px;
            overflow: hidden;
        }

        .about-visual::before,
        .about-visual::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.4);
        }

        .about-visual::before {
            width: 250px;
            height: 250px;
            top: -100px;
            left: -60px;
        }

        .about-visual::after {
            width: 220px;
            height: 220px;
            bottom: -100px;
            right: -50px;
        }

        .building {
            font-size: 120px;
            position: relative;
            z-index: 1;
            animation: floating 4s ease-in-out infinite;
            filter: drop-shadow(0 15px 12px rgba(46,115,170,.17));
        }

        .about-content h2 {
            font-size: 37px;
            color: var(--navy);
            line-height: 1.3;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .about-content p {
            font-size: 14px;
            line-height: 2;
            color: #75899a;
            margin-bottom: 15px;
        }

        .about-check {
            margin-top: 23px;
            display: grid;
            gap: 15px;
        }

        .about-check div {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
        }

        .check {
            width: 27px;
            height: 27px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: var(--primary);
            background: #e8f5ff;
        }

        /* FEATURES */

        .features {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            background:
                radial-gradient(ellipse at 50% 40%, rgba(255,255,255,.92), transparent 68%),
                linear-gradient(135deg, #f8fcff 0%, #edf7ff 52%, #f1fbfc 100%);
        }

        .features::before,
        .features::after {
            content: "";
            position: absolute;
            z-index: -1;
            width: min(52vw, 680px);
            aspect-ratio: 1;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .48;
            pointer-events: none;
        }

        .features::before {
            top: -34%;
            left: -14%;
            background: radial-gradient(circle, rgba(110,190,249,.42), rgba(160,220,255,.13) 52%, transparent 72%);
        }

        .features::after {
            right: -17%;
            bottom: -48%;
            background: radial-gradient(circle, rgba(100,220,224,.3), rgba(143,211,255,.14) 52%, transparent 72%);
        }

        .features > .section-heading,
        .features > .feature-grid {
            position: relative;
            z-index: 1;
        }

        @media(prefers-reduced-motion: no-preference) {
            .features::before {
                animation: featureBlobDrift 18s ease-in-out infinite alternate;
            }

            .features::after {
                animation: featureBlobDrift 22s ease-in-out -7s infinite alternate-reverse;
            }

            @keyframes featureBlobDrift {
                from { transform: translate3d(0, 0, 0) scale(1); }
                to { transform: translate3d(8%, 10%, 0) scale(1.12); }
            }
        }

        @property --feature-angle {
            syntax: "<angle>";
            inherits: true;
            initial-value: 0deg;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 23px;
        }

        .feature-card {
            --feature-delay: 0s;
            --pointer-x: 50%;
            --pointer-y: 50%;
            position: relative;
            isolation: isolate;
            overflow: hidden;
            padding: 32px;
            border: 1px solid rgba(255,255,255,.82);
            border-radius: 20px;
            background: rgba(255,255,255,.68);
            -webkit-backdrop-filter: blur(16px);
            backdrop-filter: blur(16px);
            box-shadow: 0 10px 30px rgba(38,90,132,.07);
            transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        }

        .feature-card::before,
        .feature-card::after {
            content: "";
            position: absolute;
            z-index: -1;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            opacity: 0;
            transition: opacity .35s ease;
        }

        .feature-card::before {
            padding: 1px;
            background: conic-gradient(
                from var(--feature-angle, 0deg),
                transparent 0 55%,
                rgba(87,177,245,.9) 72%,
                rgba(173,222,255,.95) 82%,
                transparent 100%
            );
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
        }

        .feature-card::after {
            background: radial-gradient(
                300px circle at var(--pointer-x) var(--pointer-y),
                rgba(93,177,239,.17),
                transparent 70%
            );
        }

        .feature-card:hover::before,
        .feature-card:hover::after {
            opacity: 1;
        }

        .feature-grid .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 42px rgba(49,137,202,.2), 0 0 22px rgba(96,183,243,.14);
            border-color: rgba(128,197,245,.8);
        }

        .feature-grid .feature-card.reveal {
            transition: opacity .75s ease, transform .75s ease, box-shadow .35s ease, border-color .35s ease;
            transition-delay: 0s, var(--feature-delay), 0s, 0s;
        }

        .feature-grid .feature-card.reveal.visible {
            transition-delay: var(--feature-delay), var(--feature-delay), 0s, 0s;
        }

        .feature-grid .feature-card:nth-child(2) {
            --feature-delay: .1s;
        }

        .feature-grid .feature-card:nth-child(3) {
            --feature-delay: .2s;
        }

        .feature-icon {
            width: 59px;
            height: 59px;
            display: grid;
            place-items: center;
            border-radius: 17px;
            color: #368bd0;
            background: linear-gradient(145deg, #e1f2ff, #f3faff);
            margin-bottom: 24px;
            transform-style: preserve-3d;
            transition: color .35s ease, background .35s ease, transform .35s ease, box-shadow .35s ease;
        }

        .feature-icon svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .feature-card:hover .feature-icon {
            color: white;
            background: linear-gradient(145deg, #368bd0, #6bb8ef);
            transform: translateY(-3px) rotate(-6deg) scale(1.1);
            box-shadow: 0 8px 20px rgba(54,139,208,.25);
        }

        .feature-card:hover .feature-icon svg {
            animation: featureIconPulse .9s ease-in-out infinite alternate;
        }

        @keyframes featureIconPulse {
            from { transform: translateY(0) rotateY(0); }
            to { transform: translateY(-3px) rotateY(12deg); }
        }

        @media(prefers-reduced-motion: no-preference) {
            .feature-card:hover::before {
                animation: featureBorderSpin 3s linear infinite;
            }

            @keyframes featureBorderSpin {
                to { --feature-angle: 360deg; }
            }
        }

        .feature-card h3 {
            color: var(--navy);
            font-size: 17px;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #8193a2;
            font-size: 13px;
            line-height: 1.9;
        }

        /* MARQUEE */

        .gallery {
            overflow: hidden;
            background: white;
            padding: 90px 0;
        }

        .gallery .section-heading {
            padding: 0 7%;
        }

        .marquee {
            width: 100%;
            overflow: hidden;
            position: relative;
            padding: 10px 0 25px;
            mask-image: linear-gradient(to right, transparent, black 7%, black 93%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 7%, black 93%, transparent);
        }

        .marquee-track {
            display: flex;
            width: max-content;
            gap: 22px;
            animation: marquee 35s linear infinite;
        }

        .marquee:hover .marquee-track {
            animation-play-state: paused;
        }

        .facility-card {
            width: 280px;
            min-width: 280px;
            min-height: 0;
            padding: 0;
            overflow: hidden;
            border-radius: 20px;
            background: white;
            border: 1px solid #e4eff8;
            box-shadow: 0 8px 25px rgba(36,90,135,.09);
        }

        .facility-card:hover {
            transform: translateY(-7px);
        }

        .facility-card img {
            width: 100%;
            height: 155px;
            display: block;
            object-fit: cover;
            transition: transform .5s ease;
        }

        .facility-card:hover img {
            transform: scale(1.07);
        }

        .facility-info {
            padding: 16px 18px;
        }

        .facility-icon {
            width: 53px;
            height: 53px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: #dcefff;
            font-size: 27px;
        }

        .facility-info h3 {
            font-size: 15px;
            color: #183e60;
            margin-bottom: 6px;
        }

        .facility-info p {
            font-size: 12px;
            color: #8093a4;
            margin: 0;
        }

        /* PROCESS */

        .process {
            background: linear-gradient(135deg,#eaf5ff,#f8fbff);
        }

        .process-grid {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 25px;
        }

        .process-card {
            text-align: center;
            padding: 35px 25px;
            border-radius: 22px;
            background: rgba(255,255,255,.8);
            border: 1px solid rgba(255,255,255,.95);
            box-shadow: 0 10px 28px rgba(40,100,145,.05);
        }

        .process-number {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            margin: 0 auto 20px;
            border-radius: 18px;
            background: var(--primary);
            color: white;
            font-weight: 800;
            font-size: 21px;
            box-shadow: 0 8px 20px rgba(54,139,208,.23);
        }

        .process-card h3 {
            color: var(--navy);
            font-size: 17px;
            margin-bottom: 12px;
        }

        .process-card p {
            color: #8093a4;
            font-size: 13px;
            line-height: 1.8;
        }

        /* CTA */

        .cta {
            padding: 70px 7%;
            background: white;
        }

        .cta-box {
            padding: 65px 25px;
            text-align: center;
            border-radius: 30px;
            color: white;
            background: linear-gradient(120deg,#438fce,#74b7e9);
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(54,139,208,.17);
        }

        .cta-box::before,
        .cta-box::after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 50%;
            animation: sway 7s ease-in-out infinite alternate;
        }

        .cta-box::before {
            top: -140px;
            left: -60px;
        }

        .cta-box::after {
            bottom: -150px;
            right: -40px;
        }

        .cta-box h2 {
            font-size: clamp(25px,4vw,36px);
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .cta-box p {
            font-size: 14px;
            color: rgba(255,255,255,.9);
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }

        .cta-box .btn-main {
            position: relative;
            z-index: 1;
        }

        /* FOOTER */

        footer {
            background: #edf6fd;
            padding: 35px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        footer strong {
            color: var(--navy);
            font-size: 19px;
        }

        footer p {
            color: #8093a4;
            font-size: 12px;
            margin-top: 7px;
        }

        .footer-copy {
            color: #8093a4;
            font-size: 12px;
        }

        /* SCROLL REVEAL */

        .reveal {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity .8s ease, transform .8s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ANIMATIONS */

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(35px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes floating {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }

        @keyframes sway {
            from { transform: translate(0,0) rotate(0); }
            to { transform: translate(-20px,15px) rotate(8deg); }
        }

        @keyframes videoFloat {
            from { transform: scale(1.04) translateX(0); }
            to { transform: scale(1.12) translateX(-1%); }
        }

        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        /* RESPONSIVE */

        @media(max-width: 900px) {
            .navbar {
                padding-left: 5%;
                padding-right: 5%;
            }

            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                padding: 25px 7%;
                background: white;
                box-shadow: 0 12px 20px rgba(0,0,0,.07);
                flex-direction: column;
                align-items: stretch;
                gap: 20px;
            }

            .nav-links.open {
                display: flex;
            }

            .nav-links .nav-cta {
                text-align: center;
            }

            .about {
                grid-template-columns: 1fr;
                gap: 35px;
            }

            .feature-grid,
            .process-grid {
                grid-template-columns: repeat(2,1fr);
            }

            .hero {
                padding-left: 6%;
                padding-right: 6%;
            }
        }

        @media(max-width: 600px) {
            .hero {
                min-height: 780px;
                padding-top: 120px;
            }

            .hero h1 {
                font-size: 40px;
                letter-spacing: -1.7px;
            }

            .hero-description {
                font-size: 14px;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-main,
            .btn-outline {
                width: 100%;
            }

            .section {
                padding: 75px 6%;
            }

            .feature-grid,
            .process-grid {
                grid-template-columns: 1fr;
            }

            .about-content h2 {
                font-size: 29px;
            }

            .about-visual {
                min-height: 260px;
            }

            .building {
                font-size: 90px;
            }

            .facility-card {
                width: 220px;
            }

            .cta {
                padding: 50px 5%;
            }

            footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media(prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }

            .reveal {
                opacity: 1;
                transform: none;
            }
        }

        /* =====================================
           TENTANG SIMFAS - MODERN GLASS DESIGN
        ===================================== */

        .simfas-about {
            position: relative;
            padding: 110px 7%;
            background:
                radial-gradient(circle at 8% 20%, #e7f4ff 0, transparent 30%),
                radial-gradient(circle at 90% 85%, #edf7ff 0, transparent 30%),
                #ffffff;
            overflow: hidden;
        }

        .about-container {
            max-width: 1350px;
            margin: auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 85px;
        }

        /* VISUAL COLUMN */

        .about-visual-column {
            position: relative;
            min-width: 0;
            perspective: 1200px;
        }

        .about-glow {
            position: absolute;
            width: 75%;
            height: 75%;
            top: 12%;
            left: 12%;
            background: #80c7ff;
            filter: blur(90px);
            opacity: .28;
            border-radius: 50%;
        }

        .about-visual-card {
            position: relative;
            z-index: 2;
            min-height: 420px;
            padding: 14px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,.9);
            background: rgba(255,255,255,.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow:
                0 25px 70px rgba(47,113,165,.13),
                inset 0 1px 1px rgba(255,255,255,.9);
            animation: aboutFloat 5s ease-in-out infinite;
            transform-style: preserve-3d;
        }

        /* IMAGE */

        .about-image {
            height: 390px;
            position: relative;
            overflow: hidden;
            border-radius: 23px;
        }

        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scale(1.04);
            transition: transform .8s ease;
        }

        .about-visual-card:hover .about-image img {
            transform: scale(1.1);
        }

        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                transparent 40%,
                rgba(16,54,88,.55)
            );
        }

        .image-caption {
            position: absolute;
            bottom: 20px;
            left: 22px;
            color: white;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .caption-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6ee7b7;
            box-shadow: 0 0 12px #6ee7b7;
        }

        /* DASHBOARD PREVIEW */

        .dashboard-preview {
            position: absolute;
            z-index: 4;
            width: 265px;
            left: -35px;
            bottom: -42px;
            padding: 19px;
            border-radius: 19px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(255,255,255,.95);
            box-shadow: 0 18px 45px rgba(30,83,125,.16);
            backdrop-filter: blur(20px);
            transform: translateZ(55px);
        }

        .dashboard-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 14px;
            border-bottom: 1px solid #edf2f7;
        }

        .dashboard-brand {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .dashboard-logo {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            background: #e6f3ff;
            color: #328bd0;
            border-radius: 10px;
            font-weight: 800;
        }

        .dashboard-brand strong,
        .dashboard-brand small {
            display: block;
        }

        .dashboard-brand strong {
            color: #214a6d;
            font-size: 12px;
        }

        .dashboard-brand small {
            color: #91a2b1;
            font-size: 9px;
        }

        .dashboard-status {
            color: #20a879;
            font-size: 10px;
            font-weight: 700;
        }

        .dashboard-title {
            margin: 15px 0;
        }

        .dashboard-title strong,
        .dashboard-title small {
            display: block;
        }

        .dashboard-title strong {
            color: #254b6d;
            font-size: 13px;
        }

        .dashboard-title small {
            color: #8b9eaf;
            font-size: 10px;
            margin-top: 4px;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px;
        }

        .mini-stat {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 8px;
            background: #f4f9fe;
            border-radius: 11px;
        }

        .mini-stat-icon {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            font-size: 13px;
        }

        .mini-stat-icon.blue {
            background: #deefff;
            color: #368bd0;
        }

        .mini-stat-icon.green {
            background: #e0f8ee;
            color: #16a36e;
        }

        .mini-stat strong,
        .mini-stat small {
            display: block;
        }

        .mini-stat strong {
            font-size: 14px;
            color: #21496d;
        }

        .mini-stat small {
            font-size: 8px;
            color: #8b9eaf;
        }

        .dashboard-progress {
            margin-top: 16px;
        }

        .progress-heading {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #8297a9;
            margin-bottom: 8px;
        }

        .progress-heading strong {
            color: #368bd0;
        }

        .progress-track {
            height: 6px;
            background: #e7f1f9;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-fill {
            width: 85%;
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg,#368bd0,#7dc5f5);
            animation: progressGrow 2s ease-out both;
        }

        /* FLOATING BADGE */

        .floating-badge {
            position: absolute;
            z-index: 5;
            right: -35px;
            bottom: 45px;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 14px 18px;
            background: rgba(255,255,255,.87);
            border: 1px solid white;
            border-radius: 15px;
            box-shadow: 0 12px 35px rgba(38,98,146,.13);
            backdrop-filter: blur(16px);
            transform: translateZ(75px);
        }

        .badge-icon {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: #e6f4ff;
            color: #328bd0;
            font-size: 18px;
        }

        .floating-badge strong,
        .floating-badge small {
            display: block;
        }

        .floating-badge strong {
            color: #214a6d;
            font-size: 11px;
        }

        .floating-badge small {
            color: #879bab;
            font-size: 9px;
            margin-top: 4px;
        }

        /* DECORATION */

        .visual-decoration {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .decoration-one {
            width: 110px;
            height: 110px;
            top: -40px;
            left: -35px;
            background: rgba(120,194,245,.14);
            animation: aboutFloat 6s ease-in-out infinite alternate;
        }

        .decoration-two {
            width: 75px;
            height: 75px;
            right: -20px;
            top: 25px;
            border: 12px solid rgba(108,184,238,.12);
            animation: aboutFloat 4s ease-in-out infinite reverse;
        }

        /* TEXT COLUMN */

        .about-text-column {
            position: relative;
            z-index: 2;
        }

        .about-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #368bd0;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .about-label span {
            width: 28px;
            height: 3px;
            border-radius: 10px;
            background: #368bd0;
        }

        .about-text-column h2 {
            color: #193f60;
            font-size: clamp(30px,3.4vw,45px);
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: -1.6px;
            margin-bottom: 23px;
        }

        .about-text-column h2 span {
            color: #368bd0;
        }

        .about-description {
            color: #74899c;
            font-size: 14px;
            line-height: 2;
            margin-bottom: 27px;
        }

        /* BENEFITS */

        .about-benefits {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 13px;
        }

        .benefit-check {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #328bd0;
            background: #e7f4ff;
            border-radius: 50%;
            font-size: 15px;
            font-weight: 800;
            transition: .3s;
        }

        .benefit-item:hover .benefit-check {
            color: white;
            background: #368bd0;
            transform: rotate(360deg);
        }

        .benefit-item strong,
        .benefit-item small {
            display: block;
        }

        .benefit-item strong {
            color: #254a69;
            font-size: 13px;
            margin: 4px 0 5px;
        }

        .benefit-item small {
            color: #91a1af;
            font-size: 11px;
            line-height: 1.6;
        }

        /* BUTTON */

        .about-button {
            display: inline-flex;
            align-items: center;
            gap: 18px;
            padding: 14px 23px;
            border-radius: 12px;
            background: #368bd0;
            color: white;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(54,139,208,.2);
            transition: .3s;
        }

        .about-button:hover {
            color: white;
            background: #236da9;
            transform: translateY(-4px);
            box-shadow: 0 13px 25px rgba(54,139,208,.3);
        }

        .about-button span {
            transition: transform .3s;
        }

        .about-button:hover span {
            transform: translateX(5px);
        }

        /* ANIMATIONS */

        @keyframes aboutFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes progressGrow {
            from { width: 0; }
            to { width: 85%; }
        }

        /* RESPONSIVE */

        @media(max-width: 1000px) {
            .about-container {
                gap: 55px;
            }

            .dashboard-preview {
                left: -15px;
            }

            .floating-badge {
                right: -15px;
            }
        }

        @media(max-width: 768px) {
            .simfas-about {
                padding: 85px 6%;
            }

            .about-container {
                grid-template-columns: 1fr;
                gap: 85px;
            }

            .about-visual-card {
                min-height: 350px;
            }

            .about-image {
                height: 330px;
            }

            .about-text-column h2 {
                font-size: 34px;
            }

            .dashboard-preview {
                width: 235px;
                left: -5px;
                bottom: -55px;
            }

            .floating-badge {
                right: -5px;
                bottom: 25px;
            }
        }

        @media(max-width: 480px) {
            .about-visual-card {
                padding: 9px;
                border-radius: 22px;
            }

            .about-image {
                height: 270px;
            }

            .dashboard-preview {
                width: 205px;
                padding: 13px;
                left: -5px;
            }

            .floating-badge {
                padding: 10px;
                right: -5px;
                gap: 7px;
            }

            .floating-badge small {
                font-size: 8px;
            }

            .about-text-column h2 {
                font-size: 29px;
            }
        }

        @media(prefers-reduced-motion: reduce) {
            .about-visual-card,
            .visual-decoration,
            .progress-fill {
                animation: none !important;
            }
        }
    /* SIMFAS FEATURE SECTION - ANIMATED MESH */
.features {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    background:
        radial-gradient(ellipse at 12% 18%, rgba(91,183,245,.17), transparent 38%),
        radial-gradient(ellipse at 88% 78%, rgba(90,220,225,.17), transparent 40%),
        linear-gradient(135deg, #f4faff, #edf7ff 50%, #f8fcff) !important;
}

.features::before,
.features::after {
    content: "";
    position: absolute;
    z-index: 0;
    width: 390px;
    height: 390px;
    border-radius: 50%;
    filter: blur(65px);
    opacity: .55;
    pointer-events: none;
    animation: simfasBlob 16s ease-in-out infinite alternate;
}

.features::before {
    background: rgba(81,170,245,.28);
    top: 5%;
    left: -150px;
}

.features::after {
    background: rgba(77,213,220,.25);
    right: -150px;
    bottom: 0;
    animation-delay: -7s;
}

@keyframes simfasBlob {
    0% { transform: translate(0,0) scale(1); }
    50% { transform: translate(65px,35px) scale(1.13); }
    100% { transform: translate(-25px,75px) scale(.94); }
}

.features > .section-heading,
.features > .feature-grid {
    position: relative;
    z-index: 1;
}

/* Icon micro interaction */
.feature-icon {
    transform-origin: center;
    transition:
        transform .35s cubic-bezier(.2,.8,.2,1),
        background .35s ease,
        box-shadow .35s ease;
    will-change: transform;
}

.feature-card:hover .feature-icon {
    transform: translateY(-5px) rotate(-8deg) scale(1.14);
    box-shadow: 0 12px 26px rgba(54,139,208,.28);
}

@media (prefers-reduced-motion: reduce) {
    .features::before,
    .features::after {
        animation: none !important;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <a href="/" class="logo">SIMFAS<span>.</span> Kampus</a>

    <button class="menu-toggle" id="menuToggle" aria-label="Buka menu" aria-expanded="false">
        ☰
    </button>

    <div class="nav-links" id="navLinks">
        <a href="#beranda">Beranda</a>
        <a href="#tentang">Tentang</a>
        <a href="#layanan">Layanan</a>
        <a href="#fasilitas">Fasilitas</a>
        <a href="#alur">Cara Kerja</a>
        <a href="/login" class="nav-cta">Masuk Aplikasi →</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero" id="beranda">

    @php($heroVideo = config('services.landing_video_url') ?: asset('videos/campus-tech.mp4'))
    <!-- Video hero dimuat dari URL online: isi LANDING_VIDEO_URL di file .env -->
    <video class="hero-video" autoplay muted loop playsinline preload="metadata"
           poster="{{ asset('images/campus-poster.jpg') }}">
        <source src="{{ $heroVideo }}" type="video/mp4" onerror="this.parentElement.style.display='none'">
    </video>

    <div class="hero-overlay"></div>
    <div class="hero-decoration"></div>

    <div class="hero-content">
        <div class="hero-tag">
            ✦ SISTEM INFORMASI FASILITAS KAMPUS
        </div>

        <h1>
            Kelola Fasilitas Kampus
            <span>Jadi Lebih Mudah.</span>
        </h1>

        <p class="hero-description">
            SIMFAS Kampus membantu pengelolaan fasilitas dan peminjaman
            sarana kampus secara lebih mudah, teratur, dan transparan.
        </p>

        <div class="hero-buttons">
            <a href="/login" class="btn-main">
                Mulai Sekarang <span>→</span>
            </a>

            <a href="#tentang" class="btn-outline">
                Pelajari Lebih Lanjut ↓
            </a>
        </div>

        <div class="hero-note">
            ✧ Satu sistem untuk pengelolaan fasilitas kampus yang lebih terarah.
        </div>
    </div>
</section>

<!-- TENTANG SIMFAS -->
<section class="simfas-about" id="tentang">

    <div class="about-container">

        <!-- KOLOM KIRI: VISUAL INTERAKTIF -->
        <div class="about-visual-column">

            <div class="about-glow"></div>

            <div class="about-visual-card" data-tilt
                 data-tilt-max="8"
                 data-tilt-speed="700"
                 data-tilt-glare
                 data-tilt-max-glare="0.18">

                <!-- Foto utama -->
                <div class="about-image">
                    <img
                        src="{{ asset('images/facilities/kelas.jpg') }}"
                        alt="Fasilitas dan lingkungan kampus"
                    >

                    <div class="image-overlay"></div>

                    <div class="image-caption">
                        <span class="caption-dot"></span>
                        SIMFAS CAMPUS MANAGEMENT
                    </div>
                </div>

                <!-- Dashboard preview -->
                <div class="dashboard-preview" data-tilt data-tilt-disable-axis="true">

                    <div class="dashboard-top">
                        <div class="dashboard-brand">
                            <span class="dashboard-logo">S</span>
                            <div>
                                <strong>SIMFAS</strong>
                                <small>Campus System</small>
                            </div>
                        </div>

                        <span class="dashboard-status">● Aktif</span>
                    </div>

                    <div class="dashboard-title">
                        <strong>Ringkasan Fasilitas</strong>
                        <small>Dashboard Pengelolaan</small>
                    </div>

                    <div class="dashboard-stats">

                        <div class="mini-stat">
                            <span class="mini-stat-icon blue">▦</span>
                            <div>
                                <strong>04</strong>
                                <small>Total Fasilitas</small>
                            </div>
                        </div>

                        <div class="mini-stat">
                            <span class="mini-stat-icon green">✓</span>
                            <div>
                                <strong>04</strong>
                                <small>Tersedia</small>
                            </div>
                        </div>

                    </div>

                    <div class="dashboard-progress">
                        <div class="progress-heading">
                            <span>Kondisi Fasilitas</span>
                            <strong>Terpantau</strong>
                        </div>

                        <div class="progress-track">
                            <div class="progress-fill"></div>
                        </div>
                    </div>

                </div>

                <!-- Floating badge -->
                <div class="floating-badge" data-tilt data-tilt-speed="500">
                    <span class="badge-icon">✦</span>
                    <div>
                        <strong>Terintegrasi</strong>
                        <small>Pengelolaan lebih mudah</small>
                    </div>
                </div>

            </div>

            <div class="visual-decoration decoration-one"></div>
            <div class="visual-decoration decoration-two"></div>

        </div>


        <!-- KOLOM KANAN: INFORMASI -->
        <div class="about-text-column">

            <div class="about-label">
                <span></span>
                TENTANG SIMFAS
            </div>

            <h2>
                Pengelolaan Fasilitas Kampus
                <span>dalam Satu Sistem.</span>
            </h2>

            <p class="about-description">
                SIMFAS Kampus merupakan Sistem Informasi Fasilitas Kampus
                yang dirancang untuk membantu pengelolaan sarana dan
                prasarana secara lebih terstruktur. Melalui sistem ini,
                pengguna dapat mengakses informasi fasilitas, mengajukan
                peminjaman, serta membantu proses pemantauan kondisi
                fasilitas kampus.
            </p>

            <div class="about-benefits">

                <div class="benefit-item">
                    <div class="benefit-check">✓</div>
                    <div>
                        <strong>Informasi fasilitas lebih terorganisir</strong>
                        <small>Data fasilitas tersusun secara lebih sistematis.</small>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-check">✓</div>
                    <div>
                        <strong>Pengajuan peminjaman lebih mudah</strong>
                        <small>Proses pengajuan dapat dilakukan melalui sistem.</small>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-check">✓</div>
                    <div>
                        <strong>Pengelolaan prioritas pemeliharaan</strong>
                        <small>Membantu menentukan fasilitas yang perlu diperhatikan.</small>
                    </div>
                </div>

            </div>

            <a href="/login" class="about-button">
                Jelajahi SIMFAS
                <span>→</span>
            </a>

        </div>

    </div>
</section>

<!-- LAYANAN -->
<section class="section features" id="layanan">

    <div class="section-heading reveal">
        <span class="eyebrow">LAYANAN SISTEM</span>
        <h2>Semua Kebutuhan dalam Satu Platform</h2>
        <p>
            Fitur SIMFAS Kampus dirancang untuk mendukung pengelolaan
            fasilitas dan proses peminjaman secara lebih praktis.
        </p>
    </div>

    <div class="feature-grid">

        <div class="feature-card reveal">
            <div class="feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M3 21h18M5 21V5l7-2v18M19 21V9l-7-2"></path>
                    <path d="M8 7h1M8 11h1M8 15h1M15 11h1M15 15h1"></path>
                </svg>
            </div>
            <h3>Data Fasilitas</h3>
            <p>
                Mengelola informasi fasilitas, kategori, lokasi,
                jumlah unit, dan kondisi fasilitas kampus.
            </p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                    <path d="M16 3v4M8 3v4M3 10h18M8 14h3M8 17h7"></path>
                </svg>
            </div>
            <h3>Peminjaman Fasilitas</h3>
            <p>
                Mengajukan peminjaman fasilitas dan memantau
                status pengajuan melalui sistem.
            </p>
        </div>

        <div class="feature-card reveal">
            <div class="feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M4 20V10h4v10M10 20V4h4v16M16 20v-7h4v7M2 20h20"></path>
                </svg>
            </div>
            <h3>Prioritas SAW</h3>
            <p>
                Mendukung penentuan prioritas pemeliharaan
                fasilitas menggunakan metode Simple Additive Weighting.
            </p>
        </div>

    </div>
</section>

<!-- GALERI BERJALAN -->
<section class="gallery" id="fasilitas">

    <div class="section-heading reveal">
        <span class="eyebrow">FASILITAS KAMPUS</span>
        <h2>Kenali Fasilitas yang Dikelola</h2>
        <p>Berbagai sarana kampus yang dapat dikelola melalui SIMFAS Kampus.</p>
    </div>

    <div class="marquee">
        <div class="marquee-track">

            <div class="facility-card">
                <img src="{{ asset('images/facilities/kelas.jpg') }}"
                     alt="Ruang Kelas">

                <div class="facility-info">
                    <h3>Ruang Kelas</h3>
                    <p>Fasilitas ruang pembelajaran</p>
                </div>
            </div>

            <div class="facility-card">
                <img src="{{ asset('images/facilities/laboratorium.jpg') }}"
                     alt="Laboratorium Komputer">

                <div class="facility-info">
                    <h3>Laboratorium Komputer</h3>
                    <p>Sarana praktikum mahasiswa</p>
                </div>
            </div>

            <div class="facility-card">
                <img src="{{ asset('images/facilities/proyektor.jpg') }}"
                     alt="Proyektor">

                <div class="facility-info">
                    <h3>Proyektor</h3>
                    <p>Peralatan pendukung presentasi</p>
                </div>
            </div>

            <div class="facility-card">
                <img src="{{ asset('images/facilities/aula.jpg') }}"
                     alt="Aula Kampus">

                <div class="facility-info">
                    <h3>Aula Kampus</h3>
                    <p>Ruang kegiatan dan pertemuan</p>
                </div>
            </div>

            <div class="facility-card">
                <img src="{{ asset('images/facilities/sound-system.jpg') }}"
                     alt="Sound System">

                <div class="facility-info">
                    <h3>Sound System</h3>
                    <p>Peralatan pendukung kegiatan</p>
                </div>
            </div>

            <div class="facility-card">
                <img src="{{ asset('images/facilities/peralatan.jpg') }}"
                     alt="Peralatan Ruangan">

                <div class="facility-info">
                    <h3>Peralatan Ruangan</h3>
                    <p>Sarana pendukung aktivitas</p>
                </div>
            </div>

            <!-- Duplikat untuk animasi tanpa putus -->
            <div class="facility-card" aria-hidden="true">
                <img src="{{ asset('images/facilities/kelas.jpg') }}"
                     alt="">

                <div class="facility-info">
                    <h3>Ruang Kelas</h3>
                    <p>Fasilitas ruang pembelajaran</p>
                </div>
            </div>

            <div class="facility-card" aria-hidden="true">
                <img src="{{ asset('images/facilities/laboratorium.jpg') }}"
                     alt="">

                <div class="facility-info">
                    <h3>Laboratorium Komputer</h3>
                    <p>Sarana praktikum mahasiswa</p>
                </div>
            </div>

            <div class="facility-card" aria-hidden="true">
                <img src="{{ asset('images/facilities/proyektor.jpg') }}"
                     alt="">

                <div class="facility-info">
                    <h3>Proyektor</h3>
                    <p>Peralatan pendukung presentasi</p>
                </div>
            </div>

            <div class="facility-card" aria-hidden="true">
                <img src="{{ asset('images/facilities/aula.jpg') }}"
                     alt="">

                <div class="facility-info">
                    <h3>Aula Kampus</h3>
                    <p>Ruang kegiatan dan pertemuan</p>
                </div>
            </div>

            <div class="facility-card" aria-hidden="true">
                <img src="{{ asset('images/facilities/sound-system.jpg') }}"
                     alt="">

                <div class="facility-info">
                    <h3>Sound System</h3>
                    <p>Peralatan pendukung kegiatan</p>
                </div>
            </div>

            <div class="facility-card" aria-hidden="true">
                <img src="{{ asset('images/facilities/peralatan.jpg') }}"
                     alt="">

                <div class="facility-info">
                    <h3>Peralatan Ruangan</h3>
                    <p>Sarana pendukung aktivitas</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CARA KERJA -->
<section class="section process" id="alur">

    <div class="section-heading reveal">
        <span class="eyebrow">CARA PENGGUNAAN</span>
        <h2>Mulai dengan Tiga Langkah Mudah</h2>
        <p>
            Proses penggunaan sistem yang sederhana
            untuk membantu kebutuhan fasilitas kampus.
        </p>
    </div>

    <div class="process-grid">

        <div class="process-card reveal">
            <div class="process-number">01</div>
            <h3>Masuk ke Sistem</h3>
            <p>
                Login menggunakan akun sesuai peran pengguna
                yang telah terdaftar.
            </p>
        </div>

        <div class="process-card reveal">
            <div class="process-number">02</div>
            <h3>Pilih Fasilitas</h3>
            <p>
                Lihat data fasilitas dan ajukan peminjaman
                sesuai kebutuhan.
            </p>
        </div>

        <div class="process-card reveal">
            <div class="process-number">03</div>
            <h3>Pantau Pengajuan</h3>
            <p>
                Periksa perkembangan dan status pengajuan
                melalui akun masing-masing.
            </p>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="cta-box reveal">
        <h2>Mulai Pengelolaan Fasilitas Lebih Mudah</h2>
        <p>
            Akses SIMFAS Kampus untuk menggunakan layanan
            pengelolaan fasilitas kampus.
        </p>
        <a href="/login" class="btn-main">
            Masuk ke SIMFAS <span>→</span>
        </a>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div>
        <strong>SIMFAS<span style="color:#368bd0">.</span> Kampus</strong>
        <p>Sistem Informasi Fasilitas Kampus</p>
    </div>

    <div class="footer-copy">
        © 2026 SIMFAS Kampus. All Rights Reserved.
    </div>
</footer>

<script>
    // Navbar ketika halaman digulir
    const navbar = document.getElementById('navbar');

    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    });

    // Menu mobile
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    menuToggle.addEventListener('click', () => {
        const isOpen = navLinks.classList.toggle('open');
        menuToggle.setAttribute('aria-expanded', isOpen);
        menuToggle.textContent = isOpen ? '✕' : '☰';
    });

    // Tutup menu setelah memilih navigasi
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.textContent = '☰';
        });
    });

    // Animasi elemen ketika masuk ke layar
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.12
    });

    document.querySelectorAll('.reveal').forEach(element => {
        observer.observe(element);
    });

    // Parallax lembut pada video hero
    const heroVideo = document.querySelector('.hero-video');

    if (heroVideo && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.addEventListener('scroll', () => {
            const offset = Math.min(window.scrollY * 0.12, 100);
            heroVideo.style.objectPosition = `center calc(50% + ${offset}px)`;
        }, { passive: true });
    }

    // Spotlight kartu fitur mengikuti posisi pointer.
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('pointermove', event => {
            const bounds = card.getBoundingClientRect();
            card.style.setProperty('--pointer-x', `${event.clientX - bounds.left}px`);
            card.style.setProperty('--pointer-y', `${event.clientY - bounds.top}px`);
        });

        card.addEventListener('pointerleave', () => {
            card.style.setProperty('--pointer-x', '50%');
            card.style.setProperty('--pointer-y', '50%');
        });
    });
</script>

<script>
    /* SIMFAS 3D TILT EFFECT */

    (function () {

        function initializeTilt() {

            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            if (window.VanillaTilt) {
                VanillaTilt.init(
                    document.querySelectorAll('.about-visual-card, .dashboard-preview, .floating-badge'),
                    {
                        max: 8,
                        speed: 700,
                        glare: true,
                        'max-glare': 0.18,
                        scale: 1.015,
                        perspective: 1000
                    }
                );
            }
        }

        function loadTilt() {

            if (window.VanillaTilt) {
                initializeTilt();
                return;
            }

            const script = document.createElement('script');

            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js';

            script.onload = initializeTilt;

            script.onerror = function () {
                console.warn('Vanilla Tilt tidak dapat dimuat. Animasi floating tetap aktif.');
            };

            document.head.appendChild(script);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadTilt);
        } else {
            loadTilt();
        }

    })();
</script>

<script>
(function () {
    const cards = document.querySelectorAll('.feature-card');

    cards.forEach(card => {
        const icon = card.querySelector('.feature-icon');
        if (!icon) return;

        card.addEventListener('pointermove', event => {
            if (event.pointerType !== 'mouse') return;

            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            const distance = Math.sqrt(x * x + y * y);
            const strength = Math.max(0, 1 - distance * 1.7);

            icon.style.transform =
                `translate(${x * 9 * strength}px, ${-5 - y * 7 * strength}px) rotate(${x * 9 * strength}deg) scale(${1 + .12 * strength})`;
        });

        card.addEventListener('pointerleave', () => {
            icon.style.transform = '';
        });
    });
})();
</script>
</body>
</html>


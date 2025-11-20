@extends('layouts.app')

@section('content')
    <section class="home-section">
        <!-- Add these animated background elements -->
        <div class="animated-background">
            <!-- Floating Shapes -->
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
            <div class="floating-shape shape-4"></div>

            <!-- Pulsing Orbs -->
            <div class="pulsing-orb orb-1"></div>
            <div class="pulsing-orb orb-2"></div>

            <!-- Grid Lines -->
            <div class="grid-lines"></div>

            <!-- Floating Particles -->
            <div class="particles-container">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>

            <!-- Animated Gradient Background -->
            <div class="gradient-bg"></div>
        </div>

        <div class="bg-optimized"></div>
        <div class="container">
            <!-- Your existing content remains the same -->
            <div class="home-content">
                <div class="home-text">
                    <div class="pro-badge">
                        <div class="badge-dot"></div>
                        <span>Available for Work</span>
                    </div>

                    <h1 class="main-heading">
                        <span class="greeting">Hi, I'm</span>
                        <span class="name">Baraa Al-Rifaee</span>
                    </h1>

                    <div class="profession">
                        <span class="static">I'm a </span>
                        <span class="dynamic" id="dynamicText">Full Stack Developer</span>
                    </div>

                    <p class="description">
                        I create <span class="highlight">digital experiences</span> that combine
                        modern design with cutting-edge technology. Transforming ideas
                        into scalable, user-friendly solutions.
                    </p>

                    <div class="cta-buttons">
                        <a href="{{ route('contact') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Start Project
                        </a>
                        <a href="{{ route('download.cv') }}?v={{ time() }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i>
                            Download CV
                        </a>
                    </div>
                </div>

                <div class="home-image">
                    <div class="profile-optimized">
                        <div class="profile-img">
                            <picture>
                                <source srcset="{{ asset('images/profile/about3.jpg') }}" type="image/webp">
                                <source srcset="{{ asset('images/profile/about3.jpg') }}" type="image/jpeg">
                                <img src="{{ asset('images/profile/about3.jpg') }}"
                                    alt="Baraa Al-Rifaee - Full Stack Developer">
                            </picture>
                        </div>
                        <!-- Minimal Tech Icons -->
                        <div class="tech-icon">
                            <i class="fab fa-react"></i>
                        </div>
                        <div class="tech-icon">
                            <i class="fab fa-node-js"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Optimized Skills Preview -->
    <style>
        /* === PROFESSIONAL DASHBOARD-STYLE CSS WITH BACKGROUND ANIMATIONS === */
        :root {
            --primary: #1a365d;
            --primary-light: #2d4a8a;
            --primary-dark: #0f1e3d;
            --secondary: #2d3748;
            --accent: #4c6fff;
            --dark: #1a202c;
            --darker: #0f1419;
            --light: #ffffff;
            --gray: #f7fafc;
            --gray-dark: #718096;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            --gradient-dark: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);
            --shadow: 0 10px 30px rgba(26, 54, 93, 0.15);
            --shadow-lg: 0 20px 50px rgba(26, 54, 93, 0.25);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Reset and Base Styles */
        .home-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            /* background: var(--gradient-dark); */
            overflow: hidden;
            padding: 2rem 0;
        }

        /* Enhanced Animated Background Elements */
        .animated-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
            opacity: 0.3;
        }

        /* Floating Shapes */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s infinite linear;
            filter: blur(1px);
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--accent);
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--primary);
            top: 60%;
            left: 80%;
            animation-delay: -5s;
            animation-duration: 25s;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--primary-light);
            top: 20%;
            left: 70%;
            animation-delay: -10s;
            animation-duration: 30s;
        }

        .shape-4 {
            width: 100px;
            height: 100px;
            background: var(--light);
            top: 80%;
            left: 10%;
            animation-delay: -15s;
            animation-duration: 35s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(20px, 50px) rotate(90deg);
            }

            50% {
                transform: translate(0, 100px) rotate(180deg);
            }

            75% {
                transform: translate(-20px, 50px) rotate(270deg);
            }

            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        /* Pulsing Orbs */
        .pulsing-orb {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
            animation: pulse 8s infinite ease-in-out;
            filter: blur(15px);
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: var(--accent);
            top: 50%;
            left: 20%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 250px;
            height: 250px;
            background: var(--primary-light);
            top: 30%;
            left: 60%;
            animation-delay: -2s;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.05;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.08;
            }
        }

        /* Grid Lines */
        .grid-lines {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(77, 111, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(77, 111, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 40s linear infinite;
        }

        @keyframes gridMove {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Animated Gradient Background */
        .gradient-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(-45deg, var(--darker), var(--dark), var(--primary-dark), var(--secondary));
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            z-index: -3;
            opacity: 0.2;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Floating Particles */
        .particles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--light);
            border-radius: 50%;
            opacity: 0.3;
            animation: particleFloat 20s infinite linear;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0);
            }

            100% {
                transform: translateY(-100px) translateX(20px);
            }
        }

        /* Create multiple particles with different properties */
        .particle:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
            animation-duration: 25s;
        }

        .particle:nth-child(2) {
            left: 20%;
            animation-delay: 2s;
            animation-duration: 20s;
        }

        .particle:nth-child(3) {
            left: 30%;
            animation-delay: 4s;
            animation-duration: 30s;
        }

        .particle:nth-child(4) {
            left: 40%;
            animation-delay: 6s;
            animation-duration: 18s;
        }

        .particle:nth-child(5) {
            left: 50%;
            animation-delay: 8s;
            animation-duration: 22s;
        }

        .particle:nth-child(6) {
            left: 60%;
            animation-delay: 10s;
            animation-duration: 28s;
        }

        .particle:nth-child(7) {
            left: 70%;
            animation-delay: 12s;
            animation-duration: 24s;
        }

        .particle:nth-child(8) {
            left: 80%;
            animation-delay: 14s;
            animation-duration: 26s;
        }

        /* Main Animated Background */
        .home-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(76, 111, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(26, 54, 93, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -2;
            animation: backgroundPulse 8s ease-in-out infinite;
        }

        /* Container & Content */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .home-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            width: 100%;
        }

        /* === ENHANCED TEXT CONTENT === */
        .home-text {
            position: relative;
            z-index: 15;
            animation: slideInLeft 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Professional Badge */
        .pro-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(76, 111, 255, 0.15);
            color: var(--accent);
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            border: 1px solid rgba(76, 111, 255, 0.3);
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(20px);
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        .pro-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.3), transparent);
            transition: var(--transition);
        }

        .pro-badge:hover::before {
            left: 100%;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        /* Enhanced Typography */
        .main-heading {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1rem;
            color: var(--light);
            letter-spacing: -0.5px;
            animation: fadeInUp 0.6s ease-out 0.1s both;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .greeting {
            display: block;
            color: var(--light);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .name {
            display: block;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profession {
            font-size: clamp(1.25rem, 3vw, 1.75rem);
            color: var(--gray);
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .dynamic {
            color: var(--accent);
            font-weight: 700;
        }

        .description {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--gray);
            line-height: 1.6;
            margin-bottom: 2.5rem;
            max-width: 500px;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .highlight {
            color: var(--accent);
            font-weight: 700;
        }

        /* Enhanced Buttons */
        .cta-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            z-index: 2;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient);
            color: var(--light);
            box-shadow: 0 8px 25px rgba(76, 111, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(76, 111, 255, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            border: 2px solid rgba(76, 111, 255, 0.3);
            backdrop-filter: blur(20px);
        }

        .btn-secondary:hover {
            background: rgba(76, 111, 255, 0.2);
            transform: translateY(-3px);
            border-color: var(--accent);
        }

        /* Enhanced Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        .stat {
            text-align: center;
            padding: 1.25rem;
            background: linear-gradient(145deg, rgba(26, 54, 93, 0.4), rgba(15, 20, 25, 0.6));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(20px);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.1), transparent);
            transition: var(--transition);
        }

        .stat:hover::before {
            left: 100%;
        }

        .stat:hover {
            transform: translateY(-5px);
            border-color: rgba(76, 111, 255, 0.3);
            box-shadow: var(--shadow);
        }

        .number {
            font-size: 1.75rem;
            font-weight: 900;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        .label {
            color: var(--gray);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* === ENHANCED PROFILE SECTION === */
        .home-image {
            position: relative;
            display: flex;
            justify-content: center;
            animation: slideInRight 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .profile-optimized {
            position: relative;
            max-width: 320px;
            width: 100%;
        }

        .profile-img {
            width: 100%;
            aspect-ratio: 1;
            background: var(--gradient);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                var(--shadow-lg),
                inset 0 2px 0 rgba(255, 255, 255, 0.1);
            transition: var(--transition);
            overflow: hidden;
            position: relative;
            animation: morph 8s ease-in-out infinite, photoGlow 3s ease-in-out infinite alternate;
        }

        .profile-img::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(76, 111, 255, 0.1), transparent);
            animation: photoShine 6s ease-in-out infinite;
            z-index: 2;
        }

        .profile-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
            transition: var(--transition);
            filter: brightness(1.05) contrast(1.1);
            animation: photoFloat 6s ease-in-out infinite;
        }

        .profile-img:hover {
            transform: scale(1.05);
            animation-play-state: paused;
        }

        .profile-img:hover img {
            transform: scale(1.1);
            filter: brightness(1.1) contrast(1.2);
            animation-play-state: paused;
        }

        .profile-img:hover::before {
            animation-play-state: paused;
        }

        .tech-icon {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: float 6s ease-in-out infinite;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            z-index: 3;
        }

        .tech-icon:hover {
            transform: scale(1.1);
            background: rgba(76, 111, 255, 0.2);
            animation-play-state: paused;
        }

        .tech-icon:nth-child(2) {
            top: 15%;
            right: -5%;
            animation-delay: 0s;
        }

        .tech-icon:nth-child(3) {
            bottom: 25%;
            left: -5%;
            animation-delay: 3s;
        }

        .tech-icon i {
            font-size: 1.25rem;
            color: var(--light);
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
        }

        .arrow {
            width: 25px;
            height: 25px;
            border-right: 2px solid var(--accent);
            border-bottom: 2px solid var(--accent);
            transform: rotate(45deg);
            animation: bounce 2s infinite;
        }

        /* === ENHANCED ANIMATIONS === */
        @keyframes backgroundPulse {

            0%,
            100% {
                opacity: 0.8;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.02);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.1);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(5deg);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: rotate(45deg) translateY(0);
            }

            50% {
                transform: rotate(45deg) translateY(-8px);
            }
        }

        @keyframes morph {

            0%,
            100% {
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            }

            25% {
                border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%;
            }

            50% {
                border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%;
            }

            75% {
                border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%;
            }
        }

        @keyframes photoFloat {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-8px) scale(1.02);
            }
        }

        @keyframes photoGlow {
            0% {
                box-shadow:
                    var(--shadow),
                    inset 0 2px 0 rgba(255, 255, 255, 0.1),
                    0 0 20px rgba(76, 111, 255, 0.3);
            }

            100% {
                box-shadow:
                    var(--shadow),
                    inset 0 2px 0 rgba(255, 255, 255, 0.1),
                    0 0 30px rgba(76, 111, 255, 0.5);
            }
        }

        @keyframes photoShine {

            0%,
            100% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            50% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        /* === MOBILE-FIRST RESPONSIVE DESIGN === */

        /* Large Mobile (480px and up) */
        @media (max-width: 480px) {
            .home-section {
                padding: 1.5rem 0;
                min-height: 100vh;
            }

            .container {
                padding: 0 1rem;
            }

            .home-content {
                grid-template-columns: 1fr;
                gap: 2.5rem;
                text-align: center;
            }

            .home-image {
                order: -1;
            }

            .profile-optimized {
                max-width: 250px;
            }

            .pro-badge {
                font-size: 0.8rem;
                padding: 0.6rem 1rem;
                margin-bottom: 1.5rem;
            }

            .main-heading {
                font-size: clamp(1.75rem, 8vw, 2.5rem);
                margin-bottom: 0.75rem;
            }

            .profession {
                font-size: clamp(1.1rem, 5vw, 1.4rem);
                margin-bottom: 1rem;
            }

            .description {
                font-size: 0.95rem;
                line-height: 1.5;
                margin-bottom: 2rem;
                margin-left: auto;
                margin-right: auto;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 2.5rem;
            }

            .btn {
                width: 100%;
                max-width: 220px;
                justify-content: center;
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
            }

            .stats {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stat {
                padding: 1rem;
            }

            .number {
                font-size: 1.5rem;
            }

            .label {
                font-size: 0.8rem;
            }

            .tech-icon {
                width: 40px;
                height: 40px;
            }

            .tech-icon i {
                font-size: 1rem;
            }

            .tech-icon:nth-child(2) {
                top: 10%;
                right: -3%;
            }

            .tech-icon:nth-child(3) {
                bottom: 15%;
                left: -3%;
            }

            .scroll-indicator {
                bottom: 15px;
            }

            .arrow {
                width: 20px;
                height: 20px;
            }

            /* Optimize background animations for mobile */
            .floating-shape {
                width: 150px !important;
                height: 150px !important;
            }

            .pulsing-orb {
                width: 200px !important;
                height: 200px !important;
            }

            .particle {
                display: none;
            }
        }

        /* Tablet (768px and up) */
        @media (min-width: 768px) and (max-width: 1023px) {
            .home-section {
                padding: 3rem 0;
            }

            .container {
                padding: 0 2rem;
            }

            .home-content {
                gap: 3rem;
            }

            .profile-optimized {
                max-width: 300px;
            }

            .stats {
                gap: 1.25rem;
            }

            .tech-icon {
                width: 45px;
                height: 45px;
            }

            .tech-icon i {
                font-size: 1.1rem;
            }
        }

        /* Desktop (1024px and up) */
        @media (min-width: 1024px) {
            .home-section {
                padding: 4rem 0;
            }

            .home-content {
                gap: 4rem;
            }

            .profile-optimized {
                max-width: 380px;
            }
        }

        /* Large Desktop (1200px and up) */
        @media (min-width: 1200px) {
            .container {
                padding: 0 2rem;
            }
        }

        /* === ACCESSIBILITY & PERFORMANCE === */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            .animated-background {
                display: none;
            }

            .profile-img {
                animation: none !important;
            }

            .profile-img img {
                animation: none !important;
            }

            .tech-icon {
                animation: none !important;
            }
        }

        @media (hover: none) {

            .btn:hover,
            .stat:hover,
            .profile-img:hover,
            .tech-icon:hover {
                transform: none;
            }

            .btn::before,
            .stat::before,
            .pro-badge::before {
                display: none;
            }
        }

        @media (prefers-contrast: high) {
            .pro-badge {
                background: var(--accent);
                color: var(--light);
                border-color: var(--accent);
            }

            .stat {
                border-color: var(--accent);
            }
        }

        /* Touch device optimizations */
        @media (pointer: coarse) {

            .btn,
            .stat {
                min-height: 44px;
                /* Minimum touch target size */
            }

            .tech-icon {
                min-width: 44px;
                min-height: 44px;
            }
        }

        /* Landscape mobile optimization */
        @media (max-height: 600px) and (orientation: landscape) {
            .home-section {
                min-height: 120vh;
                padding: 1rem 0;
            }

            .home-content {
                gap: 2rem;
            }

            .profile-optimized {
                max-width: 200px;
            }

            .stats {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
            }

            .stat {
                padding: 0.75rem;
            }

            .number {
                font-size: 1.25rem;
            }

            .label {
                font-size: 0.7rem;
            }
        }
    </style>

    <script>
        // === OPTIMIZED JAVASCRIPT ===

        document.addEventListener('DOMContentLoaded', function () {
            // Efficient dynamic text rotation
            function initDynamicText() {
                const element = document.getElementById('dynamicText');
                if (!element) return;

                const words = ['Full Stack Web Developer', 'Problem Solver', 'Creative Thinker'];
                let wordIndex = 0;
                let charIndex = 0;
                let isDeleting = false;
                let typeSpeed = 100;

                function type() {
                    const currentWord = words[wordIndex];

                    if (isDeleting) {
                        element.textContent = currentWord.substring(0, charIndex - 1);
                        charIndex--;
                        typeSpeed = 50;
                    } else {
                        element.textContent = currentWord.substring(0, charIndex + 1);
                        charIndex++;
                        typeSpeed = 100;
                    }

                    if (!isDeleting && charIndex === currentWord.length) {
                        typeSpeed = 2000;
                        isDeleting = true;
                    } else if (isDeleting && charIndex === 0) {
                        isDeleting = false;
                        wordIndex = (wordIndex + 1) % words.length;
                        typeSpeed = 500;
                    }

                    setTimeout(type, typeSpeed);
                }

                type();
            }

            // Efficient counter animation
            function initCounters() {
                const counters = document.querySelectorAll('.number');
                if (!counters.length) return;

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const counter = entry.target;
                            const target = parseInt(counter.getAttribute('data-count'));
                            const duration = 2000;
                            const step = target / (duration / 16);
                            let current = 0;

                            function updateCounter() {
                                current += step;
                                if (current < target) {
                                    counter.textContent = Math.floor(current);
                                    requestAnimationFrame(updateCounter);
                                } else {
                                    counter.textContent = target;
                                }
                            }

                            updateCounter();
                            observer.unobserve(counter);
                        }
                    });
                }, { threshold: 0.5 });

                counters.forEach(counter => observer.observe(counter));
            }

            // Initialize only essential features
            initDynamicText();
            initCounters();

            // Touch device optimizations
            if ('ontouchstart' in window) {
                document.body.classList.add('touch-device');
            }

            // Reduced motion preference
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.body.classList.add('reduced-motion');
            }
        });
    </script>
@endsection

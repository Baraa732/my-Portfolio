<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - {{ $title ?? 'Home' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portfolio-animations.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animations-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/three-background.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
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
            --gradient-glass: linear-gradient(135deg, rgba(26, 54, 93, 0.1) 0%, rgba(76, 111, 255, 0.1) 100%);
            --shadow: 0 10px 30px rgba(26, 54, 93, 0.15);
            --shadow-lg: 0 20px 50px rgba(26, 54, 93, 0.25);
            --shadow-inset: inset 0 2px 4px rgba(0, 0, 0, 0.06);
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 20px;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.7;
            color: var(--light);
            background: var(--gradient-dark);
            /* min-height: 100vh; */
            overflow-x: hidden;
            font-weight: 400;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Enhanced Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
        }

        /* Professional Navigation */
        .navbar {
            /* background: rgba(15, 20, 25, 0.95); */
            /* backdrop-filter: blur(40px); */
            -webkit-backdrop-filter: blur(40px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1.5rem 0;
            /* border-bottom: 1px solid rgba(255, 255, 255, 0.1); */
            transition: var(--transition-slow);
            animation: slideDown 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar.scrolled {
            padding: 1rem 0;
            /* background: rgba(10, 15, 20, 0.98); */
            backdrop-filter: blur(50px);
            box-shadow: var(--shadow);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .logo {
            font-size: 2rem;
            font-weight: 900;
            color: var(--light);
            text-decoration: none;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: var(--transition);
            letter-spacing: -0.5px;
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.3), transparent);
            transition: var(--transition-slow);
        }

        .logo:hover::before {
            left: 100%;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 3rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            position: relative;
            padding: 0.5rem 0;
            letter-spacing: 0.3px;
        }

        .nav-links a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient);
            transition: var(--transition);
            border-radius: 1px;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient);
            opacity: 0;
            transition: var(--transition);
            border-radius: 1px;
        }

        .nav-links a:hover {
            color: var(--accent);
            transform: translateY(-2px);
        }

        .nav-links a:hover::before {
            width: 100%;
        }

        .nav-links a:hover::after {
            opacity: 0.3;
        }

        .nav-links a.active {
            color: var(--accent);
            font-weight: 700;
        }

        .nav-links a.active::before {
            width: 100%;
        }

        /* Enhanced Mobile Navigation */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--light);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            width: 45px;
            height: 45px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-menu-btn:hover {
            background: rgba(76, 111, 255, 0.1);
            color: var(--accent);
            transform: scale(1.05);
        }

        /* Professional Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            font-size: 1rem;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
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
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
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
            box-shadow: var(--shadow);
        }

        /* Enhanced Sections */
        section {
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }

        section::before {
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
            z-index: 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 5rem;
            position: relative;
            z-index: 2;
        }

        .section-title h2 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 900;
            color: var(--light);
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
            background: linear-gradient(135deg, var(--light) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
            line-height: 1.1;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
            animation: widthPulse 3s ease-in-out infinite;
        }

        .section-subtitle {
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            color: var(--gray);
            max-width: 600px;
            margin: 2rem auto 0;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Enhanced Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            33% {
                transform: translateY(-20px) rotate(2deg);
            }

            66% {
                transform: translateY(-10px) rotate(-2deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
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

        @keyframes widthPulse {

            0%,
            100% {
                width: 100px;
            }

            50% {
                width: 120px;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-fade-left {
            animation: fadeInLeft 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-fade-right {
            animation: fadeInRight 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }

        .animate-gradient {
            background: linear-gradient(-45deg, var(--primary), var(--accent), var(--secondary), var(--dark));
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .animate-slide-in-up {
            animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced Footer */
        .footer {
            background: var(--darker);
            padding: 4rem 0 2rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .footer::before {
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
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 2;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 55px;
            height: 55px;
            background: linear-gradient(145deg, rgba(26, 54, 93, 0.4), rgba(15, 20, 25, 0.6));
            color: var(--light);
            font-size: 1.3rem;
            border-radius: 50%;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.1);
            /* backdrop-filter: blur(20px); */
            position: relative;
            overflow: hidden;
        }

        .social-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.3), transparent);
            transition: var(--transition);
        }

        .social-links a:hover::before {
            left: 100%;
        }

        .social-links a:hover {
            background: var(--gradient);
            color: var(--light);
            transform: translateY(-5px) scale(1.1);
            box-shadow: var(--shadow);
            border-color: transparent;
        }

        .footer p {
            color: var(--gray);
            font-size: 0.95rem;
            position: relative;
            z-index: 2;
            font-weight: 500;
        }

        /* Enhanced Loading Animation */
        .loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: var(--gradient);
            z-index: 9999;
            transition: width 0.3s ease;
            box-shadow: 0 0 10px var(--accent);
        }

        /* Scrollbar Hiding with Enhanced Performance */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        * {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Enhanced Main Content Area */
        main {
            /* min-height: 100vh; */
            overflow-y: auto;
            overflow-x: hidden;
            -ms-overflow-style: none;
            scrollbar-width: none;
            position: relative;
        }

        main::-webkit-scrollbar {
            display: none;
        }

        /* Enhanced Mobile Responsive Design */
        @media (max-width: 1200px) {
            .container {
                max-width: 1140px;
                padding: 0 2rem;
            }

            .section-title h2 {
                font-size: clamp(2.2rem, 4vw, 3.5rem);
            }
        }

        @media (max-width: 992px) {
            .container {
                max-width: 960px;
                padding: 0 1.5rem;
            }

            .section-title h2 {
                font-size: clamp(2rem, 4vw, 3rem);
            }

            .nav-links {
                gap: 2rem;
            }

            .btn {
                padding: 0.875rem 2rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                max-width: 720px;
                padding: 0 1.5rem;
            }

            .navbar {
                padding: 1.2rem 0;
            }

            .nav-links {
                position: fixed;
                top: 80px;
                left: 0;
                width: 100%;
                background: rgba(10, 15, 20, 0.98);
                /* backdrop-filter: blur(40px); */
                flex-direction: column;
                padding: 2rem;
                gap: 1.5rem;
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: var(--transition-slow);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                max-height: calc(100vh - 80px);
                overflow-y: auto;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .nav-links::-webkit-scrollbar {
                display: none;
            }

            .nav-links.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .section-title h2 {
                font-size: clamp(1.8rem, 5vw, 2.5rem);
            }

            section {
                padding: 80px 0;
            }

            .social-links a {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 1rem;
                max-width: 540px;
            }

            .section-title h2 {
                font-size: clamp(1.6rem, 6vw, 2.2rem);
            }

            .btn {
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
            }

            .logo {
                font-size: 1.6rem;
            }

            .footer {
                padding: 3rem 0 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .section-title h2 {
                font-size: clamp(1.4rem, 7vw, 2rem);
            }

            .logo {
                font-size: 1.4rem;
            }

            .social-links {
                gap: 1rem;
            }

            .social-links a {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
            }
        }

        /* Enhanced Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .mb-1 {
            margin-bottom: 1rem;
        }

        .mb-2 {
            margin-bottom: 2rem;
        }

        .mb-3 {
            margin-bottom: 3rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .mt-3 {
            margin-top: 3rem;
        }

        .d-block {
            display: block;
        }

        .d-flex {
            display: flex;
        }

        .d-grid {
            display: grid;
        }

        .align-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .w-100 {
            width: 100%;
        }

        .h-100 {
            height: 100%;
        }

        /* Enhanced Accessibility & Performance */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            .navbar {
                backdrop-filter: none;
            }
        }

        @media (hover: none) {

            .btn:hover,
            .nav-links a:hover,
            .social-links a:hover {
                transform: none;
            }

            .btn::before,
            .social-links a::before,
            .logo::before {
                display: none;
            }
        }

        /* Touch device optimizations */
        @media (pointer: coarse) {

            .btn,
            .social-links a,
            .mobile-menu-btn {
                min-height: 44px;
                min-width: 44px;
            }

            .nav-links a {
                padding: 1rem 0;
            }
        }

        /* High contrast support */
        @media (prefers-contrast: high) {
            .navbar {
                background: var(--darker);
                border-bottom: 2px solid var(--accent);
            }

            .nav-links a.active {
                color: var(--light);
                background: var(--accent);
                padding: 0.5rem 1rem;
                border-radius: var(--border-radius-sm);
            }
        }
    </style>
</head>

<body>
    <!-- Three.js Canvas -->
    <canvas id="three-canvas"></canvas>
    
    <div class="loading-bar"></div>

    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="{{ route('home') }}" class="logo">Baraa A<span>l-Rifaee</span></a>

                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <ul class="nav-links" id="navLinks">
                    <li>
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            data-page="home">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}"
                            class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" data-page="about">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skills') }}"
                            class="nav-link {{ request()->routeIs('skills') ? 'active' : '' }}" data-page="skills">
                            Skills
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects') }}"
                            class="nav-link {{ request()->routeIs('projects') ? 'active' : '' }}" data-page="projects">
                            Projects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}"
                            class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" data-page="contact">
                            Contact
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog') }}" class="nav-link {{ request()->routeIs('blog') ? 'active' : '' }}"
                            data-page="blog">
                            My Blog
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    {{-- <footer class="footer">
        <div class="container">
            <div class="social-links">
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-github"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-dribbble"></i></a>
            </div>
            <p>&copy; 2025 Portfolio. Baraa Al-Rifaee <i class="fas fa-heart" style="color: var(--primary);"></i></p>
        </div>
    </footer> --}}

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileMenuBtn.innerHTML = navLinks.classList.contains('active')
                ? '<i class="fas fa-times"></i>'
                : '<i class="fas fa-bars"></i>';
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            const loadingBar = document.querySelector('.loading-bar');

            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Loading bar progress
            const winHeight = window.innerHeight;
            const docHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset;
            const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
            loadingBar.style.width = scrollPercent + '%';
        });

        // Active Navigation Management
        class NavigationManager {
            constructor() {
                this.currentPage = this.getCurrentPage();
                this.init();
            }

            init() {
                this.highlightActiveNav();
                this.bindNavEvents();
                this.handlePageLoad();
            }

            getCurrentPage() {
                const path = window.location.pathname;
                if (path === '/') return 'home';
                if (path.includes('/about')) return 'about';
                if (path.includes('/skills')) return 'skills';
                if (path.includes('/projects')) return 'projects';
                if (path.includes('/contact')) return 'contact';
                return 'home';
            }

            highlightActiveNav() {
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Add active class to current page link
                const activeLink = document.querySelector(`.nav-link[data-page="${this.currentPage}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }

            bindNavEvents() {
                // Add click event to all navigation links
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        // Update active state immediately for better UX
                        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                        e.target.classList.add('active');

                        // Close mobile menu if open
                        if (window.innerWidth <= 768) {
                            navLinks.classList.remove('active');
                            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                        }
                    });
                });

                // Handle browser back/forward buttons
                window.addEventListener('popstate', () => {
                    this.currentPage = this.getCurrentPage();
                    this.highlightActiveNav();
                });
            }

            handlePageLoad() {
                // Add slight delay to ensure DOM is fully loaded
                setTimeout(() => {
                    this.highlightActiveNav();
                }, 100);
            }
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = `${entry.target.dataset.animation} 0.8s ease-out forwards`;
                    entry.target.style.opacity = '1';
                }
            });
        }, observerOptions);

        // Initialize animations
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize navigation manager
            new NavigationManager();

            const animatedElements = document.querySelectorAll('.animate-on-scroll');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                observer.observe(el);
            });
        });

        // Parallax effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax');

            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Handle resize events
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                navLinks.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });

        // Disable text selection and copying
        document.addEventListener('selectstart', e => e.preventDefault());
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && (e.key === 'a' || e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 's' || e.key === 'u')) {
                e.preventDefault();
            }
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });
    </script>
    <script src="{{ asset('js/three-background.js') }}"></script>
    <script src="{{ asset('js/portfolio-animations.js') }}"></script>
    

</body>

</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Baraa Al-Rifaee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/categories.js') }}"></script>
</head>
<body>
    <div class="dashboard-3d-bg">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <canvas id="particleCanvas"></canvas>
    </div>

    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="#" class="logo">
                    <div class="logo-icon"><i class="fas fa-crown"></i></div>
                    <span class="logo-text">Baraa Al-Rifaee</span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-label">Main</div>
                    <ul class="nav-items">
                        <li><a href="#" class="nav-link active" data-section="dashboard"><i class="nav-icon fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                        <li><a href="#" class="nav-link" data-section="sections"><i class="nav-icon fas fa-layer-group"></i><span>Sections</span></a></li>
                        <li><a href="#" class="nav-link" data-section="skills-ecosystem"><i class="nav-icon fas fa-code"></i><span>Skills</span><span class="nav-badge" id="skills-count">0</span></a></li>
                        <li><a href="#" class="nav-link" data-section="projects"><i class="nav-icon fas fa-project-diagram"></i><span>Projects</span><span class="nav-badge" id="projects-count">0</span></a></li>
                        <li><a href="#" class="nav-link" data-section="categories"><i class="nav-icon fas fa-tags"></i><span>Categories</span></a></li>
                    </ul>
                </div>
                <div class="nav-section">
                    <div class="nav-label">Communication</div>
                    <ul class="nav-items">
                        <li><a href="#" class="nav-link" data-section="messages"><i class="nav-icon fas fa-envelope"></i><span>Messages</span><span class="nav-badge" id="messages-count">0</span></a></li>
                        <li><a href="#" class="nav-link" data-section="analytics"><i class="nav-icon fas fa-chart-bar"></i><span>Analytics</span></a></li>
                    </ul>
                </div>
                <div class="nav-section">
                    <div class="nav-label">System</div>
                    <ul class="nav-items">
                        <li><a href="#" class="nav-link" data-section="profile"><i class="nav-icon fas fa-user"></i><span>Profile</span></a></li>
                        <li><a href="#" class="nav-link" data-section="settings"><i class="nav-icon fas fa-cogs"></i><span>Settings</span></a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <header class="admin-header">
                <h1 class="page-title" id="pageTitle">Dashboard</h1>
                <div class="header-right">
                    <div class="user-menu">
                        <div class="user-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="user-role">Administrator</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" style="margin-left: 1rem;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <section id="dashboard" class="section-content active">
                    <div class="welcome-section">
                        <div class="welcome-bg-1"></div>
                        <div class="welcome-bg-2"></div>
                        <div class="welcome-orb"></div>
                        <div class="welcome-square"></div>
                        <div class="welcome-dots">
                            <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                            <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                            <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                        </div>
                        <div class="welcome-content">
                            <h2 class="welcome-title">Welcome back, {{ Auth::user()->name ?? 'Admin' }}!</h2>
                            <p class="welcome-subtitle">Here's what's happening with your portfolio today.</p>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon gradient-primary"><i class="fas fa-project-diagram"></i></div>
                            </div>
                            <div class="stat-number" data-stat="projects">0</div>
                            <div class="stat-label">Total Projects</div>
                            <div class="stat-trend"><i class="fas fa-arrow-up"></i>+12% from last month</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon gradient-success"><i class="fas fa-code"></i></div>
                            </div>
                            <div class="stat-number" data-stat="skills">0</div>
                            <div class="stat-label">Active Skills</div>
                            <div class="stat-trend"><i class="fas fa-arrow-up"></i>+5 this week</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon gradient-warning"><i class="fas fa-envelope"></i></div>
                            </div>
                            <div class="stat-number" data-stat="messages">0</div>
                            <div class="stat-label">New Messages</div>
                            <div class="stat-trend"><i class="fas fa-clock"></i>Review needed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon gradient-info"><i class="fas fa-chart-line"></i></div>
                            </div>
                            <div class="stat-number">0</div>
                            <div class="stat-label">Analytics</div>
                            <div class="stat-trend"><i class="fas fa-arrow-up"></i>Live</div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Activity</h2>
                            <button class="btn btn-primary btn-sm" onclick="adminDashboard.refreshDashboard()"><i class="fas fa-sync-alt"></i></button>
                        </div>
                        <div class="card-body">
                            <table class="data-table" id="recent-activities">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Item</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 2rem; color: rgba(255,255,255,0.5);">Loading activities...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <div id="dynamic-sections"></div>
            </div>
        </main>
    </div>

    <script>
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        for (let i = 0; i < 150; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                size: Math.random() * 2 + 1,
                color: `rgba(${Math.random() > 0.5 ? '102, 126, 234' : '240, 147, 251'}, ${Math.random() * 0.6 + 0.4})`
            });
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
                ctx.beginPath();
                ctx.fillStyle = p.color;
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fill();

                particles.forEach(p2 => {
                    const dx = p.x - p2.x;
                    const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(102, 126, 234, ${0.3 * (1 - dist / 120)})`;
                        ctx.lineWidth = 1;
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                });
            });
            requestAnimationFrame(animate);
        }
        animate();

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    </script>
</body>
</html>

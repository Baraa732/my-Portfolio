<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Baraa Al-Rifaee</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/categories.js') }}"></script>

    <style>
        /* Advanced 3D Background */
        .dashboard-3d-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            background: radial-gradient(ellipse at top, #0f1419 0%, #000000 100%);
        }

        #particleCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #4c6fff, #1a365d);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            top: 60%;
            right: 10%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            bottom: 10%;
            left: 30%;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            top: 40%;
            right: 30%;
            animation-delay: -15s;
        }

        .shape-5 {
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            bottom: 20%;
            right: 20%;
            animation-delay: -7s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }

            25% {
                transform: translate(50px, -50px) rotate(90deg) scale(1.1);
            }

            50% {
                transform: translate(-30px, 30px) rotate(180deg) scale(0.9);
            }

            75% {
                transform: translate(40px, 60px) rotate(270deg) scale(1.05);
            }
        }

        /* Modern Card Design */
        .stat-card,
        .section-card,
        .form-card {
            background: rgba(15, 20, 25, 0.7) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(76, 111, 255, 0.1) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .stat-card:hover,
        .section-card:hover {
            background: rgba(15, 20, 25, 0.85) !important;
            border: 1px solid rgba(76, 111, 255, 0.3) !important;
            box-shadow: 0 20px 60px rgba(76, 111, 255, 0.2) !important;
            transform: translateY(-8px) scale(1.02) !important;
        }

        /* Glassmorphism Effect */
        .sidebar {
            background: rgba(15, 20, 25, 0.8) !important;
            backdrop-filter: blur(30px) !important;
            border-right: 1px solid rgba(76, 111, 255, 0.1) !important;
        }

        .admin-header {
            background: rgba(15, 20, 25, 0.8) !important;
            backdrop-filter: blur(30px) !important;
            border-bottom: 1px solid rgba(76, 111, 255, 0.1) !important;
        }

        /* Smooth Animations */
        .dashboard-grid>* {
            animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) backwards;
        }

        .dashboard-grid>*:nth-child(1) {
            animation-delay: 0.1s;
        }

        .dashboard-grid>*:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dashboard-grid>*:nth-child(3) {
            animation-delay: 0.3s;
        }

        .dashboard-grid>*:nth-child(4) {
            animation-delay: 0.4s;
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

        /* 3D Card Tilt Effect */
        .stat-card {
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        /* Admin Layout Z-index */
        .admin-layout {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>
    <!-- Advanced 3D Background -->
    <div class="dashboard-3d-bg">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
        <canvas id="particleCanvas"></canvas>
    </div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="#" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <span class="logo-text">Baraa Al-Rifaee</span>
                </a>
                <button class="toggle-sidebar" id="toggleSidebar" aria-label="Toggle sidebar">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <div class="nav-label">Main</div>
                    <ul class="nav-items">
                        <li><a href="#" class="nav-link active" data-section="dashboard">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="sections">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <span>Sections</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="skills-ecosystem">
                                <i class="nav-icon fas fa-code"></i>
                                <span>Skills Ecosystem</span>
                                <span class="nav-badge" id="skills-count">0</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="projects">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <span>Projects</span>
                                <span class="nav-badge" id="projects-count">0</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="categories">
                                <i class="nav-icon fas fa-tags"></i>
                                <span>Categories</span>
                            </a></li>
                    </ul>
                </div>

                <!-- Communication -->
                <div class="nav-section">
                    <div class="nav-label">Communication</div>
                    <ul class="nav-items">
                        <li><a href="#" class="nav-link" data-section="messages">
                                <i class="nav-icon fas fa-envelope"></i>
                                <span>Messages</span>
                                <span class="nav-badge" id="messages-count">0</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="analytics">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <span>Analytics</span>
                            </a></li>
                    </ul>
                </div>

                <!-- System -->
                <div class="nav-section">
                    <div class="nav-label">System</div>
                    <ul class="nav-items">
                        <li><a href="#" class="nav-link" data-section="profile">
                                <i class="nav-icon fas fa-user"></i>
                                <span>Profile</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="settings">
                                <i class="nav-icon fas fa-cogs"></i>
                                <span>Advanced Settings</span>
                            </a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="admin-header">
                <div class="header-left">

                    <h1 class="page-title" id="pageTitle">Dashboard</h1>
                </div>

                <div class="header-right">
                    <!-- Notification Icon -->
                    <div class="notification-wrapper" x-data="notificationComponent()">
                        <button class="header-action notification-btn" @click="toggleNotifications"
                            aria-label="Notifications">
                            <i class="fas fa-bell"></i>
                            <span x-show="unreadCount > 0" class="notification-badge" x-text="unreadCount"></span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div x-show="isOpen" @click.away="isOpen = false" class="notifications-dropdown">
                            <div class="notifications-header">
                                <h3>Notifications</h3>
                                <div class="notifications-actions">
                                    <button @click="markAllAsRead" class="btn-link">Mark all read</button>
                                    <button @click="clearAll" class="btn-link">Clear all</button>
                                </div>
                            </div>

                            <div class="notifications-list">
                                <template x-if="notifications.length === 0">
                                    <div class="no-notifications">
                                        <i class="fas fa-bell-slash"></i>
                                        <p>No notifications</p>
                                    </div>
                                </template>

                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="notification-item" :class="{ 'unread': !notification.read_at }">
                                        <div class="notification-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title" x-text="notification.data.subject"></div>
                                            <div class="notification-message" x-text="notification.data.message"></div>
                                            <div class="notification-time" x-text="formatTime(notification.created_at)">
                                            </div>
                                        </div>
                                        <div class="notification-actions">
                                            <button @click="markAsRead(notification.id)" x-show="!notification.read_at"
                                                class="btn-icon" title="Mark as read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button @click="deleteNotification(notification.id)" class="btn-icon"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="notifications-footer">
                                <a href="{{ route('admin.messages.index') }}" class="view-all-link">
                                    View all messages
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="user-menu">
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="user-role">Administrator</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" style="margin-left: 1rem;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Dashboard Section -->
                <section id="dashboard" class="section-content active">
                    <!-- Real-time Activity Monitor -->
                    <div class="live-activity-monitor">
                        <div class="monitor-header">
                            <div class="pulse-indicator"></div>
                            <span>Live Activity</span>
                            <span class="live-time" id="liveTime"></span>
                        </div>
                        <canvas id="activityWave"></canvas>
                    </div>

                    <!-- Interactive 3D Stats Globe -->
                    <div class="stats-globe-container">
                        <canvas id="statsGlobe"></canvas>
                        <div class="globe-stats">
                            <div class="globe-stat">
                                <span class="stat-number" id="totalVisits">0</span>
                                <span class="stat-label">Total Visits</span>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-grid">
                        <div class="stat-card" data-stat="projects">
                            <div class="stat-header">
                                <div class="stat-icon projects">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">0</div>
                                    <div class="stat-label">Total Projects</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>0%</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card" data-stat="skills">
                            <div class="stat-header">
                                <div class="stat-icon skills">
                                    <i class="fas fa-code"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">0</div>
                                    <div class="stat-label">Active Skills</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>0%</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card" data-stat="messages">
                            <div class="stat-header">
                                <div class="stat-icon messages">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">0</div>
                                    <div class="stat-label">New Messages</div>
                                </div>
                                <div class="stat-trend trend-down">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>0%</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card" data-stat="views">
                            <div class="stat-header">
                                <div class="stat-icon views">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">0</div>
                                    <div class="stat-label">Analytics</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>Live</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Real-time Performance Metrics -->
                    <div class="performance-metrics">
                        <div class="metric-card cpu-metric">
                            <div class="metric-icon"><i class="fas fa-microchip"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">System Load</span>
                                <span class="metric-value" id="cpuLoad">0%</span>
                            </div>
                            <canvas id="cpuChart" width="100" height="40"></canvas>
                        </div>
                        <div class="metric-card memory-metric">
                            <div class="metric-icon"><i class="fas fa-memory"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">Active Users</span>
                                <span class="metric-value" id="activeUsers">0</span>
                            </div>
                            <canvas id="memoryChart" width="100" height="40"></canvas>
                        </div>
                        <div class="metric-card network-metric">
                            <div class="metric-icon"><i class="fas fa-network-wired"></i></div>
                            <div class="metric-info">
                                <span class="metric-label">Requests/min</span>
                                <span class="metric-value" id="networkLoad">0</span>
                            </div>
                            <canvas id="networkChart" width="100" height="40"></canvas>
                        </div>
                    </div>

                    <!-- Interactive Heatmap -->
                    <div class="activity-heatmap">
                        <h3>Activity Heatmap - Last 7 Days</h3>
                        <div id="heatmapGrid"></div>
                    </div>

                    <div class="section-card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Activity</h2>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-sm" onclick="adminDashboard.refreshDashboard()">
                                    <i class="icon fas fa-sync-alt"></i>
                                    {{-- Refresh --}}
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
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
                                            <td colspan="4" class="text-center">Loading activities...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Dynamic Sections -->
                <div id="dynamic-sections"></div>
            </div>
        </main>
    </div>

    <!-- Unified Modal Template -->
    <template id="modal-template">
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button class="modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </template>

    <!-- Section Templates -->
    <template id="sections-template">
        <section class="section-content">
            <div class="section-card">
                <div class="card-header">
                    <h2 class="card-title">Manage Portfolio Sections</h2>
                    <div class="card-actions">
                        <button class="btn btn-primary btn-sm" data-action="add-section">
                            <i class="fas fa-plus"></i> Add Section
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="sections-grid" id="sections-container"></div>
                </div>
            </div>
        </section>
    </template>

    <template id="skills-template">
        <section class="section-content">
            <div class="section-card">
                <div class="card-header">
                    <h2 class="card-title">Manage Skills</h2>
                    <div class="card-actions">
                        <button class="btn btn-primary btn-sm" data-action="add-skill">
                            <i class="fas fa-plus"></i> Add Skill
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="skills-grid" id="skills-container"></div>
                </div>
            </div>
        </section>
    </template>

    <template id="projects-template">
        <section class="section-content">
            <div class="section-card">
                <div class="card-header">
                    <h2 class="card-title">Manage Projects</h2>
                    <div class="card-actions">
                        <button class="btn btn-primary btn-sm" data-action="add-project">
                            <i class="fas fa-plus"></i> Add Project
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects-grid" id="projects-container"></div>
                </div>
            </div>
        </section>
    </template>

    <template id="messages-template">
        <section class="section-content">
            <div class="section-card">
                <div class="card-header">
                    <h2 class="card-title">Contact Messages</h2>
                    <div class="card-actions">
                        <button class="btn btn-primary btn-sm" data-action="mark-all-read">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="messages-list" id="messages-container"></div>
                </div>
            </div>
        </section>
    </template>

    <template id="profile-template">
        <section class="section-content">
            <div class="form-grid">
                <div class="form-card">
                    <h2 class="card-title">Profile Information</h2>
                    <form id="profile-form" class="ajax-form" action="/admin/profile" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ Auth::user()->email ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" value="{{ Auth::user()->title ?? '' }}"
                                placeholder="Full Stack Developer">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" rows="4"
                                placeholder="Write about yourself...">{{ Auth::user()->bio ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>

                <div class="form-card">
                    <h2 class="card-title">Change Password</h2>
                    <form id="password-form" class="ajax-form" action="/admin/profile/password" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </section>
    </template>


    <!-- KEEP THESE COMPLETE MODAL TEMPLATES -->

    <!-- Section Form Template -->
    <template id="section-form-template">
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add New Section</h3>
                    <button class="modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="section-form" class="ajax-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="form-label">Section Name</label>
                            <input type="text" name="name" required placeholder="e.g., Hero, About">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="title" required placeholder="e.g., About Me">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Content</label>
                            <textarea name="content" rows="4" placeholder="Section content..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="default">Default</option>
                                <option value="hero">Hero</option>
                                <option value="about">About</option>
                                <option value="skills">Skills</option>
                                <option value="projects">Projects</option>
                                <option value="contact">Contact</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" checked>
                                <span class="checkmark"></span>
                                Active
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="show_in_nav">
                                <span class="checkmark"></span>
                                Show in Navigation
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary"
                                onclick="adminDashboard.closeModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Section</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Skill Form Template -->
    <template id="skill-form-template">
        <div class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add New Skill</h3>
                    <button class="modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="skill-form" class="ajax-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="form-label">Skill Name</label>
                            <input type="text" name="name" required placeholder="e.g., Laravel, React">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Proficiency: <span class="percentage-display">80%</span></label>
                            <input type="range" name="percentage" min="0" max="100" value="80" class="slider">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Icon Class</label>
                            <input type="text" name="icon" required placeholder="fab fa-laravel">
                            <small class="form-hint">Use FontAwesome icon classes</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <!-- Change this: add value="1" and remove checked by default -->
                                <input type="checkbox" name="is_active" value="1">
                                <span class="checkmark"></span>
                                Active
                            </label>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary"
                                onclick="adminDashboard.closeModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Skill</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Project Form Template -->
    <template id="project-form-template">
        <div class="modal-overlay">
            <div class="modal-content large">
                <div class="modal-header">
                    <h3 class="modal-title">Add New Project</h3>
                    <button class="modal-close" aria-label="Close modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="project-form" class="ajax-form" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <!-- Add hidden input for is_active with default value -->
                        <input type="hidden" name="is_active" value="0">

                        <div class="project-form-grid">
                            <div class="form-group">
                                <label class="form-label required">Project Title</label>
                                <input type="text" name="title" required placeholder="Project title">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Project URL</label>
                                <input type="url" name="project_url" placeholder="https://example.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Description</label>
                            <textarea name="description" rows="3" required
                                placeholder="Project description..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Technologies</label>
                            <input type="text" name="technologies" required placeholder="Laravel, Vue.js, MySQL">
                            <small class="form-hint">Separate technologies with commas</small>
                        </div>

                        <div class="project-form-grid">
                            <div class="form-group">
                                <label class="form-label">GitHub URL</label>
                                <input type="url" name="github_url" placeholder="https://github.com/username/project">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Order</label>
                                <input type="number" name="order" value="0" min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Project Image</label>
                            <div class="project-image-preview" id="image-preview">
                                <div class="project-image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <div>No image selected</div>
                                </div>
                            </div>
                            <input type="file" name="image" accept="image/*" id="image-input">
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <!-- Important: Add value="1" and ensure name matches hidden input -->
                                <input type="checkbox" name="is_active" value="1" checked>
                                <span class="checkmark"></span>
                                Active
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary"
                                onclick="adminDashboard.closeModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <style>
        .live-activity-monitor {
            background: linear-gradient(135deg, rgba(76, 111, 255, 0.1), rgba(26, 54, 93, 0.2));
            border: 1px solid rgba(76, 111, 255, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .monitor-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #fff;
            font-weight: 600;
        }

        .pulse-indicator {
            width: 12px;
            height: 12px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 20px #22c55e;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
        }

        #activityWave {
            width: 100%;
            height: 80px;
        }

        .stats-globe-container {
            position: relative;
            background: linear-gradient(135deg, rgba(15, 20, 25, 0.8), rgba(26, 54, 93, 0.4));
            border: 1px solid rgba(76, 111, 255, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            height: 300px;
            overflow: hidden;
        }

        #statsGlobe {
            width: 100%;
            height: 100%;
        }

        .globe-stats {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10;
        }

        .globe-stat .stat-number {
            display: block;
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4c6fff, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: countUp 2s ease-out;
        }

        .globe-stat .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .performance-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: linear-gradient(135deg, rgba(15, 20, 25, 0.6), rgba(26, 54, 93, 0.3));
            border: 1px solid rgba(76, 111, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(76, 111, 255, 0.3);
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.1), transparent);
            transition: 0.5s;
        }

        .metric-card:hover::before {
            left: 100%;
        }

        .metric-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #4c6fff;
        }

        .metric-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .metric-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
        }

        .activity-heatmap {
            background: linear-gradient(135deg, rgba(15, 20, 25, 0.6), rgba(26, 54, 93, 0.3));
            border: 1px solid rgba(76, 111, 255, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .activity-heatmap h3 {
            color: #fff;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        #heatmapGrid {
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            gap: 4px;
        }

        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 4px;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .heatmap-cell:hover {
            transform: scale(1.2);
            z-index: 10;
        }

        .heatmap-cell[data-intensity="0"] {
            background: rgba(76, 111, 255, 0.1);
        }

        .heatmap-cell[data-intensity="1"] {
            background: rgba(76, 111, 255, 0.3);
        }

        .heatmap-cell[data-intensity="2"] {
            background: rgba(76, 111, 255, 0.5);
        }

        .heatmap-cell[data-intensity="3"] {
            background: rgba(76, 111, 255, 0.7);
        }

        .heatmap-cell[data-intensity="4"] {
            background: rgba(76, 111, 255, 0.9);
        }

        .heatmap-cell[data-intensity="5"] {
            background: #4c6fff;
            box-shadow: 0 0 10px #4c6fff;
        }
    </style>

    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        // Real-time Activity Wave
        const activityCanvas = document.getElementById('activityWave');
        if (activityCanvas) {
            const ctx = activityCanvas.getContext('2d');
            activityCanvas.width = activityCanvas.offsetWidth;
            activityCanvas.height = 80;
            let points = [];
            let time = 0;

            function drawWave() {
                ctx.clearRect(0, 0, activityCanvas.width, activityCanvas.height);
                ctx.beginPath();
                ctx.strokeStyle = '#4c6fff';
                ctx.lineWidth = 2;

                for (let x = 0; x < activityCanvas.width; x++) {
                    const y = activityCanvas.height / 2 + Math.sin((x + time) * 0.02) * 20 + Math.sin((x + time) * 0.05) * 10;
                    if (x === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.stroke();

                ctx.beginPath();
                ctx.strokeStyle = 'rgba(76, 111, 255, 0.3)';
                for (let x = 0; x < activityCanvas.width; x++) {
                    const y = activityCanvas.height / 2 + Math.sin((x + time + 50) * 0.03) * 15;
                    if (x === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.stroke();

                time += 2;
                requestAnimationFrame(drawWave);
            }
            drawWave();
        }

        // 3D Stats Globe
        const globeCanvas = document.getElementById('statsGlobe');
        if (globeCanvas) {
            const ctx = globeCanvas.getContext('2d');
            globeCanvas.width = globeCanvas.offsetWidth;
            globeCanvas.height = globeCanvas.offsetHeight;
            let rotation = 0;

            function drawGlobe() {
                ctx.clearRect(0, 0, globeCanvas.width, globeCanvas.height);
                const centerX = globeCanvas.width / 2;
                const centerY = globeCanvas.height / 2;
                const radius = Math.min(centerX, centerY) - 20;

                // Draw rotating circles
                for (let i = 0; i < 8; i++) {
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(76, 111, 255, ${0.2 - i * 0.02})`;
                    ctx.lineWidth = 1;
                    const angle = (rotation + i * 45) * Math.PI / 180;
                    ctx.ellipse(centerX, centerY, radius * Math.cos(angle), radius, angle, 0, Math.PI * 2);
                    ctx.stroke();
                }

                // Draw particles
                for (let i = 0; i < 50; i++) {
                    const angle = (rotation * 2 + i * 7.2) * Math.PI / 180;
                    const distance = radius * (0.5 + Math.sin(rotation * 0.05 + i) * 0.3);
                    const x = centerX + Math.cos(angle) * distance;
                    const y = centerY + Math.sin(angle) * distance * 0.5;

                    ctx.beginPath();
                    ctx.fillStyle = `rgba(76, 111, 255, ${0.5 + Math.sin(rotation * 0.1 + i) * 0.5})`;
                    ctx.arc(x, y, 2, 0, Math.PI * 2);
                    ctx.fill();
                }

                rotation += 0.5;
                requestAnimationFrame(drawGlobe);
            }
            drawGlobe();
        }

        // Real-time Performance Metrics
        function updateMetrics() {
            const cpuLoad = Math.floor(Math.random() * 30 + 20);
            const activeUsers = Math.floor(Math.random() * 50 + 10);
            const networkLoad = Math.floor(Math.random() * 100 + 50);

            document.getElementById('cpuLoad').textContent = cpuLoad + '%';
            document.getElementById('activeUsers').textContent = activeUsers;
            document.getElementById('networkLoad').textContent = networkLoad;

            // Update mini charts
            updateMiniChart('cpuChart', cpuLoad);
            updateMiniChart('memoryChart', activeUsers);
            updateMiniChart('networkChart', networkLoad);
        }

        const chartData = { cpu: [], memory: [], network: [] };
        function updateMiniChart(canvasId, value) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const dataKey = canvasId.replace('Chart', '');

            if (!chartData[dataKey]) chartData[dataKey] = [];
            chartData[dataKey].push(value);
            if (chartData[dataKey].length > 20) chartData[dataKey].shift();

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.beginPath();
            ctx.strokeStyle = '#4c6fff';
            ctx.lineWidth = 2;

            chartData[dataKey].forEach((val, i) => {
                const x = (i / 20) * canvas.width;
                const y = canvas.height - (val / 100) * canvas.height;
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            });
            ctx.stroke();
        }

        // Activity Heatmap
        function generateHeatmap() {
            const grid = document.getElementById('heatmapGrid');
            if (!grid) return;

            grid.innerHTML = '';
            for (let i = 0; i < 168; i++) {
                const cell = document.createElement('div');
                cell.className = 'heatmap-cell';
                const intensity = Math.floor(Math.random() * 6);
                cell.setAttribute('data-intensity', intensity);
                cell.title = `Hour ${i % 24}, Day ${Math.floor(i / 24) + 1}: ${intensity * 20}% activity`;
                grid.appendChild(cell);
            }
        }

        // Live Time
        function updateLiveTime() {
            const timeEl = document.getElementById('liveTime');
            if (timeEl) {
                timeEl.textContent = new Date().toLocaleTimeString();
            }
        }

        // Animate total visits counter
        function animateCounter() {
            const counter = document.getElementById('totalVisits');
            if (!counter) return;

            let current = 0;
            const target = Math.floor(Math.random() * 10000 + 5000);
            const duration = 2000;
            const increment = target / (duration / 16);

            function update() {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(update);
                } else {
                    counter.textContent = target.toLocaleString();
                }
            }
            update();
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function () {
            generateHeatmap();
            animateCounter();
            updateLiveTime();
            setInterval(updateLiveTime, 1000);
            setInterval(updateMetrics, 2000);
            updateMetrics();
            initParticles();
            init3DCardTilt();
        });

        // Advanced Particle System
        function initParticles() {
            const canvas = document.getElementById('particleCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const particles = [];
            const particleCount = 100;

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.z = Math.random() * 1000;
                    this.vx = (Math.random() - 0.5) * 0.5;
                    this.vy = (Math.random() - 0.5) * 0.5;
                    this.vz = Math.random() * 2 + 1;
                    this.size = Math.random() * 2 + 1;
                    this.color = `rgba(76, 111, 255, ${Math.random() * 0.5 + 0.3})`;
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;
                    this.z -= this.vz;

                    if (this.z < 1) {
                        this.z = 1000;
                        this.x = Math.random() * canvas.width;
                        this.y = Math.random() * canvas.height;
                    }

                    if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
                    if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
                }

                draw() {
                    const scale = 1000 / this.z;
                    const x2d = (this.x - canvas.width / 2) * scale + canvas.width / 2;
                    const y2d = (this.y - canvas.height / 2) * scale + canvas.height / 2;
                    const size2d = this.size * scale;

                    ctx.beginPath();
                    ctx.fillStyle = this.color;
                    ctx.arc(x2d, y2d, size2d, 0, Math.PI * 2);
                    ctx.fill();

                    // Draw connections
                    particles.forEach(p => {
                        const dx = this.x - p.x;
                        const dy = this.y - p.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < 100) {
                            const px2d = (p.x - canvas.width / 2) * (1000 / p.z) + canvas.width / 2;
                            const py2d = (p.y - canvas.height / 2) * (1000 / p.z) + canvas.height / 2;

                            ctx.beginPath();
                            ctx.strokeStyle = `rgba(76, 111, 255, ${0.2 * (1 - dist / 100)})`;
                            ctx.lineWidth = 0.5;
                            ctx.moveTo(x2d, y2d);
                            ctx.lineTo(px2d, py2d);
                            ctx.stroke();
                        }
                    });
                }
            }

            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(p => {
                    p.update();
                    p.draw();
                });
                requestAnimationFrame(animate);
            }
            animate();

            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        }

        // 3D Card Tilt Effect
        function init3DCardTilt() {
            const cards = document.querySelectorAll('.stat-card, .section-card');

            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;

                    const rotateX = (y - centerY) / 10;
                    const rotateY = (centerX - x) / 10;

                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
                });
            });
        }
    </script>
    <script>
        // Profile form handling
        document.addEventListener('DOMContentLoaded', function () {
            const profileForm = document.getElementById('profile-form');
            const passwordForm = document.getElementById('password-form');

            if (profileForm) {
                profileForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('/admin/profile', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Profile updated successfully!');
                                // Update dashboard UI
                                document.querySelector('.user-name').textContent = data.user.name;
                                document.querySelector('.user-avatar').textContent = data.user.name.charAt(0);
                                // Refresh activities
                                setTimeout(() => {
                                    adminDashboard.loadDashboardData();
                                    location.reload();
                                }, 1000);
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            alert('Error updating profile');
                        });
                });
            }

            if (passwordForm) {
                passwordForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch('/admin/profile/password', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Password updated successfully!');
                                passwordForm.reset();
                                // Refresh activities
                                setTimeout(() => adminDashboard.loadDashboardData(), 500);
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            alert('Error updating password');
                        });
                });
            }
        });
    </script>
</body>

</html>

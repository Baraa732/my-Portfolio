<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Baraa Al-Rifaee</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body>
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
                        <li><a href="#" class="nav-link" data-section="skills">
                                <i class="nav-icon fas fa-code"></i>
                                <span>Skills</span>
                                <span class="nav-badge" id="skills-count">0</span>
                            </a></li>
                        <li><a href="#" class="nav-link" data-section="projects">
                                <i class="nav-icon fas fa-project-diagram"></i>
                                <span>Projects</span>
                                <span class="nav-badge" id="projects-count">0</span>
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
                        
                        <style>
                        /* Hide all scrollbars */
                        * {
                            scrollbar-width: none;
                            -ms-overflow-style: none;
                        }
                        *::-webkit-scrollbar {
                            display: none;
                        }
                        html, body, .main-content, .content-area {
                            overflow-x: hidden;
                        }
                        
                        html, body {
                            background: #0f1419 !important;
                        }
                        
                        /* Apply to all sections */
                        .section-content {
                            background: #0f1419 !important;
                            min-height: 111.11vh !important;
                        }
                        
                        .admin-layout, .main-content, .content-area {
                            background: #0f1419 !important;
                            min-height: 111.11vh !important;
                        }
                        
                        /* Scale dashboard to 90% */
                        body {
                            zoom: 0.9;
                        }
                        
                        /* Fix sidebar height */
                        .sidebar {
                            height: 111.11vh !important;
                            min-height: 111.11vh !important;
                        }
                        
                        /* Mobile Responsive */
                        @media (max-width: 768px) {
                            .sidebar {
                                position: fixed;
                                left: -280px;
                                z-index: 1000;
                                transition: left 0.3s ease;
                            }
                            .sidebar.active {
                                left: 0;
                            }
                            .main-content {
                                margin-left: 0;
                                width: 100%;
                            }
                            .admin-header {
                                padding: 0.5rem 1rem;
                                width: 100% !important;
                                right: 0 !important;
                                position: relative !important;
                            }
                            .header-left .page-title {
                                font-size: 1.25rem;
                            }
                            .header-right {
                                gap: 0.5rem;
                            }
                            .user-info {
                                display: none;
                            }
                            .dashboard-grid {
                                grid-template-columns: 1fr;
                                gap: 1rem;
                                padding: 1rem;
                            }
                            .stat-card {
                                padding: 1rem;
                            }
                            .section-card {
                                margin: 1rem;
                                padding: 1rem;
                            }
                            .form-grid {
                                grid-template-columns: 1fr;
                                gap: 1rem;
                            }
                            .modal-content {
                                width: 95vw;
                                margin: 1rem;
                            }
                            .notifications-dropdown {
                                right: 0.5rem;
                                width: calc(100vw - 1rem);
                                max-width: 350px;
                            }
                            .table-container {
                                overflow-x: auto;
                            }
                            .data-table {
                                min-width: 600px;
                            }
                        }
                        
                        @media (max-width: 480px) {
                            .admin-header {
                                padding: 0.5rem;
                            }
                            .header-left .page-title {
                                font-size: 1.1rem;
                            }
                            .dashboard-grid {
                                padding: 0.5rem;
                                gap: 0.75rem;
                            }
                            .stat-card {
                                padding: 0.75rem;
                            }
                            .stat-value {
                                font-size: 1.5rem;
                            }
                            .section-card {
                                margin: 0.5rem;
                                padding: 0.75rem;
                            }
                            .modal-content {
                                width: 98vw;
                                margin: 0.5rem;
                            }
                            .form-group {
                                margin-bottom: 1rem;
                            }
                            .btn {
                                padding: 0.5rem 1rem;
                                font-size: 0.875rem;
                            }
                        }
                        </style>
                    </div>

                    <div class="section-card">
                        <div class="card-header">
                            <h2 class="card-title">Recent Activity</h2>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-sm" onclick="adminDashboard.refreshDashboard()">
                                    <i class="fas fa-sync-alt"></i> Refresh
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

    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        // Profile form handling
        document.addEventListener('DOMContentLoaded', function() {
            const profileForm = document.getElementById('profile-form');
            const passwordForm = document.getElementById('password-form');
            
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
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
                passwordForm.addEventListener('submit', function(e) {
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

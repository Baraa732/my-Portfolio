class NotificationSystem {
    constructor() {
        this.notificationContainer = null;
        this.activeNotifications = new Set();
        this.init();
    }

    init() {
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        // Remove existing container if any
        const existingContainer = document.getElementById('admin-notification-container');
        if (existingContainer) {
            existingContainer.remove();
        }

        this.notificationContainer = document.createElement('div');
        this.notificationContainer.id = 'admin-notification-container';
        this.notificationContainer.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
        `;
        document.body.appendChild(this.notificationContainer);
    }

    showNotification(message, type = 'info', duration = 5000) {
        const notificationId = `${message}-${type}`;
        if (this.activeNotifications.has(notificationId)) {
            return;
        }

        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="notification-icon fas fa-${this.getNotificationIcon(type)}"></i>
                <div class="notification-message">${message}</div>
                <button class="notification-close" title="Dismiss">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.removeNotification(notification, notificationId);
        });

        this.notificationContainer.appendChild(notification);
        this.activeNotifications.add(notificationId);

        setTimeout(() => notification.classList.add('show'), 100);

        if (duration > 0) {
            setTimeout(() => {
                this.removeNotification(notification, notificationId);
            }, duration);
        }

        return notification;
    }

    removeNotification(notification, notificationId) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
            this.activeNotifications.delete(notificationId);
        }, 300);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
}

// Notification Component for Alpine.js
function notificationComponent() {
    return {
        isOpen: false,
        notifications: [],
        unreadCount: 0,

        init() {
            this.loadNotifications();
        },

        async loadNotifications() {
            try {
                const response = await fetch('/admin/notifications', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            } catch (error) {
                console.error('Error loading notifications:', error);
                // Don't show error toast for notifications to avoid spam
            }
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/admin/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.notifications = this.notifications.map(notification =>
                        notification.id === notificationId
                            ? { ...notification, read_at: new Date().toISOString() }
                            : notification
                    );
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },

        // Remove the Echo setup for now unless you have it configured
        setupEcho() {
            // Comment out or remove Echo initialization if not set up
            /*
            if (typeof Echo !== 'undefined') {
                this.echo = Echo;
                this.echo.private('admin-notifications')
                    .listen('NewContactMessage', (e) => {
                        this.notifications.unshift(e.notification);
                        this.unreadCount++;
                        this.showToast('New message received!');
                    });
            }
            */
        },

        // Keep other methods the same but add better error handling
        async markAllAsRead() {
            try {
                const response = await fetch('/admin/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.notifications = this.notifications.map(notification => ({
                        ...notification,
                        read_at: notification.read_at || new Date().toISOString()
                    }));
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        formatTime(timestamp) {
            // Your existing formatTime method
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;

            return date.toLocaleDateString();
        }
    };
}

class AdminDashboard {
    constructor() {
        this.notificationSystem = new NotificationSystem();
        this.currentSection = 'dashboard';

        // Fix base URL - use window.location.origin + /admin
        this.baseUrl = window.location.origin + '/admin';

        console.log('Admin Dashboard initialized with base URL:', this.baseUrl);

        this.formSubmitHandler = null;
        this.buttonClickHandler = null;
        this.init();
        this.initializeCounts();
    }

    init() {
        this.bindEvents();
        this.loadDashboardData();
        this.initLogout();
    }

    async initializeCounts() {
        try {
            // Initialize all counts when page loads
            await Promise.all([
                this.updateSkillsCount(),
                this.updateProjectsCount(),
                this.updateMessagesCount()
            ]);
        } catch (error) {
            console.error('Error initializing counts:', error);
        }
    }

    bindEvents() {
        // Navigation - bind once
        document.addEventListener('click', (e) => {
            const navLink = e.target.closest('.nav-link');
            if (navLink) {
                e.preventDefault();
                const section = navLink.getAttribute('data-section');
                if (section) {
                    this.switchSection(section);
                }
            }
        });

        // Mobile menu - bind once
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                document.getElementById('sidebar').classList.toggle('mobile-open');
            });
        }

        // Sidebar toggle - bind once
        const toggleSidebar = document.getElementById('toggleSidebar');
        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', () => {
                document.getElementById('sidebar').classList.toggle('collapsed');
            });
        }
    }

    initLogout() {
        // Logout - bind once
        document.addEventListener('click', (e) => {
            if (e.target.closest('.logout-item')) {
                e.preventDefault();
                this.confirmLogout();
            }
        });
    }

    // =====================
    // SECTION MANAGEMENT
    // =====================

    async getSectionsContent() {
        try {
            const response = await fetch(`${this.baseUrl}/sections`);
            const sections = await response.json();

            let html = `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-header">
                            <h3 class="card-title">Manage Portfolio Sections</h3>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-sm" onclick="adminDashboard.showSectionForm()">
                                    <i class="fas fa-plus"></i> Add Section
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="sections-grid" id="sections-container">
            `;

            if (sections.length === 0) {
                html += `
                    <div class="text-center w-100" style="grid-column: 1 / -1; padding: 3rem;">
                        <i class="fas fa-layer-group" style="font-size: 3rem; color: var(--gray-dark); margin-bottom: 1rem;"></i>
                        <h4 style="color: var(--gray); margin-bottom: 1rem;">No Sections Yet</h4>
                        <p style="color: var(--gray-dark); margin-bottom: 2rem;">Start by adding sections to build your portfolio structure.</p>
                        <button class="btn btn-primary" onclick="adminDashboard.showSectionForm()">
                            <i class="fas fa-plus"></i> Add Your First Section
                        </button>
                    </div>
                `;
            } else {
                sections.forEach(section => {
                    const shortContent = section.content && section.content.length > 100
                        ? section.content.substring(0, 100) + '...'
                        : section.content || 'No content';

                    html += `
                        <div class="section-item" data-section-id="${section.id}">
                            <div class="d-flex align-center justify-between mb-2">
                                <div class="d-flex align-center gap-1">
                                    <div style="width: 40px; height: 40px; background: var(--gradient); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-layer-group" style="color: var(--light); font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <h4 style="color: var(--light); margin: 0;">${section.name}</h4>
                                        <small style="color: var(--gray-dark);">${section.title}</small>
                                    </div>
                                </div>
                                <div class="d-flex align-center gap-1">
                                    <span class="status-badge ${section.is_active ? 'status-active' : 'status-inactive'}">
                                        ${section.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                    ${section.show_in_nav ? '<span class="status-badge status-active">Nav</span>' : ''}
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 1rem;">
                                <p style="color: var(--gray); font-size: 0.9rem; line-height: 1.4;">${shortContent}</p>
                            </div>
                            
                            <div class="d-flex justify-between align-center">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <span style="background: rgba(115, 12, 14, 0.2); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; border: 1px solid rgba(115, 12, 14, 0.3);">
                                        ${section.type}
                                    </span>
                                    <span style="background: rgba(59, 130, 246, 0.2); color: var(--info); padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; border: 1px solid rgba(59, 130, 246, 0.3);">
                                        Order: ${section.order}
                                    </span>
                                </div>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-primary btn-sm" onclick="adminDashboard.editSection(${section.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="adminDashboard.deleteSection(${section.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            html += `
                            </div>
                        </div>
                    </div>
                </section>
            `;

            return html;

        } catch (error) {
            console.error('Error loading sections:', error);
            return `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
                                <h4 style="color: var(--light); margin-bottom: 1rem;">Error Loading Sections</h4>
                                <p style="color: var(--gray);">Unable to load sections. Please check your backend connection.</p>
                                <button class="btn btn-primary" onclick="adminDashboard.loadSectionContent('sections')">
                                    <i class="fas fa-redo"></i> Try Again
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            `;
        }
    }

    showSectionForm(sectionId = null) {
        this.closeModal();

        const template = document.getElementById('section-form-template');
        if (!template) {
            this.showNotification('Section form template not found', 'error');
            return;
        }

        const modal = document.createElement('div');
        modal.innerHTML = template.innerHTML;
        document.body.appendChild(modal);

        const form = modal.querySelector('#section-form');
        const title = modal.querySelector('.modal-title');

        if (sectionId) {
            title.textContent = 'Edit Section';
            form.action = `${this.baseUrl}/sections/${sectionId}`;
            console.log('Edit section form action:', form.action);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            this.loadSectionData(sectionId);
        } else {
            title.textContent = 'Add New Section';
            form.action = `${this.baseUrl}/sections`;
            console.log('Add section form action:', form.action);
        }

        modal.querySelector('.modal-close').addEventListener('click', () => this.closeModal());
        modal.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target === modal.querySelector('.modal-overlay')) {
                this.closeModal();
            }
        });
    }

    async loadSectionData(sectionId) {
        try {
            const response = await fetch(`${this.baseUrl}/sections/${sectionId}`);

            if (!response.ok) {
                if (response.status === 404) {
                    this.showNotification('Section not found. It may have been deleted.', 'info');
                    this.closeModal();
                    return;
                }
                throw new Error('Failed to fetch section data');
            }

            const section = await response.json();

            const form = document.getElementById('section-form');
            if (form) {
                form.querySelector('input[name="name"]').value = section.name || '';
                form.querySelector('input[name="title"]').value = section.title || '';
                form.querySelector('textarea[name="content"]').value = section.content || '';
                form.querySelector('input[name="order"]').value = section.order || 0;
                form.querySelector('select[name="type"]').value = section.type || 'default';
                form.querySelector('input[name="is_active"]').checked = section.is_active !== undefined ? section.is_active : true;
                form.querySelector('input[name="show_in_nav"]').checked = section.show_in_nav || false;
            }

        } catch (error) {
            console.error('Error loading section data:', error);
            this.showNotification('Error loading section data', 'error');
            this.closeModal();
        }
    }

    async deleteSection(sectionId) {
        if (!confirm('Are you sure you want to delete this section?')) return;

        try {
            const response = await fetch(`${this.baseUrl}/sections/${sectionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');

                const sectionElement = document.querySelector(`[data-section-id="${sectionId}"]`);
                if (sectionElement) {
                    sectionElement.style.opacity = '0';
                    setTimeout(() => sectionElement.remove(), 300);
                }

                this.loadDashboardData();

            } else {
                this.showNotification(result.message || 'Error deleting section', 'error');
            }
        } catch (error) {
            console.error('Error deleting section:', error);
            this.showNotification('Error deleting section', 'error');
        }
    }

    editSection(sectionId) {
        this.showSectionForm(sectionId);
    }

    // =====================
    // SKILLS MANAGEMENT
    // =====================

    async getSkillsContent() {
        try {
            const response = await fetch(`${this.baseUrl}/skills`);
            const skills = await response.json();

            let html = `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-header">
                            <h3 class="card-title">Manage Skills</h3>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-sm" onclick="adminDashboard.showSkillForm()">
                                    <i class="fas fa-plus"></i> Add Skill
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="skills-grid" id="skills-container">
            `;

            if (skills.length === 0) {
                html += `
                    <div class="text-center w-100" style="grid-column: 1 / -1; padding: 3rem;">
                        <i class="fas fa-code" style="font-size: 3rem; color: var(--gray-dark); margin-bottom: 1rem;"></i>
                        <h4 style="color: var(--gray); margin-bottom: 1rem;">No Skills Yet</h4>
                        <p style="color: var(--gray-dark); margin-bottom: 2rem;">Start by adding your first skill to showcase your expertise.</p>
                        <button class="btn btn-primary" onclick="adminDashboard.showSkillForm()">
                            <i class="fas fa-plus"></i> Add Your First Skill
                        </button>
                    </div>
                `;
            } else {
                skills.forEach(skill => {
                    html += `
                        <div class="skill-item" data-skill-id="${skill.id}">
                            <div class="d-flex align-center justify-between mb-2">
                                <div class="d-flex align-center gap-1">
                                    <div style="width: 40px; height: 40px; background: var(--gradient); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="${skill.icon}" style="color: var(--light); font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 style="color: var(--light); margin: 0;">${skill.name}</h4>
                                </div>
                                <span class="status-badge ${skill.is_active ? 'status-active' : 'status-inactive'}">
                                    ${skill.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                            <div class="skill-bar" style="background: var(--secondary); height: 8px; border-radius: 4px; margin-bottom: 0.5rem;">
                                <div class="skill-progress" style="height: 100%; background: var(--gradient); width: ${skill.percentage}%; border-radius: 4px;"></div>
                            </div>
                            <div class="d-flex justify-between align-center">
                                <span style="color: var(--gray); font-size: 0.9rem;">${skill.percentage}% Proficiency</span>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-primary btn-sm" onclick="adminDashboard.editSkill(${skill.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="adminDashboard.deleteSkill(${skill.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            html += `
                            </div>
                        </div>
                    </div>
                </section>
            `;

            document.getElementById('skills-count').textContent = skills.length;
            return html;

        } catch (error) {
            console.error('Error loading skills:', error);
            return `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
                                <h4 style="color: var(--light); margin-bottom: 1rem;">Error Loading Skills</h4>
                                <p style="color: var(--gray);">Unable to load skills. Please check your backend connection.</p>
                                <button class="btn btn-primary" onclick="adminDashboard.loadSectionContent('skills')">
                                    <i class="fas fa-redo"></i> Try Again
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            `;
        }
    }

    showSkillForm(skillId = null) {
        this.closeModal();

        const template = document.getElementById('skill-form-template');
        if (!template) {
            this.showNotification('Skill form template not found', 'error');
            return;
        }

        const modal = document.createElement('div');
        modal.innerHTML = template.innerHTML;
        document.body.appendChild(modal);

        setTimeout(() => {
            const form = document.getElementById('skill-form');
            const title = modal.querySelector('.modal-title');

            if (!form) {
                console.error('Skill form not found after adding to DOM');
                this.showNotification('Error: Form not found', 'error');
                return;
            }

            console.log('=== SKILL FORM DEBUG ===');
            console.log('Skill ID:', skillId);
            console.log('Base URL:', this.baseUrl);

            if (skillId) {
                title.textContent = 'Edit Skill';
                // Use absolute URL to avoid any base URL issues
                form.action = `/admin/skills/${skillId}`;
                console.log('Edit skill form action:', form.action);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);

                setTimeout(() => {
                    this.loadSkillData(skillId);
                }, 100);
            } else {
                title.textContent = 'Add New Skill';
                // Use absolute URL
                form.action = `/admin/skills`;
                console.log('Add skill form action:', form.action);
            }

            this.initializeSkillFormSlider(form);
            this.initializeModalEvents(modal);

        }, 50);
    }

    initializeSkillFormSlider(form) {
        const slider = form.querySelector('input[name="percentage"]');
        const display = form.querySelector('.percentage-display');

        if (slider && display) {
            slider.replaceWith(slider.cloneNode(true));
            const newSlider = form.querySelector('input[name="percentage"]');

            newSlider.addEventListener('input', (e) => {
                display.textContent = e.target.value + '%';
            });

            display.textContent = newSlider.value + '%';
        }
    }

    async loadSkillData(skillId) {
        try {
            console.log('Loading skill data for ID:', skillId);

            const response = await fetch(`/admin/skills/${skillId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const skill = await response.json();
            console.log('Skill data received:', skill);

            await new Promise(resolve => setTimeout(resolve, 150));

            const form = document.getElementById('skill-form');
            if (!form) {
                throw new Error('Form not found in DOM');
            }

            this.setFormValue(form, 'input[name="name"]', skill.name);
            this.setFormValue(form, 'input[name="percentage"]', skill.percentage || 80);
            this.setFormValue(form, 'input[name="icon"]', skill.icon);
            this.setFormValue(form, 'input[name="order"]', skill.order || 0);

            // Fix: Handle checkbox properly - set checked attribute based on boolean value
            const isActiveCheckbox = form.querySelector('input[name="is_active"]');
            if (isActiveCheckbox) {
                isActiveCheckbox.checked = skill.is_active == 1 || skill.is_active === true;
            }

            const display = form.querySelector('.percentage-display');
            const slider = form.querySelector('input[name="percentage"]');
            if (display && slider) {
                display.textContent = (skill.percentage || 80) + '%';
                slider.value = skill.percentage || 80;
            }

            console.log('Skill form populated successfully');

        } catch (error) {
            console.error('Error loading skill data:', error);
            this.showNotification('Error loading skill data: ' + error.message, 'error');
        }
    }

    async deleteSkill(skillId) {
        if (!confirm('Are you sure you want to delete this skill?')) return;

        try {
            const response = await fetch(`/admin/skills/${skillId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');

                const skillElement = document.querySelector(`[data-skill-id="${skillId}"]`);
                if (skillElement) {
                    skillElement.style.opacity = '0';
                    setTimeout(() => skillElement.remove(), 300);
                }

                await this.updateSkillsCount();
                this.loadDashboardData();

            } else {
                this.showNotification(result.message || 'Error deleting skill', 'error');
            }
        } catch (error) {
            console.error('Error deleting skill:', error);
            this.showNotification('Error deleting skill', 'error');
        }
    }

    editSkill(skillId) {
        this.showSkillForm(skillId);
    }

    // =====================
    // PROJECTS MANAGEMENT
    // =====================

    async getProjectsContent() {
        try {
            const response = await fetch(`${this.baseUrl}/projects`);
            const projects = await response.json();

            let html = `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-header">
                            <h3 class="card-title">Manage Projects</h3>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-sm" onclick="adminDashboard.showProjectForm()">
                                    <i class="fas fa-plus"></i> Add Project
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="projects-grid" id="projects-container">
            `;

            if (projects.length === 0) {
                html += `
                    <div class="text-center w-100" style="grid-column: 1 / -1; padding: 3rem;">
                        <i class="fas fa-project-diagram" style="font-size: 3rem; color: var(--gray-dark); margin-bottom: 1rem;"></i>
                        <h4 style="color: var(--gray); margin-bottom: 1rem;">No Projects Yet</h4>
                        <p style="color: var(--gray-dark); margin-bottom: 2rem;">Start by adding your first project to showcase your work.</p>
                        <button class="btn btn-primary" onclick="adminDashboard.showProjectForm()">
                            <i class="fas fa-plus"></i> Add Your First Project
                        </button>
                    </div>
                `;
            } else {
                projects.forEach(project => {
                    const shortDescription = project.description.length > 120
                        ? project.description.substring(0, 120) + '...'
                        : project.description;

                    html += `
                        <div class="project-item">
                            <div style="height: 200px; background: var(--gradient); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                ${project.image ?
                            `<img src="/storage/${project.image}" alt="${project.title}" style="width: 100%; height: 100%; object-fit: cover;">` :
                            `<i class="fas fa-project-diagram" style="font-size: 3rem; color: var(--light);"></i>`
                        }
                                <div style="position: absolute; top: 10px; right: 10px;">
                                    <span class="status-badge ${project.is_active ? 'status-active' : 'status-inactive'}">
                                        ${project.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                            </div>
                            <div style="padding: 1.5rem;">
                                <h4 style="color: var(--light); margin-bottom: 0.5rem; font-size: 1.1rem;">${project.title}</h4>
                                <p style="color: var(--gray); margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.4;">${shortDescription}</p>
                                <div style="margin-bottom: 1rem;">
                                    <div class="tech-tags">
                                        ${project.technologies.split(',').map(tech =>
                            `<span class="tech-tag">${tech.trim()}</span>`
                        ).join('')}
                                    </div>
                                </div>
                                <div class="d-flex justify-between align-center">
                                    <div class="d-flex gap-1">
                                        ${project.project_url ? `
                                            <a href="${project.project_url}" target="_blank" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.6rem;">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        ` : ''}
                                        ${project.github_url ? `
                                            <a href="${project.github_url}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.6rem;">
                                                <i class="fab fa-github"></i>
                                            </a>
                                        ` : ''}
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary btn-sm" onclick="adminDashboard.editProject(${project.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="adminDashboard.deleteProject(${project.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }

            html += `
                            </div>
                        </div>
                    </div>
                </section>
            `;

            document.getElementById('projects-count').textContent = projects.length;
            return html;

        } catch (error) {
            console.error('Error loading projects:', error);
            return `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
                                <h4 style="color: var(--light); margin-bottom: 1rem;">Error Loading Projects</h4>
                                <p style="color: var(--gray);">Unable to load projects. Please check your backend connection.</p>
                                <button class="btn btn-primary" onclick="adminDashboard.loadSectionContent('projects')">
                                    <i class="fas fa-redo"></i> Try Again
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            `;
        }
    }

    showProjectForm(projectId = null) {
        this.closeModal();

        const template = document.getElementById('project-form-template');
        if (!template) {
            this.showNotification('Project form template not found', 'error');
            return;
        }

        const modal = document.createElement('div');
        modal.innerHTML = template.innerHTML;
        document.body.appendChild(modal);

        const form = modal.querySelector('#project-form');
        const title = modal.querySelector('.modal-title');

        if (projectId) {
            title.textContent = 'Edit Project';
            form.action = `/admin/projects/${projectId}`;
            console.log('Edit project form action:', form.action);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            this.loadProjectData(projectId);
        } else {
            title.textContent = 'Add New Project';
            form.action = `/admin/projects`;
            console.log('Add project form action:', form.action);
        }

        modal.querySelector('.modal-close').addEventListener('click', () => this.closeModal());
        modal.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target === modal.querySelector('.modal-overlay')) {
                this.closeModal();
            }
        });
    }

    async loadProjectData(projectId) {
        try {
            const response = await fetch(`/admin/projects/${projectId}`);

            if (!response.ok) {
                if (response.status === 404) {
                    this.showNotification('Project not found. It may have been deleted.', 'info');
                    this.closeModal();
                    return;
                }
                throw new Error('Failed to fetch project data');
            }

            const project = await response.json();

            const form = document.getElementById('project-form');
            if (form) {
                form.querySelector('input[name="title"]').value = project.title || '';
                form.querySelector('textarea[name="description"]').value = project.description || '';
                form.querySelector('input[name="technologies"]').value = project.technologies || '';
                form.querySelector('input[name="project_url"]').value = project.project_url || '';
                form.querySelector('input[name="github_url"]').value = project.github_url || '';
                form.querySelector('input[name="order"]').value = project.order || 0;
                form.querySelector('input[name="is_active"]').checked = project.is_active !== undefined ? project.is_active : true;
            }

        } catch (error) {
            console.error('Error loading project data:', error);
            this.showNotification('Error loading project data', 'error');
            this.closeModal();
        }
    }

    async deleteProject(projectId) {
        if (!confirm('Are you sure you want to delete this project? This action cannot be undone.')) return;

        try {
            const response = await fetch(`/admin/projects/${projectId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');

                const projectElement = document.querySelector(`.project-item`);
                if (projectElement) {
                    projectElement.style.opacity = '0';
                    projectElement.style.transform = 'translateX(100px)';
                    setTimeout(() => projectElement.remove(), 300);
                }

                await this.updateProjectsCount();
                this.loadDashboardData();

            } else {
                this.showNotification(result.message || 'Error deleting project', 'error');
            }
        } catch (error) {
            console.error('Error deleting project:', error);
            this.showNotification('Error deleting project', 'error');
        }
    }

    editProject(projectId) {
        this.showProjectForm(projectId);
    }

    // =====================
    // MESSAGES MANAGEMENT
    // =====================

    async getMessagesContent() {
        try {
            const response = await fetch(`${this.baseUrl}/messages`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const messages = await response.json();

            let html = `
            <section class="section-content">
                <div class="section-card">
                    <div class="card-header">
                        <h3 class="card-title">Contact Messages</h3>
                        <div class="card-actions">
                            <button class="btn btn-primary btn-sm" onclick="adminDashboard.markAllAsRead()">
                                <i class="fas fa-check-double"></i> Mark All Read
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="messages-list" id="messages-container">
        `;

            if (!messages || messages.length === 0) {
                html += `
                <div class="text-center w-100" style="padding: 3rem;">
                    <i class="fas fa-envelope" style="font-size: 3rem; color: var(--gray-dark); margin-bottom: 1rem;"></i>
                    <h4 style="color: var(--gray); margin-bottom: 1rem;">No Messages Yet</h4>
                    <p style="color: var(--gray-dark);">No contact messages have been received yet.</p>
                </div>
            `;
            } else {
                messages.forEach(message => {
                    const shortMessage = message.message.length > 150
                        ? message.message.substring(0, 150) + '...'
                        : message.message;
                    const date = new Date(message.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const replyCount = message.replies ? message.replies.length : 0;

                    html += `
                    <div class="message-item ${message.is_read ? '' : 'unread'}" data-message-id="${message.id}">
                        <div class="d-flex justify-between align-start mb-2">
                            <div style="flex: 1;">
                                <h4 style="color: var(--light); margin: 0 0 0.25rem 0;">${message.subject}</h4>
                                <div style="display: flex; gap: 1rem; color: var(--gray-dark); font-size: 0.9rem;">
                                    <span><i class="fas fa-user"></i> ${message.name}</span>
                                    <span><i class="fas fa-envelope"></i> ${message.email}</span>
                                    <span><i class="fas fa-clock"></i> ${date}</span>
                                    ${replyCount > 0 ? `<span><i class="fas fa-reply"></i> ${replyCount} reply${replyCount !== 1 ? 's' : ''}</span>` : ''}
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                ${!message.is_read ? `
                                    <button class="btn btn-success btn-sm" onclick="adminDashboard.markAsRead(${message.id})" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-info btn-sm" onclick="adminDashboard.replyToMessage(${message.id})" title="Reply to message">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="adminDashboard.viewMessage(${message.id})" title="View message">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="adminDashboard.deleteMessage(${message.id})" title="Delete message">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p style="color: var(--gray); line-height: 1.4; margin: 0;">${shortMessage}</p>
                        ${!message.is_read ? `
                            <div style="margin-top: 0.5rem;">
                                <span class="status-badge status-active">New</span>
                            </div>
                        ` : ''}
                    </div>
                `;
                });
            }

            html += `
                        </div>
                    </div>
                </div>
            </section>
        `;

            await this.updateMessagesCount();
            return html;

        } catch (error) {
            console.error('Error loading messages:', error);
            return `
            <section class="section-content">
                <div class="section-card">
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
                            <h4 style="color: var(--light); margin-bottom: 1rem;">Error Loading Messages</h4>
                            <p style="color: var(--gray);">${error.message}</p>
                            <button class="btn btn-primary" onclick="adminDashboard.loadSectionContent('messages')">
                                <i class="fas fa-redo"></i> Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        `;
        }
    }

    async markAsRead(messageId) {
        try {
            const response = await fetch(`${this.baseUrl}/messages/${messageId}/read`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.classList.remove('unread');
                    messageElement.querySelector('.btn-success').remove();
                }
                await this.updateMessagesCount();
                this.showNotification('Message marked as read', 'success');
            }
        } catch (error) {
            console.error('Error marking message as read:', error);
            this.showNotification('Error marking message as read', 'error');
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch(`${this.baseUrl}/messages/mark-all-read`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                document.querySelectorAll('.message-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const markReadBtn = item.querySelector('.btn-success');
                    if (markReadBtn) markReadBtn.remove();
                });
                await this.updateMessagesCount();
                this.showNotification('All messages marked as read', 'success');
            }
        } catch (error) {
            console.error('Error marking all messages as read:', error);
            this.showNotification('Error marking all messages as read', 'error');
        }
    }

    async replyToMessage(messageId) {
        try {
            const response = await fetch(`${this.baseUrl}/messages/${messageId}`);
            const message = await response.json();

            const modalHtml = `
            <div class="modal-overlay">
                <div class="modal-content" style="max-width: 700px;">
                    <div class="modal-header">
                        <h3 class="modal-title">Reply to ${message.name}</h3>
                        <button class="modal-close" onclick="adminDashboard.closeModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <strong>From:</strong><br>
                                    ${message.name}<br>
                                    <a href="mailto:${message.email}">${message.email}</a>
                                </div>
                                <div>
                                    <strong>Date:</strong><br>
                                    ${new Date(message.created_at).toLocaleString()}
                                </div>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>Subject:</strong><br>
                                ${message.subject}
                            </div>
                            <div style="background: rgba(115, 12, 14, 0.1); padding: 1rem; border-radius: var(--border-radius-sm); margin-bottom: 1.5rem;">
                                <strong>Original Message:</strong>
                                <p style="margin-top: 0.5rem; white-space: pre-wrap; line-height: 1.5;">${message.message}</p>
                            </div>
                        </div>

                        <form id="reply-form">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <div class="form-group">
                                <label class="form-label">Your Reply</label>
                                <textarea name="message" rows="6" class="form-control" placeholder="Type your reply here..." required style="width: 100%; padding: 1rem; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: var(--border-radius-sm); color: var(--light); font-family: inherit;"></textarea>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-secondary" onclick="adminDashboard.closeModal()">Cancel</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

            const modal = document.createElement('div');
            modal.innerHTML = modalHtml;
            document.body.appendChild(modal);

            const form = modal.querySelector('#reply-form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.sendReply(messageId, new FormData(form));
            });

        } catch (error) {
            console.error('Error loading message for reply:', error);
            this.showNotification('Error loading message', 'error');
        }
    }

    async sendReply(messageId, formData) {
        try {
            const submitBtn = document.querySelector('#reply-form button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            const response = await fetch(`${this.baseUrl}/messages/${messageId}/reply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Reply sent successfully', 'success');
                this.closeModal();
                await this.loadSectionContent('messages');
            } else {
                this.showNotification(result.message || 'Failed to send reply', 'error');
            }

        } catch (error) {
            console.error('Error sending reply:', error);
            this.showNotification('Error sending reply', 'error');
        } finally {
            const submitBtn = document.querySelector('#reply-form button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reply';
            }
        }
    }

    async viewMessage(messageId) {
        try {
            const response = await fetch(`${this.baseUrl}/messages/${messageId}`);
            const message = await response.json();

            let repliesHtml = '';
            if (message.replies && message.replies.length > 0) {
                repliesHtml = `
                <div style="margin-top: 2rem;">
                    <h4 style="color: var(--light); margin-bottom: 1rem;">Conversation</h4>
                    <div class="message-thread">
            `;

                message.replies.forEach(reply => {
                    repliesHtml += `
                    <div style="background: rgba(76, 111, 255, 0.1); padding: 1rem; border-radius: var(--border-radius-sm); margin-bottom: 1rem; border-left: 4px solid var(--accent);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <strong style="color: var(--accent);">Admin</strong>
                            <small style="color: var(--gray-dark);">${new Date(reply.created_at).toLocaleString()}</small>
                        </div>
                        <p style="margin: 0; white-space: pre-wrap; line-height: 1.5;">${reply.message}</p>
                    </div>
                `;
                });

                repliesHtml += `</div></div>`;
            }

            const modalHtml = `
            <div class="modal-overlay">
                <div class="modal-content" style="max-width: 700px;">
                    <div class="modal-header">
                        <h3 class="modal-title">Message from ${message.name}</h3>
                        <button class="modal-close" onclick="adminDashboard.closeModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <strong>From:</strong><br>
                                    ${message.name}<br>
                                    <a href="mailto:${message.email}">${message.email}</a>
                                </div>
                                <div>
                                    <strong>Date:</strong><br>
                                    ${new Date(message.created_at).toLocaleString()}
                                </div>
                            </div>
                            <div>
                                <strong>Subject:</strong><br>
                                ${message.subject}
                            </div>
                        </div>
                        <div style="background: rgba(115, 12, 14, 0.1); padding: 1rem; border-radius: var(--border-radius-sm); margin-bottom: 1.5rem;">
                            <strong>Message:</strong>
                            <p style="margin-top: 0.5rem; white-space: pre-wrap; line-height: 1.5;">${message.message}</p>
                        </div>
                        
                        ${repliesHtml}

                        ${message.ip_address ? `
                            <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--gray-dark);">
                                <strong>Technical Info:</strong><br>
                                IP: ${message.ip_address}<br>
                                User Agent: ${message.user_agent || 'N/A'}
                            </div>
                        ` : ''}
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary" onclick="adminDashboard.closeModal()">Close</button>
                            <button type="button" class="btn btn-info" onclick="adminDashboard.replyToMessage(${message.id}); adminDashboard.closeModal();">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                            ${!message.is_read ? `
                                <button type="button" class="btn btn-success" onclick="adminDashboard.markAsRead(${message.id}); adminDashboard.closeModal();">
                                    Mark as Read
                                </button>
                            ` : ''}
                            <button type="button" class="btn btn-danger" onclick="adminDashboard.deleteMessage(${message.id}); adminDashboard.closeModal();">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            const modal = document.createElement('div');
            modal.innerHTML = modalHtml;
            document.body.appendChild(modal);
        } catch (error) {
            console.error('Error loading message:', error);
            this.showNotification('Error loading message', 'error');
        }
    }

    async deleteMessage(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) return;

        try {
            const response = await fetch(`${this.baseUrl}/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Message deleted successfully', 'success');

                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.style.opacity = '0';
                    setTimeout(() => messageElement.remove(), 300);
                }

                await this.updateMessagesCount();
                this.loadDashboardData();
            }
        } catch (error) {
            console.error('Error deleting message:', error);
            this.showNotification('Error deleting message', 'error');
        }
    }

    // =====================
    // FORM HANDLING
    // =====================

    async handleFormSubmit(form) {
        console.log('=== FORM SUBMISSION START ===');

        if (form.classList.contains('submitting')) {
            return;
        }

        const formData = new FormData(form);
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;

        form.classList.add('submitting');
        button.classList.add('loading');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');
                this.closeModal();
                await this.loadSectionContent(this.currentSection);
                this.loadDashboardData();
            } else {
                this.closeModal();
                this.showNotification(result.message || 'An error occurred', 'error');
            }

        } catch (error) {
            console.error('Form submission error:', error);
            this.closeModal();

            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                this.showNotification('Network error. Please check your connection and try again.', 'error');
            } else {
                this.showNotification('An unexpected error occurred. Please try again.', 'error');
            }
        } finally {
            form.classList.remove('submitting');
            button.classList.remove('loading');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    prettifyErrorMessage(errorMessage) {
        console.log('Raw error message:', errorMessage);

        // Handle duplicate entry errors
        if (errorMessage.includes('Duplicate entry') && errorMessage.includes('for key')) {
            // Extract the duplicate value from the error message
            const match = errorMessage.match(/Duplicate entry '([^']+)'/);
            const duplicateValue = match ? match[1] : 'this value';

            return `A skill with the name "${duplicateValue}" already exists. Please use a different name.`;
        }

        // Handle other SQL constraint violations
        if (errorMessage.includes('Integrity constraint violation')) {
            if (errorMessage.includes('skills_name_unique')) {
                return 'A skill with this name already exists. Please choose a different name.';
            }
            return 'This item already exists in the system. Please check for duplicates.';
        }

        // Handle SQL syntax errors (show generic message)
        if (errorMessage.includes('SQLSTATE') || errorMessage.includes('SQL syntax')) {
            return 'A database error occurred. Please try again.';
        }

        // Handle connection errors
        if (errorMessage.includes('Connection: mysql')) {
            return 'Database connection error. Please try again.';
        }

        // Return the original message if no specific formatting applies
        return errorMessage.length > 150 ? 'An error occurred. Please check your input and try again.' : errorMessage;
    }

    // Add these helper methods for form error handling
    clearFormErrors(form) {
        // Remove existing error messages
        form.querySelectorAll('.error-message').forEach(error => error.remove());

        // Remove error classes from inputs
        form.querySelectorAll('.error').forEach(input => input.classList.remove('error'));
    }

    showFormErrors(form, errors) {
        // Clear previous errors first
        this.clearFormErrors(form);

        // Add error messages for each field
        Object.keys(errors).forEach(fieldName => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                // Add error class to input
                input.classList.add('error');

                // Create error message element
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.style.cssText = `
                color: var(--danger);
                font-size: 0.8rem;
                margin-top: 0.25rem;
                padding: 0.25rem 0.5rem;
                background: rgba(220, 53, 69, 0.1);
                border-radius: 4px;
                border-left: 3px solid var(--danger);
            `;

                // Prettify the error message
                let errorText = errors[fieldName][0];
                if (errorText.includes('unique')) {
                    errorText = 'This value already exists. Please choose a different one.';
                }

                errorElement.textContent = errorText;

                // Insert error message after the input
                input.parentNode.appendChild(errorElement);
            }
        });
    }

    // =====================
    // UTILITY METHODS
    // =====================

    async switchSection(section) {
        if (!section) {
            console.error('Section parameter is undefined');
            return;
        }
        
        this.currentSection = section;

        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        const activeLink = document.querySelector(`[data-section="${section}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }

        const pageTitle = document.getElementById('pageTitle');
        if (pageTitle) {
            pageTitle.textContent = section.charAt(0).toUpperCase() + section.slice(1);
        }

        document.querySelectorAll('.section-content').forEach(sectionEl => {
            sectionEl.classList.remove('active');
        });

        await this.loadSectionContent(section);
    }

    async loadSectionContent(section) {
        const container = document.getElementById('dynamic-sections');
        container.innerHTML = '<div class="text-center">Loading...</div>';

        try {
            let html = '';

            switch (section) {
                case 'dashboard':
                    document.getElementById('dashboard').classList.add('active');
                    return;
                case 'skills':
                    html = await this.getSkillsContent();
                    break;
                case 'projects':
                    html = await this.getProjectsContent();
                    break;
                case 'messages':
                    html = await this.getMessagesContent();
                    break;
                case 'profile':
                    html = document.getElementById('profile-template').innerHTML;
                    break;
                case 'analytics':
                    html = await this.getAnalyticsContent();
                    break;
                case 'settings':
                    html = await this.getSettingsContent();
                    break;
                case 'sections':
                    html = await this.getSectionsContent();
                    break;
                default:
                    html = '<div class="text-center">Section not found</div>';
            }

            container.innerHTML = html;
            const sectionElement = container.querySelector('.section-content');
            if (sectionElement) {
                sectionElement.classList.add('active');
            }

            this.bindSectionEvents();

        } catch (error) {
            console.error('Error loading section:', error);
            this.showNotification('Error loading content', 'error');
            container.innerHTML = '<div class="text-center">Error loading content</div>';
        }
    }

    bindSectionEvents() {
        // Remove existing event listeners first to prevent duplicates
        this.removeEventListeners();

        // Bind form submissions - use event delegation
        this.formSubmitHandler = (e) => {
            const form = e.target;
            if (form.classList.contains('ajax-form')) {
                e.preventDefault();
                e.stopImmediatePropagation();
                this.handleFormSubmit(form);
            }
        };
        document.addEventListener('submit', this.formSubmitHandler);

        // Bind button clicks using simpler event delegation
        this.buttonClickHandler = (e) => {
            const button = e.target.closest('button');
            if (!button) return;

            // Check for specific data attributes or onclick handlers
            if (button.hasAttribute('onclick')) {
                const onclick = button.getAttribute('onclick');

                if (onclick.includes('showSkillForm')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    this.showSkillForm();
                }
                else if (onclick.includes('showProjectForm')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    this.showProjectForm();
                }
                else if (onclick.includes('editSkill')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const match = onclick.match(/editSkill\((\d+)\)/);
                    if (match) this.editSkill(parseInt(match[1]));
                }
                else if (onclick.includes('deleteSkill')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const match = onclick.match(/deleteSkill\((\d+)\)/);
                    if (match) this.deleteSkill(parseInt(match[1]));
                }
                else if (onclick.includes('editProject')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const match = onclick.match(/editProject\((\d+)\)/);
                    if (match) this.editProject(parseInt(match[1]));
                }
                else if (onclick.includes('deleteProject')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const match = onclick.match(/deleteProject\((\d+)\)/);
                    if (match) this.deleteProject(parseInt(match[1]));
                }
                else if (onclick.includes('showSectionForm')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    this.showSectionForm();
                }
                else if (onclick.includes('editSection')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const match = onclick.match(/editSection\((\d+)\)/);
                    if (match) this.editSection(parseInt(match[1]));
                }
                else if (onclick.includes('deleteSection')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    const match = onclick.match(/deleteSection\((\d+)\)/);
                    if (match) this.deleteSection(parseInt(match[1]));
                }
            }
        };

        document.addEventListener('click', this.buttonClickHandler, true);
    }

    removeEventListeners() {
        if (this.formSubmitHandler) {
            document.removeEventListener('submit', this.formSubmitHandler);
        }
        if (this.buttonClickHandler) {
            document.removeEventListener('click', this.buttonClickHandler);
        }
    }

    initializeModalEvents(modal) {
        const closeBtn = modal.querySelector('.modal-close');
        const overlay = modal.querySelector('.modal-overlay');

        if (closeBtn) {
            closeBtn.onclick = () => this.closeModal();
        }

        if (overlay) {
            overlay.onclick = (e) => {
                if (e.target === overlay) {
                    this.closeModal();
                }
            };
        }
    }

    setFormValue(form, selector, value) {
        const element = form.querySelector(selector);
        if (element) {
            element.value = value || '';
        } else {
            console.warn('Form element not found:', selector);
        }
    }

    async updateSkillsCount() {
        try {
            const response = await fetch(`${this.baseUrl}/skills`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const skills = await response.json();

            const skillsCountElement = document.getElementById('skills-count');
            if (skillsCountElement) {
                skillsCountElement.textContent = skills.length;
            }

            const statsElement = document.querySelector('[data-stat="skills"] .stat-value');
            if (statsElement) {
                statsElement.textContent = skills.length;
            }

            return skills.length;
        } catch (error) {
            console.error('Error updating skills count:', error);
            return 0;
        }
    }

    async updateProjectsCount() {
        try {
            const response = await fetch(`${this.baseUrl}/projects`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const projects = await response.json();

            const projectsCountElement = document.getElementById('projects-count');
            if (projectsCountElement) {
                projectsCountElement.textContent = projects.length;
            }

            const statsElement = document.querySelector('[data-stat="projects"] .stat-value');
            if (statsElement) {
                statsElement.textContent = projects.length;
            }

            return projects.length;
        } catch (error) {
            console.error('Error updating projects count:', error);
            return 0;
        }
    }

    async updateMessagesCount() {
        try {
            const response = await fetch(`${this.baseUrl}/messages-stats`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const stats = await response.json();

            if (stats && typeof stats.unread !== 'undefined') {
                const messagesCountElement = document.getElementById('messages-count');
                if (messagesCountElement) {
                    messagesCountElement.textContent = stats.unread;
                }

                const statsElement = document.querySelector('[data-stat="messages"] .stat-value');
                if (statsElement) {
                    statsElement.textContent = stats.unread;
                }

                return stats.unread;
            }
            return 0;
        } catch (error) {
            console.error('Error updating messages count:', error);
            return 0;
        }
    }

    async loadDashboardData() {
        try {
            const response = await fetch(`${this.baseUrl}/dashboard-data`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            this.updateStats(data.stats);
            this.updateRecentActivities(data.recent_activities);

            await this.updateSkillsCount();
            await this.updateProjectsCount();
            await this.updateMessagesCount();
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            this.updateStats({
                total_projects: 0,
                active_skills: 0,
                unread_messages: 0
            });
            this.updateRecentActivities([]);
        }
    }

    updateStats(stats) {
        if (stats) {
            const projectElement = document.querySelector('[data-stat="projects"] .stat-value');
            const skillsElement = document.querySelector('[data-stat="skills"] .stat-value');
            const messagesElement = document.querySelector('[data-stat="messages"] .stat-value');

            if (projectElement) projectElement.textContent = stats.total_projects || 0;
            if (skillsElement) skillsElement.textContent = stats.active_skills || 0;
            if (messagesElement) messagesElement.textContent = stats.unread_messages || 0;
        }
    }

    updateRecentActivities(activities) {
        const tbody = document.querySelector('#recent-activities tbody');
        if (!tbody) return;

        if (!activities || activities.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No recent activities</td></tr>';
            return;
        }

        tbody.innerHTML = activities.map(activity => `
            <tr>
                <td>
                    <div class="activity-action">
                        <i class="${activity.icon || 'fas fa-circle'}"></i>
                        ${activity.action}
                    </div>
                </td>
                <td>
                    <div class="activity-item">
                        <div>${activity.item}</div>
                        <small class="text-muted">by ${activity.user || 'System'}</small>
                    </div>
                </td>
                <td>${activity.date}</td>
                <td>
                    <span class="status-badge ${activity.status.toLowerCase()}">
                        ${activity.status}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    refreshDashboard() {
        this.loadDashboardData();
        this.showNotification('Dashboard refreshed', 'success');
    }

    confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            this.performLogout();
        }
    }

    performLogout() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/logout';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = this.getCsrfToken();

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }

    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            console.error('CSRF token not found');
            return '';
        }
        return token;
    }

    refreshCsrfToken() {
        fetch('/admin/csrf-token', {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                document.querySelector('meta[name="csrf-token"]').content = data.token;
            }
        })
        .catch(error => console.error('Error refreshing CSRF token:', error));
    }

    showNotification(message, type = 'info') {
        this.notificationSystem.showNotification(message, type);
    }

    closeModal() {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => {
            modal.remove();
        });
    }

    // Other sections (simplified)
    async getAnalyticsContent() {
        try {
            const response = await fetch(`${this.baseUrl}/analytics`);
            const data = await response.json();
            
            return `
                <section class="section-content">
                    <!-- Analytics Stats Grid -->
                    <div class="dashboard-grid">
                        <div class="stat-card" data-stat="total-views">
                            <div class="stat-header">
                                <div class="stat-icon views">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">${data.page_views.total}</div>
                                    <div class="stat-label">Total Views</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+12%</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card" data-stat="unique-visitors">
                            <div class="stat-header">
                                <div class="stat-icon visitors">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">${data.visitors.unique}</div>
                                    <div class="stat-label">Unique Visitors</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+8%</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card" data-stat="monthly-views">
                            <div class="stat-header">
                                <div class="stat-icon monthly">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">${data.page_views.monthly}</div>
                                    <div class="stat-label">This Month</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+15%</span>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card" data-stat="today-views">
                            <div class="stat-header">
                                <div class="stat-icon today">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-value">${data.page_views.daily}</div>
                                    <div class="stat-label">Today</div>
                                </div>
                                <div class="stat-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+5%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Cards -->
                    <div class="analytics-section">
                        <div class="section-card">
                            <div class="card-header">
                                <h2 class="card-title">Popular Pages</h2>
                                <div class="card-actions">
                                    <button class="btn btn-primary btn-sm" onclick="adminDashboard.refreshDashboard()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="analytics-list">
                                    ${data.popular_pages.map((page, index) => `
                                        <div class="analytics-item">
                                            <div class="item-rank">#${index + 1}</div>
                                            <div class="item-info">
                                                <div class="item-title">${page.page === '/' ? 'Home' : page.page}</div>
                                                <div class="item-subtitle">${page.page}</div>
                                            </div>
                                            <div class="item-value">
                                                <span class="value-number">${page.views}</span>
                                                <span class="value-label">views</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>

                        <div class="section-card">
                            <div class="card-header">
                                <h2 class="card-title">Device Analytics</h2>
                            </div>
                            <div class="card-body">
                                <div class="device-analytics">
                                    ${data.device_stats.map(device => {
                                        const total = data.device_stats.reduce((sum, d) => sum + d.count, 0);
                                        const percentage = Math.round((device.count / total) * 100);
                                        return `
                                            <div class="device-stat">
                                                <div class="device-info">
                                                    <i class="fas fa-${device.device_type === 'Mobile' ? 'mobile-alt' : 'desktop'}"></i>
                                                    <span class="device-name">${device.device_type || 'Unknown'}</span>
                                                </div>
                                                <div class="device-progress">
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: ${percentage}%"></div>
                                                    </div>
                                                    <span class="device-percentage">${percentage}%</span>
                                                </div>
                                                <div class="device-count">${device.count}</div>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        </div>

                        <div class="section-card">
                            <div class="card-header">
                                <h2 class="card-title">Browser Statistics</h2>
                            </div>
                            <div class="card-body">
                                <div class="browser-analytics">
                                    ${data.browser_stats.map(browser => {
                                        const total = data.browser_stats.reduce((sum, b) => sum + b.count, 0);
                                        const percentage = Math.round((browser.count / total) * 100);
                                        return `
                                            <div class="browser-stat">
                                                <div class="browser-icon">
                                                    <i class="fab fa-${browser.browser?.toLowerCase() || 'question'}"></i>
                                                </div>
                                                <div class="browser-info">
                                                    <div class="browser-name">${browser.browser || 'Unknown'}</div>
                                                    <div class="browser-usage">${browser.count} visits (${percentage}%)</div>
                                                </div>
                                                <div class="browser-chart">
                                                    <div class="mini-chart" style="--percentage: ${percentage}%"></div>
                                                </div>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            `;
        } catch (error) {
            return `
                <section class="section-content">
                    <div class="section-card">
                        <div class="card-header">
                            <h3 class="card-title">Analytics</h3>
                        </div>
                        <div class="card-body">
                            <div class="error-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error loading analytics data. Please try again.</p>
                                <button class="btn btn-primary" onclick="adminDashboard.refreshDashboard()">Retry</button>
                            </div>
                        </div>
                    </div>
                </section>
            `;
        }
    }

    async getSettingsContent() {
        return `
            <link rel="stylesheet" href="/css/settings.css">
            <div class="settings-container">
                <div class="settings-header">
                    <h2><i class="fas fa-cog"></i> Advanced Settings</h2>
                    <p>Configure and customize your portfolio website</p>
                </div>
                
                <div class="settings-tabs">
                    <button class="settings-tab active" data-tab="general">
                        <i class="fas fa-home"></i> General
                    </button>
                    <button class="settings-tab" data-tab="security">
                        <i class="fas fa-shield-alt"></i> Security
                    </button>
                    <button class="settings-tab" data-tab="email">
                        <i class="fas fa-envelope"></i> Email
                    </button>
                    <button class="settings-tab" data-tab="backup">
                        <i class="fas fa-database"></i> Backup
                    </button>
                </div>
                
                <div class="settings-content">
                    <div id="settings-alert"></div>
                    
                    <div class="settings-section active" id="general-settings">
                        <form class="settings-form" data-group="general">
                            <div class="form-group">
                                <label for="site_name"><i class="fas fa-globe"></i> Site Name *</label>
                                <input type="text" id="site_name" name="site_name" class="form-control" value="Portfolio Website" required>
                                <div class="help-text">The name of your portfolio website displayed in browser title</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_description"><i class="fas fa-align-left"></i> Site Description</label>
                                <textarea id="site_description" name="site_description" class="form-control" rows="3" placeholder="Professional portfolio showcasing my work and skills"></textarea>
                                <div class="help-text">Brief description for SEO meta tags and social media sharing</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_keywords"><i class="fas fa-tags"></i> SEO Keywords</label>
                                <textarea id="site_keywords" name="site_keywords" class="form-control" rows="2" placeholder="portfolio, web developer, designer, projects"></textarea>
                                <div class="help-text">Comma-separated keywords to improve search engine visibility</div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="timezone"><i class="fas fa-clock"></i> Timezone *</label>
                                    <select id="timezone" name="timezone" class="form-control select-control" required>
                                        <option value="UTC">UTC (Coordinated Universal Time)</option>
                                        <option value="America/New_York">Eastern Time (New York)</option>
                                        <option value="America/Chicago">Central Time (Chicago)</option>
                                        <option value="America/Denver">Mountain Time (Denver)</option>
                                        <option value="America/Los_Angeles">Pacific Time (Los Angeles)</option>
                                        <option value="Europe/London">London (GMT)</option>
                                        <option value="Europe/Paris">Paris (CET)</option>
                                        <option value="Asia/Tokyo">Tokyo (JST)</option>
                                        <option value="Asia/Dubai">Dubai (GST)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode">
                                        <label for="maintenance_mode"><i class="fas fa-tools"></i> Maintenance Mode</label>
                                    </div>
                                    <div class="help-text">Show maintenance page to visitors while updating site</div>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="loadSettings()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="settings-section" id="security-settings">
                        <form class="settings-form" data-group="security">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="session_timeout"><i class="fas fa-hourglass-half"></i> Session Timeout (minutes) *</label>
                                    <input type="number" id="session_timeout" name="session_timeout" class="form-control" min="5" max="1440" value="30" required>
                                    <div class="help-text">Automatically logout admin after inactivity (5-1440 minutes)</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="max_login_attempts"><i class="fas fa-lock"></i> Max Login Attempts *</label>
                                    <input type="number" id="max_login_attempts" name="max_login_attempts" class="form-control" min="3" max="10" value="5" required>
                                    <div class="help-text">Lock account after failed login attempts (3-10)</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_expiry_days"><i class="fas fa-key"></i> Password Expiry (days)</label>
                                <input type="number" id="password_expiry_days" name="password_expiry_days" class="form-control" min="30" max="365" placeholder="90">
                                <div class="help-text">Force password change after specified days (30-365, leave empty for never)</div>
                            </div>
                            
                            <div class="form-group super-admin-section">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="two_factor_enabled" name="two_factor_enabled">
                                    <label for="two_factor_enabled"><i class="fas fa-mobile-alt"></i> Two-Factor Authentication</label>
                                    <span class="permission-badge"><i class="fas fa-crown"></i> Super Admin</span>
                                </div>
                                <div class="help-text">Require SMS or app-based 2FA for enhanced security</div>
                            </div>
                            
                            <div class="form-group super-admin-section">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="force_https" name="force_https">
                                    <label for="force_https"><i class="fas fa-certificate"></i> Force HTTPS</label>
                                    <span class="permission-badge"><i class="fas fa-crown"></i> Super Admin</span>
                                </div>
                                <div class="help-text">Automatically redirect all HTTP requests to HTTPS</div>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-shield-alt"></i> Save Security Settings
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="loadSettings()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="settings-section" id="email-settings">
                        <form class="settings-form" data-group="email">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_host"><i class="fas fa-server"></i> SMTP Host</label>
                                    <input type="text" id="smtp_host" name="smtp_host" class="form-control" placeholder="smtp.gmail.com">
                                    <div class="help-text">Your email provider's SMTP server address</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="smtp_port"><i class="fas fa-plug"></i> SMTP Port</label>
                                    <input type="number" id="smtp_port" name="smtp_port" class="form-control" placeholder="587" min="1" max="65535">
                                    <div class="help-text">Usually 587 for TLS or 465 for SSL</div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_username"><i class="fas fa-user"></i> SMTP Username</label>
                                    <input type="text" id="smtp_username" name="smtp_username" class="form-control" placeholder="your-email@gmail.com">
                                    <div class="help-text">Your email address for SMTP authentication</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="smtp_password"><i class="fas fa-eye-slash"></i> SMTP Password</label>
                                    <input type="password" id="smtp_password" name="smtp_password" class="form-control" placeholder="Leave blank to keep current">
                                    <div class="help-text">App password or account password (encrypted storage)</div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mail_from_address"><i class="fas fa-at"></i> From Email Address</label>
                                    <input type="email" id="mail_from_address" name="mail_from_address" class="form-control" placeholder="noreply@yoursite.com">
                                    <div class="help-text">Email address shown as sender in outgoing emails</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="mail_from_name"><i class="fas fa-signature"></i> From Name</label>
                                    <input type="text" id="mail_from_name" name="mail_from_name" class="form-control" placeholder="Your Portfolio">
                                    <div class="help-text">Display name for outgoing emails</div>
                                </div>
                            </div>
                            
                            <div class="test-email-section">
                                <h4><i class="fas fa-paper-plane"></i> Test Email Configuration</h4>
                                <p>Send a test email to verify your SMTP settings are working correctly.</p>
                                <div class="input-group">
                                    <input type="email" id="test_email" class="form-control" placeholder="Enter email address to test">
                                    <button type="button" class="btn btn-success" onclick="testEmail()">
                                        <i class="fas fa-paper-plane"></i> Send Test Email
                                    </button>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-envelope"></i> Save Email Settings
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="loadSettings()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="settings-section" id="backup-settings">
                        <form class="settings-form" data-group="backup">
                            <div class="form-group super-admin-section">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="auto_backup_enabled" name="auto_backup_enabled">
                                    <label for="auto_backup_enabled"><i class="fas fa-robot"></i> Enable Automatic Backups</label>
                                    <span class="permission-badge"><i class="fas fa-crown"></i> Super Admin</span>
                                </div>
                                <div class="help-text">Automatically backup database and files on schedule (requires cron job setup)</div>
                            </div>
                            
                            <div class="backup-schedule-info" style="background: rgba(59, 130, 246, 0.1); padding: 1rem; border-radius: 8px; margin: 1rem 0; border-left: 4px solid #3b82f6;">
                                <h5 style="color: #3b82f6; margin: 0 0 0.5rem;"><i class="fas fa-info-circle"></i> Cron Job Setup Required</h5>
                                <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">To enable automatic backups, add this to your server's crontab:</p>
                                <div style="background: rgba(0,0,0,0.3); padding: 1rem; margin: 0.5rem 0; border-radius: 4px; border: 1px solid rgba(255,255,255,0.1);">
                                    <code style="color: #22c55e; font-family: 'Courier New', monospace; font-size: 0.9rem; display: block; word-break: break-all;">* * * * * cd "c:\Users\Al Baraa\Desktop\yamin portfolio\portfolio-website" && php artisan schedule:run >> /dev/null 2>&1</code>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <p style="margin: 0 0 0.5rem; color: var(--gray-dark); font-size: 0.8rem;"><strong>For Windows (Task Scheduler):</strong></p>
                                    <code style="display: block; background: rgba(0,0,0,0.2); padding: 0.5rem; border-radius: 4px; font-family: monospace; font-size: 0.8rem;">cd "c:\Users\Al Baraa\Desktop\yamin portfolio\portfolio-website" && php artisan schedule:run</code>
                                    <p style="margin: 0.5rem 0 0; color: var(--gray-dark); font-size: 0.8rem;">Run this every minute in Windows Task Scheduler</p>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="backup_frequency"><i class="fas fa-calendar-alt"></i> Backup Frequency</label>
                                    <select id="backup_frequency" name="backup_frequency" class="form-control select-control">
                                        <option value="">Select backup frequency</option>
                                        <option value="daily">Daily (Recommended)</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    <div class="help-text">How often to create automatic backups</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="backup_retention_days"><i class="fas fa-history"></i> Retention Period (days)</label>
                                    <input type="number" id="backup_retention_days" name="backup_retention_days" class="form-control" min="1" max="365" placeholder="30">
                                    <div class="help-text">How long to keep backup files (1-365 days)</div>
                                </div>
                            </div>
                            
                            <div style="background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%); padding: 2rem; border-radius: 16px; margin: 2rem 0; border: 2px solid #fc8181;">
                                <h4 style="color: #742a2a; margin: 0 0 1rem;"><i class="fas fa-exclamation-triangle"></i> Manual Backup</h4>
                                <p style="color: #742a2a; margin: 0 0 1rem;">Create an immediate backup of your database and files. This may take a few minutes.</p>
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                                    <button type="button" class="btn btn-success" onclick="createBackup()" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                                        <i class="fas fa-download"></i> Create Backup Now
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="testAutoBackup()" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                        <i class="fas fa-play"></i> Test Auto Backup
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="checkCronStatus()" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                        <i class="fas fa-clock"></i> Check Cron Status
                                    </button>
                                </div>
                            </div>
                            
                            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 2rem; border-radius: 16px; margin: 2rem 0; border: 2px solid #0ea5e9;">
                                <h4 style="color: #0c4a6e; margin: 0 0 1rem;"><i class="fas fa-archive"></i> Existing Backups</h4>
                                <p style="color: #0c4a6e; margin: 0 0 1rem;">Manage your existing backup files. Click refresh to update the list.</p>
                                <div style="margin-bottom: 1rem;">
                                    <button type="button" class="btn btn-primary" onclick="loadBackupsList()" style="margin-right: 1rem;">
                                        <i class="fas fa-sync-alt"></i> Refresh List
                                    </button>
                                </div>
                                <div id="backups-list" style="margin-top: 1rem;">
                                    <div class="loading-text">Click refresh to load backups...</div>
                                </div>
                            </div>
                            
                            <style>
                            .backups-grid {
                                display: grid;
                                gap: 1rem;
                                margin-top: 1rem;
                            }
                            .backup-item {
                                background: rgba(255, 255, 255, 0.1);
                                border: 1px solid rgba(255, 255, 255, 0.2);
                                border-radius: 8px;
                                padding: 1rem;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                            }
                            .backup-info h5 {
                                margin: 0 0 0.5rem 0;
                                color: var(--light);
                            }
                            .backup-details {
                                display: flex;
                                gap: 1rem;
                                margin-bottom: 0.5rem;
                                font-size: 0.9rem;
                                color: var(--gray);
                            }
                            .backup-files {
                                display: flex;
                                gap: 0.5rem;
                            }
                            .file-badge {
                                padding: 0.2rem 0.5rem;
                                border-radius: 4px;
                                font-size: 0.8rem;
                                font-weight: 500;
                            }
                            .file-badge.database {
                                background: rgba(34, 197, 94, 0.2);
                                color: #22c55e;
                                border: 1px solid rgba(34, 197, 94, 0.3);
                            }
                            .file-badge.files {
                                background: rgba(59, 130, 246, 0.2);
                                color: #3b82f6;
                                border: 1px solid rgba(59, 130, 246, 0.3);
                            }
                            .no-backups, .error-text, .loading-text {
                                text-align: center;
                                padding: 2rem;
                                color: var(--gray);
                            }
                            .error-text {
                                color: var(--danger);
                            }
                            </style>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-database"></i> Save Backup Settings
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="loadSettings()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }
}

// Settings functionality
let currentSettings = {};
let userPermissions = {};

function initializeSettings() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('settings-tab')) {
            const tabName = e.target.dataset.tab;
            switchSettingsTab(tabName);
        }
    });

    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('settings-form')) {
            e.preventDefault();
            saveSettings(e.target);
        }
    });

    loadSettings();
}

function switchSettingsTab(tabName) {
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

    document.querySelectorAll('.settings-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(`${tabName}-settings`).classList.add('active');
}

function loadSettings() {
    showAlert('Loading settings...', 'info');
    
    fetch('/admin/settings', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            currentSettings = data.settings || {};
            userPermissions = data.user_permissions || {};
            populateSettingsForm();
            updatePermissions();
            showAlert('Settings loaded successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to load settings');
        }
    })
    .catch(error => {
        console.error('Settings load error:', error);
        showAlert('Error loading settings: ' + error.message, 'error');
        // Load default values on error
        loadDefaultSettings();
    });
}

function loadDefaultSettings() {
    // Set default values when loading fails
    document.getElementById('site_name').value = 'Portfolio Website';
    document.getElementById('site_description').value = 'Professional Portfolio';
    document.getElementById('timezone').value = 'UTC';
    document.getElementById('session_timeout').value = '30';
    document.getElementById('max_login_attempts').value = '5';
}

function populateSettingsForm() {
    Object.keys(currentSettings).forEach(group => {
        Object.keys(currentSettings[group]).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                const value = currentSettings[group][key].value;
                if (element.type === 'checkbox') {
                    element.checked = Boolean(value);
                } else {
                    element.value = value || '';
                }
            }
        });
    });
}

function updatePermissions() {
    const superAdminSections = document.querySelectorAll('.super-admin-section');
    superAdminSections.forEach(section => {
        if (!userPermissions.is_super_admin) {
            section.classList.add('super-admin-only');
            const inputs = section.querySelectorAll('input, select, textarea');
            inputs.forEach(input => input.disabled = true);
        }
    });
}

function saveSettings(form) {
    const group = form.dataset.group;
    const formData = new FormData(form);
    const settings = {};

    // Clear previous errors
    clearFormErrors(form);

    // Process form data with proper type conversion
    for (let [key, value] of formData.entries()) {
        const element = form.querySelector(`[name="${key}"]`);
        if (element.type === 'checkbox') {
            settings[key] = element.checked;
        } else if (element.type === 'number') {
            const numValue = parseInt(value);
            settings[key] = isNaN(numValue) ? 0 : numValue;
        } else {
            settings[key] = value.trim();
        }
    }

    // Handle unchecked checkboxes
    form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        if (!formData.has(checkbox.name)) {
            settings[checkbox.name] = false;
        }
    });

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span> Saving...';
    submitBtn.disabled = true;

    fetch('/admin/settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ group, settings })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert(data.message || 'Settings saved successfully!', 'success');
            // Add success animation
            form.style.transform = 'scale(1.02)';
            setTimeout(() => {
                form.style.transform = 'scale(1)';
            }, 200);
            loadSettings();
        } else {
            showAlert(data.message || 'Failed to save settings', 'error');
            if (data.errors) {
                displayValidationErrors(form, data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Settings save error:', error);
        showAlert('Network error: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function clearFormErrors(form) {
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.form-control.error').forEach(el => el.classList.remove('error'));
}

function testEmail() {
    const emailInput = document.getElementById('test_email');
    const email = emailInput.value.trim();
    
    if (!email) {
        showAlert('Please enter a valid email address', 'error');
        emailInput.focus();
        return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('Please enter a valid email format', 'error');
        emailInput.focus();
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="loading"></span> Sending...';
    btn.disabled = true;

    fetch('/admin/settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('✅ ' + (data.message || 'Test email sent successfully!'), 'success');
            emailInput.value = '';
            // Add success animation
            btn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 200);
        } else {
            showAlert('❌ ' + (data.message || 'Failed to send test email'), 'error');
        }
    })
    .catch(error => {
        console.error('Test email error:', error);
        showAlert('❌ Network error: ' + error.message, 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function createBackup() {
    if (!confirm('Create a backup now? This may take a few minutes and will include your database and files.')) {
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    const backupName = 'backup_' + new Date().toISOString().replace(/[:.]/g, '_').slice(0, 19);
    
    // Create progress modal
    const progressModal = createProgressModal();
    document.body.appendChild(progressModal);
    
    // Start backup
    fetch('/admin/settings/backup/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateProgressModal(100, 'Backup completed successfully!', true);
            setTimeout(() => {
                closeProgressModal();
                showAlert('🎉 ' + (data.message || 'Backup created successfully!'), 'success');
                loadBackupsList();
            }, 1500);
        } else {
            closeProgressModal();
            showAlert('❌ ' + (data.message || 'Failed to create backup'), 'error');
        }
    })
    .catch(error => {
        console.error('Backup creation error:', error);
        closeProgressModal();
        showAlert('❌ Network error: ' + error.message, 'error');
    });
    
    // Start progress tracking
    trackBackupProgress(backupName);
}

function createProgressModal() {
    const modal = document.createElement('div');
    modal.id = 'backup-progress-modal';
    modal.innerHTML = `
        <div class="modal-overlay" style="background: rgba(0,0,0,0.8); z-index: 10001;">
            <div class="modal-content" style="max-width: 500px; background: var(--dark); border: 1px solid rgba(255,255,255,0.1);">
                <div class="modal-header">
                    <h3 class="modal-title"><i class="fas fa-download"></i> Creating Backup</h3>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <div class="progress-container" style="margin-bottom: 1rem;">
                        <div class="progress-bar" style="width: 100%; height: 20px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden;">
                            <div class="progress-fill" style="width: 0%; height: 100%; background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); transition: width 0.3s ease; border-radius: 10px;"></div>
                        </div>
                        <div class="progress-text" style="text-align: center; margin-top: 0.5rem; color: var(--light); font-weight: 500;">0%</div>
                    </div>
                    <div class="progress-message" style="text-align: center; color: var(--gray); font-size: 0.9rem;">Initializing backup...</div>
                    <div class="progress-details" style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px; font-family: monospace; font-size: 0.8rem; color: var(--gray-dark); max-height: 100px; overflow-y: auto;"></div>
                </div>
            </div>
        </div>
    `;
    return modal;
}

function updateProgressModal(percentage, message, completed = false) {
    const modal = document.getElementById('backup-progress-modal');
    if (!modal) return;
    
    const progressFill = modal.querySelector('.progress-fill');
    const progressText = modal.querySelector('.progress-text');
    const progressMessage = modal.querySelector('.progress-message');
    const progressDetails = modal.querySelector('.progress-details');
    
    if (progressFill) {
        progressFill.style.width = percentage + '%';
        if (completed) {
            progressFill.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
        }
    }
    
    if (progressText) {
        progressText.textContent = Math.round(percentage) + '%';
    }
    
    if (progressMessage) {
        progressMessage.textContent = message;
        if (completed) {
            progressMessage.style.color = '#22c55e';
        }
    }
    
    if (progressDetails) {
        const timestamp = new Date().toLocaleTimeString();
        progressDetails.innerHTML += `<div>[${timestamp}] ${message}</div>`;
        progressDetails.scrollTop = progressDetails.scrollHeight;
    }
}

function closeProgressModal() {
    const modal = document.getElementById('backup-progress-modal');
    if (modal) {
        modal.remove();
    }
}

function trackBackupProgress(backupName) {
    let attempts = 0;
    const maxAttempts = 120; // 2 minutes max
    
    const checkProgress = () => {
        attempts++;
        
        fetch(`/admin/settings/backup/${backupName}/progress`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 404) {
                // Progress file not found yet, simulate initial progress
                const simulatedProgress = Math.min(attempts * 2, 15);
                updateProgressModal(simulatedProgress, 'Preparing backup...');
                
                if (attempts < maxAttempts) {
                    setTimeout(checkProgress, 1000);
                }
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                updateProgressModal(data.progress, data.message, data.completed);
                
                if (!data.completed && attempts < maxAttempts) {
                    setTimeout(checkProgress, 800);
                }
            } else if (attempts < maxAttempts) {
                setTimeout(checkProgress, 1000);
            }
        })
        .catch(error => {
            // Continue checking on error, might be temporary
            if (attempts < maxAttempts) {
                setTimeout(checkProgress, 1000);
            }
        });
    };
    
    // Start checking after a brief delay
    setTimeout(checkProgress, 500);
}

function loadBackupsList() {
    const backupsContainer = document.getElementById('backups-list');
    if (!backupsContainer) return;
    
    backupsContainer.innerHTML = '<div class="loading-text"><i class="fas fa-spinner fa-spin"></i> Loading backups...</div>';
    
    fetch('/admin/settings/backup/list', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (data.backups && data.backups.length > 0) {
                let html = '<div class="backups-grid">';
                data.backups.forEach(backup => {
                    html += `
                        <div class="backup-item">
                            <div class="backup-info">
                                <h5><i class="fas fa-archive"></i> ${backup.name}</h5>
                                <div class="backup-details">
                                    <span class="backup-date"><i class="fas fa-calendar"></i> ${backup.date}</span>
                                    <span class="backup-size"><i class="fas fa-hdd"></i> ${backup.size}</span>
                                </div>
                                <div class="backup-files">
                                    ${backup.files.database ? '<span class="file-badge database"><i class="fas fa-database"></i> Database</span>' : ''}
                                    ${backup.files.files ? '<span class="file-badge files"><i class="fas fa-folder"></i> Files</span>' : ''}
                                </div>
                            </div>
                            <div class="backup-actions">
                                <button class="btn btn-info btn-sm" onclick="restoreBackup('${backup.name}', 'database')" title="Restore database" style="margin-right: 0.5rem;">
                                    <i class="fas fa-database"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="restoreBackup('${backup.name}', 'files')" title="Restore files" style="margin-right: 0.5rem;">
                                    <i class="fas fa-folder"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteBackup('${backup.name}')" title="Delete backup">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                backupsContainer.innerHTML = html;
            } else {
                backupsContainer.innerHTML = '<div class="no-backups"><i class="fas fa-info-circle"></i> No backups found. Create your first backup above.</div>';
            }
        } else {
            throw new Error(data.message || 'Failed to load backups');
        }
    })
    .catch(error => {
        console.error('Load backups error:', error);
        backupsContainer.innerHTML = `<div class="error-text"><i class="fas fa-exclamation-triangle"></i> Error: ${error.message}</div>`;
    });
}

function restoreBackup(backupName, type) {
    const typeText = type === 'database' ? 'database' : 'files';
    if (!confirm(`⚠️ DANGER: This will restore the ${typeText} from backup "${backupName}" and OVERWRITE current data. This cannot be undone. Continue?`)) {
        return;
    }
    
    fetch(`/admin/settings/backup/${backupName}/restore`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('✅ ' + (data.message || 'Backup restored successfully'), 'success');
            if (type === 'database') {
                showAlert('🔄 Please refresh the page to see restored data', 'info');
            }
        } else {
            showAlert('❌ ' + (data.message || 'Failed to restore backup'), 'error');
        }
    })
    .catch(error => {
        console.error('Restore backup error:', error);
        showAlert('❌ Network error: ' + error.message, 'error');
    });
}

function testAutoBackup() {
    if (!confirm('Test automatic backup? This will create a test backup using the auto-backup command.')) {
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="loading"></span> Testing...';
    btn.disabled = true;

    fetch('/admin/settings/backup/test-auto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('✅ ' + (data.message || 'Auto backup test completed successfully'), 'success');
            loadBackupsList();
        } else {
            showAlert('❌ ' + (data.message || 'Auto backup test failed'), 'error');
        }
    })
    .catch(error => {
        console.error('Test auto backup error:', error);
        showAlert('❌ Network error: ' + error.message, 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function checkCronStatus() {
    fetch('/admin/settings/backup/cron-status', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const status = data.cron_working ? '✅ Working' : '❌ Not Working';
            const lastRun = data.last_run ? `Last run: ${data.last_run}` : 'Never run';
            showAlert(`🕐 Cron Status: ${status}\n${lastRun}`, data.cron_working ? 'success' : 'warning');
        } else {
            showAlert('❌ ' + (data.message || 'Failed to check cron status'), 'error');
        }
    })
    .catch(error => {
        console.error('Check cron status error:', error);
        showAlert('❌ Network error: ' + error.message, 'error');
    });
}

function deleteBackup(backupName) {
    if (!confirm(`Are you sure you want to delete backup "${backupName}"? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`/admin/settings/backup/${backupName}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('✅ ' + (data.message || 'Backup deleted successfully'), 'success');
            loadBackupsList(); // Refresh the list
        } else {
            showAlert('❌ ' + (data.message || 'Failed to delete backup'), 'error');
        }
    })
    .catch(error => {
        console.error('Delete backup error:', error);
        showAlert('❌ Network error: ' + error.message, 'error');
    });
}

function showAlert(message, type) {
    const alertContainer = document.getElementById('settings-alert');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    
    alertContainer.innerHTML = `
        <div class="alert ${alertClass}">
            ${message}
        </div>
    `;

    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}

function displayValidationErrors(form, errors) {
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.form-control.error').forEach(el => el.classList.remove('error'));

    Object.keys(errors).forEach(field => {
        const fieldName = field.replace('settings.', '');
        const element = form.querySelector(`[name="${fieldName}"]`);
        if (element) {
            element.classList.add('error');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = errors[field][0];
            element.parentNode.appendChild(errorDiv);
        }
    });
}

// Initialize the dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.adminDashboard = new AdminDashboard();
    console.log('Admin Dashboard initialized successfully');
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const settingsContainer = document.querySelector('.settings-container');
                if (settingsContainer) {
                    initializeSettings();
                    observer.disconnect();
                }
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Initialize Alpine.js notification component
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationComponent', notificationComponent);
});

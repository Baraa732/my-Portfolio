// Global State Management using Alpine.js
document.addEventListener('alpine:init', () => {
    // Dashboard State Store
    Alpine.store('dashboard', {
        loading: false,
        stats: {
            projects: 0,
            skills: 0,
            messages: 0,
            views: 0
        },
        activities: [],
        
        async fetchStats() {
            this.loading = true;
            try {
                const response = await fetch('/api/dashboard/stats');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Failed to fetch stats:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async fetchActivities() {
            try {
                const response = await fetch('/api/dashboard/activities');
                const data = await response.json();
                this.activities = data;
            } catch (error) {
                console.error('Failed to fetch activities:', error);
            }
        }
    });

    // Projects State Store
    Alpine.store('projects', {
        items: [],
        loading: false,
        currentProject: null,
        
        async fetchAll() {
            this.loading = true;
            try {
                const response = await fetch('/api/projects');
                this.items = await response.json();
            } catch (error) {
                console.error('Failed to fetch projects:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async create(data) {
            try {
                const response = await fetch('/api/projects', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(data)
                });
                const project = await response.json();
                this.items.push(project);
                Alpine.store('dashboard').fetchStats();
                return project;
            } catch (error) {
                console.error('Failed to create project:', error);
                throw error;
            }
        },
        
        async update(id, data) {
            try {
                const response = await fetch(`/api/projects/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(data)
                });
                const updated = await response.json();
                const index = this.items.findIndex(p => p.id === id);
                if (index !== -1) this.items[index] = updated;
                return updated;
            } catch (error) {
                console.error('Failed to update project:', error);
                throw error;
            }
        },
        
        async delete(id) {
            try {
                await fetch(`/api/projects/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.csrfToken }
                });
                this.items = this.items.filter(p => p.id !== id);
                Alpine.store('dashboard').fetchStats();
            } catch (error) {
                console.error('Failed to delete project:', error);
                throw error;
            }
        }
    });

    // Skills State Store
    Alpine.store('skills', {
        items: [],
        loading: false,
        
        async fetchAll() {
            this.loading = true;
            try {
                const response = await fetch('/api/skills');
                this.items = await response.json();
            } catch (error) {
                console.error('Failed to fetch skills:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async create(data) {
            try {
                const response = await fetch('/api/skills', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(data)
                });
                const skill = await response.json();
                this.items.push(skill);
                Alpine.store('dashboard').fetchStats();
                return skill;
            } catch (error) {
                console.error('Failed to create skill:', error);
                throw error;
            }
        },
        
        async update(id, data) {
            try {
                const response = await fetch(`/api/skills/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(data)
                });
                const updated = await response.json();
                const index = this.items.findIndex(s => s.id === id);
                if (index !== -1) this.items[index] = updated;
                return updated;
            } catch (error) {
                console.error('Failed to update skill:', error);
                throw error;
            }
        },
        
        async delete(id) {
            try {
                await fetch(`/api/skills/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.csrfToken }
                });
                this.items = this.items.filter(s => s.id !== id);
                Alpine.store('dashboard').fetchStats();
            } catch (error) {
                console.error('Failed to delete skill:', error);
                throw error;
            }
        }
    });

    // Messages State Store
    Alpine.store('messages', {
        items: [],
        loading: false,
        unreadCount: 0,
        
        async fetchAll() {
            this.loading = true;
            try {
                const response = await fetch('/api/messages');
                this.items = await response.json();
                this.unreadCount = this.items.filter(m => !m.read).length;
            } catch (error) {
                console.error('Failed to fetch messages:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async markAsRead(id) {
            try {
                await fetch(`/api/messages/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': window.csrfToken }
                });
                const message = this.items.find(m => m.id === id);
                if (message) {
                    message.read = true;
                    this.unreadCount = this.items.filter(m => !m.read).length;
                }
            } catch (error) {
                console.error('Failed to mark message as read:', error);
            }
        },
        
        async delete(id) {
            try {
                await fetch(`/api/messages/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': window.csrfToken }
                });
                this.items = this.items.filter(m => m.id !== id);
                this.unreadCount = this.items.filter(m => !m.read).length;
            } catch (error) {
                console.error('Failed to delete message:', error);
            }
        }
    });

    // UI State Store
    Alpine.store('ui', {
        activeSection: 'dashboard',
        modalOpen: false,
        modalContent: null,
        sidebarOpen: true,
        notifications: [],
        
        setActiveSection(section) {
            this.activeSection = section;
            document.getElementById('pageTitle').textContent = section.charAt(0).toUpperCase() + section.slice(1);
        },
        
        openModal(content) {
            this.modalContent = content;
            this.modalOpen = true;
        },
        
        closeModal() {
            this.modalOpen = false;
            this.modalContent = null;
        },
        
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        
        notify(message, type = 'success') {
            const notification = { id: Date.now(), message, type };
            this.notifications.push(notification);
            setTimeout(() => {
                this.notifications = this.notifications.filter(n => n.id !== notification.id);
            }, 3000);
        }
    });
});

// Initialize CSRF token
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

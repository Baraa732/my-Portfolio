// Portfolio Frontend State Management
document.addEventListener('alpine:init', () => {
    // Portfolio State Store
    Alpine.store('portfolio', {
        loading: true,
        sections: [],
        projects: [],
        skills: [],
        activeFilter: 'all',
        
        async init() {
            this.loading = true;
            await Promise.all([
                this.fetchSections(),
                this.fetchProjects(),
                this.fetchSkills()
            ]);
            this.loading = false;
        },
        
        async fetchSections() {
            try {
                const response = await fetch('/api/sections');
                this.sections = await response.json();
            } catch (error) {
                console.error('Failed to fetch sections:', error);
            }
        },
        
        async fetchProjects() {
            try {
                const response = await fetch('/api/projects/public');
                this.projects = await response.json();
            } catch (error) {
                console.error('Failed to fetch projects:', error);
            }
        },
        
        async fetchSkills() {
            try {
                const response = await fetch('/api/skills/public');
                this.skills = await response.json();
            } catch (error) {
                console.error('Failed to fetch skills:', error);
            }
        },
        
        filterProjects(category) {
            this.activeFilter = category;
        },
        
        get filteredProjects() {
            if (this.activeFilter === 'all') return this.projects;
            return this.projects.filter(p => p.category === this.activeFilter);
        },
        
        async submitContact(formData) {
            try {
                const response = await fetch('/api/contact', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(formData)
                });
                return await response.json();
            } catch (error) {
                console.error('Failed to submit contact form:', error);
                throw error;
            }
        }
    });

    // Theme State Store
    Alpine.store('theme', {
        dark: localStorage.getItem('theme') === 'dark',
        
        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.dark);
        },
        
        init() {
            document.documentElement.classList.toggle('dark', this.dark);
        }
    });

    // Navigation State Store
    Alpine.store('nav', {
        activeSection: 'home',
        mobileMenuOpen: false,
        
        setActive(section) {
            this.activeSection = section;
            this.mobileMenuOpen = false;
        },
        
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        }
    });
});

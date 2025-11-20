// Modern 3D Blog Animation System
class Modern3DBlog {
    constructor() {
        this.container = document.getElementById('modern3d');
        this.spheres = [];
        this.blobs = [];
        this.init();
    }

    init() {
        this.createModernElements();
        this.initGSAPAnimations();
        this.bindEvents();
    }

    createModernElements() {
        // Create floating spheres with modern design
        for (let i = 0; i < 15; i++) {
            const sphere = document.createElement('div');
            sphere.className = 'floating-sphere';
            
            const size = Math.random() * 100 + 50;
            sphere.style.width = size + 'px';
            sphere.style.height = size + 'px';
            sphere.style.left = Math.random() * 100 + '%';
            sphere.style.top = Math.random() * 100 + '%';
            sphere.style.opacity = Math.random() * 0.7 + 0.3;
            
            // Add glassmorphism effect
            sphere.style.backdropFilter = 'blur(10px)';
            sphere.style.border = '1px solid rgba(196, 74, 76, 0.3)';
            sphere.style.boxShadow = '0 8px 32px rgba(115, 12, 14, 0.3)';
            
            this.container.appendChild(sphere);
            this.spheres.push(sphere);
        }

        // Create morphing blobs
        for (let i = 0; i < 8; i++) {
            const blob = document.createElement('div');
            blob.className = 'morphing-blob';
            
            const size = Math.random() * 150 + 80;
            blob.style.width = size + 'px';
            blob.style.height = size + 'px';
            blob.style.left = Math.random() * 100 + '%';
            blob.style.top = Math.random() * 100 + '%';
            blob.style.opacity = Math.random() * 0.4 + 0.2;
            blob.style.animationDelay = Math.random() * 5 + 's';
            
            this.container.appendChild(blob);
            this.blobs.push(blob);
        }
    }

    initGSAPAnimations() {
        // Advanced GSAP timeline for spheres
        this.spheres.forEach((sphere, index) => {
            gsap.set(sphere, {
                scale: 0,
                rotation: Math.random() * 360
            });

            gsap.to(sphere, {
                scale: 1,
                duration: 2,
                delay: index * 0.1,
                ease: "elastic.out(1, 0.3)"
            });

            // Continuous floating animation
            gsap.to(sphere, {
                y: "random(-50, 50)",
                x: "random(-30, 30)",
                rotation: "+=360",
                duration: "random(8, 15)",
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut",
                delay: Math.random() * 2
            });

            // Pulsing effect
            gsap.to(sphere, {
                scale: "random(0.8, 1.2)",
                duration: "random(3, 6)",
                repeat: -1,
                yoyo: true,
                ease: "power2.inOut",
                delay: Math.random() * 3
            });
        });

        // Advanced blob animations
        this.blobs.forEach((blob, index) => {
            gsap.set(blob, {
                scale: 0,
                rotation: Math.random() * 360
            });

            gsap.to(blob, {
                scale: 1,
                duration: 3,
                delay: index * 0.2,
                ease: "back.out(1.7)"
            });

            // Complex morphing animation
            gsap.to(blob, {
                rotation: "+=720",
                scale: "random(0.5, 1.5)",
                x: "random(-100, 100)",
                y: "random(-80, 80)",
                duration: "random(10, 20)",
                repeat: -1,
                yoyo: true,
                ease: "power1.inOut",
                delay: Math.random() * 5
            });
        });

        // Parallax effect on mouse move
        this.initParallax();
    }

    initParallax() {
        let mouseX = 0;
        let mouseY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            mouseY = (e.clientY / window.innerHeight) * 2 - 1;

            gsap.to(this.spheres, {
                x: mouseX * 30,
                y: mouseY * 20,
                duration: 2,
                ease: "power2.out",
                stagger: 0.02
            });

            gsap.to(this.blobs, {
                x: mouseX * -20,
                y: mouseY * -15,
                rotation: mouseX * 10,
                duration: 3,
                ease: "power2.out",
                stagger: 0.03
            });
        });
    }

    bindEvents() {
        // Scroll-triggered animations
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            
            gsap.to(this.spheres, {
                y: scrollY * 0.1,
                rotation: scrollY * 0.2,
                duration: 1,
                ease: "power2.out",
                stagger: 0.01
            });
        });

        // Resize handler
        window.addEventListener('resize', () => {
            this.repositionElements();
        });

        // Click interaction
        this.container.addEventListener('click', (e) => {
            this.createClickEffect(e.clientX, e.clientY);
        });
    }

    repositionElements() {
        [...this.spheres, ...this.blobs].forEach(element => {
            gsap.to(element, {
                x: Math.random() * window.innerWidth,
                y: Math.random() * window.innerHeight,
                duration: 2,
                ease: "power2.inOut"
            });
        });
    }

    createClickEffect(x, y) {
        const ripple = document.createElement('div');
        ripple.style.position = 'absolute';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.width = '10px';
        ripple.style.height = '10px';
        ripple.style.background = 'radial-gradient(circle, #c44a4c, transparent)';
        ripple.style.borderRadius = '50%';
        ripple.style.pointerEvents = 'none';
        ripple.style.zIndex = '1000';
        
        this.container.appendChild(ripple);

        gsap.to(ripple, {
            scale: 20,
            opacity: 0,
            duration: 1,
            ease: "power2.out",
            onComplete: () => ripple.remove()
        });
    }
}

// Advanced particle system using modern techniques
class ModernParticleSystem {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.init();
    }

    init() {
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.zIndex = '1';
        this.canvas.style.pointerEvents = 'none';
        
        document.body.appendChild(this.canvas);
        this.resize();
        this.createParticles();
        this.animate();
        
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }

    createParticles() {
        for (let i = 0; i < 50; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2,
                size: Math.random() * 3 + 1,
                color: `hsla(${Math.random() * 20 + 5}, 80%, 50%, ${Math.random() * 0.5 + 0.2})`,
                life: Math.random() * 100 + 50
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.particles.forEach((particle, index) => {
            particle.x += particle.vx;
            particle.y += particle.vy;
            particle.life--;
            
            if (particle.life <= 0 || particle.x < 0 || particle.x > this.canvas.width || 
                particle.y < 0 || particle.y > this.canvas.height) {
                this.particles[index] = {
                    x: Math.random() * this.canvas.width,
                    y: Math.random() * this.canvas.height,
                    vx: (Math.random() - 0.5) * 2,
                    vy: (Math.random() - 0.5) * 2,
                    size: Math.random() * 3 + 1,
                    color: `hsla(${Math.random() * 20 + 5}, 80%, 50%, ${Math.random() * 0.5 + 0.2})`,
                    life: Math.random() * 100 + 50
                };
            }
            
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fillStyle = particle.color;
            this.ctx.fill();
        });
        
        requestAnimationFrame(() => this.animate());
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Modern3DBlog();
    new ModernParticleSystem();
});
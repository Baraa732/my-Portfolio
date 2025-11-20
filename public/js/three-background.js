class ThreeBackground {
    constructor() {
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.particles = null;
        this.geometricShapes = [];
        this.mouse = { x: 0, y: 0 };
        this.init();
    }

    init() {
        this.createScene();
        this.createCamera();
        this.createRenderer();
        this.createParticleSystem();
        this.createGeometricShapes();
        this.createLights();
        this.bindEvents();
        this.animate();
    }

    createScene() {
        this.scene = new THREE.Scene();
        this.scene.fog = new THREE.Fog(0x0f1419, 1, 1000);
    }

    createCamera() {
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.camera.position.z = 50;
    }

    createRenderer() {
        this.renderer = new THREE.WebGLRenderer({ 
            canvas: document.getElementById('three-canvas'),
            alpha: true,
            antialias: true 
        });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.setClearColor(0x0f1419, 0.8);
    }

    createParticleSystem() {
        const particleCount = window.innerWidth < 768 ? 800 : 1500;
        const positions = new Float32Array(particleCount * 3);
        const colors = new Float32Array(particleCount * 3);
        const sizes = new Float32Array(particleCount);

        const colorPalette = [
            new THREE.Color(0x4c6fff), // Primary blue
            new THREE.Color(0x1a365d), // Dark blue
            new THREE.Color(0x60a5fa), // Light blue
            new THREE.Color(0x3b82f6), // Medium blue
            new THREE.Color(0x1e40af)  // Deep blue
        ];

        for (let i = 0; i < particleCount; i++) {
            const i3 = i * 3;
            
            // Position
            positions[i3] = (Math.random() - 0.5) * 200;
            positions[i3 + 1] = (Math.random() - 0.5) * 200;
            positions[i3 + 2] = (Math.random() - 0.5) * 200;

            // Color
            const color = colorPalette[Math.floor(Math.random() * colorPalette.length)];
            colors[i3] = color.r;
            colors[i3 + 1] = color.g;
            colors[i3 + 2] = color.b;

            // Size
            sizes[i] = Math.random() * 3 + 1;
        }

        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
        geometry.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

        const material = new THREE.ShaderMaterial({
            uniforms: {
                time: { value: 0 },
                pixelRatio: { value: Math.min(window.devicePixelRatio, 2) }
            },
            vertexShader: `
                attribute float size;
                attribute vec3 color;
                varying vec3 vColor;
                uniform float time;
                
                void main() {
                    vColor = color;
                    vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);
                    
                    // Floating animation
                    mvPosition.y += sin(time * 0.001 + position.x * 0.01) * 2.0;
                    mvPosition.x += cos(time * 0.0015 + position.z * 0.01) * 1.5;
                    
                    gl_PointSize = size * (300.0 / -mvPosition.z);
                    gl_Position = projectionMatrix * mvPosition;
                }
            `,
            fragmentShader: `
                varying vec3 vColor;
                
                void main() {
                    float distanceToCenter = distance(gl_PointCoord, vec2(0.5));
                    float alpha = 1.0 - smoothstep(0.0, 0.5, distanceToCenter);
                    
                    gl_FragColor = vec4(vColor, alpha * 0.8);
                }
            `,
            transparent: true,
            blending: THREE.AdditiveBlending
        });

        this.particles = new THREE.Points(geometry, material);
        this.scene.add(this.particles);
    }

    createGeometricShapes() {
        // Floating wireframe cubes
        for (let i = 0; i < 8; i++) {
            const geometry = new THREE.BoxGeometry(5, 5, 5);
            const material = new THREE.MeshBasicMaterial({
                color: 0x4c6fff,
                wireframe: true,
                transparent: true,
                opacity: 0.3
            });
            
            const cube = new THREE.Mesh(geometry, material);
            cube.position.set(
                (Math.random() - 0.5) * 100,
                (Math.random() - 0.5) * 100,
                (Math.random() - 0.5) * 100
            );
            
            cube.rotation.set(
                Math.random() * Math.PI,
                Math.random() * Math.PI,
                Math.random() * Math.PI
            );
            
            this.geometricShapes.push(cube);
            this.scene.add(cube);
        }

        // Floating wireframe spheres
        for (let i = 0; i < 5; i++) {
            const geometry = new THREE.SphereGeometry(3, 16, 16);
            const material = new THREE.MeshBasicMaterial({
                color: 0x1a365d,
                wireframe: true,
                transparent: true,
                opacity: 0.4
            });
            
            const sphere = new THREE.Mesh(geometry, material);
            sphere.position.set(
                (Math.random() - 0.5) * 120,
                (Math.random() - 0.5) * 120,
                (Math.random() - 0.5) * 120
            );
            
            this.geometricShapes.push(sphere);
            this.scene.add(sphere);
        }

        // Floating torus
        for (let i = 0; i < 3; i++) {
            const geometry = new THREE.TorusGeometry(4, 1, 8, 16);
            const material = new THREE.MeshBasicMaterial({
                color: 0x60a5fa,
                wireframe: true,
                transparent: true,
                opacity: 0.5
            });
            
            const torus = new THREE.Mesh(geometry, material);
            torus.position.set(
                (Math.random() - 0.5) * 80,
                (Math.random() - 0.5) * 80,
                (Math.random() - 0.5) * 80
            );
            
            this.geometricShapes.push(torus);
            this.scene.add(torus);
        }
    }

    createLights() {
        // Ambient light
        const ambientLight = new THREE.AmbientLight(0x4c6fff, 0.3);
        this.scene.add(ambientLight);

        // Point lights
        const pointLight1 = new THREE.PointLight(0x4c6fff, 1, 100);
        pointLight1.position.set(50, 50, 50);
        this.scene.add(pointLight1);

        const pointLight2 = new THREE.PointLight(0x1a365d, 0.8, 100);
        pointLight2.position.set(-50, -50, -50);
        this.scene.add(pointLight2);
    }

    bindEvents() {
        window.addEventListener('resize', () => this.onWindowResize());
        document.addEventListener('mousemove', (event) => this.onMouseMove(event));
        
        // Scroll parallax effect
        window.addEventListener('scroll', () => this.onScroll());
    }

    onWindowResize() {
        this.camera.aspect = window.innerWidth / window.innerHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(window.innerWidth, window.innerHeight);
    }

    onMouseMove(event) {
        this.mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        this.mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
    }

    onScroll() {
        const scrollY = window.scrollY;
        this.camera.position.y = scrollY * 0.01;
        this.camera.rotation.x = scrollY * 0.0001;
    }

    animate() {
        requestAnimationFrame(() => this.animate());

        const time = Date.now();

        // Update particle system
        if (this.particles && this.particles.material.uniforms) {
            this.particles.material.uniforms.time.value = time;
            this.particles.rotation.y += 0.0005;
        }

        // Animate geometric shapes
        this.geometricShapes.forEach((shape, index) => {
            shape.rotation.x += 0.005 + index * 0.001;
            shape.rotation.y += 0.003 + index * 0.0005;
            shape.rotation.z += 0.002 + index * 0.0003;
            
            // Floating motion
            shape.position.y += Math.sin(time * 0.001 + index) * 0.02;
            shape.position.x += Math.cos(time * 0.0008 + index) * 0.015;
        });

        // Mouse interaction
        this.camera.position.x += (this.mouse.x * 5 - this.camera.position.x) * 0.02;
        this.camera.position.y += (-this.mouse.y * 5 - this.camera.position.y) * 0.02;
        this.camera.lookAt(this.scene.position);

        this.renderer.render(this.scene, this.camera);
    }

    destroy() {
        if (this.renderer) {
            this.renderer.dispose();
        }
        window.removeEventListener('resize', this.onWindowResize);
        document.removeEventListener('mousemove', this.onMouseMove);
        window.removeEventListener('scroll', this.onScroll);
    }
}

// Initialize immediately
if (document.getElementById('three-canvas')) {
    window.threeBackground = new ThreeBackground();
} else {
    // Initialize when DOM is ready if canvas not found
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('three-canvas')) {
            window.threeBackground = new ThreeBackground();
        }
    });
}
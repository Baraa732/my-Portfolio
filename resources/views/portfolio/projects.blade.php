@extends('layouts.app')

@section('content')
      <section class="projects-section">
         <div class="container">
            <div class="section-title">
               <h2>Featured Projects</h2>
               <p class="section-subtitle">A collection of my recent work showcasing innovative solutions and technical
                  expertise</p>
            </div>

            <div class="search-filter-bar">
               <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" id="projectSearch" placeholder="Search projects by name, description, or technology...">
                  <span class="search-count"><span id="resultCount">{{ count($projects) }}</span> projects</span>
               </div>
               <div class="filter-tags" id="filterTags">
                  <button class="filter-btn active" data-filter="all">All</button>
               </div>
            </div>

            <div class="projects-grid" id="projectsGrid">
               @foreach($projects as $project)
                  <div class="project-card" 
                       data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-perspective="1000"
                       data-title="{{ strtolower($project->title) }}"
                       data-description="{{ strtolower($project->description) }}"
                       data-technologies="{{ strtolower($project->technologies) }}">
                     <div class="card-glow"></div>
                     <div class="card-particles"></div>
                     <div class="project-image">
                        @if($project->image)
                           <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}">
                        @else
                           <div class="project-image-placeholder">
                              <i class="fas fa-project-diagram"></i>
                           </div>
                        @endif
                     </div>
                     <div class="project-info">
                        <h3 class="project-title">{{ $project->title }}</h3>
                        <div class="project-tech">
                           @foreach(explode(',', $project->technologies) as $tech)
                              <span class="tech-badge">{{ trim($tech) }}</span>
                           @endforeach
                        </div>
                     </div>
                     <div class="project-overlay">
                        <div class="overlay-content">
                           <h3>{{ $project->title }}</h3>
                           <p>{{ $project->description }}</p>
                           <div class="project-links">
                              @if($project->project_url)
                                 <a href="{{ $project->project_url }}" target="_blank"><i class="fas fa-external-link-alt"></i> Live</a>
                              @endif
                              @if($project->github_url)
                                 <a href="{{ $project->github_url }}" target="_blank"><i class="fab fa-github"></i> Code</a>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
               @endforeach
            </div>

            <!-- CTA Section -->
            <div class="cta-section animate-on-scroll" data-animation="fadeInUp">
               <h3 class="cta-title">Ready to Start Your Project?</h3>
               <p class="cta-description">Let's work together to bring your ideas to life with cutting-edge solutions</p>
               <a href="{{ route('contact') }}" class="btn btn-light">
                  <i class="fas fa-paper-plane me-2"></i>
                  Get In Touch
               </a>
            </div>
         </div>
      </section>

      <style>
         .projects-section {
            padding: 120px 0;
            position: relative;
         }

         .search-filter-bar {
            margin-bottom: 3rem;
            animation: slideDown 0.6s ease;
         }

         @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
         }

         @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
         }

         @keyframes fadeOut {
            to { opacity: 0; transform: scale(0.8); }
         }

         .search-box {
            position: relative;
            margin-bottom: 1.5rem;
         }

         .search-box i {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(76, 111, 255, 0.6);
            font-size: 1.2rem;
         }

         .search-box input {
            width: 100%;
            padding: 1.2rem 1.5rem 1.2rem 4rem;
            background: rgba(15, 20, 25, 0.6);
            border: 2px solid rgba(76, 111, 255, 0.3);
            border-radius: 50px;
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
         }

         .search-box input:focus {
            border-color: #4c6fff;
            box-shadow: 0 0 20px rgba(76, 111, 255, 0.3);
         }

         .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.4);
         }

         .search-count {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            font-weight: 600;
         }

         .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            justify-content: center;
         }

         .filter-btn {
            padding: 0.6rem 1.5rem;
            background: rgba(15, 20, 25, 0.6);
            border: 2px solid rgba(76, 111, 255, 0.3);
            border-radius: 50px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
         }

         .filter-btn:hover {
            border-color: #4c6fff;
            color: #fff;
            transform: translateY(-2px);
         }

         .filter-btn.active {
            background: #4c6fff;
            border-color: #4c6fff;
            color: #fff;
         }

         .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2.5rem;
         }

         .projects-grid .project-card:only-child {
            max-width: 450px;
         }

         .project-card {
            position: relative;
            height: 550px;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(15, 20, 25, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transform-style: preserve-3d;
            transition: all 0.3s ease;
         }

         .card-glow {
            position: absolute;
            inset: -2px;
            background: conic-gradient(from 0deg, #4c6fff, #1a365d, #4c6fff);
            border-radius: 20px;
            opacity: 0;
            filter: blur(20px);
            transition: opacity 0.5s;
            z-index: -1;
         }

         .project-card:hover .card-glow {
            opacity: 0.8;
            animation: rotate 3s linear infinite;
         }

         @keyframes rotate {
            100% { transform: rotate(360deg); }
         }

         .card-particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.5s;
         }

         .project-card:hover .card-particles {
            opacity: 1;
         }

         .project-image {
            height: 280px;
            overflow: hidden;
            position: relative;
         }

         .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
         }

         .project-card:hover .project-image img {
            transform: scale(1.1);
         }

         .project-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #4c6fff, #1a365d);
            display: flex;
            align-items: center;
            justify-content: center;
         }

         .project-image-placeholder i {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.3);
         }

         .project-info {
            padding: 1.5rem;
            height: 270px;
            display: flex;
            flex-direction: column;
         }

         .project-title {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
         }

         .project-tech {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            max-height: 150px;
            overflow-y: auto;
            padding-right: 0.5rem;
         }

         .project-tech::-webkit-scrollbar {
            width: 4px;
         }

         .project-tech::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
         }

         .project-tech::-webkit-scrollbar-thumb {
            background: rgba(76, 111, 255, 0.5);
            border-radius: 10px;
         }

         .tech-badge {
            background: rgba(76, 111, 255, 0.2);
            color: #4c6fff;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(76, 111, 255, 0.3);
         }

         .project-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 20, 25, 0.98);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s;
            padding: 2rem;
         }

         .project-card:hover .project-overlay {
            opacity: 1;
         }

         .overlay-content {
            text-align: center;
            transform: translateY(20px);
            transition: transform 0.4s 0.1s;
         }

         .project-card:hover .overlay-content {
            transform: translateY(0);
         }

         .overlay-content h3 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1rem;
         }

         .overlay-content p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            max-height: 200px;
            overflow-y: auto;
         }

         .project-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
         }

         .project-links a {
            background: rgba(76, 111, 255, 0.2);
            color: #4c6fff;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid rgba(76, 111, 255, 0.3);
            transition: all 0.3s;
         }

         .project-links a:hover {
            background: #4c6fff;
            color: #fff;
            transform: translateY(-2px);
         }

         .cta-section {
            text-align: center;
            margin-top: 5rem;
            padding: 4rem;
            background: var(--gradient);
            border-radius: var(--border-radius);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
         }

         .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000" /></svg>');
            pointer-events: none;
         }

         .cta-title {
            color: var(--light);
            font-size: clamp(2rem, 4vw, 2.5rem);
            margin-bottom: 1.5rem;
            font-weight: 800;
            position: relative;
            line-height: 1.1;
         }

         .cta-description {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-size: clamp(1rem, 2vw, 1.2rem);
            position: relative;
            font-weight: 500;
            line-height: 1.6;
         }

         .btn-light {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--light);
            position: relative;
            backdrop-filter: blur(10px);
            transition: var(--transition);
         }

         .btn-light:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
         }

         /* Responsive Design */
         @media (max-width: 1200px) {
            .projects-grid {
               grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
               gap: 2rem;
            }
         }

         @media (max-width: 768px) {
            .projects-grid {
               grid-template-columns: 1fr;
            }
            .project-card {
               height: 500px;
            }
            .project-image {
               height: 250px;
            }
            .project-info {
               height: 250px;
            }
            .search-box input {
               padding: 1rem 1rem 1rem 3.5rem;
               font-size: 0.9rem;
            }
            .search-count {
               position: static;
               display: block;
               text-align: center;
               margin-top: 0.5rem;
            }
            .filter-tags {
               gap: 0.5rem;
            }
            .filter-btn {
               padding: 0.5rem 1rem;
               font-size: 0.85rem;
            }
         }

         @media (max-width: 480px) {
            .project-card {
               height: 450px;
            }
            .project-image {
               height: 200px;
            }
            .project-info {
               height: 250px;
            }
            .project-title {
               font-size: 1.1rem;
            }
            .search-box input {
               padding: 0.9rem 1rem 0.9rem 3rem;
            }
            .search-box i {
               left: 1rem;
               font-size: 1rem;
            }
         }


      </style>

      <script src="https://cdn.jsdelivr.net/npm/vanilla-tilt@1.8.1/dist/vanilla-tilt.min.js"></script>
      <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
      <script>
         document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.project-card');
            const searchInput = document.getElementById('projectSearch');
            const resultCount = document.getElementById('resultCount');
            const filterTags = document.getElementById('filterTags');

            // Fetch categories from API
            fetch('/api/project-categories')
               .then(response => response.json())
               .then(categories => {
                  categories.forEach(category => {
                     const btn = document.createElement('button');
                     btn.className = 'filter-btn';
                     btn.textContent = category.name;
                     btn.dataset.filter = category.slug;
                     filterTags.appendChild(btn);
                  });
               })
               .catch(error => console.error('Error loading categories:', error));

            let activeFilter = 'all';

            // Real-time search
            searchInput.addEventListener('input', function() {
               filterProjects();
            });

            // Filter by technology
            filterTags.addEventListener('click', function(e) {
               if (e.target.classList.contains('filter-btn')) {
                  document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                  e.target.classList.add('active');
                  activeFilter = e.target.dataset.filter;
                  filterProjects();
               }
            });

            function filterProjects() {
               const searchTerm = searchInput.value.toLowerCase();
               let visibleCount = 0;

               cards.forEach((card, index) => {
                  // Get fresh data from DOM instead of cached attributes
                  const title = card.querySelector('.project-title')?.textContent.toLowerCase() || '';
                  const description = card.querySelector('.overlay-content p')?.textContent.toLowerCase() || '';
                  const techBadges = Array.from(card.querySelectorAll('.tech-badge')).map(b => b.textContent.toLowerCase()).join(' ');

                  const matchesSearch = !searchTerm || 
                     title.includes(searchTerm) || 
                     description.includes(searchTerm) || 
                     techBadges.includes(searchTerm);

                  const matchesFilter = activeFilter === 'all' || 
                     techBadges.includes(activeFilter) ||
                     title.includes(activeFilter) ||
                     description.includes(activeFilter);

                  if (matchesSearch && matchesFilter) {
                     card.style.display = 'block';
                     card.style.animation = `fadeInScale 0.5s ease ${index * 0.05}s both`;
                     visibleCount++;
                  } else {
                     card.style.animation = 'fadeOut 0.3s ease';
                     setTimeout(() => card.style.display = 'none', 300);
                  }
               });

               resultCount.textContent = visibleCount;
            }

            // Initialize tilt and particles
            VanillaTilt.init(cards, {
               max: 15,
               speed: 400,
               glare: true,
               "max-glare": 0.3,
               scale: 1.05
            });

            cards.forEach((card, index) => {
               const particlesContainer = card.querySelector('.card-particles');
               particlesContainer.id = 'particles-' + index;
               
               card.addEventListener('mouseenter', function() {
                  particlesJS('particles-' + index, {
                     particles: {
                        number: { value: 50, density: { enable: true, value_area: 800 } },
                        color: { value: '#4c6fff' },
                        shape: { type: 'circle' },
                        opacity: { value: 0.5, random: true },
                        size: { value: 3, random: true },
                        line_linked: { enable: true, distance: 150, color: '#4c6fff', opacity: 0.4, width: 1 },
                        move: { enable: true, speed: 2, direction: 'none', random: true, out_mode: 'out' }
                     },
                     interactivity: {
                        detect_on: 'canvas',
                        events: { onhover: { enable: true, mode: 'repulse' } },
                        modes: { repulse: { distance: 100, duration: 0.4 } }
                     }
                  });
               });
            });
         });
      </script>
@endsection

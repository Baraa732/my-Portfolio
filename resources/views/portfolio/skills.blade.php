@extends('layouts.app')

@section('content')
   <section class="skills-section">
      <div class="container">
         <div class="section-title">
            <h2>Technical Skills</h2>
            <p class="section-subtitle">A showcase of my technical expertise across different technology ecosystems</p>
         </div>

         <!-- JavaScript Ecosystem -->
         @php
            $javascriptSection = \App\Models\EcosystemSection::where('ecosystem', 'javascript')
               ->where('is_visible', true)
               ->first();

            $javascriptSkills = $javascriptSection ? \App\Models\SkillEcosystem::where('ecosystem', 'javascript')
               ->where('is_active', true)
               ->orderBy('order')
               ->get() : collect();
         @endphp

         @if($javascriptSection && $javascriptSkills->count() > 0)
            <div class="ecosystem-section animate-on-scroll" data-animation="fadeInUp">
               <div class="ecosystem-header">
                  <div class="ecosystem-icon">
                     <i class="fab fa-js-square"></i>
                  </div>
                  <div class="ecosystem-content">
                     <h2 class="ecosystem-title">{{ $javascriptSection->title }}</h2>
                     @if($javascriptSection->description)
                        <p class="ecosystem-description">{{ $javascriptSection->description }}</p>
                     @endif
                  </div>
               </div>

               <div class="ecosystem-skills">
                  @foreach($javascriptSkills as $skill)
                     <div class="ecosystem-skill-item animate-on-scroll" data-animation="fadeInUp">
                        <div class="ecosystem-skill-icon">
                           <i class="{{ $skill->icon ?: 'fab fa-js' }}"></i>
                        </div>
                        <div class="ecosystem-skill-info">
                           <h4 class="ecosystem-skill-name">{{ $skill->name }}</h4>
                           <div class="ecosystem-skill-progress">
                              <div class="ecosystem-progress-bar" data-width="{{ $skill->proficiency }}"></div>
                              <span class="ecosystem-percentage">{{ $skill->proficiency }}%</span>
                           </div>
                        </div>
                     </div>
                  @endforeach
               </div>
            </div>
         @endif

         <!-- PHP Ecosystem -->
         @php
            $phpSection = \App\Models\EcosystemSection::where('ecosystem', 'php')
               ->where('is_visible', true)
               ->first();

            $phpSkills = $phpSection ? \App\Models\SkillEcosystem::where('ecosystem', 'php')
               ->where('is_active', true)
               ->orderBy('order')
               ->get() : collect();
         @endphp

         @if($phpSection && $phpSkills->count() > 0)
            <div class="ecosystem-section animate-on-scroll" data-animation="fadeInUp">
               <div class="ecosystem-header">
                  <div class="ecosystem-icon">
                     <i class="fab fa-php"></i>
                  </div>
                  <div class="ecosystem-content">
                     <h2 class="ecosystem-title">{{ $phpSection->title }}</h2>
                     @if($phpSection->description)
                        <p class="ecosystem-description">{{ $phpSection->description }}</p>
                     @endif
                  </div>
               </div>

               <div class="ecosystem-skills">
                  @foreach($phpSkills as $skill)
                     <div class="ecosystem-skill-item animate-on-scroll" data-animation="fadeInUp">
                        <div class="ecosystem-skill-icon">
                           <i class="{{ $skill->icon ?: 'fab fa-php' }}"></i>
                        </div>
                        <div class="ecosystem-skill-info">
                           <h4 class="ecosystem-skill-name">{{ $skill->name }}</h4>
                           <div class="ecosystem-skill-progress">
                              <div class="ecosystem-progress-bar" data-width="{{ $skill->proficiency }}"></div>
                              <span class="ecosystem-percentage">{{ $skill->proficiency }}%</span>
                           </div>
                        </div>
                     </div>
                  @endforeach
               </div>
            </div>
         @endif

         <!-- No Skills Message -->
         @if((!$javascriptSection || $javascriptSkills->count() === 0) && (!$phpSection || $phpSkills->count() === 0))
            <div class="no-skills-message">
               <i class="fas fa-code"></i>
               <h3>Skills Coming Soon</h3>
               <p>My technical skills are being updated. Please check back later!</p>
            </div>
         @endif
      </div>
   </section>

   <!-- Your existing CSS and JavaScript remain the same -->
@endsection

<style>
   /* Enhanced Skills Section */
   .skills-section {
      padding: 120px 0;
      position: relative;
      overflow: hidden;
   }

   .skills-section::before {
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

   /* Skills Grid */
   .skills-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 2rem;
      position: relative;
      z-index: 2;
   }

   /* Skill Item */
   .skill-item {
      background: linear-gradient(145deg, rgba(26, 54, 93, 0.4), rgba(15, 20, 25, 0.6));
      padding: 2.5rem;
      border-radius: var(--border-radius);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) both;
   }

   .skill-background-shimmer {
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.1), transparent);
      transition: var(--transition);
   }

   .skill-item:hover .skill-background-shimmer {
      left: 100%;
   }

   .skill-item:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: var(--shadow-lg);
      border-color: rgba(76, 111, 255, 0.3);
   }

   /* Skill Header */
   .skill-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
   }

   .skill-info-main {
      display: flex;
      align-items: center;
      gap: 1rem;
   }

   .skill-icon {
      width: 60px;
      height: 60px;
      background: var(--gradient);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow);
      transition: var(--transition);
   }

   .skill-item:hover .skill-icon {
      transform: scale(1.1) rotate(5deg);
   }

   .skill-icon i {
      font-size: 1.8rem;
      color: var(--light);
   }

   .skill-text {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
   }

   .skill-name {
      color: var(--light);
      font-size: 1.3rem;
      font-weight: 700;
      margin: 0;
      transition: var(--transition);
   }

   .skill-item:hover .skill-name {
      color: var(--accent);
      transform: translateX(5px);
   }

   .skill-level {
      color: var(--gray);
      font-size: 0.9rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
   }

   .skill-percentage {
      font-size: 1.5rem;
      font-weight: 800;
      background: var(--gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      transition: var(--transition);
   }

   .skill-item:hover .skill-percentage {
      transform: scale(1.1);
   }

   /* Skill Progress */
   .skill-progress-container {
      background: rgba(255, 255, 255, 0.1);
      height: 12px;
      border-radius: 6px;
      overflow: hidden;
      position: relative;
      backdrop-filter: blur(10px);
   }

   .skill-progress {
      height: 100%;
      background: var(--gradient);
      border-radius: 6px;
      width: 0%;
      transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
   }

   .skill-progress-shimmer {
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      animation: shimmer 2s infinite;
      animation-play-state: paused;
   }

   .skill-item:hover .skill-progress-shimmer {
      animation-play-state: running;
   }

   .skill-item:hover .skill-progress {
      box-shadow: 0 0 25px rgba(76, 111, 255, 0.4);
   }

   /* Skill Meta */
   .skill-meta {
      display: flex;
      justify-content: space-between;
      margin-top: 0.8rem;
   }

   .skill-label {
      color: var(--gray);
      font-size: 0.9rem;
      font-weight: 500;
   }

   .skill-expertise {
      color: var(--accent);
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
   }

   /* Skills Categories */
   .skills-categories {
      margin-top: 5rem;
      position: relative;
      z-index: 2;
   }

   .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
   }

   .category-card {
      text-align: center;
      padding: 2.5rem 2rem;
      background: linear-gradient(145deg, rgba(26, 54, 93, 0.3), rgba(15, 20, 25, 0.5));
      border-radius: var(--border-radius);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
   }

   .category-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.1), transparent);
      transition: var(--transition);
   }

   .category-card:hover::before {
      left: 100%;
   }

   .category-card:hover {
      transform: translateY(-5px);
      border-color: rgba(76, 111, 255, 0.3);
      box-shadow: var(--shadow);
   }

   .category-icon {
      width: 80px;
      height: 80px;
      background: var(--gradient);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      box-shadow: var(--shadow);
      transition: var(--transition);
   }

   .category-card:hover .category-icon {
      transform: scale(1.1) rotate(5deg);
   }

   .category-icon i {
      font-size: 2rem;
      color: var(--light);
   }

   .category-title {
      color: var(--light);
      margin-bottom: 1rem;
      font-size: 1.4rem;
      font-weight: 700;
   }

   .category-description {
      color: var(--gray);
      line-height: 1.6;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
   }

   .category-progress {
      background: rgba(255, 255, 255, 0.1);
      height: 6px;
      border-radius: 3px;
      overflow: hidden;
      backdrop-filter: blur(10px);
   }

   .category-progress-bar {
      height: 100%;
      background: var(--gradient);
      border-radius: 3px;
      transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
   }

   /* Ecosystem Sections */
   .ecosystem-section {
      margin-top: 4rem;
      background: linear-gradient(145deg, rgba(26, 54, 93, 0.3), rgba(15, 20, 25, 0.5));
      border-radius: var(--border-radius);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      padding: 3rem;
      position: relative;
      overflow: hidden;
   }

   .ecosystem-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(76, 111, 255, 0.05), transparent);
      transition: var(--transition);
   }

   .ecosystem-section:hover::before {
      left: 100%;
   }

   .ecosystem-header {
      display: flex;
      align-items: center;
      gap: 2rem;
      margin-bottom: 3rem;
      position: relative;
      z-index: 2;
   }

   .ecosystem-icon {
      width: 80px;
      height: 80px;
      background: var(--gradient);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow);
      flex-shrink: 0;
   }

   .ecosystem-icon i {
      font-size: 2.5rem;
      color: var(--light);
   }

   .ecosystem-title {
      color: var(--light);
      font-size: 2rem;
      font-weight: 700;
      margin: 0 0 0.5rem 0;
      background: var(--gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
   }

   .ecosystem-description {
      color: var(--gray);
      font-size: 1.1rem;
      line-height: 1.6;
      margin: 0;
   }

   .ecosystem-skills {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      position: relative;
      z-index: 2;
   }

   .ecosystem-skill-item {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      padding: 1.5rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: var(--transition);
      backdrop-filter: blur(10px);
   }

   .ecosystem-skill-item:hover {
      transform: translateY(-3px);
      border-color: rgba(76, 111, 255, 0.3);
      box-shadow: 0 8px 25px rgba(76, 111, 255, 0.15);
   }

   .ecosystem-skill-icon {
      width: 50px;
      height: 50px;
      background: var(--gradient);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
      transition: var(--transition);
   }

   .ecosystem-skill-item:hover .ecosystem-skill-icon {
      transform: scale(1.1) rotate(5deg);
   }

   .ecosystem-skill-icon i {
      font-size: 1.5rem;
      color: var(--light);
   }

   .ecosystem-skill-name {
      color: var(--light);
      font-size: 1.2rem;
      font-weight: 600;
      margin: 0 0 1rem 0;
   }

   .ecosystem-skill-progress {
      position: relative;
   }

   .ecosystem-progress-bar {
      height: 8px;
      background: var(--gradient);
      border-radius: 4px;
      width: 0%;
      transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
   }

   .ecosystem-percentage {
      position: absolute;
      right: 0;
      top: -25px;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--accent);
   }

   /* Animations */
   @keyframes shimmer {
      0% {
         transform: translateX(-100%) skewX(-15deg);
      }

      100% {
         transform: translateX(200%) skewX(-15deg);
      }
   }

   @keyframes fadeInUp {
      from {
         opacity: 0;
         transform: translateY(30px);
      }

      to {
         opacity: 1;
         transform: translateY(0);
      }
   }

   /* Responsive Design */
   @media (max-width: 1200px) {
      .skills-grid {
         grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      }
   }

   @media (max-width: 768px) {
      .skills-section {
         padding: 80px 0;
      }

      .skills-grid {
         grid-template-columns: 1fr;
         gap: 1.5rem;
      }

      .skill-item {
         padding: 2rem;
      }

      .skill-header {
         margin-bottom: 1.5rem;
      }

      .skill-icon {
         width: 50px;
         height: 50px;
      }

      .skill-icon i {
         font-size: 1.5rem;
      }

      .skill-name {
         font-size: 1.2rem;
      }

      .skill-percentage {
         font-size: 1.3rem;
      }

      .categories-grid {
         grid-template-columns: 1fr;
         gap: 1.5rem;
      }

      .category-card {
         padding: 2rem 1.5rem;
      }

      .category-icon {
         width: 70px;
         height: 70px;
      }

      .category-icon i {
         font-size: 1.8rem;
      }
   }

   @media (max-width: 480px) {
      .skill-item {
         padding: 1.5rem;
      }

      .skill-info-main {
         gap: 0.75rem;
      }

      .skill-icon {
         width: 45px;
         height: 45px;
      }

      .skill-icon i {
         font-size: 1.3rem;
      }

      .skill-name {
         font-size: 1.1rem;
      }

      .skill-percentage {
         font-size: 1.2rem;
      }

      .category-card {
         padding: 1.5rem;
      }

      .category-icon {
         width: 60px;
         height: 60px;
      }

      .category-icon i {
         font-size: 1.6rem;
      }
   }

   /* Accessibility & Performance */
   @media (prefers-reduced-motion: reduce) {

      .skill-item,
      .category-card,
      .skill-icon,
      .skill-progress {
         transition: none;
         animation: none;
      }

      .skill-item:hover,
      .category-card:hover {
         transform: none;
      }

      .skill-background-shimmer,
      .skill-progress-shimmer {
         display: none;
      }
   }

   @media (hover: none) {

      .skill-item:hover,
      .category-card:hover {
         transform: none;
      }

      .skill-item::before,
      .category-card::before {
         display: none;
      }
   }
</style>

<script>
   // JavaScript for animating skill progress bars on scroll
   document.addEventListener('DOMContentLoaded', function () {
      const skillItems = document.querySelectorAll('.skill-item');

      const observer = new IntersectionObserver((entries) => {
         entries.forEach(entry => {
            if (entry.isIntersecting) {
               const skillProgress = entry.target.querySelector('.skill-progress');
               const width = skillProgress.getAttribute('data-width'); 

               setTimeout(() => {
                  skillProgress.style.width = width + '%';
               }, 200);

               observer.unobserve(entry.target);
            }
         });
      }, { threshold: 0.3 });

      skillItems.forEach(item => {
         observer.observe(item);
      });
      
      // Animate ecosystem progress bars
      const ecosystemItems = document.querySelectorAll('.ecosystem-skill-item');
      
      const ecosystemObserver = new IntersectionObserver((entries) => {
         entries.forEach(entry => {
            if (entry.isIntersecting) {
               const progressBar = entry.target.querySelector('.ecosystem-progress-bar');
               const width = progressBar.getAttribute('data-width');
               
               setTimeout(() => {
                  progressBar.style.width = width + '%';
               }, 300);
               
               ecosystemObserver.unobserve(entry.target);
            }
         });
      }, { threshold: 0.3 });
      
      ecosystemItems.forEach(item => {
         ecosystemObserver.observe(item);
      });
   });
</script>

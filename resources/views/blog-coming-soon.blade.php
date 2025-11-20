<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Blog - Coming Soon | Baraa Al-Rifaee</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
      rel="stylesheet">
   <script src="https://unpkg.com/@splinetool/runtime@1.0.47/build/runtime.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
   <style>
      :root {
         --background: #ebe2d5;
         --primary: #730c0e;
         --primary-gradient: #c44a4c;
         --buble: #540000;
         --primary-light: #8a0e11;
         --secondary: #210207;
         --accent: #480415;
         --dark: #140f17;
         --darker: #0a080b;
         --light: #ffffff;
         --gray: #e9ecef;
         --gray-dark: #6c757d;
         --gradient: linear-gradient(135deg, var(--primary) 40%, var(--accent) 60%);
         --buble-gradient: linear-gradient(135deg, var(--primary) 0%, var(--buble) 100%);
         --gradient-dark: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);
         --shadow: 0 10px 30px rgba(115, 12, 14, 0.3);
         --shadow-lg: 0 20px 50px rgba(115, 12, 14, 0.4);
         --border-radius: 16px;
         --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      }

      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }

      body {
         font-family: 'Inter', sans-serif;
         background: var(--background);
         color: var(--light);
         line-height: 1.6;
         min-height: 100vh;
         overflow-x: hidden;
      }

      .coming-soon-container {
         min-height: 100vh;
         display: flex;
         align-items: center;
         justify-content: center;
         padding: 2rem;
         position: relative;
         overflow: hidden;
      }

      #spline-canvas {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         z-index: 0;
         pointer-events: none;
      }

      .modern-3d-container {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         z-index: 0;
         pointer-events: none;
         overflow: hidden;
      }

      .floating-sphere {
         position: absolute;
         border-radius: 50%;
         background: radial-gradient(circle at 30% 30%, rgba(196, 74, 76, 0.8), rgba(115, 12, 14, 0.4));
         filter: blur(1px);
         animation: modernFloat 8s ease-in-out infinite;
      }

      .morphing-blob {
         position: absolute;
         background: linear-gradient(45deg, #730c0e, #c44a4c, #480415);
         border-radius: 50%;
         filter: blur(2px);
         animation: morphBlob 12s ease-in-out infinite;
      }

      .background-animation {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         z-index: 1;
      }

      .floating-element {
         position: absolute;
         background: var(--gradient);
         border-radius: 50%;
         /* opacity: 0.7; */
         animation: float 4s ease-in-out infinite;
      }

      .element-1 {
         width: 200px;
         height: 200px;
         top: 10%;
         left: 15%;
         animation-delay: 0s;
      }

      .element-2 {
         width: 150px;
         height: 150px;
         top: 60%;
         right: 15%;
         animation-delay: 2s;
      }

      .element-3 {
         width: 100px;
         height: 100px;
         bottom: 20%;
         left: 20%;
         animation-delay: 3s;
      }

      .element-4 {
         width: 100px;
         height: 100px;
         bottom: 30%;
         left: 10%;
         animation-delay: 5s;
      }

      .element-5 {
         width: 100px;
         height: 100px;
         top: 20%;
         right: 20%;
         animation-delay: 4s;
      }

      .content-wrapper {
         position: relative;
         z-index: 2;
         text-align: center;
         max-width: 800px;
         width: 100%;
      }

      .logo {
         font-size: 3rem;
         font-weight: 900;
         background: var(--gradient);
         -webkit-background-clip: text;
         -webkit-text-fill-color: transparent;
         background-clip: text;
         margin-bottom: 2rem;
         display: inline-block;
      }

      .coming-soon-card {
         /* background: white; */
         background: var(--background);
         color: var(--background);
         background: linear-gradient(145deg, rgba(72, 4, 21, 0.3) 25%, var(--background) 85%);
         backdrop-filter: blur(30px);
         border: 1px solid rgba(115, 12, 14, 0.3);
         border-radius: 50px;
         padding: 4rem 3rem;
         box-shadow: var(--shadow-lg);
         position: relative;
         overflow: hidden;
      }

      .coming-soon-card::before {
         content: '';
         position: absolute;
         top: 0;
         left: -100%;
         width: 100%;
         height: 100%;
         background: linear-gradient(90deg, transparent, rgba(115, 12, 14, 0.1), transparent);
         transition: var(--transition);
      }

      .coming-soon-card:hover::before {
         left: 100%;
      }

      .icon-container {
         width: 100px;
         height: 100px;
         background: var(--gradient);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 2rem;
         font-size: 2.5rem;
         color: var(--light);
         box-shadow: var(--shadow);
         animation: pulse 2s ease-in-out infinite;
      }

      h1 {
         font-size: 3.5rem;
         font-weight: 800;
         margin-bottom: 1rem;
         background: linear-gradient(135deg, var(--light) 0%, var(--gray) 100%);
         -webkit-background-clip: text;
         -webkit-text-fill-color: transparent;
         background-clip: text;
      }

      .subtitle {
         font-size: 1.3rem;
         color: var(--gray);
         margin-bottom: 2rem;
         font-weight: 400;
      }

      .description {
         font-size: 1.1rem;
         color: var(--gray-dark);
         margin-bottom: 3rem;
         max-width: 600px;
         margin-left: auto;
         margin-right: auto;
         line-height: 1.8;
      }

      .countdown {
         display: grid;
         grid-template-columns: repeat(4, 1fr);
         gap: 1rem;
         margin-bottom: 3rem;
         max-width: 500px;
         margin-left: auto;
         margin-right: auto;
      }

      .countdown-item {
         background: rgba(20, 15, 23, 0.8);
         border: 1px solid rgba(115, 12, 14, 0.3);
         border-radius: var(--border-radius);
         padding: 1.5rem;
         text-align: center;
      }

      .countdown-number {
         font-size: 2.5rem;
         font-weight: 800;
         color: var(--primary);
         display: block;
         line-height: 1;
      }

      .countdown-label {
         font-size: 0.9rem;
         color: var(--gray);
         text-transform: uppercase;
         letter-spacing: 1px;
         margin-top: 0.5rem;
      }

      .notify-form {
         max-width: 400px;
         margin: 0 auto;
      }

      .form-group {
         margin-bottom: 1rem;
      }

      .form-control {
         width: 100%;
         padding: 1rem 1.5rem;
         background: rgba(20, 15, 23, 0.8);
         border: 1px solid rgba(115, 12, 14, 0.3);
         border-radius: var(--border-radius);
         color: var(--light);
         font-size: 1rem;
         transition: var(--transition);
      }

      .form-control:focus {
         outline: none;
         border-color: var(--primary);
         box-shadow: 0 0 0 3px rgba(115, 12, 14, 0.1);
      }

      .form-control::placeholder {
         color: var(--gray-dark);
      }

      .btn {
         display: inline-flex;
         align-items: center;
         gap: 0.5rem;
         padding: 1rem 2.5rem;
         background: var(--gradient);
         border: none;
         border-radius: var(--border-radius);
         color: var(--light);
         font-weight: 700;
         text-decoration: none;
         cursor: pointer;
         transition: var(--transition);
         position: relative;
         overflow: hidden;
         font-size: 1rem;
      }

      .btn::before {
         content: '';
         position: absolute;
         top: 0;
         left: -100%;
         width: 100%;
         height: 100%;
         background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
         transition: var(--transition);
      }

      .btn:hover::before {
         left: 100%;
      }

      .btn:hover {
         transform: translateY(-3px);
         box-shadow: var(--shadow-lg);
      }

      .btn-block {
         width: 100%;
         justify-content: center;
      }

      .social-links {
         display: flex;
         justify-content: center;
         gap: 1rem;
         margin-top: 2rem;
      }

      .social-link {
         display: flex;
         align-items: center;
         justify-content: center;
         width: 50px;
         height: 50px;
         background: rgba(115, 12, 14, 0.1);
         color: var(--light);
         border-radius: 50%;
         text-decoration: none;
         transition: var(--transition);
         border: 1px solid rgba(115, 12, 14, 0.3);
      }

      .social-link:hover {
         background: var(--gradient);
         transform: translateY(-5px);
         box-shadow: var(--shadow);
      }

      .back-home {
         margin-top: 2rem;
      }

      .back-home a {
         color: var(--gray);
         text-decoration: none;
         transition: var(--transition);
         display: inline-flex;
         align-items: center;
         gap: 0.5rem;
      }

      .back-home a:hover {
         color: var(--gradient);
      }

      /* Animations */
      @keyframes modernFloat {
         0%, 100% {
            transform: translate3d(0, 0, 0) scale(1) rotate(0deg);
         }
         25% {
            transform: translate3d(20px, -30px, 0) scale(1.1) rotate(90deg);
         }
         50% {
            transform: translate3d(-10px, -60px, 0) scale(0.9) rotate(180deg);
         }
         75% {
            transform: translate3d(-30px, -30px, 0) scale(1.05) rotate(270deg);
         }
      }

      @keyframes morphBlob {
         0%, 100% {
            border-radius: 50% 40% 60% 30%;
            transform: rotate(0deg) scale(1);
         }
         25% {
            border-radius: 30% 60% 40% 70%;
            transform: rotate(90deg) scale(1.2);
         }
         50% {
            border-radius: 70% 30% 50% 60%;
            transform: rotate(180deg) scale(0.8);
         }
         75% {
            border-radius: 40% 70% 30% 50%;
            transform: rotate(270deg) scale(1.1);
         }
      }

      @keyframes pulse {

         0%,
         100% {
            transform: scale(1);
         }

         50% {
            transform: scale(1.05);
         }
      }

      @keyframes fadeInUp {
         from {
            opacity: 0;
            transform: translateY(50px);
         }

         to {
            opacity: 1;
            transform: translateY(0);
         }
      }

      /* Responsive Design */
      @media (max-width: 768px) {
         .coming-soon-container {
            padding: 1rem;
         }

         .coming-soon-card {
            padding: 3rem 2rem;
         }

         h1 {
            font-size: 2.5rem;
         }

         .subtitle {
            font-size: 1.1rem;
         }

         .countdown {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
         }

         .countdown-item {
            padding: 1rem;
         }

         .countdown-number {
            font-size: 2rem;
         }

         .icon-container {
            width: 80px;
            height: 80px;
            font-size: 2rem;
         }
      }

      @media (max-width: 480px) {
         h1 {
            font-size: 2rem;
         }

         .subtitle {
            font-size: 1rem;
         }

         .description {
            font-size: 0.9rem;
         }

         .countdown {
            grid-template-columns: 1fr;
         }

         .coming-soon-card {
            padding: 2rem 1.5rem;
         }
      }

      /* Hide scrollbar */
      ::-webkit-scrollbar {
         width: 0px;
         background: transparent;
      }

      * {
         -ms-overflow-style: none;
         scrollbar-width: none;
      }
   </style>
</head>

<body>
   <!-- Modern 3D Background -->
   <div class="modern-3d-container" id="modern3d">
      <canvas id="spline-canvas"></canvas>
   </div>
   
   <div class="coming-soon-container">
      <!-- Background Animation -->
      <div class="background-animation">
         <div class="floating-element element-1"></div>
         <div class="floating-element element-2"></div>
         <div class="floating-element element-3"></div>
         <div class="floating-element element-4"></div>
         <div class="floating-element element-5"></div>
      </div>

      <!-- Main Content -->
      <div class="content-wrapper">
         <div class="logo">Baraa Al-Rifaee</div>

         <div class="coming-soon-card">
            <div class="icon-container">
               <i class="fas fa-blog"></i>
            </div>

            <h1>Coming Soon</h1>
            <div class="subtitle">My Blog is Under Construction</div>

            <!-- Social Links -->
            <div class="social-links">
               <a href="#" class="social-link">
                  <i class="fab fa-linkedin-in"></i>
               </a>
               <a href="#" class="social-link">
                  <i class="fab fa-github"></i>
               </a>
               <a href="#" class="social-link">
                  <i class="fab fa-twitter"></i>
               </a>
               <a href="#" class="social-link">
                  <i class="fab fa-instagram"></i>
               </a>
            </div>
         </div>

         <!-- Back to Home -->
         <div class="back-home">
            <a class="btn" href="{{ route('home') }}">
               <i class="fas fa-arrow-left"></i>
               Back to Portfolio
            </a>
         </div>
      </div>
   </div>

   <script src="{{ asset('js/modern-3d-blog.js') }}"></script>
   <script>
      // Initialize fade-in animations
      document.addEventListener('DOMContentLoaded', function () {
         const elements = document.querySelectorAll('.coming-soon-card, .logo, .back-home');
         elements.forEach((element, index) => {
            gsap.from(element, {
               y: 50,
               opacity: 0,
               duration: 1,
               delay: index * 0.2,
               ease: "power3.out"
            });
         });
      });
   </script>
</body>

</html>

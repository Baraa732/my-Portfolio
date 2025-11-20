<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Baraa Al-Rifaee</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
            <div class="shape shape-6"></div>
        </div>
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h1 class="logo-text">Baraa Al-Rifaee</h1>
                <p class="logo-subtitle">Admin Portal</p>
            </div>

            <!-- Login Form -->
            <form class="login-form" method="POST" action="{{ route('admin.login.submit') }}" 
                  autocomplete="off" novalidate data-lpignore="true">
                @csrf
                
                <!-- Email Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="Email Address" 
                               value="{{ old('email') }}" required autocomplete="new-password" autofocus 
                               data-lpignore="true" data-form-type="other" 
                               maxlength="255" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">
                        <div class="input-line"></div>
                    </div>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Password" 
                               required autocomplete="new-password" 
                               data-lpignore="true" data-form-type="other" 
                               maxlength="255" minlength="6">
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div class="input-line"></div>
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        <span class="checkbox-text">Remember me</span>
                    </label>
                </div>



                <!-- Submit Button -->
                <button type="submit" class="login-btn">
                    <span class="btn-text">Sign In</span>
                    <div class="btn-loader">
                        <div class="loader-ring"></div>
                    </div>
                    <i class="fas fa-arrow-right btn-icon"></i>
                </button>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; {{ date('Y') }} Baraa Al-Rifaee. All rights reserved.</p>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
            <div class="panel-content">
                <div class="panel-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2>Welcome Back!</h2>
                <p>Access your admin dashboard to manage your portfolio, projects, and content.</p>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics Dashboard</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-project-diagram"></i>
                        <span>Project Management</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-envelope"></i>
                        <span>Message Center</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
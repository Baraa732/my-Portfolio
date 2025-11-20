<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // Enhanced validation with security rules
        $credentials = $request->validate([
            'email' => 'required|email|max:255|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => 'required|min:6|max:255|regex:/^[\x20-\x7E]*$/',
        ]);

        // Honeypot check (bot detection)
        if ($request->filled('website')) {
            \Log::warning('Honeypot triggered', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'honeypot_value' => $request->input('website')
            ]);
            
            if ($request->ajax()) {
                return response()->json(['message' => 'Invalid request.'], 422);
            }
            return back()->withErrors(['email' => 'Invalid request.']);
        }
        
        // Check for suspicious patterns in input
        $suspiciousPatterns = [
            '/(<script[^>]*>.*?<\/script>)/is',
            '/(javascript:|vbscript:|onload=|onerror=)/i',
            '/(union|select|insert|update|delete|drop|create|alter)/i'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $request->input('email')) || 
                preg_match($pattern, $request->input('password'))) {
                \Log::warning('Suspicious input detected in login', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'email' => $request->input('email')
                ]);
                
                if ($request->ajax()) {
                    return response()->json(['message' => 'Invalid input detected.'], 422);
                }
                return back()->withErrors(['email' => 'Invalid input detected.']);
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if (!$user->is_admin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                \Log::warning('Non-admin login attempt', [
                    'user_id' => $user->uuid,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                if ($request->ajax()) {
                    return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
                }
                return back()->withErrors([
                    'email' => 'Access denied. Admin privileges required.',
                ]);
            }
            
            // Session fixation prevention removed to fix session expiry issues
            
            // Set admin verification flag
            $request->session()->put('admin_verified', true);
            
            // Log successful login with more details
            \Log::info('Admin login successful', [
                'user_id' => $user->uuid,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);
            ActivityLogger::logLogin($user);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => '/admin']);
            }
            return redirect()->intended('/admin');
        }

        // Log failed login attempt with more details
        \Log::warning('Failed login attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString(),
            'referer' => $request->header('referer')
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'The provided credentials do not match our records.'], 422);
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout
        if ($user) {
            \Log::info('Admin logout', ['user_id' => $user->uuid, 'ip' => $request->ip()]);
            ActivityLogger::logLogout($user);
        }
        
        Auth::logout();
        
        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear remember me cookie
        $response = redirect('/admin/login')->with('status', 'You have been logged out successfully.');
        $response->withCookie(cookie()->forget('remember_web'));
        
        return $response;
    }
}

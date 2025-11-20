<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Check if user has admin privileges
        if (!$user->is_admin) {
            Log::warning('Non-admin user attempted to access admin area', [
                'user_id' => $user->uuid,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
            
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
            }
            return redirect()->route('admin.login')->withErrors(['email' => 'Access denied. Admin privileges required.']);
        }

        // Session security checks
        if (!$request->session()->has('admin_verified')) {
            $request->session()->put('admin_verified', true);
        }

        return $next($request);
    }
}
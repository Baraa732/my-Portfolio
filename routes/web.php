<?php

use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SkillEcosystemController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// =====================
// MAIN FRONTEND ROUTES
// =====================

// Portfolio Routes with rate limiting
Route::middleware(['rate.limit:120,1'])->group(function () {
    Route::get('/', [PortfolioController::class, 'home'])->name('home');
    Route::get('/about', [PortfolioController::class, 'about'])->name('about');
    Route::get('/skills', [PortfolioController::class, 'skills'])->name('skills');
    Route::get('/projects', [PortfolioController::class, 'projects'])->name('projects');
    Route::get('/contact', [PortfolioController::class, 'contact'])->name('contact');
    Route::get('/download-cv', [\App\Http\Controllers\CVController::class, 'download'])->name('download.cv');
});

// Contact form with stricter rate limiting and sanitization
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware(['rate.limit:5,10', 'sanitize'])
    ->name('contact.submit');

// =====================
// AUTH ROUTES
// =====================

// Auth routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth', 'admin'])
    ->name('logout');



// Redirect /admin to login if not authenticated
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

// =====================
// PROTECTED ADMIN ROUTES
// =====================

Route::middleware(['admin', 'rate.limit:300,1', 'sanitize'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard-data', [AdminController::class, 'getDashboardData'])->name('dashboard.data');



    // Projects routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Sections routes - make sure these exist
    Route::get('/sections', [AdminController::class, 'getSections'])->name('sections.index');
    Route::post('/sections', [AdminController::class, 'storeSection'])->name('sections.store');
    Route::get('/sections/{id}', [AdminController::class, 'getSection'])->name('sections.show');
    Route::put('/sections/{id}', [AdminController::class, 'updateSection'])->name('sections.update');
    Route::delete('/sections/{id}', [AdminController::class, 'deleteSection'])->name('sections.destroy');

    // Messages routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::put('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::put('/messages/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('messages.mark-all-read');
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::get('/messages-stats', [MessageController::class, 'stats'])->name('messages.stats');
    // Inside the admin group
    Route::post('/messages/{id}/reply', [MessageController::class, 'reply'])->name('messages.reply');

    // Profile routes - ADD THESE
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');

    // Notification routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Analytics
    Route::get('/analytics', [AdminController::class, 'getAnalytics'])->name('analytics');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [\App\Http\Controllers\SettingsController::class, 'testEmail'])->name('settings.test-email');
    
    // Skill Ecosystems
    Route::get('/skills-ecosystem', [SkillEcosystemController::class, 'index'])->name('skills-ecosystem.index');
    Route::get('/skills-ecosystem/data', [SkillEcosystemController::class, 'getData'])->name('skills-ecosystem.data');
    Route::post('/skills-ecosystem', [SkillEcosystemController::class, 'store'])->name('skills-ecosystem.store');
    Route::put('/skills-ecosystem/{skill}', [SkillEcosystemController::class, 'update'])->name('skills-ecosystem.update');
    Route::delete('/skills-ecosystem/{skill}', [SkillEcosystemController::class, 'destroy'])->name('skills-ecosystem.destroy');
    Route::post('/skills-ecosystem/order', [SkillEcosystemController::class, 'updateOrder'])->name('skills-ecosystem.order');
    Route::post('/skills-ecosystem/toggle-section', [SkillEcosystemController::class, 'toggleSection'])->name('skills-ecosystem.toggle-section');
    Route::post('/skills-ecosystem/update-section', [SkillEcosystemController::class, 'updateSection'])->name('skills-ecosystem.update-section');
    
    // Backup routes
    Route::post('/settings/backup/create', [\App\Http\Controllers\SettingsController::class, 'createBackup'])->name('settings.backup.create');
    Route::get('/settings/backup/list', [\App\Http\Controllers\SettingsController::class, 'listBackups'])->name('settings.backup.list');
    Route::delete('/settings/backup/{backupName}', [\App\Http\Controllers\SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
    Route::post('/settings/backup/{backupName}/restore', [\App\Http\Controllers\SettingsController::class, 'restoreBackup'])->name('settings.backup.restore');
    Route::post('/settings/backup/test-auto', [\App\Http\Controllers\SettingsController::class, 'testAutoBackup'])->name('settings.backup.test-auto');
    Route::get('/settings/backup/cron-status', [\App\Http\Controllers\SettingsController::class, 'checkCronStatus'])->name('settings.backup.cron-status');
    Route::get('/settings/backup/{backupName}/progress', [\App\Http\Controllers\SettingsController::class, 'getBackupProgress'])->name('settings.backup.progress');
});

Route::get('/blog', function () {
    return view('blog-coming-soon');
})->name('blog');

// Test email route
Route::get('/test-email', function () {
    try {
        Mail::send('emails.test', [], function ($message) {
            $message->to('baraaalrifaee732@gmail.com')
                ->subject('Test Email from Portfolio');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/test-auto-reply', [ContactController::class, 'testEmail']);

// Include test routes
include __DIR__ . '/test.php';


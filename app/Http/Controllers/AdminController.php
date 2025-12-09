<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Contact;
use App\Models\User;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ActivityLogger;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_projects' => Project::count(),
            'active_skills' => Skill::where('is_active', true)->count(),
            'unread_messages' => Contact::where('is_read', false)->count(),
            'total_views' => 2456,
        ];

        $recent_activities = $this->getRecentActivities();
        $latest_messages = Contact::latest()->take(5)->get();
        $recent_projects = Project::latest()->take(3)->get();

        return view('admin.dashboard', compact('stats', 'recent_activities', 'latest_messages', 'recent_projects'));
    }

    public function getDashboardData()
    {
        $stats = [
            'total_projects' => Project::count(),
            'active_skills' => Skill::where('is_active', true)->count(),
            'unread_messages' => Contact::where('is_read', false)->count(),
            'total_views' => 2456,
        ];

        $recent_activities = $this->getRecentActivities();
        $latest_messages = Contact::latest()->take(5)->get();

        return response()->json([
            'stats' => $stats,
            'recent_activities' => $recent_activities,
            'latest_messages' => $latest_messages
        ]);
    }

    private function getRecentActivities()
    {
        $logs = \App\Models\ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return $logs->map(function($log) {
            return [
                'action' => ucfirst($log->action),
                'item' => $log->description,
                'date' => $log->created_at->diffForHumans(),
                'status' => 'Completed',
                'icon' => $this->getActionIcon($log->action),
                'user' => $log->user ? $log->user->name : 'System'
            ];
        });
    }

    private function getActionIcon($action)
    {
        return match($action) {
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'create' => 'fas fa-plus-circle',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'view' => 'fas fa-eye',
            'password_change' => 'fas fa-key',
            default => 'fas fa-circle'
        };
    }

    // Profile Management
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    // Profile Management - UPDATED
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s\-\'\.À-ſ]+$/u',
            'email' => 'required|email|max:255|unique:users,email,' . $user->uuid . ',uuid|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'title' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-_.,()]+$/',
            'bio' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20|regex:/^[+]?[0-9\s\-()]+$/',
            'location' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-_.,()]+$/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.regex' => 'Name contains invalid characters.',
            'email.regex' => 'Please enter a valid email address.',
            'title.regex' => 'Title contains invalid characters.',
            'phone.regex' => 'Phone number contains invalid characters.',
            'location.regex' => 'Location contains invalid characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Sanitize input data
        $data = [
            'name' => htmlspecialchars(strip_tags($request->input('name')), ENT_QUOTES, 'UTF-8'),
            'email' => filter_var($request->input('email'), FILTER_SANITIZE_EMAIL),
            'title' => htmlspecialchars(strip_tags($request->input('title')), ENT_QUOTES, 'UTF-8'),
            'bio' => htmlspecialchars(strip_tags($request->input('bio')), ENT_QUOTES, 'UTF-8'),
            'phone' => htmlspecialchars(strip_tags($request->input('phone')), ENT_QUOTES, 'UTF-8'),
            'location' => htmlspecialchars(strip_tags($request->input('location')), ENT_QUOTES, 'UTF-8'),
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        try {
            $user->update($data);
            $user->refresh(); // Refresh from database
            ActivityLogger::logUpdate($user, 'Updated profile information');

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'user' => $user->fresh() // Get fresh data
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|max:255',
            'new_password' => 'required|string|min:8|max:255|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ], [
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            ActivityLogger::log('password_change', 'Changed password', $user);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Password update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    // Section Management
    public function getSections()
    {
        $sections = Section::all();
        return response()->json($sections);
    }

    public function updateSection(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['title', 'content', 'is_active']);

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::delete('public/' . $section->image);
            }
            $data['image'] = $request->file('image')->store('sections', 'public');
        }

        try {
            $section->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Section updated successfully!',
                'section' => $section
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update section: ' . $e->getMessage()
            ], 500);
        }
    }

    // Skill Management
    public function getSkills()
    {
        $skills = Skill::orderBy('order')->get();
        return response()->json($skills);
    }

    // Add this method to AdminController.php
    public function getSkill($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            return response()->json($skill);
        } catch (\Exception $e) {
            \Log::error('Skill fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Skill not found'
            ], 404);
        }
    }

    // Add these methods to AdminController.php
    public function storeSection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sections',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'show_in_nav' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            $data['show_in_nav'] = $request->has('show_in_nav') ? 1 : 0;

            $section = Section::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Section created successfully!',
                'section' => $section
            ]);
        } catch (\Exception $e) {
            \Log::error('Section creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create section: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSection($id)
    {
        try {
            $section = Section::findOrFail($id);
            return response()->json($section);
        } catch (\Exception $e) {
            \Log::error('Error fetching section: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }
    }

    public function deleteSection($id)
    {
        try {
            $section = Section::findOrFail($id);
            $section->delete();

            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Section deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete section: ' . $e->getMessage()
            ], 500);
        }
    }

    // In AdminController.php - Update storeSkill method
    public function storeSkill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
            'icon' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle checkbox boolean
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            // Ensure order is set
            $data['order'] = $request->get('order', 0);

            $skill = Skill::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Skill added successfully!',
                'skill' => $skill
            ]);
        } catch (\Exception $e) {
            \Log::error('Skill creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create skill: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update updateSkill method
    public function updateSkill(Request $request, $id)
    {
        $skill = Skill::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:0|max:100',
            'icon' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle checkbox boolean
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            // Ensure order is set
            $data['order'] = $request->get('order', $skill->order);

            $skill->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Skill updated successfully!',
                'skill' => $skill
            ]);
        } catch (\Exception $e) {
            \Log::error('Skill update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update skill: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroySkill($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->delete();

            return response()->json([
                'success' => true,
                'message' => 'Skill deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete skill: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSkillsOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skills' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->skills as $index => $skillData) {
                Skill::where('id', $skillData['id'])->update(['order' => $index]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Skills order updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skills order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Project Management
    public function getProjects()
    {
        $projects = Project::orderBy('order')->get();
        return response()->json($projects);
    }

    // In AdminController.php - Update these methods:

    public function storeProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'technologies' => 'required|string|max:500',
            'project_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['title', 'description', 'technologies', 'project_url', 'github_url', 'order']);

            // Handle checkbox boolean properly
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('projects', 'public');
                $data['image'] = $imagePath;

                \Log::info('Image uploaded successfully: ' . $imagePath);
            } else {
                \Log::info('No image file in request');
            }

            \Log::info('Creating project with data: ', $data);

            $project = Project::create($data);

            \Log::info('Project created successfully with ID: ' . $project->id);

            return response()->json([
                'success' => true,
                'message' => 'Project added successfully!',
                'project' => $project
            ]);
        } catch (\Exception $e) {
            \Log::error('Project creation error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'technologies' => 'required|string|max:500',
            'project_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['title', 'description', 'technologies', 'project_url', 'github_url', 'is_active', 'order']);

        // Handle checkbox boolean
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($project->image && Storage::exists('public/' . $project->image)) {
                Storage::delete('public/' . $project->image);
            }
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        try {
            $project->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully!',
                'project' => $project
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage()
            ], 500);
        }
    }

    // In AdminController.php - Fix the delete method
    // Temporary debug version - add this to your AdminController
    public function destroyProject($id)
    {
        \Log::info('Attempting to delete project ID: ' . $id);

        try {
            $project = Project::find($id);

            if (!$project) {
                \Log::warning('Project not found with ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found'
                ], 404);
            }

            \Log::info('Found project: ' . $project->title);

            // Check and delete image
            if ($project->image) {
                \Log::info('Project has image: ' . $project->image);
                $imagePath = 'public/' . $project->image;

                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                    \Log::info('Image deleted successfully');
                } else {
                    \Log::warning('Image file not found: ' . $imagePath);
                }
            } else {
                \Log::info('Project has no image to delete');
            }

            // Delete the project
            $project->delete();
            \Log::info('Project deleted from database');

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Project deletion failed: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Server error while deleting project'
            ], 500);
        }
    }

    public function updateProjectsOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'projects' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->projects as $index => $projectData) {
                Project::where('id', $projectData['id'])->update(['order' => $index]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Projects order updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update projects order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Contact Management
    public function getMessages()
    {
        $messages = Contact::latest()->get();
        return response()->json($messages);
    }

    public function getMessage($id)
    {
        try {
            $message = Contact::findOrFail($id);

            // Mark as read when viewing
            if (!$message->is_read) {
                $message->update(['is_read' => true]);
            }

            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
    }

    public function markAsRead($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Message marked as read!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark message as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            Contact::where('is_read', false)->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'All messages marked as read!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyMessage($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message: ' . $e->getMessage()
            ], 500);
        }
    }

    // Analytics
    public function getAnalytics()
    {
        $totalViews = Analytics::count();
        $todayViews = Analytics::whereDate('created_at', today())->count();
        $weekViews = Analytics::where('created_at', '>=', now()->subWeek())->count();
        $monthViews = Analytics::where('created_at', '>=', now()->subMonth())->count();
        
        $uniqueVisitors = Analytics::distinct('ip_address')->count();
        
        $popularPages = Analytics::selectRaw('page, COUNT(*) as views')
            ->groupBy('page')
            ->orderByDesc('views')
            ->limit(5)
            ->get();
            
        $deviceStats = Analytics::selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->get();
            
        $browserStats = Analytics::selectRaw('browser, COUNT(*) as count')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
            
        $dailyViews = Analytics::selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'page_views' => [
                'total' => $totalViews,
                'monthly' => $monthViews,
                'weekly' => $weekViews,
                'daily' => $todayViews
            ],
            'visitors' => [
                'unique' => $uniqueVisitors,
                'total_visits' => $totalViews
            ],
            'popular_pages' => $popularPages,
            'device_stats' => $deviceStats,
            'browser_stats' => $browserStats,
            'daily_views' => $dailyViews
        ]);
    }

    // Backup
    public function getBackups()
    {
        $backups = [
            ['name' => 'backup-2024-01-15-10-30-00.zip', 'size' => '45.2 MB', 'date' => '2024-01-15 10:30:00'],
            ['name' => 'backup-2024-01-14-10-30-00.zip', 'size' => '44.8 MB', 'date' => '2024-01-14 10:30:00'],
            ['name' => 'backup-2024-01-13-10-30-00.zip', 'size' => '45.1 MB', 'date' => '2024-01-13 10:30:00'],
        ];

        return response()->json($backups);
    }

    public function createBackup()
    {
        try {
            // Simulate backup creation
            $filename = 'backup-' . date('Y-m-d-H-i-s') . '.zip';

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully!',
                'file' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create backup: ' . $e->getMessage()
            ], 500);
        }
    }

    // Settings
    public function getSettings()
    {
        $settings = [
            'site' => [
                'name' => 'My Portfolio',
                'description' => 'Professional Portfolio Website',
                'keywords' => 'portfolio, web developer, laravel, vue',
                'maintenance_mode' => false,
            ],
            'contact' => [
                'email' => 'contact@portfolio.com',
                'phone' => '+1 (555) 123-4567',
                'address' => 'Your City, Country',
            ],
            'social' => [
                'github' => 'https://github.com/username',
                'linkedin' => 'https://linkedin.com/in/username',
                'twitter' => 'https://twitter.com/username',
            ]
        ];

        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site.name' => 'required|string|max:255',
            'site.description' => 'nullable|string',
            'site.keywords' => 'nullable|string',
            'site.maintenance_mode' => 'boolean',
            'contact.email' => 'nullable|email',
            'contact.phone' => 'nullable|string',
            'contact.address' => 'nullable|string',
            'social.github' => 'nullable|url',
            'social.linkedin' => 'nullable|url',
            'social.twitter' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Here you would save settings to database or config file
            // For now, we'll just return success

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    
}

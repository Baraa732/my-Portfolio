<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\ActivityLogger;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('order')->get();
        return response()->json($projects);
    }

    public function show($id)
    {
        try {
            $project = Project::find($id);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found'
                ], 404);
            }

            return response()->json($project);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching project: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
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
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('projects', 'public');
                $data['image'] = $imagePath;
                \Log::info('Image uploaded in ProjectController: ' . $imagePath);
            }

            $project = Project::create($data);
            ActivityLogger::logCreate($project);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!',
                'project' => $project
            ]);
        } catch (\Exception $e) {
            \Log::error('Project creation error in ProjectController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
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

        try {
            $data = $request->only(['title', 'description', 'technologies', 'project_url', 'github_url', 'is_active', 'order']);
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            if ($request->hasFile('image')) {
                if ($project->image && Storage::exists('public/' . $project->image)) {
                    Storage::delete('public/' . $project->image);
                }
                $data['image'] = $request->file('image')->store('projects', 'public');
            }

            $project->update($data);
            ActivityLogger::logUpdate($project);

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

    public function destroy($id)
    {
        try {
            $project = Project::find($id);

            if (!$project) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project was already deleted or not found'
                ]);
            }

            if ($project->image && Storage::exists('public/' . $project->image)) {
                Storage::delete('public/' . $project->image);
            }

            $projectTitle = $project->title;
            $project->delete();
            ActivityLogger::log('delete', "Deleted project: {$projectTitle}");

            return response()->json([
                'success' => true,
                'message' => "Project '{$projectTitle}' deleted successfully!"
            ]);
        } catch (\Exception $e) {
            \Log::error('Project deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete project: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        $categories = ProjectCategory::orderBy('order')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name',
        ]);

        $category = ProjectCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_visible' => $request->is_visible ?? true,
            'order' => $request->order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = ProjectCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:project_categories,name,' . $id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_visible' => $request->is_visible ?? $category->is_visible,
            'order' => $request->order ?? $category->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    public function toggleVisibility($id)
    {
        $category = ProjectCategory::findOrFail($id);
        $category->is_visible = !$category->is_visible;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Visibility toggled',
            'category' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = ProjectCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    public function getVisible()
    {
        $categories = ProjectCategory::where('is_visible', true)
            ->orderBy('order')
            ->get();
        return response()->json($categories);
    }
}

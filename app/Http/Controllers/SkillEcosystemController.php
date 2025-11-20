<?php

namespace App\Http\Controllers;

use App\Models\SkillEcosystem;
use App\Models\EcosystemSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SkillEcosystemController extends Controller
{
    public function index()
    {
        $javascriptSection = EcosystemSection::where('ecosystem', 'javascript')->first();
        $phpSection = EcosystemSection::where('ecosystem', 'php')->first();
        
        $javascriptSkills = SkillEcosystem::byEcosystem('javascript')->ordered()->get();
        $phpSkills = SkillEcosystem::byEcosystem('php')->ordered()->get();

        return view('admin.skills-ecosystem', compact('javascriptSection', 'phpSection', 'javascriptSkills', 'phpSkills'));
    }

    public function getData()
    {
        $javascriptSection = EcosystemSection::where('ecosystem', 'javascript')->first();
        $phpSection = EcosystemSection::where('ecosystem', 'php')->first();
        
        $javascriptSkills = SkillEcosystem::byEcosystem('javascript')->ordered()->get();
        $phpSkills = SkillEcosystem::byEcosystem('php')->ordered()->get();

        return response()->json([
            'javascriptSection' => $javascriptSection,
            'phpSection' => $phpSection,
            'javascriptSkills' => $javascriptSkills,
            'phpSkills' => $phpSkills
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ecosystem' => 'required|in:javascript,php',
            'icon' => 'nullable|string|max:255',
            'proficiency' => 'required|integer|min:1|max:100'
        ]);

        SkillEcosystem::create($request->all());
        $this->clearSkillsCache();

        return response()->json(['success' => true, 'message' => 'Skill added successfully']);
    }

    public function update(Request $request, SkillEcosystem $skill)
    {
        if ($request->has('toggle_status')) {
            $skill->update(['is_active' => !$skill->is_active]);
            $this->clearSkillsCache();
            return response()->json([
                'success' => true, 
                'message' => $skill->is_active ? 'Skill activated' : 'Skill deactivated',
                'is_active' => $skill->is_active
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'proficiency' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean'
        ]);

        $skill->update($request->all());
        $this->clearSkillsCache();

        return response()->json(['success' => true, 'message' => 'Skill updated successfully']);
    }

    public function destroy(SkillEcosystem $skill)
    {
        $skill->delete();
        $this->clearSkillsCache();
        return response()->json(['success' => true, 'message' => 'Skill deleted successfully']);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->skills as $index => $skillId) {
            SkillEcosystem::where('id', $skillId)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleSection(Request $request)
    {
        $section = EcosystemSection::where('ecosystem', $request->ecosystem)->first();
        
        if (!$section) {
            $section = EcosystemSection::create([
                'ecosystem' => $request->ecosystem,
                'title' => ucfirst($request->ecosystem) . ' Ecosystem',
                'is_visible' => false
            ]);
        }

        $section->update(['is_visible' => !$section->is_visible]);

        return response()->json([
            'success' => true, 
            'is_visible' => $section->is_visible,
            'message' => $section->is_visible ? 'Section enabled' : 'Section disabled'
        ]);
    }

    public function updateSection(Request $request)
    {
        $request->validate([
            'ecosystem' => 'required|in:javascript,php',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $section = EcosystemSection::updateOrCreate(
            ['ecosystem' => $request->ecosystem],
            $request->only(['title', 'description'])
        );

        return response()->json(['success' => true, 'message' => 'Section updated successfully']);
    }

    private function clearSkillsCache()
    {
        Cache::forget('skills.javascript');
        Cache::forget('skills.php');
        Cache::forget('skills.home.javascript');
        Cache::forget('skills.home.php');
        Cache::forget('sections.javascript');
        Cache::forget('sections.php');
    }
}
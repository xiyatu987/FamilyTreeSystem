<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\Ziwei;
use App\Services\KinshipService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FamilyMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示家族成员列表
    public function index()
    {
        $familyMembers = FamilyMember::where('user_id', auth()->id())->with(['father', 'mother', 'ziwei'])->paginate(15);
        return view('family-members.index', compact('familyMembers'));
    }

    // 显示创建家族成员表单
    public function create()
    {
        $ziweiList = Ziwei::where('user_id', auth()->id())->get();
        $familyMembers = FamilyMember::where('user_id', auth()->id())->orderBy('name')->get();
        return view('family-members.create', compact('ziweiList', 'familyMembers'));
    }

    // 存储新家族成员
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'birth_date' => 'nullable|date',
            'death_date' => 'nullable|date|after_or_equal:birth_date',
            'birth_place' => 'nullable|string|max:255',
            'death_place' => 'nullable|string|max:255',
            'father_id' => 'nullable|exists:family_members,id,user_id,' . auth()->id(),
            'mother_id' => 'nullable|exists:family_members,id,user_id,' . auth()->id(),
            'spouse_id' => 'nullable|exists:family_members,id,user_id,' . auth()->id(),
            'ziwei_id' => 'nullable|exists:ziwei,id,user_id,' . auth()->id(),
            'generation' => 'nullable|integer',
            'description' => 'nullable|string'
        ]);

        $validatedData['user_id'] = auth()->id();
        $familyMember = FamilyMember::create($validatedData);

        return redirect()->route('family-members.index')
                         ->with('success', '家族成员信息创建成功！');
    }

    // 显示家族成员详情
    public function show(FamilyMember $familyMember)
    {
        $this->authorize('view', $familyMember);
        
        // 获取所有亲属关系
        $kinshipService = new KinshipService();
        $relatives = $kinshipService->getAllRelatives($familyMember);
        
        return view('family-members.show', compact('familyMember', 'relatives'));
    }

    // 显示编辑家族成员表单
    public function edit(FamilyMember $familyMember)
    {
        $this->authorize('update', $familyMember);
        $ziweiList = Ziwei::where('user_id', auth()->id())->get();
        $familyMembers = FamilyMember::where('user_id', auth()->id())->where('id', '!=', $familyMember->id)->orderBy('name')->get();
        return view('family-members.edit', compact('familyMember', 'ziweiList', 'familyMembers'));
    }

    // 更新家族成员信息
    public function update(Request $request, FamilyMember $familyMember)
    {
        $this->authorize('update', $familyMember);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'birth_date' => 'nullable|date',
            'death_date' => 'nullable|date|after_or_equal:birth_date',
            'birth_place' => 'nullable|string|max:255',
            'death_place' => 'nullable|string|max:255',
            'father_id' => 'nullable|exists:family_members,id,user_id,' . auth()->id(),
            'mother_id' => 'nullable|exists:family_members,id,user_id,' . auth()->id(),
            'spouse_id' => 'nullable|exists:family_members,id,user_id,' . auth()->id(),
            'ziwei_id' => 'nullable|exists:ziwei,id,user_id,' . auth()->id(),
            'generation' => 'nullable|integer',
            'description' => 'nullable|string'
        ]);

        $familyMember->update($validatedData);

        return redirect()->route('family-members.index')
                         ->with('success', '家族成员信息更新成功！');
    }

    // 删除家族成员
    public function destroy(FamilyMember $familyMember)
    {
        $this->authorize('delete', $familyMember);
        $familyMember->delete();

        return redirect()->route('family-members.index')
                         ->with('success', '家族成员信息删除成功！');
    }

    // 搜索家族成员
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $familyMembers = FamilyMember::where('user_id', auth()->id())
                                     ->where(function($query) use ($searchTerm) {
                                         $query->where('name', 'like', '%' . $searchTerm . '%')
                                               ->orWhere('birth_place', 'like', '%' . $searchTerm . '%')
                                               ->orWhere('death_place', 'like', '%' . $searchTerm . '%');
                                     })->with(['father', 'mother', 'ziwei'])->paginate(15);

        return view('family-members.index', compact('familyMembers', 'searchTerm'));
    }

    // 展示家族树
    public function familyTree()
    {
        $rootMembers = FamilyMember::where('user_id', auth()->id())
                                   ->whereNull('father_id')
                                   ->whereNull('mother_id')
                                   ->with('children')->get();
        
        return view('family-members.tree', compact('rootMembers'));
    }
}
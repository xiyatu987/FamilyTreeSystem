<?php

namespace App\Http\Controllers;

use App\Models\Grave;
use App\Models\FamilyMember;
use Illuminate\Http\Request;

class GraveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示墓地信息列表
    public function index()
    {
        $graves = Grave::where('user_id', auth()->id())->with('member')->paginate(15);
        return view('graves.index', compact('graves'));
    }

    // 显示创建墓地信息表单
    public function create()
    {
        $familyMembers = FamilyMember::where('user_id', auth()->id())->get();
        return view('graves.create', compact('familyMembers'));
    }

    // 存储新墓地信息
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_id' => 'required|exists:family_members,id,user_id,' . auth()->id(),
            'location' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $validatedData['user_id'] = auth()->id();
        Grave::create($validatedData);

        return redirect()->route('graves.index')
                         ->with('success', '墓地信息创建成功！');
    }

    // 显示墓地信息详情
    public function show(Grave $grave)
    {
        $this->authorize('view', $grave);
        return view('graves.show', compact('grave'));
    }

    // 显示编辑墓地信息表单
    public function edit(Grave $grave)
    {
        $this->authorize('update', $grave);
        $familyMembers = FamilyMember::where('user_id', auth()->id())->get();
        return view('graves.edit', compact('grave', 'familyMembers'));
    }

    // 更新墓地信息
    public function update(Request $request, Grave $grave)
    {
        $this->authorize('update', $grave);

        $validatedData = $request->validate([
            'member_id' => 'required|exists:family_members,id,user_id,' . auth()->id(),
            'location' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $grave->update($validatedData);

        return redirect()->route('graves.index')
                         ->with('success', '墓地信息更新成功！');
    }

    // 删除墓地信息
    public function destroy(Grave $grave)
    {
        $this->authorize('delete', $grave);
        $grave->delete();

        return redirect()->route('graves.index')
                         ->with('success', '墓地信息删除成功！');
    }
}

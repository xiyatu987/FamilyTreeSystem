<?php

namespace App\Http\Controllers;

use App\Models\FamilyRule;
use App\Models\Clan;
use Illuminate\Http\Request;

class FamilyRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示家族规则列表
    public function index()
    {
        $familyRules = FamilyRule::where('user_id', auth()->id())->with('clan')->paginate(15);
        return view('family-rules.index', compact('familyRules'));
    }

    // 显示创建家族规则表单
    public function create()
    {
        $clans = Clan::where('user_id', auth()->id())->get();
        return view('family-rules.create', compact('clans'));
    }

    // 存储新家族规则
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'clan_id' => 'nullable|exists:clans,id,user_id,' . auth()->id()
        ]);

        $validatedData['user_id'] = auth()->id();
        FamilyRule::create($validatedData);

        return redirect()->route('family-rules.index')
                         ->with('success', '家族规则创建成功！');
    }

    // 显示家族规则详情
    public function show(FamilyRule $familyRule)
    {
        $this->authorize('view', $familyRule);
        return view('family-rules.show', compact('familyRule'));
    }

    // 显示编辑家族规则表单
    public function edit(FamilyRule $familyRule)
    {
        $this->authorize('update', $familyRule);
        $clans = Clan::where('user_id', auth()->id())->get();
        return view('family-rules.edit', compact('familyRule', 'clans'));
    }

    // 更新家族规则
    public function update(Request $request, FamilyRule $familyRule)
    {
        $this->authorize('update', $familyRule);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'clan_id' => 'nullable|exists:clans,id,user_id,' . auth()->id()
        ]);

        $familyRule->update($validatedData);

        return redirect()->route('family-rules.index')
                         ->with('success', '家族规则更新成功！');
    }

    // 删除家族规则
    public function destroy(FamilyRule $familyRule)
    {
        $this->authorize('delete', $familyRule);
        $familyRule->delete();

        return redirect()->route('family-rules.index')
                         ->with('success', '家族规则删除成功！');
    }
}

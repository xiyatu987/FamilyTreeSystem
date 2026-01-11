<?php

namespace App\Http\Controllers;

use App\Models\AncestralHall;
use App\Models\Clan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AncestralHallController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示祠堂列表
    public function index()
    {
        $ancestralHalls = AncestralHall::where('user_id', auth()->id())->with('clan')->paginate(15);
        return view('ancestral-halls.index', compact('ancestralHalls'));
    }

    // 显示创建祠堂表单
    public function create()
    {
        $clans = Clan::where('user_id', auth()->id())->get();
        return view('ancestral-halls.create', compact('clans'));
    }

    // 存储新祠堂
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'built_date' => 'nullable|date',
            'description' => 'nullable|string',
            'clan_id' => 'nullable|exists:clans,id,user_id,' . auth()->id()
        ]);

        $validatedData['user_id'] = auth()->id();
        AncestralHall::create($validatedData);

        return redirect()->route('ancestral-halls.index')
                         ->with('success', '祠堂信息创建成功！');
    }

    // 显示祠堂详情
    public function show(AncestralHall $ancestralHall)
    {
        $this->authorize('view', $ancestralHall);
        return view('ancestral-halls.show', compact('ancestralHall'));
    }

    // 显示编辑祠堂表单
    public function edit(AncestralHall $ancestralHall)
    {
        $this->authorize('update', $ancestralHall);
        $clans = Clan::where('user_id', auth()->id())->get();
        return view('ancestral-halls.edit', compact('ancestralHall', 'clans'));
    }

    // 更新祠堂信息
    public function update(Request $request, AncestralHall $ancestralHall)
    {
        $this->authorize('update', $ancestralHall);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'built_date' => 'nullable|date',
            'description' => 'nullable|string',
            'clan_id' => 'nullable|exists:clans,id,user_id,' . auth()->id()
        ]);

        $ancestralHall->update($validatedData);

        return redirect()->route('ancestral-halls.index')
                         ->with('success', '祠堂信息更新成功！');
    }

    // 删除祠堂
    public function destroy(AncestralHall $ancestralHall)
    {
        $this->authorize('delete', $ancestralHall);
        $ancestralHall->delete();

        return redirect()->route('ancestral-halls.index')
                         ->with('success', '祠堂信息删除成功！');
    }
}

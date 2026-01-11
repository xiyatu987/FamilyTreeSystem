<?php

namespace App\Http\Controllers;

use App\Models\Clan;
use Illuminate\Http\Request;

class ClanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示宗族列表
    public function index()
    {
        $clans = Clan::where('user_id', auth()->id())->paginate(15);
        return view('clans.index', compact('clans'));
    }

    // 显示创建宗族表单
    public function create()
    {
        return view('clans.create');
    }

    // 存储新宗族
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'founding_date' => 'nullable|date',
            'ancestral_home' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        $validatedData['user_id'] = auth()->id();
        Clan::create($validatedData);

        return redirect()->route('clans.index')
                         ->with('success', '宗族信息创建成功！');
    }

    // 显示宗族详情
    public function show(Clan $clan)
    {
        $this->authorize('view', $clan);
        return view('clans.show', compact('clan'));
    }

    // 显示编辑宗族表单
    public function edit(Clan $clan)
    {
        $this->authorize('update', $clan);
        return view('clans.edit', compact('clan'));
    }

    // 更新宗族信息
    public function update(Request $request, Clan $clan)
    {
        $this->authorize('update', $clan);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'founding_date' => 'nullable|date',
            'ancestral_home' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        $clan->update($validatedData);

        return redirect()->route('clans.index')
                         ->with('success', '宗族信息更新成功！');
    }

    // 删除宗族
    public function destroy(Clan $clan)
    {
        $this->authorize('delete', $clan);
        $clan->delete();

        return redirect()->route('clans.index')
                         ->with('success', '宗族信息删除成功！');
    }
}

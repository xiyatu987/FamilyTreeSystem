<?php

namespace App\Http\Controllers;

use App\Models\Ziwei;
use Illuminate\Http\Request;

class ZiweiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示字辈列表
    public function index()
    {
        $ziweiList = Ziwei::where('user_id', auth()->id())->orderBy('order')->paginate(15);
        return view('ziwei.index', compact('ziweiList'));
    }

    // 显示创建字辈表单
    public function create()
    {
        return view('ziwei.create');
    }

    // 存储新字辈
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'character' => 'required|string|max:10',
            'order' => 'required|integer',
            'generation' => 'nullable|integer',
            'description' => 'nullable|string'
        ]);

        $validatedData['user_id'] = auth()->id();
        Ziwei::create($validatedData);

        return redirect()->route('ziwei.index')
                         ->with('success', '字辈信息创建成功！');
    }

    // 显示字辈详情
    public function show(Ziwei $ziwei)
    {
        $this->authorize('view', $ziwei);
        return view('ziwei.show', compact('ziwei'));
    }

    // 显示编辑字辈表单
    public function edit(Ziwei $ziwei)
    {
        $this->authorize('update', $ziwei);
        return view('ziwei.edit', compact('ziwei'));
    }

    // 更新字辈信息
    public function update(Request $request, Ziwei $ziwei)
    {
        $this->authorize('update', $ziwei);

        $validatedData = $request->validate([
            'character' => 'required|string|max:10',
            'order' => 'required|integer',
            'generation' => 'nullable|integer',
            'description' => 'nullable|string'
        ]);

        $ziwei->update($validatedData);

        return redirect()->route('ziwei.index')
                         ->with('success', '字辈信息更新成功！');
    }

    // 删除字辈
    public function destroy(Ziwei $ziwei)
    {
        $this->authorize('delete', $ziwei);
        $ziwei->delete();

        return redirect()->route('ziwei.index')
                         ->with('success', '字辈信息删除成功！');
    }
}
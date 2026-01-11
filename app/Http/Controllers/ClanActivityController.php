<?php

namespace App\Http\Controllers;

use App\Models\ClanActivity;
use App\Models\Clan;
use Illuminate\Http\Request;

class ClanActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示宗族活动列表
    public function index()
    {
        $activities = ClanActivity::where('user_id', auth()->id())->with('clan')->paginate(15);
        return view('clan-activities.index', compact('activities'));
    }

    // 显示创建宗族活动表单
    public function create()
    {
        $clans = Clan::where('user_id', auth()->id())->get();
        return view('clan-activities.create', compact('clans'));
    }

    // 存储新宗族活动
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'participants' => 'nullable|string',
            'description' => 'nullable|string',
            'clan_id' => 'required|exists:clans,id,user_id,' . auth()->id()
        ]);

        $validatedData['user_id'] = auth()->id();
        ClanActivity::create($validatedData);

        return redirect()->route('clan-activities.index')
                         ->with('success', '宗族活动创建成功！');
    }

    // 显示宗族活动详情
    public function show(ClanActivity $clanActivity)
    {
        $this->authorize('view', $clanActivity);
        return view('clan-activities.show', compact('clanActivity'));
    }

    // 显示编辑宗族活动表单
    public function edit(ClanActivity $clanActivity)
    {
        $this->authorize('update', $clanActivity);
        $clans = Clan::where('user_id', auth()->id())->get();
        return view('clan-activities.edit', compact('clanActivity', 'clans'));
    }

    // 更新宗族活动
    public function update(Request $request, ClanActivity $clanActivity)
    {
        $this->authorize('update', $clanActivity);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'participants' => 'nullable|string',
            'description' => 'nullable|string',
            'clan_id' => 'required|exists:clans,id,user_id,' . auth()->id()
        ]);

        $clanActivity->update($validatedData);

        return redirect()->route('clan-activities.index')
                         ->with('success', '宗族活动更新成功！');
    }

    // 删除宗族活动
    public function destroy(ClanActivity $clanActivity)
    {
        $this->authorize('delete', $clanActivity);
        $clanActivity->delete();

        return redirect()->route('clan-activities.index')
                         ->with('success', '宗族活动删除成功！');
    }
}

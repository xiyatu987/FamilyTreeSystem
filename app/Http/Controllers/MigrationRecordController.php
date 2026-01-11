<?php

namespace App\Http\Controllers;

use App\Models\MigrationRecord;
use App\Models\FamilyMember;
use Illuminate\Http\Request;

class MigrationRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 显示迁徙记录列表
    public function index()
    {
        $migrationRecords = MigrationRecord::where('user_id', auth()->id())->with('member')->paginate(15);
        return view('migration-records.index', compact('migrationRecords'));
    }

    // 显示创建迁徙记录表单
    public function create()
    {
        $familyMembers = FamilyMember::where('user_id', auth()->id())->get();
        return view('migration-records.create', compact('familyMembers'));
    }

    // 存储新迁徙记录
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'member_id' => 'required|exists:family_members,id,user_id,' . auth()->id(),
            'from_place' => 'required|string|max:255',
            'to_place' => 'required|string|max:255',
            'migration_date' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        $validatedData['user_id'] = auth()->id();
        MigrationRecord::create($validatedData);

        return redirect()->route('migration-records.index')
                         ->with('success', '迁徙记录创建成功！');
    }

    // 显示迁徙记录详情
    public function show(MigrationRecord $migrationRecord)
    {
        $this->authorize('view', $migrationRecord);
        return view('migration-records.show', compact('migrationRecord'));
    }

    // 显示编辑迁徙记录表单
    public function edit(MigrationRecord $migrationRecord)
    {
        $this->authorize('update', $migrationRecord);
        $familyMembers = FamilyMember::where('user_id', auth()->id())->get();
        return view('migration-records.edit', compact('migrationRecord', 'familyMembers'));
    }

    // 更新迁徙记录
    public function update(Request $request, MigrationRecord $migrationRecord)
    {
        $this->authorize('update', $migrationRecord);

        $validatedData = $request->validate([
            'member_id' => 'required|exists:family_members,id,user_id,' . auth()->id(),
            'from_place' => 'required|string|max:255',
            'to_place' => 'required|string|max:255',
            'migration_date' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        $migrationRecord->update($validatedData);

        return redirect()->route('migration-records.index')
                         ->with('success', '迁徙记录更新成功！');
    }

    // 删除迁徙记录
    public function destroy(MigrationRecord $migrationRecord)
    {
        $this->authorize('delete', $migrationRecord);
        $migrationRecord->delete();

        return redirect()->route('migration-records.index')
                         ->with('success', '迁徙记录删除成功！');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    
    // 管理员仪表盘
    public function index()
    {
        $usersCount = User::count();
        $adminUsersCount = User::where('role', 'admin')->count();
        $regularUsersCount = User::where('role', 'user')->count();
        
        return view('admin.dashboard', compact('usersCount', 'adminUsersCount', 'regularUsersCount'));
    }
    
    // 用户管理列表
    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users', compact('users'));
    }
    
    // 切换用户角色
    public function toggleRole(Request $request, User $user)
    {
        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);
        
        return redirect()->route('admin.users')
                         ->with('success', '用户角色已成功切换！');
    }
    
    // 删除用户
    public function deleteUser(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users')
                             ->with('error', '至少需要保留一个管理员用户！');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users')
                         ->with('success', '用户已成功删除！');
    }
}
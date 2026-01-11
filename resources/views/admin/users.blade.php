@extends('layouts.app')

@section('content')
<div class="container">
    <h1>用户管理</h1>
    
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif
    
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>姓名</th>
                <th>邮箱</th>
                <th>角色</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'success' }}">
                            {{ $user->role == 'admin' ? '管理员' : '普通用户' }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <!-- 切换角色按钮 -->
                        <form action="{{ route('admin.toggleRole', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-sm btn-{{ $user->role == 'admin' ? 'warning' : 'primary' }}">
                                {{ $user->role == 'admin' ? '转为普通用户' : '设为管理员' }}
                            </button>
                        </form>
                        
                        <!-- 删除用户按钮 -->
                        <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="d-inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除用户 {{ $user->name }} 吗？');">
                                删除
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- 分页链接 -->
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
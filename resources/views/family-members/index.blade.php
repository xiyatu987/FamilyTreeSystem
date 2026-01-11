@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">家族成员管理</h1>
    
    <!-- 搜索框 -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('family-members.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="搜索家族成员姓名、出生地或去世地" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
                    <button class="btn btn-primary" type="submit">搜索</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 家族成员列表 -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">家族成员列表</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('family-members.create') }}" class="btn btn-success">添加新成员</a>
                <a href="{{ route('family-members.tree') }}" class="btn btn-secondary">查看家族树</a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>性别</th>
                        <th>出生日期</th>
                        <th>去世日期</th>
                        <th>字辈</th>
                        <th>世代</th>
                        <th>父母</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($familyMembers as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->gender_display }}</td>
                            <td>{{ $member->birth_date ? $member->birth_date->format('Y-m-d') : '未知' }}</td>
                            <td>{{ $member->death_date ? $member->death_date->format('Y-m-d') : '在世' }}</td>
                            <td>{{ $member->ziwei ? $member->ziwei->character : '未指定' }}</td>
                            <td>{{ $member->generation ? $member->generation : '未指定' }}</td>
                            <td>
                                @if($member->father || $member->mother)
                                    {{ $member->father ? $member->father->name : '未知' }} & {{ $member->mother ? $member->mother->name : '未知' }}
                                @else
                                    始祖
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('family-members.show', $member) }}" class="btn btn-info btn-sm">详情</a>
                                <a href="{{ route('family-members.edit', $member) }}" class="btn btn-warning btn-sm">编辑</a>
                                <form action="{{ route('family-members.destroy', $member) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('确定要删除该家族成员吗？')">删除</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- 分页 -->
            <div class="mt-3">
                {{ $familyMembers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
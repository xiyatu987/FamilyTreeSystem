@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">家族规则管理</h1>
    
    <div class="mb-3">
        <a href="{{ route('family-rules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加新规则
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>标题</th>
                <th>类别</th>
                <th>所属宗族</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($familyRules as $rule)
            <tr>
                <td>{{ $rule->title }}</td>
                <td>{{ $rule->category }}</td>
                <td>{{ $rule->clan ? $rule->clan->name : '无' }}</td>
                <td>
                    <a href="{{ route('family-rules.show', $rule) }}" class="btn btn-sm btn-info">查看</a>
                    <a href="{{ route('family-rules.edit', $rule) }}" class="btn btn-sm btn-warning">编辑</a>
                    <form action="{{ route('family-rules.destroy', $rule) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这条家族规则吗？')">删除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $familyRules->links() }}
</div>
@endsection

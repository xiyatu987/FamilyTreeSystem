@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">祠堂信息管理</h1>
    
    <div class="mb-3">
        <a href="{{ route('ancestral-halls.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加新祠堂
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>名称</th>
                <th>位置</th>
                <th>建造日期</th>
                <th>所属宗族</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ancestralHalls as $hall)
            <tr>
                <td>{{ $hall->name }}</td>
                <td>{{ $hall->location }}</td>
                <td>{{ $hall->built_date }}</td>
                <td>{{ $hall->clan ? $hall->clan->name : '无' }}</td>
                <td>
                    <a href="{{ route('ancestral-halls.show', $hall) }}" class="btn btn-sm btn-info">查看</a>
                    <a href="{{ route('ancestral-halls.edit', $hall) }}" class="btn btn-sm btn-warning">编辑</a>
                    <form action="{{ route('ancestral-halls.destroy', $hall) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这条祠堂信息吗？')">删除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $ancestralHalls->links() }}
</div>
@endsection

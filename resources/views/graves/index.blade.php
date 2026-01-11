@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">墓地信息管理</h1>
    
    <div class="mb-3">
        <a href="{{ route('graves.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加新墓地
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>姓名</th>
                <th>位置</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($graves as $grave)
            <tr>
                <td>{{ $grave->member->name }}</td>
                <td>{{ $grave->location }}</td>
                <td>
                    <a href="{{ route('graves.show', $grave) }}" class="btn btn-sm btn-info">查看</a>
                    <a href="{{ route('graves.edit', $grave) }}" class="btn btn-sm btn-warning">编辑</a>
                    <form action="{{ route('graves.destroy', $grave) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这条墓地信息吗？')">删除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $graves->links() }}
</div>
@endsection

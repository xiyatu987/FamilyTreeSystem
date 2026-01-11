@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">字辈管理</h1>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">字辈列表</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('ziwei.create') }}" class="btn btn-success">添加新字辈</a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>顺序</th>
                        <th>字辈</th>
                        <th>世代</th>
                        <th>描述</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ziweiList as $ziwei)
                        <tr>
                            <td>{{ $ziwei->order }}</td>
                            <td>{{ $ziwei->character }}</td>
                            <td>{{ $ziwei->generation ? $ziwei->generation : '未指定' }}</td>
                            <td>{{ $ziwei->description ? substr($ziwei->description, 0, 30) . '...' : '无' }}</td>
                            <td>
                                <a href="{{ route('ziwei.show', $ziwei) }}" class="btn btn-info btn-sm">详情</a>
                                <a href="{{ route('ziwei.edit', $ziwei) }}" class="btn btn-warning btn-sm">编辑</a>
                                <form action="{{ route('ziwei.destroy', $ziwei) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('确定要删除该字辈吗？')">删除</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- 分页 -->
            <div class="mt-3">
                {{ $ziweiList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
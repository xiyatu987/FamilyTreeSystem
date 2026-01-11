@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">宗族活动管理</h1>
    
    <div class="mb-3">
        <a href="{{ route('clan-activities.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加新活动
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>标题</th>
                <th>日期</th>
                <th>地点</th>
                <th>所属宗族</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>{{ $activity->title }}</td>
                <td>{{ $activity->date }}</td>
                <td>{{ $activity->location }}</td>
                <td>{{ $activity->clan->name }}</td>
                <td>
                    <a href="{{ route('clan-activities.show', $activity) }}" class="btn btn-sm btn-info">查看</a>
                    <a href="{{ route('clan-activities.edit', $activity) }}" class="btn btn-sm btn-warning">编辑</a>
                    <form action="{{ route('clan-activities.destroy', $activity) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这条活动记录吗？')">删除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $activities->links() }}
</div>
@endsection

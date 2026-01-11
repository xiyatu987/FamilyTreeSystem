@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">迁徙记录管理</h1>
    
    <div class="mb-3">
        <a href="{{ route('migration-records.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加新记录
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>姓名</th>
                <th>迁出地</th>
                <th>迁入地</th>
                <th>迁徙日期</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($migrationRecords as $record)
            <tr>
                <td>{{ $record->member->name }}</td>
                <td>{{ $record->from_place }}</td>
                <td>{{ $record->to_place }}</td>
                <td>{{ $record->migration_date }}</td>
                <td>
                    <a href="{{ route('migration-records.show', $record) }}" class="btn btn-sm btn-info">查看</a>
                    <a href="{{ route('migration-records.edit', $record) }}" class="btn btn-sm btn-warning">编辑</a>
                    <form action="{{ route('migration-records.destroy', $record) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这条迁徙记录吗？')">删除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $migrationRecords->links() }}
</div>
@endsection

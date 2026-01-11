@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">添加新迁徙记录</h1>
    
    <form action="{{ route('migration-records.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="member_id">家族成员</label>
            <select name="member_id" class="form-control" id="member_id" required>
                <option value="">选择成员</option>
                @foreach($members as $member)
                <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="from_place">迁出地</label>
            <input type="text" name="from_place" class="form-control" id="from_place" value="{{ old('from_place') }}" required>
        </div>
        
        <div class="form-group">
            <label for="to_place">迁入地</label>
            <input type="text" name="to_place" class="form-control" id="to_place" value="{{ old('to_place') }}" required>
        </div>
        
        <div class="form-group">
            <label for="migration_date">迁徙日期</label>
            <input type="date" name="migration_date" class="form-control" id="migration_date" value="{{ old('migration_date') }}">
        </div>
        
        <div class="form-group">
            <label for="reason">迁徙原因</label>
            <textarea name="reason" class="form-control" id="reason" rows="3">{{ old('reason') }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="description">详细描述</label>
            <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">保存</button>
        <a href="{{ route('migration-records.index') }}" class="btn btn-secondary">取消</a>
    </form>
</div>
@endsection

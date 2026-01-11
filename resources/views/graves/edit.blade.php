@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">编辑墓地信息</h1>
    
    <form action="{{ route('graves.update', $grave) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="member_id">家族成员</label>
            <select name="member_id" class="form-control" id="member_id" required>
                <option value="">选择成员</option>
                @foreach($members as $member)
                <option value="{{ $member->id }}" {{ $grave->member_id == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="location">墓地位置</label>
            <input type="text" name="location" class="form-control" id="location" value="{{ $grave->location }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">详细描述</label>
            <textarea name="description" class="form-control" id="description" rows="5">{{ $grave->description }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">保存</button>
        <a href="{{ route('graves.index') }}" class="btn btn-secondary">取消</a>
    </form>
</div>
@endsection

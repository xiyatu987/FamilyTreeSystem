@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">添加新家族规则</h1>
    
    <form action="{{ route('family-rules.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" required>
        </div>
        
        <div class="form-group">
            <label for="category">类别</label>
            <input type="text" name="category" class="form-control" id="category" value="{{ old('category') }}">
        </div>
        
        <div class="form-group">
            <label for="clan_id">所属宗族</label>
            <select name="clan_id" class="form-control" id="clan_id">
                <option value="">选择宗族</option>
                @foreach($clans as $clan)
                <option value="{{ $clan->id }}">{{ $clan->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="content">内容</label>
            <textarea name="content" class="form-control" id="content" rows="10" required>{{ old('content') }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">保存</button>
        <a href="{{ route('family-rules.index') }}" class="btn btn-secondary">取消</a>
    </form>
</div>
@endsection

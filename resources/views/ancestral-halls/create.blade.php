@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">添加新祠堂信息</h1>
    
    <form action="{{ route('ancestral-halls.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">祠堂名称</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
        </div>
        
        <div class="form-group">
            <label for="location">位置</label>
            <input type="text" name="location" class="form-control" id="location" value="{{ old('location') }}" required>
        </div>
        
        <div class="form-group">
            <label for="built_date">建造日期</label>
            <input type="date" name="built_date" class="form-control" id="built_date" value="{{ old('built_date') }}">
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
            <label for="description">描述</label>
            <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">保存</button>
        <a href="{{ route('ancestral-halls.index') }}" class="btn btn-secondary">取消</a>
    </form>
</div>
@endsection

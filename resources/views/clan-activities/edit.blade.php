@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">编辑宗族活动</h1>
    
    <form action="{{ route('clan-activities.update', $clanActivity) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">活动标题</label>
            <input type="text" name="title" class="form-control" id="title" value="{{ $clanActivity->title }}" required>
        </div>
        
        <div class="form-group">
            <label for="date">活动日期</label>
            <input type="date" name="date" class="form-control" id="date" value="{{ $clanActivity->date }}" required>
        </div>
        
        <div class="form-group">
            <label for="location">活动地点</label>
            <input type="text" name="location" class="form-control" id="location" value="{{ $clanActivity->location }}" required>
        </div>
        
        <div class="form-group">
            <label for="clan_id">所属宗族</label>
            <select name="clan_id" class="form-control" id="clan_id" required>
                <option value="">选择宗族</option>
                @foreach($clans as $clan)
                <option value="{{ $clan->id }}" {{ $clanActivity->clan_id == $clan->id ? 'selected' : '' }}>{{ $clan->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="participants">参与者</label>
            <textarea name="participants" class="form-control" id="participants" rows="3">{{ $clanActivity->participants }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="description">活动描述</label>
            <textarea name="description" class="form-control" id="description" rows="5">{{ $clanActivity->description }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">保存</button>
        <a href="{{ route('clan-activities.index') }}" class="btn btn-secondary">取消</a>
    </form>
</div>
@endsection

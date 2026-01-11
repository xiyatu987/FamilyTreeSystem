@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $familyRule->title }}</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            家族规则详情
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-2">类别：</div>
                <div class="col-sm-10">{{ $familyRule->category }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">所属宗族：</div>
                <div class="col-sm-10">{{ $familyRule->clan ? $familyRule->clan->name : '无' }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">内容：</div>
                <div class="col-sm-10">{{ $familyRule->content }}</div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('family-rules.edit', $familyRule) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('family-rules.index') }}" class="btn btn-secondary">返回列表</a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $grave->member->name }} - 墓地信息</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            墓地信息详情
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-2">家族成员：</div>
                <div class="col-sm-10">{{ $grave->member->name }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">墓地位置：</div>
                <div class="col-sm-10">{{ $grave->location }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">详细描述：</div>
                <div class="col-sm-10">{{ $grave->description }}</div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('graves.edit', $grave) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('graves.index') }}" class="btn btn-secondary">返回列表</a>
    </div>
</div>
@endsection

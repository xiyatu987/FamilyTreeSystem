@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $ancestralHall->name }}</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            祠堂信息详情
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-2">位置：</div>
                <div class="col-sm-10">{{ $ancestralHall->location }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">建造日期：</div>
                <div class="col-sm-10">{{ $ancestralHall->built_date }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">所属宗族：</div>
                <div class="col-sm-10">{{ $ancestralHall->clan ? $ancestralHall->clan->name : '无' }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">描述：</div>
                <div class="col-sm-10">{{ $ancestralHall->description }}</div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('ancestral-halls.edit', $ancestralHall) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('ancestral-halls.index') }}" class="btn btn-secondary">返回列表</a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $clanActivity->title }}</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            宗族活动详情
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-2">日期：</div>
                <div class="col-sm-10">{{ $clanActivity->date }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">地点：</div>
                <div class="col-sm-10">{{ $clanActivity->location }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">所属宗族：</div>
                <div class="col-sm-10">{{ $clanActivity->clan->name }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">参与者：</div>
                <div class="col-sm-10">{{ $clanActivity->participants }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">活动描述：</div>
                <div class="col-sm-10">{{ $clanActivity->description }}</div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('clan-activities.edit', $clanActivity) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('clan-activities.index') }}" class="btn btn-secondary">返回列表</a>
    </div>
</div>
@endsection

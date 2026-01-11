@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">字辈详情</h1>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">字辈信息</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>字辈：</strong> {{ $ziwei->character }}
                </div>
                <div class="col-md-6">
                    <strong>顺序：</strong> {{ $ziwei->order }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>世代：</strong> {{ $ziwei->generation ? $ziwei->generation : '未指定' }}
                </div>
            </div>
            
            <div class="mb-3">
                <strong>描述：</strong>
                <p>{{ $ziwei->description ? $ziwei->description : '无' }}</p>
            </div>
            
            <div class="mb-3">
                <strong>使用该字辈的家族成员：</strong>
                @if($ziwei->familyMembers->count() > 0)
                    <ul class="list-group">
                        @foreach($ziwei->familyMembers as $member)
                            <li class="list-group-item">
                                <a href="{{ route('family-members.show', $member) }}">{{ $member->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">暂无家族成员使用该字辈</p>
                @endif
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('ziwei.index') }}" class="btn btn-secondary">返回列表</a>
                <div>
                    <a href="{{ route('ziwei.edit', $ziwei) }}" class="btn btn-warning">编辑</a>
                    <form action="{{ route('ziwei.destroy', $ziwei) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('确定要删除该字辈吗？')">删除</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

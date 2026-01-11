@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $migrationRecord->member->name }} - 迁徙记录</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            迁徙记录详情
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-sm-2">家族成员：</div>
                <div class="col-sm-10">{{ $migrationRecord->member->name }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">迁出地：</div>
                <div class="col-sm-10">{{ $migrationRecord->from_place }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">迁入地：</div>
                <div class="col-sm-10">{{ $migrationRecord->to_place }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">迁徙日期：</div>
                <div class="col-sm-10">{{ $migrationRecord->migration_date }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">迁徙原因：</div>
                <div class="col-sm-10">{{ $migrationRecord->reason }}</div>
            </div>
            
            <div class="row mb-3">
                <div class="col-sm-2">详细描述：</div>
                <div class="col-sm-10">{{ $migrationRecord->description }}</div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('migration-records.edit', $migrationRecord) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('migration-records.index') }}" class="btn btn-secondary">返回列表</a>
    </div>
</div>
@endsection

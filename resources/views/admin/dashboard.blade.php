@extends('layouts.app')

@section('content')
<div class="container">
    <h1>管理员仪表盘</h1>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">总用户数</h5>
                    <h2 class="card-text">{{ $usersCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">管理员用户</h5>
                    <h2 class="card-text">{{ $adminUsersCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">普通用户</h5>
                    <h2 class="card-text">{{ $regularUsersCount }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <h2>系统概览</h2>
        <p>欢迎使用中国族谱管理系统管理员后台。在这里，您可以管理系统用户，查看系统统计数据。</p>
    </div>
</div>
@endsection
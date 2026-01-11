@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">{{ $familyMember->name }} - 详细信息</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">基本信息</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>姓名:</strong> {{ $familyMember->name }}
                    </div>
                    <div class="mb-2">
                        <strong>性别:</strong> {{ $familyMember->gender_display }}
                    </div>
                    <div class="mb-2">
                        <strong>出生日期:</strong> {{ $familyMember->birth_date ? $familyMember->birth_date->format('Y-m-d') : '未知' }}
                    </div>
                    <div class="mb-2">
                        <strong>去世日期:</strong> {{ $familyMember->death_date ? $familyMember->death_date->format('Y-m-d') : '在世' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>出生地:</strong> {{ $familyMember->birth_place ?: '未知' }}
                    </div>
                    <div class="mb-2">
                        <strong>去世地:</strong> {{ $familyMember->death_place ?: '未知' }}
                    </div>
                    <div class="mb-2">
                        <strong>字辈:</strong> {{ $familyMember->ziwei ? $familyMember->ziwei->character : '未指定' }}
                    </div>
                    <div class="mb-2">
                        <strong>世代:</strong> {{ $familyMember->generation ?: '未指定' }}
                    </div>
                </div>
            </div>
            
            @if($familyMember->description)
                <div class="mt-3">
                    <strong>备注信息:</strong>
                    <p>{{ $familyMember->description }}</p>
                </div>
            @endif
            
            <div class="mt-3">
                <a href="{{ route('family-members.edit', $familyMember) }}" class="btn btn-warning">编辑</a>
                <a href="{{ route('family-members.index') }}" class="btn btn-secondary">返回列表</a>
            </div>
        </div>
    </div>
    
    <!-- 亲属关系 -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">亲属关系</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>父母</h6>
                        <div class="ml-3">
                            @if($familyMember->father)
                                <div><strong>父亲:</strong> <a href="{{ route('family-members.show', $familyMember->father) }}">{{ $familyMember->father->name }}</a></div>
                            @else
                                <div><strong>父亲:</strong> 未知</div>
                            @endif
                            @if($familyMember->mother)
                                <div><strong>母亲:</strong> <a href="{{ route('family-members.show', $familyMember->mother) }}">{{ $familyMember->mother->name }}</a></div>
                            @else
                                <div><strong>母亲:</strong> 未知</div>
                            @endif
                        </div>
                    </div>
                    
                    @if($familyMember->spouse)
                        <div class="mb-3">
                            <h6>配偶</h6>
                            <div class="ml-3">
                                <a href="{{ route('family-members.show', $familyMember->spouse) }}">{{ $familyMember->spouse->name }}</a>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>子女</h6>
                        <div class="ml-3">
                            @if($familyMember->children->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach($familyMember->children as $child)
                                        <li>
                                            <a href="{{ route('family-members.show', $child) }}">{{ $child->name }}</a> ({{ $child->gender_display }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>无子女记录</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 扩展亲属关系 -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>兄弟姐妹</h6>
                        <div class="ml-3">
                            @php
                                $siblings = array_filter($relatives, function($relative) {
                                    return in_array($relative['relationship'], ['兄弟', '姐妹']);
                                });
                            @endphp
                            @if(count($siblings) > 0)
                                <ul class="list-unstyled">
                                    @foreach($siblings as $sibling)
                                        <li>
                                            <a href="{{ route('family-members.show', $sibling['member']) }}">{{ $sibling['member']->name }}</a> ({{ $sibling['relationship'] }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>无兄弟姐妹记录</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>祖父母</h6>
                        <div class="ml-3">
                            @php
                                $grandparents = array_filter($relatives, function($relative) {
                                    return in_array($relative['relationship'], ['祖父', '祖母', '外祖父', '外祖母']);
                                });
                            @endphp
                            @if(count($grandparents) > 0)
                                <ul class="list-unstyled">
                                    @foreach($grandparents as $grandparent)
                                        <li>
                                            <a href="{{ route('family-members.show', $grandparent['member']) }}">{{ $grandparent['member']->name }}</a> ({{ $grandparent['relationship'] }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>无祖父母记录</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>孙子女</h6>
                        <div class="ml-3">
                            @php
                                $grandchildren = array_filter($relatives, function($relative) {
                                    return in_array($relative['relationship'], ['孙子', '孙女']);
                                });
                            @endphp
                            @if(count($grandchildren) > 0)
                                <ul class="list-unstyled">
                                    @foreach($grandchildren as $grandchild)
                                        <li>
                                            <a href="{{ route('family-members.show', $grandchild['member']) }}">{{ $grandchild['member']->name }}</a> ({{ $grandchild['relationship'] }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>无孙子女记录</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 相关记录 -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">相关记录</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>迁徙记录</h6>
                        <div class="ml-3">
                            @if($familyMember->migrations->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach($familyMember->migrations as $migration)
                                        <li>
                                            {{ $migration->from_place }} → {{ $migration->to_place }} ({{ $migration->year ?: '年份未知' }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>无迁徙记录</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6>墓地信息</h6>
                        <div class="ml-3">
                            @if($familyMember->grave)
                                <div>{{ $familyMember->grave->location }}</div>
                                <div class="text-sm text-muted">{{ $familyMember->grave->description }}</div>
                            @else
                                <p>无墓地记录</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
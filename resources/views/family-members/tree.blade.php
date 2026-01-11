@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">家族树</h1>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">家族谱系</h5>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-md-8">
                    <a href="{{ route('family-members.index') }}" class="btn btn-secondary">返回成员列表</a>
                    <button class="btn btn-info" onclick="toggleTreeControls()">显示控制</button>
                    <button class="btn btn-success" onclick="refreshTree()">刷新家族树</button>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="member-search" class="form-control" placeholder="搜索家族成员...">
                        <button class="btn btn-primary" onclick="searchMember()">搜索</button>
                    </div>
                </div>
            </div>
            
            <div id="tree-controls" style="display: none;" class="mb-3 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label for="zoom" class="form-label">缩放比例</label>
                        <input type="range" id="zoom" min="50" max="200" value="100" class="form-range">
                    </div>
                    <div class="col-md-4">
                        <label for="layout" class="form-label">布局方向</label>
                        <select id="layout" class="form-select">
                            <option value="vertical">从上到下</option>
                            <option value="horizontal">从左到右</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="resetTree()">重置视图</button>
                    </div>
                </div>
            </div>
            
            <div id="family-tree-container" class="overflow-auto" style="min-height: 600px; border: 1px solid #ddd;">
                <div id="family-tree" style="zoom: 1;">
                    @forelse($rootMembers as $rootMember)
                        <div class="tree-node-container">
                            @include('family-members.partials.tree-node', ['member' => $rootMember, 'level' => 0])
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-muted">暂无家族成员记录，请先添加成员</p>
                            <a href="{{ route('family-members.create') }}" class="btn btn-success">添加第一个成员</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tree-node {
        display: inline-block;
        padding: 10px;
        margin: 5px;
        border: 2px solid #3498db;
        border-radius: 8px;
        background-color: white;
        min-width: 120px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .tree-node:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transform: translateY(-2px);
    }
    
    .tree-node.male {
        border-color: #3498db;
    }
    
    .tree-node.female {
        border-color: #e74c3c;
    }
    
    .tree-node.other {
        border-color: #95a5a6;
    }
    
    .tree-node .name {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    
    .tree-node .details {
        font-size: 0.8em;
        color: #666;
    }
    
    .tree-children {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .tree-child {
        margin: 0 10px;
    }
    
    .tree-line {
        height: 20px;
        width: 2px;
        background-color: #ddd;
        margin: 0 auto;
    }
    
    .tree-branch {
        height: 2px;
        background-color: #ddd;
        width: 100%;
        margin: 10px 0;
    }
</style>

<script>
    function toggleTreeControls() {
        const controls = document.getElementById('tree-controls');
        controls.style.display = controls.style.display === 'none' ? 'block' : 'none';
    }
    
    function resetTree() {
        const tree = document.getElementById('family-tree');
        tree.style.zoom = '1';
        document.getElementById('zoom').value = 100;
    }
    
    document.getElementById('zoom').addEventListener('input', function(e) {
        const tree = document.getElementById('family-tree');
        tree.style.zoom = e.target.value + '%';
    });
    
    document.getElementById('layout').addEventListener('change', function(e) {
        const layout = e.target.value;
        const tree = document.getElementById('family-tree');
        
        if (layout === 'vertical') {
            tree.style.flexDirection = 'column';
        } else {
            tree.style.flexDirection = 'row';
        }
    });
    
    // 节点点击事件
    document.addEventListener('DOMContentLoaded', function() {
        const nodes = document.querySelectorAll('.tree-node');
        nodes.forEach(node => {
            node.addEventListener('click', function() {
                const memberId = this.dataset.memberId;
                if (memberId) {
                    window.location.href = '{{ url('family-members') }}/' + memberId;
                }
            });
        });
    });
    
    // 搜索成员功能
    function searchMember() {
        const searchTerm = document.getElementById('member-search').value.toLowerCase();
        const nodes = document.querySelectorAll('.tree-node');
        let found = false;
        
        nodes.forEach(node => {
            const name = node.querySelector('.name').textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                node.style.backgroundColor = '#ffffcc';
                node.scrollIntoView({ behavior: 'smooth', block: 'center' });
                found = true;
            } else {
                node.style.backgroundColor = 'white';
            }
        });
        
        if (!found && searchTerm) {
            alert('未找到匹配的家族成员');
        }
    }
    
    // 刷新家族树功能
    function refreshTree() {
        window.location.reload();
    }
    
    // 监听搜索框的回车键
    document.getElementById('member-search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchMember();
        }
    });
</script>
@endsection
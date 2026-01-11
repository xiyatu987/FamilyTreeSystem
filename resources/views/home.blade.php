<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中国族谱管理系统 - 首页</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 自定义CSS -->
    <link href="{{ asset('css/chinese_style.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-red">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/home') }}">
                <i class="fa fa-home"></i> 中国族谱管理系统
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- 左侧导航 -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-users"></i> 家族管理
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('family-members.index') }}">家族成员</a></li>
                            <li><a class="dropdown-item" href="{{ route('ziwei.index') }}">字辈管理</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('clans.index') }}">宗族组织结构</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-history"></i> 历史记录
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item" href="{{ route('migration-records.index') }}">迁徙记录</a></li>
                            <li><a class="dropdown-item" href="{{ route('ancestral-halls.index') }}">祠堂信息</a></li>
                            <li><a class="dropdown-item" href="{{ route('graves.index') }}">墓地信息</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown3" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-book"></i> 家族文化
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown3">
                            <li><a class="dropdown-item" href="{{ route('family-rules.index') }}">家规家训</a></li>
                            <li><a class="dropdown-item" href="{{ route('clan-activities.index') }}">宗族活动</a></li>
                        </ul>
                    </li>
                    
                    @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-cog"></i> 系统管理
                        </a>
                    </li>
                    @endif
                </ul>
                
                <!-- 右侧用户菜单 -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a id="navbarDropdownUser" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                            <a class="dropdown-item" href="#">
                                <i class="fa fa-user"></i> 个人资料
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fa fa-cog"></i> 个人设置
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> 退出登录
                            </a>
                            
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 主内容区 -->
    <main class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-red text-white">
                            <h4><i class="fa fa-home"></i> 欢迎使用中国族谱管理系统</h4>
                        </div>
                        <div class="card-body">
                            <p>您好，{{ Auth::user()->name }}！欢迎登录中国族谱管理系统。</p>
                            <p>在这里，您可以管理家族成员信息、字辈、祠堂、迁徙记录等内容，传承家族文化。</p>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-red text-white">
                            <h4><i class="fa fa-list"></i> 最新动态</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="fa fa-user-plus text-green"></i> 张三 于 2025-12-16 15:30 添加了新成员李四
                                </li>
                                <li class="list-group-item">
                                    <i class="fa fa-pencil text-blue"></i> 王五 于 2025-12-15 10:15 更新了家族字辈
                                </li>
                                <li class="list-group-item">
                                    <i class="fa fa-calendar text-red"></i> 宗族活动：2025年12月20日 祭祀活动
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-red text-white">
                            <h4><i class="fa fa-chart-pie"></i> 数据统计</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 text-center">
                                    <div class="stat-value">1,234</div>
                                    <div class="stat-label">家族成员</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="stat-value">56</div>
                                    <div class="stat-label">字辈</div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-6 text-center">
                                    <div class="stat-value">8</div>
                                    <div class="stat-label">祠堂</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="stat-value">156</div>
                                    <div class="stat-label">迁徙记录</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-red text-white">
                            <h4><i class="fa fa-bell"></i> 系统通知</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <span class="badge bg-danger">新</span> 系统将于2025-12-18进行维护
                                </li>
                                <li class="list-group-item">
                                    <span class="badge bg-info">提示</span> 请及时更新家族成员信息
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2025 中国族谱管理系统. 保留所有权利.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
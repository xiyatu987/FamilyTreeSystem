<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中国族谱管理系统</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 自定义CSS -->
    <link href="{{ asset('css/chinese_style.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-red text-white py-5">
        <div class="container text-center">
            <h1 class="display-4">中国族谱管理系统</h1>
            <p class="lead">传承家族文化，记录血脉亲情</p>
            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg mx-2">
                    <i class="fa fa-sign-in"></i> 登录
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg mx-2">
                    <i class="fa fa-user-plus"></i> 注册
                </a>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-red text-white">
                            <i class="fa fa-users fa-2x"></i>
                            <h4 class="card-title">家族成员管理</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">详细记录家族成员信息，包括基本资料、亲属关系、生活经历等。</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-red text-white">
                            <i class="fa fa-tree fa-2x"></i>
                            <h4 class="card-title">字辈管理</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">管理家族字辈，自动分配字辈，可视化展示字辈谱系。</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-red text-white">
                            <i class="fa fa-map-marker fa-2x"></i>
                            <h4 class="card-title">迁徙记录</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">记录家族迁徙历史，可视化展示迁徙路线和时间线。</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-red text-white">
                            <i class="fa fa-building fa-2x"></i>
                            <h4 class="card-title">祠堂管理</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">管理祠堂信息，记录祠堂历史、活动和现状。</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-red text-white">
                            <i class="fa fa-book fa-2x"></i>
                            <h4 class="card-title">家规家训</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">记录和传承家族规矩、家训和价值观。</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-red text-white">
                            <i class="fa fa-calendar fa-2x"></i>
                            <h4 class="card-title">宗族活动</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">记录宗族活动，包括祭祀、聚会、庆典等。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2025 中国族谱管理系统. 保留所有权利.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
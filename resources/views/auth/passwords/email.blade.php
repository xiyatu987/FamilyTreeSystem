<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中国族谱管理系统 - 重置密码</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 自定义CSS -->
    <link href="{{ asset('css/chinese_style.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-red text-white text-center">
                        <h3><i class="fa fa-key"></i> 重置密码</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-center mb-4">请输入您的邮箱地址，我们将发送密码重置链接。</p>
                        
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            
                            <div class="form-group mb-4">
                                <label for="email" class="form-label"><i class="fa fa-envelope"></i> 邮箱</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="form-group mb-0 text-center">
                                <button type="submit" class="btn btn-red btn-lg w-100">
                                    <i class="fa fa-paper-plane"></i> 发送密码重置链接
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}" class="text-red">
                                <i class="fa fa-sign-in"></i> 返回登录
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
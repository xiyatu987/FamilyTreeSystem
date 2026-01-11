<?php

// 自动加载注册函数
function register_autoloader($prefix, $base_dir) {
    spl_autoload_register(function ($class) use ($prefix, $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    });
}

// 注册自动加载器
register_autoloader('App\\', __DIR__ . '/../app/');
register_autoloader('Database\\Factories\\', __DIR__ . '/../database/factories/');
register_autoloader('Database\\Seeders\\', __DIR__ . '/../database/seeders/');

// 模拟Laravel框架的一些基础类
require_once __DIR__ . '/../app/Http/Kernel.php';
require_once __DIR__ . '/../app/Console/Kernel.php';
require_once __DIR__ . '/../app/Exceptions/Handler.php';

// 加载必要的服务
require __DIR__ . '/../app/Services/KinshipService.php';

// 加载迁移文件
$migrations_path = __DIR__ . '/../database/migrations/';
$migration_files = glob($migrations_path . '*.php');
foreach ($migration_files as $file) {
    require_once $file;
}

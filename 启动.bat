@echo off

REM 族谱管理系统启动脚本
REM 版本: 1.0
REM 功能: 安装依赖、运行迁移、启动开发服务器

cls
echo ========================================
echo 中国族谱管理系统 - 启动脚本
echo ========================================
echo.

REM 检查PHP是否安装
echo [1/5] 检查PHP环境...
php --version >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo 错误: PHP未安装或未添加到系统PATH
    echo 请先安装PHP 8.0+并添加到系统环境变量
    pause
    exit /b 1
)
php --version | findstr /i "PHP 8."
if %ERRORLEVEL% neq 0 (
    echo 警告: 建议使用PHP 8.0及以上版本
)

echo [✓] PHP环境检查通过

REM 检查Composer是否安装
echo [2/5] 检查Composer...
composer --version >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo 错误: Composer未安装或未添加到系统PATH
    echo 请先安装Composer并添加到系统环境变量
    pause
    exit /b 1
)
echo [✓] Composer检查通过

REM 安装PHP依赖
echo [3/5] 安装PHP依赖...
if not exist "vendor" (
    composer install --no-interaction
    if %ERRORLEVEL% neq 0 (
        echo 错误: PHP依赖安装失败
        pause
        exit /b 1
    )
    echo [✓] PHP依赖安装完成
) else (
    echo [✓] PHP依赖已存在，跳过安装
)

REM 安装前端依赖
echo [4/5] 安装前端依赖...
if not exist "node_modules" (
    npm install
    if %ERRORLEVEL% neq 0 (
        echo 警告: 前端依赖安装失败，部分功能可能无法正常使用
    ) else (
        echo [✓] 前端依赖安装完成
    )
) else (
    echo [✓] 前端依赖已存在，跳过安装
)

REM 运行数据库迁移
echo [5/5] 运行数据库迁移...
php artisan migrate
if %ERRORLEVEL% neq 0 (
    echo 错误: 数据库迁移失败
    echo 请检查.env文件中的数据库配置是否正确
    pause
    exit /b 1
)
echo [✓] 数据库迁移完成

REM 启动开发服务器
echo.
echo ========================================
echo 系统启动中...
echo 访问地址: http://localhost:8000
echo ========================================
echo.
echo 按 Ctrl+C 停止服务器
echo.

php artisan serve

pause

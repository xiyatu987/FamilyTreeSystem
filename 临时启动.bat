@echo off

REM 自定义启动脚本 - 使用指定的PHP路径
REM PHP路径: C:\Users\54143\Downloads\webtrees-2.2.41\php.exe

set PHP_PATH=C:\Users\54143\Downloads\webtrees-2.2.41\php.exe
set COMPOSER_PATH=%cd%\composer.phar

cls
echo ========================================
echo 中国族谱管理系统 - 自定义启动脚本
echo ========================================
echo.

REM 检查PHP版本
echo [1/5] 检查PHP环境...
%PHP_PATH% --version
if %ERRORLEVEL% neq 0 (
    echo 错误: PHP执行失败
    pause
    exit /b 1
)
echo [✓] PHP环境检查通过

REM 安装PHP依赖
echo [2/5] 安装PHP依赖...
if not exist "vendor" (
    echo 正在安装依赖...
    %PHP_PATH% %COMPOSER_PATH% install --ignore-platform-reqs --no-interaction
    if %ERRORLEVEL% neq 0 (
        echo 警告: PHP依赖安装失败，尝试手动复制vendor目录
    ) else (
        echo [✓] PHP依赖安装完成
    )
) else (
    echo [✓] PHP依赖已存在，跳过安装
)

REM 安装前端依赖
echo [3/5] 安装前端依赖...
if not exist "node_modules" (
    echo 正在安装前端依赖...
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
echo [4/5] 运行数据库迁移...
if exist "artisan" (
    %PHP_PATH% artisan migrate
    if %ERRORLEVEL% neq 0 (
        echo 警告: 数据库迁移失败，请检查.env文件配置
    ) else (
        echo [✓] 数据库迁移完成
    )
) else (
    echo 警告: 未找到artisan文件，跳过数据库迁移
)

REM 启动开发服务器
echo [5/5] 启动开发服务器...
echo.
echo ========================================
echo 系统启动中...
echo 访问地址: http://localhost:8000
echo ========================================
echo.
echo 按 Ctrl+C 停止服务器
echo.

if exist "artisan" (
    %PHP_PATH% artisan serve
) else (
    echo 错误: 未找到artisan文件，无法启动服务器
    echo 请确保PHP依赖安装成功
    pause
    exit /b 1
)

pause
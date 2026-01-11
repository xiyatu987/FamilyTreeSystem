@echo off
echo 正在启动中国族谱管理系统...
echo.

rem 检查PHP是否可用
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo 错误：未找到PHP环境变量。请确保PHP已正确安装并添加到系统路径中。
    echo.
    echo 尝试使用系统中可能存在的PHP...
    goto find_php
)

:start_server
echo 启动PHP内置服务器...
echo 访问地址：http://localhost:8000
echo 按 Ctrl+C 停止服务器
echo.
php -S localhost:8000 -t public

:find_php
rem 尝试在常见位置查找PHP
set php_paths=("C:\PHP\php.exe" "C:\xampp\php\php.exe" "C:\wamp\bin\php\php*\php.exe" "C:\MAMP\bin\php\php*\php.exe")

for %%p in %php_paths% do (
    if exist %%p (
        echo 找到PHP：%%p
        echo 启动PHP内置服务器...
        echo 访问地址：http://localhost:8000
        echo 按 Ctrl+C 停止服务器
        echo.
        "%%p" -S localhost:8000 -t public
        goto end
    )
)

echo 错误：未找到PHP可执行文件。
echo 请手动安装PHP并添加到系统路径中。
echo.
pause

:end
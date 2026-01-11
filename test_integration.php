<?php
/**
 * 中国族谱管理系统集成测试脚本
 * 用于测试各个模块的功能是否正常工作
 */

// 模拟webtrees环境
require_once __DIR__ . '/vendor/autoload.php';

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 定义测试结果数组
$test_results = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0,
    'errors' => [],
];

/**
 * 运行测试函数
 *
 * @param string $name 测试名称
 * @param callable $test_func 测试函数
 */
function run_test($name, $test_func)
{
    global $test_results;
    
    $test_results['total']++;
    echo "\n\n=== 测试: {$name} ===\n";
    
    try {
        $result = $test_func();
        if ($result === true) {
            echo "✅ 通过\n";
            $test_results['passed']++;
        } else {
            echo "❌ 失败\n";
            $test_results['failed']++;
            $test_results['errors'][] = "测试 '{$name}' 失败: {$result}";
        }
    } catch (Exception $e) {
        echo "❌ 错误: " . $e->getMessage() . "\n";
        $test_results['failed']++;
        $test_results['errors'][] = "测试 '{$name}' 发生错误: " . $e->getMessage();
    }
}

/**
 * 检查目录是否存在
 *
 * @param string $path 目录路径
 * @return bool 是否存在
 */
function check_directory($path)
{
    return is_dir($path);
}

/**
 * 检查文件是否存在
 *
 * @param string $path 文件路径
 * @return bool 是否存在
 */
function check_file($path)
{
    return file_exists($path);
}

/**
 * 1. 模块安装与加载测试
 */
echo "\n=== 开始系统集成测试 ===\n";

// 测试1: 检查modules_v4目录是否存在
run_test('模块目录存在检查', function() {
    return check_directory(__DIR__ . '/modules_v4');
});

// 测试2: 检查各个模块文件是否存在
$modules = [
    'ZiweiModule',
    'ChineseKinship',
    'ClanStructure',
    'AncestralHall',
    'Migration',
    'Grave',
    'FamilyRules',
    'ClanActivity',
    'ChineseStyle',
];

foreach ($modules as $module) {
    run_test("检查{$module}模块文件", function() use ($module) {
        return check_file(__DIR__ . "/modules_v4/{$module}/module.php");
    });
}

// 测试3: 检查中文翻译文件是否存在
run_test('检查中文翻译文件', function() {
    return check_file(__DIR__ . '/zh-Hans_extra.php');
});

// 测试4: 检查中文样式文件是否存在
run_test('检查中文样式文件', function() {
    return check_file(__DIR__ . '/chinese_style.css');
});

/**
 * 2. 模块代码语法检查
 */
run_test('模块代码语法检查', function() use ($modules) {
    foreach ($modules as $module) {
        $file_path = __DIR__ . "/modules_v4/{$module}/module.php";
        
        // 使用PHP lint检查语法
        $output = shell_exec("php -l {$file_path}");
        
        if (strpos($output, 'No syntax errors detected') === false) {
            return "模块 {$module} 存在语法错误: {$output}";
        }
    }
    return true;
});

/**
 * 3. 翻译文件完整性检查
 */
run_test('中文翻译文件完整性检查', function() {
    $translations = include __DIR__ . '/zh-Hans_extra.php';
    
    if (!is_array($translations)) {
        return '翻译文件格式错误，不是有效的数组';
    }
    
    // 检查关键翻译是否存在
    $key_translations = [
        'Chinese genealogical generation management',
        'Chinese traditional kinship',
        'Clan organization structure management',
        'Ancestral hall information management',
        'Migration and ancestral home records',
        'Grave information management',
        'Family rules and instructions',
        'Traditional Chinese Style',
    ];
    
    foreach ($key_translations as $key) {
        if (!isset($translations[$key])) {
            return "缺少关键翻译: {$key}";
        }
    }
    
    return true;
});

/**
 * 4. 模块间依赖检查
 */
run_test('模块间依赖检查', function() {
    // 检查Migration模块是否使用了正确的_AHOM标签（修复后的标签）
    $migration_file = __DIR__ . '/modules_v4/Migration/module.php';
    $content = file_get_contents($migration_file);
    
    if (strpos($content, "_ANCH") !== false) {
        return 'Migration模块仍然使用了_ANCH标签，应该使用_AHOM标签';
    }
    
    // 检查所有模块是否都包含正确的命名空间
    $modules = [
        'ZiweiModule',
        'ChineseKinship',
        'ClanStructure',
        'AncestralHall',
        'Migration',
        'Grave',
        'FamilyRules',
        'ClanActivity',
        'ChineseStyle',
    ];
    
    foreach ($modules as $module) {
        $file_path = __DIR__ . "/modules_v4/{$module}/module.php";
        $content = file_get_contents($file_path);
        
        if (strpos($content, "namespace Fisharebest\\Webtrees\\Module;") === false) {
            return "模块 {$module} 缺少正确的命名空间声明";
        }
    }
    
    return true;
});

/**
 * 5. 样式文件完整性检查
 */
run_test('中文样式文件完整性检查', function() {
    $content = file_get_contents(__DIR__ . '/chinese_style.css');
    
    // 检查关键样式是否存在
    $key_styles = [
        '--chinese-red',
        '.btn {',
        '.card {',
        '.table {',
        '.form-group {',
        '.ziwei {',
        '.kinship {',
    ];
    
    foreach ($key_styles as $style) {
        if (strpos($content, $style) === false) {
            return "样式文件缺少关键样式: {$style}";
        }
    }
    
    return true;
});

/**
 * 测试结果汇总
 */
echo "\n\n";
echo str_repeat('=', 50) . "\n";
echo "测试结果汇总\n";
echo str_repeat('=', 50) . "\n";
echo "总测试数: {$test_results['total']}\n";
echo "通过: {$test_results['passed']}\n";
echo "失败: {$test_results['failed']}\n";
echo "成功率: " . round(($test_results['passed'] / $test_results['total']) * 100, 2) . "%\n";

if (!empty($test_results['errors'])) {
    echo "\n错误详情: \n";
    foreach ($test_results['errors'] as $error) {
        echo "- {$error}\n";
    }
}

// 退出状态
if ($test_results['failed'] > 0) {
    exit(1);
} else {
    echo "\n🎉 所有测试通过！\n";
    exit(0);
}

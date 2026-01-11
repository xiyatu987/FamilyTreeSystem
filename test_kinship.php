<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Services\KinshipService;
use App\Models\FamilyMember;

// 创建模拟对象函数
function createFamilyMemberMock($id, $gender, $user_id)
{
    $member = $this->createMock(FamilyMember::class);
    $member->method('getAttribute')->willReturn($id, $gender, $user_id);
    return $member;
}

echo "开始测试中国族谱管理系统亲属关系计算服务...\n";
echo "====================================================\n\n";

// 测试基本关系
echo "1. 测试父母关系\n";
$father = createFamilyMemberMock(1, 'male', 1);
$child = createFamilyMemberMock(2, 'male', 1);
$child->method('father')->willReturn($father);

$kinshipService = new KinshipService();
$relationship = $kinshipService->getRelationship($child, $father);
echo "   孩子称呼父亲: {$relationship}\n";

// 测试配偶关系
echo "\n2. 测试配偶关系\n";
$wife = createFamilyMemberMock(3, 'female', 1);
$husband = createFamilyMemberMock(4, 'male', 1);
$wife->method('spouse')->willReturn($husband);
$husband->method('spouse')->willReturn($wife);

$relationship = $kinshipService->getRelationship($wife, $husband);
echo "   妻子称呼丈夫: {$relationship}\n";

$relationship = $kinshipService->getRelationship($husband, $wife);
echo "   丈夫称呼妻子: {$relationship}\n";

// 测试兄弟姐妹关系
echo "\n3. 测试兄弟姐妹关系\n";
$brother = createFamilyMemberMock(5, 'male', 1);
$sister = createFamilyMemberMock(6, 'female', 1);

// 模拟父亲
$dad = createFamilyMemberMock(7, 'male', 1);
$brother->method('father')->willReturn($dad);
$sister->method('father')->willReturn($dad);

$relationship = $kinshipService->getRelationship($brother, $sister);
echo "   兄弟称呼姐妹: {$relationship}\n";

$relationship = $kinshipService->getRelationship($sister, $brother);
echo "   姐妹称呼兄弟: {$relationship}\n";

echo "\n测试完成！\n";
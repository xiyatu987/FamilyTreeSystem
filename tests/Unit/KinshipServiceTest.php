<?php

namespace Tests\Unit;

use App\Models\FamilyMember;
use App\Services\KinshipService;
use PHPUnit\Framework\TestCase;

class KinshipServiceTest extends TestCase
{
    /**
     * 测试父亲关系计算
     */
    public function testFatherRelationship()
    {
        // 创建模拟对象
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(1, 'male', 1); // id, gender, user_id
        
        $child = $this->createMock(FamilyMember::class);
        $child->method('getAttribute')->willReturn(2, 'male', 1); // id, gender, user_id
        $child->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($child, $father);
        
        $this->assertEquals('父亲', $relationship);
    }
    
    /**
     * 测试母亲关系计算
     */
    public function testMotherRelationship()
    {
        // 创建模拟对象
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(3, 'female', 1); // id, gender, user_id
        
        $child = $this->createMock(FamilyMember::class);
        $child->method('getAttribute')->willReturn(4, 'female', 1); // id, gender, user_id
        $child->method('mother')->willReturn($mother);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($child, $mother);
        
        $this->assertEquals('母亲', $relationship);
    }
    
    /**
     * 测试儿子关系计算
     */
    public function testSonRelationship()
    {
        // 创建模拟对象
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(5, 'male', 1); // id, gender, user_id
        
        $son = $this->createMock(FamilyMember::class);
        $son->method('getAttribute')->willReturn(6, 'male', 1); // id, gender, user_id
        $son->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($father, $son);
        
        $this->assertEquals('儿子', $relationship);
    }
    
    /**
     * 测试女儿关系计算
     */
    public function testDaughterRelationship()
    {
        // 创建模拟对象
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(7, 'female', 1); // id, gender, user_id
        
        $daughter = $this->createMock(FamilyMember::class);
        $daughter->method('getAttribute')->willReturn(8, 'female', 1); // id, gender, user_id
        $daughter->method('mother')->willReturn($mother);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($mother, $daughter);
        
        $this->assertEquals('女儿', $relationship);
    }
    
    /**
     * 测试哥哥关系计算
     */
    public function testOlderBrotherRelationship()
    {
        // 创建模拟对象
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(9, 'male', 1); // id, gender, user_id
        
        $olderBrother = $this->createMock(FamilyMember::class);
        $olderBrother->method('getAttribute')->willReturn(10, 'male', 1); // id, gender, user_id
        $olderBrother->method('father')->willReturn($father);
        
        $youngerBrother = $this->createMock(FamilyMember::class);
        $youngerBrother->method('getAttribute')->willReturn(11, 'male', 1); // id, gender, user_id
        $youngerBrother->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($youngerBrother, $olderBrother);
        
        $this->assertEquals('哥哥', $relationship);
    }
    
    /**
     * 测试弟弟关系计算
     */
    public function testYoungerBrotherRelationship()
    {
        // 创建模拟对象
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(12, 'male', 1); // id, gender, user_id
        
        $olderBrother = $this->createMock(FamilyMember::class);
        $olderBrother->method('getAttribute')->willReturn(13, 'male', 1); // id, gender, user_id
        $olderBrother->method('father')->willReturn($father);
        
        $youngerBrother = $this->createMock(FamilyMember::class);
        $youngerBrother->method('getAttribute')->willReturn(14, 'male', 1); // id, gender, user_id
        $youngerBrother->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($olderBrother, $youngerBrother);
        
        $this->assertEquals('兄弟', $relationship);
    }
    
    /**
     * 测试姐妹关系计算
     */
    public function testSisterRelationship()
    {
        // 创建模拟对象
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(15, 'male', 1); // id, gender, user_id
        
        $brother = $this->createMock(FamilyMember::class);
        $brother->method('getAttribute')->willReturn(16, 'male', 1); // id, gender, user_id
        $brother->method('father')->willReturn($father);
        
        $sister = $this->createMock(FamilyMember::class);
        $sister->method('getAttribute')->willReturn(17, 'female', 1); // id, gender, user_id
        $sister->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($brother, $sister);
        
        $this->assertEquals('姐妹', $relationship);
    }
    
    /**
     * 测试配偶关系计算（丈夫）
     */
    public function testHusbandRelationship()
    {
        // 创建模拟对象
        $wife = $this->createMock(FamilyMember::class);
        $wife->method('getAttribute')->willReturn(18, 'female', 1); // id, gender, user_id
        $wife->method('spouse')->willReturnCallback(function() {
            $husband = $this->createMock(FamilyMember::class);
            $husband->method('getAttribute')->willReturn(19, 'male', 1); // id, gender, user_id
            return $husband;
        });
        
        $husband = $wife->spouse();
        $husband->method('spouse')->willReturn($wife);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($wife, $husband);
        
        $this->assertEquals('丈夫', $relationship);
    }
    
    /**
     * 测试配偶关系计算（妻子）
     */
    public function testWifeRelationship()
    {
        // 创建模拟对象
        $husband = $this->createMock(FamilyMember::class);
        $husband->method('getAttribute')->willReturn(20, 'male', 1); // id, gender, user_id
        $husband->method('spouse')->willReturnCallback(function() {
            $wife = $this->createMock(FamilyMember::class);
            $wife->method('getAttribute')->willReturn(21, 'female', 1); // id, gender, user_id
            return $wife;
        });
        
        $wife = $husband->spouse();
        $wife->method('spouse')->willReturn($husband);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($husband, $wife);
        
        $this->assertEquals('妻子', $relationship);
    }
    
    /**
     * 测试祖父关系计算
     */
    public function testGrandfatherRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(22, 'male', 1); // id, gender, user_id
        
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(23, 'male', 1); // id, gender, user_id
        $father->method('father')->willReturn($grandfather);
        
        $grandson = $this->createMock(FamilyMember::class);
        $grandson->method('getAttribute')->willReturn(24, 'male', 1); // id, gender, user_id
        $grandson->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($grandson, $grandfather);
        
        $this->assertEquals('祖父', $relationship);
    }
    
    /**
     * 测试祖母关系计算
     */
    public function testGrandmotherRelationship()
    {
        // 创建模拟对象
        $grandmother = $this->createMock(FamilyMember::class);
        $grandmother->method('getAttribute')->willReturn(25, 'female', 1); // id, gender, user_id
        
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(26, 'male', 1); // id, gender, user_id
        $father->method('mother')->willReturn($grandmother);
        
        $granddaughter = $this->createMock(FamilyMember::class);
        $granddaughter->method('getAttribute')->willReturn(27, 'female', 1); // id, gender, user_id
        $granddaughter->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($granddaughter, $grandmother);
        
        $this->assertEquals('祖母', $relationship);
    }
    
    /**
     * 测试外祖父关系计算
     */
    public function testMaternalGrandfatherRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(28, 'male', 1); // id, gender, user_id
        
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(29, 'female', 1); // id, gender, user_id
        $mother->method('father')->willReturn($grandfather);
        
        $grandson = $this->createMock(FamilyMember::class);
        $grandson->method('getAttribute')->willReturn(30, 'male', 1); // id, gender, user_id
        $grandson->method('mother')->willReturn($mother);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($grandson, $grandfather);
        
        $this->assertEquals('外祖父', $relationship);
    }
    
    /**
     * 测试外祖母关系计算
     */
    public function testMaternalGrandmotherRelationship()
    {
        // 创建模拟对象
        $grandmother = $this->createMock(FamilyMember::class);
        $grandmother->method('getAttribute')->willReturn(31, 'female', 1); // id, gender, user_id
        
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(32, 'female', 1); // id, gender, user_id
        $mother->method('mother')->willReturn($grandmother);
        
        $granddaughter = $this->createMock(FamilyMember::class);
        $granddaughter->method('getAttribute')->willReturn(33, 'female', 1); // id, gender, user_id
        $granddaughter->method('mother')->willReturn($mother);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($granddaughter, $grandmother);
        
        $this->assertEquals('外祖母', $relationship);
    }
    
    /**
     * 测试孙子关系计算
     */
    public function testGrandsonRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(34, 'male', 1); // id, gender, user_id
        
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(35, 'male', 1); // id, gender, user_id
        $father->method('father')->willReturn($grandfather);
        
        $grandson = $this->createMock(FamilyMember::class);
        $grandson->method('getAttribute')->willReturn(36, 'male', 1); // id, gender, user_id
        $grandson->method('father')->willReturn($father);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($grandfather, $grandson);
        
        $this->assertEquals('孙子', $relationship);
    }
    
    /**
     * 测试孙女关系计算
     */
    public function testGranddaughterRelationship()
    {
        // 创建模拟对象
        $grandmother = $this->createMock(FamilyMember::class);
        $grandmother->method('getAttribute')->willReturn(37, 'female', 1); // id, gender, user_id
        
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(38, 'female', 1); // id, gender, user_id
        $mother->method('mother')->willReturn($grandmother);
        
        $granddaughter = $this->createMock(FamilyMember::class);
        $granddaughter->method('getAttribute')->willReturn(39, 'female', 1); // id, gender, user_id
        $granddaughter->method('mother')->willReturn($mother);
        
        // 测试亲属关系计算
        $kinshipService = new KinshipService();
        $relationship = $kinshipService->getRelationship($grandmother, $granddaughter);
        
        $this->assertEquals('孙女', $relationship);
    }
    
    /**
     * 测试伯父关系计算
     */
    public function testPaternalUncleOlderRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(40, 'male', 1); // id, gender, user_id
        
        $uncle = $this->createMock(FamilyMember::class);
        $uncle->method('getAttribute')->willReturn(41, 'male', 1); // id, gender, user_id
        $uncle->method('father')->willReturn($grandfather);
        
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(42, 'male', 1); // id, gender, user_id
        $father->method('father')->willReturn($grandfather);
        $father->method('children')->willReturnCallback(function() {
            return collect();
        });
        
        $nephew = $this->createMock(FamilyMember::class);
        $nephew->method('getAttribute')->willReturn(43, 'male', 1); // id, gender, user_id
        $nephew->method('father')->willReturn($father);
        
        // 模拟isSibling方法
        $kinshipServiceMock = $this->getMockBuilder(KinshipService::class)
                                   ->setMethods(['isSibling'])
                                   ->getMock();
        $kinshipServiceMock->method('isSibling')->willReturn(true);
        
        // 测试亲属关系计算
        $relationship = $kinshipServiceMock->getRelationship($nephew, $uncle);
        
        $this->assertEquals('伯父', $relationship);
    }
    
    /**
     * 测试姑姑关系计算
     */
    public function testPaternalAuntRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(44, 'male', 1); // id, gender, user_id
        
        $aunt = $this->createMock(FamilyMember::class);
        $aunt->method('getAttribute')->willReturn(45, 'female', 1); // id, gender, user_id
        $aunt->method('father')->willReturn($grandfather);
        
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(46, 'male', 1); // id, gender, user_id
        $father->method('father')->willReturn($grandfather);
        $father->method('children')->willReturnCallback(function() {
            return collect();
        });
        
        $nephew = $this->createMock(FamilyMember::class);
        $nephew->method('getAttribute')->willReturn(47, 'male', 1); // id, gender, user_id
        $nephew->method('father')->willReturn($father);
        
        // 模拟isSibling方法
        $kinshipServiceMock = $this->getMockBuilder(KinshipService::class)
                                   ->setMethods(['isSibling'])
                                   ->getMock();
        $kinshipServiceMock->method('isSibling')->willReturn(true);
        
        // 测试亲属关系计算
        $relationship = $kinshipServiceMock->getRelationship($nephew, $aunt);
        
        $this->assertEquals('姑姑', $relationship);
    }
    
    /**
     * 测试舅舅关系计算
     */
    public function testMaternalUncleRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(48, 'male', 1); // id, gender, user_id
        
        $uncle = $this->createMock(FamilyMember::class);
        $uncle->method('getAttribute')->willReturn(49, 'male', 1); // id, gender, user_id
        $uncle->method('father')->willReturn($grandfather);
        
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(50, 'female', 1); // id, gender, user_id
        $mother->method('father')->willReturn($grandfather);
        $mother->method('children')->willReturnCallback(function() {
            return collect();
        });
        
        $nephew = $this->createMock(FamilyMember::class);
        $nephew->method('getAttribute')->willReturn(51, 'male', 1); // id, gender, user_id
        $nephew->method('mother')->willReturn($mother);
        
        // 模拟isSibling方法
        $kinshipServiceMock = $this->getMockBuilder(KinshipService::class)
                                   ->setMethods(['isSibling'])
                                   ->getMock();
        $kinshipServiceMock->method('isSibling')->willReturn(true);
        
        // 测试亲属关系计算
        $relationship = $kinshipServiceMock->getRelationship($nephew, $uncle);
        
        $this->assertEquals('舅舅', $relationship);
    }
    
    /**
     * 测试阿姨关系计算
     */
    public function testMaternalAuntRelationship()
    {
        // 创建模拟对象
        $grandfather = $this->createMock(FamilyMember::class);
        $grandfather->method('getAttribute')->willReturn(52, 'male', 1); // id, gender, user_id
        
        $aunt = $this->createMock(FamilyMember::class);
        $aunt->method('getAttribute')->willReturn(53, 'female', 1); // id, gender, user_id
        $aunt->method('father')->willReturn($grandfather);
        
        $mother = $this->createMock(FamilyMember::class);
        $mother->method('getAttribute')->willReturn(54, 'female', 1); // id, gender, user_id
        $mother->method('father')->willReturn($grandfather);
        $mother->method('children')->willReturnCallback(function() {
            return collect();
        });
        
        $nephew = $this->createMock(FamilyMember::class);
        $nephew->method('getAttribute')->willReturn(55, 'male', 1); // id, gender, user_id
        $nephew->method('mother')->willReturn($mother);
        
        // 模拟isSibling方法
        $kinshipServiceMock = $this->getMockBuilder(KinshipService::class)
                                   ->setMethods(['isSibling'])
                                   ->getMock();
        $kinshipServiceMock->method('isSibling')->willReturn(true);
        
        // 测试亲属关系计算
        $relationship = $kinshipServiceMock->getRelationship($nephew, $aunt);
        
        $this->assertEquals('阿姨', $relationship);
    }
    
    /**
     * 测试侄子关系计算
     */
    public function testNephewRelationship()
    {
        // 创建模拟对象
        $uncle = $this->createMock(FamilyMember::class);
        $uncle->method('getAttribute')->willReturn(56, 'male', 1); // id, gender, user_id
        
        $father = $this->createMock(FamilyMember::class);
        $father->method('getAttribute')->willReturn(57, 'male', 1); // id, gender, user_id
        $father->method('children')->willReturnCallback(function() {
            $nephew = $this->createMock(FamilyMember::class);
            $nephew->method('getAttribute')->willReturn(58, 'male', 1); // id, gender, user_id
            return collect([$nephew]);
        });
        
        $uncle->method('father')->willReturnCallback(function() {
            $grandfather = $this->createMock(FamilyMember::class);
            $grandfather->method('children')->willReturnCallback(function() {
                return collect();
            });
            return $grandfather;
        });
        
        $nephew = $father->children()->first();
        $nephew->method('father')->willReturn($father);
        
        // 模拟isNephewOrNiece方法
        $kinshipServiceMock = $this->getMockBuilder(KinshipService::class)
                                   ->setMethods(['isNephewOrNiece'])
                                   ->getMock();
        $kinshipServiceMock->method('isNephewOrNiece')->willReturn(true);
        
        // 测试亲属关系计算
        $relationship = $kinshipServiceMock->getRelationship($uncle, $nephew);
        
        $this->assertEquals('侄子', $relationship);
    }
    
    /**
     * 测试堂兄弟关系计算
     */
    public function testPaternalCousinRelationship()
    {
        // 创建模拟对象
        $cousin = $this->createMock(FamilyMember::class);
        $cousin->method('getAttribute')->willReturn(59, 'male', 1); // id, gender, user_id
        
        $subject = $this->createMock(FamilyMember::class);
        $subject->method('getAttribute')->willReturn(60, 'male', 1); // id, gender, user_id
        
        // 模拟isCousin方法
        $kinshipServiceMock = $this->getMockBuilder(KinshipService::class)
                                   ->setMethods(['isCousin'])
                                   ->getMock();
        $kinshipServiceMock->method('isCousin')->willReturn(true);
        
        // 测试亲属关系计算
        $relationship = $kinshipServiceMock->getRelationship($subject, $cousin);
        
        $this->assertEquals('堂/表兄弟姐妹', $relationship);
    }
}

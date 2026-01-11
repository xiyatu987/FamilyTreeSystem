<?php

/**
 * 中国族谱管理系统 - 字辈管理模块
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\GedcomService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;

/**
 * 字辈管理模块
 */
class ZiweiModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('中国族谱字辈管理');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('为中国族谱添加字辈管理功能，支持字辈库管理、自动分配字辈和字辈谱系图展示。');
    }

    /**
     * @return string
     */
    public function customModuleAuthorName(): string
    {
        return 'webtrees development team';
    }

    /**
     * @return string
     */
    public function customModuleVersion(): string
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function customModuleSupportUrl(): string
    {
        return 'https://github.com/fisharebest/webtrees';
    }

    /**
     * 获取人员的字辈信息
     *
     * @param Individual $individual
     *
     * @return string
     */
    public static function getZiwei(Individual $individual): string
    {
        $fact = $individual->facts(['_ZIWEI'])->first();
        if ($fact instanceof Fact) {
            return $fact->value();
        }

        return '';
    }

    /**
     * 设置人员的字辈信息
     *
     * @param Individual $individual
     * @param string     $ziwei
     * @param int|null   $generation
     * @param int|null   $order
     */
    public static function setZiwei(Individual $individual, string $ziwei, int|null $generation = null, int|null $order = null): void
    {
        // 首先删除现有的字辈信息
        foreach ($individual->facts(['_ZIWEI']) as $fact) {
            $individual->deleteFact($fact->id());
        }

        // 创建新的字辈信息
        $gedcom = "1 _ZIWEI $ziwei";
        if ($generation !== null) {
            $gedcom .= "\n2 GENERATION $generation";
        }
        if ($order !== null) {
            $gedcom .= "\n2 ORDER $order";
        }

        $individual->createFact($gedcom, true);
    }

    /**
     * 获取人员的字辈世代
     *
     * @param Individual $individual
     *
     * @return int|null
     */
    public static function getZiweiGeneration(Individual $individual): int|null
    {
        $fact = $individual->facts(['_ZIWEI'])->first();
        if ($fact instanceof Fact) {
            $generation = $fact->attribute('GENERATION');
            return $generation !== '' ? (int)$generation : null;
        }

        return null;
    }

    /**
     * 获取人员的字辈排行
     *
     * @param Individual $individual
     *
     * @return int|null
     */
    public static function getZiweiOrder(Individual $individual): int|null
    {
        $fact = $individual->facts(['_ZIWEI'])->first();
        if ($fact instanceof Fact) {
            $order = $fact->attribute('ORDER');
            return $order !== '' ? (int)$order : null;
        }

        return null;
    }

    /**
     * 自动为人员分配字辈
     *
     * @param Individual $individual
     * @param Tree       $tree
     */
    public static function autoAssignZiwei(Individual $individual, Tree $tree): void
    {
        // 查找默认字辈库
        $gedcom_service = new GedcomService();
        $head = $tree->gedcomHeader();
        
        // 这里可以实现更复杂的字辈自动分配逻辑
        // 例如：根据父亲或祖父的字辈，计算当前人员的字辈
        // 目前简化实现，从字辈库中随机选择一个
        $default_ziweis = ['昭', '穆', '仁', '义', '礼', '智', '信', '忠', '孝', '悌'];
        $random_ziwei = $default_ziweis[array_rand($default_ziweis)];
        
        self::setZiwei($individual, $random_ziwei);
    }

    /**
     * 获取字辈谱系图数据
     *
     * @param Individual $root
     * @param int        $generations
     *
     * @return array
     */
    public static function getZiweiTreeData(Individual $root, int $generations = 5): array
    {
        $tree_data = [
            'individual' => $root,
            'ziwei' => self::getZiwei($root),
            'children' => [],
        ];

        if ($generations > 0) {
            foreach ($root->spouseFamilies() as $family) {
                foreach ($family->children() as $child) {
                    $tree_data['children'][] = self::getZiweiTreeData($child, $generations - 1);
                }
            }
        }

        return $tree_data;
    }
}
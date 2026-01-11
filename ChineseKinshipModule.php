<?php

/**
 * 中国族谱管理系统 - 中国传统亲属关系模块
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Family;

/**
 * 中国传统亲属关系模块
 */
class ChineseKinshipModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('中国传统亲属关系');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('为中国族谱添加传统亲属关系称谓计算功能，支持自动计算和展示复杂的亲属关系。');
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
     * 计算两个人之间的亲属关系称谓
     *
     * @param Individual $individual1
     * @param Individual $individual2
     *
     * @return string
     */
    public static function calculateKinship(Individual $individual1, Individual $individual2): string
    {
        // 自己
        if ($individual1->xref() === $individual2->xref()) {
            return I18N::translate('自己');
        }

        // 父母子女关系
        if (self::isParentChild($individual1, $individual2)) {
            return $individual1->isAncestorOf($individual2) ? 
                ($individual1->sex() === 'M' ? I18N::translate('父亲') : I18N::translate('母亲')) : 
                ($individual2->sex() === 'M' ? I18N::translate('儿子') : I18N::translate('女儿'));
        }

        // 配偶关系
        if (self::isSpouse($individual1, $individual2)) {
            return $individual1->sex() === 'M' ? I18N::translate('妻子') : I18N::translate('丈夫');
        }

        // 兄弟姐妹关系
        if (self::isSibling($individual1, $individual2)) {
            if ($individual1->sex() === 'M' && $individual2->sex() === 'M') {
                return $individual1->birthDate() <=> $individual2->birthDate() < 0 ? I18N::translate('弟弟') : I18N::translate('哥哥');
            } elseif ($individual1->sex() === 'F' && $individual2->sex() === 'F') {
                return $individual1->birthDate() <=> $individual2->birthDate() < 0 ? I18N::translate('妹妹') : I18N::translate('姐姐');
            } else {
                if ($individual1->sex() === 'M') {
                    return $individual1->birthDate() <=> $individual2->birthDate() < 0 ? I18N::translate('妹妹') : I18N::translate('姐姐');
                } else {
                    return $individual1->birthDate() <=> $individual2->birthDate() < 0 ? I18N::translate('弟弟') : I18N::translate('哥哥');
                }
            }
        }

        // 祖父母孙子女关系
        if (self::isGrandParentGrandChild($individual1, $individual2)) {
            if ($individual1->isAncestorOf($individual2)) {
                return $individual1->sex() === 'M' ? I18N::translate('祖父') : I18N::translate('祖母');
            } else {
                return $individual2->sex() === 'M' ? I18N::translate('孙子') : I18N::translate('孙女');
            }
        }

        // 其他亲属关系
        // TODO: 实现更复杂的亲属关系计算逻辑
        
        return I18N::translate('亲属');
    }

    /**
     * 检查两个人是否是父母子女关系
     *
     * @param Individual $individual1
     * @param Individual $individual2
     *
     * @return bool
     */
    private static function isParentChild(Individual $individual1, Individual $individual2): bool
    {
        return $individual1->isAncestorOf($individual2, 1) || $individual2->isAncestorOf($individual1, 1);
    }

    /**
     * 检查两个人是否是配偶关系
     *
     * @param Individual $individual1
     * @param Individual $individual2
     *
     * @return bool
     */
    private static function isSpouse(Individual $individual1, Individual $individual2): bool
    {
        foreach ($individual1->spouseFamilies() as $family) {
            $spouse = $family->spouse($individual1);
            if ($spouse && $spouse->xref() === $individual2->xref()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查两个人是否是兄弟姐妹关系
     *
     * @param Individual $individual1
     * @param Individual $individual2
     *
     * @return bool
     */
    private static function isSibling(Individual $individual1, Individual $individual2): bool
    {
        foreach ($individual1->childFamilies() as $family) {
            foreach ($family->children() as $child) {
                if ($child->xref() === $individual2->xref()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 检查两个人是否是祖父母孙子女关系
     *
     * @param Individual $individual1
     * @param Individual $individual2
     *
     * @return bool
     */
    private static function isGrandParentGrandChild(Individual $individual1, Individual $individual2): bool
    {
        return $individual1->isAncestorOf($individual2, 2) || $individual2->isAncestorOf($individual1, 2);
    }

    /**
     * 获取亲属关系树数据
     *
     * @param Individual $root
     * @param int        $generations
     *
     * @return array
     */
    public static function getKinshipTreeData(Individual $root, int $generations = 3): array
    {
        $tree_data = [
            'individual' => $root,
            'children' => [],
        ];

        if ($generations > 0) {
            // 获取子女
            foreach ($root->spouseFamilies() as $family) {
                foreach ($family->children() as $child) {
                    $tree_data['children'][] = [
                        'individual' => $child,
                        'relationship' => self::calculateKinship($root, $child),
                        'children' => self::getKinshipTreeData($child, $generations - 1)['children'],
                    ];
                }
            }
        }

        return $tree_data;
    }
}
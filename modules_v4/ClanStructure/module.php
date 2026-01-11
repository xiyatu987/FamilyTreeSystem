<?php

/**
 * 中国族谱管理系统 - 宗族组织结构管理模块
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Family;

/**
 * 宗族组织结构管理模块
 */
class ClanStructureModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('宗族组织结构管理');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('管理宗族组织结构，包括宗族分支、派系、族长制等信息。');
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
     * 获取人员的宗族信息
     *
     * @param Individual $individual
     *
     * @return string
     */
    public static function getClan(Individual $individual): string
    {
        $fact = $individual->facts(['_CLAN'])->first();
        if ($fact instanceof Fact) {
            return $fact->value();
        }

        return '';
    }

    /**
     * 设置人员的宗族信息
     *
     * @param Individual $individual
     * @param string     $clan
     * @param string     $branch
     * @param string     $faction
     */
    public static function setClan(Individual $individual, string $clan, string $branch = '', string $faction = ''): void
    {
        // 首先删除现有的宗族信息
        foreach ($individual->facts(['_CLAN']) as $fact) {
            $individual->deleteFact($fact->id());
        }

        // 创建新的宗族信息
        $gedcom = "1 _CLAN $clan";
        if ($branch !== '') {
            $gedcom .= "\n2 BRANCH $branch";
        }
        if ($faction !== '') {
            $gedcom .= "\n2 FACTION $faction";
        }

        $individual->createFact($gedcom, true);
    }

    /**
     * 获取人员的宗族分支
     *
     * @param Individual $individual
     *
     * @return string
     */
    public static function getClanBranch(Individual $individual): string
    {
        $fact = $individual->facts(['_CLAN'])->first();
        if ($fact instanceof Fact) {
            return $fact->attribute('BRANCH') ?? '';
        }

        return '';
    }

    /**
     * 获取人员的宗族派系
     *
     * @param Individual $individual
     *
     * @return string
     */
    public static function getClanFaction(Individual $individual): string
    {
        $fact = $individual->facts(['_CLAN'])->first();
        if ($fact instanceof Fact) {
            return $fact->attribute('FACTION') ?? '';
        }

        return '';
    }

    /**
     * 获取宗族分支列表
     *
     * @param Tree $tree
     *
     * @return array
     */
    public static function getClanBranches(Tree $tree): array
    {
        $branches = [];
        $individuals = Registry::gedcomRecordFactory()->allIndividuals($tree);

        foreach ($individuals as $individual) {
            $branch = self::getClanBranch($individual);
            if ($branch !== '' && !in_array($branch, $branches)) {
                $branches[] = $branch;
            }
        }

        sort($branches);
        return $branches;
    }

    /**
     * 获取宗族派系列表
     *
     * @param Tree $tree
     *
     * @return array
     */
    public static function getClanFactions(Tree $tree): array
    {
        $factions = [];
        $individuals = Registry::gedcomRecordFactory()->allIndividuals($tree);

        foreach ($individuals as $individual) {
            $faction = self::getClanFaction($individual);
            if ($faction !== '' && !in_array($faction, $factions)) {
                $factions[] = $faction;
            }
        }

        sort($factions);
        return $factions;
    }

    /**
     * 获取宗族结构图数据
     *
     * @param Tree   $tree
     * @param string $clan
     *
     * @return array
     */
    public static function getClanStructure(Tree $tree, string $clan): array
    {
        $structure = ['name' => $clan, 'branches' => []];
        $individuals = Registry::gedcomRecordFactory()->allIndividuals($tree);
        $branches = [];

        // 收集所有分支
        foreach ($individuals as $individual) {
            if (self::getClan($individual) === $clan) {
                $branch = self::getClanBranch($individual);
                if ($branch !== '') {
                    if (!isset($branches[$branch])) {
                        $branches[$branch] = ['name' => $branch, 'factions' => [], 'members' => []];
                    }
                    
                    $faction = self::getClanFaction($individual);
                    if ($faction !== '') {
                        if (!in_array($faction, $branches[$branch]['factions'])) {
                            $branches[$branch]['factions'][] = $faction;
                        }
                    }
                    
                    $branches[$branch]['members'][] = $individual;
                }
            }
        }

        // 构建结构
        foreach ($branches as $branch_data) {
            sort($branch_data['factions']);
            $structure['branches'][] = $branch_data;
        }

        return $structure;
    }
}
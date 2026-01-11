<?php

/**
 * 中国族谱管理系统 - 家规家训功能模块
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
use Fisharebest\Webtrees\GedcomRecord;

/**
 * 家规家训功能模块
 */
class FamilyRulesModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('家规家训管理');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('记录和管理宗族的家规、家训和族规等信息。');
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
     * 创建或更新家规家训
     *
     * @param Tree   $tree
     * @param string $rule_name
     * @param string $rule_type
     * @param string $content
     * @param string $author
     * @param string $date
     *
     * @return string|null 家规家训记录的ID
     */
    public static function createOrUpdateFamilyRule(
        Tree $tree,
        string $rule_name,
        string $rule_type,
        string $content,
        string $author,
        string $date
    ): ?string {
        // 创建GEDCOM数据
        $gedcom = "0 @_R$rule_name@ _RULE\n" .
                  "1 NAME $rule_name\n" .
                  "1 TYPE $rule_type\n" .
                  "1 NOTE $content\n" .
                  "1 _AUTH $author\n" .
                  "1 DATE $date\n";
        
        // 查找是否已存在同名家规
        $existing_rule = self::findFamilyRuleByName($tree, $rule_name);
        
        if ($existing_rule) {
            // 更新现有家规
            $existing_rule->updateGedcom($gedcom);
            return $existing_rule->xref();
        } else {
            // 创建新家规
            $record_factory = Registry::gedcomRecordFactory();
            $rule = $record_factory->create($gedcom, $tree, '_RULE');
            return $rule?->xref();
        }
    }

    /**
     * 根据名称查找家规家训
     *
     * @param Tree   $tree
     * @param string $rule_name
     *
     * @return GedcomRecord|null
     */
    public static function findFamilyRuleByName(Tree $tree, string $rule_name): ?GedcomRecord
    {
        $record_factory = Registry::gedcomRecordFactory();
        $records = $record_factory->records($tree, '_RULE');
        
        foreach ($records as $record) {
            if ($record->facts(['NAME'])->first()?->value() === $rule_name) {
                return $record;
            }
        }
        
        return null;
    }

    /**
     * 获取所有家规家训列表
     *
     * @param Tree $tree
     *
     * @return array
     */
    public static function getAllFamilyRules(Tree $tree): array
    {
        $record_factory = Registry::gedcomRecordFactory();
        $records = $record_factory->records($tree, '_RULE');
        $rules = [];
        
        foreach ($records as $record) {
            $name = $record->facts(['NAME'])->first()?->value() ?? '';
            $type = $record->facts(['TYPE'])->first()?->value() ?? '';
            $date = $record->facts(['DATE'])->first()?->value() ?? '';
            $author = $record->facts(['_AUTH'])->first()?->value() ?? '';
            
            if ($name !== '') {
                $rules[] = [
                    'xref'   => $record->xref(),
                    'name'   => $name,
                    'type'   => $type,
                    'date'   => $date,
                    'author' => $author
                ];
            }
        }
        
        // 按类型和日期排序
        usort($rules, function($a, $b) {
            $type_compare = strcmp($a['type'], $b['type']);
            if ($type_compare === 0) {
                return strcmp($b['date'], $a['date']);
            }
            return $type_compare;
        });
        
        return $rules;
    }

    /**
     * 获取家规家训详情
     *
     * @param Tree   $tree
     * @param string $rule_xref
     *
     * @return array|null
     */
    public static function getFamilyRuleDetails(Tree $tree, string $rule_xref): ?array
    {
        $record_factory = Registry::gedcomRecordFactory();
        $record = $record_factory->make($rule_xref, $tree);
        
        if (!$record || $record->tag() !== '_RULE') {
            return null;
        }
        
        $name = $record->facts(['NAME'])->first()?->value() ?? '';
        $type = $record->facts(['TYPE'])->first()?->value() ?? '';
        $content = $record->facts(['NOTE'])->first()?->value() ?? '';
        $author = $record->facts(['_AUTH'])->first()?->value() ?? '';
        $date = $record->facts(['DATE'])->first()?->value() ?? '';
        
        return [
            'xref'    => $record->xref(),
            'name'    => $name,
            'type'    => $type,
            'content' => $content,
            'author'  => $author,
            'date'    => $date
        ];
    }

    /**
     * 删除家规家训
     *
     * @param Tree   $tree
     * @param string $rule_xref
     *
     * @return bool
     */
    public static function deleteFamilyRule(Tree $tree, string $rule_xref): bool
    {
        $record_factory = Registry::gedcomRecordFactory();
        $record = $record_factory->make($rule_xref, $tree);
        
        if (!$record || $record->tag() !== '_RULE') {
            return false;
        }
        
        $record->delete();
        return true;
    }

    /**
     * 按类型获取家规家训
     *
     * @param Tree   $tree
     * @param string $rule_type
     *
     * @return array
     */
    public static function getFamilyRulesByType(Tree $tree, string $rule_type): array
    {
        $all_rules = self::getAllFamilyRules($tree);
        $filtered_rules = [];
        
        foreach ($all_rules as $rule) {
            if ($rule['type'] === $rule_type) {
                $filtered_rules[] = $rule;
            }
        }
        
        return $filtered_rules;
    }
}
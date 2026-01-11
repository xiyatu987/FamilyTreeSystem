<?php

/**
 * 中国族谱管理系统 - 祠堂信息管理模块
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
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\GedcomRecord;

/**
 * 祠堂信息管理模块
 */
class AncestralHallModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('祠堂信息管理');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('管理宗族祠堂的基本信息、历史沿革、修缮记录等。');
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
     * 创建或更新祠堂信息
     *
     * @param Tree   $tree
     * @param string $hall_name
     * @param string $location
     * @param string $built_date
     * @param string $description
     * @param string $history
     *
     * @return string|null 祠堂记录的ID
     */
    public static function createOrUpdateAncestralHall(
        Tree $tree,
        string $hall_name,
        string $location,
        string $built_date,
        string $description,
        string $history
    ): ?string {
        // 首先查找是否已存在同名祠堂
        $existing_hall = self::findAncestralHallByName($tree, $hall_name);
        
        // 创建GEDCOM数据
        $gedcom = "0 @_A$hall_name@ _ANCH\n" .
                  "1 NAME $hall_name\n" .
                  "1 PLAC $location\n" .
                  "1 DATE $built_date\n" .
                  "1 NOTE $description\n" .
                  "1 _HIST $history\n";
        
        if ($existing_hall) {
            // 更新现有祠堂
            $existing_hall->updateGedcom($gedcom);
            return $existing_hall->xref();
        } else {
            // 创建新祠堂
            $record_factory = Registry::gedcomRecordFactory();
            $hall = $record_factory->create($gedcom, $tree, '_ANCH');
            return $hall?->xref();
        }
    }

    /**
     * 根据名称查找祠堂
     *
     * @param Tree   $tree
     * @param string $hall_name
     *
     * @return GedcomRecord|null
     */
    public static function findAncestralHallByName(Tree $tree, string $hall_name): ?GedcomRecord
    {
        $record_factory = Registry::gedcomRecordFactory();
        $records = $record_factory->records($tree, '_ANCH');
        
        foreach ($records as $record) {
            if ($record->facts(['NAME'])->first()?->value() === $hall_name) {
                return $record;
            }
        }
        
        return null;
    }

    /**
     * 获取所有祠堂列表
     *
     * @param Tree $tree
     *
     * @return array
     */
    public static function getAllAncestralHalls(Tree $tree): array
    {
        $record_factory = Registry::gedcomRecordFactory();
        $records = $record_factory->records($tree, '_ANCH');
        $halls = [];
        
        foreach ($records as $record) {
            $name = $record->facts(['NAME'])->first()?->value() ?? '';
            $location = $record->facts(['PLAC'])->first()?->value() ?? '';
            $built_date = $record->facts(['DATE'])->first()?->value() ?? '';
            
            if ($name !== '') {
                $halls[] = [
                    'xref' => $record->xref(),
                    'name' => $name,
                    'location' => $location,
                    'built_date' => $built_date
                ];
            }
        }
        
        // 按名称排序
        usort($halls, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        return $halls;
    }

    /**
     * 获取祠堂详情
     *
     * @param Tree   $tree
     * @param string $hall_xref
     *
     * @return array|null
     */
    public static function getAncestralHallDetails(Tree $tree, string $hall_xref): ?array
    {
        $record_factory = Registry::gedcomRecordFactory();
        $record = $record_factory->make($hall_xref, $tree);
        
        if (!$record || $record->tag() !== '_ANCH') {
            return null;
        }
        
        $name = $record->facts(['NAME'])->first()?->value() ?? '';
        $location = $record->facts(['PLAC'])->first()?->value() ?? '';
        $built_date = $record->facts(['DATE'])->first()?->value() ?? '';
        $description = $record->facts(['NOTE'])->first()?->value() ?? '';
        $history = $record->facts(['_HIST'])->first()?->value() ?? '';
        
        return [
            'xref' => $record->xref(),
            'name' => $name,
            'location' => $location,
            'built_date' => $built_date,
            'description' => $description,
            'history' => $history
        ];
    }

    /**
     * 删除祠堂
     *
     * @param Tree   $tree
     * @param string $hall_xref
     *
     * @return bool
     */
    public static function deleteAncestralHall(Tree $tree, string $hall_xref): bool
    {
        $record_factory = Registry::gedcomRecordFactory();
        $record = $record_factory->make($hall_xref, $tree);
        
        if (!$record || $record->tag() !== '_ANCH') {
            return false;
        }
        
        $record->delete();
        return true;
    }
}
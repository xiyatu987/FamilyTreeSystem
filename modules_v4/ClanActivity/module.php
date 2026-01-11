<?php

/**
 * 中国族谱管理系统 - 宗族活动记录模块
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
use Fisharebest\Webtrees\Individual;

/**
 * 宗族活动记录模块
 */
class ClanActivityModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('宗族活动记录');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('记录和管理宗族的各类活动，如祭祀、修谱、聚会等。');
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
     * 创建或更新宗族活动
     *
     * @param Tree   $tree
     * @param string $activity_name
     * @param string $activity_type
     * @param string $date
     * @param string $location
     * @param string $description
     * @param array  $participants
     *
     * @return string|null 活动记录的ID
     */
    public static function createOrUpdateClanActivity(
        Tree $tree,
        string $activity_name,
        string $activity_type,
        string $date,
        string $location,
        string $description,
        array $participants
    ): ?string {
        // 创建GEDCOM数据
        $participant_list = '';
        foreach ($participants as $participant_xref) {
            $participant_list .= "1 _PART @$participant_xref@\n";
        }
        
        $gedcom = "0 @_ACT$activity_name@ _ACTI\n" .
                  "1 NAME $activity_name\n" .
                  "1 TYPE $activity_type\n" .
                  "1 DATE $date\n" .
                  "1 PLAC $location\n" .
                  "1 NOTE $description\n" .
                  $participant_list;
        
        // 查找是否已存在相同活动
        $existing_activity = self::findClanActivityByDetails($tree, $activity_name, $date);
        
        if ($existing_activity) {
            // 更新现有活动
            $existing_activity->updateGedcom($gedcom);
            return $existing_activity->xref();
        } else {
            // 创建新活动
            $record_factory = Registry::gedcomRecordFactory();
            $activity = $record_factory->create($gedcom, $tree, '_ACTI');
            return $activity?->xref();
        }
    }

    /**
     * 根据名称和日期查找活动
     *
     * @param Tree   $tree
     * @param string $activity_name
     * @param string $date
     *
     * @return GedcomRecord|null
     */
    public static function findClanActivityByDetails(Tree $tree, string $activity_name, string $date): ?GedcomRecord
    {
        $record_factory = Registry::gedcomRecordFactory();
        $records = $record_factory->records($tree, '_ACTI');
        
        foreach ($records as $record) {
            if ($record->facts(['NAME'])->first()?->value() === $activity_name &&
                $record->facts(['DATE'])->first()?->value() === $date) {
                return $record;
            }
        }
        
        return null;
    }

    /**
     * 获取所有宗族活动列表
     *
     * @param Tree $tree
     * @param int  $limit
     *
     * @return array
     */
    public static function getAllClanActivities(Tree $tree, int $limit = 100): array
    {
        $record_factory = Registry::gedcomRecordFactory();
        $records = $record_factory->records($tree, '_ACTI');
        $activities = [];
        $count = 0;
        
        foreach ($records as $record) {
            if ($count >= $limit) break;
            
            $name = $record->facts(['NAME'])->first()?->value() ?? '';
            $type = $record->facts(['TYPE'])->first()?->value() ?? '';
            $date = $record->facts(['DATE'])->first()?->value() ?? '';
            $location = $record->facts(['PLAC'])->first()?->value() ?? '';
            
            if ($name !== '') {
                $activities[] = [
                    'xref' => $record->xref(),
                    'name' => $name,
                    'type' => $type,
                    'date' => $date,
                    'location' => $location
                ];
                $count++;
            }
        }
        
        // 按日期倒序排序
        usort($activities, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        return $activities;
    }

    /**
     * 获取活动详情
     *
     * @param Tree   $tree
     * @param string $activity_xref
     *
     * @return array|null
     */
    public static function getClanActivityDetails(Tree $tree, string $activity_xref): ?array
    {
        $record_factory = Registry::gedcomRecordFactory();
        $record = $record_factory->make($activity_xref, $tree);
        
        if (!$record || $record->tag() !== '_ACTI') {
            return null;
        }
        
        $name = $record->facts(['NAME'])->first()?->value() ?? '';
        $type = $record->facts(['TYPE'])->first()?->value() ?? '';
        $date = $record->facts(['DATE'])->first()?->value() ?? '';
        $location = $record->facts(['PLAC'])->first()?->value() ?? '';
        $description = $record->facts(['NOTE'])->first()?->value() ?? '';
        
        // 获取参与者列表
        $participants = [];
        foreach ($record->facts(['_PART']) as $fact) {
            $participant_xref = substr($fact->value(), 1, -1); // 移除@符号
            $participant = Registry::gedcomRecordFactory()->make($participant_xref, $tree);
            if ($participant instanceof Individual) {
                $participants[] = [
                    'xref' => $participant->xref(),
                    'name' => $participant->fullName()
                ];
            }
        }
        
        return [
            'xref' => $record->xref(),
            'name' => $name,
            'type' => $type,
            'date' => $date,
            'location' => $location,
            'description' => $description,
            'participants' => $participants
        ];
    }

    /**
     * 删除宗族活动
     *
     * @param Tree   $tree
     * @param string $activity_xref
     *
     * @return bool
     */
    public static function deleteClanActivity(Tree $tree, string $activity_xref): bool
    {
        $record_factory = Registry::gedcomRecordFactory();
        $record = $record_factory->make($activity_xref, $tree);
        
        if (!$record || $record->tag() !== '_ACTI') {
            return false;
        }
        
        $record->delete();
        return true;
    }
}
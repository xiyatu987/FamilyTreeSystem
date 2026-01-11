<?php

/**
 * 中国族谱管理系统 - 迁徙与祖籍记录模块
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
use Fisharebest\Webtrees\Fact;

/**
 * 迁徙与祖籍记录模块
 */
class MigrationModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('迁徙与祖籍记录');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('记录和管理宗族成员的迁徙历史和祖籍信息。');
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
     * 获取人员的祖籍信息
     *
     * @param Individual $individual
     *
     * @return string
     */
    public static function getAncestralHome(Individual $individual): string
    {
        $fact = $individual->facts(['_AHOM'])->first();
        if ($fact instanceof Fact) {
            return $fact->value();
        }

        return '';
    }

    /**
     * 设置人员的祖籍信息
     *
     * @param Individual $individual
     * @param string     $ancestral_home
     */
    public static function setAncestralHome(Individual $individual, string $ancestral_home): void
    {
        // 首先删除现有的祖籍信息
        foreach ($individual->facts(['_AHOM']) as $fact) {
            $individual->deleteFact($fact->id());
        }

        // 创建新的祖籍信息
        $gedcom = "1 _AHOM $ancestral_home";
        $individual->createFact($gedcom, true);
    }

    /**
     * 添加迁徙记录
     *
     * @param Individual $individual
     * @param string     $from_location
     * @param string     $to_location
     * @param string     $date
     * @param string     $reason
     */
    public static function addMigrationRecord(Individual $individual, string $from_location, string $to_location, string $date, string $reason): void
    {
        // 创建新的迁徙记录
        $gedcom = "1 _MIGR\n" .
                  "2 FROM $from_location\n" .
                  "2 TO $to_location\n" .
                  "2 DATE $date\n" .
                  "2 REAS $reason\n";
        
        $individual->createFact($gedcom, true);
    }

    /**
     * 获取人员的所有迁徙记录
     *
     * @param Individual $individual
     *
     * @return array
     */
    public static function getMigrationRecords(Individual $individual): array
    {
        $records = [];
        
        foreach ($individual->facts(['_MIGR']) as $fact) {
            $from = $fact->attribute('FROM') ?? '';
            $to = $fact->attribute('TO') ?? '';
            $date = $fact->attribute('DATE') ?? '';
            $reason = $fact->attribute('REAS') ?? '';
            
            $records[] = [
                'id'         => $fact->id(),
                'from'       => $from,
                'to'         => $to,
                'date'       => $date,
                'reason'     => $reason
            ];
        }
        
        return $records;
    }

    /**
     * 删除迁徙记录
     *
     * @param Individual $individual
     * @param string     $fact_id
     */
    public static function deleteMigrationRecord(Individual $individual, string $fact_id): void
    {
        $fact = $individual->fact($fact_id);
        if ($fact instanceof Fact) {
            $individual->deleteFact($fact_id);
        }
    }

    /**
     * 获取人员的完整迁徙路径
     *
     * @param Individual $individual
     *
     * @return array
     */
    public static function getMigrationPath(Individual $individual): array
    {
        $path = [];
        
        // 添加祖籍
        $ancestral_home = self::getAncestralHome($individual);
        if ($ancestral_home !== '') {
            $path[] = [
                'location' => $ancestral_home,
                'type'     => 'ancestral',
                'date'     => '',
                'reason'   => '祖籍'
            ];
        }
        
        // 添加迁徙记录
        $records = self::getMigrationRecords($individual);
        foreach ($records as $record) {
            if (!empty($record['from'])) {
                $path[] = [
                    'location' => $record['from'],
                    'type'     => 'migration',
                    'date'     => $record['date'],
                    'reason'   => $record['reason']
                ];
            }
            
            if (!empty($record['to'])) {
                $path[] = [
                    'location' => $record['to'],
                    'type'     => 'migration',
                    'date'     => $record['date'],
                    'reason'   => $record['reason']
                ];
            }
        }
        
        // 添加当前居住地（假设为最新的迁徙目的地）
        $birth_place = $individual->facts(['BIRT'])->first()?->attribute('PLAC') ?? '';
        if ($birth_place !== '' && $birth_place !== $ancestral_home) {
            $path[] = [
                'location' => $birth_place,
                'type'     => 'birth',
                'date'     => $individual->facts(['BIRT'])->first()?->attribute('DATE') ?? '',
                'reason'   => '出生地'
            ];
        }
        
        return $path;
    }
}
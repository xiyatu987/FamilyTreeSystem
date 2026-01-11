<?php

/**
 * 中国族谱管理系统 - 墓地信息管理模块
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
 * 墓地信息管理模块
 */
class GraveModule extends AbstractModule implements ModuleCustomInterface
{
    use ModuleCustomTrait;

    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('墓地信息管理');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('记录和管理宗族成员的墓地信息。');
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
     * 获取人员的墓地信息
     *
     * @param Individual $individual
     *
     * @return array
     */
    public static function getGraveInfo(Individual $individual): array
    {
        $fact = $individual->facts(['_GRAV'])->first();
        if ($fact instanceof Fact) {
            return [
                'location' => $fact->value(),
                'plot'     => $fact->attribute('PLOT') ?? '',
                'coords'   => $fact->attribute('COORDS') ?? '',
                'epitaph'  => $fact->attribute('EPIT') ?? '',
                'picture'  => $fact->attribute('PICT') ?? ''
            ];
        }

        return [
            'location' => '',
            'plot'     => '',
            'coords'   => '',
            'epitaph'  => '',
            'picture'  => ''
        ];
    }

    /**
     * 设置人员的墓地信息
     *
     * @param Individual $individual
     * @param string     $location
     * @param string     $plot
     * @param string     $coords
     * @param string     $epitaph
     * @param string     $picture
     */
    public static function setGraveInfo(Individual $individual, string $location, string $plot = '', string $coords = '', string $epitaph = '', string $picture = ''): void
    {
        // 首先删除现有的墓地信息
        foreach ($individual->facts(['_GRAV']) as $fact) {
            $individual->deleteFact($fact->id());
        }

        // 创建新的墓地信息
        $gedcom = "1 _GRAV $location";
        if ($plot !== '') {
            $gedcom .= "\n2 PLOT $plot";
        }
        if ($coords !== '') {
            $gedcom .= "\n2 COORDS $coords";
        }
        if ($epitaph !== '') {
            $gedcom .= "\n2 EPIT $epitaph";
        }
        if ($picture !== '') {
            $gedcom .= "\n2 PICT $picture";
        }
        
        $individual->createFact($gedcom, true);
    }

    /**
     * 获取同一墓地的所有人员
     *
     * @param Individual $individual
     *
     * @return array
     */
    public static function getIndividualsInSameGrave(Individual $individual): array
    {
        $same_grave = [];
        $individual_grave = self::getGraveInfo($individual);
        
        if (!empty($individual_grave['location'])) {
            $tree = $individual->tree();
            $all_individuals = $tree->individuals();
            
            foreach ($all_individuals as $person) {
                if ($person->xref() !== $individual->xref()) {
                    $person_grave = self::getGraveInfo($person);
                    if ($person_grave['location'] === $individual_grave['location']) {
                        $same_grave[] = $person;
                    }
                }
            }
        }
        
        return $same_grave;
    }

    /**
     * 删除人员的墓地信息
     *
     * @param Individual $individual
     */
    public static function deleteGraveInfo(Individual $individual): void
    {
        foreach ($individual->facts(['_GRAV']) as $fact) {
            $individual->deleteFact($fact->id());
        }
    }
}
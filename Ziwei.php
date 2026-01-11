<?php

/**
 * 中国族谱管理系统 - 字辈自定义GEDCOM标签
 * Copyright (C) 2025 webtrees development team
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\CustomTags;

use Fisharebest\Webtrees\Gedcom\TagList;
use Fisharebest\Webtrees\Gedcom\TagListInterface;

/**
 * 自定义GEDCOM标签 - 字辈
 */
class Ziwei implements TagListInterface
{
    /**
     * {@inheritDoc}
     */
    public function tags(): array
    {
        return [
            // 个人记录中的字辈
            'INDI:_ZIWEI' => [
                'name'        => '字辈',
                'description' => '中国传统宗族中的字辈',
                'context'     => TagList::CONTEXT_INDIVIDUAL,
                'value'       => TagList::VALUE_TEXT,
                'subtags'     => [
                    'GENERATION' => [
                        'name'        => '世代',
                        'description' => '字辈对应的世代',
                        'value'       => TagList::VALUE_INTEGER,
                    ],
                    'ORDER' => [
                        'name'        => '排行',
                        'description' => '字辈在同世代中的排行',
                        'value'       => TagList::VALUE_INTEGER,
                    ],
                ],
            ],
            // 家谱树头部的字辈库
            'HEAD:_ZIWEI_LIBRARY' => [
                'name'        => '字辈库',
                'description' => '家谱树的字辈库',
                'context'     => TagList::CONTEXT_HEAD,
                'value'       => TagList::VALUE_NONE,
                'subtags'     => [
                    'NAME' => [
                        'name'        => '字辈库名称',
                        'description' => '字辈库的名称',
                        'value'       => TagList::VALUE_TEXT,
                    ],
                    'DESC' => [
                        'name'        => '字辈库描述',
                        'description' => '字辈库的描述',
                        'value'       => TagList::VALUE_TEXT,
                    ],
                    'ITEM' => [
                        'name'        => '字辈条目',
                        'description' => '字辈库中的一个字辈',
                        'value'       => TagList::VALUE_TEXT,
                        'subtags'     => [
                            'GENERATION' => [
                                'name'        => '世代',
                                'description' => '字辈对应的世代',
                                'value'       => TagList::VALUE_INTEGER,
                            ],
                            'DESC' => [
                                'name'        => '字辈描述',
                                'description' => '字辈的描述',
                                'value'       => TagList::VALUE_TEXT,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
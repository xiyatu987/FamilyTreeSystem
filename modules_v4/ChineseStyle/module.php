<?php
/**
 * 中国传统风格主题模块
 * 为webtrees添加中国传统风格样式
 */

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Theme\ThemeInterface;
use Fisharebest\Webtrees\Tree;

/**
 * Class ChineseStyle
 */
class ChineseStyle extends AbstractModule implements ThemeInterface
{
    /**
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Traditional Chinese Style');
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Traditional Chinese style theme with red accents and paper-like design');
    }

    /**
     * @return string
     */
    public function version(): string
    {
        return '2.2.4';
    }

    /**
     * @return string
     */
    public function cssTheme(): string
    {
        return __DIR__ . '/chinese_style.css';
    }

    /**
     * @return array
     */
    public function cssFiles(): array
    {
        return [
            __DIR__ . '/chinese_style.css',
        ];
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'chinesestyle';
    }

    /**
     * @return string
     */
    public function folder(): string
    {
        return 'ChineseStyle';
    }

    /**
     * @param Tree|null $tree
     *
     * @return string
     */
    public function customModuleIcon(Tree $tree = null): string
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isTheme(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function themeName(): string
    {
        return $this->title();
    }

    /**
     * @return array
     */
    public function themeDirs(): array
    {
        return [__DIR__ . '/views'];
    }
}

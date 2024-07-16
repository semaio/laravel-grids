<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableRegistry;

/**
 * Class FiltersRow
 *
 * provides additional render sections for columns: 'filters_row_column_<name>'
 */
class FiltersRow extends RenderableRegistry
{
    const NAME = 'filters_row';

    protected $template = '*.components.filters_row';

    protected $name = FiltersRow::NAME;

    protected $renderSection = self::SECTION_END;
}

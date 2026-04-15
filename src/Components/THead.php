<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\ComponentInterface;

/**
 * Class THead
 *
 * The component for rendering THEAD html tag inside grid.
 */
class THead extends HtmlTag
{
    const NAME = 'thead';

    /**
     * Returns default set of child components.
     *
     * @return ComponentInterface[]
     */
    protected function getDefaultComponents()
    {
        return [
            new ColumnHeadersRow,
            new FiltersRow,
        ];
    }
}

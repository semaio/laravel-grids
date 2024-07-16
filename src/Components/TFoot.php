<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

/**
 * Class TFoot
 *
 * The component for rendering TFOOT html tag inside grid.
 */
class TFoot extends HtmlTag
{
    const NAME = 'tfoot';

    /**
     * Returns default set of child components.
     *
     * @return \Nayjest\Grids\Components\Base\ComponentInterface[]
     */
    protected function getDefaultComponents()
    {
        return [
            (new OneCellRow)->addComponent(new Pager()),
        ];
    }
}

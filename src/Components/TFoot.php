<?php namespace Nayjest\Grids\Components;

use Illuminate\Foundation\Application;

/**
 * Class TFoot
 *
 * The component for rendering TFOOT html tag inside grid.
 *
 * @package Nayjest\Grids\Components
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

<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableComponent;

/**
 * Class ShowingRecords
 *
 * Renders text: Showing records $from — $to of $total
 */
class ShowingRecords extends RenderableComponent
{
    protected $template = '*.components.showing_records';

    /**
     * Passing $from, $to, $total to view
     *
     * @return mixed
     */
    protected function getViewData()
    {
        $paginator = $this->grid->getConfig()->getDataProvider()->getPaginator();
        $from = $paginator->firstItem();
        $to = $paginator->lastItem();
        $total = $paginator->total();

        return parent::getViewData() + compact('from', 'to', 'total');
    }
}

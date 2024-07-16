<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Grid;

/**
 * Class ColumnHeadersRow
 *
 * The component for rendering table row with column headers.
 */
class ColumnHeadersRow extends HtmlTag
{
    protected $tagName = 'tr';

    /**
     * Initializes component with grid
     *
     * @return null
     */
    public function initialize(Grid $grid)
    {
        $this->createHeaders($grid);
        parent::initialize($grid);
    }

    /**
     * Creates children components for rendering column headers.
     */
    protected function createHeaders(Grid $grid): void
    {
        foreach ($grid->getConfig()->getColumns() as $column) {
            $this->addComponent(new ColumnHeader($column));
        }
    }
}

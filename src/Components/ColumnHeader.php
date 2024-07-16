<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\FieldConfig;

/**
 * Class ColumnHeader
 *
 * The component for rendering column header
 */
class ColumnHeader extends TableCell
{
    protected $tagName = 'th';

    /**
     * @return $this
     */
    public function setColumn(FieldConfig $column)
    {
        $this->setContent($column->getLabel());

        if ($column->isSortable()) {
            $this->addComponent(new SortingControl($column));
        }

        return parent::setColumn($column);
    }
}

<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableComponent;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\FieldConfig;

/**
 * Class SortingControl
 *
 * The component for rendering sorting controls
 * added to column header automatically when field is sortable.
 */
class SortingControl extends RenderableComponent
{
    protected $template = '*.components.sorting_control';

    protected $column;

    protected $renderSection = RenderableRegistry::SECTION_END;

    /**
     * {@inheritdoc}
     */
    protected function getViewData()
    {
        return parent::getViewData() + ['column' => $this->column];
    }

    /**
     * Constructor.
     */
    public function __construct(FieldConfig $column)
    {
        $this->column = $column;
    }

    /**
     * Returns associated column.
     *
     * @return FieldConfig
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Sets associated column.
     */
    public function setColumn(FieldConfig $column)
    {
        $this->column = $column;
    }
}

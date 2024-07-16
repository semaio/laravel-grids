<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\FieldConfig;

/**
 * Class TableCell
 *
 * The component for rendering TD html tag inside grid.
 */
class TableCell extends HtmlTag
{
    protected $tagName = 'td';

    /**
     * @var FieldConfig
     */
    protected $column;

    /**
     * Constructor.
     */
    public function __construct(FieldConfig $column)
    {
        $this->setColumn($column);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        if (empty($this->attributes['class'])) {
            $this->attributes['class'] = 'column-'.$this->getColumn()->getName();
        }
        if ($this->getColumn()->getCellHtmlAttributes()) {
            foreach ($this->getColumn()->getCellHtmlAttributes() as $attribute => $value) {
                $this->attributes[$attribute] .= (empty($this->attributes[$attribute]) ? '' : ' ').$value;
            }
        }
        if (empty($this->attributes['data-label'])) {
            $this->attributes['data-label'] = $this->getColumn()->getLabel();
        }
        if ($this->column->isHidden()) {
            $this->attributes['style'] = 'display:none;';
        }

        return $this->attributes;
    }

    /**
     * Returns component name.
     * By default it's column_{$column_name}
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name ?: 'column_'.$this->column->getName();
    }

    /**
     * Returns associated column.
     *
     * @return FieldConfig $column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return $this
     */
    public function setColumn(FieldConfig $column)
    {
        $this->column = $column;

        return $this;
    }
}

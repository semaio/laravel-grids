<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableComponent;

/**
 * Class ColumnsHider
 *
 * The component renders control for showing/hiding columns.
 */
class ColumnsHider extends RenderableComponent
{
    protected $template = '*.components.columns_hider';

    protected $name = 'columns_hider';

    protected $hiddenByDefault = [];

    protected $title = 'Columns';

    /**
     * @param  array|string[]  $columnNames
     * @return $this
     */
    public function setHiddenByDefault(array $columnNames)
    {
        $this->hiddenByDefault = $columnNames;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getHiddenByDefault()
    {
        return $this->hiddenByDefault;
    }

    /**
     * @return array columnName => boolean
     */
    public function getColumnsVisibility()
    {
        $key = $this->getId('cookie');

        $fromCookie = [];
        if (isset($_COOKIE[$key])) {
            $fromCookie = json_decode($_COOKIE[$key], true);
        }

        $result = [];
        foreach ($this->grid->getConfig()->getColumns() as $column) {
            $name = $column->getName();
            if (isset($fromCookie[$name])) {
                $result[$name] = (bool) $fromCookie[$name];
            } else {
                $result[$name] = !in_array($name, $this->getHiddenByDefault());
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getId($name)
    {
        if ($name) {
            $name = "-$name";
        }

        $gridName = $this->grid->getConfig()->getName();

        return "{$gridName}-columns_hider{$name}";
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function prepare()
    {
        parent::prepare();

        $visible = $this->getColumnsVisibility();
        foreach ($this->grid->getConfig()->getColumns() as $column) {
            if (!$visible[$column->getName()]) {
                $column->hide();
            }
        }
    }
}

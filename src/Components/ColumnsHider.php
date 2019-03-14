<?php namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableComponent;

/**
 * Class ColumnsHider
 *
 * The component renders control for showing/hiding columns.
 *
 * @package Nayjest\Grids\Components
 */
class ColumnsHider extends RenderableComponent
{
    protected $template = '*.components.columns_hider';

    protected $name = 'columns_hider';

    protected $hiddenByDefault = [];

    protected $title = 'Columns';

    /**
     * @param array|string[] $columnNames
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
        if (isset($_COOKIE[$key])) {
            $fromCookie = json_decode($_COOKIE[$key], true);
        } else {
            $fromCookie = [];
        }
        $res = [];
        foreach ($this->grid->getConfig()->getColumns() as $column) {
            $name = $column->getName();
            if (isset($fromCookie[$name])) {
                $res[$name] = (boolean)$fromCookie[$name];
            } else {
                $res[$name] = !in_array($name, $this->getHiddenByDefault());
            }
        }

        return $res;
    }

    /**
     * @param $name
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
     * @param $title
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

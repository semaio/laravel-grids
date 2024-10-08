<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableComponent;

class Filter extends RenderableComponent
{
    protected $filteringFunc;

    protected $defaultValue;

    protected $template = '*.components.filters.input';

    protected $label;

    /**
     * Returns function that performs data filtering.
     *
     * Function will accept filter value as first argument and data provider
     * as second argument.
     *
     * @return callable
     */
    public function getFilteringFunc()
    {
        return $this->filteringFunc;
    }

    /**
     * Sets function for data filtering.
     *
     * Function will accept filter value as first argument and data provider
     * as second argument.
     *
     * @param  callable  $func
     * @return $this
     */
    public function setFilteringFunc($func)
    {
        $this->filteringFunc = $func;

        return $this;
    }

    public function getInputName()
    {
        $key = $this->grid->getInputProcessor()->getKey();

        return "{$key}[filters][{$this->name}]";
    }

    /**
     * Returns text label fo filtering control.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets text label for filtering control.
     *
     * @param  string|null  $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns default filter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Sets default filter value.
     *
     * @return $this
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * Returns filter value from input or default if there is no input.
     *
     * @return mixed
     */
    public function getValue()
    {
        $input = $this->grid->getInputProcessor()->getFilterValue($this->name);
        if ($input === null) {
            return $this->getDefaultValue();
        }

        return $input;
    }

    /**
     * Returns true if filter value exists.
     *
     * If filter has no value (from input or default), filtering function will not be called.
     * Null and empty string considered empty values.
     *
     * @return bool
     */
    protected function hasValue()
    {
        return $this->getValue() !== null && $this->getValue() !== '';
    }

    public function prepare()
    {
        if (!$this->hasValue()) {
            return;
        }
        $value = $this->getValue();
        if ($func = $this->getFilteringFunc()) {
            $func($value, $this->grid->getConfig()->getDataProvider());

            return;
        }
    }
}

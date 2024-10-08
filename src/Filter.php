<?php

declare(strict_types=1);

namespace Nayjest\Grids;

use Illuminate\Support\Facades\View;

class Filter
{
    /**
     * @var FilterConfig
     */
    protected $config;

    /**
     * @var FieldConfig
     */
    protected $column;

    /**
     * Constructor.
     */
    public function __construct(FilterConfig $config, FieldConfig $column, Grid $grid)
    {
        $this->config = $config;
        $this->column = $column;
        $this->grid = $grid;
    }

    /**
     * Returns input name for the filter.
     *
     * @return string
     */
    public function getInputName()
    {
        $key = $this->grid->getInputProcessor()->getKey();
        $name = $this->config->getId();

        return "{$key}[filters][{$name}]";
    }

    /**
     * Returns filter configuration.
     *
     * @return FilterConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns filters value.
     *
     * @return mixed
     */
    public function getValue()
    {
        $input = $this->grid->getInputProcessor()->getFilterValue($this->config->getId());
        if ($input === null) {
            return $this->config->getDefaultValue();
        } else {
            return $input;
        }
    }

    /**
     * Renders filtering control.
     *
     * @return string
     */
    public function render()
    {
        $data = $this->grid->getViewData();
        $data['column'] = $this->column;
        $data['filter'] = $this;
        $data['label'] = $this->config->getLabel();

        return View::make(
            $this->getTemplate(),
            $data
        )->render();
    }

    /**
     * Returns name of template for filtering control.
     *
     * @return string
     */
    protected function getTemplate()
    {
        $filterTemplate = $this->config->getTemplate();
        $gridTemplate = $this->grid->getConfig()->getTemplate();

        return str_replace('*.', "$gridTemplate.filters.", $filterTemplate);
    }

    /**
     * Applies filtering to data source.
     */
    public function apply()
    {
        $value = $this->getValue();
        if ($value === null || $value === '') {
            return;
        }
        if ($func = $this->config->getFilteringFunc()) {
            $func($value, $this->grid->getConfig()->getDataProvider());

            return;
        }
        // Add wildcards
        $operator = $this->config->getOperator();
        if ($operator === FilterConfig::OPERATOR_LIKE
            || $operator === FilterConfig::OPERATOR_LIKE_L
            || $operator === FilterConfig::OPERATOR_LIKE_R
        ) {
            $found = false;
            for ($i = 0; $i < mb_strlen($value); $i++) {
                if (in_array(mb_substr($value, $i, 1), ['%', '_']) && $i > 0 && mb_substr($value, $i - 1, 1) != '\\') {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                if ($operator === FilterConfig::OPERATOR_LIKE) {
                    $value = '%'.$value.'%';
                } elseif ($operator === FilterConfig::OPERATOR_LIKE_L) {
                    $value = '%'.$value;
                } elseif ($operator === FilterConfig::OPERATOR_LIKE_R) {
                    $value .= '%';
                }
            }
        }
        // Filter
        $this->grid->getConfig()->getDataProvider()->filter(
            $this->config->getName(),
            $this->config->getOperator(),
            $value
        );
    }
}

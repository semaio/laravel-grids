<?php

declare(strict_types=1);

namespace Nayjest\Grids;

use Illuminate\Support\Collection;

/**
 * Class FieldConfig
 *
 * This class describes grid column.
 */
class FieldConfig
{
    /**
     * Field name.
     *
     * @var string
     */
    protected $name;

    /**
     * Text label that will be rendered in table header.
     *
     * @var string
     */
    protected $label;

    /**
     * @var int
     */
    protected $order = 0;

    /**
     * @var bool
     */
    protected $isSortable = false;

    /**
     * @var string
     */
    protected $sorting;

    /**
     * @var Collection|FilterConfig[]
     */
    protected $filters;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var bool
     */
    protected $isHidden = false;

    /**
     * HTML Attributes to add to cells
     * [attributeName => value]
     *
     * @var array
     */
    protected $cellHtmlAttributes = [];

    /**
     * Constructor.
     *
     * @param  string|null  $name  column unique name for internal usage
     * @param  string|null  $label  column label
     */
    public function __construct($name = null, $label = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($label !== null) {
            $this->setLabel($label);
        }
    }

    /**
     * Returns column order.
     *
     * This property used to to identify column position in grid.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets column order.
     *
     * This property used to to identify column position in grid.
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Returns field name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets field name.
     *
     * @param  string  $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns true if column is hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->isHidden;
    }

    /**
     * Makes column hidden.
     *
     * @return $this
     */
    public function hide()
    {
        $this->isHidden = true;

        return $this;
    }

    /**
     * Makes column visible.
     *
     * @return $this
     */
    public function show()
    {
        $this->isHidden = false;

        return $this;
    }

    /**
     * Returns text label that will be rendered in table header.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label ?: ucwords(str_replace(['-', '_', '.'], ' ', $this->name));
    }

    /**
     * Sets text label that will be rendered in table header.
     *
     * @param  string  $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns true if column is sortable (sorting controls must be rendered).
     *
     * @return bool
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * Allows to enable or disable sorting controls for column.
     *
     * @param  bool  $isSortable
     * @return $this
     */
    public function setSortable($isSortable)
    {
        $this->isSortable = $isSortable;

        return $this;
    }

    /**
     * Returns current sorting order
     * or null if table rows are not sorted using this column.
     *
     * @return null|string null|Grid::SORT_ASC|Grid::SORT_DESC
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Allows to specify sorting by this column for data rows.
     *
     * @param  null|string  $sortOrder  null|Grid::SORT_ASC|Grid::SORT_DESC
     * @return $this
     */
    public function setSorting($sortOrder)
    {
        $this->sorting = $sortOrder;

        return $this;
    }

    /**
     * Returns true if data rows are sorted ascending using this column.
     *
     * @return bool
     */
    public function isSortedAsc()
    {
        return $this->sorting === Grid::SORT_ASC;
    }

    /**
     * Returns true if data rows are sorted descending using this column.
     *
     * @return bool
     */
    public function isSortedDesc()
    {
        return $this->sorting === Grid::SORT_DESC;
    }

    /**
     * Allows to set callback function that will render
     * content of table cells for this column.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Returns function that will render
     * content of table cells for this column if specified.
     *
     * @return callable|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Allows to specify filtering controls for column.
     *
     * @param  Collection|FilterConfig[]  $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        $this->filters = Collection::make($filters);
        foreach ($this->filters as $filterConfig) {
            $filterConfig->attach($this);
        }

        return $this;
    }

    /**
     * Allows to add filtering control to column.
     *
     * @return $this
     */
    public function addFilter(FilterConfig $filter)
    {
        $this->getFilters()->push($filter);
        $filter->attach($this);

        return $this;
    }

    /**
     * Creates instance of filtering control configuration
     * and binds it to the column.
     *
     * @param  string  $class
     * @return FilterConfig
     */
    public function makeFilter($class = '\Nayjest\Grids\FilterConfig')
    {
        $filter = new $class;
        $this->addFilter($filter);

        return $filter;
    }

    /**
     * Returns true if any filtering controls specified for the column.
     *
     * @return bool
     */
    public function hasFilters()
    {
        return !$this->getFilters()->isEmpty();
    }

    /**
     * Returns list of filtering controls specified for the column.
     *
     * @return Collection|FilterConfig[]
     */
    public function getFilters()
    {
        if ($this->filters === null) {
            $this->filters = new Collection();
        }

        return $this->filters;
    }

    /**
     * @todo move to Field instance
     *
     * @return mixed
     */
    public function getValue(DataRowInterface $row)
    {
        if ($function = $this->getCallback()) {
            return call_user_func($function, $row->getCellValue($this), $row);
        } else {
            return $row->getCellValue($this);
        }
    }

    /**
     * @return array
     */
    public function getCellHtmlAttributes()
    {
        return $this->cellHtmlAttributes;
    }

    /**
     * @param  array  $cellHtmlAttributes
     * @return FieldConfig
     */
    public function setCellHtmlAttributes($cellHtmlAttributes)
    {
        $this->cellHtmlAttributes = $cellHtmlAttributes;

        return $this;
    }
}

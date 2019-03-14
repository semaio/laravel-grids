<?php namespace Nayjest\Grids\Components\Filters;

use Carbon\Carbon;
use Nayjest\Grids\Components\Filter;
use Nayjest\Grids\DataProvider;

/**
 * Class DateRangePicker
 *
 * Date Range Picker for Bootstrap.
 * https://github.com/dangrossman/bootstrap-daterangepicker
 *
 * This component does not includes javascript & styles required to work with bootstrap-daterangepicker.
 * You need to include it manually to your pages/layout.
 *
 * @package Nayjest\Grids\Components\Filters
 */
class DateRangePicker extends Filter
{
    protected $jsOptions;

    protected $useClearButton;

    protected $template = '*.components.filters.date_range_picker';

    protected $isSubmittedOnChange = false;

    /**
     * Returns javascript options
     *
     * Available options:
     *
     * @see https://github.com/dangrossman/bootstrap-daterangepicker#options
     *
     * @return array
     */
    public function getJsOptions()
    {
        if (!$this->jsOptions) {
            $this->jsOptions = $this->getDefaultJsOptions();
        }

        return $this->jsOptions;
    }

    /**
     * Sets javascript options
     *
     * Available options:
     *
     * @see https://github.com/dangrossman/bootstrap-daterangepicker#options
     *
     * @param array $options
     */
    public function setJsOptions($options)
    {
        $this->jsOptions = $options;

        return $this;
    }

    /**
     * Returns true if form must be submitted immediately
     * when filter value selected.
     *
     * @return bool
     */
    public function isSubmittedOnChange()
    {
        return $this->isSubmittedOnChange;
    }

    /**
     * Allows to submit form immediately when filter value selected.
     *
     * @param bool $isSubmittedOnChange
     * @return $this
     */
    public function setSubmittedOnChange($isSubmittedOnChange)
    {
        $this->isSubmittedOnChange = $isSubmittedOnChange;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartValue()
    {
        $startInput = $this->grid
            ->getInputProcessor()
            ->getFilterValue($this->name . '_start');

        if ($startInput === null) {
            return $this->getDefaultStartValue();
        } else {
            return $startInput;
        }
    }

    /**
     * @return mixed
     */
    public function getEndValue()
    {
        $endInput = $this->grid
            ->getInputProcessor()
            ->getFilterValue($this->name . '_end');
        if ($endInput === null) {
            return $this->getDefaultEndValue();
        } else {
            return $endInput;
        }
    }

    /**
     * @return array|mixed
     */
    public function getValue()
    {
        return [$this->getStartValue(), $this->getEndValue()];
    }

    /**
     * Returns true if non-empty value specified for the filter.
     *
     * @return bool
     */
    protected function hasValue()
    {
        list($start, $end) = $this->getValue();

        return $start !== null && $start !== '' && $end !== null && $end !== '';
    }

    /**
     * Returns default javascript options
     *
     * Available options:
     *
     * @see https://github.com/dangrossman/bootstrap-daterangepicker#options
     *
     * @return array
     * @throws \Exception
     */
    protected function getDefaultJsOptions()
    {
        $carbon = new Carbon();
        $previousMonth = Carbon::now()->startOfMonth()->subWeek();
        $today = Carbon::now();
        $res = [
            'format' => 'YYYY-MM-DD',
            'ranges' => [
                'previous_month' => [
                    'Previous month (' . $previousMonth->format('F') . ')',
                    [
                        $previousMonth->startOfMonth()->format('Y-m-d'),
                        $previousMonth->endOfMonth()->format('Y-m-d'),
                    ],
                ],
                'current_month'  => [
                    'Cur. month (' . date('F') . ')',
                    [
                        $carbon->startOfMonth()->format('Y-m-d'),
                        $carbon->endOfMonth()->format('Y-m-d'),
                    ],
                ],
                'last_week'      => [
                    'This Week',
                    [
                        $carbon->startOfWeek()->format('Y-m-d'),
                        $carbon->endOfWeek()->format('Y-m-d'),
                    ],
                ],
                'last_14'        => [
                    'Last 14 days',
                    [
                        Carbon::now()->subDays(13)->format('Y-m-d'),
                        $today->format('Y-m-d'),
                    ],
                ],

            ],
        ];
        // will not set dates when '' passed but set default date when null passed
        if ($this->getStartValue()) {
            $res['startDate'] = $this->getStartValue();
        }
        if ($this->getEndValue()) {
            $res['endDate'] = $this->getEndValue();
        }

        return $res;
    }

    /**
     * @return mixed
     */
    public function getDefaultStartValue()
    {
        return $this->getDefaultValue()[0];
    }

    /**
     * @return mixed
     */
    public function getDefaultEndValue()
    {
        return $this->getDefaultValue()[1];
    }

    /**
     * Returns default filter value as [$startDate, $endDate]
     *
     * @return array
     */
    public function getDefaultValue()
    {
        return is_array($this->defaultValue) ? $this->defaultValue : [
            Carbon::now()->subWeek()->format('Y-m-d'),
            Carbon::now()->format('Y-m-d'),
        ];
    }

    /**
     * @return string
     */
    public function getStartInputName()
    {
        $key = $this->grid->getInputProcessor()->getKey();

        return "{$key}[filters][{$this->name}_start]";
    }

    /**
     * @return string
     */
    public function getEndInputName()
    {
        $key = $this->grid->getInputProcessor()->getKey();

        return "{$key}[filters][{$this->name}_end]";
    }

    /**
     * @return callable|\Closure
     */
    public function getFilteringFunc()
    {
        if (!$this->filteringFunc) {
            $this->filteringFunc = $this->getDefaultFilteringFunc();
        }

        return $this->filteringFunc;
    }

    /**
     * @return \Closure
     */
    protected function getDefaultFilteringFunc()
    {
        return function ($value, DataProvider $provider) {
            $provider->filter($this->getName(), '>=', $value[0]);
            $provider->filter($this->getName(), '<=', $value[1]);
        };
    }
}

<?php

declare(strict_types=1);

namespace Nayjest\Grids;

class SelectFilterConfig extends FilterConfig
{
    protected $template = '*.select';

    protected $options = [];

    protected $isSubmittedOnChange = false;

    protected $size = null;

    protected $multipleMode = false;

    protected $hideNoneOption = false;

    /**
     * Returns option items of html select tag.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets option items for html select tag.
     *
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

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
     * @param  bool  $isSubmittedOnChange
     * @return $this
     */
    public function setSubmittedOnChange($isSubmittedOnChange)
    {
        $this->isSubmittedOnChange = $isSubmittedOnChange;

        return $this;
    }

    /**
     * Sets the size of the select element.
     *
     * @param  int  $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Returns the size of the select element.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Enabled multiple mode.
     * This will switch the selected operator to IN, as any other operator does not work with multiple selections.
     *
     *
     * @return $this
     */
    public function setMultipleMode($multipleMode)
    {
        $this->multipleMode = $multipleMode;

        if ($multipleMode) {
            $this->operator = FilterConfig::OPERATOR_IN;
        }

        return $this;
    }

    /**
     * Returns true if the multiple mode is enabled.
     *
     * @return bool
     */
    public function isMultipleMode()
    {
        return $this->multipleMode;
    }

    /**
     * Returns if the "--//--" option with no value is hidden
     *
     * @return bool
     */
    public function isNoneOptionHidden()
    {
        return $this->hideNoneOption;
    }

    /**
     * Enable/disable hiding of the "--//--" option with no value
     *
     * @param  bool  $hideNoneOption
     * @return $this
     */
    public function setHideNoneOption($hideNoneOption)
    {
        $this->hideNoneOption = $hideNoneOption;

        return $this;
    }
}

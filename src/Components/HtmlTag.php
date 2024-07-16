<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableRegistry;
use Spatie\Html\Html;

class HtmlTag extends RenderableRegistry
{
    /**
     * @var string
     */
    protected $tagName;

    /**
     * @var string
     */
    protected $content;

    /**
     * HTML tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Returns component name.
     * If empty, tag_name will be used instead
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name ?: $this->getTagName();
    }

    /**
     * Allows to specify HTML tag.
     *
     * @param  string  $name
     * @return $this
     */
    public function setTagName($name)
    {
        $this->tagName = $name;

        return $this;
    }

    /**
     * Returns HTML tag.
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName ?: $this->suggestTagName();
    }

    /**
     * Suggests tag name by class name.
     *
     * @return string
     */
    private function suggestTagName()
    {
        $className = get_class($this);
        $parts = explode('\\', $className);
        $baseName = array_pop($parts);

        return ($baseName === 'HtmlTag') ? 'div' : strtolower($baseName);
    }

    /**
     * Sets content (html inside tag).
     *
     * @param  string  $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns html inside tag.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets html tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Returns html tag attributes.
     * Keys are attribute names and values are attribute values.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Renders opening tag.
     *
     * @return string
     */
    public function renderOpeningTag()
    {
        return '<'.$this->getTagName().$this->attributes($this->getAttributes()).'>';
    }

    /**
     * Renders closing tag.
     *
     * @return string
     */
    public function renderClosingTag()
    {
        return "</{$this->getTagName()}>";
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($this->getTemplate()) {
            $inner = $this->renderTemplate();
        } else {
            $this->isRendered = true;
            $inner = $this->renderOpeningTag()
                .$this->renderComponents(self::SECTION_BEGIN)
                .$this->getContent()
                .$this->renderComponents(null)
                .$this->renderComponents(self::SECTION_END)
                .$this->renderClosingTag();
        }

        return $this->wrapWithOutsideComponents($inner);
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    public function attributes($attributes)
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        // This will convert HTML attributes such as "required" to a correct
        // form instead of using incorrect numerics.
        if (is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties
        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="'.implode(' ', $value).'"';
        }

        if (!is_null($value)) {
            return $key.'="'.e($value, false).'"';
        }
    }
}

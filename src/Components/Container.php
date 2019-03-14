<?php namespace Nayjest\Grids\Components;

use Nayjest\Grids\Components\Base\RenderableRegistry;

class Container extends RenderableRegistry
{
    /**
     * @var array
     */
    protected $htmlTags = ['div'];

    /**
     * @param array $tags
     * @return $this
     */
    public function setHtmlTags(array $tags)
    {
        $this->htmlTags = $tags;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $before = '';
        $after = '';
        foreach ($this->htmlTags as $tag) {
            $before .= "<$tag>";
            $after = "</$tag>" . $after;
        }

        return $before . $this->renderComponents() . $after;
    }
}

<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components\Base;

trait TComponentView
{
    use TRenderable {
        TRenderable::getTemplate as private getTemplateInternal;
    }

    protected $renderSection;

    /**
     * Returns variables for usage inside view template.
     *
     * @return array
     */
    protected function getViewData()
    {
        return $this->grid->getViewData() + ['component' => $this];
    }

    /**
     * Returns name of view template.
     *
     * @return string
     */
    public function getTemplate()
    {
        $gridTemplate = $this->grid->getConfig()->getTemplate();

        return str_replace('*.', "$gridTemplate.", (string) $this->template);
    }

    /**
     * Returns name of section in parent component
     * where this component must be rendered.
     *
     * @return string|null
     */
    public function getRenderSection()
    {
        return $this->renderSection;
    }

    /**
     * Sets name of section in parent component
     * where this component must be rendered.
     *
     * @param  string|null  $sectionName
     * @return $this
     */
    public function setRenderSection($sectionName)
    {
        $this->renderSection = $sectionName;

        return $this;
    }
}

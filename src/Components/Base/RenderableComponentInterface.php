<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components\Base;

/**
 * Interface RenderableComponentInterface
 *
 * Interface for grid components that can be rendered.
 */
interface RenderableComponentInterface extends ComponentInterface, RenderableInterface
{
    /**
     * Returns section (named placeholder in parent object markup)
     * where component must be rendered.
     *
     * @return string|null
     */
    public function getRenderSection();

    /**
     * Sets section (named placeholder in parent object markup)
     * where component must be rendered.
     *
     * @param  string|null  $sectionName
     * @return $this
     */
    public function setRenderSection($sectionName);
}

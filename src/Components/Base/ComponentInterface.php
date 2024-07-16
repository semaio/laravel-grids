<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components\Base;

use Nayjest\Grids\Grid;

/**
 * Interface ComponentInterface
 *
 * Interface of Grid component.
 *
 * Basically, component is an object that can be attached
 * to grid components hierarchy and react to initialize & prepare calls.
 */
interface ComponentInterface extends TaggableInterface
{
    /**
     * Attaches component to registry.
     *
     * @return null
     */
    public function attachTo(RegistryInterface $parent);

    /**
     * Returns parent object.
     *
     * @return RegistryInterface
     */
    public function getParent();

    /**
     * Initializes component with grid.
     *
     * @return null
     */
    public function initialize(Grid $grid);

    /**
     * Performs all required operations before rendering component.
     *
     * @return mixed
     */
    public function prepare();

    /**
     * Returns component name.
     *
     * @return string|null
     */
    public function getName();

    /**
     * Sets component name.
     *
     * @param  string  $name
     * @return $this
     */
    public function setName($name);
}

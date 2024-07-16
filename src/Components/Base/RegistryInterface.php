<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components\Base;

use Illuminate\Support\Collection;

/**
 * Interface RegistryInterface
 *
 * Interface of Grid components registry
 */
interface RegistryInterface
{
    /**
     * Returns collection of attached components.
     *
     * @return Collection|ComponentInterface[]|array
     */
    public function getComponents();

    /**
     * Returns child component
     * with specified name or null if component not found.
     *
     * @param  string  $name
     * @return ComponentInterface|null
     */
    public function getComponentByName($name);

    /**
     * Adds component to collection.
     *
     * @return $this
     */
    public function addComponent(ComponentInterface $component);

    /**
     * Sets children components collection.
     *
     * @param  Collection|ComponentInterface[]|array  $components
     * @return $this
     */
    public function setComponents($components);

    /**
     * Adds components to collection.
     *
     * @param  Collection|ComponentInterface[]|array  $components
     * @return $this
     */
    public function addComponents($components);

    /**
     * Creates component be class name,
     * attaches it to children collection
     * and returns this component as result.
     *
     * @param  string  $class
     * @return ComponentInterface
     */
    public function makeComponent($class);
}

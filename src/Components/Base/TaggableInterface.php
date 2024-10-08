<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components\Base;

/**
 * Interface TaggableInterface
 *
 * @deprecated
 */
interface TaggableInterface
{
    /**
     * @deprecated
     *
     * @return array
     */
    public function getTags();

    /**
     * @deprecated
     *
     * @return $this
     */
    public function setTags(array $tagNames);

    /**
     * @deprecated
     *
     * @param  string  $tagName
     * @return bool
     */
    public function hasTag($tagName);

    /**
     * @deprecated
     *
     * @param  array|string[]  $tagNames
     * @return bool
     */
    public function hasTags(array $tagNames);
}

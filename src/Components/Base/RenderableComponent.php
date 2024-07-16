<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components\Base;

/**
 * Class RenderableComponent
 *
 * Base class for components that can be rendered.
 */
class RenderableComponent implements RenderableComponentInterface
{
    use TComponent;
    use TComponentView;
}

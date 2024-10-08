<?php

declare(strict_types=1);

namespace Nayjest\Grids;

use Illuminate\Support\Collection;
use Nayjest\Grids\Components\Base\RegistryInterface;
use Nayjest\Grids\Components\Base\RenderableComponentInterface;
use Nayjest\Grids\Components\Base\TComponent;
use Nayjest\Grids\Components\Base\TRegistry;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\Components\Tr;

class GridConfig implements RegistryInterface
{
    use TComponent;
    use TRegistry;

    const SECTION_DO_NOT_RENDER = 'not_render';

    protected $template = 'grids::default';

    /**
     * @var FieldConfig[]|Collection
     */
    protected $columns;

    /**
     * @var DataProvider
     */
    protected $dataProvider;

    protected $pageSize = 50;

    /**
     * @var Collection|FilterConfig[]
     */
    protected $filters;

    /**
     * @var int
     */
    protected $cachingTime = 0;

    protected $mainTemplate = '*.grid';

    protected $rowComponent;

    protected $isResponsive = false;

    /**
     * @return RenderableComponentInterface
     */
    public function getRowComponent()
    {
        if (!$this->rowComponent) {
            $this->rowComponent = (new Tr)->setRenderSection(self::SECTION_DO_NOT_RENDER);
            if ($this->grid) {
                $this->rowComponent->initialize($this->grid);
            }
            $this->addComponent($this->rowComponent);
        }

        return $this->rowComponent;
    }

    /**
     * @return $this
     */
    public function setRowComponent(RenderableComponentInterface $rowComponent)
    {
        $this->rowComponent = $rowComponent;
        $this->addComponent($rowComponent);
        $rowComponent->setRenderSection(self::SECTION_DO_NOT_RENDER);

        return $this;
    }

    /**
     * Returns default child components.
     *
     *
     * @return \Illuminate\Support\Collection|Components\Base\ComponentInterface[]|array
     */
    protected function getDefaultComponents()
    {
        return [
            new THead,
            new TFoot,
        ];
    }

    /**
     * @param  string  $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function setMainTemplate($template)
    {
        $this->mainTemplate = $template;

        return $this;
    }

    public function getMainTemplate()
    {
        return str_replace('*.', "$this->template.", $this->mainTemplate);
    }

    /**
     * @param  Collection|FilterConfig[]  $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        $this->filters = Collection::make($filters);

        return $this;
    }

    public function getFilters()
    {
        if ($this->filters === null) {
            $this->filters = new Collection();
        }

        return $this->filters;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return $this
     */
    public function setDataProvider(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;

        return $this;
    }

    /**
     * @return DataProvider
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param  FieldConfig[]|Collection  $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = Collection::make($columns);

        return $this;
    }

    /**
     * Returns collection of grid columns.
     *
     * @return FieldConfig[]|Collection
     */
    public function getColumns()
    {
        if ($this->columns === null) {
            $this->columns = new Collection;
        }

        return $this->columns;
    }

    /**
     * Returns column by name.
     *
     * @param  string  $name
     * @return null|FieldConfig
     */
    public function getColumn($name)
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }
    }

    /**
     * Returns cache expiration time in minutes.
     *
     * @return int
     */
    public function getCachingTime()
    {
        return $this->cachingTime;
    }

    /**
     * Sets cache expiration time in seconds.
     *
     * @param  int  $seconds
     * @return $this
     */
    public function setCachingTime($seconds)
    {
        $this->cachingTime = $seconds;

        return $this;
    }

    /**
     * Adds column to grid.
     *
     * @return $this
     */
    public function addColumn(FieldConfig $column)
    {
        if ($this->columns === null) {
            $this->setColumns([]);
        }

        $this->columns->push($column);

        return $this;
    }

    /**
     * Sets maximal quantity of rows per page.
     *
     * @param  int  $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = (int) $pageSize;

        return $this;
    }

    /**
     * Returns maximal quantity of rows per page.
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * Checks if table should be responsive.
     *
     * @return bool
     */
    public function isResponsive()
    {
        return $this->isResponsive;
    }

    /**
     * Sets if table should be responsive.
     *
     * @param bool $isResponsive
     * @return $this
     */
    public function setIsResponsive($isResponsive)
    {
        $this->isResponsive = $isResponsive;

        return $this;
    }
}

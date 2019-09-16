<?php namespace Nayjest\Grids\Components;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Nayjest\Grids\Components\Base\RenderableComponent;
use Nayjest\Grids\Grid;

class Pager extends RenderableComponent
{
    protected $inputKey;

    protected $previousPageName;

    protected $name = 'pager';

    public function render()
    {
        $this->setupPaginationForLinks();
        $result = (string)$this->links();

        return $result;
    }

    protected function setupPaginationForReading()
    {
        Paginator::currentPageResolver(function () {
            return Request::input("$this->inputKey.page", 1);
        });
    }

    protected function setupPaginationForLinks()
    {
        /** @var  Paginator $paginator */
        $paginator = $this->grid->getConfig()->getDataProvider()->getPaginator();
        $paginator->setPageName("{$this->inputKey}[page]");
    }

    /**
     * Renders pagination links & returns rendered html.
     */
    protected function links()
    {
        /** @var  Paginator $paginator */
        $paginator = $this->grid->getConfig()->getDataProvider()->getPaginator();
        $input = $this->grid->getInputProcessor()->getInput();
        if (isset($input['page'])) {
            unset($input['page']);
        }

        return str_replace('/?', '?', $paginator->appends($this->inputKey, $input)->render());
    }

    public function initialize(Grid $grid)
    {
        parent::initialize($grid);
        $this->inputKey = $grid->getInputProcessor()->getKey();
        $this->setupPaginationForReading();
    }
}

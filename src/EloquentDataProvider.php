<?php namespace Nayjest\Grids;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentDataProvider extends DataProvider
{
    /**
     * @var
     */
    protected $collection;

    /**
     * @var
     */
    protected $paginator;

    /**
     * @var \ArrayIterator
     */
    protected $iterator;

    /**
     * Constructor.
     *
     * @param Builder $src
     */
    public function __construct(Builder $src)
    {
        parent::__construct($src);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->getIterator()->rewind();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $paginator = $this->getPaginator();
            $this->collection = Collection::make(
                $this->getPaginator()->items()
            );
        }

        return $this->collection;
    }

    /**
     * @return \Illuminate\Pagination\Paginator
     */
    public function getPaginator()
    {
        if (!$this->paginator) {
            $this->paginator = $this->src->paginate($this->pageSize);
        }

        return $this->paginator;
    }

    /**
     * @return \Illuminate\Pagination\Factory
     */
    public function getPaginationFactory()
    {
        return $this->src->getQuery()->getConnection()->getPaginator();
    }

    /**
     * @return \ArrayIterator
     */
    protected function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = $this->getCollection()->getIterator();
        }

        return $this->iterator;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->src;
    }

    /**
     * @return DataRow|EloquentDataRow|null
     */
    public function getRow()
    {
        if ($this->index < $this->count()) {
            $this->index++;
            $item = $this->iterator->current();
            $this->iterator->next();
            $row = new EloquentDataRow($item, $this->getRowId());
            Event::fire(self::EVENT_FETCH_ROW, [$row, $this]);

            return $row;
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getCollection()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($fieldName, $direction)
    {
        $this->src->orderBy($fieldName, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($fieldName, $operator, $value)
    {
        switch ($operator) {
            case 'like_l':
                $operator = 'like';
                break;
            case 'like_r':
                $operator = 'like';
                break;
            case 'eq':
                $operator = '=';
                break;
            case 'n_eq':
                $operator = '<>';
                break;
            case 'gt':
                $operator = '>';
                break;
            case 'lt':
                $operator = '<';
                break;
            case 'ls_e':
                $operator = '<=';
                break;
            case 'gt_e':
                $operator = '>=';
                break;
            case 'in':
                if (!is_array($value)) {
                    $operator = '=';
                    break;
                }
                $this->src->whereIn($fieldName, $value);

                return $this;
            case 'ft':
                // @codingStandardsIgnoreStart
                $this->src
                    ->select(DB::raw('*, match(' . $fieldName . ') against (' . DB::connection()->getPdo()->quote($value) . ' in boolean mode) as score'))
                    ->whereRaw('match(' . $fieldName . ') against (' . DB::connection()->getPdo()->quote($value) . ' in boolean mode)')
                    ->orderBy('score', 'desc');
                // @codingStandardsIgnoreEnd

                return $this;
        }
        $this->src->where($fieldName, $operator, $value);

        return $this;
    }
}

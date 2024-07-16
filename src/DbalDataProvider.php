<?php

declare(strict_types=1);

namespace Nayjest\Grids;

use App;
use ArrayIterator;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use PDO;

class DbalDataProvider extends DataProvider
{
    protected $collection;

    protected $paginator;

    /**
     * @var ArrayIterator
     */
    protected $iterator;

    /**
     * Set true if Laravel query logging required.
     * Fails when using Connection::PARAM_INT_ARRAY parameters
     *
     * @var bool
     */
    protected $execUsingLaravel = false;

    /**
     * Constructor.
     */
    public function __construct(QueryBuilder $src)
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
            $query = clone $this->src;
            $query
                ->setFirstResult(
                    ($this->getCurrentPage() - 1) * $this->pageSize
                )
                ->setMaxResults($this->pageSize);

            if ($this->isExecUsingLaravel()) {
                $res = DB::select($query, $query->getParameters());
            } else {
                $res = $query->execute()->fetchAll(PDO::FETCH_OBJ);
            }
            $this->collection = Collection::make($res);
        }

        return $this->collection;
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Pagination\Paginator
     */
    public function getPaginator()
    {
        if (!$this->paginator) {
            $items = $this->getCollection()->toArray();
            $this->paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $this->getTotalRowsCount(),
                $this->pageSize,
                $this->getCurrentPage(),
                [
                    'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
                ]
            );
        }

        return $this->paginator;
    }

    /**
     * @return \Illuminate\Pagination\Factory
     */
    public function getPaginationFactory()
    {
        return App::make('paginator');
    }

    /**
     * @return ArrayIterator
     */
    protected function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = $this->getCollection()->getIterator();
        }

        return $this->iterator;
    }

    /**
     * @return QueryBuilder
     */
    public function getBuilder()
    {
        return $this->src;
    }

    /**
     * @return DataRow|ObjectDataRow|null
     */
    public function getRow()
    {
        if ($this->index < $this->getCurrentPageRowsCount()) {
            $this->index++;
            $item = $this->iterator->current();
            $this->iterator->next();
            $row = new ObjectDataRow($item, $this->getRowId());
            Event::dispatch(self::EVENT_FETCH_ROW, [$row, $this]);

            return $row;
        } else {
            return null;
        }
    }

    /**
     * @deprecated
     *
     * @return int
     */
    public function count()
    {
        return $this->getCurrentPageRowsCount();
    }

    /**
     * @return mixed
     */
    public function getTotalRowsCount()
    {
        return $this->src->execute()->rowCount();
    }

    /**
     * @return int
     */
    public function getCurrentPageRowsCount()
    {
        return $this->getCollection()->count();
    }

    /**
     * @param  string  $fieldName
     * @param  string  $direction
     * @return $this|DataProvider
     */
    public function orderBy($fieldName, $direction)
    {
        $this->src->orderBy($fieldName, $direction);

        return $this;
    }

    /**
     * @param  string  $fieldName
     * @param  string  $operator
     * @param  mixed  $value
     * @return $this|DataProvider
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
                // may be broken, @see https://github.com/Nayjest/Grids/issues/109
                $operator = 'IN';
                if (!is_array($value)) {
                    $operator = '=';
                }
                break;
        }
        $parameterName = str_replace('.', '_', $fieldName); // @see https://github.com/Nayjest/Grids/issues/111
        $this->src->andWhere("$fieldName $operator :$parameterName");
        $this->src->setParameter($parameterName, $value);

        return $this;
    }

    /**
     * @return bool
     */
    public function isExecUsingLaravel()
    {
        return $this->execUsingLaravel;
    }

    /**
     * @param  bool  $execUsingLaravel
     */
    public function setExecUsingLaravel($execUsingLaravel)
    {
        $this->execUsingLaravel = $execUsingLaravel;
    }
}

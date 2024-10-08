<?php

declare(strict_types=1);

namespace Nayjest\Grids\Components;

use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Nayjest\Grids\Components\Base\RenderableComponent;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\DataProvider;
use Nayjest\Grids\DataRow;
use Nayjest\Grids\Grid;

/**
 * Class CsvExport
 *
 * The component provides control for exporting data to CSV.
 *
 * @author  Vitaliy Ofat <i@vitaliy-ofat.com>
 */
class CsvExport extends RenderableComponent
{
    const NAME = 'csv_export';

    const INPUT_PARAM = 'csv';

    const CSV_DELIMITER = ';';

    const CSV_EXT = '.csv';

    const DEFAULT_ROWS_LIMIT = 5000;

    protected $template = '*.components.csv_export';

    protected $name = CsvExport::NAME;

    protected $renderSection = RenderableRegistry::SECTION_END;

    protected $rowsLimit = self::DEFAULT_ROWS_LIMIT;

    /**
     * @var string
     */
    protected $output;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @return null|void
     */
    public function initialize(Grid $grid)
    {
        parent::initialize($grid);

        Event::listen(Grid::EVENT_PREPARE, function (Grid $grid) {
            if ($this->grid !== $grid) {
                return;
            }
            if ($grid->getInputProcessor()->getValue(static::INPUT_PARAM, false)) {
                $this->renderCsv();
            }
        });
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setFileName($name)
    {
        $this->fileName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName.static::CSV_EXT;
    }

    /**
     * @return int
     */
    public function getRowsLimit()
    {
        return $this->rowsLimit;
    }

    /**
     * @param  int  $limit
     * @return $this
     */
    public function setRowsLimit($limit)
    {
        $this->rowsLimit = $limit;

        return $this;
    }

    protected function setCsvHeaders(Response $response)
    {
        $response->header('Content-Type', 'application/csv');
        $response->header('Content-Disposition', 'attachment; filename='.$this->getFileName());
        $response->header('Pragma', 'no-cache');
    }

    protected function resetPagination(DataProvider $provider)
    {
        if (version_compare(Application::VERSION, '5.0.0', '<')) {
            $provider->getPaginationFactory()->setPageName('page_unused');
        } else {
            Paginator::currentPageResolver(function () {
                return 1;
            });
        }
        $provider->setPageSize($this->getRowsLimit());
        $provider->setCurrentPage(1);
    }

    protected function renderCsv()
    {
        $file = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$this->getFileName().'"');
        header('Pragma: no-cache');

        set_time_limit(0);

        /** @var $provider DataProvider */
        $provider = $this->grid->getConfig()->getDataProvider();

        $this->renderHeader($file);

        $this->resetPagination($provider);
        $provider->reset();
        /** @var DataRow $row */
        while ($row = $provider->getRow()) {
            $output = [];
            foreach ($this->grid->getConfig()->getColumns() as $column) {
                if (!$column->isHidden()) {
                    $output[] = $this->escapeString($column->getValue($row));
                }
            }
            fputcsv($file, $output, static::CSV_DELIMITER);
        }

        fclose($file);
        exit;
    }

    /**
     * @param  string  $str
     * @return string
     */
    protected function escapeString($str)
    {
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = str_replace('"', '\'', $str);
        $str = preg_replace('/\s+/', ' ', $str); // remove double spaces
        $str = trim($str);

        return $str;
    }

    /**
     * @param  resource  $file
     */
    protected function renderHeader($file)
    {
        $output = [];
        foreach ($this->grid->getConfig()->getColumns() as $column) {
            if (!$column->isHidden()) {
                $output[] = $this->escapeString($column->getLabel());
            }
        }
        fputcsv($file, $output, static::CSV_DELIMITER);
    }
}

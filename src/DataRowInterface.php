<?php

declare(strict_types=1);

namespace Nayjest\Grids;

/**
 * Interface DataRowInterface
 *
 * Interface for row of data received from data provider.
 */
interface DataRowInterface
{
    /**
     * Returns row ID (row number).
     *
     * @return int
     */
    public function getId();

    /**
     * Returns value of specified field.
     *
     * @param  string|FieldConfig  $field
     * @return mixed
     */
    public function getCellValue($field);
}

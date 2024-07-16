<?php

declare(strict_types=1);

namespace Nayjest\Grids;

/**
 * Class IdFieldConfig
 *
 * IdFieldConfig is a column type that will render a row number in table rows.
 */
class IdFieldConfig extends FieldConfig
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('ID', 'Id');
    }

    /**
     * Returns row id (row number).
     *
     * @return int
     */
    public function getValue(DataRowInterface $row)
    {
        return $row->getId();
    }
}

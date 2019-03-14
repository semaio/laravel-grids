<?php namespace Nayjest\Grids;

use Exception;
use RuntimeException;

class ObjectDataRow extends DataRow
{
    /**
     * @param string $fieldName
     * @return mixed
     * @throws Exception
     */
    protected function extractCellValue($fieldName)
    {
        if (strpos($fieldName, '.') !== false) {
            $parts = explode('.', $fieldName);
            $parts = array_reverse($parts);
            $res = $this->src;
            try {
                foreach ($parts as $part) {
                    return is_object($res) ? $res->{$part} : $res[$part];
                }
            } catch (Exception $e) {
                throw new RuntimeException(
                    "Can't read '$fieldName' as '$part' property from DataRow",
                    0,
                    $e
                );
            }
            throw new RuntimeException(
                "Can't read '$fieldName' property from DataRow",
                0
            );
        } else {
            try {
                return $this->src->{$fieldName};
            } catch (Exception $e) {
                throw new RuntimeException(
                    "Can't read '$fieldName' property from DataRow",
                    0,
                    $e
                );
            }
        }
    }
}

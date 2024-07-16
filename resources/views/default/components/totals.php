<?php
/**
 * @var TotalsRow $component
 */

use Nayjest\Grids\Components\TotalsRow;

?>
<tr>
    <?php foreach ($columns as $column) { ?>
        <td class="column-<?= $column->getName() ?>" <?= $column->isHidden() ? 'style="display:none"' : '' ?>>
            <?php
            if ($component->uses($column)) {
                $label = '';
                switch ($component->getFieldOperation($column->getName())) {
                    case \Nayjest\Grids\Components\TotalsRow::OPERATION_SUM:
                        $label = '∑';
                        break;
                    case \Nayjest\Grids\Components\TotalsRow::OPERATION_COUNT:
                        $label = 'Count';
                        break;
                    case \Nayjest\Grids\Components\TotalsRow::OPERATION_AVG:
                        $label = 'Avg.';
                        break;
                }
                echo $label, '&nbsp;', $column->getValue($component);
            }
        ?>
        </td>
    <?php } ?>
</tr>

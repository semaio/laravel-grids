<?php
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\FieldConfig;

// ========== FILTERS ROW ==========
/**
 * @var FiltersRow $component
 * @var FieldConfig $column
 */
?>
<?php if ($grid->getFiltering()->available()) { ?>
    <tr>
        <?php foreach ($columns as $column) { ?>
            <td class="column-<?= $column->getName() ?>" <?= $column->isHidden() ? 'style="display:none"' : '' ?>>
                <?php if ($column->hasFilters()) { ?>
                    <?php foreach ($column->getFilters() as $filter) { ?>
                        <?= $grid->getFiltering()->render($filter) ?>
                    <?php } ?>
                <?php } ?>
                <?= $component->renderComponents('filters_row_column_'.$column->getName()) ?>
            </td>
        <?php } ?>
        <?= $grid->getInputProcessor()->getSortingHiddenInputsHtml() ?>
    </tr>
<?php } ?>

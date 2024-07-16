<small style="white-space: nowrap">
    <a title="Sort ascending"
        <?php if ($column->isSortedAsc()) { ?>
            class="text-success"
        <?php } else { ?>
            href="<?= $grid->getSorter()->link($column, 'ASC') ?>"
        <?php } ?>
    >&#x25B2;</a>
    <a title="Sort descending"
        <?php if ($column->isSortedDesc()) { ?>
            class="text-success"
        <?php } else { ?>
            href="<?= $grid->getSorter()->link($column, 'DESC') ?>"
        <?php } ?>
    >&#x25BC;</a>
</small>

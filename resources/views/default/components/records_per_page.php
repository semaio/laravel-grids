<span>Records per page</span>
<select onchange="this.form.submit()" name="<?= $component->getInputName() ?>" class="form-control input-sm grids-control-records-per-page" style="display: inline; width: 80px; margin-right: 10px">
    <?php foreach ($component->getVariants() as $variant) { ?>
    <option value="<?= $variant ?>" <?php if ($variant === $component->getValue()) { ?>selected="selected"<?php } ?>><?= $variant ?></option>
    <?php } ?>
</select>

<?php
/** @var Nayjest\Grids\Components\SelectFilter $component */
?>
<?php if ($component->getLabel()) { ?>
    <span><?= $component->getLabel() ?></span>
<?php } ?>
<select onchange="this.form.submit()" name="<?= $component->getInputName() ?>" class="form-control input-sm" style="display: inline; width: 160px; margin-right: 10px">
    <?php foreach ($component->getVariants() as $variant) { ?>
    <option value="<?= $variant ?>" <?php if ($variant === $component->getValue()) { ?>selected="selected"<?php } ?>><?= $variant ?></option>
    <?php } ?>
</select>

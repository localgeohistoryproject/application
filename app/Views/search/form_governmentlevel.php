<?php
$form ??= '';
$type ??= '';
?>
<label class="forselectize" for="<?= $form ?>_governmentlevel"> <?= lang('Application.level') ?></label>
<a href="/<?= \Config\Services::request()->getLocale() ?>/key/#governmentlevel" class="forselectize" aria-label="<?= lang('Application.levelKey') ?>" title="<?= lang('Application.levelKey') ?>"><span class="keyiconfill">vpn_key</span></a>
<br>
<select id="<?= $form ?>_governmentlevel" name="governmentlevel" style="width: 300px;" required="required">
    <option></option>
    <?php if ($type !== 'statewide') { ?>
        <option value="Municipality"><?= lang('Application.municipality') ?></option>
    <?php } ?>
    <option value="County"><?= lang('Application.county') ?></option>
    <option value="State"><?= lang('Application.state') ?></option>
</select>
<br><br>
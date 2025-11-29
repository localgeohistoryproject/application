<?php
$form ??= '';
$isRequired ??= false;
?>
<label class="forselectize" for="<?= $form ?>_eventtype"> <?= lang('Application.eventType') ?></label>
<a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventtype" class="forselectize" aria-label="<?= lang('Application.eventTypeKey') ?>" title="<?= lang('Application.eventTypeKey') ?>"><span class="keyiconfill">vpn_key</span></a>
<br>
<select id="<?= $form ?>_eventtype" name="eventtype" style="width: 300px;" <?= ($isRequired ? ' required="required"' : '') ?>>
</select>
<br>
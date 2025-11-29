<?php $form ??= ''; ?>
<label class="forselectize" for="<?= $form ?>_tribunalgovernment"><?= lang('Application.government') ?>: </label>
<select id="<?= $form ?>_tribunalgovernment" name="tribunalgovernment" style="width: 300px;" required="required">
</select>
<br>
<label class="forselectize" for="<?= $form ?>_tribunaltype"><?= lang('Application.tribunal') ?>: </label>
<select id="<?= $form ?>_tribunaltype" name="tribunaltype" style="width: 300px;" required="required">
</select>
<br>
<label class="forselectize" for="<?= $form ?>_adjudicationtype"><?= lang('Application.type') ?>: </label>
<select id="<?= $form ?>_adjudicationtype" name="adjudicationtype" style="width: 300px;" required="required">
</select>
<br>
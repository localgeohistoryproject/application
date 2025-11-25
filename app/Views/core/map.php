<?php
$includeBase ??= true;
?>
<section>
    <h2><?= lang('Application.map') ?></h2>
    <?php if (isset($includeDisclaimer)) { ?>
        <p><span class="b"><?= lang('Application.note') ?>: </span>
            <?= lang('Application.mappingApproximate') ?>
        </p>
    <?php } elseif (isset($eventIsMapped) && !$eventIsMapped) { ?>
        <p><span class="b"><?= lang('Application.note') ?>: </span>
            <?= lang('Application.mappingIncomplete') ?>
        </p>
    <?php } ?>
    <?= ((\App\Controllers\BaseController::isLive() && $includeBase) ? '<div style="width: 40%; float: right;">' . lang('Application.coordinates') . ':&nbsp;<div id="coord" style="float: right;"></div></div>' : '') ?>
    <div id="map">
    </div>
    <input type="hidden" name="idholder" id="idholder" value="-100">
</section>
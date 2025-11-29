<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.summary') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <th><?= lang('Application.id') ?></th>
                <?php } ?>
                <th><?= lang('Application.citation') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#law" aria-label="<?= lang('Application.lawKey') ?>" title="<?= lang('Application.lawKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <th><?= lang('Application.title') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <td><?= $query[0]->lawsectionid ?></td>
                <?php } ?>
                <td><?= $query[0]->lawsectioncitation ?></td>
                <td><?= $query[0]->lawtitle ?></td>
            </tr>
        </tbody>
    </table>
</section>
<?php } ?>
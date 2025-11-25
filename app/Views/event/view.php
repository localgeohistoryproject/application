<?php if (is_array($query ?? '') && $query !== []) {
    $row = $query[0]; ?>
<section>
    <h2><?= lang('Application.summary') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <th><?= lang('Application.id') ?></th>
                <?php } ?>
                <th><?= lang('Application.type') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventtype" aria-label="<?= lang('Application.typeKey') ?>" title="<?= lang('Application.typeKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <?php if ($row->eventgranted !== 'government') { ?>
                    <th><?= lang('Application.method') ?></th>
                <?php } ?>
                <th><?= lang('Application.description') ?></th>
                <?php if ($row->eventgranted !== 'government') { ?>
                    <th><?= lang('Application.successful') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventgranted" aria-label="<?= lang('Application.successfulKey') ?>" title="<?= lang('Application.successfulKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <?php } else { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <td><?= $row->eventid ?></td>
                <?php } ?>
                <td><?= $row->eventtypeshort ?></td>
                <?php if ($row->eventgranted !== 'government') { ?>
                    <td><?= $row->eventmethodlong ?></td>
                <?php } ?>
                <td><?= $row->eventlong ?></td>
                <?php if ($row->eventgranted !== 'government') { ?>
                    <td><?= $row->eventgranted ?></td>
                <?php } else { ?>
                    <td><?php echo view('core/link', [
                        'type' => 'government',
                        'link' => $row->government,
                        'text' => lang('Application.view'),
                    ]) ?></td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</section>
<?php if ($row->textflag === 't') { ?>
    <section>
        <h2><?= lang('Application.dates') ?></h2>
        <table class="normal cell-border compact stripe">
            <thead>
                <tr>
                    <th><?= lang('Application.eventYears') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="<?= lang('Application.dateKey') ?>" title="<?= lang('Application.dateKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                    <th><?= (is_null($row->otherdatetype) ? lang('Application.finalDecree') : $row->otherdatetype) ?> Date</th>
                    <th><?= lang('Application.effectiveDate') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="<?= lang('Application.dateKey') ?>" title="<?= lang('Application.dateKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                    <th><?= lang('Application.howEffectiveDateDetermined') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $row->eventyear ?></td>
                    <td><?= $row->otherdate ?></td>
                    <td><?= $row->eventeffective ?></td>
                    <td><?= $row->eventeffectivetype ?></td>
                </tr>
            </tbody>
        </table>
    </section>
<?php }
} ?>
<?php if (is_array($query ?? '') && $query !== []) {
    $isHistory ??= false;
    $isMultiple ??= false;
    ?>
<section>
    <?php if (!$isHistory) { ?>
        <h2><?= lang('Application.affectedGovernment') ?></h2>
    <?php } ?>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= ($isHistory ? lang('Application.label') : lang('Application.detail')) ?></th>
                <?php if ($isMultiple) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
                <th><?= lang('Application.howAffected') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="<?= lang('Application.affectedTypeKey') ?>" title="<?= lang('Application.affectedTypeKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <th><?= lang('Application.adverseGovernment') ?></th>
                <th><?= lang('Application.howAdverseAffected') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="<?= lang('Application.affectedTypeKey') ?>" title="<?= lang('Application.affectedTypeKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <th><?= lang('Application.date') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="<?= lang('Application.dateKey') ?>" title="<?= lang('Application.dateKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
    foreach ($query as $row) { ?>
                <tr>
                    <td data-sort="<?= $row->eventsort ?>"><?= ($isHistory ? $i : '<a href="/' . \Config\Services::request()->getLocale() . '/event/' . $row->eventslug . '/">' . lang('Application.view') . '</a>') ?></td>
                    <?php if ($isMultiple) { ?>
                        <td><?= $row->governmentaffectedlong ?></td>
                    <?php } ?>
                    <td><?= $row->affectedtypesame . ($row->eventreconstructed === 't' ? '?' : '') ?></td>
                    <td><?php echo view('core/link', [
                        'type' => 'government',
                        'link' => $row->governmentslug,
                        'text' => $row->governmentlong,
                    ]) ?></td>
                    <td><?= $row->affectedtypeother . ($row->eventreconstructed === 't' ? '?' : '') ?></td>
                    <td data-sort="<?= $row->eventsort ?>"><?= ($row->eventeffective === '' ? $row->eventyear : $row->eventeffective) ?></td>
                </tr>
            <?php $i++;
    } ?>
        </tbody>
    </table>
</section>
<?php } ?>
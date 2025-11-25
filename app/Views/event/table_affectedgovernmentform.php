<?php if (is_array($query ?? '') && $query !== []) {
    $includeGovernment ??= false;
    $isHistory ??= false;
    ?>
<section>
    <h2><?= lang('Application.affectedGovernmentForm') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($includeGovernment) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } else { ?>
                    <th><?= lang('Application.detail') ?></th>
                <?php }
                if (isset($isMultiple) && !$isMultiple) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
                <th><?= lang('Application.governmentForm') ?></th>
                <?php if (!$includeGovernment) { ?>
                    <th><?= lang('Application.date') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="<?= lang('Application.dateKey') ?>" title="<?= lang('Application.dateKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
    foreach ($query as $row) { ?>
                <tr>
                    <?php if ($includeGovernment) { ?>
                        <td><?php echo view('core/link', [
                            'type' => 'government',
                            'link' => $row->governmentslug,
                            'text' => $row->governmentlong,
                        ]) ?></td>
                    <?php } else { ?>
                        <td data-sort="<?= $row->eventsort ?>"><?= ($isHistory ? $i : '<a href="/' . \Config\Services::request()->getLocale() . '/event/' . $row->eventslug . '/">' . lang('Application.view') . '</a>') ?></td>
                    <?php }
                    if (isset($isMultiple) && !$isMultiple) { ?>
                        <td><?= $row->governmentaffectedlong ?></td>
                    <?php } ?>
                    <td><?= $row->governmentformlong . ((!$includeGovernment && $row->eventreconstructed === 't') ? '?' : '') ?></td>
                    <?php if (!$includeGovernment) { ?>
                        <td data-sort="<?= $row->eventsort ?>"><?= ($row->eventeffective === '' ? $row->eventyear : $row->eventeffective) ?></td>
                    <?php } ?>
                </tr>
            <?php $i++;
    } ?>
        </tbody>
    </table>
</section>
<?php } ?>
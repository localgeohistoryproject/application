<?php if (is_array($query ?? '') && $query !== []) {
    $isMultiple ??= false;
    $type ??= '';
    ?>
<section>
    <h2><?= lang('Application.governmentAction') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($type !== 'source') { ?>
                    <th><?= lang('Application.detail') ?></th>
                <?php }
                if ($type !== 'government' || $isMultiple) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
                <th><?= lang('Application.action') ?></th>
                <th><?= lang('Application.date') ?></th>
                <th><?= lang('Application.approved') ?></th>
                <th><?= lang('Application.effective') ?></th>
                <th><?= lang('Application.location') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if ($type !== 'source') { ?>
                        <td data-sort="<?= $row->governmentsourcesort ?>">
                            <?php if (isset($row->governmentsourceslug) && $row->governmentsourceslug !== '') { ?>
                                <a href="/<?= \Config\Services::request()->getLocale() ?>/governmentsource/<?= $row->governmentsourceslug ?>/"><?= lang('Application.view') ?></a>
                                <?php } elseif (isset($row->eventslug)) {
                                    $i = 0;
                                    foreach (explode(',', str_replace(['{', '}'], '', $row->eventslug)) as $event) { ?>
                                    <?= ($i > 0 ? '<br>' : '') ?><a href="/<?= \Config\Services::request()->getLocale() ?>/event/<?= $event ?>/"><?= lang('Application.view') ?></a>
                            <?php $i++;
                                    }
                                } ?>
                        </td>
                    <?php }
                    if ($type !== 'government' || $isMultiple) { ?>
                        <td><?php echo view('core/link', [
                            'type' => 'government',
                            'link' => $row->governmentslug,
                            'text' => $row->governmentlong,
                        ]); ?></td>
                    <?php } ?>
                    <td><span class="b">
                            <?= ($row->governmentsourcebody === '' ? '' : $row->governmentsourcebody . ' ') . $row->governmentsourcetype
                                . ($row->governmentsourcenumber === '' ? '' : ' ' . $row->governmentsourcenumber)
                                . ($row->governmentsourceterm === '' ? '' : ', ' . $row->governmentsourceterm)
                                . ($row->governmentsourcetitle === '' ? '</span>' : ':</span> ' . $row->governmentsourcetitle) ?>
                    </td>
                    <td data-sort="<?= $row->governmentsourcedatesort ?>"><?= $row->governmentsourcedate ?></td>
                    <td data-sort="<?= $row->governmentsourceapproveddatesort ?>">
                        <?= ($row->governmentsourceapproved === 't'
                            ? (($row->governmentsourcetype === 'Election' && $row->governmentsourceapproveddate !== '') ? lang('Application.certified') . ' ' : '') . $row->governmentsourceapproveddate : ($row->governmentsourcetype === 'Election' ? lang('Application.rejected') : ($row->governmentsourcetype === 'Bill' ? '' : lang('Application.veto') . ($row->governmentsourceapproveddate !== '' ? ' ' . lang('Application.overridden') . ' ' . $row->governmentsourceapproveddate : 'ed')))) ?>
                    </td>
                    <td data-sort="<?= $row->governmentsourceeffectivedatesort ?>"><?= $row->governmentsourceeffectivedate ?></td>
                    <td><?=
                        $row->governmentsourcelocation
                            . (($row->governmentsourcelocation !== '' && $row->sourcecitationlocation !== '') ? '; ' : '')
                            . $row->sourcecitationlocation ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
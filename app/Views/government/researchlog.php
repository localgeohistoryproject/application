<?php if (is_array($query ?? '') && $query !== []) {
    $isMultiple ??= false; ?>
<section>
    <h2><?= lang('Application.researchLog') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($isMultiple) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
                <th><?= lang('Application.type') ?>/<?= lang('Application.notes') ?></th>
                <th><?= lang('Application.logDate') ?></th>
                <th><?= lang('Application.coverage') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr<?= ($row->researchlogismissing === 't' ? ' class="warning"' : '') ?>>
                    <?php if ($isMultiple) { ?>
                        <td><?= $row->governmentlong ?></td>
                    <?php } ?>
                    <td>
                        <span class="b"><?= $row->researchlogtypelong . ($row->researchlognotes === '' ? '</span>'
                                            : ':</span> ' . $row->researchlognotes) ?>
                    </td>
                    <td data-sort="<?= $row->researchlogdatesort ?>"><?= $row->researchlogdate ?></td>
                    <td data-sort="<?= $row->researchlogyear ?>">
                        <?= ($row->researchlogvolume === '' ? '' : 'bk. ') . $row->researchlogvolume
                            . ($row->researchlogyear === '' ? '' : ' (' . $row->researchlogyear . ')') ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.surveySystem') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.surveyTownship') ?></th>
                <th><?= lang('Application.firstDivision') ?></th>
                <th><?= lang('Application.part') ?></th>
                <th><?= lang('Application.relationship') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventrelationship" aria-label="<?= lang('Application.relationshipKey') ?>" title="<?= lang('Application.relationshipKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { /* Need to add support for second division and special survey */ ?>
                <tr>
                    <td><?= $row->plsstownship ?></td>
                    <td><?= $row->plssfirstdivision ?></td>
                    <td><?= $row->plssfirstdivisionpart ?></td>
                    <td><?= $row->plssrelationship ?></td>
                </tr>
<?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
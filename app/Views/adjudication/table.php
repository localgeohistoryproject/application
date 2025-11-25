<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.adjudication') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.detail') ?></th>
                <th><?= lang('Application.tribunal') ?></th>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.no') ?></th>
                <th><?= lang('Application.term') ?></th>
                <?php if (isset($eventRelationship)) { ?>
                    <th><?= lang('Application.relationship') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventrelationship" aria-label="<?= lang('Application.relationshipKey') ?>" title="<?= lang('Application.relationshipKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><a href="/<?= \Config\Services::request()->getLocale() ?>/adjudication/<?= $row->adjudicationslug ?>/"><?= lang('Application.view') ?></a></td>
                    <td><?= $row->tribunallong ?></td>
                    <td><?= $row->adjudicationtypelong ?></td>
                    <td><?= $row->adjudicationnumber ?></td>
                    <td data-sort="<?= $row->adjudicationtermsort ?>"><?= $row->adjudicationterm ?></td>
                    <?php if (isset($eventRelationship)) { ?>
                        <td><?= $row->eventrelationship ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
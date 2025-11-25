<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <?= (isset($title) && $title !== '' ? '<h2>' . $title . '</h2>' : '') ?>
    <table id="<?= ($tableId ?? 'event') ?>" class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.detail') ?></th>
                <th><?= lang('Application.type') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventtype" aria-label="<?= lang('Application.typeKey') ?>" title="<?= lang('Application.typeKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <th><?= lang('Application.description') ?></th>
                <th><?= lang('Application.successful') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventgranted" aria-label="<?= lang('Application.successfulKey') ?>" title="<?= lang('Application.successfulKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <th><?= lang('Application.date') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="<?= lang('Application.dateKey') ?>" title="<?= lang('Application.dateKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <?php if (isset($includeLawGroup)) { ?>
                    <th><?= lang('Application.group') ?></th>
                <?php } if (isset($eventRelationship)) { ?>
                    <th><?= lang('Application.relationship') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventrelationship" aria-label="<?= lang('Application.relationshipKey') ?>" title="<?= lang('Application.relationshipKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><a href="/<?= \Config\Services::request()->getLocale() ?>/event/<?= $row->eventslug ?>/"><?= lang('Application.view') ?></a></td>
                    <td><?= $row->eventtypeshort ?></td>
                    <td><?= $row->eventlong ?></td>
                    <td><?= $row->eventgranted ?></td>
                    <td data-sort="<?= $row->eventsort ?>"><?= ($row->eventeffective === '' ? $row->eventyear : $row->eventeffective) ?></td>
                    <?php if (isset($includeLawGroup)) { ?>
                        <td><?= $row->lawgrouplong ?></td>
                    <?php } if (isset($eventRelationship)) { ?>
                        <td><?= $row->eventrelationship ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
<?php if (is_array($query ?? '') && $query !== []) {
    $title ??= '';
    $type ??= '';
    ?>
<section>
    <?php if (!isset($omitTitle)) { ?>
        <h2><?= $title ?></h2>
    <?php } ?>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.detail') ?></th>
                <th><?= lang('Application.citation') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#law" aria-label="<?= lang('Application.lawKey') ?>" title="<?= lang('Application.lawKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
                <th><?php if ($type === 'relationship') {
                    if (isset($includeLawGroup)) { ?>
                        <?= lang('Application.group') ?></th>
                        <th><?php } ?><?= lang('Application.relationship') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventrelationship" aria-label="<?= lang('Application.relationshipKey') ?>" title="<?= lang('Application.relationshipKey') ?>"><span class="keyiconfill">vpn_key</span></a>
                    <?php } else { ?>
                        <?= lang('Application.type') ?>
                    <?php } ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><a href="/<?= \Config\Services::request()->getLocale() ?>/law/<?= $row->lawsectionslug ?>/"><?= ($row->lawsectionslug === '' ? '' : lang('Application.view')) ?></a></td>
                    <td data-sort="<?= $row->lawapproved ?>"><?= $row->lawsectioncitation ?></td>
                    <?php if ($type === 'relationship' && isset($includeLawGroup)) { ?>
                        <td><?= $row->lawgrouplong ?></td>
                    <?php } ?>
                    <td><?= (($type === 'relationship') ? $row->lawsectioneventrelationship : $row->eventtypeshort) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
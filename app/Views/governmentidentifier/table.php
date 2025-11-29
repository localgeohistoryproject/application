<?php if (is_array($query ?? '') && $query !== []) {
    $title ??= ''; ?>
<section>
    <h2><?= $title ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($title !== lang('Application.detail')) { ?>
                    <th><?= lang('Application.detail') ?></th>
                <?php }
                if (isset($isMultiple) && !$isMultiple) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.source') ?></th>
                <th><?= lang('Application.identifier') ?></th>
                <?php if ($title === lang('Application.identifier')) { ?>
                    <th><?= lang('Application.relationship') ?></th>
                <?php } ?>
                <th><?= lang('Application.webLink') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if ($title !== lang('Application.detail')) { ?>
                        <td><a href="/<?= \Config\Services::request()->getLocale() ?>/governmentidentifier/<?= $row->governmentidentifiertypeslug . '/' . strtolower($row->governmentidentifier) ?>/"><?= lang('Application.view') ?></a></td>
                    <?php }
                    if (isset($isMultiple) && !$isMultiple) { ?>
                        <td><?= $row->governmentlong ?></td>
                    <?php } ?>
                    <td><?= $row->governmentidentifiertypetype ?></td>
                    <td><?= $row->governmentidentifiertypeshort ?></td>
                    <td><?= $row->governmentidentifier ?></td>
                    <?php if ($title === lang('Application.identifier')) { ?>
                        <td><?= $row->governmentidentifierstatus ?></td>
                    <?php } ?>
                    <td><?= ($row->governmentidentifiertypeurl === '' ? '' : '<a href="' . $row->governmentidentifiertypeurl . '">' . lang('Application.view') . '</a>') ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
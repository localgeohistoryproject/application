<?php if (is_array($query ?? '') && $query !== []) {
    $hasLink ??= false;
    $title ??= '';
    ?>
<section>
    <h2><?= $title ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($hasLink) { ?>
                    <th><?= lang('Application.detail') ?></th>
                <?php } elseif (\App\Controllers\BaseController::isLive()) { ?>
                    <th><?= lang('Application.id') ?></th>
                <?php } ?>
                <th><?= lang('Application.description') ?></th>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.source') ?></th>
                <th><?= lang('Application.acres') ?></th>
                <th><?= lang('Application.beginningPoint') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if ($hasLink) { ?>
                        <td><a href="/<?= \Config\Services::request()->getLocale() ?>/metes/<?= $row->metesdescriptionslug ?>/"><?= lang('Application.view') ?></a></td>
                    <?php } elseif (\App\Controllers\BaseController::isLive()) { ?>
                        <td><?= $row->metesdescriptionid ?></td>
                    <?php } ?>
                    <td><?= $row->metesdescriptionlong ?></td>
                    <td><?= $row->metesdescriptiontype ?></td>
                    <td><?= $row->metesdescriptionsource ?></td>
                    <td><?= ($row->metesdescriptionacres <= 0 ? '' : $row->metesdescriptionacres) ?></td>
                    <td><?= $row->metesdescriptionbeginningpoint ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
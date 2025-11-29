<?php if (is_array($query ?? '') && $query !== []) {
    $isMultiple ??= false; ?>
<section>
    <h2><?= lang('Application.nationalArchives') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.detail') ?></th>
                <?php if ($isMultiple) { ?>
                    <th><?= lang('Application.government') ?></th>
                <?php } ?>
                <th><?= lang('Application.source') ?></th>
                <th><?= lang('Application.set') ?></th>
                <th><?= lang('Application.description') ?></th>
                <th><?= lang('Application.fileUnit') ?></th>
                <th><?= lang('Application.from') ?></th>
                <th><?= lang('Application.to') ?></th>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <th><?= lang('Application.examined') ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><?php if ($row->url !== '') { ?><a href="<?= $row->url ?>"><?= lang('Application.view') ?></a><?php } ?></td>
                    <?php if ($isMultiple) { ?>
                        <td><?= $row->governmentlong ?></td>
                    <?php } ?>
                    <td><?= $row->sourceabbreviation ?></td>
                    <td><?= $row->nationalarchivesset ?></td>
                    <td><?= $row->nationalarchivesgovernment ?></td>
                    <td><?= $row->nationalarchivesunit ?></td>
                    <td><?= $row->nationalarchivesunitfrom ?></td>
                    <td><?= $row->nationalarchivesunitto ?></td>
                    <?php if (\App\Controllers\BaseController::isLive()) { ?>
                        <td><?= ($row->nationalarchivesexamined === 't' ? 'yes' : 'no') ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
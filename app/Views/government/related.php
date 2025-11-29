<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.related') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.government') ?></th>
                <th><?= lang('Application.relationship') ?></th>
                <th><?= lang('Application.status') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td class="folder<?= (\App\Controllers\BaseController::isLive() ? $row->governmentcolor : 'none') ?>"><?php echo view('core/link', [
                        'type' => $row->governmentslugtype,
                        'link' => $row->governmentslug,
                        'text' => $row->governmentlong,
                    ]) ?></td>
                    <td class="folder<?= (\App\Controllers\BaseController::isLive() ? $row->governmentcolor : 'none') ?>"><?= $row->governmentrelationship ?></td>
                    <td class="folder<?= (\App\Controllers\BaseController::isLive() ? $row->governmentcolor : 'none') ?>"><?= $row->governmentparentstatus ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
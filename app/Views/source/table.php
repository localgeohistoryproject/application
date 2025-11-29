<?php if (is_array($query ?? '') && $query !== []) {
    $hasLink ??= false; ?>
<section>
    <h2><?= lang('Application.source') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($hasLink) { ?>
                    <th><?= lang('Application.detail') ?></th>
                <?php } ?>
                <th><?= lang('Application.abbreviation') ?></th>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.citation') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if ($hasLink) { ?>
                        <td><a href="/<?= \Config\Services::request()->getLocale() ?>/<?= $row->linktype ?>/<?= $row->sourceid ?>/"><?= lang('Application.view') ?></a></td>
                    <?php } ?>
                    <td><?= $row->sourceabbreviation ?></td>
                    <td><?= $row->sourcetype ?></td>
                    <td><?= $row->sourcefullcitation ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
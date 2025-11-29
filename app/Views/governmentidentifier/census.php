<?php if (is_array($query ?? '') && $query !== []) {
    $type ??= ''; ?>
<section>
    <h2><?= lang('Application.censusGazetteer') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($type === 'usgs') { ?>
                    <th><?= lang('Application.type') ?></th>
                <?php } ?>
                <th><?= lang('Application.from') ?></th>
                <th><?= lang('Application.to') ?></th>
                <th><?= lang('Application.name') ?></th>
                <th><?= lang('Application.relatedIdentifier') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if ($type === 'usgs') { ?>
                        <td><?= $row->gazetteertype ?></td>
                    <?php } ?>
                    <td><?= $row->gazetteerfrom ?></td>
                    <td><?= $row->gazetteerto ?></td>
                    <td><?= $row->governmentfullname ?></td>
                    <td><?= $row->governmentidentifier ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
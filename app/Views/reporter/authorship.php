<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.authorship') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.reporter') ?></th>
                <th><?= lang('Application.opinionAdjudicators') ?></th>
                <th><?= lang('Application.dissentingAdjudicators') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><?= $row->adjudicationsourcecitationauthor ?></td>
                    <td><?= $row->adjudicationsourcecitationjudge ?></td>
                    <td><?= $row->adjudicationsourcecitationdissentjudge ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
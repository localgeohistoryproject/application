<?php if (is_array($query ?? '') && $query !== []) {
    $row = $query[0]; ?>
<section>
    <h2><?= lang('Application.tribunal') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <th><?= lang('Application.id') ?></th>
                <?php } ?>
                <th><?= lang('Application.tribunal') ?></th>
                <th><?= lang('Application.currentFilingOffice') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive()) { ?>
                    <td><?= $row->adjudicationid ?></td>
                <?php } ?>
                <td><?= $row->tribunallong ?></td>
                <td><?= $row->tribunalfilingoffice ?></td>
            </tr>
        </tbody>
    </table>
</section>
<section>
    <h2><?= lang('Application.summary') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.no') ?></th>
                <th><?= lang('Application.term') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $row->adjudicationtypelong ?></td>
                <td><?= $row->adjudicationnumber ?></td>
                <td><?= $row->adjudicationterm ?></td>
            </tr>
        </tbody>
    </table>
</section>
<?php if ($row->textflag === 't') { ?>
    <section>
        <h2><?= lang('Application.detail') ?></h2>
        <table class="normal cell-border compact stripe">
            <thead>
                <tr>
                    <th><?= lang('Application.longCaption') ?></th>
                    <th><?= lang('Application.shortDescription') ?></th>
                    <th><?= lang('Application.notes') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $row->adjudicationlong ?></td>
                    <td><?= $row->adjudicationshort ?></td>
                    <td><?= $row->adjudicationnotes ?></td>
                </tr>
            </tbody>
        </table>
    </section>
<?php }
} ?>
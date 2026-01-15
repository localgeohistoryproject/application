<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.locationReferences') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.location') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><?= $row->adjudicationlocationtypelong . ' ' . $row->adjudicationlocationtypetype
                            . ($row->tribunallong !== '' ? ' <span class="i"><span class="b">(' . lang('Application.tribunal') . ': </span>' . $row->tribunallong
                                . '<span class="b">; ' . lang('Application.currentFilingOffice') . ': </span>' . $row->tribunalfilingoffice . '<span class="b">)</span></span>' : '') ?></td>
                    <td><?= ($row->adjudicationlocationpage === 'electronic' ? lang('Application.electronic') : ($row->adjudicationlocationtypearchiveseries !== '' ? $row->adjudicationlocationtypearchivetype . ' ' . lang('Application.archivesSeries') . ' ' . $row->adjudicationlocationtypearchiveseries . ', ' : '')
                            . ($row->adjudicationlocationtypevolumetype === 'Volume' ? 'v.' : strtolower($row->adjudicationlocationtypevolumetype)) . ' ' . $row->adjudicationlocationvolume . ', '
                            . ($row->adjudicationlocationtypepagetype === 'Page' ? 'p.' : strtolower($row->adjudicationlocationtypepagetype)) . ' ' . $row->adjudicationlocationpage) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
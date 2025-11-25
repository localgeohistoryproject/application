<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.currentGovernment') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if (\App\Controllers\BaseController::isLive() && isset($query[0]->governmentshapeid)) { ?>
                    <th><?= lang('Application.id') ?></th>
                <?php } ?>
                <th><?= lang('Application.subMunicipality') ?></th>
                <th><?= lang('Application.municipality') ?></th>
                <th><?= lang('Application.county') ?></th>
                <th><?= lang('Application.state') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if (\App\Controllers\BaseController::isLive() && isset($row->governmentshapeid)) { ?>
                        <td><?= $row->governmentshapeid ?></td>
                    <?php } ?>
                    <td>
                        <?php if ($row->governmentsubmunicipality !== '') {
                            echo view('core/link', [
                                'type' => 'government',
                                'link' => $row->governmentsubmunicipality,
                                'text' => $row->governmentsubmunicipalitylong,
                            ]);
                        } ?>
                    </td>
                    <td>
                        <?php echo view('core/link', [
                            'type' => 'government',
                            'link' => $row->governmentmunicipality,
                            'text' => $row->governmentmunicipalitylong,
                        ]); ?>
                    </td>
                    <td>
                        <?php echo view('core/link', [
                            'type' => 'government',
                            'link' => $row->governmentcounty,
                            'text' => $row->governmentcountyshort,
                        ]); ?>
                    </td>
                    <td>
                        <?php echo view('core/link', [
                            'type' => 'government',
                            'link' => $row->governmentstate,
                            'text' => $row->governmentstateabbreviation,
                        ]); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
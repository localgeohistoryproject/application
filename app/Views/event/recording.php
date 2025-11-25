<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.recordedDocument') ?></h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.government') ?></th>
                <th><?= lang('Application.type') ?></th>
                <th><?= lang('Application.location') ?></th>
                <th><?= lang('Application.alternateLocation') ?></th>
                <th><?= lang('Application.date') ?></th>
                <th><?= lang('Application.relationship') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#eventrelationship" aria-label="<?= lang('Application.relationshipKey') ?>" title="<?= lang('Application.relationshipKey') ?>"><span class="keyiconfill">vpn_key</span></a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) {
                if ($row->recordingtype === '') {
                    $row->recordingtype = $row->recordingnumbertype;
                    $row->recordinglocation = $row->recordingnumberlocation;
                    $row->recordingnumbertype = null;
                    $row->recordingnumberlocation = null;
                } ?>
                <tr>
                    <td><a href="/<?= \Config\Services::request()->getLocale() ?>/government/<?= $row->government ?>/"><?= $row->governmentshort ?></a></td>
                    <td><?= $row->recordingtype . ($row->hasbothtype === 't' ? '<br>' : '') . $row->recordingnumbertype ?></td>
                    <td><?= $row->recordinglocation . ($row->hasbothtype === 't' ? '<br>' : '') . $row->recordingnumberlocation ?></td>
                    <td><?=
                        $row->recordingrepositoryshort
                                        . ($row->recordingrepositoryseries === '' ? '' : ', ' . lang('Application.series') . ' ' . $row->recordingrepositoryseries)
                                        . ($row->recordingrepositorycontainer === '' ? '' : ', ' . lang('Application.container') . ' ' . $row->recordingrepositorycontainer)
                                        . ($row->recordingrepositoryitemlocation === '' ? '' : ', ' . lang('Application.location_lower') . ' ' . $row->recordingrepositoryitemlocation)
                                        . ($row->recordingrepositoryitemnumber === '' ? '' : ', ' . lang('Application.folder') . ' ' . $row->recordingrepositoryitemnumber)
                                        . ($row->recordingrepositoryitemrange === '' ? '' : ', ' . lang('Application.part_lower') . ' ' . $row->recordingrepositoryitemrange)
                ?></td>
                    <td data-sort="<?= $row->recordingdatesort ?>"><?= $row->recordingdate ?></td>
                    <td><?= $row->recordingeventrelationship ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
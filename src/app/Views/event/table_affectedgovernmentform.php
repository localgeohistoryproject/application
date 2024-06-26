<?php if (is_array($query ?? '') && $query !== []) {
    $includeGovernment ??= false;
    $isHistory ??= false;
    $state ??= 'usa';
    ?>
<section>
    <h2>Affected Government Form</h2>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <?php if ($includeGovernment) { ?>
                    <th>Government</th>
                <?php } else { ?>
                    <th>Detail</th>
                <?php }
                if (isset($isMultiple) && !$isMultiple) { ?>
                    <th>Government</th>
                <?php } ?>
                <th>Government Form</th>
                <?php if (!$includeGovernment) { ?>
                    <th>Date <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="Date Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicon']); ?></a></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
    foreach ($query as $row) { ?>
                <tr>
                    <?php if ($includeGovernment) { ?>
                        <td><?php echo view('core/link', ['link' => $row->governmentstatelink, 'text' => $row->governmentlong]) ?></td>
                    <?php } else { ?>
                        <td data-sort="<?= $row->eventsort ?>"><?= ($isHistory ? $i : '<a href="/' . \Config\Services::request()->getLocale() . '/' . $state . '/event/' . $row->eventslug . '/">View</a>') ?></td>
                    <?php }
                    if (isset($isMultiple) && !$isMultiple) { ?>
                        <td><?= $row->governmentaffectedlong ?></td>
                    <?php } ?>
                    <td><?= $row->governmentformlong . ((!$includeGovernment && $row->eventreconstructed === 't') ? '?' : '') ?></td>
                    <?php if (!$includeGovernment) { ?>
                        <td data-sort="<?= $row->eventsort ?>"><?= ($row->eventeffective === '' ? $row->eventyear : $row->eventeffective) ?></td>
                    <?php } ?>
                </tr>
            <?php $i++;
    } ?>
        </tbody>
    </table>
</section>
<?php } ?>
<?php if (is_array($query ?? '') && $query !== []) {
    $includeDate ??= false;
    $isComplete ??= true;
    $state ??= 'usa';
    ?>
<section>
    <?php if ($isComplete) { ?>
        <h2>Affected Government</h2>
    <?php } ?>
    <table class="normal cell-border compact stripe wrap">
        <thead>
            <tr>
                <?php if ($includeDate) { ?>
                    <th>Detail</th>
                    <th>Date <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="Date Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicon']); ?></a>
                    </th>
                <?php }
                if ($isComplete) { ?>
                    <th>From<br>Municipality</th>
                <?php } ?>
                <th>From<br>County</th>
                <th>From<br>State</th>
                <?php if ($isComplete) { ?>
                    <th>To<br>Municipality</th>
                <?php } ?>
                <th>To<br>County</th>
                <th>To<br>State</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <?php if ($includeDate) { ?>
                        <td data-sort="<?= $row->eventorder ?>"><?php echo view('core/link', ['link' => ($row->eventslug === '' ? '' : "/" . \Config\Services::request()->getLocale() . "/" . $state . "/event/" . $row->eventslug . "/"), 'text' => ($row->eventslug === '' ? 'Missing' : 'View')]) ?></td>
                        <td data-sort="<?= $row->eventsort ?>"><?= ($row->eventeffective === '' ? $row->eventyear : $row->eventeffective) ?></td>
                    <?php }
                    if ($isComplete) { ?>
                        <td>
                            <?php echo view('core/link', ['link' => $row->municipalityfrom, 'text' => $row->municipalityfromlong]) ?><br>
                            <span class="i"><?= $row->affectedtypemunicipalityfrom ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                            <?php if ($row->submunicipalityfrom !== '') { ?>
                                <br><span class="b i">Sub:</span>
                                <?php echo view('core/link', ['link' => $row->submunicipalityfrom, 'text' => $row->submunicipalityfromlong]) ?>
                                <br><span class="i"><?= $row->affectedtypesubmunicipalityfrom ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php echo view('core/link', ['link' => $row->countyfrom, 'text' => $row->countyfromshort]) ?><br>
                        <span class="i"><?= $row->affectedtypecountyfrom ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                        <?php if ($isComplete && $row->subcountyfrom !== '') { ?>
                            <br><span class="b i">Sub:</span>
                            <?php echo view('core/link', ['link' => $row->subcountyfrom, 'text' => $row->subcountyfromshort]) ?>
                            <br><span class="i"><?= $row->affectedtypesubcountyfrom ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo view('core/link', ['link' => $row->statefrom, 'text' => $row->statefromabbreviation]) ?><br>
                        <span class="i"><?= $row->affectedtypestatefrom ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                    </td>
                    <?php if ($isComplete) { ?>
                        <td>
                            <?php echo view('core/link', ['link' => $row->municipalityto, 'text' => $row->municipalitytolong]) ?><br>
                            <span class="i"><?= $row->affectedtypemunicipalityto ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                            <?php if ($row->submunicipalityto !== '') { ?>
                                <br><span class="b i">Sub:</span>
                                <?php echo view('core/link', ['link' => $row->submunicipalityto, 'text' => $row->submunicipalitytolong]) ?>
                                <br><span class="i"><?= $row->affectedtypesubmunicipalityto ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php echo view('core/link', ['link' => $row->countyto, 'text' => $row->countytoshort]) ?><br>
                        <span class="i"><?= $row->affectedtypecountyto ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                        <?php if ($isComplete && $row->subcountyto !== '') { ?>
                            <br><span class="b i">Sub:</span>
                            <?php echo view('core/link', ['link' => $row->subcountyto, 'text' => $row->subcountytoshort]) ?>
                            <br><span class="i"><?= $row->affectedtypesubcountyto ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo view('core/link', ['link' => $row->stateto, 'text' => $row->statetoabbreviation]) ?><br>
                        <span class="i"><?= $row->affectedtypestateto ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="Affected Type Key"><?= view('core/svg_icon', ['iconLabel' => 'key icon', 'iconName' => 'key', 'iconType' => 'keyicontext']); ?></a></span>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<?php } ?>
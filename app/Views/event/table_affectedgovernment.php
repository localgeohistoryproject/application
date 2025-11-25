<?php
$affectedGovernment ??= ['linkTypes' => [], 'rows' => [], 'types' => []];
$includeDate ??= false;
$isComplete ??= true;
?>
<section>
    <?php if ($isComplete) { ?>
        <h2><?= lang('Application.affectedGovernment') ?></h2>
    <?php } ?>
    <table class="normal cell-border compact stripe wrap">
        <thead>
            <tr>
                <?php if ($includeDate) { ?>
                    <th><?= lang('Application.detail') ?></th>
                    <th><?= lang('Application.date') ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date" aria-label="<?= lang('Application.dateKey') ?>" title="<?= lang('Application.dateKey') ?>"><span class="keyiconfill">vpn_key</span></a>
                    </th>
                <?php } elseif (\App\Controllers\BaseController::isLive() && $isComplete) { ?>
                    <th><?= lang('Application.mapLink') ?></th>
                    <?php }
                foreach ($affectedGovernment['types'] as $fromTo => $levels) {
                    foreach ($levels as $level) { ?>
                        <th><?= ucfirst($fromTo) . '<br>' . str_replace(' ', '<br>', $level) ?></th>
                <?php  }
                } ?>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($affectedGovernment['rows'] ?? '') && $affectedGovernment['rows'] !== []) {
                foreach ($affectedGovernment['rows'] as $id => $row) { ?>
                <tr>
                    <?php if ($includeDate) { ?>
                        <td data-sort="<?= $row->eventsort ?>"><?php echo view('core/link', [
                            'type' => 'event',
                            'link' => $row->eventslug,
                            'text' => ($row->eventslug  === '' ? lang('Application.missing') : lang('Application.view')),
                        ]) ?></td>
                        <td data-sort="<?= $row->eventsort ?>"><?= $row->eventeffective ?></td>
                    <?php } elseif (\App\Controllers\BaseController::isLive() && $isComplete) { ?>
                        <td>

                            <?php if (is_array($affectedGovernment['linkTypes'] ?? '') && $affectedGovernment['linkTypes'] !== []) {
                                foreach ($affectedGovernment['linkTypes'] as $fromTo => $levels) {
                                    foreach ($levels as $level) {
                                        if (isset($row->{ucfirst($fromTo) . ' ' . $level . ' Long'})) { ?>
                                        <?php echo view('core/link', [
                                            'type' => 'governmentmap',
                                            'link' => $row->{ucfirst($fromTo) . ' ' . $level . ' Link'} . '/' . $id,
                                            'text' => ucfirst($fromTo),
                                        ]) ?><br>
                            <?php
                                        }
                                    }
                                }
                            } ?>
                        </td>
                        <?php }
                    if (is_array($affectedGovernment['types'] ?? '') && $affectedGovernment['types'] !== []) {
                        foreach ($affectedGovernment['types'] as $fromTo => $levels) {
                            foreach ($levels as $level) { ?>
                            <td>
                                <?php if (isset($row->{ucfirst($fromTo) . ' ' . $level . ' Long'})) { ?>
                                    <?php echo view('core/link', [
                                        'type' => 'government',
                                        'link' => $row->{ucfirst($fromTo) . ' ' . $level . ' Link'},
                                        'text' => $row->{ucfirst($fromTo) . ' ' . $level . ' Long'},
                                    ]) ?>
                                    <br><span class="i"><?= $row->{ucfirst($fromTo) . ' ' . $level . ' Affected'} ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#affectedtype" aria-label="<?= lang('Application.affectedTypeKey') ?>" title="<?= lang('Application.affectedTypeKey') ?>"><span class="keyiconfill">vpn_key</span></a></span>
                                <?php } ?>
                            </td>
                    <?php }
                            }
                    } ?>
                </tr>
            <?php }
                } ?>
        </tbody>
    </table>
</section>
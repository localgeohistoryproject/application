<?php if (is_array($wholeQuery ?? '') && $wholeQuery !== []) {
    $dateRange ??= '';
    $isContemporaneous ??= true;
    $notEvent ??= true;
    $query ??= '{}';
    $jurisdiction ??= '';
    $statistics ??= [
        'Contemporaneous' => '',
        'CreationDissolution' => '',
        'DateKey' => '',
        'DateStart' => '',
        'Incomplete' => '',
        'Parent' => '',
        'Successful' => '',
    ];
    ?>
    <section>
        <h2><?= lang('Application.byJurisdiction') ?>:</h2>
        <div id="map" class="map"></div>
    </section>
    <section>
        <h2><?= lang('Application.byYear') ?>: <a href="#" class="chartdownload" aria-label="<?= lang('Application.download') ?>" title="<?= lang('Application.download') ?>"><span class="statisticsicon">download</span></a></h2>
        <div id="chart" class="chart"></div>
    </section>
    <section>
        <h2><?= lang('Application.notes') ?>:</h2>
        <ol id="notes">
            <li><?= $statistics['Incomplete'] ?></li>
            <li><?= $statistics['Successful'] ?></li>
            <li><?= $statistics['DateStart'] ?> <a href="/<?= \Config\Services::request()->getLocale() ?>/key/#date"><?= $statistics['DateKey'] ?></a>.</li>
            <?php if ($isContemporaneous) { ?>
                <li><?= $statistics['Contemporaneous'] ?></li>
                <?php if ($notEvent) { ?>
                    <li><?= $statistics['CreationDissolution'] ?></li>
                    <li><?= $statistics['Parent'] ?></li>
            <?php }
                } ?>
        </ol>
    </section>
    <script>
        var mapPath = [
            '/asset/<?= ((\App\Controllers\BaseController::isLive() && ($jurisdiction !== '' && !in_array($jurisdiction, \App\Controllers\BaseController::getProductionJurisdictions()))) ? 'development' : 'application') ?>/map/statistics/<?= ($jurisdiction === '' ? (\App\Controllers\BaseController::isLive() ? 'development' : 'production') : $jurisdiction) ?>.geojson'
        ];
        var partData = <?= $query ?>;
        var lastLayer = "";
    </script>
    <?= view('leaflet/source'); ?>
    <style>
        .leaflet-container {
            background-color: rgba(255, 0, 0, 0.0);
        }
    </style>
    <script src="/<?= (\App\Controllers\BaseController::isOnline() ? '/' . getenv('dependency_classybrew') : 'asset/application/dependency') ?>/classybrew.js"></script>
    <script src="/asset/application/map/statistics.js"></script>
<?php } else { ?>
    <br><?= lang('Application.noResultsFound') ?>
<?php } ?>
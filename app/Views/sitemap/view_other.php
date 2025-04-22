<?php foreach ($query as $row) { ?>
<?= $_ENV['app_baseLocalGeohistoryProjectUrl'] ?? '' ?>/<?= \Config\Services::request()->getLocale() ?>/<?= $row ?>

<?php }
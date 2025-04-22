<?php if (is_array($query ?? '') && $query !== []) {
    foreach ($query as $row) { ?>
<?= $_ENV['app_baseLocalGeohistoryProjectUrl'] ?? '' ?>/<?= \Config\Services::request()->getLocale() ?>/<?= $row ?>

<?php }
    }

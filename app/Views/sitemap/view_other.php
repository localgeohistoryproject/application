<?php if (is_array($query ?? '') && $query !== []) {
    foreach ($query as $row) { ?>
<?= $_ENV['app_baseCanonicalProjectUrl'] ?? '' ?>/<?= \Config\Services::request()->getLocale() ?>/<?= $row ?>

<?php }
    }

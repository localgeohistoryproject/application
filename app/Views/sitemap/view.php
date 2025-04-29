<?php if (is_array($query ?? '') && $query !== []) {
    $id ??= '';
    foreach ($query as $row) { ?>
<?= $_ENV['app_baseCanonicalProjectUrl'] ?? '' ?>/<?= \Config\Services::request()->getLocale() ?>/<?= $id ?>/<?= $row->slug ?>/
<?php }
    }

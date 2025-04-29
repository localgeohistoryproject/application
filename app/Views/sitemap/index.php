<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php if (is_array($query ?? '') && $query !== []) {
    foreach ($query as $row) { ?>
    <sitemap>
        <loc><?= $_ENV['app_baseCanonicalProjectUrl'] ?? '' ?>/<?= \Config\Services::request()->getLocale() ?>/<?= ($row === 'other' ? '' : $row . '/') ?>sitemap.txt</loc>
    </sitemap>
<?php }
    } ?>
</sitemapindex>
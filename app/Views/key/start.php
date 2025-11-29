<section id="table-of-contents">
    <h2><?= lang('Application.tableOfContents') ?></h2>
    <?php if (is_array($keys ?? '') && $keys !== []) {
        foreach ($keys as $key => $value) { ?>
        <a href="#<?= strtolower($value) ?>"><?= $key ?></a><br>
    <?php }
        } ?>
</section>
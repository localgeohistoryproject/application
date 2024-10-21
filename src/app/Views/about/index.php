<?php if (is_array($query ?? '') && $query !== []) { ?>
<section id="key">
    <?php foreach ($query as $row) { ?>
        <a href="#<?= $row->keysort ?>"><?= $row->keyshort ?></a><br>
    <?php } ?>
</section>
<?php foreach ($query as $row) { ?>
<section id="<?= $row->keysort ?>">
    <h2><?= $row->keyshort ?></h2>
    <p><?= $row->keylong ?></p>
    <?php if ($row->keysort == 'about' && $jurisdictions !== []) { ?>
        <?php foreach ($jurisdictions as $jurisdiction) { ?>
            <a href="/en/about/<?= $jurisdiction->governmentabbreviation ?>/"><?= $jurisdiction->governmentshort ?></a><br> 
        <?php } ?>
    <?php } ?>
</section>
<?php }
} ?>
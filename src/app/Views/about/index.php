<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <?php foreach ($query as $row) { ?>
        <a href="#<?= $row->keysort ?>"><?= $row->keyshort ?></a><br>
    <?php } ?>
</section>
<?php foreach ($query as $row) { ?>
<section>
    <h2 id="<?= $row->keysort ?>"><?= $row->keyshort ?></h2>
    <p><?= $row->keylong ?></p>
</section>
<?php }
} ?>
<?php $query ??= []; ?>
<?php foreach ($query as $row) { ?>
    <section id="<?= $row->documentationsort ?>">
        <h2><?= $row->documentationshort ?></h2>
        <p>
            <?= $row->documentationlong ?>
        </p>
    </section>
<?php } ?>
    <section id="compiler">
        <h2><?= lang('Application.compilerContactInformation') ?></h2>
        <p>
            <?= getenv('app_compiler_name') ?><br>
            <?= lang('Application.email') ?>: <a href="mailto:<?= getenv('app_compiler_email') ?>"><?= getenv('app_compiler_email') ?></a><br>
            <?= lang('Application.fax') ?>: <?= getenv('app_compiler_fax') ?>
        </p>
    </section>
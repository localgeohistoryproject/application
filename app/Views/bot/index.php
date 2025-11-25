        <h3><?= lang('Application.summary') ?></h3>
        <p><?= $summary ?></p>
        <p><?= getenv('app_compiler_name') ?><br><?= lang('Application.email') ?>: <a href="mailto:<?= getenv('app_compiler_email') ?>"><?= getenv('app_compiler_email') ?></a><br><?= lang('Application.fax') ?>: <?= getenv('app_compiler_fax') ?></p>
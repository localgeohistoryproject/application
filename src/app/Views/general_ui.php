<script src="/<?= (\App\Controllers\BaseController::isOnline() ? '/' . getenv('dependency_selectize') : 'asset/dependency') ?>/js/standalone/selectize.min.js"></script>
<link rel="stylesheet" href="/<?= (\App\Controllers\BaseController::isOnline() ? '/' . getenv('dependency_selectize') : 'asset/dependency') ?>/css/selectize.css">
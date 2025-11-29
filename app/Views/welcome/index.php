<?php
$icons ??= [];
$welcome ??= '';
?>
<div class="push">&nbsp;</div>
<div id="welcomecontainer">
    <div id="welcometext" class="welcomecontent"><?= $welcome ?></div>
    <div id="welcomestate" class="welcomecontent">
        <a href="/<?= \Config\Services::request()->getLocale() ?>/about/" class="welcomeiconcontainer" aria-label="<?= lang('Application.about') ?>">
            <span class="welcomeicon">info</span>
            <div style="height: auto;"><?= lang('Application.about') ?></div>
        </a>
        <a href="/<?= \Config\Services::request()->getLocale() ?>/key/" class="welcomeiconcontainer" aria-label="<?= lang('Application.key') ?>">
            <span class="welcomeiconfill">vpn_key</span>
            <div style="height: auto;"><?= lang('Application.key') ?></div>
        </a>
        <a href="/<?= \Config\Services::request()->getLocale() ?>/search/" class="welcomeiconcontainer" aria-label="<?= lang('Application.search') ?>">
            <span class="welcomeicon">search</span>
            <div style="height: auto;"><?= lang('Application.search') ?></div>
        </a>
        <a href="/<?= \Config\Services::request()->getLocale() ?>/statistics/" class="welcomeiconcontainer" aria-label="<?= lang('Application.statistics') ?>">
            <span class="welcomeicon">insert_chart</span>
            <div style="height: auto;"><?= lang('Application.statistics') ?></div>
        </a>
        <a href="/<?= \Config\Services::request()->getLocale() ?>/status/" class="welcomeiconcontainer" aria-label="<?= lang('Application.status') ?>">
            <span class="welcomeicon">map</span>
            <div style="height: auto;"><?= lang('Application.status') ?></div>
        </a>
    </div>
</div>
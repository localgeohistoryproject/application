			<div class="push">&nbsp;</div>
			</main>
			<footer class="headerfooter">
			    <div id="footertext" class="bodytext">
			        <?= lang('Template.originalContent') ?> &copy; <?= getenv('app_compiler_copyright_start') ?>–<?= date('Y') ?> <a href="<?= getenv('app_compiler_url') ?>"><?= getenv('app_compiler_name') ?></a>
			        <?= lang('Template.license') ?> <a rel="license" href="https://creativecommons.org/licenses/by-sa/4.0/deed.<?= \Config\Services::request()->getLocale() ?>">CC BY-SA 4.0</a> |
			        <a href="/<?= \Config\Services::request()->getLocale() ?>/disclaimer/"><?= lang('Template.disclaimers') ?></a> |
					<a href="<?= getenv('app_compiler_link1_url') ?>"><?= getenv('app_compiler_link1_text') ?></a> |
                    <a href="<?= getenv('app_compiler_link2_url') ?>"><?= getenv('app_compiler_link2_text') ?></a> |
			        <?= (\App\Controllers\BaseController::isLive() ? 'LIVE as of' : 'Data updated') ?> <?= \App\Controllers\BaseController::lastUpdated() ?>
			    </div>
			</footer>
			</div>
			</body>

			</html>
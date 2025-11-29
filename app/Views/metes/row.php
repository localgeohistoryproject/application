<?php if (is_array($query ?? '') && $query !== []) { ?>
<section>
    <h2><?= lang('Application.coursesAndDistances') ?></h2>
    <p><span class="b"><?= lang('Application.note') ?>: </span><?= $summary ?></p>
    <table class="normal cell-border compact stripe">
        <thead>
            <tr>
                <th><?= lang('Application.point') ?></th>
                <th><?= lang('Application.thence') ?></th>
                <th><?= lang('Application.course') ?></th>
                <th><?= lang('Application.distance') ?></th>
                <th><?= lang('Application.to') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query as $row) { ?>
                <tr>
                    <td><?= $row->metesdescriptionline ?></td>
                    <td><?= $row->thencepoint ?></td>
                    <td class="metesdegree" data-ns="<?= $row->northsouth ?>" data-deg="<?= $row->degree ?>" data-ew="<?= $row->eastwest ?>">
                        <?= $row->northsouth . ' ' . (is_null($row->degree) ? '' : $row->degree . '&deg;') . ' ' . $row->eastwest ?></td>
                    <td class="metesfoot" data-ft="<?= $row->foot ?>"><?= (is_null($row->foot) ? '' : $row->foot . ' <span class="i">' . lang('Application.ft') . '</span>') ?></td>
                    <td><?= $row->topoint ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<section>
    <h2><?= lang('Application.changeMeasurementUnits') ?></h2>
    <form id="courseform">
        <span class="b"><?= lang('Application.courses') ?>&#58;&nbsp;</span>
        <input value="1" name="metesdegreetype" type="radio" checked="checked"><?= lang('Application.degrees') ?>&#59;
        <input value="2" name="metesdegreetype" type="radio"><?= lang('Application.degrees') ?> &amp; <?= lang('Application.minutes') ?>&#59; or
        <input value="3" name="metesdegreetype" type="radio"><?= lang('Application.degrees') ?>, <?= lang('Application.minutes') ?>, &amp; <?= lang('Application.seconds') ?>.
    </form>
    <form id="distanceform">
        <span class="b"><?= lang('Application.distances') ?>&#58;&nbsp;</span>
        <input value="1" name="metesfoottype" type="radio" checked="checked"><?= lang('Application.feet') ?>&#59;
        <input value="2" name="metesfoottype" type="radio"><?= lang('Application.feet') ?> &amp; <?= lang('Application.inches') ?>&#59;
        <input value="3" name="metesfoottype" type="radio"><?= lang('Application.rods') ?>&#59;
        <input value="4" name="metesfoottype" type="radio"><?= lang('Application.rods') ?> &amp; <?= lang('Application.feet_lower') ?>&#59;
        <input value="5" name="metesfoottype" type="radio"><?= lang('Application.rods') ?>, <?= lang('Application.feet_lower') ?>, &amp; <?= lang('Application.inches') ?>&#59; or
        <input value="6" name="metesfoottype" type="radio"><?= lang('Application.chains') ?>.
    </form>
</section>
<script src="/asset/application/tool/metes.js"></script>
<?php } ?>
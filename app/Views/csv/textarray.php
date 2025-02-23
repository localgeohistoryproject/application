<?php

$handle = fopen('php://output', 'w');
if (count($query) > 0) {
    foreach ($query as $row) {
        fwrite($handle, $row . PHP_EOL);
    }
}
fclose($handle);

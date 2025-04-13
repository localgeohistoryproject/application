<?php

$handle = fopen('php://output', 'w');

if (is_array($query ?? '') && $query !== []) {
    foreach ($query as $row) {
        fwrite($handle, $row . PHP_EOL);
    }
}

fclose($handle);

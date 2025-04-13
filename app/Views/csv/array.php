<?php

$handle = fopen('php://output', 'w');

if (is_array($query ?? '') && $query !== []) {
    $isFirstRow = true;
    foreach ($query as $row) {
        if ($isFirstRow) {
            fputcsv($handle, array_keys($row), escape: '');
            $isFirstRow = false;
        }
        fputcsv($handle, $row, escape: '');
    }
}

fclose($handle);

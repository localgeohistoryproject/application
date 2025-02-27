<?php

$handle = fopen('php://output', 'w');

if (is_array($query ?? '') && $query !== []) {
    $i = 0;
    foreach ($query as $row) {
        if ($i === 0) {
            fputcsv($handle, array_keys($row));
            $i++;
        }
        fputcsv($handle, $row);
    }
}
fclose($handle);

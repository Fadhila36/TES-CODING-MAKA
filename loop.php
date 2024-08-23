<?php
for ($i = 1; $i <= 100; $i++) {
    $output = '';

    if ($i % 3 == 0 && $i % 5 === 0) {
        $output = 'Mari Berkarya';
    }
    elseif ($i % 3 == 0) {
        $output .= 'Mari';
    }

    elseif ($i % 5 == 0) {
        $output = 'Berkarya';
    } else {
        $output = $i;
    }
    echo $output;

    if ($i < 100) {
        echo ',';
    }
}

<?php

function is_valid($line) {
    if (!preg_match('/^([0-9]+)-([0-9]+)\s+([^:]*):\s+(.*)$/', $line, $matches)) {
        return 0;
    }

    $min = $matches[1];
    $max = $matches[2];
    $chr = $matches[3];
    $wrd = $matches[4];

    $count = substr_count($wrd, $chr);
    $is_valid = $count <= $max && $count >= $min;
//    echo "Password: $wrd [$min <= $count <= $max] valid? [" . ($is_valid ? "yes" : "no") . "]\n";

    return $is_valid ? 1 : 0;
}

// $lines = ["1-3 a: abcde", "1-3 b: cdefg", "2-9 c: ccccccccc"];
$lines = file('star-03-input.txt');

$total = 0;
foreach ($lines as $line) {
    $total += is_valid($line);
}

echo "Total passwords valid: $total\n";

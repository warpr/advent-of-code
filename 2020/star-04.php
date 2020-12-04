<?php

function show($bool) {
    return $bool ? "true" : "false";
}

function ksor($bool1, $bool2) {
    return $bool1 xor $bool2;

    if ($bool1 && $bool2) {
        return false;
    }
    if (!$bool1 && !$bool2) {
        return false;
    }
    return true;
}

function is_valid($line) {
    if (!preg_match('/^([0-9]+)-([0-9]+)\s+([^:]*):\s+(.*)$/', $line, $matches)) {
        return 0;
    }

    $chr = $matches[3];
    $wrd = $matches[4];

    $pos1 = $matches[1] - 1;
    $pos2 = $matches[2] - 1;
    $chr1 = $wrd[$pos1];
    $chr2 = $wrd[$pos2];

    $pos1_valid = $chr1 == $chr;
    $pos2_valid = $chr2 == $chr;

    $is_valid = ($pos1_valid xor $pos2_valid);

//    echo "\n" . trim($line) . "\n";
//    echo "Password: $wrd [chr: $chr, pos1: $pos1 ($chr1), pos2: $pos2 ($chr2)]\n";
//    echo "--> valid? [pos1: " . show($pos1_valid)
//       . ", pos2: " . show($pos2_valid)
//       . ", xor: " . show($is_valid) . "]\n";

    return $is_valid ? 1 : 0;
}

// $lines = ["1-3 a: abcde", "1-3 b: cdefg", "2-9 c: ccccccccc"];
$lines = file('star-03-input.txt');

$total = 0;
$count = 0;
foreach ($lines as $line) {
    $total += is_valid($line);
}

echo "Total passwords valid: $total\n";

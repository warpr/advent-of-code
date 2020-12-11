<?php

function jolt_differences($chain) {
    $prev = array_shift($chain);
    $ret = [];
    foreach ($chain as $adapter) {
        $diff = abs($adapter - $prev);
        $ret[$diff]++;
        $prev = $adapter;
    }

    ksort($ret);
    return $ret;
}

function main($filename) {
    $input = file($filename);
    $lines = [];
    foreach ($input as $line) {
        $lines[] = (int) trim($line);
    }

    sort($lines);

    $highest = $lines[0];
    foreach ($lines as $line) {
        if ($line > $highest) {
            $highest = $line;
        }
    }

    $start = 0;
    $built_in = $highest + 3;
    echo "-------------------------------\n";
    echo "Highest rated adapter: $highest\n";
    echo "Built-in adapter:      $built_in\n";
    echo "Outlet rating:         $start\n";
    echo "Adapter set from:      $filename\n";

    array_unshift($lines, 0);
    $lines[] = $built_in;

    // not actually checked if valid, however if we must use all adapters
    // the only valid solution is the one where all adapters are just sorted
    echo "Valid chain: " . implode(" -> ", $lines) . "\n";
    $differences = jolt_differences($lines);
    foreach ($differences as $key => $val) {
        echo "$key jolt jumps: $val\n";
    }
    echo 'Puzzle answer: ' . $differences[1] * $differences[3] . "\n";
}

main('star-19-example.txt');
main('star-19-example2.txt');
main('star-19-input.txt');

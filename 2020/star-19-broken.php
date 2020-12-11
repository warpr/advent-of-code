<?php

function use_adapter($use, $adapters) {
    $selected = $adapters[$use];
    unset($adapters[$use]);

    return [ $selected, array_values($adapters) ];
}

function is_valid($adapter, $outlet) {
    return ($outlet >= ($adapter - 3)) && ($outlet < $adapter);
}

function valid_adapters($adapters, $outlet) {
    foreach ($adapters as $key => $adapter) {
        if (is_valid($adapter, $outlet)) {
            list($selected, $rest) = use_adapter($key, $adapters);
            yield $selected => $rest;
        }
    }
}

function chain_adapters($adapters, $chain) {
    $start = end($chain);
    foreach (valid_adapters($adapters, $start) as $selected => $rest) {
        $chain[] = $selected;

        if (empty($rest)) {
            echo "1. CHAIN: " . implode(",", $chain) . "\n";
            echo "1. LEFT:  " . implode(",", $rest) . "\n";

            yield $chain;
        } else {
            foreach (chain_adapters($rest, $chain) as $final) {
                echo "2. CHAIN: " . implode(",", $chain) . "\n";
                echo "2. LEFT:  " . implode(",", $rest) . "\n";
                echo "2. FINAL: " . implode(",", $final) . "\n";

                yield $final;
            }
        }
    }
}

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

    foreach (chain_adapters($lines, [ $start ]) as $chain) {
        if (is_valid($built_in, end($chain))) {
            array_push($chain, $built_in);
            echo "Valid chain: " . implode(" -> ", $chain) . "\n";
            $differences = jolt_differences($chain);
            foreach ($differences as $key => $val) {
                echo "$key jolt jumps: $val\n";
            }
            echo 'Puzzle answer: ' . $differences[1] * $differences[3] . "\n";
        } else {
            array_push($chain, $built_in);
            echo "Invalid chain: " . implode(" -> ", $chain) . "\n";
        }
    }
}

main('star-19-example.txt');
//main('star-19-example2.txt');

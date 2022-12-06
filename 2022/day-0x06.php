<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function count_unique(array $str)
{
    $tmp = [];
    foreach ($str as $val) {
        $tmp[$val] = true;
    }
    return count($tmp);
}

function main($filename, $verbose, $marker_size)
{
    $input = str_split(trim(file_get_contents($filename)));

    if ($verbose) {
        echo 'Line: ' . implode('', $input) . "\n";
    }

    for ($i = 0; $i < count($input) - $marker_size; $i++) {
        $chunk = array_slice($input, $i, $marker_size);
        $unique = count_unique($chunk);
        if ($verbose) {
            echo "chunk $i: " . implode('', $chunk) . " (unique {$unique})\n";
        }

        if ($unique === $marker_size) {
            return $i + $marker_size;
        }
    }

    return count($input); // not found
}

function part1($filename, $verbose)
{
    return main($filename, $verbose, 4);
}

function part2($filename, $verbose)
{
    return main($filename, $verbose, 14);
}

run_part1('example', true, 7);
run_part1('example2', false, 5);
run_part1('example3', false, 6);
run_part1('example4', false, 10);
run_part1('example5', false, 11);
run_part1('input');

run_part2('example', true, 19);
run_part2('example2', false, 23);
run_part2('example3', false, 23);
run_part2('example4', false, 29);
run_part2('example5', false, 26);
run_part2('input');

echo "\n";

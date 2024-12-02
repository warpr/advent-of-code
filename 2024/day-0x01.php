<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $col1 = [];
    $col2 = [];

    foreach ($lines as $idx => $line) {
        if (preg_match('/^([0-9]*)\s+([0-9]*)$/', $line, $matches)) {
            $col1[] = trim($matches[1]);
            $col2[] = trim($matches[2]);
        }
    }

    return [$col1, $col2];
}

function part1($col1, $col2)
{
    sort($col1);
    sort($col2);

    $diff = [];

    foreach ($col1 as $idx => $val1) {
        $val2 = $col2[$idx];
        $diff[] = abs($val2 - $val1);
    }

    return $diff;
}

function part2($col1, $col2)
{
    $sim = [];

    $counts = array_count_values($col2);

    foreach ($col1 as $val) {
        @$sim[$val] += $val * ($counts[$val] ?? 0);
    }

    return $sim;
}

function main(string $filename, bool $part2)
{
    $cols = parse($filename, vecho::$verbose, $part2);

    if ($part2) {
        $values = part2(...$cols);
    } else {
        $values = part1(...$cols);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', false, 11);
run_part1('input', false);
echo "\n";

// 31 (9 + 4 + 0 + 0 + 9 + 9).
run_part2('example', false, 31);
run_part2('input', false);
echo "\n";

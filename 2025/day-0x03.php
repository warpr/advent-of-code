<?php
/**
 *   Copyright (C) 2024  Kuno Woudt <kuno@frob.nl>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of copyleft-next 0.3.1.  See copyleft-next-0.3.1.txt.
 *
 *   SPDX-License-Identifier: copyleft-next-0.3.1
 */

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $idx => $line) {
        $ret[] = trim($line);
    }

    return [$ret];
}

function find_highest_digit_pos(string $str)
{
    for ($i = 9; $i >= 0; $i--) {
        $idx = strpos($str, "{$i}");
        if ($idx !== false) {
            return $idx;
        }
    }

    return 0;
}

function part1($data)
{
    $ret = [];

    foreach ($data as $bank) {
        $search1 = substr($bank, 0, -1);
        $pos1 = find_highest_digit_pos($search1);
        $search2 = substr($bank, $pos1 + 1);
        $pos2 = $pos1 + 1 + find_highest_digit_pos($search2);
        vecho::msg("[$bank] found pos {$pos1} in {$search1} and pos {$pos2} in {$search2}");
        $ret[] = (int) "{$bank[$pos1]}{$bank[$pos2]}";
    }

    // 98 + 89 + 78 + 92 = 357

    return $ret;
}

function part2($data)
{
    return [23];
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, vecho::$verbose, $part2);

    if ($part2) {
        $values = part2(...$parsed);
    } else {
        $values = part1(...$parsed);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 357);
run_part1('input', false);
echo "\n";

run_part2('example', false, 3121910778619);
run_part2('input', false);
echo "\n";

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

function find_largest_stuff(string $bank, int $offset, int $count)
{
    if (empty($count)) {
        return '';
    }

    if ($count > 1) {
        $search = substr($bank, $offset, 1 - $count);
    } else {
        $search = substr($bank, $offset);
    }

    $pos = $offset + find_highest_digit_pos($search);

    vecho::msg("[$bank] $offset // $search found $pos");

    return $bank[$pos] . find_largest_stuff($bank, $pos + 1, $count - 1);
}

function part1($data)
{
    $ret = [];

    foreach ($data as $bank) {
        $str = find_largest_stuff($bank, 0, 2);
        $ret[] = (int) $str;
    }

    // 98 + 89 + 78 + 92 = 357

    return $ret;
}

function part2($data)
{
    $ret = [];

    foreach ($data as $bank) {
        $str = find_largest_stuff($bank, 0, 12);
        $ret[] = (int) $str;
    }

    return $ret;
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

run_part1('example', false, 357);
run_part1('input', false);
echo "\n";

run_part2('example', false, 3121910778619);
run_part2('input', false);
echo "\n";

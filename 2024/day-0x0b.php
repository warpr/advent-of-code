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

function parse(string $filename, bool $part2)
{
    $lines = file($filename);

    $stones = array_map('intval', explode(' ', trim($lines[0])));

    return $stones;
}

$blink = memoize(blink(...));

function blink_one_number(int $stone): array
{
    $stone_str = "{$stone}";
    $odd_number_of_digits = strlen($stone_str) % 2;

    if ($stone == 0) {
        return [1];
    } elseif (!$odd_number_of_digits) {
        $two_stones = str_split($stone_str, strlen($stone_str) >> 1);
        return [(int) $two_stones[0], (int) $two_stones[1]];
    } else {
        return [$stone * 2024];
    }
}

function blink(int $blink_left, int $stone)
{
    global $blink;

    if ($blink_left <= 0) {
        return 1;
    }

    $numbers = blink_one_number($stone);

    $ret = [];
    foreach ($numbers as $num) {
        $ret[] = $blink($blink_left - 1, $num);
    }

    return array_sum($ret);
}

function part1($stones)
{
    global $blink;

    $result = [];

    foreach ($stones as $stone) {
        $result[] = $blink(25, (int) $stone);
    }

    return $result;
}

function part2($stones)
{
    global $blink;

    $result = [];

    foreach ($stones as $stone) {
        $result[] = $blink(75, (int) $stone);
    }

    return $result;
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, $part2);

    if ($part2) {
        $values = part2($parsed);
    } else {
        $values = part1($parsed);
    }

    if (vecho::$verbose) {
        print_r($values);
    }

    return array_sum($values);
}

run_part1('example', false, 55312);
run_part1('input', false, 185205);
run_part1('input', false);
echo "\n";

run_part2('input', true);
echo "\n";

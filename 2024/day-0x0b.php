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

function blink($before)
{
    $after = [];

    foreach ($before as $stone) {
        $stone_str = "{$stone}";
        $odd_number_of_digits = strlen($stone_str) % 2;

        if ($stone == 0) {
            $after[] = 1;
        } elseif (!$odd_number_of_digits) {
            $two_stones = str_split($stone_str, strlen($stone_str) >> 1);
            $after[] = (int) $two_stones[0];
            $after[] = (int) $two_stones[1];
        } else {
            $after[] = $stone * 2024;
        }
    }

    return $after;
}

function part1($stones)
{
    $test = blink(explode(' ', '0 1 10 99 999'));
    $actual = implode(' ', $test);
    $expected = '1 2024 1 0 9 9 2021976';
    if ($expected !== $actual) {
        echo "Actual:   $actual\n";
        echo "Expected: $expected\n";
        die();
    }

    for ($i = 0; $i < 25; $i++) {
        vecho::msg("step $i:", implode(' ', $stones));

        if ($i > 5) {
            vecho::$verbose = false;
        }

        $stones = blink($stones);
    }

    return [count($stones)];
}

function part2($stones)
{
    return [23];
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

run_part1('example', true, 55312);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 81);
// run_part2('input', false);
echo "\n";

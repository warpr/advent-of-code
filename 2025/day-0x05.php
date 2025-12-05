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

    $ranges = [];
    $ingredients = [];

    foreach ($lines as $idx => $line) {
        if (str_contains($line, '-')) {
            [$start, $end] = explode('-', trim($line));
            $ranges[] = (object) compact('start', 'end');
            continue;
        }

        if (empty(trim($line))) {
            continue;
        }

        $ingredients[] = (int) trim($line);
    }

    return [$ranges, $ingredients];
}

function is_fresh(array $ranges, int $ingredient)
{
    foreach ($ranges as $range) {
        if ($ingredient >= $range->start && $ingredient <= $range->end) {
            return true;
        }
    }

    return false;
}

function part1($ranges, $ingredients)
{
    $ret = 0;
    foreach ($ingredients as $i) {
        if (is_fresh($ranges, $i)) {
            $ret++;
        }
    }

    return [$ret];
}

function part2($ranges, $ingredients)
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

run_part1('example', true, 3);
run_part1('input', false);
echo "\n";

run_part2('example', false, 43);
run_part2('input', false);
echo "\n";

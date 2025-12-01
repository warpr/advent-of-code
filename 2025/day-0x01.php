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

function part1($data)
{
    $current = 50;
    $count = 0;

    foreach ($data as $line) {
        $op = substr($line, 0, 1);
        $val = substr(trim($line), 1);

        if ($op === 'L') {
            $current -= $val;
        } else {
            $current += $val;
        }

        while ($current < 0) {
            $current += 100;
        }
        while ($current > 99) {
            $current -= 100;
        }

        if ($current === 0) {
            $count++;
            vecho::msg("ZERO {$line} \t=> {$current}");
        } else {
            vecho::msg("val  {$line} \t=> {$current}");
        }
    }

    return [$count];
}

function part2($data)
{
    return [5];
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

// So, if the dial were pointing at 11, a rotation of R8
// would cause the dial to point at 19. After that, a
// rotation of L19 would cause it to point at 0.

// So, if the dial were pointing at 5, a rotation of L10
// would cause it to point at 95. After that, a rotation
// of R5 could cause it to point at 0.

/*
Following these rotations would cause the dial to move as follows:

The dial starts by pointing at 50.
The dial is rotated L68 to point at 82.
The dial is rotated L30 to point at 52.
The dial is rotated R48 to point at 0.
The dial is rotated L5 to point at 95.
The dial is rotated R60 to point at 55.
The dial is rotated L55 to point at 0.
The dial is rotated L1 to point at 99.
The dial is rotated L99 to point at 0.
The dial is rotated R14 to point at 14.
The dial is rotated L82 to point at 32.
Because the dial points at 0 a total of three times during this process, the password in this example is 3.
*/

run_part1('example', false, 3);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 31);
// run_part2('input', false);
echo "\n";

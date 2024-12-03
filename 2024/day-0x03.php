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
    $body = file_get_contents($filename);

    if (!preg_match_all('/mul\(([0-9]{1,3}),([0-9]{1,3})\)/', $body, $matches)) {
        die('No valid instructions found');
    }

    return array_map(null, $matches[1], $matches[2]);
}

function part1($values)
{
    return array_map(fn($i) => $i[0] * $i[1], $values);
}

function part2($values)
{
    return [23];
}

function main(string $filename, bool $part2)
{
    $reports = parse($filename, $part2);

    if ($part2) {
        $values = part2($reports);
    } else {
        $values = part1($reports);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

// 161 (2*4 + 5*5 + 11*8 + 8*5).
run_part1('example', true, 161);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 4);
// run_part2('input', false);
// echo "\n";

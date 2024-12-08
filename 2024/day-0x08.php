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

    $ret = [];

    return $ret;
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, $part2);

    if ($part2) {
        $values = part2($parsed);
    } else {
        $values = part1($parsed);
    }

    return array_sum($values);
}

function part1($lines)
{
    return [23];
}

function part2($lines)
{
    return [23];
}

run_part1('example', false, 14);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 11387);
// run_part2('input', false);
echo "\n";

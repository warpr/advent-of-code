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

    $patterns = explode(', ', trim(array_shift($lines)));
    $designs = array_values(array_filter(array_map(trim(...), $lines)));

    return [$patterns, $designs];
}

function resolve_design($design, $patterns)
{
    foreach ($patterns as $p) {
        if ($p === $design) {
            return [$p];
        }

        if (!str_starts_with($design, $p)) {
            continue;
        }

        $head = [substr($design, 0, strlen($p))];
        $tail = resolve_design(substr($design, strlen($p)), $patterns);

        if ($tail) {
            return array_merge($head, $tail);
        }
    }

    return false;
}

function part1(array $input)
{
    list($patterns, $designs) = $input;

    $found = [];
    foreach ($designs as $d) {
        $seq = resolve_design($d, $patterns);
        vecho::msg("design $d made with", $seq);
        $found[] = $seq;
    }

    return [count(array_filter($found))];
}

function part2(array $input)
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

run_part1('example', true, 6);
run_part1('input', false);
echo "\n";

// run_part2('example', true, 10101);
// run_part2('input', true);
echo "\n";

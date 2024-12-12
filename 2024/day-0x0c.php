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

    $grid = [];
    foreach ($lines as $line) {
        $line = trim($line);

        $grid[] = $line;
    }

    return new grid($grid);
}

function part1($grid)
{
    $regions = [];

    foreach ($grid->walk() as $spot) {
        if (empty($regions[$spot->val])) {
            $regions[$spot->val] = (object) [
                'perimeter' => 0,
                'area' => 0,
            ];
        }

        $p =
            ($grid->look($spot->pos, N) == $spot->val ? 0 : 1) +
            ($grid->look($spot->pos, E) == $spot->val ? 0 : 1) +
            ($grid->look($spot->pos, S) == $spot->val ? 0 : 1) +
            ($grid->look($spot->pos, W) == $spot->val ? 0 : 1);

        $regions[$spot->val]->area++;
        $regions[$spot->val]->perimeter += $p;
    }

    $ret = [];
    foreach ($regions as $name => $region) {
        $cost = $region->perimeter * $region->area;
        vecho::msg("region $name:", $region, " (cost $cost)");
        $ret[] = $cost;
    }

    return $ret;
}

function part2($grid)
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

run_part1('example1', true, 140);
run_part1('example2', true, 772);
run_part1('example', true, 1930);
run_part1('input', false);
echo "\n";

run_part2('input', true);
echo "\n";

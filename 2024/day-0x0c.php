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

function find_region(grid $grid, pos $start)
{
    $todo = [$start];

    $name = $grid->get($start);
    $region = [];

    while (!empty($todo)) {
        $current = array_pop($todo);
        $current_str = (string) $current;
        $seen = $region[$current_str] ?? null;
        if ($seen) {
            continue;
        } else {
            $region[$current_str] = $current;
        }

        foreach ([N, E, S, W] as $dir) {
            $look_at = $current->add($dir);

            if ($grid->get($look_at) == $name) {
                $todo[] = $look_at;
            }
        }
    }

    return (object) ['name' => $name, 'spots' => $region];
}

function find_perimeter(grid $grid, $region)
{
    $ret = 0;

    foreach ($region->spots as $pos) {
        $val = $grid->get($pos);

        $ret +=
            ($grid->look($pos, N) == $val ? 0 : 1) +
            ($grid->look($pos, E) == $val ? 0 : 1) +
            ($grid->look($pos, S) == $val ? 0 : 1) +
            ($grid->look($pos, W) == $val ? 0 : 1);
    }

    return $ret;
}

function part1($grid)
{
    $seen = [];
    $regions = [];

    foreach ($grid->walk() as $spot) {
        $spot_str = (string) $spot->pos;
        $seen_this = $seen[$spot_str] ?? null;
        if ($seen_this) {
            continue;
        }

        $region = find_region($grid, $spot->pos);
        foreach ($region->spots as $pos) {
            $pos_str = (string) $pos;
            $seen[$pos_str] = $pos;
        }

        $regions[] = $region;
    }

    $ret = [];
    foreach ($regions as $region) {
        $area = count($region->spots);
        $perimeter = find_perimeter($grid, $region);

        $name = $region->name;
        $cost = $area * $perimeter;

        $data = compact('area', 'perimeter', 'cost');
        vecho::msg("region $name:", $data);
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

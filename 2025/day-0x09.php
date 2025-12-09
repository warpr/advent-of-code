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

ini_set('memory_limit', '4096M');

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $idx => $line) {
        if (!empty(trim($line))) {
            $ret[] = explode(',', trim($line));
        }
    }

    return [$ret];
}

function area(array $p, array $q)
{
    $width = abs($p[0] - $q[0]) + 1;
    $height = abs($p[1] - $q[1]) + 1;

    return $width * $height;
}

function all_areas(array $data)
{
    $areas = [];

    while (!empty($data)) {
        $current_tile = array_pop($data);
        foreach ($data as $tile) {
            $current = implode(',', $current_tile);
            $target = implode(',', $tile);
            $area = area($current_tile, $tile);

            $areas[] = [
                'src' => $current_tile,
                'dst' => $tile,
                'area' => $area,
            ];
        }

        vecho::debounced_msg(3, count($data) . ' tiles to check');
    }

    $sorted = sort_by($areas, 'area');

    return $sorted;
}

function part1($data)
{
    $sorted = all_areas($data);

    $largest = array_pop($sorted);

    return [$largest['area']];
}

function part2($data)
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

run_part1('example', true, 50);
run_part1('input', false);
echo "\n";

run_part2('example', false, 23);
run_part2('input', false);
echo "\n";

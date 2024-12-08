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

function all_pairs($list)
{
    $max = count($list);

    foreach ($list as $idx => $a) {
        for ($i = $idx + 1; $i < $max; $i++) {
            yield [$a, $list[$i]];
        }
    }
}

function part1($grid)
{
    $antinodes = clone $grid;

    // get a list of antennas and their locations
    $antennas = [];
    foreach ($grid->walk() as $loc) {
        if ($loc->val !== '.') {
            @$antennas[$loc->val][] = $loc->pos;
        }
    }

    foreach ($antennas as $type => $positions) {
        foreach (all_pairs($positions) as $pair) {
            list($a, $b) = $pair;
            $delta_a = new pos($a->x - $b->x, $a->y - $b->y);
            $delta_b = new pos($b->x - $a->x, $b->y - $a->y);
            $antinodes->set($a->add($delta_a), '#');
            $antinodes->set($b->add($delta_b), '#');
            $antinodes->render();
        }
    }

    $all = iterator_to_array($antinodes->find_all('#'));

    return [count($all)];
}

function part2($grid)
{
    return [23];
}

run_part1('example', true, 14);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 11387);
// run_part2('input', false);
echo "\n";

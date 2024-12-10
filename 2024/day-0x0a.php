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

function follow_the_path(grid $vis, grid $grid, pos $start)
{
    $height = (int) $grid->get($start);

    $ret = [];

    foreach ([N, E, S, W] as $direction) {
        $next_pos = $start->add($direction);
        $next_height = (int) $grid->get($next_pos);

        if ($next_height == $height + 1) {
            $ret[] = $next_pos;
        }
    }

    $vis->set($start, '.');

    return $ret;
}

function walk_the_trail(grid $grid, pos $start, bool $part2 = false)
{
    $visualize = clone $grid;
    $next = [$start];
    $ret = [];

    while (!empty($next)) {
        $current = $next;
        $next = [];
        foreach ($current as $pos) {
            if ($grid->get($pos) == '9') {
                if ($part2) {
                    $ret[] = $pos;
                } else {
                    $ret[(string) $pos] = $pos;
                }
            }

            foreach (follow_the_path($visualize, $grid, $pos) as $step) {
                $next[] = $step;
            }

            $visualize->render(10);
        }
    }

    return $ret;
}

function part1($grid)
{
    $grid->render();

    $trailheads = $grid->find_all('0');

    $end_counts = [];

    foreach ($trailheads as $start) {
        $trail_ends = walk_the_trail(clone $grid, $start);
        $end_counts[] = count($trail_ends);
    }

    return $end_counts;
}

function part2($grid)
{
    $grid->render();

    $trailheads = $grid->find_all('0');

    $end_counts = [];

    foreach ($trailheads as $start) {
        $trail_ends = walk_the_trail(clone $grid, $start, part2: true);
        $end_counts[] = count($trail_ends);
    }

    return $end_counts;
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

run_part1('example1', false, 1);
run_part1('example2', false, 2);
run_part1('example3', false, 4);
run_part1('example4', false, 3);
run_part1('example', false, 36);
run_part1('challenge', false, 464);
run_part1('input', false);
echo "\n";

run_part2('example5', false, 3);
run_part2('example6', false, 13);
run_part2('example7', false, 227);
run_part2('example', false, 81);

// https://www.reddit.com/r/adventofcode/comments/1hawlbo/2024_day_10_challenge_input/
run_part2('challenge', false, 16451);
run_part2('input', false);
echo "\n";

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
    foreach ($lines as $line) {
        // if (preg_match("/p=([0-9-]+),([0-9-]+), v=([0-9-]+),([0-9-]+)/", $line, $matches)) {
        if (preg_match('/p=([0-9-]+),([0-9-]+)\s+v=([0-9-]+),([0-9-]+)/', $line, $matches)) {
            $ret[] = (object) [
                'pos' => new pos((int) $matches[1], (int) $matches[2]),
                'vel' => new pos((int) $matches[3], (int) $matches[4]),
            ];
        }
    }

    return $ret;
}

function simulate(grid $grid, array &$input, pos $space)
{
    foreach ($input as $idx => $robot) {
        $grid->set($robot->pos, '.');
    }

    foreach ($input as $idx => $robot) {
        $robot->pos = $robot->pos->add_wrap($robot->vel, $space);
        $grid->set($robot->pos, 'o');
    }
}

function quadrants(array &$input, pos $space)
{
    $hline = $space->y >> 1;
    $vline = $space->x >> 1;

    $quadrants = [0, 0, 0, 0];
    foreach ($input as $robot) {
        $pos = $robot->pos;

        if ($pos->x < $vline && $pos->y < $hline) {
            $quadrants[0]++;
        } elseif ($pos->x > $vline && $pos->y < $hline) {
            $quadrants[1]++;
        } elseif ($pos->x < $vline && $pos->y > $hline) {
            $quadrants[2]++;
        } elseif ($pos->x > $vline && $pos->y > $hline) {
            $quadrants[3]++;
        }
    }

    return $quadrants;
}

function part1(array $input, pos $space)
{
    $grid = new grid(array_fill(0, $space->y, str_repeat('.', $space->x)));

    $grid->render();

    for ($s = 0; $s < 100; $s++) {
        simulate($grid, $input, $space);
        $grid->render(1);
    }

    return quadrants($input, $space);
}

function has_picture_frame(array &$input)
{
    $per_y = [];

    foreach ($input as $robot) {
        $per_y[$robot->pos->y][$robot->pos->x] = true;
    }

    foreach ($per_y as $y => $x_positions) {
        $x_list = array_keys($x_positions);
        sort($x_list);

        $consec_count = 0;
        $prev_x = null;
        foreach ($x_list as $x) {
            if ($prev_x === null) {
                $prev_x = $x;
                continue;
            }

            if ($x == $prev_x + 1) {
                $consec_count++;
            }

            $prev_x = $x;

            if ($consec_count > 25) {
                // found a 10 pixel horizontal line
                return true;
            }
        }
    }

    return false;
}

function part2($input, pos $space)
{
    $grid = new grid(array_fill(0, $space->y, str_repeat('.', $space->x)));

    $max_seconds = 100000000;

    for ($s = 0; $s < $max_seconds; $s++) {
        vecho::debounced_msg(5, "Simulating ($s seconds)");
        simulate($grid, $input, $space);

        if (has_picture_frame($input)) {
            $grid->render(1);
            vecho::msg("\n\nPicture found? ($s seconds)\n");
            return [$s + 1];
        }
    }
}

function main(string $filename, bool $part2)
{
    $space = new pos(101, 103);
    if ($filename === 'day-0x0e.example.txt') {
        $space = new pos(11, 7);
    }

    $parsed = parse($filename, $part2);

    if ($part2) {
        $values = part2($parsed, $space);
    } else {
        $values = part1($parsed, $space);
    }

    if (vecho::$verbose) {
        print_r($values);
    }

    $ret = 1;
    foreach ($values as $v) {
        $ret = $ret * $v;
    }

    return $ret;
}

run_part1('example', false, 12);
run_part1('input', false);
echo "\n";

// run_part2('example', true);
run_part2('input', true);
echo "\n";

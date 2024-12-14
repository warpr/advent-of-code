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

function part1(array $input, pos $space)
{
    $stupid_grid = array_fill(0, $space->y, str_repeat('.', $space->x));
    $grid = new grid($stupid_grid);

    $grid->render();

    for ($s = 0; $s < 100; $s++) {
        foreach ($input as $idx => $robot) {
            $grid->set($robot->pos, '.');
        }

        foreach ($input as $idx => $robot) {
            $robot->pos = $robot->pos->add_wrap($robot->vel, $space);
            $grid->set($robot->pos, 'o');
        }

        $grid->render(1);
    }

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

function part2($input, pos $space)
{
    return [23];
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

// run_part2('example', false, 875318608908);
// run_part2('input', false);
echo "\n";

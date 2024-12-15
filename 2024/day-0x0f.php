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
    while (!empty($lines)) {
        $line = trim(array_shift($lines));
        if (empty($line)) {
            break;
        }

        $grid[] = $line;
    }

    $grid = new grid($grid);

    $movements = [];
    foreach ($lines as $line) {
        foreach (str_split($line) as $chr) {
            switch ($chr) {
                case '^':
                    $movements[] = N;
                    break;
                case '>':
                    $movements[] = E;
                    break;
                case '<':
                    $movements[] = W;
                    break;
                case 'v':
                case 'V':
                    $movements[] = S;
                    break;
            }
        }
    }

    return compact('grid', 'movements');
}

function simulate(int $stepno, grid $grid, pos $robot, pos $dir)
{
    $pos = $robot;

    $current = '@';
    $found = [(object) ['val' => '@', 'pos' => $pos]];
    do {
        $pos = $pos->add($dir);
        $current = $grid->get($pos);
        $found[] = (object) ['val' => $current, 'pos' => $pos];
    } while ($current == 'O');

    $prefix = "$stepno. Seeing [" . implode('', array_column($found, 'val')) . ']';

    $end = array_pop($found);
    if ($end->val != '.') {
        vecho::msg($prefix . ', not moving');
        return $robot;
    }

    vecho::msg($prefix . ', moving (maybe pushing boxes)');

    $to_move = array_reverse($found);
    foreach ($to_move as $t) {
        $grid->set($t->pos->add($dir), $t->val);
    }
    $grid->set($robot, '.');

    return $robot->add($dir);
}

function part1(array $input)
{
    extract($input);

    $grid->render();

    $robot = $grid->find_first('@');

    foreach ($movements as $step => $m) {
        $robot = simulate($step, $grid, $robot, $m);
        $grid->render(10);
    }

    $boxes = $grid->find_all('O');

    $gps = [];
    foreach ($boxes as $box) {
        $gps[] = 100 * $box->y + $box->x;
    }

    return $gps;
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

run_part1('example1', true, 2028);
run_part1('example', true, 10092);
run_part1('input', false);
echo "\n";

// run_part2('example', true);
// run_part2('input', true);
echo "\n";

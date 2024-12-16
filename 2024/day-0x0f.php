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

function simulate_h(int $stepno, grid $grid, pos $robot, pos $dir)
{
    $pos = $robot;

    $current = '@';
    $found = [(object) ['val' => '@', 'pos' => $pos]];
    do {
        $pos = $pos->add($dir);
        $current = $grid->get($pos);
        $found[] = (object) ['val' => $current, 'pos' => $pos];
    } while ($current == '[' || $current == ']');

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

function next_step(grid $grid, array $locations, pos $dir)
{
    $next_row = [];

    foreach ($locations as $loc) {
        $front = $loc->pos->add($dir);
        $current = $grid->get($front);

        switch ($current) {
            case '[':
                $next_row[(string) $front] = (object) ['val' => $current, 'pos' => $front];
                $pos = $front->add(turn_right($dir));
                $val = $grid->get($pos);
                $next_row[(string) $pos] = (object) compact('val', 'pos');
                break;
            case ']':
                $next_row[(string) $front] = (object) ['val' => $current, 'pos' => $front];
                $pos = $front->add(turn_left($dir));
                $val = $grid->get($pos);
                $next_row[(string) $pos] = (object) compact('val', 'pos');
                break;
            case '.':
                break;
            default:
                return false;
        }
    }

    return $next_row;
}

function simulate_v(int $stepno, grid $grid, pos $robot, pos $dir)
{
    $rows = [];

    $next_row = [(object) ['val' => '@', 'pos' => $robot]];
    $rows[] = $next_row;
    while (true) {
        $next_row = next_step($grid, $next_row, $dir);
        if ($next_row === false) {
            return $robot;
        }
        if (empty($next_row)) {
            break;
        }
        $rows[] = $next_row;
    }

    $to_move = array_reverse($rows);
    foreach ($to_move as $row) {
        foreach ($row as $t) {
            $grid->set($t->pos->add($dir), $t->val);
            $grid->set($t->pos, '.');
            $grid->render(10);
        }
    }

    return $grid->find_first('@');
}

function simulate2(int $stepno, grid $grid, pos $robot, pos $dir)
{
    if (is_horizontal($dir)) {
        return simulate_h($stepno, $grid, $robot, $dir);
    } else {
        return simulate_v($stepno, $grid, $robot, $dir);
    }
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

function double_grid(grid $grid): grid
{
    $grid2 = [];
    foreach ($grid->grid as $row) {
        $line = [];
        foreach (str_split($row) as $chr) {
            if ($chr == 'O') {
                $line[] = '[';
                $line[] = ']';
            } elseif ($chr == '@') {
                $line[] = '@';
                $line[] = '.';
            } else {
                $line[] = $chr;
                $line[] = $chr;
            }
        }
        $grid2[] = implode('', $line);
    }

    return new grid($grid2);
}

function part2(array $input)
{
    extract($input);

    $grid = double_grid($grid);
    $robot = $grid->find_first('@');

    foreach ($movements as $step => $m) {
        $robot = simulate2($step, $grid, $robot, $m);
        $grid->render($step > 180 ? 500 : 1);

        if ($step > 220) {
            die();
        }
    }

    $boxes = $grid->find_all('[');

    $gps = [];
    foreach ($boxes as $box) {
        $gps[] = 100 * $box->y + $box->x;
    }

    return $gps;
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

run_part1('example1', false, 2028);
run_part1('example', false, 10092);
run_part1('input', false);
echo "\n";

run_part2('example', true, 9021);
// run_part2('input', true);
echo "\n";

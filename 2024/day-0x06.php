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

function guard_move(grid $grid, pos $pos, pos $dir)
{
    if ($grid->look($pos, $dir) === null) {
        return [false, $pos->add($dir), $dir];
    }

    if ($grid->look($pos, $dir) === '#') {
        return [true, $pos, turn_right($dir)];
    }

    return [true, $pos->add($dir), $dir];
}

function find_loops($grid)
{
    return [23];
}

function find_loops_old($grid)
{
    $ret = [];

    foreach ([N, E, S, W] as $first_corner_val => $start_dir) {
        vecho::msg("\nSearching valid loops starting at corner", $first_corner_val, $start_dir);
        foreach ($grid->find_all("{$first_corner_val}") as $first_corner_pos) {
            vecho::msg(' - first_corner_val', $first_corner_val, $first_corner_pos);

            $next_dir = turn_right($start_dir);
            $prev_dir = turn_right($next_dir);
            $second_corner_val = (string) (($first_corner_val + 1) % 4);
            $last_corner_val = (string) (($first_corner_val + 3) % 4);

            $second_corner_pos = $grid->search(
                $first_corner_pos,
                $next_dir,
                $second_corner_val,
                '#'
            );
            $last_corner_pos = $grid->search($first_corner_pos, $prev_dir, $last_corner_val, '#');

            if (empty($second_corner_pos) || empty($last_corner_pos)) {
                continue;
            }

            vecho::msg(
                '   - found some corners',
                compact(
                    'second_corner_pos',
                    'next_dir',
                    'second_corner_val',
                    'last_corner_pos',
                    'prev_dir',
                    'last_corner_val'
                )
            );

            if (is_horizontal($prev_dir)) {
                $missing_corner = new pos($last_corner_pos->x, $second_corner_pos->y);
            } else {
                $missing_corner = new pos($second_corner_pos->x, $last_corner_pos->y);
            }

            vecho::msg('   - missing corner is', $missing_corner);

            if ($grid->get($missing_corner) === '#') {
                // obstacle, this probably cannot happen?
                vecho::msg('   - found an obstacle at', $missing_corner);
                continue;
            }

            $second_corner = $grid->search(
                $missing_corner,
                opposite_dir($prev_dir),
                $second_corner_val,
                '#'
            );
            $last_corner = $grid->search(
                $missing_corner,
                opposite_dir($next_dir),
                $last_corner_val,
                '#'
            );

            // validate if we can get to the other corners without obstacles
            if ($second_corner == $second_corner_pos && $last_corner == $last_corner_pos) {
                $obstacle_pos = $missing_corner->add(turn_right($next_dir));
                vecho::msg(
                    'made a square!',
                    compact(
                        'obstacle_pos',
                        'second_corner_pos',
                        'last_corner_pos',
                        'missing_corner'
                    )
                );
                $ret[] = $obstacle_pos;
            } else {
                vecho::msg(
                    '      - not a square',
                    compact('second_corner', 'second_corner_pos', 'last_corner', 'last_corner_pos')
                );
            }
        }
    }

    return $ret;
}

function part1($grid)
{
    $pos = $grid->find_first('^');
    $dir = N;

    vecho::msg('start', compact('pos', 'dir'));

    $keep_going = true;

    while ($keep_going) {
        $grid->set($pos, 'X');
        list($keep_going, $pos, $dir) = guard_move($grid, $pos, $dir);

        $grid->render();
    }

    vecho::msg("\nGuard out of bounds at", compact('pos', 'dir'));
    vecho::msg('');

    return [$grid->count('X')];
}

function simulate_guard($grid)
{
    $pos = $grid->find_first('^');
    $dir = N;

    $keep_going = true;
    $seen = [];

    while ($keep_going) {
        $grid->set($pos, 'o');
        $key = "{$pos}/{$dir}";

        if (empty($seen[$key])) {
            $seen[$key] = 1;
        } else {
            $seen[$key]++;

            if ($seen[$key] > 2) {
                vecho::msg("I think we're looping?", compact('key'));
                return false;
            }
        }

        list($keep_going, $pos, $dir) = guard_move($grid, $pos, $dir);

        $grid->render(1000);
    }

    return true;
}

function part2($grid)
{
    $ret = [];

    $verbose = vecho::$verbose;

    $escape_path = clone $grid;
    vecho::$verbose = false;
    simulate_guard($escape_path);
    vecho::$verbose = $verbose;

    $escape_path->render();

    foreach ($grid->grid as $y => $line) {
        foreach (str_split($line) as $x => $val) {
            if ($val !== '.') {
                continue;
            }

            $obstacle_pos = new pos($x, $y);
            if ($escape_path->get($obstacle_pos) !== 'o') {
                // guard doesn't come here
                continue;
            }

            $copy = clone $grid;
            $copy->set($obstacle_pos, '#');

            vecho::$verbose = false;
            $escaped = simulate_guard($copy);
            vecho::$verbose = $verbose;

            if (!$escaped) {
                vecho::msg('possible loop at', compact('obstacle_pos'));
                $ret[] = $obstacle_pos;
            }
        }
    }

    return [count($ret)];
}

run_part1('example', false, 41);
run_part1('input', false);
echo "\n";

run_part2('example', true, 6);
run_part2('input', true);
echo "\n";

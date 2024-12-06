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
        $dir = turn_right($dir);
    }

    return [true, $pos->add($dir), $dir];
}

function find_loops($grid)
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

function part2($grid)
{
    $pos = $grid->find_first('^');
    $dir = N;

    vecho::msg('start', compact('pos', 'dir'));

    $keep_going = true;

    while ($keep_going) {
        $draw = $dir == N || $dir == S ? '|' : '-';
        $old_dir = $dir;
        $old_pos = $pos;

        $prev_val = $grid->get($pos);
        // don't overwrite our loop corners
        if (!is_numeric($prev_val)) {
            $grid->set($pos, $draw);
        }
        list($keep_going, $pos, $dir) = guard_move($grid, $pos, $dir);

        if ($old_dir != $dir) {
            if (is_numeric($prev_val)) {
                // presumably this doesn't happen
                $grid->set($old_pos, '8');
            } else {
                // changed direction, mark possible loop corner with
                // css border index
                switch ($old_dir) {
                    case N:
                        $grid->set($old_pos, '0');
                        break;
                    case E:
                        $grid->set($old_pos, '1');
                        break;
                    case S:
                        $grid->set($old_pos, '2');
                        break;
                    case W:
                        $grid->set($old_pos, '3');
                        break;
                }
            }
        }

        $grid->render();
    }

    vecho::msg("\nGuard out of bounds at", compact('pos', 'dir'));
    vecho::msg('');

    $loops = find_loops($grid);
    // $grid->render();

    /*
       expected obstacles at
       (3, 6) found
       (6, 7) found
       (7, 7) found
       (1, 8) found
       (3, 8)
       (7, 9) found
    */

    return [count($loops)];
}

run_part1('example', false, 41);
run_part1('input', false);
echo "\n";

run_part2('example', true, 6);
// run_part2('input', false);
echo "\n";

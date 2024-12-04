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

    return $grid;
}

/*
___0123456789
0  ....XXMAS.   (5,0 e) (4,0 se)
1  .SAMXMS...   (4,1 w)
2  ...S..A...
3  ..A.A.MS.X   (9,3 s) (9,3 sw)
4  XMASAMX.MM   (0,4 e) (6,4 w) (6,4 n)
5  X.....XA.A   (0,5 ne) (6,5 nw)
6  S.S.S.S.SS
7  .A.A.A.A.A
8  ..M.M.M.MM
9  .X.X.XMASX   (5,9 e) (9,9 n) (1,9 ne) (3,9 ne) (5,9 ne)
*/

function has_xmas($grid, $at)
{
    extract($at);

    $max_x = strlen($grid[0]) - 1;
    $max_y = count($grid) - 1;

    $xpos = $x;
    $ypos = $y;

    foreach (str_split('XMAS') as $idx => $chr) {
        if ($xpos < 0 || $xpos > $max_x) {
            return 0;
        }

        if ($ypos < 0 || $ypos > $max_y) {
            return 0;
        }

        $found = $grid[$ypos][$xpos] ?? null;
        if ($found !== $chr) {
            return 0;
        }

        $xpos += $xdelta;
        $ypos += $ydelta;
    }

    vecho::msg('Found XMAS', $at);

    return 1;
}

function find_xmas($grid, $xdelta, $ydelta)
{
    $ret = [];

    foreach ($grid as $y => $grid_line) {
        $line = str_split($grid_line);
        foreach ($line as $x => $chr) {
            $ret[] = has_xmas($grid, compact('x', 'y', 'xdelta', 'ydelta'));
        }
    }

    return array_sum($ret);
}

/*
___0123456789
0  ....XXMAS.   (5,0 e) (4,0 se)
1  .SAMXMS...   (4,1 w)
2  ...S..A...
3  ..A.A.MS.X   (9,3 s) (9,3 sw)
4  XMASAMX.MM   (0,4 e) (6,4 w) (6,4 n)
5  X.....XA.A   (0,5 ne) (6,5 nw)
6  S.S.S.S.SS
7  .A.A.A.A.A
8  ..M.M.M.MM
9  .X.X.XMASX   (5,9 e) (9,9 n) (1,9 ne) (3,9 ne) (5,9 ne)
*/

function has_mas($grid, $at)
{
    extract($at);

    if ($grid[$y][$x] !== 'A') {
        return 0;
    }

    $max_x = strlen($grid[0]) - 1;
    $max_y = count($grid) - 1;

    if ($x <= 0 || $x >= $max_x) {
        return 0;
    }

    if ($y <= 0 || $y >= $max_y) {
        return 0;
    }

    $sw = implode('', [$grid[$y - 1][$x - 1], $grid[$y][$x], $grid[$y + 1][$x + 1]]);

    $se = implode('', [$grid[$y - 1][$x + 1], $grid[$y][$x], $grid[$y + 1][$x - 1]]);

    if (($sw === 'MAS' || $sw === 'SAM') && ($se === 'MAS' || $se === 'SAM')) {
        vecho::msg('Found one', $at, $sw, $se);
        return 1;
    }

    return 0;
}

function find_mas($grid)
{
    $ret = [];

    foreach ($grid as $y => $grid_line) {
        $line = str_split($grid_line);
        foreach ($line as $x => $chr) {
            $ret[] = has_mas($grid, compact('x', 'y'));
        }
    }

    return array_sum($ret);
}

function part1($grid)
{
    $ret = [];

    // horizontal
    $ret[] = find_xmas($grid, 1, 0);
    $ret[] = find_xmas($grid, -1, 0);

    // vertical
    $ret[] = find_xmas($grid, 0, 1);
    $ret[] = find_xmas($grid, 0, -1);

    // diagnonal
    $ret[] = find_xmas($grid, 1, 1);
    $ret[] = find_xmas($grid, 1, -1);
    $ret[] = find_xmas($grid, -1, 1);
    $ret[] = find_xmas($grid, -1, -1);

    return $ret;
}

function part2($grid)
{
    $ret = [];

    $ret[] = find_mas($grid);

    return $ret;
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
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', false, 18);
run_part1('input', false);
echo "\n";

run_part2('example', true, 9);
run_part2('input', false);
echo "\n";

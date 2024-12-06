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

function clear_screen()
{
    echo chr(27) . '[2J';
    echo chr(27) . '[H';
}

class pos
{
    function __construct(public readonly int $x, public readonly int $y)
    {
    }

    function add(pos $pos)
    {
        return new pos($this->x + $pos->x, $this->y + $pos->y);
    }
}

const N = new pos(0, -1);
const NE = new pos(1, -1);
const E = new pos(1, 0);
const SE = new pos(1, 1);
const S = new pos(0, 1);
const SW = new pos(-1, 1);
const W = new pos(-1, 0);
const NW = new pos(-1, -1);

class grid
{
    public array $grid;
    public readonly int $size_x;
    public readonly int $size_y;

    function __construct($grid)
    {
        $size_x = 0;
        foreach ($grid as $line) {
            $len = strlen($line);
            if ($len > $size_x) {
                $size_x = $len;
            }
        }

        $this->size_x = $size_x;
        $this->size_y = count($grid);
        $this->grid = $grid;
    }

    function get(pos $pos, $default = null)
    {
        if ($pos->x < 0 || $pos->x >= $this->size_x || $pos->y < 0 || $pos->y >= $this->size_y) {
            return $default;
        }

        return $this->grid[$pos->y][$pos->x] ?? $default;
    }

    function set(pos $pos, $val = null)
    {
        if ($pos->x < 0 || $pos->x >= $this->size_x || $pos->y < 0 || $pos->y >= $this->size_y) {
            vecho::msg('[WARNING] not writing out of bounds of the grid', compact('pos', 'val'));
            return;
        }

        $this->grid[$pos->y][$pos->x] = $val;
    }

    function is_val($pos, $val)
    {
        return $this->get($pos->x, $pos->y) === $val;
    }

    function find_first($val)
    {
        foreach ($this->grid as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                if ($char === $val) {
                    return new pos($x, $y);
                }
            }
        }
    }

    function count($val)
    {
        $ret = 0;

        foreach ($this->grid as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                if ($char === $val) {
                    $ret++;
                }
            }
        }

        return $ret;
    }

    function look(pos $pos, pos $dir)
    {
        return $this->get($pos->add($dir));
    }

    function render()
    {
        if (!vecho::$verbose) {
            return;
        }

        clear_screen();

        foreach ($this->grid as $line) {
            echo $line . "\n";
        }

        usleep(50 * 1000);
    }
}

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

function turn_right(pos $dir)
{
    switch ($dir) {
        case N:
            return E;
        case E:
            return S;
        case S:
            return W;
        default:
            return N;
    }
}

function guard_move(grid $grid, pos $pos, pos $dir)
{
    if ($grid->look($pos, $dir) === null) {
        throw new \Exception('out of bounds');
    }

    if ($grid->look($pos, $dir) === '#') {
        $dir = turn_right($dir);
    }

    return [$pos->add($dir), $dir];
}

function part1($grid)
{
    $pos = $grid->find_first('^');
    $dir = N;

    vecho::msg('start', compact('pos', 'dir'));

    try {
        while (true) {
            $grid->set($pos, 'X');
            list($pos, $dir) = guard_move($grid, $pos, $dir);

            $grid->render();
        }
    } catch (\Exception $e) {
        vecho::msg("\nGuard out of bounds at", compact('pos', 'dir'));
        vecho::msg('');
    }

    return [$grid->count('X')];
}

function part2($grid)
{
    return [23];
}

run_part1('example', true, 41);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 123);
// run_part2('input', false);
echo "\n";

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

class pos implements JsonSerializable
{
    function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly string $name = ''
    ) {
    }

    function add(pos $pos)
    {
        return new pos($this->x + $pos->x, $this->y + $pos->y);
    }

    function __toString(): string
    {
        if (empty($this->name)) {
            return '(' . $this->x . ', ' . $this->y . ')';
        } else {
            return $this->name;
        }
    }

    function jsonSerialize(): string
    {
        return (string) $this;
    }
}

const N = new pos(0, -1, 'north');
const NE = new pos(1, -1, 'north-east');
const E = new pos(1, 0, 'east');
const SE = new pos(1, 1, 'south-east');
const S = new pos(0, 1, 'south');
const SW = new pos(-1, 1, 'south-west');
const W = new pos(-1, 0, 'west');
const NW = new pos(-1, -1, 'north-west');

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

function opposite_dir(pos $dir)
{
    return turn_right(turn_right($dir));
}

function is_horizontal(pos $dir)
{
    return $dir->y == 0 && $dir->x != 0;
}

function is_vertical(pos $dir)
{
    return $dir->y != 0 && $dir->x == 0;
}

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
            return false;
        }

        $this->grid[$pos->y][$pos->x] = $val;
        return true;
    }

    function is_val($pos, $val)
    {
        return $this->get($pos->x, $pos->y) === $val;
    }

    function walk($fn = null)
    {
        foreach ($this->grid as $y => $line) {
            foreach (str_split($line) as $x => $val) {
                $pos = new pos($x, $y);
                if (empty($fn)) {
                    yield (object) compact('pos', 'val');
                } else {
                    $something = $fn($pos, $val);
                    if ($something !== null) {
                        yield $something;
                    }
                }
            }
        }
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

    function search(pos $start, pos $dir, $needle, $abort)
    {
        $pos = $start;
        while (true) {
            switch ($this->get($pos)) {
                case null:
                    // not found
                    return false;
                case $abort:
                    // found some kind of obstacle
                    return false;
                case $needle:
                    // found it
                    return $pos;
                default:
                    // keep searching
                    $pos = $pos->add($dir);
            }
        }
    }

    function find_all($val)
    {
        return $this->walk(fn($pos, $char) => $char === $val ? $pos : null);
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

    function render(int $sleep = 25)
    {
        if (!vecho::$verbose) {
            return;
        }

        clear_screen();

        foreach ($this->grid as $line) {
            echo $line . "\n";
        }

        usleep($sleep * 1000);
    }
}

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

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $idx => $line) {
        $ret[] = trim($line);
    }

    return [$ret];
}

function can_be_accessed(grid $grid, pos $pos): bool
{
    $neighbours = implode('', [
        $grid->get($pos->add(N)),
        $grid->get($pos->add(NE)),
        $grid->get($pos->add(E)),
        $grid->get($pos->add(SE)),
        $grid->get($pos->add(S)),
        $grid->get($pos->add(SW)),
        $grid->get($pos->add(W)),
        $grid->get($pos->add(NW)),
    ]);

    $count = substr_count($neighbours, '@');

    return $count < 4;
}

function part1($data)
{
    $grid = new grid($data);

    $ret = [];

    foreach ($grid->walk() as $item) {
        if ($item->val != '@') {
            continue;
        }

        if (can_be_accessed($grid, $item->pos)) {
            $ret[] = 1;
        }
    }

    return $ret;
}

function find_rolls_to_remove(grid $grid)
{
    $to_remove = [];

    foreach ($grid->walk() as $item) {
        if ($item->val != '@') {
            continue;
        }

        if (can_be_accessed($grid, $item->pos)) {
            $to_remove[] = $item->pos;
        }
    }

    return $to_remove;
}

function part2($data)
{
    $ret = [];

    $grid = new grid($data);

    do {
        $rolls = find_rolls_to_remove($grid);
        $ret[] = count($rolls);

        foreach ($rolls as $remove) {
            $grid->set($remove, '.');
        }
    } while (!empty($rolls));

    return $ret;
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, vecho::$verbose, $part2);

    if ($part2) {
        $values = part2(...$parsed);
    } else {
        $values = part1(...$parsed);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', false, 13);
run_part1('input', false);
echo "\n";

run_part2('example', true, 43);
run_part2('input', false);
echo "\n";

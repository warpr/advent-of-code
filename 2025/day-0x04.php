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

function part1($data)
{
    $grid = new grid($data);

    $ret = [];

    foreach ($grid->walk() as $item) {
        if ($item->val != '@') {
            continue;
        }

        $neighbours = implode('', [
            $grid->get($item->pos->add(N)),
            $grid->get($item->pos->add(NE)),
            $grid->get($item->pos->add(E)),
            $grid->get($item->pos->add(SE)),
            $grid->get($item->pos->add(S)),
            $grid->get($item->pos->add(SW)),
            $grid->get($item->pos->add(W)),
            $grid->get($item->pos->add(NW)),
        ]);

        $count = substr_count($neighbours, '@');
        if ($count < 4) {
            $ret[] = 1;
        }

        vecho::msg(
            "{$item->pos->y},{$item->pos->x} {$item->val} neigbours: {$count} {$neighbours}",
        );
    }

    return $ret;
}

function part2($data)
{
    $ret = [];

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

run_part1('example', true, 13);
run_part1('input', false);
echo "\n";

run_part2('example', false, 3121910778619);
run_part2('input', false);
echo "\n";

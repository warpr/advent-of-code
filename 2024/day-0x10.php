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

    return new grid($grid);
}

class edge
{
    function __construct(
        public readonly pos $pos,
        public readonly pos $dir,
        public readonly array $path
    ) {
    }

    function __toString()
    {
        return '<' . $this->pos->x . ',' . $this->pos->y . ',' . $this->dir->name . '>';
    }
}

function shortest_paths(grid $grid): array
{
    $grid->render();
    $start = $grid->find_first('S');
    $target = $grid->find_first('E');

    $queue = new \SplPriorityQueue();
    $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    $queue->insert(new edge($start, E, []), 0);
    $visited = [];

    $solutions = [];
    foreach ($queue as $item) {
        $edge = $item['data'];
        $cost = -$item['priority'];

        $pos = $edge->pos;
        $dir = $edge->dir;
        $path = $edge->path;

        $path[] = $edge->pos;

        $visited[(string) $edge] = true;

        if ($pos->x == $target->x && $pos->y == $target->y) {
            $solutions[] = compact('path', 'cost');
        }

        $check_next = [
            (object) ['pos' => $pos->add($dir), 'dir' => $dir, 'cost' => 1],
            (object) ['pos' => $pos, 'dir' => turn_left($dir), 'cost' => 1000],
            (object) ['pos' => $pos, 'dir' => turn_right($dir), 'cost' => 1000],
        ];

        foreach ($check_next as $check) {
            $val = $grid->get($check->pos);

            $new_edge = new edge($check->pos, $check->dir, $path);
            if (!empty($visited[(string) $new_edge])) {
                continue;
            }

            if ($val === '.' || $val === 'S' || $val === 'E') {
                $queue->insert($new_edge, -($cost + $check->cost));
            }
        }
    }

    return $solutions;
}

function part1($grid)
{
    $solutions = shortest_paths($grid);
    return [min(array_column($solutions, 'cost'))];
}

function part2($grid)
{
    $solutions = shortest_paths($grid);
    $cost = min(array_column($solutions, 'cost'));

    foreach ($solutions as $item) {
        if ($item['cost'] != $cost) {
            continue;
        }

        foreach ($item['path'] as $pos) {
            $grid->set($pos, 'o');
            $grid->render(10);
        }
    }

    return [count(iterator_to_array($grid->find_all('o')))];
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

run_part1('kuno1', false, 1004);
run_part1('kuno', false, 1007);
run_part1('example', false, 7036);
run_part1('example2', false, 21148);
run_part1('input', false);
echo "\n";

run_part2('kuno1', false, 5);
run_part2('kuno', false, 8);
run_part2('example', false, 45);
run_part2('example2', false, 149);
run_part2('input', false, 465);
echo "\n";

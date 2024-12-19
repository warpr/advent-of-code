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

ini_set('memory_limit', '28672M');

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        $parts = explode(',', trim($line));
        if (count($parts) > 1) {
            $ret[] = new pos((int) $parts[0], (int) $parts[1]);
        }
    }

    return $ret;
}

class edge
{
    function __construct(public readonly pos $pos, public readonly array $path)
    {
    }

    function __toString()
    {
        return '<' . $this->pos->x . ',' . $this->pos->y . '>';
    }
}

function shortest_paths(grid $grid): array
{
    $grid->render();
    $tmp = clone $grid;

    $start = $grid->find_first('S');
    $target = $grid->find_first('E');

    $queue = new \SplPriorityQueue();
    $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    $queue->insert(new edge($start, []), 0);
    $visited = [];

    $solutions = [];
    foreach ($queue as $item) {
        $edge = $item['data'];
        $cost = -$item['priority'];

        $pos = $edge->pos;
        $path = $edge->path;
        $path[] = $edge->pos;

        $visited[(string) $edge] = $edge->pos;
        $tmp->set($edge->pos, 'v');
        $tmp->debounced_render();

        if ($pos->x == $target->x && $pos->y == $target->y) {
            $solutions[] = compact('path', 'cost');
        }

        $check_next = [
            (object) ['pos' => $pos->add(N), 'cost' => 1],
            (object) ['pos' => $pos->add(E), 'cost' => 1],
            (object) ['pos' => $pos->add(S), 'cost' => 1],
            (object) ['pos' => $pos->add(W), 'cost' => 1],
        ];

        foreach ($check_next as $check) {
            $val = $grid->get($check->pos);

            $new_edge = new edge($check->pos, $path);
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

function part1(grid $grid, array $input)
{
    $stop = 1024;
    if ($grid->size_x < 10) {
        $stop = 12;
    }

    foreach ($input as $idx => $pos) {
        if ($idx >= $stop) {
            break;
        }

        $grid->set($pos, '#');
    }

    $grid->set(new pos(0, 0), 'S');
    $grid->set(new pos($grid->size_x - 1, $grid->size_y - 1), 'E');
    $grid->render();

    echo "Calculating shortest paths....\n";

    $solutions = shortest_paths($grid);
    $cost = min(array_column($solutions, 'cost'));

    foreach ($solutions as $item) {
        if ($item['cost'] != $cost) {
            continue;
        }

        foreach ($item['path'] as $pos) {
            $grid->set($pos, 'o');
            $grid->render(100);
        }
        break;
    }

    // don't count first step
    $grid->set(new pos(0, 0), 'S');

    return [count(iterator_to_array($grid->find_all('o')))];
}

function part2(grid $grid, array $input)
{
    return [23];
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, $part2);

    if (str_contains($filename, 'example')) {
        $grid = grid::create(7, 7);
    } else {
        $grid = grid::create(71, 71);
    }

    if ($part2) {
        $values = part2($grid, $parsed);
    } else {
        $values = part1($grid, $parsed);
    }

    if (vecho::$verbose) {
        print_r($values);
    }

    return array_sum($values);
}

// run_part1('example', true, 22);
run_part1('input', true);
echo "\n";

// run_part2('example', true, 10101);
// run_part2('input', true);
echo "\n";

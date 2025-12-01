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

function apply_rotations($data)
{
    $current = 50;
    $count = 0;
    $clicks = 0;
    $end_click = false;

    vecho::msg("START        \t=> {$current} \t(clicks {$clicks}, count {$count})");

    foreach ($data as $line) {
        $op = substr($line, 0, 1);
        $val = substr(trim($line), 1);

        while ($val > 0) {
            if ($current === 0) {
                vecho::msg(
                    "LINE {$line} (val {$val}) \t=> {$current} \t(clicks {$clicks}, count {$count})",
                );
                $clicks++;
            }

            $val--;
            if ($op === 'L') {
                $current--;
            } else {
                $current++;
            }

            if ($current > 99) {
                $current -= 100;
            }
            if ($current < 0) {
                $current += 100;
            }
        }

        if ($current === 0) {
            $count++;
        }
    }

    if ($current === 0) {
        $clicks++;
    }

    vecho::msg("END          \t=> {$current} \t(clicks {$clicks}, count {$count})");

    return (object) compact('count', 'clicks');
}

function part1($data)
{
    $result = apply_rotations($data);

    return [$result->count];
}

function part2($data)
{
    $debug = [
        // Be careful: if the dial were pointing at 50, a single
        // rotation like R1000 would cause the dial to point at
        // 0 ten times before returning back to 50!

        ['try' => ['R1000'], 'expected' => 10],
        ['try' => ['R50', 'L100'], 'expected' => 2],
        ['try' => ['R50', 'L200'], 'expected' => 3],
    ];

    foreach ($debug as $scenario) {
        $actual = apply_rotations($scenario['try']);

        if ($actual->clicks != $scenario['expected']) {
            echo 'Broke ' . implode(' ', $scenario['try']) . "\n";
            exit(0);
        }
        vecho::msg(' --- ');
    }

    echo "\n";

    $result = apply_rotations($data);

    return [$result->clicks];
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

run_part1('example', false, 3);
run_part1('input', false);
echo "\n";

run_part2('example', false, 6);
run_part2('input', false);
echo "\n";

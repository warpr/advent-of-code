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
        $len = strlen($line);
        for ($x = 0; $x < $len; $x++) {
            @$ret[$x] .= $line[$x];
        }
    }

    $problems = [];
    $problem = [];
    foreach ($ret as $line) {
        if (empty(trim($line))) {
            $problems[] = $problem;
            $problem = [];
        } else {
            $problem[] = $line;
        }
    }

    if (!empty($problem)) {
        $problems[] = $problem;
    }

    $ret = [];
    foreach ($problems as $problem) {
        $current = [];
        foreach ($problem as $idx => $columns) {
            $len = strlen($columns);
            for ($y = 0; $y < $len; $y++) {
                @$current[$y] .= $columns[$y];
            }
        }
        $ret[] = $current;
    }

    return [$ret];
}

function part1($data)
{
    $ret = [];

    foreach ($data as $problem) {
        $operation = trim(array_pop($problem));
        $values = [];
        foreach ($problem as $val) {
            $values[] = (int) trim($val);
        }

        if ($operation === '+') {
            $result = array_sum($values);
        } elseif ($operation === '*') {
            $result = array_product($values);
        } else {
            continue;
        }

        $ret[] = $result;
    }

    return $ret;
}

function part2($data)
{
    $ret = 23;

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

run_part1('example', true, 4277556);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 14);
// run_part2('input', false);
echo "\n";

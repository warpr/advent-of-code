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

    $machines = [];
    $current = [];

    foreach ($lines as $line) {
        if (preg_match("/Button ([AB]+): X\+([0-9]+), Y\+([0-9]+)/", $line, $matches)) {
            $name = strtolower('button-' . $matches[1]);
            $current[$name] = [ "x" => (int) $matches[2], "y" => (int) $matches[3] ];
        }

        if (preg_match("/Prize: X=([0-9]+), Y=([0-9]+)/", $line, $matches)) {
            $current['prize'] = [ "x" => (int) $matches[1], "y" => (int) $matches[2] ];
        }

        if (empty(trim($line))) {
            $machines[] = $current;
            $current = [];
        }
    }

    if (!empty($current)) {
        $machines[] = $current;
    }

    return $machines;
}

function brute_force($machine) {
    $px = $machine['prize']['x'];
    $py = $machine['prize']['y'];
    $ax = $machine['button-a']['x'];
    $ay = $machine['button-a']['y'];
    $bx = $machine['button-b']['x'];
    $by = $machine['button-b']['y'];

    $answers = [];
    for ($a = 0; $a < 100; $a++) {
        for ($b = 0; $b < 100; $b++) {
            $x = ($ax * $a) + ($bx * $b);
            $y = ($ay * $a) + ($by * $b);

            if ($x == $px && $y == $py) {
                $answers[] = [ $a, $b ];
            }
        }
    }

    return $answers;
}

function to_integer(float $val) {
    $tmp = abs(round($val) - $val);
    if ($tmp < 0.0001) {
        return (int) round($val);
    }

    return null;
}

function math($machine) {
    $px = $machine['prize']['x'] + 10000000000000;
    $py = $machine['prize']['y'] + 10000000000000;
    $ax = $machine['button-a']['x'];
    $ay = $machine['button-a']['y'];
    $bx = $machine['button-b']['x'];
    $by = $machine['button-b']['y'];

    $b = to_integer(($py - $px * $ay / $ax) / ($by - $ay * $bx / $ax));
    if ($b === null) {
        return [];
    }
    $a = to_integer($px / $ax - $b * $bx / $ax);
    if ($a === null) {
        return [];
    }

    return [ [ $a, $b ] ];
}

function cost(array $answers) {
    return $answers[0] * 3 + $answers[1];
}

function part1($input)
{
    $ret = [];

    foreach ($input as $idx => $machine) {
        $valid_answers = brute_force($machine);
        if (empty($valid_answers)) {
            vecho::msg("machine $idx has no valid answers", compact('machine'));
            continue;
        }

        $costs = array_map('cost', $valid_answers);
        sort($costs);
        vecho::msg("machine $idx valid answers", compact('machine', 'valid_answers', 'costs'));
        $ret[] = array_shift($costs);
    }

    vecho::$verbose = false;

    return $ret;
}

function part2($input)
{
    $ret = [];

    foreach ($input as $idx => $machine) {
        $valid_answers = math($machine);
        if (empty($valid_answers)) {
            vecho::msg("machine $idx has no valid answers", compact('machine'));
            continue;
        }

        $costs = array_map('cost', $valid_answers);
        sort($costs);
        vecho::msg("machine $idx valid answers", compact('machine', 'valid_answers', 'costs'));
        $ret[] = array_shift($costs);
    }

    vecho::$verbose = false;

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
        print_r($values);
    }

    return array_sum($values);
}

run_part1('example', false, 480);
run_part1('input', false);
echo "\n";

run_part2('example', false, 875318608908);
run_part2('input', false);
echo "\n";

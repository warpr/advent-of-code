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

    $ret = [];

    foreach ($lines as $line) {
        $line = trim($line);

        list($answer, $inputs) = explode(': ', $line);

        $ret[] = [
            'answer' => $answer,
            'inputs' => explode(' ', $inputs),
        ];
    }

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

    return array_sum($values);
}

function permutations($slots, $options)
{
    $prefixes = [[]];

    for ($i = 0; $i < $slots; $i++) {
        $new_prefixes = [];
        foreach ($prefixes as $idx => $prefix) {
            foreach ($options as $opt) {
                $new_prefixes[] = array_merge($prefix, [$opt]);
            }
        }
        $prefixes = $new_prefixes;
    }

    return $prefixes;
}

function do_math(array $eq)
{
    $acc = array_shift($eq);
    $op = null;

    foreach ($eq as $token) {
        if (!is_numeric($token)) {
            $op = $token;
            continue;
        }

        switch ($op) {
            case '*':
                $acc = $acc * $token;
                break;
            case '+':
                $acc = $acc + $token;
                break;
        }
    }

    return $acc;
}

function part1($lines)
{
    $ret = [];

    foreach ($lines as $stuff) {
        extract($stuff);

        $operator_count = count($inputs) - 1;
        vecho::msg("For answer {$answer} we need {$operator_count} operators, inputs are", $inputs);

        foreach (permutations($operator_count, ['*', '+']) as $p) {
            $p[] = null;
            $zipped = flatten(array_map(null, $inputs, $p));
            array_pop($zipped);
            vecho::msg(' - ', implode(' ', $zipped));

            if (do_math($zipped) != $answer) {
                continue;
            }

            vecho::msg('Found a correct set of operators', implode(' ', $zipped), ' = ', $answer);
            $ret[$answer] = implode(' ', $zipped);
            break;
        }
    }

    return array_keys($ret);
}

function part2($grid)
{
    return [23];
}

run_part1('example', false, 3749);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 6);
// run_part2('input', false);
echo "\n";

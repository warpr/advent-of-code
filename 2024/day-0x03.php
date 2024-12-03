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
    $body = file_get_contents($filename);

    $do = preg_quote('do()');
    $dont = preg_quote("don't()");
    $mul = 'mul\([0-9]{1,3},[0-9]{1,3}\)';

    if (!preg_match_all("/($do|$dont|$mul)/", $body, $matches)) {
        die('No valid instructions found');
    }

    return $matches[0];
}

function part1($values)
{
    $ret = [];

    foreach ($values as $instr) {
        if (preg_match('/mul\(([0-9]+),\s*([0-9]+)\)/', $instr, $matches)) {
            $ret[] = $matches[1] * $matches[2];
        }
    }

    return $ret;
}

function part2($values)
{
    $ret = [];

    $disabled = false;

    foreach ($values as $instr) {
        if ($instr == 'do()') {
            $disabled = false;
            continue;
        }

        if ($instr == "don't()") {
            $disabled = true;
            continue;
        }

        if ($disabled) {
            vecho::msg("skipping $instr");
            continue;
        }

        if (preg_match('/mul\(([0-9]+),\s*([0-9]+)\)/', $instr, $matches)) {
            vecho::msg("calculating $instr");
            $ret[] = $matches[1] * $matches[2];
        }
    }

    return $ret;
}

function main(string $filename, bool $part2)
{
    $reports = parse($filename, $part2);

    if ($part2) {
        $values = part2($reports);
    } else {
        $values = part1($reports);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

// 161 (2*4 + 5*5 + 11*8 + 8*5).
run_part1('example', false, 161);
run_part1('input', false);
echo "\n";

// 48 (2*4 + 8*5)
run_part2('example2', true, 48);
run_part2('input', false);
echo "\n";

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

    $reports = [];
    foreach ($lines as $idx => $line) {
        $reports[] = explode(' ', trim($line));
    }

    return $reports;
}

function is_safe($values)
{
    $error_idx = find_error($values);

    return $error_idx === null;
}

function find_error($values)
{
    if (count($values) < 2) {
        die('need at least 2 values');
    }

    $decreasing = $values[0] > $values[1];
    for ($i = 1; $i < count($values); $i++) {
        $diff = $values[$i] - $values[$i - 1];
        if ($decreasing) {
            if ($diff >= 0) {
                return $i;
            }
        } else {
            if ($diff <= 0) {
                return $i;
            }
        }

        if (abs($diff) > 3) {
            return $i;
        }
    }

    return null;
}

function retry_without_idx($values, $remove_idx)
{
    array_splice($values, $remove_idx, 1);

    return is_safe($values);
}

function is_safe_v2($values)
{
    if (is_safe($values)) {
        return true;
    }

    vecho::msg("\nUNSAFE... ", $values);

    // let's just brute force it.
    foreach ($values as $idx => $unused) {
        if (retry_without_idx($values, $idx)) {
            vecho::msg('OK after removing index', $idx);
            return true;
        }
    }

    vecho::msg('UNSAFE');

    return false;
}

function part1($reports)
{
    $safe = [];
    foreach ($reports as $values) {
        if (is_safe($values)) {
            $safe[] = 1;
        }
    }

    return $safe;
}

function part2($reports)
{
    $safe = [];
    foreach ($reports as $idx => $values) {
        if (is_safe_v2($values)) {
            $safe[] = 1;
        }

        if ($idx > 10) {
            vecho::$verbose = false;
        }
    }

    return $safe;
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

run_part1('example', false, 2);
run_part1('input', false);
echo "\n";

run_part2('example', false, 4);
run_part2('input', false);
echo "\n";

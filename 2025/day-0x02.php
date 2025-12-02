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
    $body = file_get_contents($filename);

    $parts = explode(',', $body);

    foreach ($parts as $part) {
        yield trim($part);
    }
}

function is_invalid(int $id)
{
    $id_str = "{$id}";
    $digit_count = strlen($id_str);
    if ($digit_count % 2) {
        return false;
    }

    $max_length = $digit_count >> 1;

    for ($i = 1; $i <= $max_length; $i++) {
        $segment = substr($id_str, 0, $i);
        $repeated = str_repeat($segment, 2);
        if (str_starts_with($repeated, $id_str)) {
            vecho::msg("$id_str. MATCH {$repeated}");
            return true;
        } else {
            vecho::msg("$id_str. NO MATCH {$repeated}");
        }
    }

    return false;
}

function check_range(int $start_id, int $end_id)
{
    for ($i = $start_id; $i <= $end_id; $i++) {
        if (is_invalid($i)) {
            yield $i;
        }
    }
}

function part1($data)
{
    $invalid_ids = [];

    foreach ($data as $range) {
        [$start_id, $end_id] = explode('-', $range);

        foreach (check_range((int) $start_id, (int) $end_id) as $invalid) {
            $invalid_ids[] = $invalid;
        }
    }

    return $invalid_ids;
}

function part2($data)
{
    return [23];
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, vecho::$verbose, $part2);

    if ($part2) {
        $values = part2($parsed);
    } else {
        $values = part1($parsed);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 1227775554);
run_part1('input', false);
echo "\n";

run_part2('example', false, 6);
run_part2('input', false);
echo "\n";

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

ini_set('memory_limit', '4096M');

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $idx => $line) {
        if (!empty(trim($line))) {
            $ret[] = explode(',', trim($line));
        }
    }

    return [$ret];
}

function distance(array $p, array $q)
{
    return sqrt(
        ($p[0] - $q[0]) * ($p[0] - $q[0]) +
            ($p[1] - $q[1]) * ($p[1] - $q[1]) +
            ($p[2] - $q[2]) * ($p[2] - $q[2]),
    );
}

function all_distances(array $data)
{
    $distances = [];

    while (!empty($data)) {
        $current_box = array_pop($data);
        foreach ($data as $box) {
            $current = implode(',', $current_box);
            $target = implode(',', $box);
            $distance = distance($current_box, $box);

            $distances[] = [
                'src' => $current_box,
                'dst' => $box,
                'len' => $distance,
            ];
        }

        vecho::debounced_msg(3, count($data) . ' boxes to check');
    }

    $sorted = sort_by($distances, 'len');

    /*
    foreach ($sorted as $s) {
        vecho::msg("Sorted " . $s["len"]
            . " from " . implode(",", $s["src"])
            . " to " . implode(",", $s["dst"]));
    }
    */

    return $sorted;
}

function part1($data)
{
    $max = 10;
    if (count($data) > 500) {
        $max = 1000;
    }

    $boxes_in_circuits = [];
    foreach ($data as $box) {
        $key = implode(',', $box);
        $boxes_in_circuits[$key] = false;
    }

    $circuits = [];
    $sorted = all_distances($data);

    print_memory();

    for ($i = 0; $i < $max; $i++) {
        $cable = array_shift($sorted);

        $src_str = implode(',', $cable['src']);
        $dst_str = implode(',', $cable['dst']);

        $src_circuit = $boxes_in_circuits[$src_str] ?? null;
        $dst_circuit = $boxes_in_circuits[$dst_str] ?? null;

        $circuit_id = null;

        if ($src_circuit && $dst_circuit && $src_circuit === $dst_circuit) {
            // already connected, let's skip this cable
            continue;
        } elseif ($src_circuit && $dst_circuit) {
            // need to merge circuits
            $circuit_id = $src_circuit;
            foreach ($boxes_in_circuits as $box => $id) {
                if ($id === $dst_circuit) {
                    $boxes_in_circuits[$box] = $circuit_id;
                }
            }
            $circuits[$circuit_id] = array_merge($circuits[$src_circuit], $circuits[$dst_circuit]);
            unset($circuits[$dst_circuit]);
        } elseif ($src_circuit) {
            // add to src circuit
            $circuit_id = $src_circuit;
        } elseif ($dst_circuit) {
            // add to dst circuit
            $circuit_id = $dst_circuit;
        } else {
            // new circuit
            $circuit_id = uniqid();
        }

        $circuits[$circuit_id][] = $cable;
        $boxes_in_circuits[$src_str] = $circuit_id;
        $boxes_in_circuits[$dst_str] = $circuit_id;
    }

    print_memory();

    $sizes = [];
    foreach ($circuits as $cables) {
        $sizes[] = count($cables) + 1;
    }

    foreach ($boxes_in_circuits as $box => $circuit) {
        if (empty($circuit)) {
            $cable = ['src' => explode(',', $box), 'dst' => null, 'len' => null];
            $circuits[] = [$cable];
            $sizes[] = 1;
        }
    }

    vecho::msg('');
    foreach ($circuits as $id => $c) {
        vecho::msg("circuit $id:");
        foreach ($c as $cable) {
            if ($cable['len']) {
                vecho::msg(
                    "- {$cable['len']} from " .
                        implode(',', $cable['src']) .
                        ' to ' .
                        implode(',', $cable['dst']),
                );
            } else {
                vecho::msg('- ' . implode(',', $cable['src']));
            }
        }
    }

    rsort($sizes);

    return [$sizes[0], $sizes[1], $sizes[2]];
}

function part2($data)
{
    return [23];
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

    return array_product($values);
}

run_part1('example', false, 40);
run_part1('input', false);
echo "\n";

// run_part2('example', true, 40);
// run_part2('input', false);
echo "\n";

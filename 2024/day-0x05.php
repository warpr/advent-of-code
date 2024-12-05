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

    $rules = [];
    $print = [];

    foreach ($lines as $line) {
        if (str_contains($line, '|')) {
            $rules[] = explode('|', trim($line));
        }

        if (str_contains($line, ',')) {
            $print[] = explode(',', trim($line));
        }
    }

    return compact('rules', 'print');
}

function parse_rules($rules)
{
    $before = [];
    $after = [];

    foreach ($rules as $pair) {
        list($b, $a) = $pair;

        @$before[$b][] = $a;
        @$after[$a][] = $b;
    }

    return compact('before', 'after');
}

function verify_page_order($pages, $before)
{
    $seen = [];

    $printing = array_fill_keys($pages, true);

    foreach ($pages as $page) {
        if (empty($before[$page])) {
            // page doesn't have to come before any pages
            continue;
        }

        $must_have_seen = [];
        foreach ($before[$page] as $later_page) {
            if (array_key_exists($later_page, $printing) && array_key_exists($later_page, $seen)) {
                // $later_page will be printed, and must be printed after
                // $page. However, it was printed already, that's an error.
                vecho::msg("expected $later_page after $page");
                return false;
            }
        }

        $seen[$page] = true;
    }

    return true;
}

function part1($values)
{
    $rule_set = parse_rules($values['rules']);

    vecho::msg('before', $rule_set['before']);

    $ret = [];

    foreach ($values['print'] as $idx => $pages) {
        vecho::msg("Verifying print {$idx}", $pages);
        if (
            verify_page_order($pages, $rule_set['before']) &&
            verify_page_order(array_reverse($pages), $rule_set['after'])
        ) {
            $middle_page_idx = count($pages) >> 1;
            $middle_page = $pages[$middle_page_idx];

            vecho::msg("Print {$idx} is OK, middle page is", $middle_page);
            $ret[] = $middle_page;

            vecho::$verbose = false;
        }
    }

    return $ret;
}

function part2($values)
{
    return [23];
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
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', false, 143);
run_part1('input', false);
echo "\n";
/*
run_part2('example', false, 9);
run_part2('input', false);
echo "\n";
*/

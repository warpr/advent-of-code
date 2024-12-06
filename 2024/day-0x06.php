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

function fix_page_order($pages, $rule_set)
{
    extract($rule_set);

    vecho::msg('Need to fix', $pages);

    usort($pages, function ($a, $b) use ($before, $after) {
        $a_must_precede = array_fill_keys($before[$a] ?? [], true);
        $b_must_precede = array_fill_keys($before[$b] ?? [], true);
        $a_must_follow = array_fill_keys($after[$a] ?? [], true);
        $b_must_follow = array_fill_keys($after[$b] ?? [], true);

        if (array_key_exists($b, $a_must_precede) || array_key_exists($a, $b_must_follow)) {
            // a must precede b
            // b must follow a
            return -1;
        }

        if (array_key_exists($a, $b_must_precede) || array_key_exists($b, $a_must_follow)) {
            // b must precede a
            // a must follow b
            return 1;
        }

        return 0;
    });

    vecho::msg('After sort', $pages);

    return $pages;
}

function part2($values)
{
    $rule_set = parse_rules($values['rules']);

    $ret = [];

    foreach ($values['print'] as $idx => $pages) {
        vecho::msg("Verifying print {$idx}", $pages);
        if (
            verify_page_order($pages, $rule_set['before']) &&
            verify_page_order(array_reverse($pages), $rule_set['after'])
        ) {
            // print is OK, ignore for part 2
        } else {
            $fixed = fix_page_order($pages, $rule_set);
            $middle_page_idx = count($fixed) >> 1;
            $middle_page = $fixed[$middle_page_idx];

            vecho::msg("Print {$idx} is fixed, middle page is", $middle_page);
            $ret[] = $middle_page;

            vecho::$verbose = false;
        }
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

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', false, 143);
run_part1('input', false);
echo "\n";

/*
    75,97,47,61,53 becomes 97,75,47,61,53.
    61,13,29 becomes 61,29,13.
    97,13,75,29,47 becomes 97,75,47,29,13.

After taking only the incorrectly-ordered updates and ordering
them correctly, their middle page numbers are 47, 29, and 47.
Adding these together produces 123.
*/
run_part2('example', false, 123);
run_part2('input', false);
echo "\n";

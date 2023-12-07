<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$strength = ['A', 'K', 'Q', 'J', 'T', '9', '8', '7', '6', '5', '4', '3', '2'];

$strength = [
    '2' => 2,
    '3' => 3,
    '4' => 4,
    '5' => 5,
    '6' => 6,
    '7' => 7,
    '8' => 8,
    '9' => 9,
    'T' => 10,
    'J' => 11,
    'Q' => 12,
    'K' => 13,
    'A' => 14,
];

/*
    Five of a kind, where all five cards have the same label: AAAAA
    Four of a kind, where four cards have the same label and one card has a different label: AA8AA
    Full house, where three cards have the same label, and the remaining two cards share a different label: 23332
    Three of a kind, where three cards have the same label, and the remaining two cards are each different from any other card in the hand: TTT98
    Two pair, where two cards share one label, two other cards share a second label, and the remaining card has a third label: 23432
    One pair, where two cards share one label, and the other three cards have a different label from the pair and each other: A23A4
    High card, where all cards' labels are distinct: 23456
 */

function analyze_race(bool $verbose, int $time, int $distance)
{
    $total_races = $time;
    $losing_start = 0;
    $losing_end = 0;

    for ($held = 0; $held <= $time; $held++) {
        $speed = $held;
        $left = $time - $held;
        $run = $speed * $left;

        if ($run > $distance) {
            $losing_start = $held;
            break;
        }
    }

    for ($held = $time; $held > 0; $held--) {
        $speed = $held;
        $left = $time - $held;
        $run = $speed * $left;

        if ($run > $distance) {
            $losing_end = $time - $held - 1;
            break;
        }
    }

    return $total_races - $losing_start - $losing_end;
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $input = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        list($field, $values) = explode(':', $line);
        $input[$field] = array_values(array_filter(explode(' ', trim($values))));
    }

    $values = [];

    if ($part2) {
        $time = (int) implode('', $input['Time']);
        $distance = (int) implode('', $input['Distance']);
        return analyze_race($verbose, (int) $time, (int) $distance);
    }

    foreach ($input['Time'] as $race_no => $time) {
        $distance = $input['Distance'][$race_no];
        $values[$race_no] = analyze_race($verbose, (int) $time, (int) $distance);
    }

    return $values;
}

function main(string $filename, bool $verbose, bool $part2)
{
    if ($part2) {
        return parse($filename, $verbose, $part2);
    }

    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    return array_product($values);
}

run_part1('example', true, 288);
run_part1('input', false);
echo "\n";

run_part2('example', true, 71503);
run_part2('input', false);
echo "\n";

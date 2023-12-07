<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function hand_type(string $hand)
{
    $count = [];

    $parts = str_split($hand);
    foreach ($parts as $card) {
        @$count[$card]++;
    }

    $count = array_values($count);
    rsort($count);

    // this will sort correctly as strings, e.g. full
    // house is represented as "3-2", one pair as
    // "2-1-1-1", etc..
    return implode('-', $count);
}

function cmp_hands(string $a, string $b)
{
    $on_type = strcmp(hand_type($a), hand_type($b));
    if ($on_type) {
        return $on_type;
    }

    return strcmp($a, $b);
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $hands = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        list($hand, $bid) = explode(' ', $line);

        // replace card names to make them
        // string sortable.
        $hand = str_replace(['T', 'J', 'Q', 'K', 'A'], ['B', 'C', 'D', 'E', 'F'], $hand);
        $hands[$hand] = $bid;
    }

    uksort($hands, 'cmp_hands');
    if ($verbose) {
        print_r(['sorted' => $hands]);
    }

    $ret = [];

    $rank = 1;
    foreach ($hands as $hand => $bid) {
        $ret[] = $rank * $bid;
        $rank++;
    }

    return $ret;
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 6440);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 71503);
run_part2('input', false);
echo "\n";
*/

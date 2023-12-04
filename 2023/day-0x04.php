<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        list($card, $numbers) = explode(': ', trim($line));
        list($winning, $on_card) = explode('|', $numbers);
        $winning2 = array_filter(explode(' ', $winning));
        $on_card2 = array_filter(explode(' ', $on_card));
        $matches = array_intersect($winning2, $on_card2);
        $match_count = count($matches);
        if ($match_count > 0) {
            $points = 1;
            for ($i = 1; $i < $match_count; $i++) {
                $points *= 2;
            }
            vecho(
                $verbose,
                "$card has $match_count winning numbers (" .
                    implode(', ', $matches) .
                    "), so it is worth $points points.\n"
            );
            $ret[] = $points;
        } else {
            vecho($verbose, "$card has no winning numbers, so it is worth no points.\n");
        }
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

run_part1('example', true, 13);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 467835);
run_part2('input', false);
echo "\n";
*/

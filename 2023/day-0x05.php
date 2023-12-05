<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function find_location(bool $verbose, array $almanac, string $from, string $final, int $value)
{
    $ranges = $almanac[$from];
    $to = array_keys($ranges)[0];

    $new_value = null;

    foreach ($ranges[$to] as $range) {
        $range_start = $range[$from];
        $range_end = $range_start + $range['range'];

        if ($range_start <= $value && $range_end > $value) {
            vecho($verbose, "$from $value is in range ($range_start, $range_end)\n");
            $new_value = $value - $range[$from] + $range[$to];
            vecho($verbose, "$from $value is $to $new_value\n");
        }
    }

    if ($new_value === null) {
        $new_value = $value;
        vecho($verbose, "$from $value is $to $new_value (default)\n");
    }

    if ($to === $final) {
        return $new_value;
    }

    return find_location($verbose, $almanac, $to, $final, $new_value);
}

function record_range(array $almanac, array $mapping, array $range)
{
    $from = $mapping[0];
    $to = $mapping[1];

    $data = [
        $from => $range[1],
        $to => $range[0],
        'range' => $range[2],
    ];

    @$almanac[$from][$to][] = $data;

    return $almanac;
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $almanac = [];

    $current_map = null;

    foreach ($lines as $line) {
        $line = trim($line);

        if (preg_match('/^seeds:(.*)/', $line, $matches)) {
            $almanac['seeds'] = explode(' ', trim($matches[1]));
        } elseif (preg_match('/(.*)-to-(.*) map:/', $line, $matches)) {
            $current_map = [$matches[1], $matches[2]];
        } elseif (!empty($line) && !empty($current_map)) {
            $almanac = record_range($almanac, $current_map, explode(' ', trim($line)));
        }
    }

    $ret = [];
    foreach ($almanac['seeds'] as $seed) {
        $ret[] = find_location($verbose, $almanac, 'seed', 'location', (int) $seed);
        vecho($verbose, "-------\n");
    }

    return $ret;
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    return min($values);
}

/*
    Seed 79, soil 81, fertilizer 81, water 81, light 74, temperature 78, humidity 78, location 82.
    Seed 14, soil 14, fertilizer 53, water 49, light 42, temperature 42, humidity 43, location 43.
    Seed 55, soil 57, fertilizer 57, water 53, light 46, temperature 82, humidity 82, location 86.
    Seed 13, soil 13, fertilizer 52, water 41, light 34, temperature 34, humidity 35, location 35.
*/

run_part1('example', true, 35);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 30);
run_part2('input', false);
echo "\n";
*/

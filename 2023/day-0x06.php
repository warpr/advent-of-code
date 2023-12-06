<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

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

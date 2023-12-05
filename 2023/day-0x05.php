<?php

declare(strict_types=1);

// ini_set('memory_limit','4096M');

require_once __DIR__ . '/common.php';

function find_location(bool $verbose, array &$almanac, string $from, string $final, int $value)
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

    $smallest = PHP_INT_MAX;

    if ($part2) {
        $pairs = [];
        for ($i = 0; $i < count($almanac['seeds']); $i += 2) {
            $pairs[] = [$almanac['seeds'][$i], $almanac['seeds'][$i + 1]];
        }

        $ranges = count($pairs);
        foreach ($pairs as $idx => $seed_range) {
            $start = (int) $seed_range[0];
            $end = (int) $seed_range[0] + (int) $seed_range[1];
            for ($seed = $start; $seed < $end; $seed++) {
                $msg = "Processing ($idx of $ranges), seed $seed ($start to $end)";
                display_percentage($msg, $start, $end, $seed);

                $loc = find_location($verbose, $almanac, 'seed', 'location', (int) $seed);
                if ($loc < $smallest) {
                    $smallest = $loc;
                }
            }
            vecho($verbose, "----\n");
        }
    } else {
        foreach ($almanac['seeds'] as $seed) {
            $loc = find_location($verbose, $almanac, 'seed', 'location', (int) $seed);
            if ($loc < $smallest) {
                $smallest = $loc;
            }
            vecho($verbose, "-------\n");
        }
    }

    return $smallest;
}

function main(string $filename, bool $verbose, bool $part2)
{
    return parse($filename, $verbose, $part2);
}

run_part1('example', false, 35);
run_part1('input', false);
echo "\n";

run_part2('example', true, 46);
run_part2('input', false);
echo "\n";

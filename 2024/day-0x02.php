<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $reports = [];
    foreach ($lines as $idx => $line) {
        $reports[] = explode(' ', trim($line));
    }

    return $reports;
}

function is_safe($values)
{
    if (count($values) < 3) {
        return false;
    }

    $decreasing = $values[0] > $values[1];
    for ($i = 1; $i < count($values); $i++) {
        $diff = $values[$i] - $values[$i - 1];
        if ($decreasing) {
            if ($diff >= 0) {
                return false;
            }
        } else {
            if ($diff <= 0) {
                return false;
            }
        }

        if (abs($diff) > 3) {
            return false;
        }
    }

    return true;
}

function part1($reports)
{
    $safe = [];
    foreach ($reports as $values) {
        if (is_safe($values)) {
            $safe[] = 1;
        }
    }

    return $safe;
}

function part2($col1, $col2)
{
}

function main(string $filename, bool $verbose, bool $part2)
{
    $reports = parse($filename, $verbose, $part2);

    if ($part2) {
        $values = part2($reports);
    } else {
        $values = part1($reports);
    }

    if ($verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 2);
run_part1('input', false);
echo "\n";

// run_part2('example', true, 31);
// run_part2('input', false);
echo "\n";

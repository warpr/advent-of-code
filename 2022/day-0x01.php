<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function part1($filename, $verbose)
{
    $lines = array_map('trim', file($filename));

    $current = 0;
    $elves = [0];
    foreach ($lines as $line) {
        if (empty($line)) {
            $elves[++$current] = 0;
        }
        $elves[$current] += (int) $line;
    }

    if ($verbose) {
        echo "\nElf calorie counts from $filename:\n\n";
        echo '  ' . json_encode($elves) . "\n\n";
    }

    return max($elves);
}

function part2($filename, $verbose)
{
    $lines = array_map('trim', file($filename));

    $current = 0;
    $elves = [0];
    foreach ($lines as $line) {
        if (empty($line)) {
            $elves[++$current] = 0;
        }
        $elves[$current] += (int) $line;
    }

    rsort($elves);

    if ($verbose) {
        echo "\nElf calorie counts from $filename:\n\n";
        echo '  ' . json_encode($elves) . "\n\n";
    }

    return array_sum(array_slice($elves, 0, 3));
}

run_part1('example', true, 24000);
run_part1('input');
run_part2('example', true, 45000);
run_part2('input');
echo "\n";

<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function score_table($filename, $verbose, $scores)
{
    $lines = array_map('trim', file($filename));

    $ret = 0;
    foreach ($lines as $round => $line) {
        $score = $scores[$line];
        $ret += $score;

        if ($verbose) {
            echo "Round $round: score $score\n";
        }
    }

    return $ret;
}

function part1($filename, $verbose)
{
    $scores = [
        'A X' => 3 + 1,
        'A Y' => 6 + 2,
        'A Z' => 0 + 3,
        'B X' => 0 + 1,
        'B Y' => 3 + 2,
        'B Z' => 6 + 3,
        'C X' => 6 + 1,
        'C Y' => 0 + 2,
        'C Z' => 3 + 3,
    ];

    return score_table($filename, $verbose, $scores);
}

function part2($filename, $verbose)
{
    $scores = [
        'A X' => 0 + 3,
        'A Y' => 3 + 1,
        'A Z' => 6 + 2,
        'B X' => 0 + 1,
        'B Y' => 3 + 2,
        'B Z' => 6 + 3,
        'C X' => 0 + 2,
        'C Y' => 3 + 3,
        'C Z' => 6 + 1,
    ];

    return score_table($filename, $verbose, $scores);
}

run_part1('example', true, 15);
run_part1('input');
run_part2('example', true, 12);
run_part2('input');
echo "\n";

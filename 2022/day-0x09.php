<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function move(array &$board, $direction)
{
    $head = $board['head'];

    switch ($direction) {
        case 'L':
            $board['head'][0]--;
            break;
        case 'R':
            $board['head'][0]++;
            break;
        case 'D':
            $board['head'][1]--;
            break;
        case 'U':
            $board['head'][1]++;
            break;
    }

    $x_distance = $board['head'][0] - $board['tail'][0];
    $y_distance = $board['head'][1] - $board['tail'][1];

    if ($y_distance === 0) {
        // head and tail are in the same row

        if ($x_distance > 1) {
            $board['tail'][0] = $board['head'][0] - 1;
        } elseif ($x_distance < -1) {
            $board['tail'][0] = $board['head'][0] + 1;
        }
    } elseif ($x_distance === 0) {
        // head and tail are in the same column

        if ($y_distance > 1) {
            $board['tail'][1] = $board['head'][1] - 1;
        } elseif ($y_distance < -1) {
            $board['tail'][1] = $board['head'][1] + 1;
        }
    } elseif (abs($x_distance) > 1 || abs($y_distance) > 1) {
        // tail is trailing diagonally, and not touching

        if ($x_distance > 0) {
            $board['tail'][0]++;
        } elseif ($x_distance < 0) {
            $board['tail'][0]--;
        }

        if ($y_distance > 0) {
            $board['tail'][1]++;
        } elseif ($y_distance < 0) {
            $board['tail'][1]--;
        }
    }
}

function part1($filename, bool $verbose)
{
    $board = [
        'head' => [0, 0],
        'tail' => [0, 0],
    ];

    $seen = [];

    $lines = file($filename);
    foreach ($lines as $line) {
        list($direction, $amount) = explode(' ', trim($line));

        for ($i = 0; $i < $amount; $i++) {
            move($board, $direction);

            if ($verbose) {
                echo '[' . trim($line) . " - $i] Positions";
                echo '| H: ' . json_encode($board['head']);
                echo '| T: ' . json_encode($board['tail']);
                echo "\n";
            }

            $tail_pos = implode(',', $board['tail']);
            $seen[$tail_pos] = true;
        }
    }

    return count($seen);
}

run_part1('example', true, 13);
run_part1('input');
//run_part2('example', true, 8);
//run_part2('input');

echo "\n";

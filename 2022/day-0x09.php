<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function move_knot(array $head, array $tail): array
{
    $x_distance = $head[0] - $tail[0];
    $y_distance = $head[1] - $tail[1];

    if ($y_distance === 0) {
        // head and tail are in the same row

        if ($x_distance > 1) {
            $tail[0] = $head[0] - 1;
        } elseif ($x_distance < -1) {
            $tail[0] = $head[0] + 1;
        }
    } elseif ($x_distance === 0) {
        // head and tail are in the same column

        if ($y_distance > 1) {
            $tail[1] = $head[1] - 1;
        } elseif ($y_distance < -1) {
            $tail[1] = $head[1] + 1;
        }
    } elseif (abs($x_distance) > 1 || abs($y_distance) > 1) {
        // tail is trailing diagonally, and not touching

        if ($x_distance > 0) {
            $tail[0]++;
        } elseif ($x_distance < 0) {
            $tail[0]--;
        }

        if ($y_distance > 0) {
            $tail[1]++;
        } elseif ($y_distance < 0) {
            $tail[1]--;
        }
    }

    return $tail;
}

function move_head(array $head, string $direction): array
{
    switch ($direction) {
        case 'L':
            $head[0]--;
            break;
        case 'R':
            $head[0]++;
            break;
        case 'D':
            $head[1]--;
            break;
        case 'U':
            $head[1]++;
            break;
    }

    return $head;
}

function move(array &$board, $direction)
{
    $board[0] = move_head($board[0], $direction);

    for ($i = 1; $i < count($board); $i++) {
        $board[$i] = move_knot($board[$i - 1], $board[$i]);
    }
}

function main($filename, int $rope_length, bool $verbose)
{
    $board = array_fill(0, $rope_length, [0, 0]);

    $seen = [];

    $lines = file($filename);
    foreach ($lines as $line) {
        list($direction, $amount) = explode(' ', trim($line));

        for ($i = 0; $i < $amount; $i++) {
            move($board, $direction);

            if ($verbose) {
                echo '[' . trim($line) . " - $i] Positions: ";
                echo json_encode($board) . "\n";
            }

            $tail = end($board);
            $tail_pos = implode(',', $tail);
            $seen[$tail_pos] = true;
        }
    }

    return count($seen);
}

function part1($filename, bool $verbose)
{
    return main($filename, 2, $verbose);
}

function part2($filename, bool $verbose)
{
    return main($filename, 10, $verbose);
}

run_part1('example', true, 13);
run_part1('input');
run_part2('example', true, 1);
run_part2('example2', false, 36);
run_part2('input');

echo "\n";

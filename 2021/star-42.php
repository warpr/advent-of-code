<?php

function show_progress()
{
    static $prev = 0;

    $now = hrtime(true);
    $seconds_elapsed = ($now - $prev) / 1000000000; // to seconds

    if ($seconds_elapsed > 1) {
        $prev = $now;
        return true;
    } else {
        return false;
    }
}

function dice_counts()
{
    foreach ([1, 2, 3] as $throw1) {
        foreach ([1, 2, 3] as $throw2) {
            foreach ([1, 2, 3] as $throw3) {
                yield $throw1 + $throw2 + $throw3;
            }
        }
    }
}

function play_round($p1_pos, $p2_pos, $p1_score, $p2_score)
{
    static $cache = [];
    $key = "$p1_pos,$p2_pos,$p1_score,$p2_score";
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $p1_wins = 0;
    $p2_wins = 0;

    foreach (dice_counts() as $move) {
        $p1_new_pos = ($p1_pos + $move) % 10;
        $p1_new_score = $p1_score + $p1_new_pos + 1;

        if ($p1_new_score >= 21) {
            $p1_wins++;
            continue;
        }

        foreach (dice_counts() as $move) {
            $p2_new_pos = ($p2_pos + $move) % 10;
            $p2_new_score = $p2_score + $p2_new_pos + 1;

            if ($p2_new_score >= 21) {
                $p2_wins++;
                continue;
            }

            $results = play_round($p1_new_pos, $p2_new_pos, $p1_new_score, $p2_new_score);
            $p1_wins += $results[0];
            $p2_wins += $results[1];
        }
    }

    if (show_progress()) {
        printf("wins: %16d vs %16d, cache size: %d\n", $p1_wins, $p2_wins, count($cache));
    }

    $cache[$key] = [$p1_wins, $p2_wins];

    return [$p1_wins, $p2_wins];
}

function run($p1, $p2, $verbose = false)
{
    // use 0-indexed position
    $p1--;
    $p2--;

    echo "\n ___ The game starts! ___ \n";
    $results = play_round($p1, $p2, 0, 0);
    // print_r(compact('results'));

    return max($results);
}

function main($p1, $p2, $verbose = null, $expected = null)
{
    $str = "(P1: $p1, P2: $p2)";
    $actual = run($p1, $p2, $verbose);
    if ($expected !== null) {
        if ($actual !== $expected) {
            echo "You broke $str, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

// Player 1 starting position: 4
// Player 2 starting position: 8
main(4, 8, true, 444356092776315);

// Player 1 starting position: 6
// Player 2 starting position: 4
main(6, 4, false);

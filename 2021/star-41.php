<?php

function throw_dice(&$die, &$rolls)
{
    $die++;
    $rolls++;
    if ($die > 100) {
        $die = 1;
    }

    return $die;
}

function play_round(&$die, &$rolls, $current_p, &$players, &$scores)
{
    foreach ($scores as $player => $score) {
        if ($score >= 1000) {
            return $player;
        }
    }

    $move = throw_dice($die, $rolls) + throw_dice($die, $rolls) + throw_dice($die, $rolls);
    $players[$current_p] = $players[$current_p] + $move;
    while ($players[$current_p] > 10) {
        $players[$current_p] -= 10;
    }

    $scores[$current_p] += $players[$current_p];

    echo 'Player ' .
        ($current_p + 1) .
        " moved $move places to " .
        $players[$current_p] .
        ' for a total score of ' .
        $scores[$current_p] .
        "\n";

    return null;
}

function run($p1, $p2, $verbose = false)
{
    $die = 0;
    $rolls = 0;
    $players = [$p1, $p2];
    $scores = [0, 0];
    $winner = null;

    echo "\n ___ The game starts! ___ \n";

    $rounds = 0;
    while ($winner === null) {
        foreach ($players as $p => $pos) {
            $winner = play_round($die, $rolls, $p, $players, $scores);
            if ($winner !== null) {
                break;
            }
        }
    }

    unset($scores[$winner]);
    $loser_score = array_sum($scores);

    echo "Rolls: $rolls, Score: $loser_score\n";

    return $rolls * $loser_score;
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
main(4, 8, true, 739785);

// Player 1 starting position: 6
// Player 2 starting position: 4
main(6, 4, false);

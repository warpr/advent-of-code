<?php

namespace foo;

function display($decks) {
    foreach ($decks as $idx => $deck) {
        echo "Player $idx's deck: " . implode(", ", $deck) . "\n";
    }
}

function play_round(&$decks) {
    $player1 = array_shift($decks[1]);
    $player2 = array_shift($decks[2]);

    if ($player1 > $player2) {
        $decks[1][] = $player1;
        $decks[1][] = $player2;
    } else {
        $decks[2][] = $player2;
        $decks[2][] = $player1;
    }
}

function main($filename) {
    $lines = file($filename);

    $decks = [];
    $current_player = 0;
    foreach ($lines as $line) {
        if (preg_match("/Player ([0-9]):/", trim($line), $matches)) {
            $current_player = (int) $matches[1];
            $decks[$current_player] = [];
            continue;
        }

        $line = trim($line);
        if (!empty($line)) {
            $decks[$current_player][] = (int) $line;
        }
    }

    $count = 0;
    $winner = null;
    while (true) {
        $count++;
        echo "\n-- Round $count --\n";
        display($decks);
        play_round($decks);

        if (empty($decks[1])) {
            $winner = 2;
            break;
        }

        if (empty($decks[2])) {
            $winner = 1;
            break;
        }
    }

    echo "\n== Post-game results ==\n";
    display($decks);

    $score = 0;
    $multiplier = 1;
    while(!empty($decks[$winner])) {
        $card = array_pop($decks[$winner]);
        $score += $card * $multiplier++;
    }

    echo "\nPlayer $winner has won with a score of: $score\n";
}

// main('star-43-example.txt');
main('star-43-input.txt');


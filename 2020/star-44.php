<?php

namespace foo;

function display($decks) {
    foreach ($decks as $idx => $deck) {
        echo "Player $idx's deck: " . implode(", ", $deck) . "\n";
    }
}

function play_round(&$game, &$decks) {
    $key = implode(",", $decks[1]) . '~' . implode(",", $decks[2]);
    if (!empty($game[$key])) {
        return 1;
    }

    $game[$key] = true;

    $player1 = array_shift($decks[1]);
    $player2 = array_shift($decks[2]);
    $winner = null;

    if (count($decks[1]) >= $player1 && count($decks[2]) >= $player2) {
        echo "Playing a sub-game to determine the winner...\n";
        $new_decks = [
            1 => array_slice($decks[1], 0, $player1),
            2 => array_slice($decks[2], 0, $player2),
        ];
        $result = play_game($new_decks);
        $winner = $result['winner'];
    } else {
        $winner = ($player1 > $player2) ? 1 : 2;
    }

    if ($winner == 1) {
        $decks[1][] = $player1;
        $decks[1][] = $player2;
    } else {
        $decks[2][] = $player2;
        $decks[2][] = $player1;
    }

    return null;
}

function play_game($decks) {
    static $game_no = 0;

    $game_no++;
    $this_game_no = $game_no;
    $count = 0;
    $winner = null;
    $game = [];
    while (true) {
        $count++;
        echo "\n-- Round $count (Game $this_game_no) --\n";
        display($decks);
        $winner = play_round($game, $decks);

        if (empty($decks[1])) {
            $winner = 2;
        }

        if (empty($decks[2])) {
            $winner = 1;
        }

        if ($winner) {
            break;
        }
    }

    echo "The winner of game $this_game_no is player $winner!\n";
    return compact('winner', 'decks');
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

    $result = play_game($decks);
    $winner = $result['winner'];
    $decks = $result['decks'];

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
// main('star-44-example.txt');


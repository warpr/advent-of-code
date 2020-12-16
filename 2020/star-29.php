<?php

$verbose = false;

function display_state($turn, $game) {
    global $verbose;
    if (!$verbose) {
        return;
    }
    echo "__ TURN $turn __\n";
    foreach ($game as $key => $val) {
        if (is_array($val)) {
            echo "$key: \t " . json_encode($val) . "\n";
        } else {
            echo "$key:  $val\n";
        }
    }
}

function turn($turn, $game) {
    global $verbose;
    $prev = $game['prev'];
    $game['prev'] = $game['mem'];

    if (empty($game['seq'])) {
        $spoken = $game['spoken'];
        if ($verbose) {
            echo "At $turn, last number spoken is $spoken ... ";
        }
        if (isset($prev[$spoken])) {
            if ($verbose) {
                echo 'and it was previously spoken at ' . $prev[$spoken] . "\n";
            }
            $number = ($turn - 1) - $prev[$spoken];
        } else {
            if ($verbose) {
                echo "and it is a new number\n";
            }
            $number = 0;
        }
    } else {
        $number = array_shift($game['seq']);
    }

    $game['mem'][$number] = $turn;
    $game['spoken'] = $number;

    display_state($turn, $game);

    return $game;
}

function main($seq, $turns) {
    $game = [ 'seq' => $seq, 'mem' => [] ];

    for ($i = 1; $i <= $turns; $i++) {
        $game = turn($i, $game);
    }

    echo "Final number spoken at turn " . ($i - 1) . " is " . $game['spoken'] . "\n";
}

echo "___ STAR 29 ___\n";
main([ 0, 3, 6 ], 10);
main([ 1, 3, 2 ], 2020);
main([ 2, 1, 3 ], 2020);
main([ 1, 2, 3 ], 2020);
main([ 2, 3, 1 ], 2020);
main([ 3, 2, 1 ], 2020);
main([ 3, 1, 2 ], 2020);
main([ 14,3,1,0,9,5 ], 2020);


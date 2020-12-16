<?php

ini_set('memory_limit','4096M');

$verbose = false;

function progress($i, $goal) {
    static $t = 0;

    if (time() > $t) {
        $perc = ($i * 100) / $goal;

        echo "Turn $i of $goal ($perc %)\n";
        $t = time();
    }
}

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
            echo str_pad($key . ":", 8) . " $val\n";
        }
    }
}

function turn(&$turn, &$game) {
    if (empty($game['seq'])) {
        $number = $game['next'];
    } else {
        $number = array_shift($game['seq']);
    }

    if (isset($game['mem'][$number])) {
        $game['next'] = $turn - $game['mem'][$number];
    } else {
        $game['next'] = 0;
    }

    $game['spoken'] = $number;
    $game['mem'][$number] = $turn;

    display_state($turn, $game);
}

function turn_ignore_seq(&$turn, &$game) {
    $number = $game['next'];

    if (isset($game['mem'][$number])) {
        $game['next'] = $turn - $game['mem'][$number];
    } else {
        $game['next'] = 0;
    }

    $game['spoken'] = $number;
    $game['mem'][$number] = $turn;
}

function main($seq, $turns) {
    $game = [ 'seq' => $seq, 'mem' => [] ];

    for ($i = 1; $i <= $turns; $i++) {
        turn($i, $game);
        if (empty($game['seq'])) {
            $i++;
            break;
        }
    }

    for (; $i <= $turns; $i++) {
        progress($i, $turns);
        turn_ignore_seq($i, $game);
    }

    echo "Final number spoken at turn " . ($i - 1) . " is " . $game['spoken'] . "\n";
}

echo "___ STAR 30 ___\n";
main([ 0, 3, 6 ], 10);
main([ 0, 3, 6 ], 2020);
main([ 1, 3, 2 ], 2020);
main([ 2, 1, 3 ], 2020);
main([ 1, 2, 3 ], 2020);
main([ 2, 3, 1 ], 2020);
main([ 3, 2, 1 ], 2020);
main([ 3, 1, 2 ], 2020);
//  main([ 0, 3, 6 ], 30000000);
main([ 14,3,1,0,9,5 ], 30000000);


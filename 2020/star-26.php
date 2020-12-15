<?php

/**
 * This function stolen from
 * https://www.reddit.com/r/adventofcode/comments/kc4njx/2020_day_13_solutions/gfq1vuz/
 *
 * NOTE: this function is incorrect for certain solutions,
 * presumably those where the answer is smaller than one of
 * the bus IDs, e.g.:    "3,4,5" => 3
 */
function gold_coin($line) {
    $buses = explode(",", trim($line));

    $n = 0;
    $inc = (int) $buses[0];

    for ($t = 1; $t < count($buses); $t++) {
	    if ($buses[$t] == "x") {
            continue;
        }

	    $first = 0;
	    while (true) {
		    $bus = (int) $buses[$t];
		    if (floor(($n + $t) / $bus) == ($n + $t) / $bus) {
			    // echo "{$t}|{$bus}|" . floor(($n+$t)/$bus) . "\n";
			    if ($first == 0) {
				    if ($t == count($buses) - 1) {
                        return $n;
				    }
				    $first = $n;
			    }
			    else {
				    $inc = $n - $first;
				    break;
			    }
		    }
		    $n += $inc;
	    }
    }

    return null;
}

function departures_for($timestamp, $buses){
    $ret = true;
    $departure = $timestamp;
    $stuff = [];
    foreach ($buses as $bus) {
        $remainder = ($departure++) % $bus;
        if ($remainder) {
            $ret = false;
            return false;
        }
        $stuff[] = $remainder ? '.' : 'D';
    }

//    echo "@$timestamp: \t " . implode("  ", $stuff) . "\n";;

    return $ret;
}

/**
 * This function takes a more brute force approach, which works fine on
 * the example inputs, but runs forever on the actual puzzle input.
 */
function gold_coin_old($line) {
    $buses = array_map(function ($item) {
        return $item != 'x' ? (int) $item : 1;
    }, explode(",", trim($line)));

    $upper_bound = array_reduce($buses, function ($memo, $item) {
        return $memo * $item;
    }, 1);

    $max_bus = max($buses);
    $max_pos = array_search($max_bus, $buses);
    for ($i = -$max_pos; $i < $upper_bound; $i += $max_bus) {
        if (departures_for($i, $buses)) {
            return $i;
        }
    }

    return $t;
}

function main($filename) {
    echo "----[examples]----\n";

    $examples = [
        "3,4,5" => 3,
        "17,x,13,19" => 3417,
        "67,7,59,61" => 754018,
        "67,x,7,59,61" => 779210,
        "67,7,x,59,61" => 1261476,
        "1789,37,47,1889" => 1202161486,
    ];

    foreach ($examples as $line => $expected) {
        $timestamp = gold_coin_old($line);
        if ($timestamp) {
            echo "[old] $line => $timestamp (expected is $expected)\n";
        }
        $timestamp = gold_coin($line);
        if ($timestamp) {
            echo "[new] $line => $timestamp (expected is $expected)\n";
        }
    }

    echo "----[$filename]----\n";

    $lines = file($filename);
    $input = trim($lines[1]);
    echo $input . "\n";
    $timestamp = gold_coin($input);
    echo "Answer: $timestamp\n";
}

main('star-25-example.txt');
main('star-25-input.txt');

<?php

function first_departure($bus, $start) {
    $d = 0;
    while ($d <= $start) {
        $d += $bus;
    }

    return $d;
}

function main($filename) {
    $lines = file($filename);

    echo "----[$filename]----\n";

    $start = (int) trim($lines[0]);
    $buses = array_filter(explode(",", trim($lines[1])), function ($item) {
        return trim($item) != 'x';
    });

    $departures = [];
    foreach ($buses as $bus) {
        $d = first_departure($bus, $start);
        echo "BUS $bus, first departure: $d\n";
        $departures[$d] = $bus;
    }

    ksort($departures);
    $leaving = array_key_first($departures);
    $bus = $departures[$leaving];
    $wait = $leaving - $start;
    echo "First bus leaving after $start is $bus,"
       . " leaving at $leaving (wait time $wait)\n";
    echo "Answer is " . $wait * $bus . "\n";
}

main('star-25-example.txt');
main('star-25-input.txt');

<?php

function manhattan($pos) {
    return abs($pos['x']) + abs($pos['y']);
}

function ccw($pos) {
    $wx = $pos['wx'];
    $wy = $pos['wy'];

    $pos['wx'] = $wy;
    $pos['wy'] = -$wx;
    return $pos;
}

function cw($pos) {
    return ccw(ccw(ccw($pos)));
}

function move($pos, $line) {
    $cmd = $line[0];
    $arg = (int) substr($line, 1);

    switch($cmd) {
        case 'F':
            $pos['x'] += $pos['wx'] * $arg;
            $pos['y'] += $pos['wy'] * $arg;
            break;
        case 'N':
            $pos['wy'] -= $arg;
            break;
        case 'E':
            $pos['wx'] += $arg;
            break;
        case 'S':
            $pos['wy'] += $arg;
            break;
        case 'W':
            $pos['wx'] -= $arg;
            break;
        case 'L':
            $times = $arg / 90;
            for ($i = 0; $i < $times; $i++) {
                $pos = ccw($pos);
            }
            break;
        case 'R':
            $times = $arg / 90;
            for ($i = 0; $i < $times; $i++) {
                $pos = cw($pos);
            }
            break;
    }

    return $pos;
}

function main($filename) {
    $pos = [
        "x" => 0,
        "y" => 0,
        "wx" => 10,
        "wy" => -1,
    ];

    echo "----[$filename]----\n";
    $lines = file($filename);
    foreach ($lines as $idx => $line) {
        $pos = move($pos, $line);
        echo "$idx \t | " . trim($line) . " \t | \t "
           . "({$pos['x']}, {$pos['y']}) waypoint at ({$pos['wx']}, {$pos['wy']})\n";
    }

    echo "Manhattan distance: " . manhattan($pos) . "\n";
}

main('star-23-example.txt');
main('star-23-input.txt');

<?php

function manhattan($pos) {
    return abs($pos['x']) + abs($pos['y']);
}

function move($pos, $line) {
    $cmd = $line[0];
    $arg = (int) substr($line, 1);

    switch($cmd) {
        case 'F':
            if ($pos['direction'] == 0) {
                $pos['x'] += $arg;
            } elseif ($pos['direction'] == 90) {
                $pos['y'] += $arg;
            } elseif ($pos['direction'] == 180) {
                $pos['x'] -= $arg;
            } elseif ($pos['direction'] == 270) {
                $pos['y'] -= $arg;
            }
            break;
        case 'N':
            $pos['y'] -= $arg;
            break;
        case 'E':
            $pos['x'] += $arg;
            break;
        case 'S':
            $pos['y'] += $arg;
            break;
        case 'W':
            $pos['x'] -= $arg;
            break;
        case 'L':
            $pos['direction'] = $pos['direction'] - $arg;
            break;
        case 'R':
            $pos['direction'] = $pos['direction'] + $arg;
            break;
    }

    while($pos['direction'] > 359) {
        $pos['direction'] -= 360;
    }

    while($pos['direction'] < 0) {
        $pos['direction'] += 360;
    }

    return $pos;
}

function main($filename) {
    $pos = [
        "direction" => 0,
        "x" => 0,
        "y" => 0,
    ];

    echo "----[$filename]----\n";
    $lines = file($filename);
    foreach ($lines as $idx => $line) {
        $pos = move($pos, $line);
        echo "$idx \t | " . trim($line) . " \t | \t "
           . "({$pos['x']}, {$pos['y']}) facing {$pos['direction']}\n";
    }

    echo "Manhattan distance: " . manhattan($pos) . "\n";
}

main('star-23-example.txt');
main('star-23-input.txt');

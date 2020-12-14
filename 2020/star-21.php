<?php

$verbose = false;

function display_grid($grid) {
    foreach ($grid as $line) {
        echo implode("", $line) . "\n";
    }
}

function adjacent_seats($grid, $x, $y) {
    global $verbose;

    $adjacent = array_filter(array_merge(
        array_slice($grid[$y - 1], $x - 1, 3),
        [ $grid[$y][$x-1], $grid[$y][$x+1] ],
        array_slice($grid[$y + 1], $x - 1, 3),
    ), function ($item) { return $item !== ' '; });

    if ($verbose) {
        $seat = $grid[$y][$x];
        echo "($x, $y) is $seat adjacent seats: " . implode(", ", $adjacent) . "\n";
    }

    return $adjacent;
}

function pad_floor($grid) {
    $ret = [];
    foreach ($grid as $line) {
        $ret[] = array_merge([' '], $line, [' ']);
    }

    $size = count($ret[0]);
    $padding = str_split(str_repeat(' ', $size));

    return array_merge([$padding], $ret, [$padding]);
}

function game_of_seats($grid) {
    $new = [];

    for($y = 0; $y < count($grid); $y++) {
        for($x = 0; $x < count($grid[0]); $x++) {
            if ($grid[$y][$x] === 'L') {
                $seats = adjacent_seats($grid, $x, $y);
                $new[$y][$x] = in_array('#', $seats) ? 'L' : '#';

            } else if ($grid[$y][$x] === '#') {
                $seats = array_filter(adjacent_seats($grid, $x, $y), function ($s) {
                    return $s === '#';
                });

                $new[$y][$x] = (count($seats) >= 4) ? 'L' : '#';
            } else {
                $new[$y][$x] = $grid[$y][$x];
            }
        }
    }

    return $new;
}

function count_occupied($grid) {
    $count = 0;

    foreach ($grid as $line) {
        foreach ($line as $item) {
            if ($item === '#') {
                $count++;
            }
        }
    }

    return $count;
}

function main($filename) {
    $lines = file($filename);

    $grid = [];
    foreach ($lines as $line) {
        $grid[] = str_split(trim($line));
    }

    $grid = pad_floor($grid);

    echo "----[$filename]----\n";
    display_grid($grid);

    $stable = false;
    for ($round = 1; $round < 200; $round++) {
        $prev = $grid;
        echo "__ ROUND $round __\n";
        $grid = game_of_seats($grid);
        display_grid($grid);
        if ($prev === $grid) {
            $stable = true;
            break;
        }
    }

    if ($stable) {
        echo "Occupied seats at ROUND $round: " . count_occupied($grid) . "\n";
    }
}

main('star-21-example.txt');
main('star-21-input.txt');

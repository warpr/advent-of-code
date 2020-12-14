<?php

$verbose = false;

function display_grid($grid) {
    foreach ($grid as $line) {
        echo implode("", $line) . "\n";
    }
}

function game_of_seats($grid) {
    $new = [];

    for($y = 0; $y < count($grid); $y++) {
        for($x = 0; $x < count($grid[0]); $x++) {
            if ($grid[$y][$x] === 'L') {
                $seats = visible_seats($grid, $x, $y);

                $new[$y][$x] = in_array('#', $seats) ? 'L' : '#';

            } else if ($grid[$y][$x] === '#') {
                $seats = array_filter(visible_seats($grid, $x, $y), function ($s) {
                    return $s === '#';
                });

                $new[$y][$x] = (count($seats) >= 5) ? 'L' : '#';
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

function visible_seats($grid, $x, $y) {
    $visible = [];

    $max_y = count($grid);
    $max_x = count($grid[0]);

    foreach ([ -1, 0, 1 ] as $move_x) {
        foreach ([ -1, 0, 1 ] as $move_y) {
            if (!$move_x && !$move_y) {
                continue;
            }

            // echo "Moving in direction ($move_x, $move_y): \t ";

            $x_pos = $x;
            $y_pos = $y;
            while (true) {
                $x_pos += $move_x;
                $y_pos += $move_y;
                if ($x_pos < 0 || $y_pos < 0 || $x_pos >= $max_x || $y_pos >= $max_y) {
                    break;
                }
                $seat = $grid[$y_pos][$x_pos];
                // echo $seat;
                if ($seat !== '.') {
                    $visible[] = $seat;
                    break;
                }
            }

            // echo "\n";
        }
    }

    return $visible;
}

function main($filename) {
    $lines = file($filename);

    $grid = [];
    foreach ($lines as $line) {
        $grid[] = str_split(trim($line));
    }

    echo "----[$filename]----\n";
    display_grid($grid);

    /*
    $y = 4;
    $x = 3;
    echo "seat at ($x, $y): " . $grid[$y][$x] . "\n";
    $seats = visible_seats($grid, $x, $y);
    print_r($seats);
    */

    $stable = false;
    for ($round = 1; $round < 200; $round++) {
        $prev = $grid;
        echo "\n__ ROUND $round __\n\n";
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
// main('star-22-example.txt');
main('star-21-input.txt');

<?php

function pos_within_rect($pos, $rect) {
    if ($pos[0] < $rect[0]
        || $pos[0] > $rect[2]
        || $pos[1] < $rect[1]
        || $pos[1] > $rect[3]) {
        return false;
    }

    return true;
}

function fire($target, $x_velocity, $y_velocity) {
    $max_x = $target[2];
    $min_y = $target[1];

    $x = 0;
    $y = 0;
    $ret = [];

    if ($x_velocity < 1) {
        echo "x_velocity < 1 not supported.\n";
        die();
    }

    while ($x < $max_x && $y > $min_y) {
        $x += $x_velocity;
        $y += $y_velocity;
        $ret["$x,$y"] = "#";

        if (pos_within_rect([ $x, $y ], $target)) {
            return $ret;
        }

        $x_velocity--;
        if ($x_velocity < 0) {
            $x_velocity = 0;
        }

        $y_velocity--;
    }

    return false;
}

function height($shots) {
    $all_y = [];
    foreach ($shots as $pos => $chr) {
        list($x, $y) = explode(",", $pos);
        $all_y[] = $y;
    }
    return max($all_y);
}

function try_shot($target, $x, $y, $verbose) {
    echo "Attempt ($x, $y)";

    $trajectory = fire($target, $x, $y);

    if (empty($trajectory)) {
        echo " failed to hit the target\n";
        return false;
    }

    echo "\n";
    if ($verbose) {
        render($target, $trajectory);
    }
    return true;
}

function render($target, $shots) {
    $max_x = max($target[0], $target[2]);
    $min_y = min([0, $target[1], $target[3] ]);
    $max_y = max([0, $target[1], $target[3] ]);

    foreach ($shots as $pos => $chr) {
        list($x, $y) = explode(",", $pos);
        if ($y > $max_y) {
            $max_y = $y;
        }
        if ($x > $max_x) {
            $max_x = $x;
        }
    }

    for ($y = $max_y; $y >= $min_y; $y--) {
        printf("%4d|", $y);
        for ($x = 0; $x < $max_x; $x++) {
            $str_pos = "$x,$y";
            if (!empty($shots[$str_pos])) {
                echo $shots[$str_pos];
            } else if ($x == 0 && $y == 0) {
                echo "S";
            } else if (pos_within_rect([ $x, $y ], $target)) {
                echo "T";
            } else {
                echo ".";
            }
        }
        echo "\n";
    }

    echo "\n";
}

function run($str, $verbose = false)
{
    $re = '/target area:\s+x=([0-9-]+)\.\.([0-9-]+),\s*y=([0-9-]+)\.\.([0-9-]+)/';
    if (preg_match($re, $str, $matches)) {
        $target = [ (int) $matches[1], (int) $matches[3], (int) $matches[2], (int) $matches[4] ];
    }

    $range_x = [];
    $start_x = 0;
    for ($step = 1; $step < 9999; $step++) {
        $start_x += $step;
        if ($start_x >= $target[0]) {
            $range_x[] = $step;
            break;
        }
    }

    $range_x[] = $target[2];
    $range_y = [ $target[1], -$target[1] ];

    $valid_trajectories = [];
    for($x = $range_x[0]; $x <= $range_x[1]; $x++) {
        for($y = $range_y[0]; $y <= $range_y[1]; $y++) {
            if (try_shot($target, $x, $y, $verbose)) {
                $valid_trajectories[] = "$x,$y";
            }
        }
    }

    if ($verbose) {
        print_r($valid_trajectories);
    }

    echo "Searched from ({$range_x[0]}, {$range_y[0]})"
    . " to ({$range_x[1]}, {$range_y[1]})\n\n";

    return count($valid_trajectories);
}

function main($str, $verbose = null, $expected = null)
{
    $actual = run($str, $verbose);
    if ($expected !== null) {
        if ($actual !== $expected) {
            echo "You broke $str, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

// In the above example, using an initial velocity of
// 6,9 is the best you can do, causing the probe to
// reach a maximum y position of 45. (Any higher
// initial y velocity causes the probe to overshoot
// the target area entirely.)
main('target area: x=20..30, y=-10..-5', true, 112);
main('target area: x=241..273, y=-97..-63', false);

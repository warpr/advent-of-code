<?php

function size($grid) {
    $max_x = 0;
    $max_y = 0;

    foreach ($grid as $point => $unused) {
        list($x,$y) = explode(",", $point);

        $max_x = $x > $max_x ? $x : $max_x;
        $max_y = $y > $max_y ? $y : $max_y;
    }

    return [ $max_x, $max_y ];
}

function cmp_y($a, $b)
{
    if ($a[1] == $b[1]) {
        return 0;
    }
    return ($a[1] < $b[1]) ? -1 : 1;
}

function display_grid($grid) {
    $grid_size = size($grid);

    for ($j = 0; $j <= $grid_size[1]; $j++) {
        for ($i = 0; $i <= $grid_size[0]; $i++) {
            echo empty($grid["$i,$j"]) ? "." : "#";
        }
        echo "\n";
    }
}

function fold($grid, $fold_cmd) {
    if (preg_match('/fold along ([x-y])=([0-9]*)/', $fold_cmd, $matches)) {
        if ($matches[1] == 'y') {
            return fold_y($grid, $matches[2]);
        }

        if ($matches[1] == 'x') {
            return fold_x($grid, $matches[2]);
        }
    }
    return $grid;
}

function fold_x($grid, $fold_line) {
    $ret = [];

    foreach ($grid as $point => $unused) {
        list($x, $y) = explode(",", $point);

        if ($x < $fold_line) {
            $ret[$point] = true;
        } else {
            $moved_x = $x - (($x - $fold_line) << 1);
            $ret["$moved_x,$y"] = true;
        }
    }

    return $ret;
}

function fold_y($grid, $fold_line) {
    $ret = [];

    foreach ($grid as $point => $unused) {
        list($x, $y) = explode(",", $point);

        if ($y < $fold_line) {
            $ret[$point] = true;
        } else {
            $moved_y = $y - (($y - $fold_line) << 1);
            $ret["$x,$moved_y"] = true;;
        }
    }

    return $ret;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $grid = [];
    $folds = [];
    foreach ($lines as $line) {
        if (empty(trim($line))) {
            continue;
        }

        if (stripos($line, 'fold') === false) {
            $grid[trim($line)] = true;
        } else {
            $folds[] = $line;
        }
    }

    $grid_size = size($grid);

    if ($verbose) {
        display_grid($grid);
    }

    $cmd = array_shift($folds);
    $folded = fold($grid, $cmd);

    echo "CMD[$cmd]\n";
    if ($verbose) {
        display_grid($folded);
    }

    return count($folded);
}

function main($filename, $verbose = null, $expected = null)
{
    $actual = run($filename, $verbose);
    if ($expected) {
        if ($actual !== $expected) {
            echo "You broke $filename, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

main('star-25-example.txt', true, 17);
main('star-25-input.txt');

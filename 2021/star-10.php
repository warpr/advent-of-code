<?php

function parse_line($line)
{
    if (preg_match('/^([0-9]+)\s*,([0-9]+)\s*->\s*([0-9]+)\s*,\s*([0-9]+)$/', $line, $matches)) {
        return [
            'start' => [$matches[1], $matches[2]],
            'end' => [$matches[3], $matches[4]],
        ];
    }

    return null;
}

function steps($start, $end)
{
    $ret = [];

    if ($start > $end) {
        for ($i = $start; $i >= $end; $i--) {
            $ret[] = $i;
        }
    } else {
        for ($i = $start; $i <= $end; $i++) {
            $ret[] = $i;
        }
    }

    return $ret;
}

function plot_line(&$grid, $line)
{
    $start = $line['start'];
    $end = $line['end'];
    $x = $start[0];
    $y = $start[1];

    if ($start[0] === $end[0]) {
        // vertical line
        foreach (steps($start[1], $end[1]) as $y) {
            $grid[$y][$x]++;
        }
    } elseif ($start[1] === $end[1]) {
        // horizontal line
        foreach (steps($start[0], $end[0]) as $x) {
            $grid[$y][$x]++;
        }
    } else {
        // diagonal line
        $y_steps = steps($start[1], $end[1]);
        $x_steps = steps($start[0], $end[0]);
        foreach ($y_steps as $idx => $y) {
            $x = $x_steps[$idx];
            $grid[$y][$x]++;
        }
    }
}

function display_grid($grid)
{
    $rows = 0;
    $cols = 0;
    foreach ($grid as $y => $line) {
        if ($y > $rows) {
            $rows = $y;
        }
        foreach ($line as $x => $val) {
            if ($x > $cols) {
                $cols = $x;
            }
        }
    }

    echo "\n";
    for ($j = 0; $j <= $rows; $j++) {
        printf("%4d)\t", $j);
        for ($i = 0; $i <= $cols; $i++) {
            if (empty($grid[$j][$i])) {
                echo '. ';
            } else {
                printf('%1d ', $grid[$j][$i]);
            }
        }
        echo "\n";
    }
    echo "\n";
}

function run($filename, $display = false)
{
    $lines = array_map('trim', file($filename));

    echo "\nstart\n=====\n";

    $grid = [];
    foreach ($lines as $idx => $line) {
        plot_line($grid, parse_line($line));
        if ($display) {
            display_grid($grid);
        }
    }

    $ret = 0;
    foreach ($grid as $row) {
        foreach ($row as $val) {
            if ($val > 1) {
                $ret++;
            }
        }
    }

    return $ret;
}

$expected = 12;
$actual = run('star-09-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-09-input.txt');

echo "The puzzle answer is:  $output\n";

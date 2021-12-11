<?php

function flash_octopus(&$grid, $x, $y)
{
    $neighbours = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];

    $count = 1;

    $grid[$y][$x] = 'z';
    foreach ($neighbours as $pair) {
        $ny = $y + $pair[0];
        $nx = $x + $pair[1];

        if (!is_numeric($grid[$ny][$nx])) {
            continue;
        }

        if (++$grid[$ny][$nx] > 9) {
            $count += flash_octopus($grid, $nx, $ny);
        }
    }

    return $count;
}

function simulate(&$grid)
{
    $flashes = 0;

    $kx = 9;
    $ky = 3;

    foreach ($grid as $y => &$row) {
        foreach ($row as $x => &$octopus) {
            if ($octopus === 'z') {
                continue;
            }
            if (++$octopus > 9) {
                $flashes += flash_octopus($grid, $x, $y);
            }
        }
    }

    foreach ($grid as $y => &$row) {
        foreach ($row as $x => &$octopus) {
            if ($octopus === 'z') {
                $octopus = 0;
            }
        }
    }

    return $flashes;
}

function display_grid($grid)
{
    foreach ($grid as $row) {
        foreach ($row as $octopus) {
            if ($octopus == 0) {
                echo chr(27) . '[1m' . $octopus . chr(27) . '[0m';
            } else {
                echo $octopus;
            }
        }
        echo "\n";
    }
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $grid = array_map('str_split', $lines);

    $total_flashes = 0;

    for ($i = 1; $i < 101; $i++) {
        $total_flashes += simulate($grid);
        if ($verbose) {
            echo "After step $i:\n";
            display_grid($grid);
        }
        echo "Step $i, total flashes so far: " . $total_flashes . "\n";
    }

    return $total_flashes;
}

$expected = 1656;
$actual = run('star-21-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-21-input.txt');

echo "The puzzle answer is:  $output\n";

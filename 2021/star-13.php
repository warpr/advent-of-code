<?php

function fuel_required($pos, $crabs)
{
    $sum = 0;
    foreach ($crabs as $crab) {
        $sum += abs($crab - $pos);
    }

    return $sum;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $positions = explode(',', array_shift($lines));
    $max = $positions[0];
    $min = $positions[0];

    foreach ($positions as $pos) {
        $max = $pos > $max ? $pos : $max;
        $min = $pos < $min ? $pos : $min;
    }

    $best = fuel_required($min, $positions);

    for ($i = $min; $i <= $max; $i++) {
        $fuel = fuel_required($i, $positions);
        if ($verbose) {
            echo "Fuel required for pos $i: $fuel\n";
        }

        if ($fuel < $best) {
            $best = $fuel;
            if ($verbose) {
                echo "Best so far is position $i with a cost of $fuel\n";
            }
        }
    }

    return $best;
}

$expected = 37;
$actual = run('star-13-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-13-input.txt');

echo "The puzzle answer is:  $output\n";

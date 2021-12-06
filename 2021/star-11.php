<?php

function simulate(&$fish)
{
    $new = [];
    foreach ($fish as $idx => $val) {
        $fish[$idx]--;

        if ($val === 0) {
            $new[] = 8;
            $fish[$idx] = 6;
        }
    }

    array_splice($fish, count($fish), 0, $new);
}

function run($filename, $display = false)
{
    $lines = array_map('trim', file($filename));

    $fish = explode(',', array_shift($lines));

    for ($i = 1; $i <= 80; $i++) {
        simulate($fish);

        if ($display) {
            echo "Day $i: " . count($fish) . " fish.\n";
        }
    }

    return count($fish);
}

$expected = 5934;
$actual = run('star-11-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-11-input.txt');

echo "The puzzle answer is:  $output\n";

<?php

function empty_buckets()
{
    $buckets = [];
    for ($i = 0; $i < 9; $i++) {
        $buckets[$i] = 0;
    }
    return $buckets;
}

function simulate($fish)
{
    $buckets = empty_buckets();

    foreach ($fish as $idx => $val) {
        if ($idx === 0) {
            $buckets[6] += $val;
            $buckets[8] += $val;
        } else {
            $buckets[$idx - 1] += $val;
        }
    }

    return $buckets;
}

function run($filename, $display = false)
{
    $lines = array_map('trim', file($filename));

    $fish = explode(',', array_shift($lines));
    $buckets = empty_buckets();

    foreach ($fish as $val) {
        $buckets[$val]++;
    }

    for ($i = 1; $i <= 256; $i++) {
        $buckets = simulate($buckets);
        $total = array_sum($buckets);

        if ($display) {
            printf('Day %3d: %7d fish > ', $i, $total);
            foreach ($buckets as $bucket => $amount) {
                printf('%d ', $amount);
            }
            echo "\n";
        }
    }

    return $total;
}

$expected = 26984457539;
$actual = run('star-11-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-11-input.txt');

echo "The puzzle answer is:  $output\n";

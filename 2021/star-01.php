<?php

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $prev = array_shift($lines);
    $inc = 0;
    while ($lines) {
        $current = array_shift($lines);
        if ($current === '') {
            continue;
        }

        if ($current > $prev) {
            $inc++;
        }

        $prev = $current;
    }

    return $inc;
}

$result = run('star-01-example.txt');
if ($result !== 7) {
    echo "You broke the example.\n";
}

$output = run('star-01-input.txt');

echo "The answer is:  $output\n";

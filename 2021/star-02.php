<?php

function offset(&$lines, $offset)
{
    for ($i = 0; $i < $offset; $i++) {
        yield '';
    }

    foreach ($lines as $line) {
        yield $line;
    }
}

function zip($arrays)
{
    $idx = 0;

    while (true) {
        $zipped = [];
        foreach ($arrays as $idx => $arr) {
            if (!$arr->valid()) {
                return;
            }

            $zipped[] = $arr->current();
            $arr->next();
        }

        yield $zipped;
    }
}

function is_window_complete($window)
{
    foreach ($window as $value) {
        if ($value === null || $value === '') {
            return false;
        }
    }

    return true;
}

function complete_windows($windows)
{
    foreach ($windows as $w) {
        if (is_window_complete($w)) {
            yield $w;
        }
    }
}

function sum_window($windows)
{
    foreach ($windows as $w) {
        yield array_sum($w);
    }
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $prev = array_shift($lines);
    $inc = 0;

    foreach (
        sum_window(complete_windows(zip([offset($lines, 2), offset($lines, 1), offset($lines, 0)])))
        as $current
    ) {
        if ($current > $prev) {
            $inc++;
        }

        $prev = $current;
    }

    return $inc;
}

$result = run('star-01-example.txt');
if ($result !== 5) {
    echo "You broke the example.\n";
}

$output = run('star-01-input.txt');

echo "The answer is:  $output\n";

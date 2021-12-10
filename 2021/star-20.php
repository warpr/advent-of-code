<?php

function is_open($chr) {
    return $chr == '(' || $chr == '[' || $chr == '{' || $chr == '<';
}

function is_match($open, $close) {
    return ($open == '(' && $close == ')')
        || ($open == '[' && $close == ']')
        || ($open == '{' && $close == '}')
        || ($open == '<' && $close == '>');
}

function filter_corrupted($line) {
    $open = [];
    foreach (str_split($line) as $chr) {
        if (is_open($chr)) {
            $open[] = $chr;
            continue;
        }

        if (is_match(array_pop($open), $chr)) {
            continue;
        }

        return null;
    }

    return $line;
}

function score_incomplete($line, $verbose) {
    $open = [];
    foreach (str_split($line) as $chr) {
        if (is_open($chr)) {
            $open[] = $chr;
            continue;
        }

        if (is_match(array_pop($open), $chr)) {
            continue;
        }
    }

    $scoring = [
        '(' => 1,
        '[' => 2,
        '{' => 3,
        '<' => 4,
    ];

    $line_score = 0;
    $open = array_reverse($open);
    foreach ($open as $chr) {
        $line_score = $line_score * 5 + $scoring[$chr];
    }

    if ($verbose) {
        echo $line . " | " . implode("", $open) . " = $line_score\n";
    }
    return $line_score;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $scoring = [
        ')' => 1,
        ']' => 2,
        '}' => 3,
        '>' => 4,
    ];

    $incomplete = [];
    foreach ($lines as $line) {
        $i = filter_corrupted($line);
        if ($i) {
            $incomplete[] = $i;
        }
    }

    $results = [];
    foreach ($incomplete as $line) {
        $results[] = score_incomplete($line, $verbose);
    }

    sort($results);
    $idx = count($results) >> 1;
    if ($verbose) {
        print_r(compact('idx', 'results'));
    }

    return $results[$idx];
}

$expected = 288957;
$actual = run('star-19-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-19-input.txt');

echo "The puzzle answer is:  $output\n";

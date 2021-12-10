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

function parse_line($line) {
    $open = [];
    foreach (str_split($line) as $chr) {
        if (is_open($chr)) {
            $open[] = $chr;
            continue;
        }

        if (is_match(array_pop($open), $chr)) {
            continue;
        }

        return $chr;
    }

    return '';
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $scoring = [
        ')' => 3,
        ']' => 57,
        '}' => 1197,
        '>' => 25137,
    ];

    $results = [];
    foreach ($lines as $line) {
        $results[] = parse_line($line);
    }

    $ret = 0;
    foreach ($results as $r) {
        $ret += $scoring[$r] ?? 0;
    }

    return $ret;
}

$expected = 26397;
$actual = run('star-19-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-19-input.txt');

echo "The puzzle answer is:  $output\n";

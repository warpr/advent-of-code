<?php

$segments = [
    'abcefg',
    'cf',
    'acdeg',
    'acdfg',
    'bcdf',
    'abdfg',
    'abdefg',
    'acf',
    'abcdefg',
    'abcdfg',
];

$segment_lengths = [];
foreach ($segments as $idx => $s) {
    $segment_lengths[strlen($s)][] = $idx;
}

function determine_value($input)
{
    global $segment_lengths;

    $possibilites = $segment_lengths[strlen($input)] ?? [];

    if (count($possibilites) == 1) {
        return 1;
    }

    return null;
}

function parse_values($str)
{
    $parts = explode(' ', $str);

    $ret = [];
    foreach ($parts as $i => $digit) {
        $tmp = trim($digit);
        if (empty($tmp)) {
            continue;
        }
        $val = determine_value($tmp);
        if ($val) {
            $ret[] = $val;
        }
    }

    return $ret;
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $total = 0;
    foreach ($lines as $line) {
        list($signal, $output) = explode('|', $line);
        $line_total = array_sum(parse_values($output));
        $total += $line_total;
    }

    return $total;
}

$expected = 26;
$actual = run('star-15-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-15-input.txt');

echo "The puzzle answer is:  $output\n";

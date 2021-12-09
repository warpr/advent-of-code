<?php

$segments = [
    'abcefg', // 0
    'cf', // 1
    'acdeg', // 2
    'acdfg', // 3
    'bcdf', // 4
    'abdfg', // 5
    'abdefg', // 6
    'acf', // 7
    'abcdefg', // 8
    'abcdfg', // 9
];

$segments_to_num = array_flip($segments);

function map_digit($digit, $permutation)
{
    global $segments_to_num;

    $ret = [];
    foreach (str_split($digit) as $char) {
        $ret[] = chr($permutation[$char] + ord('a'));
    }

    sort($ret);
    $val = implode('', $ret);

    return array_key_exists($val, $segments_to_num) ? $segments_to_num[$val] : null;
}

// Dijkstra via
// 1. https://stackoverflow.com/questions/10222835/get-all-permutations-of-a-php-array
// 2. https://docstore.mik.ua/orelly/webprog/pcook/ch04_26.htm
function pc_next_permutation($p, $size)
{
    // slide down the array looking for where we're smaller than the next guy
    for ($i = $size - 1; $p[$i] >= $p[$i + 1]; --$i) {
    }

    // if this doesn't occur, we've finished our permutations
    // the array is reversed: (1, 2, 3, 4) => (4, 3, 2, 1)
    if ($i == -1) {
        return false;
    }

    // slide down the array looking for a bigger number than what we found before
    for ($j = $size; $p[$j] <= $p[$i]; --$j) {
    }

    // swap them
    $tmp = $p[$i];
    $p[$i] = $p[$j];
    $p[$j] = $tmp;

    // now reverse the elements in between by swapping the ends
    for (++$i, $j = $size; $i < $j; ++$i, --$j) {
        $tmp = $p[$i];
        $p[$i] = $p[$j];
        $p[$j] = $tmp;
    }

    return $p;
}

function all_permutations($arr)
{
    yield $arr;

    $size = count($arr) - 1;
    $permutation = array_keys($arr);

    while ($permutation = pc_next_permutation($permutation, $size)) {
        $ret = [];
        foreach ($permutation as $i => $v) {
            $ret[$i] = $arr[$v];
        }
        yield $ret;
    }
}

function split_digits($str)
{
    $parts = explode(' ', $str);
    $ret = [];
    foreach ($parts as $digit) {
        $val = trim($digit);
        if (!empty($val)) {
            $ret[] = $val;
        }
    }
    return $ret;
}

function try_permutation($signal_digits, $output_digits, $permutation)
{
    $signal = [];
    foreach ($signal_digits as $digit) {
        $signal[] = map_digit($digit, $permutation);
        if (end($signal) === null) {
            return false;
        }
    }

    $output = [];
    foreach ($output_digits as $digit) {
        $output[] = map_digit($digit, $permutation);
        if (end($output) === null) {
            return false;
        }
    }

    return implode('', $output);
}

function process_line($line)
{
    list($signal, $output) = explode('|', $line);
    $signal_digits = split_digits($signal);
    $output_digits = split_digits($output);

    foreach (all_permutations(str_split('abcdefg')) as $i => $p) {
        $pflip = array_flip($p);
        $answer = try_permutation($signal_digits, $output_digits, $pflip);

        if ($answer !== false) {
            return $answer;
        }
    }

    return false;
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    echo "Start!\n";
    $answers = [];
    foreach ($lines as $i => $line) {
        echo "$i> $line\n";
        $answers[] = process_line($line);
        echo "$i] answer is " . end($answers) . "\n";
    }
    echo "Done!\n";

    return array_sum($answers);
}

$expected = 5353;
$actual = run('star-16-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$expected = 61229;
$actual = run('star-15-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-15-input.txt');

echo "The puzzle answer is:  $output\n";

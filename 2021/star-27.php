<?php

function parse_pair($line)
{
    list($input, $insert) = explode(' -> ', $line);

    return [$input, [$input[0] . $insert, $insert . $input[1]]];
}

function make_pairs($template)
{
    for ($i = 1; $i < strlen($template); $i++) {
        yield $template[$i - 1] . $template[$i];
    }
}

function simulate_step($pairs, &$rules)
{
    $ret = [];
    foreach ($pairs as $pair => $count) {
        foreach ($rules[$pair] as $new_pair) {
            $ret[$new_pair] += $count;
        }
    }

    return $ret;
}

function count_letters($template, $pairs)
{
    $ret = [];
    foreach ($pairs as $pair => $how_many) {
        $ret[$pair[0]] += $how_many * 0.5;
        $ret[$pair[1]] += $how_many * 0.5;
    }

    $ret[$template[0]] += 0.5;
    $ret[substr($template, -1)] += 0.5;
    return $ret;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $template = array_shift($lines);

    $rules = [];
    foreach ($lines as $line) {
        if (empty($line)) {
            continue;
        }

        list($input, $output) = parse_pair($line);
        $rules[$input] = $output;
    }

    $pairs = [];
    foreach (make_pairs($template) as $pair) {
        $pairs[$pair]++;
    }

    if ($verbose) {
        echo 'Before: ';
        print_r($pairs);
    }

    $steps = 10;
    for ($i = 0; $i < $steps; $i++) {
        $pairs = simulate_step($pairs, $rules);
    }

    $counts = count_letters($template, $pairs);

    if ($verbose) {
        echo 'After: ';
        print_r($pairs);
        echo 'Counts: ';
        print_r($counts);
        echo 'Total: ' . array_sum(array_values($counts)) . "\n";
    }

    $min = null;
    $max = null;
    foreach ($counts as $count) {
        if ($min === null || $min > $count) {
            $min = $count;
        }
        if ($max === null || $max < $count) {
            $max = $count;
        }
    }

    if ($verbose) {
        echo "Min: $min, Max: $max, Answer: " . (int) ($max - $min) . "\n";
    }

    return (int) ($max - $min);
}

function main($filename, $verbose = null, $expected = null)
{
    $actual = run($filename, $verbose);
    if ($expected) {
        if ($actual !== $expected) {
            echo "You broke $filename, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

main('star-27-example.txt', true, 1588);
main('star-27-input.txt', false);

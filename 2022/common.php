<?php

declare(strict_types=1);

function runner($func, $filename, $verbose = null, $expected = null)
{
    chdir(__DIR__);

    $actual = $func($filename, $verbose);
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

function run_part($part_no, $input_name, $verbose = false, $expected = null)
{
    global $argv;

    if (!preg_match(',/day-0x([0-9a-f][0-9a-f]).php,', $argv[0], $matches)) {
        return;
    }

    $day = hexdec($matches[1]);
    $part = "part$part_no";
    $filename = sprintf('day-0x%02x.%s.txt', $day, $input_name);

    runner($part, $filename, $verbose, $expected);
}

function run_part1($input_name, $verbose = false, $expected = null)
{
    return run_part(1, $input_name, $verbose, $expected);
}

function run_part2($input_name, $verbose = false, $expected = null)
{
    return run_part(2, $input_name, $verbose, $expected);
}

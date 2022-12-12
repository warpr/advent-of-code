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

/**
 * Array helpers
 * =============
 */

function get_by_path(array $var, array $path, $default = null)
{
    while (count($path) > 0) {
        if (is_object($var)) {
            $var = (array) $var;
        }

        if (!is_array($var)) {
            return $default;
        }

        $part = array_shift($path);
        if (isset($var[$part]) || (is_numeric($part) && isset($var[(int) $part]))) {
            $var = &$var[$part];
        } else {
            return $default;
        }
    }

    return $var;
}

function set_by_path(array &$var, array $path, $value): void
{
    while (count($path) > 1) {
        $part = array_shift($path);
        if (!isset($var[$part])) {
            $var[$part] = [];
        }
        $var = &$var[$part];
    }

    $var[$path[0]] = $value;
}

function array_edit_recursive($tree, $callable)
{
    if (is_array($tree)) {
        foreach ($tree as $key => $val) {
            $tree[$key] = array_edit_recursive($val, $callable);
        }
    }

    return $callable($tree);
}

function sort_by(array $items, string $field)
{
    usort($items, function ($a, $b) use ($field) {
        $a_val = is_array($a) ? $a[$field] : $a->$field;
        $b_val = is_array($b) ? $b[$field] : $b->$field;

        if ($a_val == $b_val) {
            return 0;
        }
        return $a_val < $b_val ? -1 : 1;
    });

    return $items;
}

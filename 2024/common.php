<?php

declare(strict_types=1);

require_once __DIR__ . '/vecho.php';

function memoize(callable $fn): callable
{
    return function (...$args) use ($fn) {
        static $cache = [];
        $key = serialize($args);
        if (!isset($cache[$key])) {
            $cache[$key] = $fn(...$args);
        }
        return $cache[$key];
    };
}

function display_percentage(string $msg, int $start, int $end, int $current)
{
    static $prev = 0;

    if (time() - $prev < 2) {
        return;
    }

    $total = $end - $start;
    $percentage = (int) round(($current / $total) * 100);
    echo '[' . str_pad("$percentage", 4, ' ', STR_PAD_LEFT) . "%] $msg\n";

    $prev = time();
}

function runner($filename, $verbose = null, $part_no = 1, $expected = null)
{
    chdir(__DIR__);

    vecho::$verbose = $verbose;

    $actual = main($filename, $part_no == 2);
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
    $filename = sprintf('day-0x%02x.%s.txt', $day, $input_name);

    runner($filename, $verbose, $part_no, $expected);
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

function as_array($value)
{
    return is_array($value) ? $value : [$value];
}

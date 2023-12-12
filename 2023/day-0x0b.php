<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

$empty_space_size = 1;

function display_grid(bool $verbose, array $grid)
{
    if (!$verbose) {
        return;
    }

    foreach ($grid as $idx => $row) {
        echo str_pad("$idx", 4, ' ', STR_PAD_LEFT);
        echo '. ' . implode('', $row) . "\n";
    }
}

function get_column(array &$grid, int $col)
{
    $ret = [];
    foreach ($grid as $row) {
        $ret[] = $row[$col];
    }
    return $ret;
}

function expand(array $grid)
{
    $max_y = count($grid);
    $max_x = count($grid[0]);

    $add_columns = [];
    for ($x = 0; $x < $max_x; $x++) {
        // is column empty?
        $galaxies = array_filter(get_column($grid, $x), fn($i) => $i === '#');
        if (empty($galaxies)) {
            $add_columns[] = $x;
        }
    }

    $add_rows = [];
    for ($y = 0; $y < $max_y; $y++) {
        $galaxies = array_filter($grid[$y], fn($i) => $i === '#');
        if (empty($galaxies)) {
            $add_rows[] = $y;
        }
    }

    $add_columns = array_reverse($add_columns);
    $add_rows = array_reverse($add_rows);

    foreach ($add_columns as $x) {
        foreach ($grid as $y => &$row) {
            array_splice($row, $x, 0, ['o']);
        }
    }

    $max_x = count($grid[0]);
    foreach ($add_rows as $y) {
        $empty_row = array_fill(0, $max_x, 'o');
        array_splice($grid, $y, 0, [$empty_row]);
    }

    return $grid;
}

function find_galaxies(array &$grid)
{
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $chr) {
            if ($chr === '#') {
                yield [$x, $y];
            }
        }
    }
}

function pairs(array $coords)
{
    $ret = [];

    foreach ($coords as $a) {
        foreach ($coords as $b) {
            $coords_str = array_unique([implode(',', $a), implode(',', $b)]);
            if (count($coords_str) > 1) {
                sort($coords_str);
                $key = implode(' ', $coords_str);
                $ret[$key] = [$a, $b];
            }
        }
    }

    return array_values($ret);
}

function distances(array &$grid, array $pairs)
{
    global $empty_space_size;

    foreach ($pairs as $pair) {
        $from = $pair[0];
        $to = $pair[1];

        $x_range = [$from[0], $to[0]];
        $y_range = [$from[1], $to[1]];
        sort($x_range);
        sort($y_range);
        $y = $y_range[0];

        $path = [];
        for ($x = $x_range[0]; $x < $x_range[1]; $x++) {
            $path[] = $grid[$y][$x];
        }

        for ($y = $y_range[0]; $y < $y_range[1]; $y++) {
            $path[] = $grid[$y][$x];
        }

        $total = 0;
        foreach ($path as $chr) {
            $total += $chr === 'o' ? $empty_space_size : 1;
        }

        yield $total;
    }
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $grid = [];
    foreach ($lines as $line) {
        $grid[] = str_split(trim($line));
    }

    $grid = expand($grid);

    display_grid($verbose, $grid);

    $pairs = pairs(iterator_to_array(find_galaxies($grid)));

    return iterator_to_array(distances($grid, $pairs));
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 374);
run_part1('input', false);
echo "\n";

$empty_space_size = 9;
run_part2('example', true, 1030); // 10 times

$empty_space_size = 99;
run_part2('example', true, 8410); // 100 times

$empty_space_size = 999999;
run_part2('input', false); // 1000000 times
echo "\n";

<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

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

    // print_r(compact('max_x', 'max_y'));

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

    // print_r(compact('add_columns', 'add_rows'));

    foreach ($add_columns as $x) {
        foreach ($grid as $y => &$row) {
            array_splice($row, $x, 0, ['.']);
        }
    }

    $max_x = count($grid[0]);
    foreach ($add_rows as $y) {
        $empty_row = array_fill(0, $max_x, '.');
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

function distances(array $pairs)
{
    foreach ($pairs as $pair) {
        yield abs($pair[0][0] - $pair[1][0]) + abs($pair[0][1] - $pair[1][1]);
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

    return iterator_to_array(distances($pairs));
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

/*
run_part2('example', true, 4);
run_part2('input', false);
echo "\n";
*/

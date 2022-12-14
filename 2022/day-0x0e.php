<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse(string $filename)
{
    $lines = file($filename);

    foreach ($lines as $line) {
        $path = [];
        $parts = explode('->', trim($line));
        foreach ($parts as $pos) {
            list($x, $y) = explode(',', $pos);
            $path[] = (object) ['x' => (int) trim($x), 'y' => (int) trim($y)];
        }

        yield $path;
    }
}

function pairs(array $items)
{
    $first = array_shift($items);
    foreach ($items as $second) {
        yield (object) compact('first', 'second');
        $first = $second;
    }
}

function draw_line(array &$grid, $line): void
{
    if ($line->first->x === $line->second->x) {
        // vertical line
        $start_y = min($line->first->y, $line->second->y);
        $end_y = max($line->first->y, $line->second->y);
        for ($y = $start_y; $y <= $end_y; $y++) {
            @$grid[$y][$line->first->x] = '#';
        }
    } else {
        // horizontal line
        $start_x = min($line->first->x, $line->second->x);
        $end_x = max($line->first->x, $line->second->x);
        for ($x = $start_x; $x <= $end_x; $x++) {
            @$grid[$line->first->y][$x] = '#';
        }
    }
}

function draw_lines($paths)
{
    $grid = [];

    foreach ($paths as $path) {
        foreach (pairs($path) as $line) {
            draw_line($grid, $line);
        }
    }

    @$grid[0][500] = '+';

    return $grid;
}

function grid_size(array $grid)
{
    $min_y = 0;
    $max_y = max(array_keys($grid)) + 2;

    $min_x = min(array_map('min', array_map('array_keys', $grid))) - 2;
    $max_x = max(array_map('max', array_map('array_keys', $grid))) + 2;

    return (object) compact('min_x', 'min_y', 'max_x', 'max_y');
}

function display_grid(array $grid, bool $verbose)
{
    $size = grid_size($grid);

    if (!$verbose) {
        return;
    }

    echo "___ x starts at {$size->min_x} ___\n";
    for ($y = $size->min_y; $y <= $size->max_y; $y++) {
        printf('[%03d] ', $y);
        for ($x = $size->min_x; $x <= $size->max_x; $x++) {
            echo $grid[$y][$x] ?? '.';
        }
        echo "\n";
    }
}

function simulate_sand(array &$grid, int $max_y, bool $floor)
{
    $x = 500;
    $y = 0;

    while ($y <= $max_y) {
        if ($floor) {
            $grid[$max_y][$x - 2] = '#';
            $grid[$max_y][$x - 1] = '#';
            $grid[$max_y][$x] = '#';
            $grid[$max_y][$x + 1] = '#';
            $grid[$max_y][$x + 2] = '#';
        }

        $below = $grid[$y + 1][$x] ?? null;
        $left = $grid[$y + 1][$x - 1] ?? null;
        $right = $grid[$y + 1][$x + 1] ?? null;

        if (!$below) {
            $y++;
            continue;
        }

        if (!$left) {
            $y++;
            $x--;
            continue;
        }

        if (!$right) {
            $y++;
            $x++;
            continue;
        }

        if ($grid[$y][$x] ?? null === '+') {
            // blocked the starting point
            $grid[$y][$x] = 'o';
            return false;
        }

        // sand came to rest
        $grid[$y][$x] = 'o';
        return true;
    }

    // sand fell forever
    return false;
}

function main(string $filename, bool $verbose, bool $floor)
{
    $grid = draw_lines(parse($filename));

    $size = grid_size($grid);
    display_grid($grid, $verbose);

    $grains = 0;
    while (simulate_sand($grid, $size->max_y, $floor)) {
        $grains++;
        display_grid($grid, $verbose);
    }

    display_grid($grid, $verbose);

    if ($verbose) {
        print_r(compact('grains'));
    }

    return $grains;
}

function part1(string $filename, bool $verbose)
{
    return main($filename, false, false);
}

function part2(string $filename, bool $verbose)
{
    return 1 + main($filename, $verbose, true);
}

run_part1('example', true, 24);
run_part1('input', false);
echo "\n";

run_part2('example', true, 93);
run_part2('input', false);
echo "\n";

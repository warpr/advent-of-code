<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function distance($start, $end)
{
    $x_distance = abs($start->x - $end->x);
    $y_distance = abs($start->y - $end->y);
    return $x_distance + $y_distance;
}

function parse(string $filename)
{
    $lines = file($filename);

    foreach ($lines as $line) {
        if (
            preg_match(
                '/Sensor at x=([0-9-]+), y=([0-9-]+): closest beacon is at x=([0-9-]+), y=([0-9-]+)/',
                $line,
                $matches
            )
        ) {
            yield (object) [
                'sensor' => (object) [
                    'x' => (int) $matches[1],
                    'y' => (int) $matches[2],
                ],
                'beacon' => (object) [
                    'x' => (int) $matches[3],
                    'y' => (int) $matches[4],
                ],
            ];
        }
    }
}

function grid_size(array $grid)
{
    $min_y = min(array_keys($grid));
    $max_y = max(array_keys($grid));

    $min_x = min(array_map('min', array_map('array_keys', $grid)));
    $max_x = max(array_map('max', array_map('array_keys', $grid)));

    return (object) compact('min_x', 'min_y', 'max_x', 'max_y');
}

function display_grid(array $grid, bool $verbose)
{
    $size = grid_size($grid);

    if (!$verbose) {
        return;
    }

    for ($y = $size->min_y; $y <= $size->max_y; $y++) {
        printf('[%3d] ', $y);
        for ($x = $size->min_x; $x <= $size->max_x; $x++) {
            echo $grid[$y][$x] ?? '.';
        }
        echo "\n";
    }
}

function draw_sensor_range(&$grid, $sensor, $distance)
{
    $min_y = $sensor->y - $distance - 10;
    $max_y = $sensor->y + $distance + 10;
    $min_x = $sensor->x - $distance - 10;
    $max_x = $sensor->x + $distance + 10;

    for ($y = $min_y; $y <= $max_y; $y++) {
        for ($x = $min_x; $x <= $max_x; $x++) {
            if (distance($sensor, (object) compact('x', 'y')) <= $distance) {
                if (empty($grid[$y][$x])) {
                    @$grid[$y][$x] = '#';
                }
            }
        }
    }
}

function draw_sensor_data($data)
{
    $grid = [];

    $count = 1;
    foreach ($data as $reading) {
        printf(
            "Drawing sensor reading %d at (%d, %d)\n",
            $count++,
            $reading->sensor->x,
            $reading->sensor->y
        );

        @$grid[$reading->sensor->y][$reading->sensor->x] = 'S';
        @$grid[$reading->beacon->y][$reading->beacon->x] = 'B';

        $beacon_distance = distance($reading->sensor, $reading->beacon);
        draw_sensor_range($grid, $reading->sensor, $beacon_distance);
    }

    return $grid;
}

function main(string $filename, bool $verbose)
{
    $grid = draw_sensor_data(parse($filename));

    $size = grid_size($grid);
    display_grid($grid, $verbose);

    $check_row = $filename === 'input' ? 2000000 : 10;

    $no_beacon = count(array_filter($grid[$check_row], fn($i) => $i !== 'B'));

    return $no_beacon;
}

function part1(string $filename, bool $verbose)
{
    return main($filename, $verbose);
}

function part2(string $filename, bool $verbose)
{
    return main($filename, $verbose);
}

run_part1('example', true, 26);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 93);
run_part2('input', false);
echo "\n";
*/

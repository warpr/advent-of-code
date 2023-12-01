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
            $sensor = (object) ['x' => (int) $matches[1], 'y' => (int) $matches[2]];
            $beacon = (object) ['x' => (int) $matches[3], 'y' => (int) $matches[4]];
            $range = distance($sensor, $beacon);

            yield (object) compact('sensor', 'beacon', 'range');
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

function display_grid(array $grid, bool $verbose, $bounds = null)
{
    $size = $bounds ? $bounds : grid_size($grid);

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

        draw_sensor_range($grid, $reading->sensor, $reading->range);
    }

    return $grid;
}

function bounds(array $readings)
{
    $r = array_pop($readings);
    $min_x = $r->sensor->x - $r->range;
    $max_x = $r->sensor->x + $r->range;
    $min_y = $r->sensor->y - $r->range;
    $max_y = $r->sensor->y + $r->range;

    foreach ($readings as $r) {
        $min_x = min($min_x, $r->sensor->x - $r->range);
        $max_x = max($max_x, $r->sensor->x + $r->range);
        $min_y = min($min_y, $r->sensor->y - $r->range);
        $max_y = max($max_y, $r->sensor->y + $r->range);
    }

    return (object) compact('min_x', 'min_y', 'max_x', 'max_y');
}

function part1(string $filename, bool $verbose)
{
    $y = str_contains($filename, 'input') ? 2000000 : 10;

    $readings = iterator_to_array(parse($filename));

    if ($verbose) {
        $grid = draw_sensor_data($readings);

        display_grid($grid, $verbose);
    }

    $beacons = [];
    foreach ($readings as $r) {
        $beacon_identifier = sprintf('%d,%d', $r->beacon->x, $r->beacon->y);
        $beacons[$beacon_identifier] = true;
    }

    $bounds = bounds($readings);
    print_r(compact('bounds', 'beacons'));

    $no_beacon = 0;
    for ($x = $bounds->min_x; $x < $bounds->max_x; $x++) {
        if (!empty($beacons["$x,$y"])) {
            if ($verbose) {
                echo 'B';
            }
            continue;
        }

        $current = (object) compact('x', 'y');

        $occupied = false;
        foreach ($readings as $r) {
            $distance_to_sensor = distance($r->sensor, $current);
            if ($distance_to_sensor <= $r->range) {
                $no_beacon++;
                $occupied = true;
                break;
            }
        }
        if ($verbose) {
            echo $occupied ? '#' : '.';
        }
    }

    return $no_beacon;
}

function part2(string $filename, bool $verbose)
{
    $max = str_contains($filename, 'input') ? 4000000 : 20;
    $bounds = (object) ['min_x' => 0, 'min_y' => 0, 'max_x' => $max, 'max_y' => $max];

    $readings = iterator_to_array(parse($filename));

    if ($verbose) {
        $grid = draw_sensor_data($readings);
        display_grid($grid, $verbose, $bounds);
    }

    $beacons = [];
    foreach ($readings as $r) {
        $beacon_identifier = sprintf('%d,%d', $r->beacon->x, $r->beacon->y);
        $beacons[$beacon_identifier] = true;
    }

    $distress_beacon = null;

    $count = 0;
    $to_check = $bounds->max_y - $bounds->min_y;

    for ($y = $bounds->min_y; $y < $bounds->max_y; $y++) {
        printf("Checking %d of %d ... \n", $count++, $to_check);

        for ($x = $bounds->min_x; $x < $bounds->max_x; $x++) {
            if (!empty($beacons["$x,$y"])) {
                continue;
            }

            $current = (object) compact('x', 'y');

            $occupied = false;
            foreach ($readings as $r) {
                $distance_to_sensor = distance($r->sensor, $current);
                if ($distance_to_sensor <= $r->range) {
                    $occupied = true;
                    break;
                }
            }

            if (!$occupied) {
                $distress_beacon = $current;
                break 2;
            }
        }
    }

    $answer = $distress_beacon->x * 4000000 + $distress_beacon->y;
    print_r(compact('distress_beacon', 'answer'));

    return $answer;
}

run_part1('example', true, 26);
run_part1('input', false);
echo "\n";

run_part2('example', true, 56000011);
run_part2('input', false);
echo "\n";

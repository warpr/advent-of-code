<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function pipe_mapping()
{
    return [
        '|' => ['N', 'S'],
        '-' => ['E', 'W'],
        'L' => ['E', 'N'],
        'J' => ['N', 'W'],
        '7' => ['S', 'W'],
        'F' => ['E', 'S'],
    ];
}

function find_direction(array $a, array $b)
{
    if ($a[0] === $b[0]) {
        return $a[1] < $b[1] ? 'S' : 'N';
    } else {
        return $a[0] < $b[0] ? 'W' : 'E';
    }
}

function patch_grid(array &$grid, array $start, array $start_shape)
{
    sort($start_shape);

    foreach (pipe_mapping() as $chr => $shape) {
        if ($start_shape == $shape) {
            $grid[$start[1]][$start[0]] = $chr;
        }
    }
}

function invert_dir(string $dir)
{
    switch ($dir) {
        case 'N':
            return 'S';
        case 'E':
            return 'W';
        case 'S':
            return 'N';
        case 'W':
            return 'E';
    }
}

function find_start(array $grid)
{
    foreach ($grid as $y => $row) {
        $x = strpos($row, 'S');
        if ($x !== false) {
            return [$x, $y];
        }
    }

    echo "Start not found\n";
    die();
}

function follow_pipe(
    bool $verbose,
    array $grid,
    array $current,
    string $from,
    string $pipe_override = null
) {
    $pipe_mapping = pipe_mapping();

    $directions = [
        'N' => [$current[0], $current[1] - 1],
        'E' => [$current[0] + 1, $current[1]],
        'S' => [$current[0], $current[1] + 1],
        'W' => [$current[0] - 1, $current[1]],
    ];

    $pipe = $grid[$current[1]][$current[0]];
    if ($pipe === 'S') {
        $pipe = $pipe_override;
    }

    $dirs = $pipe_mapping[$pipe] ?? null;
    if (empty($dirs)) {
        return null;
    }

    $diff = array_diff($dirs, [$from]);
    if (count($diff) != 1) {
        return null;
    }
    $next_dir = array_pop($diff);

    return [
        'coord' => $directions[$next_dir],
        'from' => invert_dir($next_dir),
    ];
}

function find_loop(bool $verbose, array &$grid, array $start, string $dir, string $pipe_override)
{
    $coords = [$start];

    $current = $start;
    while (true) {
        $next_step = follow_pipe($verbose, $grid, $current, $dir, $pipe_override);
        if (!$next_step) {
            return null;
        }

        $next_chr = $grid[$next_step['coord'][1]][$next_step['coord'][0]];
        if ($next_chr === '.') {
            return null;
        }

        if ($next_chr === 'S') {
            return $coords;
        }

        $current = $next_step['coord'];
        $dir = $next_step['from'];
        $coords[] = $current;
    }
}

function clear_junk(array &$grid, array $path)
{
    $hash = [];
    foreach ($path as $coord) {
        $key = implode(',', $coord);
        $hash[$key] = true;
    }

    foreach ($grid as $y => $row) {
        foreach (str_split($row) as $x => $chr) {
            $pos = implode(',', [$x, $y]);
            if (!array_key_exists($pos, $hash)) {
                $grid[$y][$x] = '.';
            }
        }
    }
}

function ray_cast(array &$grid)
{
    $pipe_mapping = pipe_mapping();

    $painted = 0;
    foreach ($grid as $y => $row) {
        $outside = true;

        $seen = [];

        $debug = $y == 62;

        foreach (str_split($row) as $x => $chr) {
            $exits = $pipe_mapping[$chr] ?? [];

            if (array_search('N', $exits) !== false) {
                $seen[] = 'N';
            }

            if (array_search('S', $exits) !== false) {
                $seen[] = 'S';
            }

            if (count($seen) >= 2) {
                if ($seen[0] !== $seen[1]) {
                    // crossed N-S or S-N pipe
                    $outside = !$outside;
                }
                $seen = [];
            }

            if (!$outside && $chr == '.') {
                $painted++;
                $grid[$y][$x] = '*';
            }
        }
    }

    return $painted;
}

function area(bool $verbose, array $grid, array $path)
{
    $start = $path[0];
    $first = $path[1];
    $last = end($path);
    $shape = [find_direction($start, $first), find_direction($start, $last)];

    patch_grid($grid, $start, $shape);
    clear_junk($grid, $path);
    $painted = ray_cast($grid);
    if ($verbose) {
        print_r($grid);
    }

    return $painted;
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $grid = [];
    foreach ($lines as $line) {
        $grid[] = trim($line);
    }

    $start = find_start($grid);

    $starting_directions = [
        'E' => '-',
        'N' => '|',
        'S' => '|',
        'W' => '-',
    ];

    foreach ($starting_directions as $dir => $pipe_override) {
        $path = find_loop($verbose, $grid, $start, $dir, $pipe_override);
        if ($path) {
            break;
        }
    }

    if ($part2) {
        return area($verbose, $grid, $path);
    }

    if ($verbose) {
        print_r($grid);
    }

    return (int) round(count($path) / 2);
}

function main(string $filename, bool $verbose, bool $part2)
{
    return parse($filename, $verbose, $part2);
}

run_part1('example', true, 4);
run_part1('example2', true, 8);
run_part1('input', false);
echo "\n";

run_part2('example3', true, 4);
run_part2('example4', true, 8);
run_part2('example5', true, 10);
run_part2('input', false);
echo "\n";

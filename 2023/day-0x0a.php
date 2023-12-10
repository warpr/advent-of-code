<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

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
    $directions = [
        'N' => [$current[0], $current[1] - 1],
        'E' => [$current[0] + 1, $current[1]],
        'S' => [$current[0], $current[1] + 1],
        'W' => [$current[0] - 1, $current[1]],
    ];

    $pipe_mapping = [
        '|' => ['N', 'S'],
        '-' => ['E', 'W'],
        'L' => ['N', 'E'],
        'J' => ['N', 'W'],
        '7' => ['W', 'S'],
        'F' => ['E', 'S'],
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

function find_loop(bool $verbose, array $grid, array $start, string $dir, string $pipe_override)
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

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $grid = [];
    foreach ($lines as $line) {
        $grid[] = trim($line);
    }

    if ($verbose) {
        print_r($grid);
    }

    $start = find_start($grid);

    $starting_directions = [
        'N' => '|',
        'E' => '-',
        'S' => '|',
        'W' => '-',
    ];

    foreach ($starting_directions as $dir => $pipe_override) {
        $path = find_loop($verbose, $grid, $start, $dir, $pipe_override);
        if ($path) {
            break;
        }
    }

    return (int) round(count($path) / 2);
}

function main(string $filename, bool $verbose, bool $part2)
{
    return parse($filename, $verbose, $part2);
}

run_part1('example', true, 4);
run_part1('example2', false, 8);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 2);
run_part2('input', false);
echo "\n";
*/

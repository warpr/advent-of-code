<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function part1(array $instructions, array $paths)
{
    $steps = 0;
    $current = 'AAA';
    while (true) {
        foreach ($instructions as $cmd) {
            $steps++;
            $current = $paths[$current][$cmd] ?? null;
            if (empty($current)) {
                echo "Game stopped\n";
                print_r(compact('current', 'cmd'));
                die();
            }
            if ($current === 'ZZZ') {
                return $steps;
            }
        }
    }

    return null;
}

function part2(array $instructions, array $paths)
{
    $steps = 0;
    $current = array_values(array_filter(array_keys($paths), fn($i) => str_ends_with($i, 'A')));

    $steps_since_z = [];
    $z_steps = [];

    while (true) {
        foreach ($instructions as $cmd) {
            $steps++;
            foreach ($current as $idx => $here) {
                $current[$idx] = $paths[$here][$cmd] ?? null;
                @$steps_since_z[$idx]++;
            }

            $non_zees = array_filter($current, fn($i) => !str_ends_with($i, 'Z'));
            if (empty($non_zees)) {
                return $steps;
            }

            foreach ($current as $idx => $node) {
                if (str_ends_with($node, 'Z')) {
                    $z_steps[$idx] = $steps_since_z[$idx];
                    $steps_since_z[$idx] = 0;
                }
            }

            // give it some random amount of iterations
            if ($steps > 100000) {
                break 2;
            }
        }
    }

    $common = array_shift($z_steps);
    while (count($z_steps)) {
        $common = gmp_lcm($common, array_shift($z_steps));
    }

    return $common;
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $instructions = str_split(trim(array_shift($lines)));
    $paths = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        if (preg_match('/(.*) = \((.*), (.*)\)/', $line, $matches)) {
            $paths[$matches[1]] = [
                'L' => $matches[2],
                'R' => $matches[3],
            ];
        }
    }

    return [$instructions, $paths];
}

function main(string $filename, bool $verbose, bool $part2)
{
    list($instructions, $paths) = parse($filename, $verbose, $part2);

    if ($part2) {
        return part2($instructions, $paths);
    }

    return part1($instructions, $paths);
}

run_part1('example', true, 2);
run_part1('example2', true, 6);
run_part1('input', false);
echo "\n";

run_part2('example3', true, 6);
run_part2('input', false);
echo "\n";

<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

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
                vecho($verbose, "Game concluded\n");
                return $steps;
            }
        }
    }

    return null;
}

function main(string $filename, bool $verbose, bool $part2)
{
    return parse($filename, $verbose, $part2);
}

run_part1('example', true, 2);
run_part1('example2', true, 6);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 5905);
run_part2('input', false);
echo "\n";
*/

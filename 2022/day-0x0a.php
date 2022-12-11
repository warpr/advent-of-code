<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function display_screen(array $screen)
{
    echo "\n";

    while (count($screen) > 0) {
        $line = array_slice($screen, 0, 40);
        $screen = array_slice($screen, 40);
        echo implode('', $line) . "\n";
    }

    echo "\n";
}

function current_row(array $screen)
{
    while (count($screen) > 40) {
        $screen = array_slice($screen, 40);
    }

    echo implode('', $screen) . "\n";
}

function update_screen(int $cycle, int $x, array &$screen, bool $verbose)
{
    $beam_pos = ($cycle - 1) % 40;
    if ($verbose) {
        echo "During cycle  $cycle: CRT draws pixel in position $beam_pos\n";
    }

    $pixel_visible = $beam_pos >= $x - 1 && $beam_pos <= $x + 1;
    $screen[$cycle - 1] = $pixel_visible ? '#' : '.';

    if ($verbose) {
        echo 'Current CRT row: ';
        current_row($screen);
    }
}

function main($filename, bool $verbose, array &$screen)
{
    $x = 1;

    $lines = file($filename);

    $signals = [];

    $cycles = 0;
    foreach ($lines as $line) {
        $cycles++;
        if ($verbose) {
            echo "\nStart cycle   $cycles: begin executing $line";
        }
        update_screen($cycles, $x, $screen, $verbose);

        if (preg_match('/noop/', $line)) {
            $signals[] = $x;
        } elseif (preg_match('/addx (-?[0-9]+)/', $line, $matches)) {
            $signals[] = $x;
            $cycles++;
            update_screen($cycles, $x, $screen, $verbose);
            $signals[] = $x;
            $x += (int) $matches[1];

            if ($verbose) {
                echo "End of cycle  $cycles: finish executing " .
                    trim($line) .
                    " (Register X is now $x)\n";
            }
        }
    }

    $pick = [20, 60, 100, 140, 180, 220];
    $signal_strengths = [];
    foreach ($pick as $p) {
        $signal_strengths[] = $p * ($signals[$p - 1] ?? 0);
        if ($verbose) {
            echo "Signal [$p] = " . end($signal_strengths) . "\n";
        }
    }

    return array_sum($signal_strengths);
}

function part1($filename, bool $verbose)
{
    $screen = [];

    return main($filename, $verbose, $screen);
}

function part2($filename, bool $verbose)
{
    $screen = [];

    main($filename, $verbose, $screen);

    display_screen($screen);

    return 23;
}

run_part1('example0', true, 0);
run_part1('example', true, 13140);
run_part1('input');
run_part2('example0', true, 0);
run_part2('example', true, 0);
run_part2('input');

// answer: PGPHBEAB

echo "\n";

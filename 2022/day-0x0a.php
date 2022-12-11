<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function part1($filename, bool $verbose)
{
    $x = 1;

    $lines = file($filename);

    $signals = [];

    $cycles = 0;
    foreach ($lines as $line) {
        if (preg_match('/noop/', $line)) {
            $cycles++;
            $signals[] = $x;
        } elseif (preg_match('/addx (-?[0-9]+)/', $line, $matches)) {
            $cycles++;
            $signals[] = $x;
            $cycles++;
            $signals[] = $x;
            $x += (int) $matches[1];
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

run_part1('example0', true, 0);
run_part1('example', true, 13140);
run_part1('input');

/* run_part2('example', true, 1);
 * run_part2('example2', false, 36);
 * run_part2('input');
 *  */
/**

noop
addx 3
addx -5


The interesting signal strengths can be determined as follows:

The sum of these signal strengths is 13140.

Find the signal strength during the 20th, 60th, 100th, 140th, 180th, and 220th cycles. What is the sum of these six signal strengths?

*/

echo "\n";

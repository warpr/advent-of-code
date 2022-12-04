<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function priority(string $chr)
{
    // Lowercase item types a through z have priorities 1 through 26.
    // Uppercase item types A through Z have priorities 27 through 52.

    if (ord($chr) >= ord('a') && ord($chr) <= ord('z')) {
        return ord($chr) - ord('a') + 1;
    } else {
        return ord($chr) - ord('A') + 27;
    }
}

function part1($filename, $verbose)
{
    $lines = file($filename);
    $total = 0;

    foreach ($lines as $idx => $line) {
        $pairs = explode(',', trim($line));
        $elf1 = explode('-', $pairs[0]);
        $elf2 = explode('-', $pairs[1]);
        if ($verbose) {
            echo json_encode(compact('elf1', 'elf2')) . "\n";
        }

        if ($elf1[0] >= $elf2[0] && $elf1[1] <= $elf2[1]) {
            // elf1 section inside elf2 section
            $total++;
        } elseif ($elf2[0] >= $elf1[0] && $elf2[1] <= $elf1[1]) {
            $total++;
        }
    }

    return $total;
}

/*
    5-7,7-9 overlaps in a single section, 7.
    2-8,3-7 overlaps all of the sections 3 through 7.
    6-6,4-6 overlaps in a single section, 6.
    2-6,4-8 overlaps in sections 4, 5, and 6.
*/

function part2($filename, $verbose)
{
    $lines = file($filename);
    $total = 0;

    foreach ($lines as $idx => $line) {
        $pairs = explode(',', trim($line));
        $elf1 = explode('-', $pairs[0]);
        $elf2 = explode('-', $pairs[1]);
        if ($verbose) {
            echo json_encode(compact('elf1', 'elf2')) . "\n";
        }

        if ($elf1[0] >= $elf2[0] && $elf1[0] <= $elf2[1]) {
            // elf1 section starts inside elf2 section
            $total++;
        } elseif ($elf1[1] >= $elf2[0] && $elf1[1] <= $elf2[1]) {
            // elf1 section ends inside elf2 section
            $total++;
        } elseif ($elf2[0] >= $elf1[0] && $elf2[0] <= $elf1[1]) {
            $total++;
        } elseif ($elf2[1] >= $elf1[0] && $elf2[1] <= $elf1[1]) {
            $total++;
        }
    }

    return $total;
}

run_part1('example', true, 2);
run_part1('input');
run_part2('example', true, 4);
run_part2('input');
echo "\n";

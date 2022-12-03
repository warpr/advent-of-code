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
        $rucksack = str_split(trim($line));
        $cutoff = strlen($line) >> 1;

        $ruck = array_slice($rucksack, 0, $cutoff);
        $sack = array_slice($rucksack, $cutoff);
        $intersect = array_intersect($ruck, $sack);
        $common = array_shift($intersect);
        if ($verbose) {
            echo "[rucksack $idx] Common item: {$common}\n";
        }

        $total += priority($common);
    }

    return $total;
}

function group_by_three(array $lines)
{
    while (!empty($lines)) {
        yield [array_shift($lines), array_shift($lines), array_shift($lines)];
    }
}

function part2($filename, $verbose)
{
    $groups = iterator_to_array(group_by_three(array_map('trim', file($filename))));
    $total = 0;

    foreach ($groups as $idx => $group) {
        list($elf1, $elf2, $elf3) = array_map('str_split', $group);

        $intersect = array_intersect($elf1, $elf2, $elf3);
        $common = array_shift($intersect);
        if ($verbose) {
            echo "[group $idx] badge is $common\n";
        }

        $total += priority($common);
    }

    return $total;
}

run_part1('example', true, 157);
run_part1('input');
run_part2('example', true, 70);
run_part2('input');
echo "\n";

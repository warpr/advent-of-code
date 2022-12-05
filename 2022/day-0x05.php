<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

// Manually transcribe the stacks, as parsing sounds like a lot of work.
$input_stacks = [
    'example' => ['', 'ZN', 'MCD', 'P'],
    'input' => [
        '',
        'WDGBHRV',
        'JNGCRF',
        'LSFHDNJ',
        'JDSV',
        'SHDRQWNV',
        'PGHCM',
        'FJBGLZHC',
        'SJR',
        'LGSRBNVM',
    ],
];

function print_stacks(array $stacks)
{
    foreach ($stacks as $idx => $stack) {
        if ($idx == 0) {
            continue;
        }

        echo "[Stack $idx] " . implode(' ', $stack) . "\n";
    }
}

function move(int $count, array $stacks, int $from, int $to): array
{
    for ($i = 0; $i < $count; $i++) {
        $stacks[$to][] = array_pop($stacks[$from]);
    }

    return $stacks;
}

function move9001(int $count, array $stacks, int $from, int $to): array
{
    $tmp = [];
    for ($i = 0; $i < $count; $i++) {
        $tmp[] = array_pop($stacks[$from]);
    }

    $stacks[$to] = array_merge($stacks[$to], array_reverse($tmp));

    return $stacks;
}

function main($filename, $verbose, $move_func)
{
    global $input_stacks;

    $key = str_contains($filename, 'example') ? 'example' : 'input';
    $stacks = array_map('str_split', $input_stacks[$key]);

    if ($verbose) {
        print_stacks($stacks);
    }

    $lines = file($filename);
    foreach ($lines as $line) {
        if (preg_match('/^move ([0-9]+) from ([0-9]+) to ([0-9]+)/', $line, $matches)) {
            $stacks = $move_func((int) $matches[1], $stacks, (int) $matches[2], (int) $matches[3]);
            if ($verbose) {
                echo 'Command: [' . trim($line) . "]\n";
            }
        }
    }

    if ($verbose) {
        print_stacks($stacks);
    }

    $answer = '';
    foreach ($stacks as $stack) {
        $answer .= array_pop($stack);
    }

    return $answer;
}

function part1($filename, $verbose)
{
    return main($filename, $verbose, 'move');
}

function part2($filename, $verbose)
{
    return main($filename, $verbose, 'move9001');
}

run_part1('example', true, 'CMZ');
run_part1('input');
run_part2('example', true, 'MCD');
run_part2('input');
echo "\n";

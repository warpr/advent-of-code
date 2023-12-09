<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function pairs(array $arr)
{
    if (count($arr) < 2) {
        return [];
    }

    for ($i = 1; $i < count($arr); $i++) {
        yield [$arr[$i - 1], $arr[$i]];
    }
}

function is_all_zeroes(array $arr)
{
    foreach ($arr as $item) {
        if ($item !== 0) {
            return false;
        }
    }

    return true;
}

function process_history(bool $verbose, array $history)
{
    $log = [$history];

    $tmp = $history;
    do {
        $tmp = array_map(fn($i) => $i[1] - $i[0], iterator_to_array(pairs($tmp)));
        $log[] = $tmp;
    } while (!is_all_zeroes($tmp));

    $log = array_reverse($log);
    $last_item = 0;
    foreach ($log as $idx => $record) {
        $last_item = end($record) + $last_item;
        $log[$idx][] = $last_item;
    }

    $result = array_pop($log);
    return array_pop($result);
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        $history = array_map(fn($i) => (int) trim($i), explode(' ', $line));
        if (empty($history)) {
            continue;
        }

        $ret[] = process_history($verbose, $history);
    }

    return $ret;
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    // 18, 28, 68

    return array_sum($values);
}

run_part1('example', true, 114);
run_part1('input', false);
echo "\n";
/*
run_part2('example3', true, 6);
run_part2('input', false);
echo "\n";
*/

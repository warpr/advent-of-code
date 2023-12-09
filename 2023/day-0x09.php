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

function process_history_part2(bool $verbose, array $history)
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
        $last_item = $record[0] - $last_item;
        $log[$idx] = array_merge([$last_item], $record);
    }

    $result = array_pop($log);
    return array_shift($result);
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

        if ($part2) {
            $ret[] = process_history_part2($verbose, $history);
        } else {
            $ret[] = process_history($verbose, $history);
        }
    }

    return $ret;
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    // 5 -3 0

    return array_sum($values);
}

run_part1('example', false, 114);
run_part1('input', false);
echo "\n";

run_part2('example', true, 2);
run_part2('input', false);
echo "\n";

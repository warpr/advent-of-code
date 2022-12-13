<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function as_array($value)
{
    return is_array($value) ? $value : [$value];
}

function vecho(bool $verbose, string $msg)
{
    if ($verbose) {
        echo $msg;
    }
}

/**
 * @return integer:
 *   - positive integer means the order is correct
 *   - negative integer means the order is incorrect
 *   - zero means both values are equal
 */
function is_order_correct($first, $second, bool $verbose = false, string $nesting = ''): int
{
    vecho(
        $verbose,
        $nesting . '- Compare ' . json_encode($first) . ' vs ' . json_encode($second) . "\n"
    );

    if (is_numeric($first) && is_numeric($second)) {
        $ret = $second - $first;
        vecho($verbose, $nesting . "  - [num] returning $ret\n");
        return $ret;
    }

    $first = as_array($first);
    $second = as_array($second);

    foreach ($first as $idx => $left) {
        if (!array_key_exists($idx, $second)) {
            break;
        }

        $right = $second[$idx];

        $result = is_order_correct($left, $right, $verbose, $nesting . '  ');
        if ($result !== 0) {
            vecho($verbose, $nesting . "  - [val] returning $result\n");
            return $result;
        }
    }

    $result = count($second) - count($first);
    vecho($verbose, $nesting . "  - [len] returning $result\n");

    return $result;
}

function parse_packets(string $filename)
{
    $lines = file($filename);

    $packets = [];
    $packet = [];

    foreach ($lines as $line) {
        $data = trim($line);
        if (empty($data)) {
            $packets[] = $packet;
            $packet = [];
        } else {
            $packet[] = json_decode($data);
        }
    }

    $packets[] = $packet;

    return $packets;
}

function part1(string $filename, bool $verbose)
{
    $packets = parse_packets($filename);

    $correct_packets = [];
    foreach ($packets as $idx => $p) {
        $result = is_order_correct($p[0], $p[1], $verbose);

        vecho($verbose, "\nPair " . ($idx + 1) . " final result: $result\n\n");

        if ($result > 0) {
            $correct_packets[] = $idx + 1;
        }
    }

    return array_sum($correct_packets);
}

function unwrap_packets(array $packets)
{
    foreach ($packets as $p) {
        yield $p[0];
        yield $p[1];
    }
}

function part2(string $filename, bool $verbose)
{
    $lines = file($filename);
    $packets = [];

    foreach ($lines as $line) {
        $data = trim($line);
        if (empty($data)) {
            continue;
        }

        $packets[] = json_decode($data);
    }

    $packets[] = json_decode('[[2]]');
    $packets[] = json_decode('[[6]]');

    usort($packets, 'is_order_correct');
    $sorted = array_reverse($packets);

    if ($verbose) {
        echo "===[sorted]===\n";
    }

    $multiply = [];

    foreach ($sorted as $idx => $pckt) {
        $val = json_encode($pckt);
        if ($verbose) {
            echo "packet $idx: $val\n";
        }

        if ($val === '[[2]]' || $val === '[[6]]') {
            $multiply[] = $idx + 1;
        }
    }

    echo 'Final divider packet indices: ' . json_encode($multiply) . "\n";

    return array_product($multiply);
}

run_part1('example', true, 13);
run_part1('input');
echo "\n";

run_part2('example', true, 140);
run_part2('input');
echo "\n";

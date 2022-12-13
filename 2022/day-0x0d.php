<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

/**
 * @return integer:
 *   - positive integer means the order is correct
 *   - negative integer means the order is incorrect
 *   - zero means both values are equal
 */
function is_order_correct($first, $second): int
{
    if (is_numeric($first) && is_numeric($second)) {
        return $second - $first;
    }

    $first = as_array($first);
    $second = as_array($second);

    foreach ($first as $idx => $left) {
        if (!array_key_exists($idx, $second)) {
            break;
        }

        $result = is_order_correct($left, $second[$idx]);
        if ($result !== 0) {
            return $result;
        }
    }

    return count($second) - count($first);
}

function parse_packets(string $filename)
{
    foreach (file($filename) as $line) {
        $data = trim($line);
        if (!empty($data)) {
            yield json_decode($data);
        }
    }
}

function part1(string $filename, bool $verbose)
{
    $packets = parse_packets($filename);
    $pairs = array_chunk(iterator_to_array($packets), 2);

    $correct_packets = [];
    foreach ($pairs as $idx => $p) {
        if (is_order_correct($p[0], $p[1], $verbose) > 0) {
            $correct_packets[] = $idx + 1;
        }
    }

    return array_sum($correct_packets);
}

function part2(string $filename, bool $verbose)
{
    $packets = iterator_to_array(parse_packets($filename));

    $packets[] = json_decode('[[2]]');
    $packets[] = json_decode('[[6]]');

    usort($packets, 'is_order_correct');
    $sorted = array_reverse($packets);

    $multiply = [];

    foreach ($sorted as $idx => $pckt) {
        $val = json_encode($pckt);
        if ($val === '[[2]]' || $val === '[[6]]') {
            $multiply[] = $idx + 1;
        }
    }

    return array_product($multiply);
}

run_part1('example', true, 13);
run_part1('input', false);
echo "\n";

run_part2('example', true, 140);
run_part2('input', false);
echo "\n";

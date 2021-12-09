<?php

function low_point($map, $x, $y)
{
    $val = $map[$y][$x];

    $ret = [
        $map[$y - 1][$x] ?? 99,
        $map[$y + 1][$x] ?? 99,
        $map[$y][$x - 1] ?? 99,
        $map[$y][$x + 1] ?? 99,
    ];

    if ($val < $ret[0] && $val < $ret[1] && $val < $ret[2] && $val < $ret[3]) {
        return $val;
    } else {
        return null;
    }
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $map = [];
    foreach ($lines as $line) {
        $map[] = str_split($line);
    }

    $total = 0;
    foreach ($map as $y => $row) {
        foreach ($row as $x => $point) {
            $risk_score = low_point($map, $x, $y);
            if ($risk_score !== null) {
                $total += 1 + $risk_score;
            }
        }
    }

    return $total;
}

$expected = 15;
$actual = run('star-17-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-17-input.txt');

echo "The puzzle answer is:  $output\n";

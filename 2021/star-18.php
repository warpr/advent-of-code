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

function display_map($map)
{
    foreach ($map as $row) {
        foreach ($row as $val) {
            if ($val == 9) {
                echo chr(27) . '[1;30m' . $val . chr(27) . '[0m';
            } else {
                echo $val;
            }
        }
        echo "\n";
    }
}

function basin_size(&$map, $x, $y)
{
    if ($map[$y][$x] == 9) {
        return 0;
    }

    $size = 1;
    $map[$y][$x] = 9;

    $neighbours = [[$x, $y - 1], [$x, $y + 1], [$x - 1, $y], [$x + 1, $y]];

    foreach ($neighbours as $pos) {
        if ($map[$pos[1]][$pos[0]] ?? 99 < 9) {
            $size += basin_size($map, $pos[0], $pos[1]);
        }
    }

    return $size;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $map = [];
    foreach ($lines as $line) {
        $map[] = str_split($line);
    }

    $sizes = [];
    foreach ($map as $y => $row) {
        foreach ($row as $x => $point) {
            $risk_score = low_point($map, $x, $y);
            if ($risk_score !== null) {
                $size = basin_size($map, $x, $y);
                echo "Size of basin at ($x, $y) is " . $size . "\n";
                $sizes[] = $size;
                if ($verbose) {
                    display_map($map);
                }
            }
        }
    }

    sort($sizes);

    return array_pop($sizes) * array_pop($sizes) * array_pop($sizes);
}

$expected = 1134;
$actual = run('star-17-example.txt', true);
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-17-input.txt');

echo "The puzzle answer is:  $output\n";

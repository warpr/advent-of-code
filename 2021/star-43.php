<?php

function parse_line($line)
{
    if (
        preg_match(
            '/([a-z]+) x=([0-9-]+)\.\.([0-9-]+),y=([0-9-]+)\.\.([0-9-]+),z=([0-9-]+)\.\.([0-9-]+)/',
            $line,
            $matches
        )
    ) {
        return [
            'cmd' => $matches[1],
            'start' => [
                'x' => $matches[2],
                'y' => $matches[4],
                'z' => $matches[6],
            ],
            'end' => [
                'x' => $matches[3],
                'y' => $matches[5],
                'z' => $matches[7],
            ],
        ];
    }

    return null;
}

function apply(&$cube, $do)
{
    $startx = max(-50, $do['start']['x']);
    $starty = max(-50, $do['start']['y']);
    $startz = max(-50, $do['start']['z']);
    $endx = min(50, $do['end']['x']);
    $endy = min(50, $do['end']['y']);
    $endz = min(50, $do['end']['z']);
    $on = $do['cmd'] === 'on';
    /*
    print_r(compact(
        'startx', 'starty', 'startz',
            'endx', 'endy', 'endz', 'on'));
*/
    for ($i = $startx; $i <= $endx; $i++) {
        for ($j = $starty; $j <= $endy; $j++) {
            for ($k = $startz; $k <= $endz; $k++) {
                $cube[$i][$j][$k] = $on;
            }
        }
    }
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $cube = [];
    $line_count = count($lines);
    foreach ($lines as $idx => $line) {
        if ($verbose) {
            echo 'Applying instruction ' . ($idx + 1) . " of $line_count\n";
        }
        $do = parse_line($line);
        apply($cube, $do);
    }

    $count = 0;
    foreach ($cube as $x) {
        foreach ($x as $y) {
            foreach ($y as $z) {
                if ($z) {
                    $count++;
                }
            }
        }
    }

    return $count;
}

function main($filename, $verbose = null, $expected = null)
{
    $actual = run($filename, $verbose);
    if ($expected) {
        if ($actual !== $expected) {
            echo "You broke $filename, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

main('star-43-example.txt', true, 590784);
main('star-43-input.txt', false);

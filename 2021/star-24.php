<?php

function is_small_cave($cave)
{
    return preg_match("/^[a-z]+$/", $cave);
}

function have_i_visited_a_small_cave_twice($path)
{
    $counts = [];
    foreach ($path as $cave) {
        if (is_small_cave($cave)) {
            $counts[$cave]++;
            if ($counts[$cave] > 1) {
                return true;
            }
        }
    }
    return false;
}

function paths($connections, $p, $verbose)
{
    $start_cave = end($p);

    if ($verbose) {
        echo 'Looking for more paths: ' . implode(' ', $p) . "\n";
    }

    $ret = [];
    foreach ($connections[$start_cave] as $cave) {
        if ($verbose) {
            echo " -> branch $cave ... ";
        }
        $next_path = $p;
        $next_path[] = $cave;
        if ($cave === 'end') {
            if ($verbose) {
                echo 'Concluding path: ' . implode(' ', $next_path) . "\n";
            }
            $ret[] = $next_path;
            continue;
        }

        if (is_small_cave($cave) && in_array($cave, $p) && have_i_visited_a_small_cave_twice($p)) {
            if ($verbose) {
                echo "Skipping small cave $cave: " . implode(' ', $p) . "\n";
            }
            continue;
        }

        if ($cave === 'start') {
            if ($verbose) {
                echo 'Skipping start: ' . implode(' ', $next_path) . "\n";
            }
            continue;
        }

        foreach (paths($connections, $next_path, $verbose) as $sub_path) {
            $ret[] = $sub_path;
        }
    }

    return $ret;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $connections = [];

    foreach ($lines as $line) {
        list($a, $b) = explode('-', $line);

        $connections[$a][] = $b;
        $connections[$b][] = $a;
    }

    $paths = paths($connections, ['start'], $verbose);
    foreach ($paths as $idx => $p) {
        if ($verbose) {
            echo 'Path ' . ($idx + 1) . ': ' . implode(',', $p) . "\n";
        }
    }

    return count($paths);
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

main('star-23-example.txt', true, 36);
main('star-23-example-2.txt', false, 103);
main('star-23-example-3.txt', false, 3509);
main('star-23-input.txt');

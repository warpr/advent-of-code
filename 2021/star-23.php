<?php

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

        if (preg_match("/^[a-z]+$/", $cave) && in_array($cave, $p)) {
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

main('star-23-example.txt', true, 10);
main('star-23-example-2.txt', false, 19);
main('star-23-example-3.txt', false, 226);
main('star-23-input.txt');

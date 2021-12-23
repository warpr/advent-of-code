<?php

function explode_pair_at($key, $input)
{
    $before_idx = null;
    $after_idx = null;

    for ($i = $key - 1; $i >= 0; $i--) {
        if (is_numeric($input[$i])) {
            $before_idx = $i;
            break;
        }
    }

    $size = count($input);
    for ($i = $key + 3; $i < $size; $i++) {
        if (is_numeric($input[$i])) {
            $after_idx = $i;
            break;
        }
    }

    if ($before_idx !== null) {
        $input[$before_idx] += $input[$key];
    }

    if ($after_idx !== null) {
        if (!is_numeric($input[$after_idx])) {
            echo "Pos $after_idx is not numeric: " . $input[$after_idx] . "\n";
            echo 'Input: ' . implode('', $input) . "\n";
            die();
        }
        if (!is_numeric($input[$key + 2])) {
            echo "Pos $key + 3 is not numeric: " . $input[$key + 2] . "\n";
            echo 'Input: ' . implode('', $input) . "\n";
            die();
        }

        $input[$after_idx] += $input[$key + 2];
    }

    array_splice($input, $key - 1, 5, '0');

    return $input;
}

function explode_pairs($input)
{
    $nested = 0;
    foreach ($input as $key => $val) {
        if ($val === '[') {
            $nested++;
        } elseif ($val === ']') {
            $nested--;
        } elseif (is_numeric($val)) {
            // cannot explode pair if the second element is also a pair?
            if (is_numeric($input[$key + 2]) && $nested > 4) {
                return explode_pair_at($key, $input);
            }
        }
    }

    return $input;
}

function create_pair_at($key, $input)
{
    $a = floor($input[$key] / 2);
    $b = ceil($input[$key] / 2);
    $pair = ['[', $a, ',', $b, ']'];

    array_splice($input, $key, 1, $pair);
    return $input;
}

function split_large_numbers($input)
{
    foreach ($input as $key => $val) {
        if (is_numeric($val) && $val >= 10) {
            return create_pair_at($key, $input);
        }
    }

    return $input;
}

function reduce_pairs($tokens)
{
    $prev = null;
    $current = implode('', $tokens);
    $steps = 0;
    //    printf(" %03d Befor> $current\n", $steps);
    do {
        $steps++;
        $prev = $current;
        $tokens = explode_pairs($tokens);
        $tmp = implode('', $tokens);
        if ($tmp === $prev) {
            //            printf(" %03d Boom!> $tmp\n", $steps);

            // only split if there is nothing more to explode

            $tokens = split_large_numbers($tokens);
            $current = implode('', $tokens);
            if ($tmp !== $current) {
                //                printf(" %03d Split> $current\n", $steps);
            }
        } else {
            $current = $tmp;
        }
    } while ($prev !== $current);

    //    printf(" %03d After> $current\n", $steps);

    return $tokens;
}

function addition($a, $b)
{
    return array_merge(['['], $a, [','], $b, [']']);
}

function t($str)
{
    return str_split($str, 1);
}

function magnitude_pair($tokens)
{
    foreach ($tokens as $key => $val) {
        if (!is_numeric($val)) {
            continue;
        }

        if (is_numeric($tokens[$key + 2])) {
            $here = 3 * $val + 2 * $tokens[$key + 2];
            array_splice($tokens, $key - 1, 5, $here);
            return $tokens;
        }
    }

    return $tokens;
}

function magnitude($tokens)
{
    $prev = null;
    $current = implode('', $tokens);

    do {
        $prev = $current;
        $tokens = magnitude_pair($tokens);
        $current = implode('', $tokens);
    } while ($prev !== $current);

    if (count($tokens) !== 1) {
        echo "Failed to determine magnitude for input\n";
        die();
    }

    return (int) $tokens[0];
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));
    $copy = $lines;

    $max_mag = 0;
    foreach ($lines as $lineno_a => $a) {
        foreach ($copy as $lineno_b => $b) {
            $tokens = reduce_pairs(addition(t($a), t($b)));
            $mag = magnitude($tokens);
            if ($mag > $max_mag) {
                echo "Highest so far is line $lineno_a + line $lineno_b.\n";
                $max_mag = $mag;
            }
        }
    }

    echo "Largest magnitude is $max_mag\n";
    return $max_mag;
}

function main($str, $verbose = null, $expected = null)
{
    $actual = run($str, $verbose);
    if ($expected !== null) {
        if ($actual !== $expected) {
            echo "You broke $str, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

main('star-35-example.txt', true, 3993);
main('star-35-input.txt', false);

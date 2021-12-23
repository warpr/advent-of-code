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

function reduce_test($input, $expected)
{
    $reduced = reduce_pairs(t($input));

    $actual = implode('', $reduced);

    if ($actual !== $expected) {
        echo "Reduce test failed, expected: $expected, actual: $actual.\n";
        die();
    } else {
        echo "Reduced $input to $actual\n";
    }
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

function magnitude_test($input, $expected)
{
    $actual = magnitude($input);

    if ($actual !== $expected) {
        echo "Magnitude test failed, expected: $expected, actual: $actual.\n";
        die();
    } else {
        echo "Magnitude is good at $actual\n";
    }
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $tokens = t(array_shift($lines));
    foreach ($lines as $line) {
        //        echo "  " . implode("", $tokens) . "\n";
        //        echo "+ " . implode("", t($line)) . "\n";
        $tokens = reduce_pairs(addition($tokens, t($line)));
        //        echo "= " . implode("", $tokens) . "\n";
    }

    echo "\nFinal sum for $filename:\n  " . implode('', $tokens) . "\n\n";
    return magnitude($tokens);
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

/*
// (the 9 has no regular number to its left, so it is not added to any regular number).
reduce_test('[[[[[9,8],1],2],3],4]', '[[[[0,9],2],3],4]');
// (the 2 has no regular number to its right, and so it is not added to any regular number).
reduce_test('[7,[6,[5,[4,[3,2]]]]]', '[7,[6,[5,[7,0]]]]');
reduce_test('[[6,[5,[4,[3,2]]]],1]', '[[6,[5,[7,0]]],3]');
reduce_test('[[3,[2,[1,[7,3]]]],[6,[5,[4,[3,2]]]]]', '[[3,[2,[8,0]]],[9,[5,[7,0]]]]');

$addition_test = addition(t('[[[[4,3],4],4],[7,[[8,4],9]]]'), t('[1,1]'));
reduce_test(implode("", $addition_test), '[[[[0,7],4],[[7,8],[6,0]]],[8,1]]');
*/

magnitude_test(t('[9,1]'), 29);
magnitude_test(t('[[9,1],[1,9]]'), 129);
magnitude_test(t('[[1,2],[[3,4],5]]'), 143);
magnitude_test(t('[[[[0,7],4],[[7,8],[6,0]]],[8,1]]'), 1384);
magnitude_test(t('[[[[1,1],[2,2]],[3,3]],[4,4]]'), 445);
magnitude_test(t('[[[[3,0],[5,3]],[4,4]],[5,5]]'), 791);
magnitude_test(t('[[[[5,0],[7,4]],[5,5]],[6,6]]'), 1137);
magnitude_test(t('[[[[8,7],[7,7]],[[8,6],[7,7]]],[[[0,7],[6,6]],[8,7]]]'), 3488);

main('star-35-example.txt', true, 4140);
main('star-35-input.txt', false);

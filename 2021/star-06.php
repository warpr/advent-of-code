<?php

function oxygen_criteria($numbers, $bitpos)
{
    $ones = 0;
    $zeroes = 0;

    $group_ones = [];
    $group_zeroes = [];

    foreach ($numbers as $n) {
        if ($n[$bitpos]) {
            $group_ones[] = $n;
            $ones++;
        } else {
            $group_zeroes[] = $n;
            $zeroes++;
        }
    }

    return $zeroes > $ones ? $group_zeroes : $group_ones;
}

function co2_scrubber($numbers, $bitpos)
{
    $ones = 0;
    $zeroes = 0;

    $group_ones = [];
    $group_zeroes = [];

    foreach ($numbers as $n) {
        if ($n[$bitpos]) {
            $group_ones[] = $n;
            $ones++;
        } else {
            $group_zeroes[] = $n;
            $zeroes++;
        }
    }

    return $zeroes <= $ones ? $group_zeroes : $group_ones;
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $numbers = [];
    foreach ($lines as $line) {
        $numbers[] = str_split($line);
    }

    $oxygen_numbers = $numbers;
    $co2_numbers = $numbers;

    $final_oxygen = null;
    $final_co2 = null;

    $first_line = $numbers[0];
    foreach ($first_line as $pos => $unused) {
        $oxygen_numbers = oxygen_criteria($oxygen_numbers, $pos);
        if (count($oxygen_numbers) === 1) {
            $final_oxygen = array_pop($oxygen_numbers);
        }

        $co2_numbers = co2_scrubber($co2_numbers, $pos);
        if (count($co2_numbers) === 1) {
            $final_co2 = array_pop($co2_numbers);
        }
    }

    $int_oxygen = bindec(implode('', $final_oxygen));
    $int_co2 = bindec(implode('', $final_co2));

    // print_r(compact('final_oxygen', 'final_co2', 'int_oxygen', 'int_co2'));

    return $int_oxygen * $int_co2;
}

$result = run('star-05-example.txt');
if ($result !== 230) {
    echo "You broke the example.\n";
    die();
}

$output = run('star-05-input.txt');

echo "The answer is:  $output\n";

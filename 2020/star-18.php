<?php

function validate_item($preamble, $item) {
    foreach ($preamble as $x) {
        foreach ($preamble as $y) {
            if ($x == $y) {
                continue;
            }

            if ($x + $y == $item) {
                // echo sprintf("%6d is valid (%d + %d)\n", $item, $x, $y);
                return true;
            }
        }
    }
    return false;
}

function validate($preamble, $rest) {
    foreach ($rest as $item) {
        if (!validate_item($preamble, $item)) {
            return $item;
        }

        array_shift($preamble);
        $preamble[] = $item;
    }

    return null;
}

function find_contiguous($lines, $size) {
    $max = count($lines);

    for ($i = 0; $i < $max - $size; $i++) {
        yield array_slice($lines, $i, $size);
    }
}

function sum_smallest_and_largest($set) {
    $smallest = $set[0];
    $largest = $set[0];
    foreach ($set as $number) {
        if ($number < $smallest) {
            $smallest = $number;
        }

        if ($number > $largest) {
            $largest = $number;
        }
    }

    return $smallest + $largest;
}

function main($filename, $preamble_size) {
    $input = file($filename);
    $lines = [];
    foreach ($input as $line) {
        $lines[] = (int) trim($line);
    }

    echo "Processing $filename\n";

    $preamble = array_slice($lines, 0, $preamble_size);
    $rest = array_slice($lines, $preamble_size);
    $invalid = validate($preamble, $rest);
    echo sprintf("- %d is the first invalid number\n", $invalid);

    for ($size = 2; $size < count($lines); $size++) {
        foreach (find_contiguous($lines, $size) as $set) {
            if (array_sum($set) == $invalid) {
                echo "- found the set: [" . implode(", ", $set) . "]";
                echo ", answer is " . sum_smallest_and_largest($set) . "\n\n";
                return;
            }
        }
    }

    echo "- no aswer found\n";
}

main('star-17-example.txt', 5);
main('star-17-input.txt', 25);

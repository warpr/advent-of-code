<?php

function validate_item($preamble, $item) {
    foreach ($preamble as $x) {
        foreach ($preamble as $y) {
            if ($x == $y) {
                continue;
            }

            if ($x + $y == $item) {
                echo sprintf("%6d is valid (%d + %d)\n", $item, $x, $y);
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

function main($filename, $preamble_size) {
    $input = file($filename);
    $lines = [];
    foreach ($input as $line) {
        $lines[] = (int) trim($line);
    }

    $preamble = array_slice($lines, 0, $preamble_size);
    $rest = array_slice($lines, $preamble_size);
    $invalid = validate($preamble, $rest);
    echo sprintf("%6d is the first invalid number\n", $invalid);
}

main('star-17-example.txt', 5);
main('star-17-input.txt', 25);

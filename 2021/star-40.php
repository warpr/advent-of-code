<?php

function count_pixels($image)
{
    $ret = 0;
    foreach ($image as $y => $row) {
        foreach ($row as $x => $pixel) {
            if ($pixel === '#') {
                $ret++;
            }
        }
    }

    return $ret;
}

function pixel_bits_to_int($bits)
{
    $number = '';

    foreach ($bits as $val) {
        $number .= $val === '.' ? '0' : '1';
    }

    $ret = bindec($number);
    // echo "pixel bits: " . implode("", $bits) . " = $ret\n";
    return $ret;
}

function enhance(&$algo, $image, $default)
{
    $ret = [];

    foreach ($image as $y => $row) {
        foreach ($row as $x => $pixel) {
            $pixel_val = pixel_bits_to_int([
                $image[$y - 1][$x - 1] ?? $default,
                $image[$y - 1][$x] ?? $default,
                $image[$y - 1][$x + 1] ?? $default,
                $image[$y][$x - 1] ?? $default,
                $image[$y][$x] ?? $default,
                $image[$y][$x + 1] ?? $default,
                $image[$y + 1][$x - 1] ?? $default,
                $image[$y + 1][$x] ?? $default,
                $image[$y + 1][$x + 1] ?? $default,
            ]);

            $ret[$y][$x] = $algo[$pixel_val] ?? $default;
        }
    }

    return $ret;
}

function remove_border(&$image, $padding)
{
    for ($i = 0; $i < $padding; $i++) {
        array_shift($image);
        array_pop($image);
    }

    $ret = [];
    foreach ($image as $row) {
        for ($i = 0; $i < $padding; $i++) {
            array_shift($row);
            array_pop($row);
        }

        $ret[] = $row;
    }

    return $ret;
}

function add_border(&$image, $padding, $default)
{
    $ret = [];

    $row_length = count($image[0]) + $padding * 2;
    $empty = str_split(str_repeat($default, $row_length), 1);

    for ($i = 0; $i < $padding; $i++) {
        $ret[] = $empty;
    }

    foreach ($image as $row) {
        $pad = str_split(str_repeat($default, $padding), 1);
        $ret[] = array_merge($pad, $row, $pad);
    }

    for ($i = 0; $i < $padding; $i++) {
        $ret[] = $empty;
    }

    return $ret;
}

function display_image(&$image)
{
    foreach ($image as $row) {
        echo implode('', $row) . "\n";
    }
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $algo = array_shift($lines);
    $empty = array_shift($lines);

    $image = [];
    foreach ($lines as $line) {
        $image[] = str_split($line, 1);
    }

    $image = add_border($image, 300, '.');
    if ($verbose) {
        display_image($image);
    }

    for ($i = 0; $i < 50; $i++) {
        $default = '.';
        if ($step % 2 && $algo[0] == '#') {
            // previous step has inverted the
            // infinite pixels from '.' to '#'
            $default = '#';
        }

        echo "Step $i, enhancing (infinite is $default)...\n";
        $image = enhance($algo, $image, $default);

        // weird stuff happens at the border, so
        // just remove it
        $image = remove_border($image, 4);

        if ($verbose) {
            display_image($image);
        }
    }

    if ($verbose) {
        display_image($image);
    }

    return count_pixels($image);
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

main('star-39-example.txt', true, 3351);
main('star-39-input.txt', false);

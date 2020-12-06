<?php

// FBFBBFFRLR reveals that it is the seat at row 44, column 5.

// Every seat also has a unique seat ID: multiply the row by 8, then add the column. In this example, the seat has ID 44 * 8 + 5 = .

//Here are some other boarding passes:

    /*
FBFBBFFRLR; row 44, column 5, seat ID 357.
BFFFBBFRRR: row 70, column 7, seat ID 567.
FFFBBBFRRR: row 14, column 7, seat ID 119.
BBFFBBFRLL: row 102, column 4, seat ID 820.
     */

echo "------------------\n";

$codes = [
    'FBFBBFFRLR',
    'BFFFBBFRRR',
    'FFFBBBFRRR',
    'BBFFBBFRLL',
];

function decode_binary($str, $zero, $one) {
    $str = str_replace($zero, "0", $str);
    $str = str_replace($one, "1", $str);
    return bindec($str);
}

function decode_boarding_pass($code) {
    if (preg_match('/^([FB]{7})([RL]{3})$/', trim($code), $matches)) {
        $row = decode_binary($matches[1], "F", "B");
        $col = decode_binary($matches[2], "L", "R");
        $id = $row * 8 + $col;
        return compact('row', 'col', 'id');
    }

    return [];
}

foreach ($codes as $code) {
    $seat = decode_boarding_pass($code);

    echo "$code: row {$seat['row']}, column {$seat['col']}, seat ID {$seat['id']}.\n";
}

$lines = file('star-09-input.txt');
$max_id = 0;
foreach ($lines as $line) {
    $seat = decode_boarding_pass($line);
    echo "$code: row {$seat['row']}, column {$seat['col']}, seat ID {$seat['id']}.\n";
    if ($seat['id'] > $max_id) {
        $max_id = $seat['id'];
    }
}

echo "Highest Seat ID: $max_id\n";

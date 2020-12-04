<?php

function move($pos) {
    $pos[0] += 3;
    $pos[1] += 1;
    return $pos;
}

function item_at_pos($lines, $pos) {
    list($x, $y) = $pos;
    $x_pos = $x % strlen($lines[$y]);
    return $lines[$y][$x_pos];
}

function pos_context($pos, $lines) {
    list($x, $y) = $pos;
    $line = $lines[$y];
    $x_pos = $x % strlen($line);

    $before = substr($line, 0, $x_pos);
    $after = substr($line, $x_pos);

    if ($after[0] === '.') {
        $after[0] = 'O';
    } else {
        $after[0] = 'X';
    }

    return $before . $after;
}

function pos_log($pos, $msg, $context) {
    list($x, $y) = $pos;
    echo str_pad("($x, $y)", 10) . " => $msg   [ $context ]\n";
}

function test_slope($filename, $move) {
    $lines = [];
    foreach (file($filename) as $line) {
        $lines[] = trim($line);
    }

    // echo "---------------------------------\n";
    $pos = $move([0, 0]);
    $trees = 0;
    while (!empty($lines[$pos[1]])) {
        // pos_log($pos, item_at_pos($lines, $pos), pos_context($pos, $lines));
        if (item_at_pos($lines, $pos) == '#') {
            $trees++;
        }
        $pos = $move($pos);
    }

    // echo "---------------------------------\n";
    echo "Total trees encountered: $trees\n";
    return $trees;
}

function main($filename) {
    $slopes = [
        function ($pos) { $pos[0] += 1; $pos[1] += 1; return $pos; },
        function ($pos) { $pos[0] += 3; $pos[1] += 1; return $pos; },
        function ($pos) { $pos[0] += 5; $pos[1] += 1; return $pos; },
        function ($pos) { $pos[0] += 7; $pos[1] += 1; return $pos; },
        function ($pos) { $pos[0] += 1; $pos[1] += 2; return $pos; },
    ];

    $trees = [];
    foreach ($slopes as $slope) {
        $trees[] = test_slope($filename, $slope);
    }

    echo "Test slopes for: $filename";
    $result = array_reduce($trees, function ($memo, $item) {
        return $memo * $item;
    }, 1);
    echo ", [" . implode(",", $trees) . "] -- final result: $result\n";
}

main('star-05-example.txt');
main('star-05-input.txt');

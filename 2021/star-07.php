<?php

function all_columns($board)
{
    $columns = [];

    foreach ($board as $row) {
        foreach ($row as $idx => $val) {
            $columns[$idx][] = $val;
        }
    }

    return $columns;
}

function all_found($values)
{
    foreach ($values as $val) {
        if (!$val['found']) {
            return false;
        }
    }

    return true;
}

function has_won($board)
{
    foreach (all_columns($board) as $col) {
        if (all_found($col)) {
            return true;
        }
    }

    foreach ($board as $row) {
        if (all_found($row)) {
            return true;
        }
    }

    return false;
}

function add_draw(&$board, $draw)
{
    foreach ($board as &$line) {
        foreach ($line as &$pos) {
            if ($pos['val'] == $draw) {
                $pos['found'] = true;
            }
        }
    }
}

function gather_boards($lines)
{
    $board = [];

    foreach ($lines as $line) {
        if (empty($line)) {
            if (!empty($board)) {
                yield $board;
                $board = [];
            }
            continue;
        }

        $row = [];
        foreach (explode(' ', $line) as $part) {
            $trimmed = trim($part);
            if ($trimmed !== '') {
                $row[] = ['found' => false, 'val' => $trimmed];
            }
        }
        $board[] = $row;
    }

    if (!empty($board)) {
        yield $board;
    }
}

function bold($str)
{
    echo chr(27) . '[1m' . $str . chr(27) . '[0m';
}

function display_board($board)
{
    foreach ($board as $line) {
        foreach ($line as $pos) {
            $num = $pos['val'];
            if ($pos['found']) {
                echo bold(sprintf('%3d ', $num));
            } else {
                echo sprintf('%3d ', $num);
            }
        }
        echo "\n";
    }
    echo "\n";
}

function sum_remaining($board)
{
    $ret = 0;

    foreach ($board as $row) {
        foreach ($row as $pos) {
            if (!$pos['found']) {
                $ret += $pos['val'];
            }
        }
    }

    return $ret;
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $drawn = explode(',', array_shift($lines));

    $boards = iterator_to_array(gather_boards($lines));

    echo "\nThe game starts\n";

    foreach ($drawn as $draw_no => $draw) {
        printf("[Draw %2d]   The number is %d.\n", $draw_no, $draw);

        foreach ($boards as $board_no => &$board) {
            add_draw($board, $draw);

            if (has_won($board)) {
                echo "\n";
                display_board($board);
                return $draw * sum_remaining($board);
            }
        }
    }

    echo "No board has one\n";
    return 0;
}

$expected = 4512;
$actual = run('star-07-example.txt');
if ($actual !== $expected) {
    echo 'You broke the example, ' . "expected: $expected, actual: $actual.\n";
    die();
} else {
    echo "Example answer OK: $actual\n";
}

$output = run('star-07-input.txt');

echo "The puzzle answer is:  $output\n";

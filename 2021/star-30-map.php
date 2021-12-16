<?php

function grid_size(&$grid) {
    return [ count($grid[0]), count($grid) ];
}

function wrap_incr($val) {
    if (++$val > 9) {
        return $val - 9;
    } else {
        return $val;
    }
}

function render_grid(&$grid) {
    $ret = [];
    foreach ($grid as $y => $row) {
        $ret[] = implode("", $row) . "\n";
    }

    return implode("", $ret);
}

function main($src, $dst)
{
    $lines = array_map('trim', file($src));

    $grid = [];
    foreach ($lines as $line) {
        if (empty($line)) {
            continue;
        }
        $grid[] = str_split($line);
    }

    list($size_x, $size_y) = grid_size($grid);

    // copy right
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            $x_copy = $x;
            foreach ([1,2,3,4] as $rep) {
                $val = wrap_incr($val);
                $x_copy += $size_x;
                $grid[$y][$x_copy] = $val;
            }
        }

        ksort($grid[$y]);
    }

    // copy down
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $val) {
            $y_copy = $y;
            foreach ([1,2,3,4] as $rep) {
                $val = wrap_incr($val);
                $y_copy += $size_y;
                $grid[$y_copy][$x] = $val;
            }
        }
    }

    ksort($grid);

    file_put_contents($dst, render_grid($grid));
}

main('star-29-example.txt', 'star-30-example.txt');
main('star-29-input.txt', 'star-30-input.txt');


<?php

function neighbour_coords($x, $y, $z) {
    $xarr = [ $x - 1, $x, $x + 1];
    $yarr = [ $y - 1, $y, $y + 1];
    $zarr = [ $z - 1, $z, $z + 1];
    foreach ($xarr as $nx) {
        foreach ($yarr as $ny) {
            foreach ($zarr as $nz) {
                if ($nx === $x && $ny === $y && $nz === $z) {
                    continue;
                }

                yield [ $nx, $ny, $nz ];
            }
        }
    }
}

function get_neighbours(&$grid, $x, $y, $z) {
    $ret = [];

    foreach (neighbour_coords($x, $y, $z) as $n) {
        list($nx, $ny, $nz) = $n;
        $ret[] = [ $nx, $ny, $nz, $grid[$nz][$ny][$nx] ?? '.' ];
    }

    return $ret;
}

function cycle($cycle, $grid) {
    $inactive_cells = [];

    $sparse = [];
    foreach ($grid as $z => &$plane) {
        foreach ($plane as $y => &$row) {
            foreach ($row as $x => $val) {
                if ($val === '#') {
                    $neighbours = get_neighbours($grid, $x, $y, $z);
                    $active_count = 0;
                    foreach ($neighbours as $n) {
                        if ($n[3] === '.') {
                            $inactive_cells[sprintf("%d,%d,%d", $n[0], $n[1], $n[2])] = true;
                        } else {
                            $active_count++;
                        }
                    }
                    if ($active_count === 2 || $active_count === 3) {
                        // cube remains active
                        $sparse[$z][$y][$x] = '#';
                    }
                }
            }
        }
    }

    foreach ($inactive_cells as $cell => $unused) {
        list($x, $y, $z) = explode(",", $cell);
        $neighbours = get_neighbours($grid, $x, $y, $z);
        $active_count = 0;
        foreach ($neighbours as $n) {
            if ($n[3] === '#') {
                $active_count++;
            }
        }
        // echo "[Cycle $cycle] Active neighbours ($x, $y, $z) is $active_count\n";
        if ($active_count === 3) {
            // cube becomes active
            $sparse[$z][$y][$x] = '#';
        }
    }

    return $sparse;
}

function display_grid($cycle, $grid) {
    $total_active = 0;

    ksort($grid);
    foreach ($grid as $z => &$plane) {
//        echo "[Cycle $cycle] Layer Z = $z\n";
        ksort($plane);
        foreach ($plane as $y => &$row) {
            ksort($row);
//            printf("%3d.\t", $y);
            foreach ($row as $x => $val) {
//                printf("%3d[%s] ", $x, $val);
                if ($val === '#') {
                    $total_active++;
                }
            }
//            echo "\n";
        }
    }

    echo "[Cycle $cycle] Total active cubes: $total_active\n";
}

function main($filename) {
    $lines = file($filename);

    echo "----[$filename]----\n";

    $grid = [ [] ];
    foreach ($lines as $line) {
        $grid[0][] = str_split(trim($line));
    }

    display_grid(0, $grid);

    for($cycle = 1; $cycle <= 6; $cycle++) {
        $grid = cycle($cycle, $grid);
        display_grid($cycle, $grid);
    }
}

main('star-33-example.txt');
main('star-33-input.txt');

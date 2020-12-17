<?php

function neighbour_coords($x, $y, $z, $w) {
    $xarr = [ $x - 1, $x, $x + 1];
    $yarr = [ $y - 1, $y, $y + 1];
    $zarr = [ $z - 1, $z, $z + 1];
    $warr = [ $w - 1, $w, $w + 1];
    foreach ($xarr as $nx) {
        foreach ($yarr as $ny) {
            foreach ($zarr as $nz) {
                foreach ($warr as $nw) {
                    if ($nx === $x && $ny === $y && $nz === $z && $nw === $w) {
                        continue;
                    }

                    yield [ $nx, $ny, $nz, $nw ];
                }
            }
        }
    }
}

function get_neighbours(&$grid, $x, $y, $z, $w) {
    $ret = [];

    foreach (neighbour_coords($x, $y, $z, $w) as $n) {
        list($nx, $ny, $nz, $nw) = $n;
        $ret[] = [ $nx, $ny, $nz, $nw, $grid[$nw][$nz][$ny][$nx] ?? '.' ];
    }

    /* if ($x == 0 && $y == 0 && $z == 0 && $w == 0) {
     *     foreach ($ret as $n) {
     *         printf("(%2d, %2d, %2d, %2d) => (%2d, %2d, %2d, %2d) is %s\n",
     *                $x, $y, $z, $w, $n[0], $n[1], $n[2], $n[3], $n[4]);
     *     }
     * }
     */
    return $ret;
}

function cycle($cycle, $grid) {
    $inactive_cells = [];

    $sparse = [];
    foreach ($grid as $w => &$cube) {
        foreach ($cube as $z => &$plane) {
            foreach ($plane as $y => &$row) {
                foreach ($row as $x => $val) {
                    if ($val === '#') {
                        $neighbours = get_neighbours($grid, $x, $y, $z, $w);
                        $active_count = 0;
                        foreach ($neighbours as $n) {
                            if ($n[4] === '.') {
                                $inactive_cells[sprintf("%d,%d,%d,%d", $n[0], $n[1], $n[2], $n[3])] = true;
                            } else {
                                $active_count++;
                            }
                        }
                        if ($active_count === 2 || $active_count === 3) {
                            // cube remains active
                            $sparse[$w][$z][$y][$x] = '#';
                        }
                    }
                }
            }
        }
    }

    foreach ($inactive_cells as $cell => $unused) {
        list($x, $y, $z, $w) = explode(",", $cell);
        $neighbours = get_neighbours($grid, $x, $y, $z, $w);
        $active_count = 0;
        foreach ($neighbours as $n) {
            if ($n[4] === '#') {
                $active_count++;
            }
        }
        // echo "[Cycle $cycle] Active neighbours ($x, $y, $z, $w) is $active_count\n";
        if ($active_count === 3) {
            // cube becomes active
            $sparse[$w][$z][$y][$x] = '#';
        }
    }

    return $sparse;
}

function display_grid($cycle, $grid) {
    $total_active = 0;

    ksort($grid);
    foreach ($grid as $w => &$cube) {
        ksort($cube);
        foreach ($cube as $z => &$plane) {
            // echo "[Cycle $cycle] Layer Z = $z, W = $w\n";
            ksort($plane);
            foreach ($plane as $y => &$row) {
                ksort($row);
                // printf("%3d.\t", $y);
                foreach ($row as $x => $val) {
                    // printf("%3d[%s] ", $x, $val);
                    if ($val === '#') {
                        $total_active++;
                    }
                }
                // echo "\n";
            }
        }
    }

    echo "[Cycle $cycle] Total active cubes: $total_active\n";
}

function main($filename) {
    $lines = file($filename);

    echo "----[$filename]----\n";

    $grid = [ ];
    foreach ($lines as $line) {
        $grid[0][0][] = str_split(trim($line));
    }

    display_grid(0, $grid);

    for($cycle = 1; $cycle <= 6; $cycle++) {
        $grid = cycle($cycle, $grid);
        display_grid($cycle, $grid);
    }
}

main('star-33-example.txt');
main('star-33-input.txt');

<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function visible_trees(array &$visible, array $line) {
    $current = null;
    foreach ($line as $tree) {
        list($height, $pos) = explode(" ", $tree);
        if ($current === null || $height > $current) {
            $visible[$pos] = $height;
            $current = $height;
        }
    }
}

function display_grid(array $grid) {
    foreach ($grid as $row) {
        echo "   " . implode(" ", $row) . "\n";
    }
}

function rotate90(array $grid) {
    $ret = [];

    $grid = array_reverse($grid);
    $grid_y_size = count($grid) - 1;

    foreach ($grid as $y => $row) {
        foreach ($row as $x => $tree) {
            if (!str_contains($tree, ' ')) {
                // suffix original coord after tree.
                $tree .= " ($x," . ($grid_y_size - $y) . ")";
            }
            $ret[$x][$y] = $tree;
        }
    }

    return $ret;
}

function viewing_distances(array &$distances, array $grid, bool $verbose): array {
    foreach ($grid as $row) {
        foreach ($row as $offset => $tree) {
            $can_see = array_slice($row, $offset + 1);
            if ($verbose && $tree == "5 (2,1)") {
                echo $tree . " can see " . json_encode($can_see) . "\n";
            }

            list($tree_house_height, $tree_house_pos) = explode(" ", $tree);
            $visible_count = 0;
            foreach ($can_see as $visible_tree) {
                $visible_count++;

                list($height, $pos) = explode(" ", $visible_tree);
                if ($height >= $tree_house_height) {
                    break;
                }
            }

            $distances[$tree_house_pos][] = $visible_count;
        }
    }

    return $distances;
}

function get_grid($filename) {
    $grid = [];

    $lines = file($filename);
    foreach ($lines as $line) {
        $grid[] = str_split(trim($line));
    }

    return $grid;
}

function part1($filename, $verbose)
{
    $grid = get_grid($filename);

    $visible = [];
    foreach ([90,180,270,360] as $step) {
        $grid = rotate90($grid);

        foreach ($grid as $row) {
            visible_trees($visible, $row);
        }
    }

    if ($verbose) {
        display_grid($grid);
        echo count($visible) . " trees are visible from the outside.\n";
    }

    return count($visible);
}

function part2($filename, $verbose)
{
    $grid = get_grid($filename);

    $distances = [];

    foreach ([90,180,270,360] as $step) {
        $grid = rotate90($grid);
        viewing_distances($distances, $grid, $verbose);
    }

    foreach ($distances as $tree => $visible_trees) {
        $distances[$tree] = array_product($visible_trees);
    }

    sort($distances);

    if ($verbose) {
        print_r(compact('distances'));
    }

    return array_pop($distances);
}

run_part1('example', true, 21);
run_part1('input');
run_part2('example', true, 8);
run_part2('input');

echo "\n";

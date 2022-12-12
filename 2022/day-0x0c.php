<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/search.php';

$grid = [];
$part2 = false;

function node_name($x, $y)
{
    return sprintf('(%d,%d)', $x, $y);
}

class Square extends Node
{
    public $pos = null;

    public function set_pos(int $x, int $y)
    {
        $this->pos = (object) compact('x', 'y');
        $this->name = node_name($x, $y);
    }

    public function get_height()
    {
        global $grid;
        $height = $grid[$this->pos->y][$this->pos->x] ?? 'error';
        if ($height === 'S') {
            $height = 'a';
        } elseif ($height === 'E') {
            $height = 'z';
        }

        return ord($height) - ord('a');
    }

    public function get_links()
    {
        global $grid;
        global $part2;

        if (!empty($this->links)) {
            return $this->links;
        }

        $ret = [];
        foreach ([[-1, 0], [0, -1], [0, 1], [1, 0]] as $dir) {
            list($x_offset, $y_offset) = $dir;

            $x = $this->pos->x + $x_offset;
            $y = $this->pos->y + $y_offset;

            if (empty($grid[$y][$x])) {
                continue;
            }

            $sq = new Square();
            $sq->set_pos($x, $y);

            if ($part2) {
                // if part2, we're moving in the other direction, so reverse cost
                $cost = $this->get_height() - $sq->get_height();
            } else {
                $cost = $sq->get_height() - $this->get_height();
            }

            if ($cost <= 1) {
                // can at most move up 1, if the cost is more, don't include as a valid path
                $ret[$sq->name] = (object) ['node' => $sq, 'cost' => $cost];
            }
        }

        $this->links = $ret;

        return $ret;
    }
}

function part1(string $filename, bool $verbose)
{
    global $grid;

    $lines = file($filename);

    $grid = [];
    foreach ($lines as $y => $line) {
        $grid[] = str_split(trim($line));
    }

    $starting_point = null;
    $destination = null;

    foreach ($grid as $y => $line) {
        foreach ($line as $x => $height) {
            if ($height === 'S') {
                $starting_point = [$x, $y];
            } elseif ($height === 'E') {
                $destination = [$x, $y];
            }
        }
    }

    $sq = new Square();
    $sq->set_pos($starting_point[0], $starting_point[1]);

    $table = dijkstra($sq);
    echo 'Nodes found... ' . count($table) . "\n";

    $path = dijkstra_shortest_path($table, node_name($destination[0], $destination[1]));
    echo 'Shortest path found... ' . count($path) . "\n";

    return count($path) - 1;
}

function part2(string $filename, bool $verbose)
{
    global $grid;
    global $part2;

    $part2 = true;

    $lines = file($filename);

    $grid = [];
    foreach ($lines as $y => $line) {
        $grid[] = str_split(trim($line));
    }

    $starting_point = null;
    $destination = null;

    foreach ($grid as $y => $line) {
        foreach ($line as $x => $height) {
            if ($height === 'E') {
                $starting_point = [$x, $y];
            } elseif ($height === 'S') {
                $destination = [$x, $y];
            }
        }
    }

    $sq = new Square();
    $sq->set_pos($starting_point[0], $starting_point[1]);

    $table = dijkstra($sq);

    $shortest_paths = [];

    foreach ($table as $name => $data) {
        if ($data['self']->get_height() === 0) {
            $path = dijkstra_shortest_path($table, $name);
            $shortest_paths[] = count($path);
        }
    }

    if ($verbose) {
        print_r($shortest_paths);
    }

    sort($shortest_paths);

    return array_shift($shortest_paths) - 1;
}

run_part1('example', true, 31);
run_part1('input');
echo "\n";

run_part2('example', true, 29);
run_part2('input');
echo "\n";

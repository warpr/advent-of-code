<?php

function end_pos(&$grid) {
    $x = count($grid[0]) - 1;
    $y = count($grid) - 1;
    return "$x,$y";
}

function poor_priority_queue(&$queue, $val) {
    list($node, $cost, $from) = $val;

    if (array_key_exists($node, $queue)) {
        if ($queue[$node]['cost'] > $cost) {
            $queue[$node] = compact('cost', 'from');
        }
    } else {
        $queue[$node] = compact('cost', 'from');
    }

    asort($queue);
}

function neighbours(&$grid, $pos) {
    list($x,$y) = explode(",", $pos);

    $neighbours = [
        [ 0,1 ], [ 1,0 ], [ 0, -1 ], [ -1, 0]
    ];

    foreach ($neighbours as $n) {
        $new_x = $x + $n[0];
        $new_y = $y + $n[1];
        if (empty($grid[$new_y][$new_x])) {
            continue;
        }

        yield [ $new_x, $new_y, $grid[$new_y][$new_x] ];
    }
}

function retrace_path(&$grid, &$via, $pos) {
    $path = [];
    $total_cost = 0;
    do {
        list($x, $y) = explode(",", $pos);
        $total_cost += $grid[$y][$x];

        if (empty($via[$y][$x])) {
            return [ $path, $total_cost ];
        }

        $pos = $via[$y][$x];
        $path[] = $pos;
    } while($pos);
}

function dijkstra(&$grid, &$via, &$cost, &$queue) {
    $pos = array_key_first($queue);
    $data = array_shift($queue);
    $val = $data['cost'];

    $from_str = empty($data['from']) ? '' : "from {$data['from']}, ";

    list($x,$y) = explode(",", $pos);
    $via[$y][$x] = $data['from'];
    $cost[$y][$x] = $data['cost'];

    $neighbours = neighbours($grid, $pos);
    foreach ($neighbours as $n) {
        list($new_x, $new_y, $n_cost) = $n;
        $n_pos = "$new_x,$new_y";

        $cost_to_visit = $n_cost+$val;

        $previous_visit = $cost[$new_y][$new_x] ?? false;
        if ($previous_visit === false || $previous_visit > $cost_to_visit) {
            poor_priority_queue($queue, [$n_pos, $cost_to_visit, $pos]);
        }
    }
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $grid = [];
    foreach ($lines as $line) {
        if (empty($line)) {
            continue;
        }
        $grid[] = str_split($line);
    }

    $end = end_pos($grid);
    echo "Trying to find path to $end\n";

    // no cost for starting point
    $grid[0][0] = 0;
    $via = [];
    $cost = [];

    $queue = [];
    poor_priority_queue($queue, ["0,0", 0, null ]);

    while (count($queue) > 0) {
        dijkstra($grid, $via, $cost, $queue);
    }

    list($path, $total_cost) = retrace_path($grid, $via, $end);

    return $total_cost;
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

main('star-30-example.txt', true, 315);
main('star-30-input.txt', false);


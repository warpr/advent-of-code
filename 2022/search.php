<?php

declare(strict_types=1);

class Node
{
    public $links = [];

    public function __construct(public string $name = '')
    {
    }

    public function add_link(Node $n, int $cost)
    {
        if (empty($this->links[$n->name])) {
            $this->links[$n->name] = (object) ['node' => $n, 'cost' => $cost];
            $n->add_link($this, $cost);
        }
    }

    public function get_links()
    {
        return $this->links;
    }
}

function dijkstra_shortest_path(array $table, string $target): array
{
    $path = [];

    $current = $table[$target] ?? null;

    while (true) {
        $path[] = $current['self'];

        if (empty($current)) {
            // something went wrong
            return $path;
        }

        if (empty($current['previous'])) {
            return $path;
        }

        $current = $table[$current['previous']->name];
        unset($table[$current['self']->name]);
    }
}

function dijkstra(Node $starting_node)
{
    $table = [];

    $table[$starting_node->name] = [
        'distance' => 0,
        'previous' => null,
        'self' => $starting_node,
    ];

    $visited = [];
    $next = [$starting_node];

    $step = 0;

    while (!empty($next)) {
        $current = array_shift($next);

        $links = sort_by($current->get_links(), 'cost');

        foreach ($links as $other) {
            if (!empty($visited[$other->node->name])) {
                unset($next[$other->node->name]);
                continue;
            }

            $distance_to_current = $table[$current->name]['distance'] ?? 0;
            $total = $distance_to_current + $other->cost;

            if (empty($table[$other->node->name])) {
                $table[$other->node->name] = [
                    'distance' => $total,
                    'previous' => $current,
                    'self' => $other->node,
                ];
            } else {
                $distance_to_current = $table[$current->name]['distance'] ?? 0;
                $total = $distance_to_current + $other->cost;

                if ($total < $table[$other->node->name]['distance']) {
                    $table[$other->node->name]['distance'] = $total;
                    $table[$other->node->name]['previous'] = $current;
                }
            }

            $visited[$current->name] = true;
            $next[$other->node->name] = $other->node;
        }
    }

    return $table;
}

function test_dijkstra()
{
    $all_nodes = [];
    foreach (['A', 'B', 'C', 'D', 'E'] as $letter) {
        $all_nodes[$letter] = new Node($letter);
    }

    $all_nodes['A']->add_link($all_nodes['B'], 6);
    $all_nodes['A']->add_link($all_nodes['D'], 1);

    $all_nodes['B']->add_link($all_nodes['D'], 2);
    $all_nodes['B']->add_link($all_nodes['E'], 2);
    $all_nodes['B']->add_link($all_nodes['C'], 5);

    $all_nodes['C']->add_link($all_nodes['E'], 5);

    $all_nodes['D']->add_link($all_nodes['E'], 1);

    $tree = $all_nodes['A'];

    $path = array_column(dijkstra_shortest_path(dijkstra($tree), 'C'), 'name');
    $path_str = implode(' ', array_reverse($path));

    $expected = 'A D E C';
    if ($path_str === $expected) {
        echo "All OK! (path is $path_str)\n";
    } else {
        echo "All OK! (path is $path_str, expected $expected)\n";
    }
}

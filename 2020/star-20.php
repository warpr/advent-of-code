<?php

function is_valid($outlet, $adapter) {
    return ($outlet >= ($adapter - 3)) && ($outlet < $adapter);
}

function is_sequence_valid($seq) {
    $max = count($seq);
    for ($i = 1; $i < $max; $i++) {
        if (!is_valid($seq[$i - 1]['val'], $seq[$i]['val'])) {
            return false;
        }
    }
    return true;
}

function all_permutations($seq) {
    $first = array_shift($seq);

    if (empty($seq)) {
        yield [ $first ];
        yield [];
    } else {
        foreach (all_permutations($seq) as $rest) {
            yield array_merge([ $first ], $rest);
            yield $rest;
        }
    }
}

function count_valid_permutations($start, $seq, $end) {
    if ($seq[0]['req']) {
        return 1;
    }

    $valid = 0;

    foreach (all_permutations($seq) as $chain) {
        if (is_sequence_valid(array_merge([ $start ], $chain, [ $end ]))) {
            $valid++;
            /* echo "- ({$start['val']}) "
             *    . implode(" ", display_sequence($chain))
             *    . " ({$end['val']})\n"; */
        }
    }

    return $valid;
}

function count_branches($lines) {
    $sequences = [];

    $sequence = [ array_shift($lines) ];
    $current = $sequence[0]['req'];

    foreach ($lines as $line) {
        if ($line['req'] != $current) {
            $sequences[] = $sequence;
            $sequence = [];
        }

        $sequence[] = $line;
        $current = $line['req'];
    }

    $sequences[] = $sequence;

    $branches = [];
    foreach ($sequences as $idx => $seq) {
        $prev = $sequences[$idx - 1] ?? null;
        $next = $sequences[$idx + 1] ?? null;
        if ($prev && $next) {
            $permutations = count_valid_permutations(end($prev), $seq, $next[0]);
            if ($permutations > 1) {
                $branches[] = $permutations;
            }
        }
    }

    $total = 1;
    foreach ($branches as $b) {
        $total = $total * $b;
    }
    echo "Branches: " . implode(" x ", $branches) . " = $total\n";
    return $total;
}

function display_sequence($items) {
    $ret = [];
    foreach ($items as $item) {
        $ret[] = $item['req'] ? $item['val'] : "[{$item['val']}]";
    }
    return $ret;
}

function determine_optional($lines, $verbose) {
    $chain = [];
    foreach ($lines as $line) {
        $chain[] = [ 'req' => true, 'val' => $line ];
    }

    $max = count($chain) - 1;

    for ($i = 0; $i < $max; $i++) {
        if (is_valid($lines[$i - 1], $lines[$i + 1])) {
            $chain[$i]['req'] = false;
        }
    }

    if ($verbose) {
        echo implode(" ", display_sequence($chain)) . "\n";
    }
    return $chain;
}

function main($filename, $verbose) {
    $input = file($filename);
    $lines = [];
    foreach ($input as $line) {
        $lines[] = (int) trim($line);
    }

    sort($lines);

    $highest = end($lines);

    $start = 0;
    $built_in = $highest + 3;
    echo "-------------------------------\n";
    echo "Highest rated adapter: $highest\n";
    echo "Built-in adapter:      $built_in\n";
    echo "Outlet rating:         $start\n";
    echo "Adapter set from:      $filename\n";

    array_unshift($lines, 0);
    $lines[] = $built_in;

    $chain = determine_optional($lines, $verbose);
    $collected = count_branches($chain);
    echo "Total arrangements: " . $collected . "\n";
}

main('star-19-example.txt', true);
main('star-19-example2.txt', false);
main('star-19-input.txt', false);

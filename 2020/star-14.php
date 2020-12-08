<?php

function parse_line($line) {
    $line = str_replace("bags", "bag", trim($line));
    $line = str_replace(".", "", $line);
    list($outer, $inside) = explode("contain", $line);
    $outer = trim($outer);

    $ret = [];
    $ret[$outer] = [];
    if (trim($inside) !== 'no other bag') {
        $bags = explode(",", $inside);
        foreach ($bags as $bag_allow) {
            if (preg_match('/([0-9]+)(.*bag)/', $bag_allow, $matches)) {
                $ret[$outer][trim($matches[2])] = (int) $matches[1];
            }
        }
    }
    return $ret;
}

function valid_rule($rules, $look_for, $via = []) {
    foreach ($rules as $outside => $inside) {
        if (empty($inside[$look_for])) {
            continue;
        }

        if (empty($via)) {
            $via = [ $look_for ];
        }
        $path = array_merge([ $outside ], $via);
        yield $path;
        foreach (valid_rule($rules, $outside, $path) as $entry) {
            yield $entry;
        }
    }
}

function count_bags($rules, $name, $indent) {
    if (empty($rules[$name])) {
        return 1;
    }

    // 32
    // 126

    $total = 1;
    foreach ($rules[$name] as $bag => $count) {
        $inner_bag_count = count_bags($rules, $bag, $indent . "  ");
        echo $indent . "- $count $bag ($count x $inner_bag_count)\n";
        $total += ($count * $inner_bag_count);
    }

    return $total;
}

function main($filename) {
    $lines = file($filename);

    echo "--------------\n";

    $rules = [];
    foreach ($lines as $line) {
        $rules += parse_line($line);
    }

    echo "Shiny gold bag contents for $filename:\n";
    $total_count = count_bags($rules, 'shiny gold bag', "");
    echo "Total inner bag count: " . ($total_count - 1) . "\n";
}

main('star-13-example.txt');
main('star-14-example.txt');
main('star-13-input.txt');

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

function main($filename) {
    $lines = file($filename);

    echo "--------------\n";

    $rules = [];
    foreach ($lines as $line) {
        $rules += parse_line($line);
    }

    $output = [];
    foreach (valid_rule($rules, 'shiny gold bag') as $rule) {
        $output[$rule[0]] = $rule;
    }

    ksort($output);
    foreach ($output as $solution) {
        echo "PATH: " . implode(" <- ", $solution) . "\n";
    }

    echo "Total options for $filename: " . count($output) . "\n";
}

main('star-13-example.txt');
main('star-13-input.txt');

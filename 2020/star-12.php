<?php

function per_group($lines) {
    $ret = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            yield $ret;
            $ret = [];
        } else {
            $ret[] = $line;
        }
    }

    yield $ret;
}

function count_questions($group) {
    $all = [];
    $first = true;
    foreach ($group as $person) {
        $questions = [];
        foreach (str_split($person) as $chr) {
            $questions[$chr] = true;
        }
        if ($first) {
            $all = array_keys($questions);
            $first = false;
        } else {
            $all = array_intersect($all, array_keys($questions));
        }
    }
    return count($all);
}

function main($filename) {
    $groups = per_group(file($filename));

    $total = 0;
    foreach ($groups as $group) {
        $total += count_questions($group);;
    }

    echo "Total yes questions: $total\n";
}

main('star-11-example.txt');
main('star-11-input.txt');

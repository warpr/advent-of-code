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
    $yes = implode("", $group);
    $questions = [];
    foreach (str_split($yes) as $chr) {
        $questions[$chr] = true;
    }
    return count(array_keys($questions));
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

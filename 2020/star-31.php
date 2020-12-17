<?php

function parse($lines) {
    $rules = [];
    $tickets = [];

    foreach ($lines as $line) {
        if (empty(trim($line))) {
            continue;
        }

        if (preg_match("/your ticket/", $line)
            || preg_match("/nearby tickets/", $line)) {
            continue;
        }

        if (strpos($line, ":") === false) {
            $tickets[] = trim($line);
        } else {
            $rules[] = trim($line);
        }
    }

    $yours = array_shift($tickets);
    return compact('rules', 'tickets', 'yours');
}

function parse_rules($rules) {
    $ranges = [];
    foreach ($rules as $rule) {
        preg_match_all("/([0-9]+-[0-9]+)/", $rule, $matches);
        foreach ($matches[1] as $match) {
            list($start, $end) = explode("-", $match);
            $ranges[] = compact('start', 'end');
        }
    }
    return $ranges;
}

function is_valid($ranges, $value) {
    foreach ($ranges as $r) {
        if ($value >= $r['start'] && $value <= $r['end']) {
            return true;
        }
    }
    return false;;
}

function validate_ticket($ranges, $ticket) {
    $values = explode(",", $ticket);
    $errors = [];
    foreach ($values as $value) {
        if (!is_valid($ranges, (int) trim($value))) {
            $errors[] = $value;
        }
    }
    // echo "errors for ticket [$ticket] => [" . implode(",", $errors) . "]\n";
    return array_sum($errors);
}

function main($filename) {
    $lines = file($filename);

    $parsed = parse($lines);
    extract($parsed);

    $ranges = parse_rules($rules);

    $error_rate = 0;
    foreach ($parsed['tickets'] as $ticket) {
        $error_rate += validate_ticket($ranges, $ticket);
    }

    echo "Error rate for $filename: $error_rate\n";
}

main('star-31-example.txt');
main('star-31-input.txt');

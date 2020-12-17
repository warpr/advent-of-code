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
        list($field, $rest) = explode(":", $rule);
        preg_match_all("/([0-9]+-[0-9]+)/", $rule, $matches);
        foreach ($matches[1] as $match) {
            list($start, $end) = explode("-", $match);
            $ranges[$match] = compact('start', 'end', 'field');
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
    return count($errors);
}

function guess_fields($possibilities, $ranges, $ticket) {
    $values = explode(",", $ticket);
    $fields = [];

    $guesses = [];
    foreach ($values as $pos => $value) {
        foreach ($ranges as $rname => $r) {
            if ($value >= $r['start'] && $value <= $r['end']) {
                $guesses[$pos][$r['field']] = true;
            }
        }
    }

    foreach ($possibilities as $pos => $fields) {
        $g = $guesses[$pos];

        foreach ($fields as $field => $unused) {
            if (empty($g[$field])) {
                // echo "Field $field at $pos? " . (empty($g[$field]) ? "nope" : "yes") ."\n";

                unset($possibilities[$pos][$field]);
            }
        }

    }

    return $possibilities;
}

function cleanup_known($possibilities) {
    $changes = true;
    while($changes) {
        $changes = false;
        foreach ($possibilities as $pos => $fields) {
            if (count($possibilities[$pos]) == 1) {
                $field = array_key_first($possibilities[$pos]);
                foreach ($possibilities as $pos2 => $fields2) {
                    if (empty($possibilities[$pos2][$field])) {
                        continue;
                    }
                    if ($pos2 != $pos) {
                        // echo "$field is definitely at $pos, removing from $pos2!\n";
                        unset($possibilities[$pos2][$field]);
                        $changes = true;
                    }
                }
            }
        }
    }

    return $possibilities;
}

function main($filename) {

    echo "___[$filename]___\n";

    $lines = file($filename);

    $parsed = parse($lines);
    extract($parsed);

    $ranges = parse_rules($rules);

    $valid_tickets = [];
    foreach ($parsed['tickets'] as $ticket) {
        if (validate_ticket($ranges, $ticket) > 0) {
            continue;
        }

        $valid_tickets[] = $ticket;
    }

    $possibilities = [];
    $field_count = count(explode(",", $yours));
    $all_fields = array_column($ranges, 'field');
    for($i = 0; $i < $field_count; $i++) {
        $possibilities[$i] = array_flip($all_fields);
    }

    $valid_tickets[] = $yours;

    $count = 0;
    foreach ($valid_tickets as $ticket) {
        $possibilities = guess_fields($possibilities, $ranges, $ticket);
        $possibilities = cleanup_known($possibilities);
    }

    $departure_values = [];
    $my_ticket = explode(",", $yours);
    foreach ($my_ticket as $pos => $value) {
        $field = array_key_first($possibilities[$pos]);
        echo "$field: $value\n";
        if (preg_match("/^departure/", $field)) {
            $departure_values[] = $value;
        }
    }

    echo "Final answer: " . array_reduce($departure_values, function ($memo, $item) {
        return $memo * $item;
    }, 1) . "\n";
}

main('star-32-example.txt');
main('star-31-input.txt');

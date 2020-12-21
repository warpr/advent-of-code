<?php

ini_set('memory_limit','4096M');

function valid_options($prefixes, $suffixes) {
    $ret = [];
    foreach ($prefixes as $prefix) {
        foreach ($suffixes as $suffix) {
            $ret[] = $prefix . $suffix;
        }
    }

    return $ret;
}

function resolve_rules($validators, $rules) {
    $rest = [];

    foreach ($rules as $idx => $rule) {
        if (preg_match('/"([a-z])"/', $rule, $matches)) {
            $validators[$idx] = [ $matches[1] ];
            // echo "$idx: $rule, resolved as final\n";
            continue;
        }

        $parts = explode("|", $rule);
        $resolved = true;
        $options = [];
        foreach ($parts as $part) {
            $refs = explode(" ", $part);
            $prefixes = [ '' ];
            foreach ($refs as $ref) {
                $ref = trim($ref);
                if (empty($ref)) {
                    continue;
                }
                // echo "$idx: $rule, looking for $ref\n";
                if (!isset($validators[$ref])) {
                    $resolved = false;
                } else {
                    $prefixes = valid_options($prefixes, $validators[$ref]);
                }
            }
            $options[] = $prefixes;
        }
        if ($resolved) {
            // echo "$idx: $rule, resolved by following refs\n";
            $validators[$idx] = array_merge(...$options);
        } else {
            // echo "$idx: $rule, not resolved\n";
            $rest[$idx] = $rule;
        }
    }

    // print_r(compact('validators', 'rest'));
    return [ $validators, $rest ];
}

function generate_validator($rules) {
    $validators = [];
    for ($i = 0; $i < 100; $i++) {
        list($validators, $rules) = resolve_rules($validators, $rules);
        if (empty($rules)) {
            break;
        }
    }

//    echo "Validators are ready!\n";
//    print_r($validators[0]);

    return $validators[0];
}

function main($filename) {
    $lines = file($filename);

    $rules = [];
    $messages = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match("/^([0-9]+): (.*)$/", $line, $matches)) {
            $rules[$matches[1]] = $matches[2];
        } else if (!empty($line)) {
            $messages[] = $line;
        }
    }

    $valid_messages = generate_validator($rules);
    $valid_hash = array_flip($valid_messages);

    $count = 0;
    foreach ($messages as $message) {
        if (isset($valid_hash[$message])) {
            $count++;
        }
    }

    echo "Valid messages for $filename: $count\n";
}

//generate_validator([ '1 2','"a"', '1 3 | 3 1', "b" ]);
//generate_validator([ '4 1 5', '2 3 | 3 2', '4 4 | 5 5', '4 5 | 5 4', '"a"', '"b"' ]);

main('star-37-example.txt');
main('star-37-input.txt');

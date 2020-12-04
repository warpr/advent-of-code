<?php

function passports($lines) {
    $current = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            yield $current;
            $current = [];
            continue;
        }

        $parts = explode(" ", $line);
        $current = array_merge($current, $parts);
    }
    yield $current;
}

function decode_passport($passport) {
    $ret = [];
    foreach ($passport as $pair) {
        list($key, $value) = explode(":", $pair);
        $ret[$key] = $value;
    }
    return $ret;
}

function validate_passport($passport) {
    $required_fields = [
        'byr', // (Birth Year)
        'iyr', // (Issue Year)
        'eyr', // (Expiration Year)
        'hgt', // (Height)
        'hcl', // (Hair Color)
        'ecl', // (Eye Color)
        'pid', // (Passport ID)
        'cid', // (Country ID)
    ];

    if (empty($passport['cid'])) {
        $passport['cid'] = 'north-pole';
    }

    foreach ($required_fields as $field) {
        if (empty($passport[$field])) {
            return 0;
        }
    }

    return 1;
}

function main($filename) {
    $lines = file($filename);
    $passports = passports($lines);

    $valid = 0;
    foreach ($passports as $pass) {
        $valid += validate_passport(decode_passport($pass));
    }
    echo "Valid passports in $filename: $valid\n";
}

main('star-07-example.txt');
main('star-07-input.txt');

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
    $validators = [
        'byr' => function ($value) {
            // byr (Birth Year) - four digits; at least 1920 and at most 2002.
            if (preg_match("/^[0-9]{4}$/", trim($value))) {
                if ($value < 2003 && $value > 1919) {
                    return true;
                }
            }
            return false;
        },
        'iyr' => function ($value) {
            // iyr (Issue Year) - four digits; at least 2010 and at most 2020.
            if (preg_match("/^[0-9]{4}$/", trim($value))) {
                if ($value < 2021 && $value > 2009) {
                    return true;
                }
            }
            return false;
        },
        'eyr' => function ($value) {
            // eyr (Expiration Year) - four digits; at least 2020 and at most 2030.
            if (preg_match("/^[0-9]{4}$/", trim($value))) {
                if ($value < 2031 && $value > 2019) {
                    return true;
                }
            }
            return false;
        },
        'hgt' => function ($value) {
            // hgt (Height) - a number followed by either cm or in:
            // If cm, the number must be at least 150 and at most 193.
            // If in, the number must be at least 59 and at most 76.
            if (preg_match("/^([0-9]*)cm$/", trim($value), $matches)) {
                if ($matches[1] < 194 && $matches[1] > 149) {
                    return true;
                }
            }
            if (preg_match("/^([0-9]*)in$/", trim($value), $matches)) {
                if ($matches[1] < 77 && $matches[1] > 58) {
                    return true;
                }
            }
            return false;
        },
        'hcl' => function ($value) {
            // hcl (Hair Color) - a # followed by exactly six characters 0-9 or a-f.
            if (preg_match("/^\#[0-9a-f]{6}$/", trim($value))) {
                return true;
            }
            return false;
        },
        'ecl' => function ($value) {
            // ecl (Eye Color) - exactly one of: amb blu brn gry grn hzl oth.
            $value = trim($value);
            return in_array($value, ["amb", "blu", "brn", "gry", "grn", "hzl", "oth"]);
        },
        'pid' => function ($value) {
            // pid (Passport ID) - a nine-digit number, including leading zeroes.
            if (preg_match("/^[0-9]{9}$/", trim($value))) {
                return true;
            }
            return false;
        },
        'cid' => function ($value) {
            // cid (Country ID) - ignored, missing or not.
            return true;
        },
    ];

    if (empty($passport['cid'])) {
        $passport['cid'] = 'north-pole';
    }

    foreach ($validators as $field => $func) {
        if (empty($passport[$field])) {
            // echo "Passport is missing $field: " . print_r($passport, true) . "\n";
            return 0;
        }

        if (!$func($passport[$field])) {
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
main('star-08-invalid.txt');
main('star-08-valid.txt');

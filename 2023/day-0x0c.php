<?php

declare(strict_types=1);

ini_set('memory_limit', '24576M');

require_once __DIR__ . '/common.php';

$find_valid = memoize(find_valid_permutations(...));

function find_valid_permutations(bool $verbose, int $pos, array $record, array $expected)
{
    global $find_valid;

    $max = count($record);

    //    $prefix = str_pad("", $pos, "-") . "> ";
    //    vecho ($verbose, $prefix . "searching ($pos) " . implode("", $record) . " \t " . implode(",", $expected) . "\n");

    while ($pos < $max && $record[$pos] === '.') {
        $pos++;
    }

    if ($pos >= $max) {
        $valid = empty($expected);
        if ($valid) {
            vecho($verbose, 'VALID foo ' . implode('', $record) . "\n");
            return 1;
        }
        return 0;
    }

    if (empty($expected)) {
        $tmp = array_slice($record, $pos);
        if (array_search('#', $tmp) === false) {
            // if we no longer expect any sequences of '#', and only '.' and '?'
            // remain, the sequence has one valid solution which is to turn all
            // remaining '?' into '.'.
            for ($i = $pos; $i < $max; $i++) {
                $record[$i] = '.';
            }

            vecho($verbose, 'VALID bar ' . implode('', $record) . "\n");
            return 1; // valid
        } else {
            return 0; // no
        }
    }

    $in_hash_sequence = false;

    while ($expected[0] > 0) {
        if ($pos >= $max) {
            // expected sequence of #s is longer, this is not a valid permutation
            return 0;
        }

        switch ($record[$pos]) {
            case '.':
                return 0;
            case '#':
                $expected[0]--;
                $pos++;
                $in_hash_sequence = true;
                break;
            case '?':
                $a = 0;
                if (!$in_hash_sequence) {
                    $record[$pos] = '.';
                    $a = $find_valid($verbose, $pos, $record, $expected);
                }

                $record[$pos] = '#';
                $b = $find_valid($verbose, $pos, $record, $expected);
                return $a + $b;
        }
    }

    if (array_shift($expected) !== 0) {
        echo "Something weird happened...\n";
        print_r(compact('record', 'expected'));
        die();
    }

    if ($pos >= $max) {
        $valid = empty($expected);
        if ($valid) {
            vecho($verbose, 'VALID baz ' . implode('', $record) . "\n");
            return 1;
        }
        // expected sequence of #s is longer, this is not a valid permutation
        return 0;
    }

    if ($record[$pos] === '#') {
        // sequence longer than expected, invalid
        return 0;
    }

    if ($record[$pos] === '?') {
        $record[$pos] = '.'; // only option
        $pos++;
    }

    return $find_valid($verbose, $pos, $record, $expected);
}

function unfold(string $record, array $code)
{
    $unfolded_record = implode('?', [$record, $record, $record, $record, $record]);
    $unfolded_code = array_merge($code, $code, $code, $code, $code);

    return [$unfolded_record, $unfolded_code];
}

function parse(string $filename, bool $verbose, bool $part2)
{
    global $find_valid;

    $lines = file($filename);

    $ret = [];

    foreach ($lines as $idx => $line) {
        list($record, $verification) = explode(' ', trim($line));
        $code = explode(',', $verification);

        if ($part2) {
            list($record, $code) = unfold($record, $code);
        }

        vecho(
            true,
            "[ $idx ] ___ $record _____ verification code " . implode(',', $code) . " _____\n"
        );
        $ret[] = $find_valid($verbose, 0, str_split($record), $code);
    }

    return $ret;
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example0', false, 6);
run_part1('example', false, 21);
run_part1('input', false);
echo "\n";

run_part2('example', false, 525152);
run_part2('input', false);
echo "\n";

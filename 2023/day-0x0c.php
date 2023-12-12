<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function permutations(string $record)
{
    $qmarkpos = strpos($record, '?');
    if ($qmarkpos === false) {
        yield $record;
        return;
    }

    $with_hash = $record;
    $with_dot = $record;

    $with_hash[$qmarkpos] = '#';
    $with_dot[$qmarkpos] = '.';

    foreach (permutations($with_hash) as $tmp) {
        yield $tmp;
    }

    foreach (permutations($with_dot) as $tmp) {
        yield $tmp;
    }
}

function record_validation(string $record)
{
    $sequences = [];
    $in_sequence = false;
    $seq_length = 0;

    foreach (str_split($record) as $idx => $chr) {
        if ($chr === '#') {
            $in_sequence = true;
            $seq_length++;
        } elseif ($chr === '.') {
            if ($in_sequence) {
                $sequences[] = $seq_length;
            }
            $in_sequence = false;
            $seq_length = 0;
        }
    }

    if ($in_sequence) {
        $sequences[] = $seq_length;
    }

    return $sequences;
}

function is_record_valid(string $record, array $expected)
{
    $actual = record_validation($record);

    return $actual == $expected;
}

function unfold(string $record, array $code)
{
    $unfolded_record = implode('?', [$record, $record, $record, $record, $record]);
    $unfolded_code = array_merge([$code, $code, $code, $code, $code]);

    return [$unfolded_record, $unfolded_code];
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];

    foreach ($lines as $idx => $line) {
        list($record, $verification) = explode(' ', trim($line));
        $code = explode(',', $verification);

        if ($part2) {
            list($record, $code) = unfold($record, $code);
        }

        foreach (permutations($record) as $a => $attempt) {
            // loop over permutations...
            if (is_record_valid($attempt, $code)) {
                @$ret[$idx]++;
                vecho($verbose, "{$idx} v{$a}: {$attempt} (VALID)\n");
            } else {
                vecho($verbose, "{$idx} v{$a}: {$attempt} (INVALID)\n");
            }
        }
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

run_part1('example0', true, 6);
run_part1('example', true, 21);
//run_part1('input', false);
echo "\n";

run_part2('example', true, 525152);
// run_part2('input', false);
// echo "\n";

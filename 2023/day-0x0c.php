<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function find_valid_permutations(bool $verbose, array $record, array $expected)
{
    // vecho($verbose, "searching... " . implode("", $record) . " \t " . implode(",", $expected) . "\n");

    $ret = 0;

    while ($record && $record[0] === '.') {
        array_shift($record);
    }

    if (empty($record)) {
        return $ret + (empty($expected) ? 1 : 0);
    }

    if (empty($expected)) {
        if (array_search('#', $record) === false) {
            // if we no longer expect any sequences of '#', and only '.' and '?'
            // remain, the sequence has one valid solution which is to turn all
            // remaining '?' into '.'.
            return $ret + 1; // valid
        } else {
            return $ret; // no
        }
    }

    while ($expected[0] > 0) {
        if (empty($record)) {
            // expected sequence of #s is longer, this is not a valid permutation
            return $ret;
        }

        switch ($record[0]) {
            case '.':
                return 0;
            case '#':
                $expected[0]--;
                array_shift($record);
                break;
            case '?':
                $record[0] = '.';
                $a = find_valid_permutations($verbose, $record, $expected);
                if ($a) {
                    vecho(
                        $verbose,
                        'VALID ' . implode('', $record) . " \t " . implode(',', $expected) . "\n"
                    );
                }
                $record[0] = '#';
                $b = find_valid_permutations($verbose, $record, $expected);
                if ($b) {
                    vecho(
                        $verbose,
                        'VALID ' . implode('', $record) . " \t " . implode(',', $expected) . "\n"
                    );
                }

                vecho($verbose, "returning [$ret + $a + $b]\n");

                return $ret + $a + $b;
        }
    }

    if (array_shift($expected) !== 0) {
        echo "Something weird happened...\n";
        print_r(compact('record', 'expected'));
        die();
    }

    if (empty($record)) {
        return $ret + (empty($expected) ? 1 : 0);
    }

    if ($record[0] === '#') {
        // sequence longer than expected, invalid
        return $ret;
    }

    if ($record[0] === '?') {
        $record[0] = '.'; // only option
    }

    return $ret + find_valid_permutations($verbose, $record, $expected);
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

        vecho($verbose, "=======[ $idx ]=======\n");
        $ret[] = find_valid_permutations($verbose, str_split($record), $code);
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

// run_part2('example', true, 525152);
// run_part2('input', false);
// echo "\n";

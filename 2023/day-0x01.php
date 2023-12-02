<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function word_to_digit(array $matches)
{
    $word = array_pop($matches);
    $mapping = array_flip([
        'zero',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
    ]);

    // a bit hacky, this turns eight into 8ight,
    // which ensures that lines such as "eighthree"
    // get parsed as 83.
    return $mapping[$word] . substr($word, 1);
}

function replace_digits(string $line)
{
    $input = $line;
    $output = null;

    // loop for a bit, a first pass may turn eighthree into 8ighthree,
    // needing a second pass to get 8ight3hree
    while ($output != $input) {
        $input = $output ?? $line;
        $output = preg_replace_callback(
            '/(one|two|three|four|five|six|seven|eight|nine|zero)/',
            'word_to_digit',
            $input
        );
    }

    return $output;
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        $before = trim($line);
        if ($part2) {
            $line = replace_digits(trim($line));
        }
        preg_match('/^[^0-9]*([0-9])/', $line, $matches);
        $first = $matches[1];
        preg_match("/.*([0-9])[^0-9]*$/", $line, $matches);
        $last = $matches[1];
        $ret[] = (int) "{$first}{$last}";
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

function part1(string $filename, bool $verbose)
{
    return main($filename, $verbose, false);
}

function part2(string $filename, bool $verbose)
{
    return main($filename, $verbose, true);
}

run_part1('example', true, 142);
run_part1('input', false);
echo "\n";

run_part2('example2', true, 281);
run_part2('input', false);
echo "\n";

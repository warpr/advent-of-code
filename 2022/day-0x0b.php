<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse_input(array $lines)
{
    $monkeys = [];

    $current = [];
    foreach ($lines as $line) {
        if (preg_match('/(Monkey .*):/', $line, $matches)) {
            if (!empty($current)) {
                $monkeys[] = $current;
            }
            $current = ['name' => $matches[1]];
        } elseif (preg_match('/Starting items: (.*)/', trim($line), $matches)) {
            $current['items'] = array_map('intval', explode(',', $matches[1]));
        } elseif (preg_match('/Operation: new = (.*)/', trim($line), $matches)) {
            $current['operation'] = explode(' ', $matches[1]);
        } elseif (preg_match('/Test: divisible by (.*)/', trim($line), $matches)) {
            $current['test-div'] = (int) $matches[1];
        } elseif (preg_match('/If (.*): throw to monkey (.*)/', trim($line), $matches)) {
            $key = 'throw-' . trim($matches[1]);
            $current[$key] = (int) $matches[2];
        }
    }

    $monkeys[] = $current;

    return $monkeys;
}

function run_operation(array $op, int $val)
{
    $op = array_map(fn($i) => $i === 'old' ? $val : $i, $op);
    list($arg1, $infix, $arg2) = $op;

    switch ($infix) {
        case '*':
            return $arg1 * $arg2;
        case '+':
            return $arg1 + $arg2;
        case '-':
            return $arg1 - $arg2;
    }

    echo 'OPERATIOR NOT SUPPORTED [' . implode(' ', $op) . "]\n";
    die();
}

function play_round(int $roundno, array $state, array &$inspections, bool $verbose): array
{
    foreach ($state as $idx => $unused) {
        // $unused is outdated once we get to subsequent monkeys
        $monkey = $state[$idx];

        if ($verbose) {
            //            echo $monkey['name'] . ":\n";
        }

        $state[$idx]['items'] = [];

        foreach ($monkey['items'] as $item) {
            @$inspections[$monkey['name']]++;

            $worry_level = run_operation($monkey['operation'], $item);
            $bored_level = (int) floor($worry_level / 3);
            $rest = $bored_level % $monkey['test-div'];
            $throw_to = $rest ? $monkey['throw-false'] : $monkey['throw-true'];

            if ($verbose) {
                //                echo "  " . $monkey['name'] . " inspects an item with a worry level of $item\n";
                //                echo "    Worry level is [" . implode(" ", $monkey['operation']) . "] to $worry_level.\n";
                //                echo "    Monkey gets bored with item. Worry level is divided by 3 to $bored_level.\n";
                //                echo "    Current worry level is " . (empty($rest) ? "" : "not ") . "divisible by " . $monkey['test-div'] . ".\n";
                //                echo "    Item with worry level $bored_level is thrown to monkey $throw_to.\n";
            }

            $state[$throw_to]['items'][] = $bored_level;
        }
    }

    if ($verbose) {
        echo "After round {$roundno}, the monkeys are holding items with these worry levels:\n";

        foreach ($state as $idx => $monkey) {
            echo $monkey['name'] . ': ' . implode(' ', $monkey['items']) . "\n";
        }
    }

    return $state;
}

function main($filename, bool $verbose)
{
    $lines = file($filename);
    $state = parse_input($lines);

    if ($verbose) {
        print_r($state);
    }

    $inspections = [];

    for ($i = 1; $i <= 20; $i++) {
        $state = play_round($i, $state, $inspections, $verbose);
    }

    sort($inspections);
    $monkey_business = array_pop($inspections) * array_pop($inspections);

    echo "Monkey business: $monkey_business\n";

    return $monkey_business;
}

function part1($filename, bool $verbose)
{
    return main($filename, $verbose);
}

run_part1('example', true, 10605);
run_part1('input');

echo "\n";

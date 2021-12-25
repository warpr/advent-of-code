<?php

declare(strict_types=1);

ini_set('memory_limit', '24576M');

function remove_duplicates($states)
{
    $hashed = [];
    foreach ($states as $input => $state) {
        $key = implode(',', [$state['w'], $state['x'], $state['y'], $state['z']]);
        $hashed[$key] = $input;
    }

    // print_r(compact('hashed'));

    $ret = [];
    foreach ($hashed as $state_str => $input) {
        $ret[$input] = $states[$input];
    }
    return $ret;
}

function enumerate_program($prog, $states)
{
    $ret = [];
    foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $input) {
        foreach ($states as $seq => $s) {
            $key = $seq . $input;
            $ret[$key] = alu($prog, [$input], $s);
        }
    }

    return $ret;
}

function show_progress()
{
    static $prev = 0;

    $now = hrtime(true);
    $seconds_elapsed = ($now - $prev) / 1000000000; // to seconds

    if ($seconds_elapsed > 1) {
        $prev = $now;
        return true;
    } else {
        return false;
    }
}

function print_instruction($instruction)
{
    extract($instruction);
    echo ">>> $cmd " . implode(' ', $args) . "\n";
}

function print_state($state)
{
    extract($state);
    printf("ALU[w: %d, x: %d, y: %d, z: %d]\n", $w, $x, $y, $z);
}

function parse_line($line)
{
    $parts = explode(' ', $line);
    $cmd = array_shift($parts);
    return ['cmd' => $cmd, 'args' => $parts];
}

function arg_to_value(&$state, $arg)
{
    $registers = ['w', 'x', 'y', 'z'];

    if (in_array($arg, $registers)) {
        return $state[$arg];
    } elseif (is_numeric($arg)) {
        return $arg;
    }

    echo "ERROR: unable to parse argument $arg\n";
    die();
}

function run_instruction(&$state, &$input, $instruction)
{
    $registers = ['w', 'x', 'y', 'z'];
    extract($instruction);

    switch ($cmd) {
        case 'inp':
            $register = $args[0];
            $state[$register] = array_shift($input);
            break;
        case 'mul':
            $register = $args[0];
            $state[$register] = arg_to_value($state, $args[0]) * arg_to_value($state, $args[1]);
            break;
        case 'add':
            $register = $args[0];
            $state[$register] = arg_to_value($state, $args[0]) + arg_to_value($state, $args[1]);
            break;
        case 'div':
            $register = $args[0];
            $state[$register] = floor(
                arg_to_value($state, $args[0]) / arg_to_value($state, $args[1])
            );
            break;
        case 'mod':
            $register = $args[0];
            $state[$register] = arg_to_value($state, $args[0]) % arg_to_value($state, $args[1]);
            break;
        case 'eql':
            $register = $args[0];
            $a = (int) arg_to_value($state, $args[0]);
            $b = (int) arg_to_value($state, $args[1]);
            $state[$register] = $a === $b ? 1 : 0;
            break;
        default:
            echo "ERROR: unknown instruction \"" . $instruction['cmd'] . "\".\n";
            die();
    }
}

function alu(array $program, array $input, array $state = null)
{
    $lines = array_map('trim', $program);

    if (empty($state)) {
        $state = ['w' => 0, 'x' => 0, 'y' => 0, 'z' => 0];
    }

    foreach ($lines as $line) {
        $instruction = parse_line($line);
        run_instruction($state, $input, $instruction);
        //        print_instruction($instruction);
        //        print_state($state);
    }

    return $state;
}

function run($filename, $verbose = false)
{
    $lines = array_map('trim', file($filename));

    $programs = [];
    $current = [];
    foreach ($lines as $line) {
        $instruction = parse_line($line);
        if ($instruction['cmd'] === 'inp') {
            if (!empty($current)) {
                $programs[] = $current;
            }
            $current = [];
        }

        $current[] = $line;
    }

    if (!empty($current)) {
        $programs[] = $current;
    }

    $state = ['w' => 0, 'x' => 0, 'y' => 0, 'z' => 0];
    $states = ['' => $state];
    foreach ($programs as $idx => $p) {
        $states = enumerate_program($p, $states);
        $total = count($states);
        $states = remove_duplicates($states);
        echo "Part $idx: tracking " . count($states) . " states (reduced from $total states)\n";
    }

    foreach ($states as $input => $state) {
        if ($state['z']) {
            echo "Found z = 1: $input\n";
        }
    }

    return $input;
}

function main($filename, $verbose = null, $expected = null)
{
    $actual = run($filename, $verbose);
    if ($expected) {
        if ($actual !== $expected) {
            echo "You broke $filename, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

function expect($actual, $expected, $desc)
{
    if ($actual !== $expected) {
        echo "ERROR: expected: $expected, actual: $actual -- $desc\n";
        die();
    } else {
        echo "OK: value: $actual -- $desc\n";
    }
}

$result = alu(['inp x', 'mul x -1'], [4]);

print_state($result);
expect($result['x'], -4, 'negated in x');

$example2 = ['inp z', 'inp x', 'mul z 3', 'eql z x'];

$result = alu($example2, [3, 9]);
print_state($result);
expect($result['z'], 1, 'second number is three times larger');

$result = alu($example2, [3, 8]);
print_state($result);
expect($result['z'], 0, 'second number is NOT three times larger');

$example3 = [
    'inp w',
    'add z w',
    'mod z 2',
    'div w 2',
    'add y w',
    'mod y 2',
    'div w 2',
    'add x w',
    'mod x 2',
    'div w 2',
    'mod w 2',
];

$result = alu($example3, [0x0a]);
print_state($result);
expect($result['w'], 1, '0x0A is 1010');

main('star-47-input.txt', false);

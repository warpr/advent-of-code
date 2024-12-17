<?php
/**
 *   Copyright (C) 2024  Kuno Woudt <kuno@frob.nl>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of copyleft-next 0.3.1.  See copyleft-next-0.3.1.txt.
 *
 *   SPDX-License-Identifier: copyleft-next-0.3.1
 */

declare(strict_types=1);

require_once __DIR__ . '/common.php';

const adv = 0;
const bxl = 1;
const bst = 2;
const jnz = 3;
const bxc = 4;
const out = 5;
const bdv = 6;
const cdv = 7;

function empty_state()
{
    return [
        'ax' => 0,
        'bx' => 0,
        'cx' => 0,
        'ip' => 0,
        'mem' => [],
        'out' => [],
    ];
}

function parse(string $filename, bool $part2)
{
    $lines = file($filename);

    $state = empty_state();

    foreach ($lines as $line) {
        if (preg_match('/Register ([A-Z]): ([0-9]+)/', $line, $matches)) {
            $reg = strtolower($matches[1] . 'X');
            $state[$reg] = (int) $matches[2];
        }

        if (preg_match('/Program: (.*)/', $line, $matches)) {
            $state['mem'] = array_map('intval', explode(',', trim($matches[1])));
        }
    }

    return $state;
}

function combo(int $operand, array $state)
{
    switch ($operand) {
        case 4:
            return $state['ax'];
        case 5:
            return $state['bx'];
        case 6:
            return $state['cx'];
        case 7:
            die('illegal combo operand');
        default:
            return $operand;
    }
}

function test_execute()
{
    $state = empty_state();
    $state['cx'] = 9;
    $state['mem'] = [2, 6];
    $output = run($state);
    if ($output['bx'] !== 1) {
        die('If register C contains 9, the program 2,6 would set register B to 1.');
    }

    $state = empty_state();
    $state['ax'] = 10;
    $state['mem'] = [5, 0, 5, 1, 5, 4];
    $output = run($state);
    $out_str = implode(',', $output['out']);
    if ($out_str !== '0,1,2') {
        print_r(compact('out_str'));
        die('If register A contains 10, the program 5,0,5,1,5,4 would output 0,1,2.');
    }

    $state = empty_state();
    $state['ax'] = 2024;
    $state['mem'] = [0, 1, 5, 4, 3, 0];
    $output = run($state);
    $out_str = implode(',', $output['out']);
    if ($out_str !== '4,2,5,6,7,7,7,7,3,1,0') {
        print_r(['ax' => $output['ax'], 'out' => $out_str]);
        die(
            'If register A contains 2024, the program 0,1,5,4,3,0 would output 4,2,5,6,7,7,7,7,3,1,0 and leave 0 in register A.'
        );
    }
    if ($output['ax']) {
        print_r(['ax' => $output['ax'], 'out' => $out_str]);
        die(
            'If register A contains 2024, the program 0,1,5,4,3,0 would output 4,2,5,6,7,7,7,7,3,1,0 and leave 0 in register A.'
        );
    }

    $state = empty_state();
    $state['bx'] = 29;
    $state['mem'] = [1, 7];
    $output = run($state);

    if ($output['bx'] !== 26) {
        die('If register B contains 29, the program 1,7 would set register B to 26.');
    }

    $state = empty_state();
    $state['bx'] = 2024;
    $state['cx'] = 43690;
    $state['mem'] = [4, 0];
    $output = run($state);
    if ($output['bx'] !== 44354) {
        die(
            'If register B contains 2024 and register C contains 43690, the program 4,0 would set register B to 44354.'
        );
    }

    echo "CPU diagnostics OK\n";
}

function execute(array $state): array
{
    $ip = $state['ip'];

    $opcode = $state['mem'][$state['ip']++];
    $operand = $state['mem'][$state['ip']++];

    //    vecho::msg("ip:", $ip, "ax:", $state['ax'], "bx:", $state['bx'], "cx:", $state['cx'], "out:", $state['out']);

    switch ($opcode) {
        case adv:
            $val = $state['ax'] / pow(2, combo($operand, $state));
            $state['ax'] = intval($val);
            break;
        case bdv:
            $val = $state['ax'] / pow(2, combo($operand, $state));
            $state['bx'] = intval($val);
            break;
        case cdv:
            $val = $state['ax'] / pow(2, combo($operand, $state));
            $state['cx'] = intval($val);
            break;
        case bxl:
            $state['bx'] = $state['bx'] ^ $operand;
            break;
        case bst:
            $state['bx'] = combo($operand, $state) % 8;
            break;
        case jnz:
            if ($state['ax'] != 0) {
                $state['ip'] = $operand;
            }
            break;
        case bxc:
            $state['bx'] = $state['bx'] ^ $state['cx'];
            break;
        case out:
            $state['out'][] = combo($operand, $state) % 8;
            break;
    }

    return $state;
}

function run(array $state)
{
    while (isset($state['mem'][$state['ip']])) {
        $state = execute($state);
    }

    return $state;
}

function part1(array $input)
{
    $output = run($input);
    return $output['out'];
}

function part2(array $input)
{
    return [23];
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, $part2);

    if ($part2) {
        $values = part2($parsed);
    } else {
        $values = part1($parsed);
    }

    if (vecho::$verbose) {
        print_r($values);
    }

    return implode(',', $values);
}

test_execute();

run_part1('example', false, '4,6,3,5,6,3,5,2,1,0');
run_part1('input', false);
echo "\n";

// run_part2('example', true, 9021);
// run_part2('input', false, 1582688);
echo "\n";

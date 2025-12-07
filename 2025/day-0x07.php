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

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $idx => $line) {
        $ret[] = trim($line);
    }

    return [$ret];
}

function part1($data)
{
    $grid = new grid($data);

    $pos = $grid->find_first('S');

    $split_count = 0;

    $beams = [$pos];
    foreach ($grid->grid as $y => $line) {
        $old_beams = $beams;
        $beams = [];

        foreach ($old_beams as $beam) {
            $s_pos = $beam->add(S);
            $s = $grid->get($s_pos);

            if ($s === null) {
                // end of grid
                break;
            }

            if ($s === '.') {
                $grid->set($s_pos, '|');
                $beams[] = $s_pos;
                continue;
            }

            if ($s === '|') {
                // a beam has appeared under us, I guess that's OK
                continue;
            }

            if ($s !== '^') {
                $grid->render();
                echo "[ERROR] Unexpected situation! ({$s} at {$s_pos}\n";
                exit(1);
            }

            $sw = $grid->get($beam->add(SW));
            $se = $grid->get($beam->add(SE));
            $split_count++;
            if ($sw !== '|') {
                $grid->set($beam->add(SW), '|');
                $beams[] = $beam->add(SW);
            }
            if ($se !== '|') {
                $grid->set($beam->add(SE), '|');
                $beams[] = $beam->add(SE);
            }
        }
    }

    $grid->render();

    return [$split_count];
}

function part2($data)
{
    $grid = new grid($data);

    $pos = $grid->find_first('S');

    $beams = [];
    $final_beams = [];

    $beams["{$pos}"] = ['pos' => $pos, 'count' => 1];

    foreach ($grid->grid as $y => $line) {
        $old_beams = $beams;
        $beams = [];

        foreach ($old_beams as $quantum_beam) {
            $beam = $quantum_beam['pos'];

            $s_pos = $beam->add(S);
            $s = $grid->get($s_pos);

            if ($s === null) {
                $final_beams = $old_beams;
                // end of grid
                break;
            }

            if ($s === '.') {
                $grid->set($s_pos, '|');
                $beams["{$s_pos}"] = ['pos' => $s_pos, 'count' => $quantum_beam['count']];
                continue;
            }

            if ($s === '|') {
                // a beam has appeared under us, I guess that's OK
                $beams["{$s_pos}"]['count'] += $quantum_beam['count'];
                continue;
            }

            if ($s !== '^') {
                $grid->render();
                echo "[ERROR] Unexpected situation! ({$s} at {$s_pos}\n";
                exit(1);
            }

            $sw_pos = $beam->add(SW);
            $se_pos = $beam->add(SE);

            if ($grid->get($sw_pos) === '|') {
                $beams["{$sw_pos}"]['count'] += $quantum_beam['count'];
            } else {
                $grid->set($sw_pos, '|');
                $beams["{$sw_pos}"] = ['pos' => $sw_pos, 'count' => $quantum_beam['count']];
            }

            if ($grid->get($se_pos) === '|') {
                $beams["{$se_pos}"]['count'] += $quantum_beam['count'];
            } else {
                $grid->set($se_pos, '|');
                $beams["{$se_pos}"] = ['pos' => $se_pos, 'count' => $quantum_beam['count']];
            }
        }
    }

    $grid->render();

    return array_column($final_beams, 'count');
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, vecho::$verbose, $part2);

    if ($part2) {
        $values = part2(...$parsed);
    } else {
        $values = part1(...$parsed);
    }

    if (vecho::$verbose) {
        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 21);
run_part1('input', false);
echo "\n";

run_part2('example', true, 40);
run_part2('input', false);
echo "\n";

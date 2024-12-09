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

class disk
{
    function __construct(public array $frag, public array $done = [])
    {
    }

    function find_free_space()
    {
        foreach ($this->frag as $block) {
            if ($block['id'] === null) {
                return true;
            }

            // move all blocks before the first free space to "done"
            $this->done[] = array_shift($this->frag);
        }

        // no more free space left.
        return false;
    }

    function refrag()
    {
        if (!$this->find_free_space() || empty($this->frag)) {
            return false;
        }

        $last_file = array_pop($this->frag);
        if (empty($last_file['id'])) {
            // found some free space at the end of the disk,
            // not a real file.
            return true;
        }

        $free_space = array_shift($this->frag);

        if ($last_file['len'] == $free_space['len']) {
            $this->done[] = $last_file;
            return true;
        }

        if ($last_file['len'] > $free_space['len']) {
            // split up the file
            $free_space['id'] = $last_file['id'];
            $this->done[] = $free_space;
            $last_file['len'] -= $free_space['len'];
            $this->frag[] = $last_file;
            return true;
        }

        $this->done[] = $last_file;
        $free_space['len'] -= $last_file['len'];
        array_unshift($this->frag, $free_space);
        return true;
    }

    function checksum()
    {
        $ret = [];

        $pos = 0;
        foreach ($this->done as $block) {
            for ($i = 0; $i < $block['len']; $i++) {
                $ret[] = $pos++ * $block['id'];
            }
        }

        return $ret;
    }

    function render(int $sleep = 100, bool $clear = true)
    {
        if (!vecho::$verbose) {
            return;
        }

        if ($clear) {
            clear_screen();
        }

        vecho::msg('DONE ', $this->done);
        vecho::msg('FRAG ', $this->frag);
        vecho::msg("\n");

        usleep($sleep * 1000);
    }
}

function parse(string $filename, bool $part2)
{
    $lines = file($filename);

    $output = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if (!empty($line)) {
            $output[] = str_split($line);
        }
    }

    if (count($output) > 1) {
        die('unexpected multi-line input');
    }

    return array_pop($output);
}

function main(string $filename, bool $part2)
{
    $parsed = parse($filename, $part2);

    if ($part2) {
        $values = part2($parsed);
    } else {
        $values = part1($parsed);
    }

    return array_sum($values);
}

function find_free_space(&$expanded)
{
}

function defrag($expanded)
{
    $last_file = array_pop($expanded['frag']);
}

function reconstruct($disk_map)
{
    $len = count($disk_map);

    $ret = [];
    $file_id = 0;

    $i = 0;
    while (array_key_exists($i, $disk_map)) {
        $ret[] = ['id' => $file_id++, 'len' => $disk_map[$i++]];

        $free_length = $disk_map[$i++] ?? null;
        if ($free_length !== null) {
            $ret[] = ['id' => null, 'len' => $free_length];
        }
    }

    return new disk($ret);
}

function part1($disk_map)
{
    vecho::msg($disk_map);

    $disk = reconstruct($disk_map);

    $disk->render(clear: false);
    $disk->find_free_space();
    $disk->render(clear: false);

    while ($disk->refrag()) {
        $disk->render();
    }

    return $disk->checksum();
}

function part2($disk_map)
{
    return [23];
}

run_part1('example', true, 1928);
run_part1('input', false);
echo "\n";

// run_part2('example', false, 34);
// run_part2('input', false);
echo "\n";

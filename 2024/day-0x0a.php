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

    function merge_free_space()
    {
        $prev_block = null;
        foreach ($this->frag as $idx => $block) {
            if ($block['len'] == 0) {
                array_splice($this->frag, $idx, 1, []);
                return true;
            }

            if ($prev_block) {
                if ($prev_block['id'] === null && $block['id'] === null) {
                    $backup = $block;
                    $block['len'] += $prev_block['len'];
                    array_splice($this->frag, $idx - 1, 2, [$block]);
                    return true;
                }
            }

            $prev_block = $block;
        }

        $last_block = end($this->frag);
        if ($last_block && $last_block['id'] === null) {
            array_pop($this->frag);
            return true;
        }

        return false;
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

    function find_empty_block_index(int $size)
    {
        foreach ($this->frag as $idx => $block) {
            if ($block['id'] === null && $block['len'] >= $size) {
                return $idx;
            }
        }

        return null;
    }

    function find_file_index(int $file_id)
    {
        for ($i = count($this->frag) - 1; $i >= 0; $i--) {
            if ($this->frag[$i]['id'] == $file_id) {
                return $i;
            }
        }

        return null;
    }

    function whole_file()
    {
        $all_file_ids = array_filter(array_column($this->frag, 'id'));

        $all_file_ids = array_reverse($all_file_ids);
        foreach ($all_file_ids as $file_id) {
            while ($this->merge_free_space()) {
            }

            if (!$this->find_free_space() || empty($this->frag)) {
                return;
            }

            $this->render();

            $file_idx = $this->find_file_index($file_id);
            $file_block = $this->frag[$file_idx] ?? null;
            if (empty($file_block)) {
                continue; // file already moved to done
            }

            $empty_idx = $this->find_empty_block_index($file_block['len']);
            if ($empty_idx === null) {
                // no free space fits this block
                continue;
            }
            $empty_block = $this->frag[$empty_idx];

            if ($empty_idx > $file_idx) {
                // we'd be making it worse, skip
                continue;
            }

            if ($file_block['len'] === $empty_block['len']) {
                $this->frag[$empty_idx] = $file_block;
                $this->frag[$file_idx] = $empty_block;
            } elseif ($file_block['len'] > $empty_block['len']) {
                $empty_block['id'] = $file_block['id'];
                $file_block['len'] -= $empty_block['len'];
                $this->frag[$empty_idx] = $empty_block;
                $this->frag[$file_idx] = $file_block;
            } else {
                $this->frag[$file_idx] = ['id' => null, 'len' => $file_block['len']];
                $empty_block['len'] -= $file_block['len'];
                array_splice($this->frag, $empty_idx, 1, [$file_block, $empty_block]);
            }
        }
    }

    function checksum()
    {
        $ret = [];

        $pos = 0;
        foreach ([$this->done, $this->frag] as $blocks) {
            foreach ($blocks as $block) {
                for ($i = 0; $i < $block['len']; $i++) {
                    $id = $block['id'] === null ? 0 : $block['id'];
                    $ret[] = $pos++ * $id;
                }
            }
        }

        vecho::msg('checksum', $ret);

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

        echo 'DONE';
        foreach ($this->done as $block) {
            if ($block['id'] === null) {
                echo ' ' . str_pad('.', (int) $block['len'], '.');
            } else {
                echo ' ' . str_pad((string) $block['id'], (int) $block['len'], '_', STR_PAD_LEFT);
            }
        }
        echo "\n";

        echo 'FRAG';
        foreach ($this->frag as $block) {
            if ($block['len'] == 0) {
                echo ' ~';
                continue;
            }

            if ($block['id'] === null) {
                echo ' ' . str_pad('.', (int) $block['len'], '.');
            } else {
                echo ' ' . str_pad((string) $block['id'], (int) $block['len'], '_', STR_PAD_LEFT);
            }
        }
        echo "\n";
        echo "\n";

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

function reconstruct($disk_map)
{
    $len = count($disk_map);

    $ret = [];
    $file_id = 0;

    $i = 0;
    while (array_key_exists($i, $disk_map)) {
        $ret[] = ['id' => $file_id++, 'len' => (int) $disk_map[$i++]];

        $free_length = $disk_map[$i++] ?? null;
        if ($free_length !== null) {
            $ret[] = ['id' => null, 'len' => (int) $free_length];
        }
    }

    return new disk($ret);
}

function part1($disk_map)
{
    $disk = reconstruct($disk_map);

    while ($disk->refrag()) {
        $disk->render();
    }

    return $disk->checksum();
}

function part2($disk_map)
{
    $disk = reconstruct($disk_map);

    $disk->render(clear: false);
    $disk->whole_file();

    return $disk->checksum();
}

run_part1('example', false, 1928);
run_part1('input', false);
echo "\n";

run_part2('example', false, 2858);
run_part2('input', false);
echo "\n";

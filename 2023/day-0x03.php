<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function is_digit($chr)
{
    if ($chr === null) {
        return false;
    }

    return $chr >= '0' && $chr <= '9';
}

function is_adjacent_to_symbol(array $grid, int $x, int $y)
{
    $neighbours = [
        [$x - 1, $y - 1],
        [$x + 0, $y - 1],
        [$x + 1, $y - 1],
        [$x - 1, $y + 0],
        [$x + 1, $y + 0],
        [$x - 1, $y + 1],
        [$x + 0, $y + 1],
        [$x + 1, $y + 1],
    ];

    foreach ($neighbours as $coord) {
        list($tx, $ty) = $coord;
        $n = $grid[$ty][$tx] ?? null;

        if ($n === null || $n === '.' || is_digit($n)) {
            continue;
        }

        // $n is a symbol.
        return true;
    }

    return false;
}

function find_engine_parts(array $grid, bool $verbose)
{
    $engine_parts = [];
    foreach ($grid as $y => $line) {
        $engine_part = '';
        $keep_part = false;

        foreach ($line as $x => $chr) {
            if (is_digit($chr)) {
                $engine_part .= $chr;
                if (is_adjacent_to_symbol($grid, $x, $y)) {
                    $keep_part = true;
                }
            } else {
                if (!empty($engine_part) && $keep_part) {
                    vecho($verbose, "$y\t $x\t Keeping engine part $engine_part\n");
                    $engine_parts[] = $engine_part;
                } elseif (!empty($engine_part)) {
                    vecho($verbose, "$y\t $x\t Discarding engine part $engine_part\n");
                }
                $engine_part = '';
                $keep_part = false;
            }
        }

        vecho($verbose, "\n");

        if (!empty($engine_part) && $keep_part) {
            vecho($verbose, "$y\t $x\t Keeping engine part $engine_part\n");
            $engine_parts[] = $engine_part;
        }
    }

    return $engine_parts;
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        $grid[] = str_split(trim($line));
    }

    return find_engine_parts($grid, $verbose);
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        //        print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', false, 4361);
run_part1('input', false);
echo "\n";

/*
run_part2('example', true, 2286);
run_part2('input', false);
echo "\n";
*/

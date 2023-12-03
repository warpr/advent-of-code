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

function is_adjacent_to_symbol(array $grid, int $x, int $y, array $gears)
{
    $ret = false;

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

        if ($n === '*') {
            $gears["($tx, $ty)"] = true;
        }

        // $n is a symbol.
        $ret = true;
    }

    return [$ret, $gears];
}

function find_engine_parts(array $grid, bool $verbose)
{
    $engine_parts = [];
    $gears_with_parts = [];

    foreach ($grid as $y => $line) {
        $engine_part = '';
        $next_to_gears = [];
        $keep_part = false;

        foreach ($line as $x => $chr) {
            if (is_digit($chr)) {
                $engine_part .= $chr;

                list($is_adjacent, $next_to_gears) = is_adjacent_to_symbol(
                    $grid,
                    $x,
                    $y,
                    $next_to_gears
                );
                if ($is_adjacent) {
                    $keep_part = true;
                }
            } else {
                if (!empty($engine_part) && $keep_part) {
                    vecho($verbose, "$y\t $x\t Keeping engine part $engine_part\n");
                    $engine_parts[] = $engine_part;
                    foreach ($next_to_gears as $gear_pos => $unused) {
                        $gears_with_parts[$gear_pos][] = $engine_part;
                    }
                } elseif (!empty($engine_part)) {
                    vecho($verbose, "$y\t $x\t Discarding engine part $engine_part\n");
                }
                $engine_part = '';
                $keep_part = false;
                $next_to_gears = [];
            }
        }

        vecho($verbose, "\n");

        if (!empty($engine_part) && $keep_part) {
            vecho($verbose, "$y\t $x\t Keeping engine part $engine_part\n");
            $engine_parts[] = $engine_part;
            foreach ($next_to_gears as $gear_pos => $unused) {
                $gears_with_parts[$gear_pos][] = $engine_part;
            }
        }
    }

    return [$engine_parts, $gears_with_parts];
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        $grid[] = str_split(trim($line));
    }

    list($engine_parts, $gears) = find_engine_parts($grid, $verbose);

    if ($part2) {
        $ret = [];
        foreach ($gears as $pos => $parts) {
            if (count($parts) > 1) {
                $ratio = array_reduce($parts, fn($memo, $p) => $memo * $p, 1);
                vecho($verbose, "Gear at $pos: $ratio\n");
                $ret[] = $ratio;
            }
        }
        return $ret;
    } else {
        return $engine_parts;
    }
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        // print_r(compact('values'));
    }

    return array_sum($values);
}

run_part1('example', true, 4361);
run_part1('input', false);
echo "\n";

run_part2('example', true, 467835);
run_part2('input', false);
echo "\n";

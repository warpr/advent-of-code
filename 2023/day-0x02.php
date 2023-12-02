<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function parse_rgb(string $str)
{
    $rgb = [0, 0, 0];

    foreach (['red', 'green', 'blue'] as $pos => $color) {
        if (preg_match("/([0-9]+) {$color}/", $str, $matches)) {
            $rgb[$pos] = (int) $matches[1];
        }
    }

    return $rgb;
}

function is_game_possible(array $bag, array $grabbed)
{
    foreach ($grabbed as $grab) {
        if ($grab[0] <= $bag[0] && $grab[1] <= $bag[1] && $grab[2] <= $bag[2]) {
            // all good
        } else {
            return false;
        }
    }

    return true;
}

function game_power(array $grabbed)
{
    $reds = array_column($grabbed, 0);
    $greens = array_column($grabbed, 1);
    $blues = array_column($grabbed, 2);

    return max($reds) * max($greens) * max($blues);
}

function parse(string $filename, bool $verbose, bool $part2)
{
    $bag = [12, 13, 14];

    $lines = file($filename);

    $ret = [];
    foreach ($lines as $line) {
        if (preg_match('/^Game ([0-9]+): (.*)/i', $line, $matches)) {
            $game_no = $matches[1];
            $grabbed = array_map('parse_rgb', explode(';', $matches[2]));

            if ($part2) {
                $pwr = game_power($grabbed);
                vecho($verbose, "Game $game_no power is $pwr\n");
                $ret[] = $pwr;
            } else {
                if (is_game_possible($bag, $grabbed)) {
                    vecho($verbose, "Game $game_no is possible\n");
                    $ret[] = (int) $game_no;
                } else {
                    vecho($verbose, "Game $game_no is NOT possible\n");
                }
            }
        }
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

run_part1('example', true, 8);
run_part1('input', false);
echo "\n";

run_part2('example', true, 2286);
run_part2('input', false);
echo "\n";

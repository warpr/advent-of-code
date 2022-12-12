<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/search.php';

function main($filename, bool $verbose)
{
    $lines = file($filename);

    return 23;
}

test_dijkstra();

/*
run_part1('example', false, 10605);
run_part1('input');
echo "\n";

run_part2('example', true, 2713310158);
run_part2('input');
echo "\n";
*/

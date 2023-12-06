<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function analyze_race(bool $verbose, int $time, int $distance)
{
    $ret = [];

    for ($held = 0; $held <= $time; $held++) {
        $speed = $held;
        $left = $time - $held;
        $run = $speed * $left;
        vecho(
            $verbose,
            "Button held $held, speed $speed, total distance is ($speed * $left): $run ..."
        );

        if ($run > $distance) {
            vecho($verbose, " a winning race\n");
            $ret[] = $held;
        } else {
            vecho($verbose, " NOT a winning race\n");
        }
    }

    return $ret;
}

/*
    Don't hold the button at all (that is, hold it for 0 milliseconds) at the start of the race. The boat won't move; it will have traveled 0 millimeters by the end of the race.
    Hold the button for 1 millisecond at the start of the race. Then, the boat will travel at a speed of 1 millimeter per millisecond for 6 milliseconds, reaching a total distance traveled of 6 millimeters.
    Hold the button for 2 milliseconds, giving the boat a speed of 2 millimeters per millisecond. It will then get 5 milliseconds to move, reaching a total distance of 10 millimeters.
    Hold the button for 3 milliseconds. After its remaining 4 milliseconds of travel time, the boat will have gone 12 millimeters.
    Hold the button for 4 milliseconds. After its remaining 3 milliseconds of travel time, the boat will have gone 12 millimeters.
    Hold the button for 5 milliseconds, causing the boat to travel a total of 10 millimeters.
    Hold the button for 6 milliseconds, causing the boat to travel a total of 6 millimeters.
    Hold the button for 7 milliseconds. That's the entire duration of the race. You never let go of the button. The boat can't move until you let go of the button. Please make sure you let go of the button so the boat gets to move. 0 millimeters.
*/

function parse(string $filename, bool $verbose, bool $part2)
{
    $lines = file($filename);

    $input = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }

        list($field, $values) = explode(':', $line);
        $input[$field] = array_values(array_filter(explode(' ', trim($values))));
    }

    $values = [];

    foreach ($input['Time'] as $race_no => $time) {
        $distance = $input['Distance'][$race_no];
        $winning_races = analyze_race($verbose, (int) $time, (int) $distance);
        $values[$race_no] = count($winning_races);
    }

    return $values;
}

function main(string $filename, bool $verbose, bool $part2)
{
    $values = parse($filename, $verbose, $part2);

    if ($verbose) {
        print_r(compact('values'));
    }

    return array_product($values);
}

run_part1('example', true, 288);
run_part1('input', false);
echo "\n";
/*
run_part2('example', true, 71503);
run_part2('input', false);
echo "\n";
*/

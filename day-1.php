<?php

$lines = file('day-1-input.txt');

function fuelRequired($mass) {
    $fuel = -2 + (int) floor($mass / 3.0);
    return $fuel < 0 ? 0 : $fuel;
}

function calculate($lines) {
    $totalFuel = [];
    $actualFuel = [];

    foreach ($lines as $idx => $line) {
        $module = (int) trim($line);
        $fuel = fuelRequired($module);

        $moreFuel = [ $fuel ];
        while ($fuel > 0) {
            $fuel = fuelRequired(end($moreFuel));
            $moreFuel[] = $fuel;
        }

        $moduleFuel = array_sum ($moreFuel);

        echo "module $idx | mass $module"
            . " | base fuel " . $moreFuel[0]
            . " | total fuel " . $moduleFuel . "\n";

        $totalFuel[] = $moreFuel[0];
        $actualFuel[] = $moduleFuel;
    }

    return [ array_sum($totalFuel), array_sum($actualFuel) ];
}

$fuelTest = [ "12", "14", "1969", "100756" ];

echo "\n";
echo "Preparing launch ... \n";

/* list($total, $actual) = calculate ($fuelTest); */
list($total, $actual) = calculate ($lines);
echo "===================\n";
echo "Total fuel: $total\n";
echo "Actual fuel: $actual\n";

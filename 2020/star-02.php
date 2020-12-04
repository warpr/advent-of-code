<?php

$lines = file('star-01-input.txt');
$copy = $lines;
$copy2 = $lines;


foreach ($lines as $first) {
    $first = trim($first);
    foreach ($copy as $second) {
        $second = trim($second);
        foreach ($copy2 as $third) {
            $third = trim($third);
            if (($first + $second + $third) == 2020) {
                echo $first . " + " . $second . " + " . $third . " = 2020\n";
                echo $first . " * " . $second . " * " . $third . " = " . ($first * $second * $third) . "\n";
            }
        }
    }
}

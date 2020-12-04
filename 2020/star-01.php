<?php

$lines = file('star-01-input.txt');
$copy = $lines;

foreach ($lines as $first) {
    $first = trim($first);
    foreach ($copy as $second) {
        $second = trim($second);
        if ($first + $second == 2020) {
            echo $first . " + " . $second . " = 2020\n";
            echo $first . " * " . $second . " = " . ($first * $second) . "\n";
        }
    }
}

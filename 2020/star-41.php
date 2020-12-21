<?php

function parse_line($line) {
    $clean = strtr(trim($line), '(,)', '   ');
    list($ingredients, $allergens) = explode("contains", $clean);

    return [
        array_filter(explode(" ", $ingredients)),
        array_filter(explode(" ", $allergens)),
    ];
}

function main($filename) {
    $lines = file($filename);

    $all = [];
    $seen = [];

    foreach ($lines as $line) {
        list($ingredients, $allergens) = parse_line($line);

        foreach ($ingredients as $i) {
            $seen[$i]++;
        }

        foreach ($allergens as $a) {
            if (array_key_exists($a, $all)) {
                $all[$a] = array_intersect($all[$a], $ingredients);
            } else {
                $all[$a] = $ingredients;
            }
        }
    }

    $maybe_contains_allergens = [];
    foreach ($all as $allergen => $ingredients) {
        foreach ($ingredients as $i) {
            $maybe_contains_allergens[$i] = true;
        }
    }

    // print_r($all);
    // print_r($maybe_contains_allergens);
    // print_r($seen);
    $total_without_allergens = 0;
    foreach ($seen as $ingredient => $count) {
        if (empty($maybe_contains_allergens[$ingredient])) {
            // echo "Adding $ingredient with count $count\n";
            $total_without_allergens += $count;
        }
    }

    echo "Total ingredients without allergens for $filename: $total_without_allergens\n";
}

main('star-41-example.txt');
main('star-41-input.txt');

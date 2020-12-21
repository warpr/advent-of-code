<?php

function parse_line($line) {
    $clean = strtr(trim($line), '(,)', '   ');
    list($ingredients, $allergens) = explode("contains", $clean);

    return [
        array_filter(explode(" ", $ingredients)),
        array_filter(explode(" ", $allergens)),
    ];
}

function resolve_dangerous(&$all) {
    $found = [];
    $changes = 0;

    foreach ($all as $allergen => $ingredients) {
        if (count($ingredients) === 1) {
            $i = array_pop($ingredients);
            $found[$i] = $allergen;
            // echo "$allergen is only found in {$i}\n";
        }
    }

    foreach ($found as $i => $a) {
        foreach ($all as $allergen => $ingredients) {
            if (($allergen != $a) && count($ingredients) > 1) {
                $all[$allergen] = array_diff($ingredients, [ $i ]);
                $changes++;
            }
        }
    }

    return $changes;
}

function main($filename) {
    $lines = file($filename);

    $all = [];

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

    while(resolve_dangerous($all));

    ksort($all);
    $dangerous = [];
    foreach ($all as $allergen => $ingredient) {
        $dangerous[] = array_pop($ingredient);
    }

    echo "\nDangerous ingredients for $filename:\n" . implode(",", $dangerous) . "\n";
}

main('star-41-example.txt');
main('star-41-input.txt');

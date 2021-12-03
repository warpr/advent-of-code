<?php

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $gamma = [];

    foreach ($lines as $line) {
        $bits = str_split($line);
        foreach ($bits as $pos => $val) {
            $gamma[$pos][$val]++;
        }
    }

    $final_gamma = '';
    $final_epsilon = '';
    foreach ($gamma as $pos => $counts) {
        if ($counts[0] > $counts[1]) {
            $final_gamma .= '0';
            $final_epsilon .= '1';
        } else {
            $final_gamma .= '1';
            $final_epsilon .= '0';
        }
    }

    $int_gamma = bindec($final_gamma);
    $int_epsilon = bindec($final_epsilon);

    print_r(compact('final_gamma', 'final_epsilon', 'int_gamma', 'int_epsilon'));

    return $int_gamma * $int_epsilon;
}


$result = run('star-05-example.txt');
if ($result !== 198) {
    echo "You broke the example.\n";
    die();
}

$output = run('star-05-input.txt');

echo "The answer is:  $output\n";


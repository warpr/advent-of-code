<?php

function execute($pos, $cmd, $arg)
{
    switch ($cmd) {
        case 'forward':
            $pos['x'] += $arg;
            $pos['z'] += $pos['aim'] * $arg;
            break;
        case 'down':
            $pos['aim'] += $arg;
            break;
        case 'up':
            $pos['aim'] -= $arg;
            break;
    }

    return $pos;
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $pos = ['x' => 0, 'aim' => 0, 'z' => 0];
    foreach ($lines as $line) {
        list($cmd, $arg) = explode(' ', $line);

        $pos = execute($pos, $cmd, $arg);
        //        echo "After ($cmd $arg) \t| x: " . $pos['x'] . " aim: " . $pos['aim'] . " z: " . $pos['z'] . "\n";
    }

    return $pos['x'] * $pos['z'];
}

$result = run('star-03-example.txt');
if ($result !== 900) {
    echo "You broke the example.\n";
    die();
}

$output = run('star-03-input.txt');

echo "The answer is:  $output\n";

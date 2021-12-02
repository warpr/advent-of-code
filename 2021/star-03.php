<?php

function execute($pos, $cmd, $arg)
{
    switch ($cmd) {
        case 'forward':
            $pos['x'] += $arg;
            break;
        case 'down':
            $pos['z'] += $arg;
            break;
        case 'up':
            $pos['z'] -= $arg;
            break;
    }

    return $pos;
}

function run($filename)
{
    $lines = array_map('trim', file($filename));

    $pos = ['x' => 0, 'y' => 0, 'z' => 0];
    foreach ($lines as $line) {
        list($cmd, $arg) = explode(' ', $line);

        $pos = execute($pos, $cmd, $arg);
        //        echo "After ($cmd $arg) \t| x: " . $pos['x'] . " z: " . $pos['z'] . "\n";
    }

    return $pos['x'] * $pos['z'];
}

$result = run('star-03-example.txt');
if ($result !== 150) {
    echo "You broke the example.\n";
    die();
}

$output = run('star-03-input.txt');

echo "The answer is:  $output\n";

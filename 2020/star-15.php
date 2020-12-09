<?php

function run($filename, $program) {
    $acc = 0;

    $ip = 0;
    $next = $program[$ip];
    while(!$next['ran']) {
        echo "ACC {$acc} | RUN [{$next['cmd']} {$next['arg']}]\n";
        $program[$ip]['ran'] = true;
        switch($next['cmd']) {
            case 'jmp':
                $ip += $next['arg'];
                break;
            case 'acc':
                $acc += $next['arg'];
            default:
                $ip++;
        }
        $next = $program[$ip];
    }
    echo "Program $filename halted.  Accumulator is $acc\n";
}

function main($filename) {
    $lines = file($filename);

    $code = [];
    foreach ($lines as $line) {
        list($cmd, $arg) = explode(" ", trim($line));
        $ran = false;
        $code[] = compact('cmd', 'arg', 'ran');
    }

    run($filename, $code);
}

main('star-15-example.txt');
main('star-15-input.txt');

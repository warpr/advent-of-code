<?php

function run($filename, $program, $patch) {
    $acc = 0;

    $ip = 0;
    $next = $program[$ip] ?? null;
    while(!$next['ran']) {
        // echo "ACC {$acc} | RUN [{$next['cmd']} {$next['arg']}]\n";
        $program[$ip]['ran'] = true;

        if ($patch === $ip) {
            if ($next['cmd'] === 'jmp') {
                $next['cmd'] = 'nop';
            } else if ($next['cmd'] === 'nop') {
                $next['cmd'] = 'jmp';
            }
        }

        switch($next['cmd']) {
            case 'jmp':
                $ip += $next['arg'];
                break;
            case 'acc':
                $acc += $next['arg'];
            default:
                $ip++;
        }
        $next = $program[$ip] ?? null;
        if (empty($next)) {
            echo "Program $filename terminated.  Accumulator is $acc\n";
            return $acc;
        }
    }
    echo "Program $filename halted.  Accumulator is $acc\n";
    throw new \Exception("halted");
}

function main($filename) {
    $lines = file($filename);

    $code = [];
    foreach ($lines as $line) {
        list($cmd, $arg) = explode(" ", trim($line));
        $ran = false;
        $code[] = compact('cmd', 'arg', 'ran');
    }

    foreach ($code as $idx => $line) {
        try {
            $acc = run($filename, $code, $idx);
            echo "Program $filename did not loop after patching line $idx, ";
            echo "accumulator is $acc\n";
        } catch (\Exception $e) {
            // ignore broker programs
        }
    }
}

main('star-15-example.txt');
main('star-15-input.txt');

<?php

function parse_mask($line) {
    $parts = explode("=", $line);

    $mask = [];
    $repl = [];
    foreach (str_split(trim($parts[1])) as $chr) {
        switch($chr) {
            case '0':
            case '1':
                $mask[] = '0';
                $repl[] = $chr;
                break;
            default:
                $mask[] = '1';
                $repl[] = '0';
        }
    }
    $ret = [
        'mask' => bindec(implode("", $mask)),
        'repl' => bindec(implode("", $repl)),
    ];

    return $ret;
}

function apply_bitmask($val, $mask) {
    return $val & $mask['mask'] | $mask['repl'];
}

function apply_operation(&$mem, &$bitmask, $line) {
    if (preg_match("/mask\s+=\s+([X01]+)$/", $line, $matches)) {
        $new_bitmask = parse_mask($line);
        $bitmask['mask'] = $new_bitmask['mask'];
        $bitmask['repl'] = $new_bitmask['repl'];
    } elseif (preg_match("/mem\[([0-9]+)\]\s+=\s+([0-9]+)$/", $line, $matches)) {
        $address = $matches[1];
        $value = $matches[2];
        $new_value = apply_bitmask((int) $value, $bitmask);
        // echo "mem[$address] = $new_value (from $value)\n";
        $mem[$address] = $new_value;
    }
}

function main($filename) {
    echo "----[$filename]----\n";
    $lines = file($filename);

    $mem = [];
    $bitmask = [ 'mask' => 0, 'repl' => 0 ];
    foreach ($lines as $line) {
        apply_operation($mem, $bitmask, $line);
    }

    echo "Memory sum: " . array_sum($mem) . "\n";
}

main('star-27-example.txt');
main('star-27-input.txt');

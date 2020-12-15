<?php

$verbose = false;

function all_permutations($float) {
    $permutations = bindec(implode("", array_filter($float)));
    $size = count(array_filter($float));

    $ret = [];
    for ($i = 0; $i <= $permutations; $i++) {
        $str = str_pad(decbin($i), $size, '0', STR_PAD_LEFT);

        $bitpos = 0;
        $value = [];
        foreach ($float as $idx => $chr) {
            if ($chr == 1) {
                $value[$idx] = $str[$bitpos++];
            } else {
                $value[$idx] = 0;
            }
        }
        $ret[] = $value;
    }

    return array_map(function ($item) {
        return bindec(implode("", $item));
    }, $ret);
}

function parse_mask($line) {
    global $verbose;

    $parts = explode("=", $line);

    $mask = [];
    $float = [];
    $or = [];
    foreach (str_split(trim($parts[1])) as $chr) {
        switch($chr) {
            case '0':
            case '1':
                $float[] = '0';
                $mask[] = '1';
                $or[] = $chr;
                break;
            default:
                $mask[] = '0';
                $float[] = '1';
                $or[] = '0';
        }
    }
    $ret = [
        'mask' => bindec(implode("", $mask)),
        'or' => bindec(implode("", $or)),
    ];

    $ret['permutations'] = all_permutations($float);
    if ($verbose) {
        printf("mask %06b\nor   %06b\n", $ret['mask'], $ret['or']);
        echo "perm";
        foreach ($ret['permutations'] as $p) {
            printf(" %06b", $p);
        }
        echo "\n";
    }

    return $ret;
}

function apply_bitmask($val, $mask) {
    global $verbose;

    $ret = [];
    foreach ($mask['permutations'] as $p) {
        $ret[] = $val & $mask['mask'] | $mask['or'] | $p;
        if ($verbose) {
            printf("%06b & %06b | %06b | %06b = %06b\n", $val, $mask['mask'], $mask['or'], $p, end($ret));
        }
    }
    return $ret;
}

function apply_operation(&$mem, &$bitmask, $line) {
    global $verbose;

    if (preg_match("/mask\s+=\s+([X01]+)$/", $line, $matches)) {
        $new_bitmask = parse_mask($line);
        $bitmask['mask'] = $new_bitmask['mask'];
        $bitmask['or'] = $new_bitmask['or'];
        $bitmask['permutations'] = $new_bitmask['permutations'];
    } elseif (preg_match("/mem\[([0-9]+)\]\s+=\s+([0-9]+)$/", $line, $matches)) {
        $address = $matches[1];
        $value = $matches[2];
        foreach (apply_bitmask((int) $address, $bitmask) as $new_address) {
            if ($verbose) {
                echo "mem[$new_address (from $address)] = $value\n";
            }
            $mem[$new_address] = $value;
        }
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

//main('star-27-example.txt');
main('star-28-example.txt');
main('star-27-input.txt');

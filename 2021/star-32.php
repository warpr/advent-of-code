<?php

function evaluate($cmd, $arg) {
    switch($cmd) {
        case 'SUM':
            return array_sum($arg);
        case 'MUL':
            $ret = 1;
            foreach ($arg as $val) {
                $ret *= $val;
            }
            return $ret;
        case 'MIN':
            return min($arg);
        case 'MAX':
            return max($arg);
        case 'IF>':
            return $arg[0] > $arg[1] ? 1 : 0;
        case 'IF<':
            return $arg[0] < $arg[1] ? 1 : 0;
        case 'IF=':
            return $arg[0] == $arg[1] ? 1 : 0;
        default:
            echo "$cmd not implemented\n";
            die();
    }
}

function byte($str) {
    $bits = sprintf("%08d", decbin(hexdec($str)));
    return str_split($bits);
}

function str_to_bits($str) {
    $parts = str_split($str, 2);

    foreach ($parts as $chunk) {
        $bits = byte($chunk);
        foreach ($bits as $bit) {
            yield $bit;
        }
    }
}

function grab_as_array(&$bits, $amount) {
    return array_splice($bits, 0, $amount);
}

function grab(&$bits, $amount) {
    $parts = grab_as_array($bits, $amount);
    $str = implode("", $parts);
    return bindec($str);
}

function decode_literal(&$bits) {
    $digits = [];
    do {
        $group_prefix = array_shift($bits);
        $digits[] = array_shift($bits);
        $digits[] = array_shift($bits);
        $digits[] = array_shift($bits);
        $digits[] = array_shift($bits);
    } while ($group_prefix);

    return bindec(implode("", $digits));
}

function decode_operator_packet(&$data, &$bits, $indent) {
    $length_type_id = grab($bits, 1);

    $ret = [];

    if ($length_type_id) {
        $num_of_sub_packets = grab($bits, 11);
        if ($data['verbose']) {
            echo "$indent  (decoding $num_of_sub_packets sub packets...)\n";
        }

        for ($i = 0; $i < $num_of_sub_packets; $i++) {
            $ret[] = decode_packet($data, $bits, $indent . '    ');
        }

        if ($data['verbose']) {
            echo "$indent  (done)\n";
        }
    } else {
        $size_in_bits = grab($bits, 15);
        $substream = grab_as_array($bits, $size_in_bits);
        if ($data['verbose']) {
            echo "$indent (decoding $size_in_bits bits of sub packets...)\n";
        }

        while (!empty($substream)) {
            $ret[] = decode_packet($data, $substream, $indent . '    ');
        }

        if ($data['verbose']) {
            echo "$indent (done)\n";
        }
    }

    return $ret;
}

function decode_packet(&$data, &$bits, $indent = '') {
    $version = grab($bits, 3);
    $type_id = grab($bits, 3);

    $data['versions'][] = $version;

    $command_mapping = [
        0 => "SUM",
        1 => "MUL",
        2 => "MIN",
        3 => "MAX",
        4 => "VAL",
        5 => "IF>",
        6 => "IF<",
        7 => "IF="
    ];

    $cmd = $command_mapping[$type_id] ?? '---';
    $prefix = $indent . "PACKET[v{$version} t{$type_id} $cmd] ";

    if ($cmd === "VAL") {
        $literal = decode_literal($bits);
        if ($data['verbose']) {
            echo "$prefix value: $literal\n";
        }
        return $literal;
    }

    if ($data['verbose']) {
        echo "$prefix\n";
    }

    $args = decode_operator_packet($data, $bits, $indent);
    return evaluate($cmd, $args);
}

function run($str, $verbose = false)
{
    $bits = iterator_to_array(str_to_bits($str));
    $data = [
        'verbose' => $verbose,
        'versions' => [],
    ];
    $value = decode_packet($data, $bits);

    return $value;
}

function main($str, $verbose = null, $expected = null)
{
    $actual = run($str, $verbose);
    if ($expected !== null) {
        if ($actual !== $expected) {
            echo "You broke $str, expected: $expected, actual: $actual.\n";
            die();
        } else {
            echo "Example answer OK: $actual\n";
        }
    } else {
        echo "The puzzle answer is:  $actual\n";
    }
}

main('C200B40A82', false, 3);
main('04005AC33890', false, 54);
main('880086C3E88112', false, 7);
main('CE00C43D881120', false, 9);
main('D8005AC2A8F0', false, 1);
main('F600BC2D8F', false, 0);
main('9C005AC2F8F0', false, 0);
main('9C0141080250320F1802104A08', false, 1);

$lines = array_map('trim', file('star-31-input.txt'));
main($lines[0], true, null);

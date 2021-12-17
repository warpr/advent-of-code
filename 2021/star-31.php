<?php

function byte($str)
{
    $bits = sprintf('%08d', decbin(hexdec($str)));
    return str_split($bits);
}

function str_to_bits($str)
{
    $parts = str_split($str, 2);

    foreach ($parts as $chunk) {
        $bits = byte($chunk);
        foreach ($bits as $bit) {
            yield $bit;
        }
    }
}

function grab_as_array(&$bits, $amount)
{
    return array_splice($bits, 0, $amount);
}

function grab(&$bits, $amount)
{
    $parts = grab_as_array($bits, $amount);
    $str = implode('', $parts);
    return bindec($str);
}

function decode_literal(&$bits)
{
    $digits = [];
    do {
        $group_prefix = array_shift($bits);
        $digits[] = array_shift($bits);
        $digits[] = array_shift($bits);
        $digits[] = array_shift($bits);
        $digits[] = array_shift($bits);
    } while ($group_prefix);

    return bindec(implode('', $digits));
}

function decode_operator_packet(&$data, &$bits, $indent)
{
    $length_type_id = grab($bits, 1);

    if ($length_type_id) {
        $num_of_sub_packets = grab($bits, 11);
        if ($data['verbose']) {
            echo "$indent (decoding $num_of_sub_packets sub packets...)\n";
        }

        for ($i = 0; $i < $num_of_sub_packets; $i++) {
            decode_packet($data, $bits, $indent . '  ');
        }

        if ($data['verbose']) {
            echo "$indent (done)\n";
        }
    } else {
        $size_in_bits = grab($bits, 15);
        $substream = grab_as_array($bits, $size_in_bits);
        if ($data['verbose']) {
            echo "$indent (decoding $size_in_bits bits of sub packets...)\n";
        }

        while (!empty($substream)) {
            decode_packet($data, $substream, $indent . '  ');
        }

        if ($data['verbose']) {
            echo "$indent (done)\n";
        }
    }
}

function decode_packet(&$data, &$bits, $indent = '')
{
    $version = grab($bits, 3);
    $type_id = grab($bits, 3);

    $data['versions'][] = $version;

    $prefix = $indent . "PACKET[v{$version} t{$type_id}] ";

    switch ($type_id) {
        case 4:
            $literal = decode_literal($bits);
            if ($data['verbose']) {
                echo "$prefix value: $literal\n";
            }
            break;
        default:
            if ($data['verbose']) {
                echo "$prefix\n";
            }
            decode_operator_packet($data, $bits, $indent);
    }
}

function run($str, $verbose = false)
{
    $bits = iterator_to_array(str_to_bits($str));
    $data = [
        'verbose' => $verbose,
        'versions' => [],
    ];
    decode_packet($data, $bits);

    return array_sum($data['versions']);
}

function main($str, $verbose = null, $expected = null)
{
    $actual = run($str, $verbose);
    if ($expected) {
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

main('D2FE28', false, 6);
main('8A004A801A8002F478', false, 16);
main('38006F45291200', true, 9);
main('EE00D40C823060', true, 14);
main('620080001611562C8802118E34', true, 12);
main('C0015000016115A2E0802F182340', true, 23);
main('A0016C880162017C3686B18A3D4780', true, 31);

$lines = array_map('trim', file('star-31-input.txt'));
main($lines[0], false);

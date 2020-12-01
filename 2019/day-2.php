<?php

function opcode($pos, &$program) {
    if ($pos > count($program)) {
        return 0;
    }

    $code = $program[$pos];

    if ($code == 99) {
        if ($verbose) {
            echo "$pos | EXIT\n";
        }
        return 0;
    }

    $mapping = [
        1 => "ADD",
        2 => "MUL",
        99 => "EXIT",
    ];
    $cmd = $mapping[$code];
    $src0 = $program[$pos+1];
    $src1 = $program[$pos+2];
    $dst = $program[$pos+3];

    $val0 = $program[$src0];
    $val1 = $program[$src1];

    if ($code == 1) {
        // add (src0, src1, dst)
        $program[$dst] = $val0 + $val1;
        $ret = 1;
    } else if ($code == 2) {
        // multiply (src0, src1, dst)
        $program[$dst] = $val0 * $val1;
        $ret = 1;
    } else {
        $ret = 0;
    }

    if ($verbose) {
        echo "$pos | $cmd"
           . " ($src0: " . $val0 . ")"
           . " ($src1: " . $val1 . ")"
           . " ($dst: " . $program[$dst] . ")\n";
    }

    // either opcode 99 (exit), or unknown opecode
    return $ret;
}

function intcode($sourceCode, $patch = []) {
    $program = explode(",", $sourceCode);

    foreach ($patch as $idx => $code) {
        if ($code != null) {
            $program[$idx] = $code;
        }
    }

    $pos = 0;
    while (opcode($pos, $program)) {
        $cmd = $mapping[$program[$pos]];
        $pos += 4;
    }

    return $program[0];
}

$testPrograms = [
    "1,0,0,0,99" => "2,0,0,0,99",
    "2,3,0,3,99" => "2,3,0,6,99",
    "2,4,4,5,99,0" => "2,4,4,5,99,9801",
    "1,1,1,4,99,5,6,0,99" => "30,1,1,4,2,5,6,0,99",
];

echo "\n";
echo "Rebuilding intcode computing device...\n";

foreach ($testPrograms as $program => $expected) {
    $input = $program;
    $output = intcode($program);
    echo "input $input | output $output | expected $expected\n";
}

echo "Running real input...\n";
$program = trim(file_get_contents("day-2-input.txt"));
$output = intcode($program, [null, 12, 2]);
echo $output."\n";

foreach (range(0, 99) as $noun) {
    foreach (range(0, 99) as $verb) {

        $output = intcode($program, [null, $noun, $verb]);

        if ($output == 19690720) {
            echo "noun: $noun, verb: $verb, result: " . (100 * $noun + $verb) . "\n";
        }
    }
}

echo "Done.\n";

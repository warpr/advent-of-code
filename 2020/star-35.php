<?php

function tokens($line) {
    $chars = str_split($line);

    $n = '';
    foreach ($chars as $chr) {
        if ($chr === ' ') {
            continue;
        } else if (is_numeric($chr)) {
            $n .= $chr;
        } else {
            if (!empty($n)) {
                yield $n;
                $n = '';
            }
            yield $chr;
        }
    }

    if (!empty($n)) {
        yield $n;
    }
}

function parens($tokens) {
    $depth = 0;
    $sub = [];
    foreach ($tokens as $token) {
        if ($token == '(') {
            $depth++;
        } elseif ($token == ')') {
            $exp = $sub[$depth];
            unset($sub[$depth]);
            $depth--;
            $sub[$depth][] = $exp;
        } else {
            $sub[$depth][] = $token;
        }
    }

    return $sub;
}

function calc($line) {
    $tokens = parens(iterator_to_array(tokens($line)));

    $result = evaluate($tokens);
    echo "Line: $line = $result\n";

    return $result;
}

function evaluate($tokens) {
    $input = $tokens;

    while($token = current($tokens)) {
        if (is_array($token)) {
            $stack[] = evaluate($token);
        } else if (is_numeric($token)) {
            $stack[] = $token;
        } elseif (in_array($token, ['*', '+', '-', '/'])) {
            $first = array_pop($stack);
            $second = next($tokens);
            if (is_array($second)) {
                $second = evaluate($second);;
            }
            if ($token == '*') {
                $stack[] = $first * $second;
            } elseif ($token == '+') {
                $stack[] = $first + $second;
            } elseif ($token == '-') {
                $stack[] = $first - $second;
            } elseif ($token == '/') {
                $stack[] = $first / $second;
            }
        } else {
            echo "Invalid token: $token\n";
            die();
        }

        next($tokens);
    }

    // print_r([ 'input' => $input, 'stack' => $stack ]);

    return $stack[0];
}

function main($filename) {
    $lines = file($filename);

    $total = 0;
    foreach ($lines as $line) {
        $total += calc(trim($line));
    }

    echo "Total for $filename is $total\n";
}

main('star-35-example.txt');
main('star-35-input.txt');


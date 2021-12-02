#!/usr/bin/env deno run --allow-read

function offset(list, n) {
    return Array(n).fill(null).concat(list);
}

function is_empty_pair(pair) {
    for (const p of pair) {
        if (!p && p !== 0) {
            return true;
        }
    }
    return false;
}

function* zip(lists) {
    const first = lists.shift();

    for (const [idx, item] of first.entries()) {
        const pair = [ item ];
        for (const l of lists) {
            pair.push(l.at(idx));
        }

        if (!is_empty_pair(pair)) {
            yield pair;
        }
    }
}

function main(filename) {
    const values = Deno.readTextFileSync(filename).split("\n").map(line => line.trim()).filter(line => line !== '');

    let increases = 0;
    for (const pair of zip([ offset(values, 1), values ])) {
        if (pair[0] < pair[1]) {
            increases++;
        }
    }

    return increases;
}

const result = main("star-01-example.txt");
if (result !== 7) {
    console.log("You broke the example.");
}

const output = main("star-01-input.txt");

console.log('Answer is: ', output);

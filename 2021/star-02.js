#!/usr/bin/env deno run --allow-read

function offset(list, n) {
    return Array(n).fill(null).concat(list);
}

function is_empty_window(window) {
    for (const p of window) {
        if (!p && p !== 0) {
            return true;
        }
    }
    return false;
}

function* zip(lists) {
    const first = lists.shift();

    for (const [idx, item] of first.entries()) {
        const window = [item];
        for (const l of lists) {
            window.push(l.at(idx));
        }

        if (!is_empty_window(window)) {
            yield window;
        }
    }
}

function sum(items) {
    return items.reduce((memo, x) => x + memo, 0);
}

function main(filename) {
    const values = Deno.readTextFileSync(filename)
        .split('\n')
        .map((line) => line.trim())
        .filter((line) => line !== '')
        .map((line) => parseInt(line, 10));

    let increases = 0;
    const summed = [];
    for (const window of zip([offset(values, 2), offset(values, 1), values])) {
        summed.push(sum(window));
    }

    for (const pair of zip([offset(summed, 1), summed])) {
        if (pair[0] < pair[1]) {
            increases++;
        }
    }

    return increases;
}

const result = main('star-01-example.txt');
if (result !== 5) {
    console.log('You broke the example.');
}

const output = main('star-01-input.txt');
console.log('Answer is: ', output);

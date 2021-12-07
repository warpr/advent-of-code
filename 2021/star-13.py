import sys
import pprint

def fuel_required(pos, crabs):
    sum = 0
    for crab in crabs:
        sum = sum + abs(crab - pos)

    return sum

def run(filename, verbose):
    lines = []

    with open(filename) as file:
        lines = [line.strip() for line in file.readlines()]

    positions = [int(v) for v in lines[0].split(',')]
    if (verbose):
        pprint.pprint(positions)

    max = positions[0]
    min = positions[0]

    for p in positions:
        max = p if p > max else max
        min = p if p < min else min

    best = fuel_required(min, positions)
    for i in xrange(min, max + 1):
        fuel = fuel_required(i, positions)
        if (verbose):
            print('Fuel required for pos %d: %d' % (i, fuel))

        if (fuel < best):
            best = fuel
            if (verbose):
                print('Best so far is position %d with a cost of %d' % (i, fuel))

    return best

expected = 37;
actual = run('star-13-example.txt', True);
if actual == expected:
    print("Example answer OK: %d." % (actual));
else:
    print('You broke the example, expected: %d, actual: %d.' % (expected, actual))
    sys.exit()


output = run('star-13-input.txt', False);

print("The puzzle answer is: %d" % (output));

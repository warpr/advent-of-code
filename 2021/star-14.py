import sys
import pprint

def sequence(max):
    ret = [];

    current = 0;
    for i in xrange(0, max + 1):
        current = current + i
        ret.append(current)

    return ret


def fuel_required(pos, crabs, costs):
    sum = 0
    for crab in crabs:
        move = abs(crab - pos)
        sum = sum + costs[move]

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

    costs = sequence(max)
    if (verbose):
        cost_str = ",".join([ str(c) for c in costs ])
        print("costs: " + cost_str)

    best = fuel_required(min, positions, costs)
    for i in xrange(min, max + 1):
        fuel = fuel_required(i, positions, costs)
        if (verbose):
            print('Fuel required for pos %d: %d' % (i, fuel))

        if (fuel < best):
            best = fuel
            if (verbose):
                print('Best so far is position %d with a cost of %d' % (i, fuel))

    return best

expected = 168;
actual = run('star-13-example.txt', True);
if actual == expected:
    print("Example answer OK: %d." % (actual));
else:
    print('You broke the example, expected: %d, actual: %d.' % (expected, actual))
    sys.exit()


output = run('star-13-input.txt', False);

print("The puzzle answer is: %d" % (output));

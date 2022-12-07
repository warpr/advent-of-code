<?php

declare(strict_types=1);

require_once __DIR__ . '/common.php';

function run_chdir(array $cwd, string $path): array
{
    if ($path === '/') {
        return [];
    } elseif ($path === '..') {
        array_pop($cwd);
        return $cwd;
    } else {
        $cwd[] = $path;
        return $cwd;
    }
}

function recalc_size($dir)
{
    if (!is_array($dir)) {
        return $dir;
    }
    $type = $dir['__type'] ?? null;
    if ($type === 'file') {
        return $dir;
    }

    $size = array_sum(array_column($dir, '__size'));
    $dir['__size'] = $size;
    $dir['__type'] = 'dir';

    return $dir;
}

function main($filename, $verbose)
{
    $lines = file($filename);

    $cwd = [];
    $tree = [];

    foreach ($lines as $line) {
        if (preg_match('/([0-9]+) (.*)/', $line, $matches)) {
            $filesize = (int) $matches[1];
            $filename = $matches[2];

            set_by_path($tree, array_merge($cwd, [$filename]), [
                '__type' => 'file',
                '__size' => $filesize,
            ]);
        } elseif (preg_match('/\$ cd (.*)/', trim($line), $matches)) {
            $cwd = run_chdir($cwd, $matches[1]);
            $path = $matches[1];
        }
    }

    $tree = array_edit_recursive($tree, 'recalc_size');
    if ($verbose) {
        // print_r($tree);
    }

    $dirs = [];
    $tree = array_edit_recursive($tree, function ($item) use (&$dirs) {
        if (is_array($item) && $item['__type'] === 'dir') {
            $dirs[] = $item['__size'];
        }

        return $item;
    });

    return $dirs;
}

function part1($filename, $verbose)
{
    $dirs = main($filename, $verbose);
    $total = array_sum(array_filter($dirs, fn($val) => $val < 100000));
    return $total;
}

function part2($filename, $verbose)
{
    $dirs = main($filename, $verbose);
    sort($dirs);

    $disk = 70000000;
    $needed = 30000000;
    $used = array_pop($dirs); // largest is inherently always the root

    $unused = $disk - $used;
    foreach ($dirs as $dir_size) {
        $total_free = $unused + $dir_size;

        if ($total_free >= $needed) {
            if ($verbose) {
                echo "Need $needed units, deleting $dir_size units would result in total free space of $total_free units, which is enough\n";
            }
            return $dir_size;
        } else {
            if ($verbose) {
                echo "Need $needed units, deleting $dir_size units would result in total free space of $total_free units (not enough)\n";
            }
        }
    }

    return 0;
}

run_part1('example', true, 95437);
run_part1('input');
run_part2('example', true, 24933642);
run_part2('input');

echo "\n";

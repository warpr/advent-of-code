<?php
/**
 *   Copyright (C) 2024  Kuno Woudt <kuno@frob.nl>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of copyleft-next 0.3.1.  See copyleft-next-0.3.1.txt.
 *
 *   SPDX-License-Identifier: copyleft-next-0.3.1
 */

declare(strict_types=1);

class vecho
{
    static bool $verbose = false;

    static function msg(...$args)
    {
        $output = [];

        foreach ($args as $a) {
            if (is_string($a)) {
                $output[] = $a;
            } else {
                $output[] = json_encode($a);
            }
        }

        if (static::$verbose) {
            echo implode(' ', $output) . "\n";
        }
    }
}

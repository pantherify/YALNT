<?php

namespace Pantherify\YALNT\Common;

class PathManipulation
{
    public static function joinPaths()
    {
        $args = func_get_args();
        $paths = array();
        foreach ($args as $arg) {
            $paths = array_merge($paths, (array)$arg);
        }

        $paths = array_map(function ($p) {
            return trim($p, "/");
        }, $paths);
        $paths = array_filter($paths);

        return join('/', $paths);
    }

    public static function runPrettier()
    {
        $base_path = config('yalnt.nova.path');
        system("prettier --write " . base_path($base_path) . "/**/*.php");
    }
}

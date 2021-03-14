<?php
declare(strict_types = 1);
//-----------------------------------------------------------------------------
// NinjaLinks Copyright Ekaterina <scripts@robotess.net>
// http://scripts.robotess.net
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------

namespace RobotessNet;

spl_autoload_register([new Autoloader(), 'autoload']);

final class Autoloader
{
    private string $path;

    public function __construct()
    {
        $this->path = __DIR__ . DIRECTORY_SEPARATOR;
    }

    public function autoload(string $class): void
    {
        if (strpos($class, 'RobotessNet\\') !== 0) {
            return;
        }

        $classNameWithoutRobotessNS = str_replace('RobotessNet\\', '', $class);

        $filename = $this->path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR,
                $classNameWithoutRobotessNS) . '.php';

        if(file_exists($filename)) {
            include $filename;
        }
    }
}

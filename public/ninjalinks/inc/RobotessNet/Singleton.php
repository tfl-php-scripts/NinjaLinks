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

trait Singleton
{
    /**
     * @var self|null
     */
    private static $instance;

    private function __construct()
    { /***/ }

    /**
     * @return self
     */
    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
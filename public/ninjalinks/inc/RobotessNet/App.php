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

final class App
{
    use Singleton;

    public function getVersion(): string
    {
        return '[Robotess Fork] v. 1.0';
    }

    public function getFormed(): string
    {
        return 'NinjaLinks '.$this->getVersion().' ; http://scripts.robotess.net/projects/ninja-links';
    }

    public function getLinkWithOriginal(): string
    {
        return $this->getLink() . ' (originally by <a href="http://www.jemjabella.co.uk/scripts" target="_blank">Jem</a>)';
    }

    public function getLink(): string
    {
        return '<a href="https://scripts.robotess.net" target="_blank" title="PHP Scripts ported to PHP 7: NinjaLinks, Enthusiast, Siteskin, CodeSort, FanUpdate, Listing Admin, etc.">NinjaLinks ' . $this->getVersion() . '</a>';
    }
}

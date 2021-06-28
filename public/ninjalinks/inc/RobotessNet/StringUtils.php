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

use function addslashes;
use function htmlentities;
use function strip_tags;
use function strtolower;
use function trim;
use const ENT_QUOTES;

final class StringUtils
{
    use Singleton;

    public function cleanIfNotNull(?string $data, bool $leaveHtml = false): ?string
    {
        if ($data === null) {
            return null;
        }

        return $this->clean($data, $leaveHtml);
    }

    public function clean(?string $data, bool $leaveHtml = false): string
    {
        if ($data === null) {
            return '';
        }

        if ($leaveHtml) {
            $data = trim($data);
        } else {
            $data = trim(htmlentities(strip_tags($data), ENT_QUOTES));
        }

        $data = addslashes($data);

        return $data;
    }

    public function cleanNormalize(?string $data): string
    {
        if ($data === null) {
            return '';
        }

        return strtolower($this->clean($data));
    }

    public function isEmailValid(?string $email): bool
    {
        if ($email === null) {
            return false;
        }

        return (bool)preg_match("/^([A-Za-z0-9-_.+]+)@(([A-Za-z0-9-_]+\.)+)([a-zA-Z]{2,})$/i", $email);
    }
}

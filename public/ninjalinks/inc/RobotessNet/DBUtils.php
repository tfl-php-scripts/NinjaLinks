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

use Exception;
use SQLConnection;
use function sprintf;

final class DBUtils
{
    use Singleton;

    public function safeExecuteUpdateStep(SQLConnection $connection, string $query, string $dbpref, string $stepDescription): void
    {
        try {
            $queryResult = $connection->query(sprintf($query, $dbpref), false);
            if ($queryResult) {
                echo sprintf("<p>%s: success</p>", $stepDescription);
            } else {
                echo sprintf("<p style=\"color: red;\">%s: error - check database settings and try again. MySQL error: %s</p>",
                    $stepDescription, $connection->error());
            }
        } catch (Exception $e) {
            echo sprintf("<p style=\"color: red;\">%s: error - check database settings and try again. Exception message: %s</p>",
                $stepDescription, $e);
        }
    }
}

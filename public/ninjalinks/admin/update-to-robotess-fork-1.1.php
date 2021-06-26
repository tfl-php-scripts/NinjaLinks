<?php
declare(strict_types=1);
//-----------------------------------------------------------------------------
// NinjaLinks Copyright Ekaterina <scripts@robotess.net>
// http://scripts.robotess.net
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------

use RobotessNet\DBUtils;

include('header.php');

$steps = [
    "Version 1.0" => [
        [
            "ALTER TABLE `%slinks` CHANGE `ownername` `ownername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `owneremail` `owneremail` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `linkname` `linkname` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `linkurl` `linkurl` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `linkbutton` `linkbutton` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `dateadded` `dateadded` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `dateupdated` `dateupdated` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;",
            "Updating links table",
        ],
        [
            "ALTER TABLE `%supdates` CHANGE `entry` `entry` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `datetime` `datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;",
            "Updates table fix"
        ],
    ],
    "Version 1.1" => [
        [
            "ALTER TABLE `%slinks` CHANGE `category` `category` INT NULL DEFAULT NULL, CHANGE `hits` `hits` INT NOT NULL DEFAULT '0'",
            "Fixing links table",
        ],
        ["ALTER TABLE `%scategories` CHANGE `catparent` `catparent` INT NULL DEFAULT NULL", "Fixing categories table"],
    ],
];

foreach ($steps as $version => $subSteps) {
    echo '<legend>'.$version.': </legend>';
    echo '<fieldset>';
    foreach ($subSteps as [$query, $stepDescription]) {
        DBUtils::instance()->safeExecuteUpdateStep($mysql, $query, $dbpref, $stepDescription);
    }
    echo '</fieldset>';
}
?>

    <p>If there are no red errors above, consider this upgrade a success! :) You must now <b>delete that file</b></p>

<?php
include('../footer.php');

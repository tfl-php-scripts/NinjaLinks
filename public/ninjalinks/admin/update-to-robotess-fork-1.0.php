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

include('header.php');

$alterLinksTable = $mysql->query("ALTER TABLE `" . $dbpref . "links` CHANGE `ownername` `ownername` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `owneremail` `owneremail` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `linkname` `linkname` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `linkurl` `linkurl` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `linkbutton` `linkbutton` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `dateadded` `dateadded` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `dateupdated` `dateupdated` DATETIME NULL DEFAULT CURRENT_TIMESTAMP;");
if ($alterLinksTable) {
    echo '<p>Links table has been successfully updated.</p>';
} else {
    echo '<p style="color: red;">Links table could not be updated - check database settings and try again. MySQL error: '.$mysql->error().'</p>';
}

$alterUpdatesTable = $mysql->query("ALTER TABLE `" . $dbpref . "updates` CHANGE `entry` `entry` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `datetime` `datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;");
if ($alterUpdatesTable) {
    echo '<p>Updates table has been successfully updated.</p>';
} else {
    echo '<p style="color: red;">Updates table could not be updated - check database settings and try again. MySQL error: '.$mysql->error().'</p>';
}
?>

<p>If there are no red errors above, consider this upgrade a success! :) You must now <b>delete that file</b></p>

<?php
include('../footer.php');

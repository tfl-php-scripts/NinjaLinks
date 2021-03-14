<?php
declare(strict_types=1);
//-----------------------------------------------------------------------------
// NinjaLinks Copyright � Jem Turner 2007, 2008 unless otherwise noted
// http://www.jemjabella.co.uk/
//
// Contributor (since 2021): Ekaterina <scripts@robotess.net>
// http://scripts.robotess.net
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License. See README.txt
// or LICENSE.txt for more information.
//-----------------------------------------------------------------------------

use RobotessNet\StringUtils;

include('header.php');
$validtypes = ["ip", "email"];

switch (getView()) {
    case "delete":
        if (!isset($_POST['zomgkey']) || $_POST['zomgkey'] != md5($opt['salt'] . date("H"))) {
            exit('<p>Invalid token. <a href="manage_categories.php">Try again</a>?</p>');
        }

        doDelete("banned", $_POST['del']);
        break;
    case "add":
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = null;

            if (!in_array($_POST['type'], $validtypes)) {
                $error = "Invalid ban type; please try again.";
            }

            $cleanType = StringUtils::instance()->cleanNormalize($_POST['type'] ?? '');
            $cleanBanValue = StringUtils::instance()->cleanNormalize($_POST['banvalue'] ?? '');

            if ($cleanType == "email" && !StringUtils::instance()->isEmailValid($cleanBanValue)) {
                $error = "Ban value doesn't match legitimate e-mail address; please try again.";
            } elseif ($cleanType == "ip" && !preg_match('/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/i',
                    $cleanBanValue)) {
                $error = "Ban value doesn't match legitimate IP address; please try again.";
            }

            if ($error == null) {
                $addBan = $mysql->query("INSERT INTO `" . $dbpref . "banned` (`type`, `value`) VALUES ('" .$cleanType . "', '" . $cleanBanValue . "')");

                if ($addBan) {
                    echo '<p><b class="red">Note:</b> The ban was successfully added. <a href="manage_banned.php">Return to Manage Banned IPs/Emails</a>.</p>';
                } else {
                    echo '<p><b class="red">Note:</b> There was an error, the ban could not be added. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
                }
            }
        }

        if (isset($error)) {
            echo '<p class="red">' . $error . '</p>';
        }

        ?>
        <form action="manage_banned.php?v=add" method="post" id="linkform">
            <fieldset>
                <label for="type">Update Title*</label>
                <select name="type" id="title" required>
                    <option value="ip">IP Address</option>
                    <option value="email">Email Address</option>
                </select>

                <label for="banvalue">Ban Value (IP/Email)*</label>
                <input type="text" name="banvalue" id="banvalue" required/>

                <input type="submit" name="submit" class="button" value="Add Ban"/>
            </fieldset>
        </form>
        <?php
        break;
    case "edit":
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            exit('<p>Invalid category ID</p>');
        }

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = null;

            // check the POSTed md5 hash of salt + link id against the hash of salt plus GET id (if the GET has been tampered with, will fail)
            if ($_POST['banid'] != md5($opt['salt'] . $_GET['id'])) {
                exit('<p>Ban item IDs do not match</p>');
            }

            if (!in_array($_POST['type'], $validtypes)) {
                $error = "Invalid ban type; please try again.";
            }

            $cleanType = StringUtils::instance()->cleanNormalize($_POST['type'] ?? '');
            $cleanBanValue = StringUtils::instance()->cleanNormalize($_POST['banvalue'] ?? '');

            if ($cleanType == "email" && !StringUtils::instance()->isEmailValid($cleanBanValue)) {
                $error = "Ban value doesn't match legitimate e-mail address; please try again.";
            } elseif ($cleanType == "ip" && !preg_match('/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/i',
                    $cleanBanValue)) {
                $error = "Ban value doesn't match legitimate IP address; please try again.";
            }

            if ($error == null) {

                $editBan = $mysql->query("UPDATE `" . $dbpref . "banned` SET
				`type` = '" . $cleanType . "',
				`value` = '" . $cleanBanValue . "'
			WHERE `id` = " . (int)$_GET['id'] . " LIMIT 1");

                if ($editBan) {
                    echo '<p><b class="red">Note:</b> The ban was successfully edited. <a href="manage_banned.php">Return to Manage Banned IPs/Emails</a>.</p>';
                } else {
                    echo '<p><b class="red">Note:</b> There was an error, the ban could not be edited. Please try again; or, contact your host for help if the MySQL connection has failed.</p>';
                }
            }
        }

        if (isset($error)) {
            echo '<p class="red">' . $error . '</p>';
        }

        $getbanned = $mysql->query("SELECT * FROM `" . $dbpref . "banned` WHERE `id` = " . (int)$_GET['id'] . " LIMIT 1");
        if ($mysql->count($getbanned) == 1) {
            $ban = $mysql->fetchAssoc($getbanned);
            ?>
            <form action="manage_banned.php?v=edit&amp;id=<?= $ban['id'] ?>" method="post" id="linkform">
                <fieldset>
                    <input type="hidden" name="banid" id="banid" value="<?= md5($opt['salt'] . $ban['id']) ?>"/>

                    <label for="type">Update Title*</label>
                    <select name="type" id="type" required>
                        <option value="ip"<?php if ($ban['type'] == "ip") {
                            echo ' selected="selected"';
                        } ?>>IP Address
                        </option>
                        <option value="email"<?php if ($ban['type'] == "email") {
                            echo ' selected="selected"';
                        } ?>>Email Address
                        </option>
                    </select>

                    <label for="banvalue">Ban Value (IP/Email)*</label>
                    <input type="text" name="banvalue" id="banvalue" value="<?= $ban['value'] ?>" required/>

                    <input type="submit" name="submit" class="button" value="Edit Ban"/>
                </fieldset>
            </form>
            <?php
        } else {
            echo "<p>Oh noes! There ain't no ban to be edited with that ID, matey.</p>";
        }
        break;
    default:
        ?>
        <h1>Manage Banned IPs/Emails</h1>
        <p><a href="?v=add">Add a Ban</a></p>

        <?php
        $from = ((getPage() * $opt['perpage']) - $opt['perpage']);

        $adminBaned = $mysql->query("SELECT * FROM `" . $dbpref . "banned` ORDER BY `id` DESC  LIMIT " . $from . ", " . $opt['perpage']);
        ?>
        <form action="manage_banned.php?v=delete" method="post">
            <p>
                <input type="hidden" name="zomgkey" id="zomgkey" value="<?= md5($opt['salt'] . date("H")) ?>"/>
            </p>
            <table>
                <tr>
                    <th>Ban Value</th>
                    <th>Ban Type</th>
                    <th colspan="2">Admin</th>
                </tr>
                <?php
                $rowCount = 0;
                while ($b = $mysql->fetchAssoc($adminBaned)) {
                    if ($rowCount % 2) {
                        $rowClass = 'linkeven';
                    } else {
                        $rowClass = 'linkodd';
                    }

                    echo '
			<tr class="' . $rowClass . '"><td>' . $b['type'] . '</td> 
			<td>' . $b['value'] . '</td> 
			<td class="center">
				<a href="manage_banned.php?v=edit&amp;id=' . $b['id'] . '"><img src="../njicons/edit.gif" title="edit this" alt="edit" /></a>
			</td>
			<td class="center"><input type="checkbox" name="del[' . $b['id'] . ']" value="' . $b['id'] . '" /></td>
		</tr>' . "\r\n";

                    ++$rowCount;
                }
                ?>
            </table>
            <p class="right"><input type="submit" name="submit" value="Delete"/></p>
        </form>
        <?php
        getPagination($mysql->single("SELECT COUNT(`id`) FROM `" . $dbpref . "banned`"));
        break;
}
include('../footer.php');
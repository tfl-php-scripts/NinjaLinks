<?php
declare(strict_types=1);
//-----------------------------------------------------------------------------
// NinjaLinks Copyright � Jem Turner 2007-2009 unless otherwise noted
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

require('config.php');
include('header.php');
?>

    <h1>Update Your Link</h1>

<?php
if (isset($_GET['linkid']) && is_numeric($_GET['linkid'])) {
    if (isset($_GET['key']) && strlen($_GET['key']) == 32) {
        $findLink = $mysql->query("SELECT * FROM `" . $dbpref . "links` WHERE `id` = '" . (int)$_GET['linkid'] . "'");
        if ($mysql->count($findLink) > 0) {
            $link = $mysql->fetchAssoc($findLink);
            if ($_GET['key'] == md5($link['linkname'] . $link['owneremail'] . date("Y-m-d"))) {
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    $error = null;

                    // check the POSTed md5 hash of salt + link id against the hash of salt plus GET id (if the GET has been tampered with, will fail)
                    if ($_POST['linkid'] != md5($_GET['key'] . $_GET['linkid'])) {
                        exit('<p>Link IDs do not match</p>');
                    }

                    foreach ($_POST as $key => $value) {
                        if (in_array($key, $opt['required']) && empty($value)) {
                            $error = $key . ' is a required field.';
                        }
                    }

                    if (!StringUtils::instance()->isEmailValid($_POST['email'])) {
                        $error = "Invalid E-mail Address, please fix and try again.";
                    } elseif (!preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i',
                        $_POST['linkurl'])) {
                        $error = "Invalid Link URL, please fix and try again.";
                    } elseif (!is_numeric($_POST['linkcat'])) {
                        $error = "Invalid Category, please fix and try again.";
                    }

                    if ($opt['allowdesc'] == 0 && !empty($_POST['linkdesc'])) {
                        $error = "Link Description shouldn't be filled in!";
                    }

                    if ($error == null) {
                        foreach ($_POST as $key => $value) {
                            $$key = StringUtils::instance()->clean($value);
                        }

                        if (!isset($linktags)) {
                            $linktags = null;
                        }

                        if (!isset($linkdesc)) {
                            $linkdesc = null;
                        }

                        $editLink = $mysql->query("UPDATE `" . $dbpref . "links` SET
							`ownername` = '" . $ownername . "',
							`owneremail` = '" . strtolower($email) . "',
							`linkname` = '" . $linkname . "',
							`linkurl` = '" . $linkurl . "', 
							`linkbutton` = '',
							`linkdesc` = '" . $linkdesc . "',
							`linktags` = '" . $linktags . "',
							`category` = '" . (int)$linkcat . "',
							`approved` = 0,
							`dateupdated` = NOW()
						WHERE `id` = " . (int)$_GET['linkid'] . " LIMIT 1");

                        if ($editLink) {
                            $message = "Edited link pending approval in " . $opt['dirname'] . "\r\n\r\n";

                            $message .= "Owner: " . $ownername . " (" . $email . ")\r\n";
                            $message .= "Site Name: " . $linkname . "\r\n";
                            $message .= "Site URL: " . strtolower($linkurl) . "\r\n";
                            $message .= "Link Tags: " . $linktags . "\r\n";
                            $message .= "Description: " . $linkdesc . "\r\n\r\n";

                            $message .= "You must approve this link before it will re-appear in your directory.\r\n";
                            $message .= $opt['dirlink'] . "admin/manage_links.php\r\n\r\n";

                            $message .= "Submission info:\r\n";
                            $message .= "Date: " . TODAY . "\r\n";
                            $message .= "User IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";
                            $message .= "Browser: " . $_SERVER['HTTP_USER_AGENT'];

                            doEmail($opt['email'], "Link '" . $linkname . "' Edited", $message);

                            echo '<p><b class="red">Note:</b> The link was successfully edited and is now awaying re-approval.</p>';
                        } else {
                            echo '<p><b class="red">Note:</b> There was an error, the link could not be updated. Please try again.</p>';
                        }
                    }
                }
                ?>
                <form action="updatelink.php?linkid=<?= (int)$_GET['linkid'] ?>&amp;key=<?= $_GET['key'] ?>"
                      method="post" id="linkform">
                    <fieldset>
                        <input type="hidden" name="linkid" id="linkid" value="<?= md5($_GET['key'] . $link['id']) ?>"/>

                        <label for="ownername">Your Name</label>
                        <input type="text" name="ownername" id="ownername" value="<?= $link['ownername'] ?>"/>

                        <label for="email">E-mail Address</label>
                        <input type="email" name="email" id="email" value="<?= $link['owneremail'] ?>"/>

                        <label for="linkname">Link Name</label>
                        <input type="text" name="linkname" id="linkname" value="<?= $link['linkname'] ?>"/>

                        <label for="linkurl">Link URL</label>
                        <input type="url" name="linkurl" id="linkurl" value="<?= $link['linkurl'] ?>"/>

                        <?php if ($opt['allowdesc'] == 1) : ?>
                            <label for="linkdesc">Link Description</label>
                            <textarea name="linkdesc" id="linkdesc" rows="10"
                                      cols="5"><?= $link['linkdesc'] ?></textarea>
                        <?php endif; ?>

                        <?php if (isset($opt['allowtags']) && $opt['allowtags'] == 1) : ?>
                        <label for="linktags">Link Tags</label>
                        <input type="text" name="linktags" id="linktags" value="<?= $link['linktags'] ?>" />
						<?php endif; ?>

						<label for=" linkcat">Link Category</label>
                        <select name="linkcat" id="linkcat">
                            <?php
                            getAllCats('dropdown', '&nbsp;&nbsp;', $link['category']);
                            ?>
                        </select>

                        <input type="submit" name="submit" class="button" value="Update Link"/>
                    </fieldset>
                </form>
                <?php
                include('footer.php');
                exit;
            }

            $error = "Could not find link for updating: invalid link key";
        } else {
            $error = "Could not find link for updating: invalid link ID";
        }
    } else {
        $error = "Could not find link for updating: invalid link key";
    }
}
if (isset($_GET['viewsites']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $error = null;
    checkBots();

    $email = StringUtils::instance()->cleanNormalize($_POST['email'] ?? '');

    if (!StringUtils::instance()->isEmailValid($email)) {
        $error = "Invalid E-mail Address, please fix and try again.";
    }

    if ($error === null) {
        $findSites = $mysql->query("SELECT `id`, `linkname`, `linkurl` FROM `" . $dbpref . "links` WHERE LOWER(`owneremail`) = '" . $email . "'");
        if ($mysql->count($findSites) > 0) {
            $message = "Thank you for requesting a link update from " . $opt['dirname'] . "\r\n\r\n";
            $message .= "The following sites were found to be associated with your e-mail address; please click the link under each one to begin editing:\r\n";
            while ($r = $mysql->fetchAssoc($findSites)) {
                $message .= "Link: " . $r['linkname'] . " - " . $r['linkurl'] . "\r\n";
                $message .= $opt['dirlink'] . "updatelink.php?linkid=" . $r['id'] . "&key=" . md5($r['linkname'] . $email . date("Y-m-d")) . "\r\n\r\n";
            }
            $message .= "Each link is only valid until the end of the day that the edit request was made. Please update your links straight away.";

            doEmail($email, "Link Update Request from " . $opt['dirname'], $message);
        }
        echo '<p>Thank you for requesting a link update. If there are any links in the database registered to your e-mail address, you will receive an e-mail with further instructions on how to update each link as required.</p>';
    }
}
if (isset($error)) {
    echo '<p class="red">' . $error . '</p>';
}
?>

    <form action="updatelink.php?viewsites" method="post" id="linkform">
        <fieldset>
            <label for="email">E-mail Address *</label>
            <input type="email" name="email" id="email" required/>

            <input type="submit" name="submit" class="button" value="Find Links"/>
        </fieldset>
    </form>


<?php
include('footer.php');
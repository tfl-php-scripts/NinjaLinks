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

    <h1>Add Your Link</h1>

<?php
$cleanName = null;
$cleanEmail = null;
$cleanLinkName = null;
$cleanLinkUrl = null;
$cleanLinkDesc = null;
$cleanLinkTags = null;
$catId = null;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = [];
    $karma = 0;

    if (checkBots() === true) {
        doError("No bots allowed.");
    }

    foreach ($_POST as $key => $value) {
        if (strcasecmp(trim($value), '') === 0 && in_array($key, $opt['required'])) {
            $errors[$key] = $key . ' is a required field.';
        }

        $karma += spamCount($value) * 2;
    }

    $cleanName = StringUtils::instance()->clean($_POST['ownername'] ?? '');
    $cleanEmail = StringUtils::instance()->cleanNormalize($_POST['email'] ?? '');
    $cleanLinkName = StringUtils::instance()->clean($_POST['linkname'] ?? '');
    $cleanLinkUrl = StringUtils::instance()->clean($_POST['linkurl'] ?? '');
    $cleanLinkDesc = StringUtils::instance()->clean($_POST['linkdesc'] ?? '');

    if (isset($_POST['linkdesc']) && strcasecmp(trim($_POST['linkdesc']), '') !== 0) {
        $karma += exploitKarma($_POST['linkdesc']);
    }

    if (isset($_POST['email']) && strcasecmp(trim($_POST['email']), '') !== 0) {
        $karma += badMailKarma($_POST['email']);
    }

    if (strcasecmp(trim($_POST['linkdesc']), '') !== 0 && preg_match("/(<.*>)/i", $_POST['linkdesc'])) {
        $karma += 2;
    }
    if (strcasecmp(trim($_POST['ownername']),
            '') !== 0 && (strlen($_POST['ownername']) < 3 || strlen($_POST['ownername']) > 15)) {
        $karma += 2;
    }
    if (strcasecmp(trim($_POST['linkurl']), '') !== 0 && strlen($_POST['linkurl']) > 30) {
        $karma += 2;
    }
    if (strcasecmp(trim($_POST['linkdesc']), '') !== 0 && substr_count($_POST['linkdesc'], 'http') >= 1) {
        $karma += 2;
    }
    if (strcasecmp(trim($_POST['linkdesc']), '') !== 0 && substr_count($_POST['linkdesc'], 'http') >= 3) {
        $karma += 4;
    }

    if (!isset($errors['ownername']) && preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i", $_POST['ownername'])) {
        $errors['ownername'] = "Name contains invalid characters. Please fix and try again.";
    }

    if (!isset($errors['email']) && !StringUtils::instance()->isEmailValid($cleanEmail)) {
        $errors['email'] = "Invalid E-mail Address, please fix and try again.";
    }

    if (!isset($errors['linkurl']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i',
            $_POST['linkurl'])) {
        $errors['linkurl'] = "Invalid Link URL, please fix and try again. Link must start with 'http://' and contain no special characters.";
    }

    $catId = (int)StringUtils::instance()->clean($_POST['linkcat'] ?? '');
    if (!isset($errors['linkcat']) && !is_numeric($_POST['linkcat'])) {
        $errors['linkcat'] = "Invalid Category, please fix and try again. Do not tamper with the form.";
    }

    if (isBanned($_POST['email']) === true) {
        $errors['email'] = "There was an error whilst trying to add your link to the directory.";
    }

    if (!isset($errors['linkdesc']) && $opt['allowdesc'] == 0 && !empty($_POST['linkdesc'])) {
        $errors['linkdesc'] = "Link Description shouldn't be filled in! Do not tamper with the form.";
    }

    if ($opt['allowdupes'] == 0 && !empty($_POST['linkurl'])) {
        $findLink = $mysql->query("SELECT * FROM `" . $dbpref . "links` WHERE `linkurl` LIKE '%" . StringUtils::instance()->clean($_POST['linkurl']) . "%' LIMIT 1");
        if ($mysql->count($findLink) == 1) {
            $errors['dupelinkurl'] = "Duplicate link detected - please only add your website once.";
        }
    }

    if ($karma > $opt['maxkarma']) {
        $errors['maxkarma'] = "Your link seems awfully spammy, and has been rejected.";
    }

    $cleanLinkTags = StringUtils::instance()->clean($_POST['linktags'] ?? '');
    if (count($errors) === 0) {
        $addLink = $mysql->query("INSERT INTO `" . $dbpref . "links` (`ownername`, `owneremail`, `linkname`, `linkurl`, `linkbutton`, `linkdesc`, `linktags`, `category`, `approved`) VALUES ('" . $cleanName . "', '" . $cleanEmail . "', '" . $cleanLinkName . "', '" . $cleanLinkUrl . "', '', '" . $cleanLinkDesc . "', '" . $cleanLinkTags . "', '" . $catId . "', 0)");
        if ($addLink) {
            if ($opt['emailnew'] == 1) {
                $message = "New link pending approval in " . html_entity_decode($opt['dirname']) . "\r\n\r\n";

                $message .= "Owner: " . html_entity_decode($cleanName) . " (" . $cleanEmail . ")\r\n";
                $message .= "Site Name: " . html_entity_decode($cleanLinkName) . "\r\n";
                $message .= "Site URL: " . $cleanLinkUrl . "\r\n";
                $message .= "Link Tags: " . html_entity_decode($cleanLinkTags) . "\r\n";
                $message .= "Description: " . html_entity_decode($cleanLinkDesc) . "\r\n\r\n";

                $message .= "You must approve this link before it will appear in your directory.\r\n";
                $message .= $opt['dirlink'] . "admin/manage_links.php\r\n\r\n";

                $message .= "Submission info:\r\n";
                $message .= "Date: " . TODAY . "\r\n";
                $message .= "Karma: " . $karma . "\r\n";
                $message .= "User IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";
                $message .= "Browser: " . $_SERVER['HTTP_USER_AGENT'];

                doEmail($opt['email'], "Link added to " . $opt['dirname'], $message);
            }

            echo '<p>Thank you for submitting a link to my directory. Your link should be added shortly if it passes moderation.</p>';
        } else {
            echo '<p>There was an error, the link could not be added. Please contact the website admin for more information.</p>';
        }

        $cleanName = null;
        $cleanEmail = null;
        $cleanLinkName = null;
        $cleanLinkUrl = null;
        $cleanLinkButtonUrl = null;
        $cleanLinkDesc = null;
        $cleanLinkTags = null;
        $catId = null;
    } else {
        echo '<p class="red">' . implode('<br/>', $errors) . '</p>';
    }
}
global $opt;
?>
    <form action="addlink.php" method="post" id="linkform">
        <fieldset>
            <label for="ownername">Your Name<?= in_array('ownername',
                    $opt['required'], true) ? '*' : '' ?></label>
            <input type="text" name="ownername" id="ownername"
                   value="<?= $cleanName ?? ''; ?>" <?= in_array('ownername',
                $opt['required'], true) ? ' required' : '' ?>/>

            <label for="email">E-mail Address<?= in_array('email',
                    $opt['required'], true) ? '*' : '' ?></label>
            <input type="email" name="email" id="email" value="<?= $cleanEmail ?? ''; ?>" <?= in_array('email',
                $opt['required'], true) ? ' required' : '' ?>/>

            <label for="linkname">Link Name<?= in_array('linkname',
                    $opt['required'], true) ? '*' : '' ?></label>
            <input type="text" name="linkname" id="linkname"
                   value="<?= $cleanLinkName ?? ''; ?>" <?= in_array('linkname',
                $opt['required'], true) ? ' required' : '' ?>/>

            <label for="linkurl">Link URL<?= in_array('linkurl',
                    $opt['required'], true) ? '*' : '' ?></label>
            <input type="url" name="linkurl" id="linkurl"
                   value="<?= $cleanLinkUrl ?? ''; ?>" <?= in_array('linkurl',
                $opt['required'], true) ? ' required' : '' ?>/>

            <?php if (isset($opt['allowdesc']) && $opt['allowdesc'] == 1) : ?>
                <label for="linkdesc">Link Description<?= in_array('linkdesc',
                        $opt['required'], true) ? '*' : '' ?></label>
                <textarea name="linkdesc" id="linkdesc" rows="10"
                          cols="5" <?= in_array('linkdesc',
                    $opt['required'], true) ? ' required' : '' ?>><?= $cleanLinkDesc ?? ''; ?></textarea>
            <?php endif; ?>

            <?php if (isset($opt['allowtags']) && $opt['allowtags'] == 1) : ?>
                <label for="linktags">Link Tags<?= in_array('linktags',
                        $opt['required'], true) ? '*' : '' ?></label>
                <input type="text" name="linktags" id="linktags"
                       value="<?= $cleanLinkTags ?? ''; ?>" <?= in_array('linktags',
                    $opt['required'], true) ? ' required' : '' ?>/>
            <?php endif; ?>

            <label for="linkcat">Link Category*</label>
            <select name="linkcat" id="linkcat" required>
                <?php
                getAllCats('dropdown', '&nbsp;&nbsp;', $catId);
                ?>
            </select>

            <input type="submit" name="submit" class="button" value="Add Link"/>
        </fieldset>
    </form>

<?php
include('footer.php');

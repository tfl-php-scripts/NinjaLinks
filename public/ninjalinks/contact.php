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

$error_msg = null;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $karma = 0;

    if (checkBots() === true) {
        doError("No bots allowed.");
    }

    foreach ($_POST as $key => $value) {
        $karma += spamCount($value) * 2;
    }

    $karma += exploitKarma($_POST['comments']);
    $karma += badMailKarma($_POST['email']);

    if (!empty($_POST['comments']) && preg_match("/(<.*>)/i", $_POST['comments'])) {
        $karma += 2;
    }
    if (!empty($_POST['name']) && (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 15)) {
        $karma += 2;
    }
    if (strlen($_POST['url']) > 30) {
        $karma += 2;
    }
    if (!empty($_POST['comments']) && substr_count($_POST['comments'], 'http') >= 1) {
        $karma += 2;
    }
    if (!empty($_POST['comments']) && substr_count($_POST['comments'], 'http') >= 3) {
        $karma += 4;
    }

    $cleanEmail = StringUtils::instance()->cleanNormalize($_POST['email'] ?? '');

    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['comments'])) {
        $error_msg .= "Name, e-mail and comments are required fields. \n";
    } elseif (strlen($_POST['name']) > 15) {
        $error_msg .= "The name field is limited at 15 characters. Your first name or nickname will do! \n";
    } elseif (!preg_match("/^[A-Za-z' -]*$/", $_POST['name'])) {
        $error_msg .= "The name field must not contain special characters. \n";
    } elseif (!StringUtils::instance()->isEmailValid($cleanEmail)) {
        $error_msg .= "That is not a valid e-mail address. \n";
    } elseif (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i',
            $_POST['url'])) {
        $error_msg .= "Invalid Link URL, please fix and try again.\n";
    }

    if ($karma > $opt['maxkarma']) {
        $error_msg = "Your message seems awfully spammy, and has been rejected. \n";
    }

    if ($error_msg == null) {

        $cleanName = StringUtils::instance()->clean($_POST['name'] ?? '');
        $cleanEmail = StringUtils::instance()->cleanNormalize($_POST['email'] ?? '');
        $cleanUrl = StringUtils::instance()->clean($_POST['url'] ?? '');
        $cleanComments = StringUtils::instance()->clean($_POST['comments'] ?? '');

        foreach ($_POST as $key => $val) {
            $$key = StringUtils::instance()->clean($val);
        }

        $message = "You received this e-mail message through your directory: \r\n\r\n";

        $message .= "Name: " . $cleanName . "\r\n";
        $message .= "E-mail: " . $cleanEmail . "\r\n";
        $message .= "Website: " . $cleanUrl . "\r\n";
        $message .= "Comments: " . $cleanComments . "\r\n\r\n";

        $message .= "Message Info\r\n";
        $message .= "Date: " . TODAY . "\r\n";
        $message .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";
        $message .= "Browser: " . $_SERVER['HTTP_USER_AGENT'];

        if (doEmail($opt['email'], "Mail From " . $opt['dirname'], $message, "\r\nReply-To: " . $cleanEmail)) {
            echo "<p>Your mail was successfully sent.</p>";
        } else {
            echo "<p>Your mail could not be sent this time.</p>";
        }
    }
}

if ($error_msg != null) {
    echo "<p><strong style='color: red;'>ERROR:</strong><br />";
    echo nl2br($error_msg) . "</p>";
}
?>
    <h1>Contact Directory Owner</h1>

    <form action="contact.php" method="post" id="linkform">
        <fieldset>
            <label for="name">Name*</label>
            <input type="text" name="name" id="name" value="" required/>

            <label for="email">E-mail*</label>
            <input type="email" name="email" id="email" value="" required/>

            <label for="url">Website</label>
            <input type="url" name="url" id="url" value=""/>

            <label for="comments">Comments*</label>
            <textarea name="comments" id="comments" required></textarea>

            <input type="submit" name="submit" id="submit" class="button" value="Send"/>
        </fieldset>
    </form>

    <br class="clearer"/>

<?php
include('footer.php');
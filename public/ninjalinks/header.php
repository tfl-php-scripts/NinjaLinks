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
?>
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta name="language" content="en"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="stylesheet.css" rel="stylesheet" type="text/css"/>

    <title><?= $opt['dirname'] ?></title>

</head>
<body>

<div id="container">

    <?php
    if (basename($_SERVER['SCRIPT_NAME']) != "install.php") {
        checkInstall();
    }
    ?>

    <ul id="navigation">
        <li><a href="<?= $opt['dirlink'] ?>">Home</a></li>
        <li><a href="addlink.php">Add Link</a></li>
        <li><a href="links.php">View Links</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>

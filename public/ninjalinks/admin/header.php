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
session_start();
require('../config.php');
if (doCheckLogin() === false) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="../stylesheet.css" rel="stylesheet" type="text/css"/>
    <script src="rss.js" type="text/javascript"></script>

    <title><?= $opt['dirname'] ?> Admin Panel</title>

</head>
<body>

<div id="container">

    <ul id="navigation">
        <li><a href="index.php">Admin Home</a></li>
        <li><a href="manage_links.php">Manage Links</a></li>
        <li><a href="manage_updates.php">Manage Updates</a></li>
        <li><a href="manage_categories.php">Manage Categories</a></li>
        <li><a href="manage_banned.php">Manage Banned IPs/Emails</a></li>
        <li><a href="../index.php">View Directory</a></li>
    </ul>
<?php
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

require('config.php');
include('header.php');

if (empty($dbuser) || empty($dbpass) || empty($dbname))
	exit("You must fill out the username, password and database name fields in config.php");

$createLinks = $mysql->query("CREATE TABLE `".$dbpref."links` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`ownername` VARCHAR(50) NOT NULL, 
	`owneremail` VARCHAR(255) NOT NULL,
	`linkname` VARCHAR(250) NOT NULL,
	`linkurl` VARCHAR(255) NOT NULL,
	`linkbutton` VARCHAR(255) NOT NULL,
	`linkdesc` TEXT NOT NULL,
	`linktags` TEXT NOT NULL,
	`category` int(11) NOT NULL default '0',
	`rating` TINYINT(1) NOT NULL default '0',
	`approved` TINYINT(1) NOT NULL default '0',
	`premium` TINYINT(1) NOT NULL default '0',
	`dateadded` DATETIME NOT NULL default '1970-01-01',
	`dateupdated` DATETIME NOT NULL default '1970-01-01',
	`hits` int(11) NOT NULL default '0',
	PRIMARY KEY (`id`)
)");
if ($createLinks)
	echo '<p>Links table successfully created.</p>';
else
	echo '<p style="color: red;">Links table could not be created - check database settings and try again.</p>';


$createUpdates = $mysql->query("CREATE TABLE `".$dbpref."updates` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(250) NOT NULL default '',
	`entry` TEXT NOT NULL,
	`datetime` DATETIME NOT NULL default '1970-01-01',
	PRIMARY KEY (`id`)
)");
if ($createUpdates)
	echo '<p>Updates table successfully created.</p>';
else
	echo '<p style="color: red;">Updates table could not be created - check database settings and try again.</p>';


$createCategories = $mysql->query("CREATE TABLE `".$dbpref."categories` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`catname` VARCHAR(25) NOT NULL default '',
	`catparent` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)");
if ($createCategories) {
	echo '<p>Categories table successfully created.</p>';
	
	$addcats = $mysql->query("INSERT INTO `".$dbpref."categories` (`catname`) VALUES ('Blog'), ('Directory'), ('Clique'), ('Fansite'), ('Forum')");
	if ($addcats)
		echo '<p>Default categories added.</p>';
	else
		echo '<p style="color: red;">Could not add default categories - please add categories via the control panel</p>';	
} else {
	echo '<p style="color: red;">Categories table could not be created - check database settings and try again.</p>';
}


$createBanned = $mysql->query("CREATE TABLE `".$dbpref."banned` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`type` ENUM('ip', 'email') NOT NULL default 'ip',
	`value` VARCHAR(255) NOT NULL default '',
	PRIMARY KEY (`id`)
)");
if ($createBanned)
	echo '<p>Banned table successfully created.</p>';
else
	echo '<p style="color: red;">Banned table could not be created - check database settings and try again.</p>';
?>
	
	<p>If there are no red errors above, consider this install a success! :) You must now <b>delete install.php</b> and <a href="admin/">log in to the admin panel</a> to manage your script.</p>

<?php	
include('footer.php');
?>
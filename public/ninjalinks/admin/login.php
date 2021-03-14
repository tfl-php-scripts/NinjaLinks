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

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require('../config.php');
    if ($_POST['username'] == $opt['user'] && $_POST['password'] == $opt['pass']) {
        // password is correct
        session_start();

        $_SESSION['nlLogin'] = md5($opt['user'] . md5($opt['pass'] . $opt['salt']));
        header("Location: index.php");
        exit;
    }

    exit("<p>Invalid username and/or password.</p>");
}
?>
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta name="language" content="en"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>NinjaLinks Robotess Fork Login Form</title>
</head>
<body>
<form method="post" action="login.php">

    Username:<br>
    <input type="text" name="username" id="username" required><br>
    Password:<br>
    <input type="password" name="password" id="password" required><br>

    <input type="submit" name="submit" value="Login">

</form>
</body>
</html>
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

use RobotessNet\App;

$isDashboard = true;
include('header.php'); ?>

    <h1>Script version and server info</h1>

    <p>You are currently using NinjaLinks <?= RobotessNet\App::instance()->getVersion() ?>. Please make sure you
        always keep your script up-to-date. Link to the latest version is available on
        <a href="https://scripts.robotess.net/projects/ninja-links" target="_blank"
           title="PHP Script NinjaLinks for PHP 7">project's page</a>.</p>

    <h2>Server info (useful for debugging and reporting issues)</h2>
    <p class="script-version">NinjaLinks <?= RobotessNet\App::instance()->getVersion() ?>
        <br/> PHP: <?= PHP_VERSION ?>
        <br/> Showing MySQLi errors: <?= $mysql->isReportErrors() ? 'YES' : 'NO' ?> - to set to YES, replace
    <pre>$mysql = new SQLConnection($dbhost, $dbuser, $dbpass, $dbname);</pre> in your config.php with
    <pre>$mysql = SQLConnection::instance($dbhost, $dbuser, $dbpass, $dbname)->setReportErrors(true);</pre>
    </p>
    <hr/>
<?php
if (getStats("pending") > 0) {
    ?>
    <p>You have <?= getStats("pending") ?> pending link(s).</p>

    <?php
} else {
    echo '<p>You have no links pending approval.</p>';
}

if (file_exists("../install.php")) {
    echo '<p class="red"><b>PLEASE DELETE install.php FROM YOUR NINJALINKS DIRECTORY!</b></p>';
}

?>
    <h1>Script Updates</h1>
    <script>
        showRss(`<?= App::instance()->getRssUrl() ?>?date=<?=date('Y-m-d');?>`);
    </script>

    <div id="rss-feed-robotess-net">
        Nothing here yet. Please check <a href="<?= App::instance()->getRssUrl() ?>" target="_blank">feed</a> manually.
    </div>

<?php
include('../footer.php');

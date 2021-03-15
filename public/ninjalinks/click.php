<?php
declare(strict_types=1);
include('config.php');
if (isset($_GET['link']) && is_numeric($_GET['link'])) {
    $link = $mysql->query("SELECT `linkurl` FROM `" . $dbpref . "links` WHERE `id` = " . (int)$_GET['link'] . " LIMIT 1");
    if ($mysql->count($link) === 1) {
        $link = $mysql->fetchAssoc($link);
        $mysql->query("UPDATE `" . $dbpref . "links` SET `hits` = `hits` + 1 WHERE `id` = " . (int)$_GET['link'] . " LIMIT 1");
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header($_SERVER["SERVER_PROTOCOL"] . " 301 Moved Permanently");
        header("Location: " . $link['linkurl'], true, 301);
        exit('Could not forward, <a href="' . $link['linkurl'] . '">click here to continue</a>');
    }
    exit("Invalid link ID.");
}

exit("Invalid link ID.");

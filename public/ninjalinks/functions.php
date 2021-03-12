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

require_once('inc/RobotessNet/Autoloader.php');

// IMPORTANT FUNCTIONS -- DO NOT EDIT
class SQLConnection
{
    private mysqli $mysqli_connect;

    public function __construct($host, $user, $pass, $dbnm)
    {
        $this->mysqli_connect = mysqli_connect($host, $user, $pass) or doError('no-connect');
        $select = mysqli_select_db($this->mysqli_connect, $dbnm) or doError('no-select-db');

        $this->query("SET NAMES 'utf8'");
    }

    public function query($query)
    {
        $result = mysqli_query($this->mysqli_connect, $query);
        if ($result === false) {
            doError('query-fail');
        }

        return $result;
    }

    public function single($query)
    {
        $result = $this->query($query);
        if ($result === false) {
            exit('Could not run query: ' . mysqli_error($this->mysqli_connect));
        }
        $array = mysqli_fetch_array($result);
        return $array[0] ?? null;
    }

    public function count(mysqli_result $query): int
    {
        return mysqli_num_rows($query);
    }

    /**
     * @param mysqli_result $query
     * @return string[]|null
     */
    public function fetchAssoc(mysqli_result $query): ?array
    {
        return mysqli_fetch_assoc($query);
    }
}

$mysql = new SQLConnection($dbhost, $dbuser, $dbpass, $dbname);


define('TODAY', gmdate("Y-m-d H:i:s"));


// DATA MANIPULATION AND VALIDATION FUNCTIONS
function clean($input, $fordb = 'yes')
{
    $input = str_replace("<3", "&lt;3", $input);
    $input = htmlentities(strip_tags(urldecode($input)), ENT_NOQUOTES, 'UTF-8');

    if ($fordb == "yes") {
        $input = escape($input);
    }

    return trim($input);
}

function escape($input)
{
    $input = addslashes($input);

    return $input;
}

function nl2p($pee, $br = 1)
{
    /* THANK YOU MATT - http://ma.tt & http://wordpress.org */
    $pee .= "\n"; // just to make things a little easier, pad the end
    $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
    // Space things out a little
    $allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|style|script|object|input|param|p|h[1-6])';
    $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
    $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
    $pee = str_replace(["\r\n", "\r"], "\n", $pee); // cross-platform newlines
    $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
    $pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
    $pee = preg_replace('|<p>\s*?</p>|', '',
        $pee); // under certain strange conditions it could create a P of entirely whitespace
    $pee = preg_replace('|<p>(<div[^>]*>\s*)|', "$1<p>", $pee);
    $pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
    $pee = preg_replace('|<p>|', "$1<p>", $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
    if ($br) {
        $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
        $pee = str_replace('<PreserveNewline />', "\n", $pee);
    }
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
    $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
    $pee = preg_replace("|\n</p>$|", '</p>', $pee);
    /**/
    return $pee;
}

function make_excerpt($entry, $excerpt_length, $extension, $cutword = 'false')
{
    $entry = strip_tags($entry);
    $cutmarker = "**cut_here**";
    if (is_array($entry) && strlen($entry) > $excerpt_length) {
        $entry = wordwrap($entry, $excerpt_length, $cutmarker, $cutword);
        $entry = explode($cutmarker, $entry);
        $entry = $entry[0] . $extension;
    }
    return $entry;
}

function checkBots()
{
    $isbot = false;

    $bots = [
        "Indy",
        "Blaiz",
        "Java",
        "libwww-perl",
        "Python",
        "OutfoxBot",
        "User-Agent",
        "PycURL",
        "AlphaServer",
        "T8Abot",
        "Syntryx",
        "WinHttp",
        "WebBandit",
        "nicebot",
        "[en]",
        "0.6 Beta",
        "build",
        "OpenWare",
        "Opera/9.0 (Windows NT 5.1; U; en)"
    ];
    foreach ($bots as $bot) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
            $isbot = true;
        }
    }

    if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ") {
        $isbot = true;
    }

    return $isbot;
}

function spamCount($input)
{
    $spam = [
        "beastial",
        "bestial",
        "blowjob",
        "clit",
        "cum",
        "cunilingus",
        "cunillingus",
        "cunnilingus",
        "cunt",
        "ejaculate",
        "fag",
        "felatio",
        "fellatio",
        "fuck",
        "fuk",
        "fuks",
        "gangbang",
        "gangbanged",
        "gangbangs",
        "hotsex",
        "jism",
        "jiz",
        "orgasim",
        "orgasims",
        "orgasm",
        "orgasms",
        "phonesex",
        "phuk",
        "phuq",
        "porn",
        "pussies",
        "pussy",
        "spunk",
        "xxx",
        "viagra",
        "phentermine",
        "tramadol",
        "adipex",
        "advai",
        "alprazolam",
        "ambien",
        "ambian",
        "amoxicillin",
        "antivert",
        "blackjack",
        "backgammon",
        "texas",
        "holdem",
        "poker",
        "carisoprodol",
        "ciara",
        "ciprofloxacin",
        "debt",
        "dating",
        "porn"
    ];

    $words = [];
    foreach (preg_split('/[^\w]/', strtolower($input), -1, PREG_SPLIT_NO_EMPTY) as $word) {
        $words[] = $word;
    }

    $compare = array_intersect($spam, $words);

    return count($compare);
}

function exploitKarma($input)
{
    $tempKarma = 0;

    $exploits = ["content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript"];
    foreach ($exploits as $exploit) {
        if (!empty($input) && stripos($input, $exploit) !== false) {
            $tempKarma += 2;
        }
    }

    return $tempKarma;
}

function badMailKarma($input)
{
    $tempKarma = 0;

    $badmails = ["mail.ru", "hotsheet.com", "ibizza.com", "aeekart.com", "fooder.com", "yahone.com"];
    $expodedArray = explode("@", $input);
    $domain = array_pop($expodedArray);
    foreach ($badmails as $ext) {
        if ($domain == $ext) {
            $tempKarma += 2;
        }
    }

    return $tempKarma;
}

function isBanned($email)
{
    global $mysql, $dbpref;

    $list = [];
    $getbanned = $mysql->query("SELECT * FROM `" . $dbpref . "banned`");
    if ($mysql->count($getbanned)) {
        while ($r = $mysql->fetchAssoc($getbanned)) {
            if ($r['type'] == "ip") {
                $list['ip'][] = $r['value'];
            } else {
                $list['email'][] = strtolower($r['value']);
            }
        }

        if (in_array($_SERVER['REMOTE_ADDR'], $list['ip'])) {
            return true;
        }

        if (in_array($email, $list['email'])) {
            return true;
        }

        return false;
    } else {
        return false;
    }
}

function validateButton($button)
{
    global $opt;

    $allowed = [".jpg", ".gif", ".png"];

    if (filesize($button) > $opt['buttonsize']) {
        $error[] = "Button larger than max file size";
    } elseif (in_array(ext($button), $allowed)) {
        $error[] = "Invalid file type";
    }

    if (!$imginfo = @getimagesize($button)) {
        $error[] = "Invalid file - images only.";
    } elseif ($imginfo[0] > $opt['buttonmaxwidth']) {
        $error[] = "Button too wide; max width: " . $opt['buttonmaxwidth'];
    } elseif ($imginfo[1] > $opt['buttonmaxheight']) {
        $error[] = "Button too high; max height: " . $opt['buttonmaxheight'];
    } elseif ($imginfo[2] == 4) {
        $error[] = "Only jpg, gif and png buttons are supported.";
    }

    if (!isset($error) || count($error) > 0) {
        return $button;
    }

    return $error;
}

// GET FUNCTIONS
function ext($file)
{
    return strrchr($file, ".");
}

function getAllCats($display = 'dropdown', $spacer = '&nbsp;&nbsp;', $selected = null, $level = 2)
{
    global $mysql, $opt, $dbpref;

    /*
        this is probably the single most hackiest piece of SHIT I've ever written... nonetheless
        it fulfills it purpose and will enable me to get the script out some time this milennium
        if you're knowledgeable enough to be reading this far down - forgive me, please!
    */

    $cats = [];
    $meow = $mysql->query("SELECT `catparent`, `catname`, `" . $dbpref . "categories`.`id` as `catid`, COUNT(`" . $dbpref . "links`.`id`) AS `linkcount` FROM `" . $dbpref . "categories` LEFT JOIN `" . $dbpref . "links` ON `" . $dbpref . "categories`.`id` = `" . $dbpref . "links`.`category` GROUP BY `" . $dbpref . "categories`.`id` ORDER BY `catparent`, `catname`");
    while ($row = $mysql->fetchAssoc($meow)) {
        if ($row['catparent'] == 0) {
            $cats[$row['catid']] = ['name' => $row['catname'], 'subcats' => "", 'linkcount' => $row['linkcount']];
        } else {
            $cats[$row['catparent']]['subcats'][$row['catid']] = ['name' => $row['catname']];
        }
    }

    if ($opt['topdirlinks'] == 0) {
        $disallow = ' disabled="disabled"';
    } else {
        $disallow = null;
    }

    foreach ($cats as $catid => $catinfo) {
        foreach ($catinfo as $key => $value) {
            if ($key == "name") {
                if ($display == "dropdown") {
                    if ($selected != null && $selected == $catid) {
                        $showsel = ' selected="selected"';
                    } else {
                        $showsel = null;
                    }

                    echo '<option value="' . $catid . '"' . $disallow . $showsel . '>' . $value . '</option>' . "\r\n";
                } elseif ($opt['topdirlinks'] == 1) {
                    echo '<li><a href="links.php?cat=' . $catid . '">' . $value . '</a> (' . $cats[$catid]['linkcount'] . ' links)</li>' . "\r\n";
                } else {
                    echo '<li>' . $value . '</li>' . "\r\n";
                }
            } elseif ($key == "subcats") {
                if (is_array($value)) {
                    foreach ($value as $subcatid => $subcat) {
                        if ($level == 2) {
                            foreach ($subcat as $subkey => $info) {
                                if ($subkey == "name") {
                                    if ($display == "dropdown") {
                                        if ($selected != null && $selected == $subcatid) {
                                            $showsel = ' selected="selected"';
                                        } else {
                                            $showsel = null;
                                        }

                                        echo '<option value="' . $subcatid . '"' . $showsel . '>' . $spacer . $info . '</option>' . "\r\n";
                                    } else {
                                        echo '<li><a href="links.php?cat=' . $subcatid . '">' . $spacer . $info . '</a></li>' . "\r\n";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function getLinks($offset, $limit, $category)
{
    global $mysql, $opt, $dbpref;

    $buildQuery = "SELECT `" . $dbpref . "links`.*, `" . $dbpref . "categories`.`catname` FROM `" . $dbpref . "links` LEFT JOIN `" . $dbpref . "categories` ON `" . $dbpref . "links`.`category` = `" . $dbpref . "categories`.`id` WHERE `" . $dbpref . "links`.`approved` = 1";
    if ($category != "all") {
        $buildQuery .= " AND `category` = " . $category;
    }
    $buildQuery .= " ORDER BY `dateadded` DESC";

    $links = $mysql->query($buildQuery . " LIMIT " . $offset . ", " . $limit);
    echo '<ul>';
    while ($l = $mysql->fetchAssoc($links)) {
        echo '<li><a href="click.php?link=' . $l['id'] . '"';
        if ($opt['opentarget'] == 1) {
            echo ' target="_blank"';
        }
        if ($opt['nofollow'] == 1) {
            echo ' rel="nofollow"';
        }
        echo '>';
        echo $l['linkname'];
        echo '</a> <em>(' . $l['hits'] . ' hits)</em>';
        if ($opt['allowdesc'] == 1) {
            echo '<br />' . $l['linkdesc'] . '</li>';
        } else {
            echo '</li>';
        }
    }
    echo '</ul>';
}

function getUpdates($limit)
{
    global $mysql, $dbpref;

    $updates = $mysql->query("SELECT *, DATE_FORMAT(`datetime`, '%a %D %b %Y \- %H:%i') AS `date` FROM `" . $dbpref . "updates` ORDER BY `datetime` DESC LIMIT " . $limit);
    while ($u = $mysql->fetchAssoc($updates)) {
        ?>
        <h2><?= $u['title'] ?></h2>
        <?= nl2p($u['entry']) ?>
        <p class="updatemeta">Posted on <?= $u['date'] ?></p>
        <?php
    }
}

function getStats($stat)
{
    global $mysql, $dbpref;

    if ($stat == "approved") {
        return $mysql->single("SELECT COUNT(`id`) FROM `" . $dbpref . "links` WHERE `approved` = 1");
    }

    if ($stat == "pending") {
        return $mysql->single("SELECT COUNT(`id`) FROM `" . $dbpref . "links` WHERE `approved` = 0");
    }

    return $mysql->single("SELECT COUNT(`id`) FROM `" . $dbpref . "links`");
}

function getPage()
{
    if (!isset($_GET['pg']) || empty($_GET['pg']) || !is_numeric($_GET['pg'])) {
        $page = 1;
    } else {
        $page = (int)$_GET['pg'];
    }

    return $page;
}

function getView()
{
    if (isset($_GET['v']) && preg_match("/^[A-Za-z0-9]*$/", $_GET['v'])) {
        $view = $_GET['v'];
    } else {
        $view = null;
    }

    return $view;
}

function getPagination($total)
{
    global $opt;

    $totalPages = ceil($total / $opt['perpage']);

    echo '<p class="center">Pages: ';
    for ($x = 1; $x <= $totalPages; $x++) {
        if ($x == getPage()) {
            echo '<strong class="current">' . $x . '</strong> ';
        } else {
            echo '<a href="' . basename($_SERVER['PHP_SELF']) . '?pg=' . $x . '">' . $x . '</a> ';
        }
    }
    echo '</p>';
}

// DO FUNCTIONS
function doEmail($recipient, $subject, $message, $xtraheaders = '')
{
    global $opt;

    if (strpos($_SERVER['SERVER_SOFTWARE'], "Win") !== false) {
        $headers = "From: " . $opt['email'];
    } else {
        $headers = "From: " . $opt['dirname'] . " <" . $opt['email'] . ">";
    }

    $headers .= $xtraheaders;

    if (mail($recipient, $subject, $message, $headers)) {
        return true;
    }

    return false;
}

function doCheckLogin()
{
    global $opt;

    if (!isset($_SESSION['nlLogin'])) {
        return false;
    }

    return $_SESSION['nlLogin'] == md5($opt['user'] . md5($opt['pass'] . $opt['salt']));
}

function doDelete($table, $ids)
{
    global $mysql, $dbpref;

    if (isset($ids) && is_array($ids)) {
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                exit("<p>Invalid update id selected.</p>");
            }
        }

        $delete = $mysql->query("DELETE FROM `" . $dbpref . $table . "` WHERE `id` IN (" . implode(", ", $ids) . ")");

        if ($delete) {
            echo '<p>Item(s) successfully deleted.</p>';
        } else {
            echo '<p>Item(s) not deleted; please check for errors and try again.</p>';
        }
    } else {
        exit("<p>Invalid (or no) item selected.</p>");
    }
}

function isInstalled()
{
    global $mysql, $dbname;

    $findTables = $mysql->query("SHOW TABLES FROM `" . $dbname . "`");
    if ($mysql->count($findTables)) {
        return true;
    }

    return false;
}

function checkInstall()
{
    if (!isInstalled()) {
        doError('not-installed');
    } elseif (file_exists("install.php")) {
        doError('install-file');
    }
}

function doError($errorID)
{
    // at some point I shall set this up to do something with $details - store or w/e

    switch ($errorID) {
        case "no-connect":
            $displaymsg = 'Could not connect to the database. Please check your database details and try again.';
            break;
        case "no-select-db":
            $displaymsg = 'Could not select the database. Please check your database details and try again.';
            break;
        case "not-installed":
            $displaymsg = 'NinjaLinks is not installed; please run install.php';
            break;
        case "install-file":
            $displaymsg = 'You have not deleted the install.php file; please do so to continue.';
            break;
        case "query-fail":
            $displaymsg = 'Could not run query on the database. Please check your database details and try again.';
            break;
        default:
            $displaymsg = 'Unidentified error.';
            break;
    }

    echo '<p style="background: #fff; color: #f00; font-weight: bold;">Error: ' . $displaymsg . '</p>';
    include('footer.php');
    exit;
}
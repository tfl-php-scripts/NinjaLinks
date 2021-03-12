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

include('header.php'); ?>


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

include('../footer.php');
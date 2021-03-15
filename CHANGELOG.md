# Changes:

v.1.0

* Changing all mysql_* functions to mysqli_*
* PHP migration fixes: converting to short syntax array, removing closing tags, new style constructor usage, replacing ereg with preg_match.
* Various code smell and PHP inspection fixes
* Hiding all mysqli_* functions in SQLConnection class
* Getting rid of preg_replace_callback
* Using * and 'required' for required fields; use my own clean function
* Fixes for adding links when there's banned
* Using MySQL native NOW()
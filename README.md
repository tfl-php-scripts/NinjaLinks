# NinjaLinks for PHP 7 [Robotess Fork]

The main repository with the issue tracking can be found on [gitlab](https://gitlab.com/tfl-php-scripts/ninja-links).

An original author is [Jem Turner](http://www.jemjabella.co.uk/scripts) / Original readme by Jem
can be found [here](https://gitlab.com/tfl-php-scripts/ninja-links/-/blob/master/README.txt).

#### I would highly recommend not to use this script for new installations. Although some modifications were made, this script is still pretty old, not very secure, and does not have any tests, that's why please only update it if you have already installed it before.

This version requires at least PHP 7.2 and MySQLi extension turned on (tested on MySQL version 5.7 and 8.0.22).

| PHP version | Supported by the script | Link to download |
|------------------------------------------|-------------------------|---------------------|
| 7.2 | :white_check_mark: |[an archive of the public folder of this repository for PHP 7.2](https://scripts.robotess.net/files/ninja-links/php72-php73-master.zip)|
| 7.3 | :white_check_mark: |[an archive of the public folder of this repository for PHP 7.3](https://scripts.robotess.net/files/ninja-links/php72-php73-master.zip)| 
| 7.4 | :white_check_mark: |[an archive of the public folder of this repository for PHP 7.4](https://gitlab.com/tfl-php-scripts/ninja-links/-/archive/master/ninja-links-master.zip?path=public) ([mirror](https://scripts.robotess.net/files/ninja-links/php74-master.zip))|
| 8.0 | :grey_question: |-|

Changes are available in [changelog](https://gitlab.com/tfl-php-scripts/ninja-links/-/blob/master/CHANGELOG.md).

## Upgrading instructions

I'm not providing support for those who have a script with the version lower than 1.1.

If you are using NinjaLinks 1.1 (old version by Jem) or [Robotess Fork] 1.* (my version):

1. **Back up all your current script configurations, files, and databases first.**
2. Take note of your database and scripts settings in your `config.php` file.
3. Download an archive - please choose appropriate link from the table above. Extract the archive.
4. Replace your current `ninjalinks/` files with the `public/ninjalinks/` files from this repository. Make sure that you
   have all files from the folder uploaded.
5. Open `config.sample.php`. Edit your database/scripts settings accordingly, and save it as `config.php` to overwrite your old
   file.
6. Run `admin/update-to-robotess-fork-1.0.php`. After successful run, remove the file.

That's it! Should you encounter any problems, please create an
issue [here](https://gitlab.com/tfl-php-scripts/ninja-links/-/issues), and I will try and solve it if I can. You can
also report an issue via [contact form](http://contact.robotess.net?box=scripts&subject=Issue+with+NinjaLinks). Do not forget to include the logs.
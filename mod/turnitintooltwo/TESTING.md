# Testing
To run the unit tests you will require PHP, Composer, a working Moodle installation and an installed copy of the plugin. The following guide explains how to do this.

## Set up a local Moodle environment
You may well have a local Moodle setup already. If so, skip this step.

If not, the easiet way to get Moodle set up on a Mac is with the MAMP Moodle installers.

Grab an installer for the latest stable version from here:

[https://download.moodle.org/macosx/](https://download.moodle.org/macosx/)

## Set up PHP

You will need PHP and [Composer](https://getcomposer.org/) installed. 

## Install the plugin
Grab a copy of the plugin.

[https://github.com/turnitin/moodle-mod_turnitintooltwo](https://github.com/turnitin/moodle-mod_turnitintooltwo)

Put the plugin in the `mod` folder inside your Moodle installation.

Go to the admin notifications screen in Moodle to install the plugin.

## Prepare Moodle for running unit tests

* `cd` into the root folder of your Moodle installation. From here run `composer install` to install the required dependencies.

* Edit `config.php` in the root of the Moodle folder and add the `phpunit` config. The following works for the MAMP Moodle install mentioned above:

```
$CFG->phpunit_prefix    = 'phpu_';
$CFG->phpunit_dataroot  = '/Applications/MAMP/data/phpu_moodledata';
$CFG->phpunit_dbtype    = 'mysqli';      // 'pgsql', 'mariadb', 'mysqli', 'mssql', 'sqlsrv' or 'oci'
$CFG->phpunit_dbhost    = '127.0.0.1:8889';  // eg 'localhost' or 'db.isp.com' or IP
$CFG->phpunit_dbname    = 'moodle31';     // database name, eg moodle
$CFG->phpunit_dbuser    = 'moodle';   // your database username
$CFG->phpunit_dbpass    = 'moodle';   // your database password
```

* Run `php admin/tool/phpunit/cli/init.php`

## Run the tests

To run the test commands, make sure you are in the root directory of Moodle.

Run the whole test suite:

`vendor/bin/phpunit --testsuite mod_turnitintooltwo_testsuite`

Run only a specific test file:

`vendor/bin/phpunit -v mod/turnitintooltwo/tests/unit/classes/digitalreceipt/receipt_message_test.php`


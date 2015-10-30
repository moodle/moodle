PHPUnit testing support in Moodle
==================================


Documentation
-------------
* [Moodle PHPUnit integration](http://docs.moodle.org/dev/PHPUnit)
* [PHPUnit online documentation](http://www.phpunit.de/manual/current/en/)
* [Composer dependency manager](http://getcomposer.org/)


Composer installation
---------------------
Composer is a dependency manager for PHP projects.
It installs PHP libraries into /vendor/ subdirectory inside your moodle dirroot.

1. install Composer - [http://getcomposer.org/doc/00-intro.md](http://getcomposer.org/doc/00-intro.md)
2. install PHUnit and dependencies - go to your Moodle dirroot and execute `php composer.phar install --dev`


Configure your server
---------------------
You need to create a new dataroot directory and specify a separate database prefix for the test environment,
see config-dist.php for more information.

* add `$CFG->phpunit_prefix = 'phpu_';` to your config.php file
* and `$CFG->phpunit_dataroot = '/path/to/phpunitdataroot';` to your config.php file


Initialise the test environment
-------------------------------
Before first execution and after every upgrade the PHPUnit test environment needs to be initialised,
this command also builds the phpunit.xml configuration files.

* execute `php admin/tool/phpunit/cli/init.php`


Execute tests
--------------
* execute `vendor/bin/phpunit` from dirroot directory
* you can execute a single test case class using class name followed by path to test file `vendor/bin/phpunit core_phpunit_basic_testcase lib/tests/phpunit_test.php`
* it is also possible to create custom configuration files in xml format and use `vendor/bin/phpunit -c mytestsuites.xml`


How to add more tests?
----------------------
1. create `tests/` directory in your add-on
2. add test file, for example `local/mytest/tests/my_test.php` file with `local_my_testcase` class that extends `basic_testcase` or `advanced_testcase`
3. add some test_*() methods
4. execute your new test case `vendor/bin/phpunit local_my_testcase local/mytest/tests/my_test.php`
5. execute `php admin/tool/phpunit/cli/init.php` to get the plugin tests included in main phpunit.xml configuration file


Windows support
---------------
* use `\` instead of `/` in paths in examples above
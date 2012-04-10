PHPUnit testing support in Moodle
==================================


Documentation
-------------
* [Moodle Dev wiki](http://docs.moodle.org/dev/PHPUnit)
* [PHPUnit online documentaion](http://www.phpunit.de/manual/current/en/)


Installation
------------
1. install PEAR package manager - see [PEAR Manual](http://pear.php.net/manual/en/installation.php)
2. install PHPUnit package and phpunit/DbUnit extension - see [PHPUnit installation documentation](http://www.phpunit.de/manual/current/en/installation.html)
3. edit main config.php - add `$CFG->phpunit_prefix` and `$CFG->phpunit_dataroot` - see config-dist.php
4. execute `admin/tool/phpunit/cli/init.sh` to initialise the test environemnt, repeat it after every upgrade or installation of plugins


Test execution
--------------
* execute `phpunit` from dirroot directory
* you can execute a single test case class using class name followed by path to test file `phpunit core_phpunit_basic_testcase lib/tests/phpunit_test.php`
* it is also possible to create custom configuration files in xml format and use `phpunit -c mytestsuites.xml`


How to add more tests?
----------------------
1. create `tests` directory in your plugin
2. add `local/mytest/tests/my_test.php` file with `local_my_testcase` class that extends `basic_testcase` or `advanced_testcase`
3. add some test_*() methods
4. execute your new test case `phpunit local_my_testcase local/mytest/tests/my_test.php`
5. execute `admin/tool/phpunit/cli/init.sh` to get the plugin tests included in main phpunit.xml configuration file


How to convert existing tests?
------------------------------
1. create new test file in `xxx/tests/yyy_test.php`
2. copy contents of the old test file
3. replace `extends UnitTestCase` with `extends basic_testcase`
4. fix setUp(), tearDown(), asserts, etc.

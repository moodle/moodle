PHPUnit testing support in Moodle
==================================


Documentation
-------------
* [Moodle Dev wiki](http://docs.moodle.org/dev/PHPUnit)
* [PHPUnit online documentaion](http://www.phpunit.de/manual/current/en/)


Installation
------------
1. install PEAR package manager - see [PEAR Manual](http://pear.php.net/manual/en/installation.php)
2. install PHPUnit package - see [PHPUnit installation documentation](http://www.phpunit.de/manual/current/en/installation.html)
3. edit main config.php - add `$CFG->phpunit_prefix` and `$CFG->phpunit_dataroot` - see config-dist.php
4. execute `admin/tool/phpunit/cli/init.sh` to initialise the test environemnt, repeat it after every upgrade or installation of plugins


Test execution
--------------
* execute `phpunit` from dirroot directory
* you can also execute a single test `phpunit lib/tests/phpunit_test.php`
* or all tests in one directory `phpunit --configuration phpunit.xml lib/tests/*_test.php`
* it is possible to create custom configuration files in xml format and use `phpunit -c myconfig.xml`


How to add more tests?
----------------------
1. create `tests` directory in your plugin if does not already exist
2. add `*_test.php` files with custom class that extends `basic_testcase` or `advanced_testcase`
3. execute your new test, for example `phpunit local/mytest/tests/mytest_test.php`


How to convert existing tests?
------------------------------
1. create new test file in `xxx/tests/yyy_test.php`
2. copy contents of the old test file
3. replace `extends UnitTestCase` with `extends basic_testcase`
4. fix setUp(), tearDown(), asserts, etc.

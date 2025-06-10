<?php
if(version_compare(PHP_VERSION, '5.1.0', '<')) {
    exit('Your current PHP version is: ' . PHP_VERSION . '. Wiris Quizzes needs version 5.1.0 or later');
}
;
$bootfile = dirname(__FILE__) . '/../bootstrap.php';
if (@is_readable($bootfile)) require_once($bootfile);

require_once dirname(__FILE__) . '/lib/php/Boot.class.php';

com_wiris_quizzes_test_LockTester::main(null);

?>
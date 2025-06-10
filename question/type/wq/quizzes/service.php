<?php
if(version_compare(PHP_VERSION, '5.1.0', '<')) {
    exit('Your current PHP version is: ' . PHP_VERSION . '. Wiris Quizzes needs version 5.1.0 or later');
};


$bootfile = dirname(__FILE__) . '/../bootstrap.php';
if (@is_readable($bootfile)) require_once($bootfile);

if (!class_exists('com_wiris_system_CallWrapper')) {
    require_once dirname(__FILE__).'/lib/com/wiris/system/CallWrapper.class.php';
}
com_wiris_system_CallWrapper::getInstance()->init(dirname(__FILE__));
com_wiris_system_CallWrapper::getInstance()->start();

com_wiris_quizzes_service_PhpServiceProxy::dispatch();

?>
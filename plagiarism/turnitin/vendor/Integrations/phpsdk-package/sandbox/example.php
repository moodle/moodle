<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../tests/tiiapi/testconsts.php');

use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiClass;

$logdir = getcwd().'/logs';

$api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT);

$api->setLogPath($logdir);
$api->setDebug(true);

$class = new TiiClass();
$class->setTitle('Test Class');
$class->setEndDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('+1 years')));

try {
    $response = $api->createClass($class);
    $classid = $response->getClass()->getClassId();
} catch (Integrations\PhpSdk\TurnitinSDKException $e) {
    echo $e->getFaultCode() . ': ' . $e->getMessage() . '<br />';
    exit();
}

echo $classid;

// Can be used to clean up test classes
//try {
//    $response = $api->findClasses($class);
//    $class = $response->getClass();
//    $classids = $class->getClassIds();
//} catch (Integrations\PhpSdk\TurnitinSDKException $e) {
//    echo $e->getFaultCode() . ': ' . $e->getMessage() . '<br />';
//    exit();
//}

//foreach ($classids as $classid) {
//    try {
//        $class->setClassId($classid);
//        $response = $api->deleteClass($class);
//    } catch (TurnitinSDKException $e) {
//        echo $e->getFaultCode() . ': ' . $e->getMessage() . '<br />';
//        exit();
//    }
//}

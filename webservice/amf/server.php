<?php
/**
 * Created on 01/20/2009
 *
 * AMF Moodle server.
 *
 * @author Jerome Mouneyrac
 */

require_once(dirname(__FILE__) . '/../../config.php');
include "Zend/Loader.php";
Zend_Loader::registerAutoload();
if (empty($CFG->enablewebservices)) {
    die;
}

/*
 * FULL SERVER
 *
 */
//retrieve the api name
$classpath = optional_param(classpath,'user',PARAM_ALPHA);
require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

/// run the server
$server = new Zend_Amf_Server();
$server->setClass($classpath."_external");
$response = $server->handle();
echo $response;


?>
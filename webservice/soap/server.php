<?php
/**
 * Main script - SOAP server
 *
 * @author Jerome Mouneyrac <jerome@moodle.com>
 * @version 1.0
 * @package webservices
 */

/*
 * SOAP server
 */
require_once(dirname(__FILE__) . '/../../config.php');

if (empty($CFG->enablewebservices)) {
    die;
}

//retrieve the api name
$classpath = optional_param(classpath,null,PARAM_ALPHA);
require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

/// run the server
$server = new SoapServer("moodle.wsdl");
$server->setClass($classpath."_external");
$server->handle();

?>
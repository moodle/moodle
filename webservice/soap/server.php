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

//retrieve the api name
$classpath = optional_param(classpath,null,PARAM_ALPHA);
require_once(dirname(__FILE__) . '/../../'.$classpath.'/wsapi.php');

/// run the server
$server = new SoapServer("moodle.wsdl");
$server->setClass($classpath."_ws_api");
$server->handle();

?>
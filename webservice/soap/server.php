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

//TODO retrieve the token from the url
//     if the token doesn't exist create a server with a connection.wsdl
//     and set a class containing only get_token() (need to create connection.wsdl and class soapiniconnection)
//     if token exist, do the authentication here

/// run the server
$server = new SoapServer("moodle.wsdl"); //TODO: need to call the wsdl generation on the fly
$server->setClass($classpath."_external"); //TODO: pass $user as parameter
$server->handle();

?>
<?php

/**
 * Main script - try a SOAP connection
 *
 * @author Jerome Mouneyrac <jerome@moodle.com>
 * @version 1.0
 * @package webservices
 */

/*
 * SOAP client
 */
require_once(dirname(__FILE__) . '/../../../config.php');

//$client = new SoapClient("moodle.wsdl");

$client = new SoapClient("../moodle.wsdl",array(
    "trace"      => 1,
    "exceptions" => 0));

try {
    var_dump($client->tmp_get_users("admin"));
    printLastRequestResponse($client);
} catch (SoapFault $exception) {
    echo $exception;
}


function printLastRequestResponse($client) {
    print "<pre>\n";
    print "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
    print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
    print "</pre>";
}

?>
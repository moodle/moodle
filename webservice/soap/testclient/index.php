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
    var_dump($client->tmp_get_users(array('search' => "admin")));
    printLastRequestResponse($client);
    var_dump($client->tmp_create_user(array('username' => "mockuser66",'firstname' => "firstname6",'lastname' => "lastname6",'email' => "mockuser6@mockuser6.com",'password' => "password6")));
    printLastRequestResponse($client);
    var_dump($client->tmp_update_user(array('username' => "mockuser66",'mnethostid' => 1,'newusername' => "mockuser6b",'firstname' => "firstname6b")));
    printLastRequestResponse($client);
    var_dump($client->tmp_delete_user(array('username' => "mockuser6b",'mnethostid' => 1)));
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
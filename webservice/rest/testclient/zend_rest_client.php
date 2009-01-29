<?php

/**
 * Main script - try a REST connection
 *
 * @author Jerome Mouneyrac <jerome@moodle.com>
 * @version 1.0
 * @package webservices
 */

/*
 * Zend Rest sclient
 */
require_once('../../../config.php');
include "Zend/Loader.php";
Zend_Loader::registerAutoload();


//1. authentication
$client = new Zend_Rest_Client($CFG->wwwroot."/webservice/rest/zend_rest_server.php");


    $token = $client->tmp_get_token(array('username' => "wsuser", 'password' => "wspassword"))->get();
   echo $token->response();
   $token = $token->response();
    printLastRequestResponse($client);


//2. test functions
$client = new Zend_Rest_Client($CFG->wwwroot."/webservice/rest/zend_rest_server.php/?classpath=user&token=".$token);
   
    var_dump($client->tmp_get_users(array('search' => "admin"))->get());
    printLastRequestResponse($client);
    var_dump($client->tmp_create_user(array('username' => "mockuser66",'firstname' => "firstname6",'lastname' => "lastname6",'email' => "mockuser6@mockuser6.com",'password' => "password6"))->get());
    printLastRequestResponse($client);
    var_dump($client->tmp_update_user(array('username' => "mockuser66",'mnethostid' => 1,'newusername' => "mockuser6b",'firstname' => "firstname6b"))->get());
   printLastRequestResponse($client);
    var_dump($client->tmp_delete_user(array('username' => "mockuser6b",'mnethostid' => 1))->get());
   printLastRequestResponse($client);



function printLastRequestResponse($client) {
    print "<pre>\n";
  //  print "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
   // print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
    print "</pre>";
}

?>
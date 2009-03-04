<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   webservice
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */


/*
 * Zend Rest sclient
 */

require_once('../../../config.php');
require_once('../lib.php');
include "Zend/Loader.php";
Zend_Loader::registerAutoload();

print_header('Soap test client', 'Soap test client'.":", true);
if (!webservice_lib::display_webservices_availability("soap")) {
    echo "<br/><br/>";
    echo "Please fix the previous problem(s), the testing session has been interupted.";
    print_footer();
    exit();
}

//1. authentication
$client = new Zend_Soap_Client($CFG->wwwroot."/webservice/soap/server.php?wsdl");
try {
    $token = $client->tmp_get_token(array('username' => "wsuser", 'password' => "wspassword"));
    print "<pre>\n";
    var_dump($token);
    print "</pre>";
} catch (moodle_exception $exception) {
    echo $exception;
}

//2. test functions
//$client = new Zend_Http_Client($CFG->wwwroot."/webservice/soap/server.php?token=".$token."&classpath=user&wsdl", array(
//    'maxredirects' => 0,
//    'timeout'      => 30));
//$response = $client->request();
//$wsdl = $response->getBody();
//varlog($wsdl,"user.wsdl", "w");


$client = new Zend_Soap_Client($CFG->wwwroot."/webservice/soap/server.php?token=".$token."&classpath=user&wsdl");
var_dump($CFG->wwwroot."/webservice/soap/server.php?token=".$token."&classpath=user&wsdl");
print "<pre>\n";
var_dump($client->tmp_get_users(array('search' => "admin")));
print "</pre>";

//$param = array('search' => "admin");
//$expectedresult = array(array(  'id' => 2,
//                                'auth' => 'manual',
//                                'confirmed' => '1',
//                                'username' => 'admin',
//                                'idnumber' => '',
//                                'firstname' => 'Admin',
//                                'lastname' => 'HEAD',
//                                'email' => 'jerome@moodle.com',
//                                'emailstop' => '0',
//                                'lang' => 'en_utf8',
//                                'theme' => '',
//                                'timezone' => '99',
//                                'mailformat' => '1'));
//$functionname = tmp_get_users;
//call_soap_function($client,$functionname,$param,$expectedresult);

print "<pre>\n";
var_dump($client->tmp_create_user(array('username' => "mockuser66",'firstname' => "firstname6",'lastname' => "lastname6",'email' => "mockuser6@mockuser6.com",'password' => "password6")));
print "</pre>";

print "<pre>\n";
var_dump($client->tmp_update_user(array('mnethostid' => 1,'username' => "mockuser66",'newusername' => "mockuser6b",'firstname' => "firstname6b")));
print "</pre>";

print "<pre>\n";
var_dump($client->tmp_delete_user(array('username' => "mockuser6b",'mnethostid' => 1)));
print "</pre>";

print "<pre>\n";
var_dump($client->tmp_do_multiple_user_searches(array(array('search' => "jerome"),array('search' => "mock"))));
print "</pre>";


print_footer();

//function call_soap_function($client,$functionname,$param,$expectedresult) {
//    print "<pre>\n";
//    var_dump($client->$functionname($param));
//    print "</pre>";
//}


function printLastRequestResponse($client) {
    print "<pre>\n";
    print "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
    print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
    print "</pre>";
}

?>
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
 * Zend XMLRPC sclient
 */
require_once('../../../config.php');

include "Zend/Loader.php";
Zend_Loader::registerAutoload();


//1. authentication
$client = new Zend_XmlRpc_Client($CFG->wwwroot."/webservice/xmlrpc/server.php");
$params = new stdClass();
$params->username = 'wsuser';
$params->password = 'wspassword';
$token = $client->call('authentication.get_token', $params);
var_dump($token);

//2. test functions
$client = new Zend_XmlRpc_Client($CFG->wwwroot."/webservice/xmlrpc/server.php?classpath=user&token=".$token);
$params = new stdClass();
$params->search = "admin";
var_dump($users = $client->call('user.get_users', $params));
print "<br/><br/>\n";
$user = new stdClass();
$user->password = "password6";
$user->email = "mockuser6@mockuser6.com";
$user->username = "mockuser66";
$user->firstname = "firstname6";
$user->lastname = "lastname6";
$params = new stdClass();
$params->users = array($user);
var_dump($users = $client->call('user.create_users', $params));
print "<br/><br/>\n";
$usertoupdate = new stdClass();
$usertoupdate->email = "mockuser6@mockuser6.com";
$usertoupdate->username = "mockuser66";
$usertoupdate->newusername = 'mockuser6b';
$usertoupdate->firstname = "firstname6b";
$params = new stdClass();
$params->users = array($usertoupdate);
var_dump($users = $client->call('user.update_users', $params));
print "<br/><br/>\n";
$params = new stdClass();
$params->usernames = array("mockuser6b");
var_dump($users = $client->call('user.delete_users', $params));
//print "<br/><br/>\n";
//var_dump($users = $client->call('user.tmp_do_multiple_user_searches', array(array(array('search' => "jerome"),array('search' => "admin")))));
//print "<br/><br/>\n";

?>
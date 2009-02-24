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
$token = $client->call('authentication.tmp_get_token', array(array('username' => "wsuser", 'password' => "wspassword")));
var_dump($token);

//2. test functions
$client = new Zend_XmlRpc_Client($CFG->wwwroot."/webservice/xmlrpc/server.php?classpath=user&token=".$token);
var_dump($users = $client->call('user.tmp_get_users', array(array('search' => "admin"))));
print "<br/><br/>\n";
var_dump($users = $client->call('user.tmp_create_user', array(array('username' => "mockuser66",'firstname' => "firstname6",'lastname' => "lastname6",'email' => "mockuser6@mockuser6.com",'password' => "password6"))));
print "<br/><br/>\n";
var_dump($users = $client->call('user.tmp_update_user', array(array('username' => "mockuser66",'mnethostid' => 1,'newusername' => "mockuser6b",'firstname' => "firstname6b"))));
print "<br/><br/>\n";
var_dump($users = $client->call('user.tmp_delete_user', array(array('username' => "mockuser6b",'mnethostid' => 1))));
print "<br/><br/>\n";
var_dump($users = $client->call('user.tmp_do_multiple_user_searches', array(array(array('search' => "jerome"),array('search' => "admin")))));
print "<br/><br/>\n";

?>
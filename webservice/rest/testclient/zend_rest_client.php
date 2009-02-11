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
 * Moodle Zend Rest test client
 */
require_once('../../../config.php');
include "Zend/Loader.php";
Zend_Loader::registerAutoload();


//1. authentication
$client = new Zend_Rest_Client($CFG->wwwroot."/webservice/rest/zend_rest_server.php");
$token = $client->tmp_get_token(array('username' => "wsuser", 'password' => "wspassword"))->get();
echo $token->response();
$token = $token->response();
print "<pre>\n</pre>";

//2. test functions
$client = new Zend_Rest_Client($CFG->wwwroot."/webservice/rest/zend_rest_server.php/?classpath=user&token=".$token);
var_dump($client->tmp_get_users(array('search' => "admin"))->get());
print "<pre>\n</pre>";
var_dump($client->tmp_create_user(array('username' => "mockuser66",'firstname' => "firstname6",'lastname' => "lastname6",'email' => "mockuser6@mockuser6.com",'password' => "password6"))->get());
print "<pre>\n</pre>";
var_dump($client->tmp_update_user(array('username' => "mockuser66",'mnethostid' => 1,'newusername' => "mockuser6b",'firstname' => "firstname6b"))->get());
print "<pre>\n</pre>";
var_dump($client->tmp_delete_user(array('username' => "mockuser6b",'mnethostid' => 1))->get());
print "<pre>\n</pre>";
var_dump($client->tmp_do_multiple_user_searches(array(array('search' => "admin"),array('search' => 'mock')))->get());
print "<pre>\n</pre>";

?>
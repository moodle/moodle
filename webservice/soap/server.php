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
 * SOAP server
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');
ini_set("soap.wsdl_cache_enabled", "0"); //must not cache wsdl
if (empty($CFG->enablewebservices)) {
    die;
}

$server = new soap_server();
$server->run();

?>
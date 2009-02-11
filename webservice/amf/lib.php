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


require_once('../lib.php');

/*
 * AMF server class
 */
final class amf_server extends webservice_server {


    public function __construct() {
        //set web service proctol name
        $this->set_protocolname("Amf");
    }

    /**
     * Run the AMF server
     */
    public function run() {
        include "Zend/Loader.php";
        Zend_Loader::registerAutoload();

        //retrieve the api name
        $classpath = optional_param(classpath,'user',PARAM_ALPHA);
        require_once(dirname(__FILE__) . '/../../'.$classpath.'/external.php');

        /// run the Zend AMF server
        $server = new Zend_Amf_Server();
        $server->setClass($classpath."_external");
        $response = $server->handle();
        echo $response;
    }
}



?>

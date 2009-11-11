<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * AMF web service implementation classes and methods.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");

/**
 * AMF service server implementation.
 * @author Petr Skoda (skodak)
 */
class webservice_amf_server extends webservice_zend_server {
    /**
     * Contructor
     * @param bool $simple use simple authentication
     */
    public function __construct($simple) {
        require_once 'Zend/Amf/Server.php';
        parent::__construct($simple, 'Zend_Amf_Server');
        $this->wsname = 'amf';
    }

    /**
     * Set up zend service class
     * @return void
     */
    protected function init_zend_server() {
        parent::init_zend_server();
        $this->zend_server->setProduction(false); //set to false for development mode
                                                 //(complete error message displayed into your AMF client)
        // TODO: add some exception handling
    }
}

// TODO: implement AMF test client somehow, maybe we could use moodle form to feed the data to the flash app somehow

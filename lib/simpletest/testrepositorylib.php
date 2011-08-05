<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Unit tests for  ../repositorylib.php.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("$CFG->dirroot/repository/lib.php");

// Generate a mock class for each plugin subclass present
$repository_plugins = get_list_of_plugins('repository');

foreach ($repository_plugins as $plugin) {
    require_once($CFG->dirroot . "/repository/$plugin/lib.php");
    Mock::generatePartial("repository_$plugin", "partialmock_$plugin", array('send_package'));
}

class repositorylib_test extends UnitTestCaseUsingDatabase {

    public static $includecoverage = array('repository/lib.php');

    function setup() {
        parent::setup();
    }

    public function test_plugins() {
        $plugins = get_list_of_plugins('repository');

        foreach ($plugins as $plugin) {
            // Instantiate a fake plugin instance
            $plugin_class = "partialmock_$plugin";
            $plugin = new $plugin_class($this);

            // add common plugin tests here
        }
    }
}


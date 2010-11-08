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
 * Unit tests for  ../portfoliolib.php.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/portfoliolib.php');

class portfoliolibaddbutton_test extends UnitTestCaseUsingDatabase {

    public static $includecoverage = array('lib/portfoliolib.php');

    protected $testtables = array(
                'lib' => array(
                    'portfolio_instance', 'portfolio_instance_user'));
    
    public function setUp() {
        parent::setUp();

        $this->switch_to_test_db(); // Switch to test DB for all the execution

        foreach ($this->testtables as $dir => $tables) {
            $this->create_test_tables($tables, $dir); // Create tables
        }

    }

    public function tearDown() {
        parent::tearDown(); // In charge of droppng all the test tables
    }

    function test_set_formats() {

        $button = new portfolio_add_button();
        $button->set_callback_options('assignment_portfolio_caller', array('id' => 6), '/mod/assignment/locallib.php');
        $formats = array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_IMAGE);
        $button->set_formats($formats);
        $this->assertEqual(2, count($button->get_formats()));
    }
}



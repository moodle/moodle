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

    /*
     * TODO: The portfolio unit tests were obselete and did not work.
     * They have been commented out so that they do not break the
     * unit tests in Moodle 2.
     *
     * At some point:
     * 1. These tests should be audited to see which ones were valuable.
     * 2. The useful ones should be rewritten using the current standards
     *    for writing test cases.
     *
     * This might be left until Moodle 2.1 when the test case framework
     * is due to change.
     */
    /*
     * A test of setting and getting formats. What is returned in the getter is a combination of what is explicitly set in 
     * the button, and what is set in the static method of the export class.
     * 
     * In some cases they conflict, in which case the button wins. 
     */

    /*
    function test_set_formats() {

        $button = new portfolio_add_button();
        $button->set_callback_options('assignment_portfolio_caller', array('id' => 6), '/mod/assignment/locallib.php');
        $formats = array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_IMAGE);
        $button->set_formats($formats);
 
        // Expecting $formats + assignment_portfolio_caller::base_supported_formats merged to unique values.
        $formats_combined = array_unique(array_merge($formats, assignment_portfolio_caller::base_supported_formats()));
        
        // In this case, neither file or image conflict with leap2a, which is why all three are returned.
        $this->assertEqual(count($formats_combined), count($button->get_formats()));
    }
    */
}



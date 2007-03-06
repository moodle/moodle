<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
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

require_once(dirname(__FILE__) . '/../../config.php');
global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/moodlelib.php');

/**
 * This test is meant(!?) to run extensive tests on as much of moodle's 
 * xhtml output as possible, using a test database as a stable test bed,
 * and sending the output of each page to the w3c validating service
 * (or some other service that can return a better report), and checking
 * whether that report is valid or not.
 * 
 * A global function for validating xhtml output is badly needed.
 * 
 * One possible solution is to extend this WebTestCase class by adding a 
 * AssertValidXhtml($output) method. The output is obtained by any 
 * WebTestCase using $this->_browser->getContent().
 * 
 * Setting up validation scripts would then be very simple.
 */
class xhtml_test extends WebTestCase {
    
    function setUp() {
        global $CFG;
        
        $this->get($CFG->wwwroot);
        $this->click('Course 4');
    }
    
    function testLogin() {
        $this->assertTitle('Nick\'s tests & Things: Login to the site');
        
    }
}
?>
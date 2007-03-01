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

/**
 * Unit tests for (some of) ../moodlelib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/** $Id */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/moodlelib.php');

class moodlelib_test extends UnitTestCase {
    
    function setUp() {
    }

    function tearDown() {
    }

    function test_address_in_subnet() {
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('123.121.234.2', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('123.121.134.1', '123.121.234.1'));
        $this->assertFalse(address_in_subnet('113.121.234.1', '123.121.234.1'));
        $this->assertTrue(address_in_subnet('123.121.234.0', '123.121.234.2/28'));
        $this->assertTrue(address_in_subnet('123.121.234.15', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.16', '123.121.234.2/28'));
        $this->assertFalse(address_in_subnet('123.121.234.255', '123.121.234.2/28'));
        $this->assertTrue(address_in_subnet('123.121.234.1', '123.121.'));
        $this->assertFalse(address_in_subnet('123.122.234.1', '123.121.'));
        $this->assertFalse(address_in_subnet('223.121.234.1', '123.121.'));
        $this->assertFalse(address_in_subnet('123.121.234.9', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.10', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.15', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('123.121.234.20', '123.121.234.10-20'));
        $this->assertFalse(address_in_subnet('123.121.234.21', '123.121.234.10-20'));
        $this->assertTrue(address_in_subnet('  123.121.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  1.1.2.3 ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  2.2.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertTrue(address_in_subnet('  3.3.3.4  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  123.121.234.2  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  2.1.2.3 ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  2.3.234.1  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
        $this->assertFalse(address_in_subnet('  3.3.3.7  ', '  123.121.234.1  , 1.1.1.1/16,2.2.,3.3.3.3-6  '));
    }
    
    /**
     * Modifies $_SERVER['HTTP_USER_AGENT'] manually to check if check_browser_version 
     * works as expected.
     */
    function test_check_browser_version()
    {
        require_once($CFG->libdir . '/simpletest/fixtures/user_agents.php');
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Safari']['2.0']['Mac OS X'];
        var_dump($_SERVER['HTTP_USER_AGENT']);
        $this->assertTrue(check_browser_version('Safari', '312'));
        $this->assertFalse(check_browser_version('Safari', '500'));
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Opera', '8.0'));
        $this->assertFalse(check_browser_version('Opera', '10.0'));
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(check_browser_version('MSIE', '5.0'));
        $this->assertFalse(check_browser_version('MSIE', '7.0'));
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Firefox', '1.5'));
        $this->assertFalse(check_browser_version('Firefox', '3.0'));        
    }
}

?>

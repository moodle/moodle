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
 * Unit tests for (some of) ../ajax/ajaxlib.php.
 *
 * @copyright &copy; 2006 The Open University
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/** $Id */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->libdir . '/ajax/ajaxlib.php');

class ajaxlib_test extends UnitTestCase {
    
    function setUp() {
    }

    function tearDown() {
    }

    /** 
     * Uses the array of user agents to test ajax_lib::ajaxenabled
     */
    function test_ajaxenabled()
    {
        global $CFG, $USER;
        $CFG->enableajax = true;
        $USER->ajax      = true;
        
        require_once($CFG->libdir . '/simpletest/fixtures/user_agents.php');
        
        // Should be true
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(ajaxenabled());
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Firefox']['1.5']['Windows XP'];
        $this->assertTrue(ajaxenabled());
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertTrue(ajaxenabled());
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(ajaxenabled());
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(ajaxenabled());
        
        // Should be false
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Firefox']['1.0.6']['Windows XP'];
        $this->assertFalse(ajaxenabled());        
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Safari']['312']['Mac OS X'];
        $this->assertFalse(ajaxenabled());
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['Opera']['8.51']['Windows XP'];
        $this->assertFalse(ajaxenabled());
        
        $_SERVER['HTTP_USER_AGENT'] = $user_agents['MSIE']['5.5']['Windows 2000'];
        $this->assertFalse(ajaxenabled());
    }
}

?>
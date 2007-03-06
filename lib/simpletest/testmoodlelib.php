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
    
    var $user_agents = array(
            'MSIE' => array(
                '5.5' => array('Windows 2000' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)'),
                '6.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
                '7.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; YPC 3.0.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)')
            ),  
            'Firefox' => array(
                '1.0.6'   => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6'),
                '1.5'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5'),
                '1.5.0.1' => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1'),
                '2.0'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1',
                                   'Ubuntu Linux AMD64' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.1) Gecko/20060601 Firefox/2.0 (Ubuntu-edgy)')
            ),
            'Safari' => array(
                '312' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/312.1 (KHTML, like Gecko) Safari/312'),
                '2.0' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412')
            ),
            'Opera' => array(
                '8.51' => array('Windows XP' => 'Opera/8.51 (Windows NT 5.1; U; en)'),
                '9.0'  => array('Windows XP' => 'Opera/9.0 (Windows NT 5.1; U; en)',
                                'Debian Linux' => 'Opera/9.01 (X11; Linux i686; U; en)')
            )
        );
        
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
        global $CFG;
        
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];        
        $this->assertTrue(check_browser_version('Safari', '312'));
        $this->assertFalse(check_browser_version('Safari', '500'));
        
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Opera', '8.0'));
        $this->assertFalse(check_browser_version('Opera', '10.0'));
        
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(check_browser_version('MSIE', '5.0'));
        $this->assertFalse(check_browser_version('MSIE', '7.0'));
        
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(check_browser_version('Firefox', '1.5'));
        $this->assertFalse(check_browser_version('Firefox', '3.0'));        
    }
}

?>

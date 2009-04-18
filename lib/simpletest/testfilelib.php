<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/filelib.php');

class filelib_test extends UnitTestCase {
    public function test_get_file_url() {
        global $CFG, $HTTPSPAGEREQUIRED;

        // Slasharguments off
        $CFG->slasharguments = false;

        $path = '/path/to/file/file.txt';
        $this->assertEqual($CFG->wwwroot.'/file.php?file=%2Fpath%2Fto%2Ffile%2Ffile.txt', get_file_url($path));

        $options = array('var1' => 'value1', 'var2' => 'value2');
        $this->assertEqual($CFG->wwwroot.'/file.php?file=%2Fpath%2Fto%2Ffile%2Ffile.txt&amp;var1=value1&amp;var2=value2', get_file_url($path, $options));
        
        $this->assertEqual($CFG->httpswwwroot.'/file.php?file=%2Fpath%2Fto%2Ffile%2Ffile.txt&amp;var1=value1&amp;var2=value2', get_file_url($path, $options, 'httpscoursefile'));
        
        $path = 'C:\\path\\to\\file.txt';
        $this->assertEqual($CFG->wwwroot.'/file.php?file=%2FC%3A%5Cpath%5Cto%5Cfile.txt&amp;var1=value1&amp;var2=value2', get_file_url($path, $options));
        
        // With slasharguments on
        $CFG->slasharguments = true;
        
        $path = '/path/to/file/file.txt';
        $this->assertEqual($CFG->wwwroot.'/file.php'.$path, get_file_url($path));

        $options = array('var1' => 'value1', 'var2' => 'value2');
        $this->assertEqual($CFG->wwwroot.'/file.php'.$path.'?var1=value1&amp;var2=value2', get_file_url($path, $options));
        
        $this->assertEqual($CFG->httpswwwroot.'/file.php'.$path.'?var1=value1&amp;var2=value2', get_file_url($path, $options, 'httpscoursefile'));
        
        $path = 'C:\\path\\to\\file.txt';
        $this->assertEqual($CFG->wwwroot.'/file.php/C%3A%5Cpath%5Cto%5Cfile.txt?var1=value1&amp;var2=value2', get_file_url($path, $options));

        $path = '/path/to/file/file.txt';
        
        $HTTPSPAGEREQUIRED = true; 
        $this->assertEqual($CFG->httpswwwroot.'/user/pix.php'.$path, get_file_url($path, null, 'user'));
        $HTTPSPAGEREQUIRED = false; 
        $this->assertEqual($CFG->wwwroot.'/user/pix.php'.$path, get_file_url($path, null, 'user'));

        $this->assertEqual($CFG->wwwroot.'/question/exportfile.php'.$path, get_file_url($path, null, 'questionfile'));
        $this->assertEqual($CFG->wwwroot.'/rss/file.php'.$path, get_file_url($path, null, 'rssfile'));
        
        // Test relative path
        $path = 'relative/path/to/file.txt'; 
        $this->assertEqual($CFG->wwwroot.'/file.php/'.$path, get_file_url($path));
        
        // Test with anchor in path
        $path = 'relative/path/to/index.html#anchor1'; 
        $this->assertEqual($CFG->wwwroot.'/file.php/'.$path, get_file_url($path));
        
        // Test with anchor and funny characters in path
        $path = 'rela89èà7(##&$tive/path/to /indéx.html#anchor1'; 
        $this->assertEqual($CFG->wwwroot.'/file.php/rela89%C3%A8%C3%A07%28##%26%24tive/path/to%20/ind%C3%A9x.html#anchor1', get_file_url($path));
    }
}

require_once($CFG->libdir.'/file/file_browser.php'); 
/**
 * Tests for file_browser class
 * @note This class is barely testable. Only one of the methods doesn't make direct calls to complex global functions.
 *       I suggest a rethink of the design, and a jolly good refactoring.
 */
class file_browser_test extends UnitTestCase {
    public function test_encodepath() {
        global $CFG;
        $fb = new file_browser();

        $CFG->slasharguments = true;
        $this->assertEqual('http://test.url.com/path/to/page.php', $fb->encodepath('http://test.url.com', '/path/to/page.php'));
        $this->assertEqual('http://test.url.com/path/to/page.php?forcedownload=1', $fb->encodepath('http://test.url.com', '/path/to/page.php', true));
        $this->assertEqual('https://test.url.com/path/to/page.php?forcedownload=1', $fb->encodepath('http://test.url.com', '/path/to/page.php', true, true));

        // TODO add error checking for malformed path (does method support get variables?)
        $this->assertEqual('http://test.url.com/path/to/page.php?var1=value1&var2=value2', $fb->encodepath('http://test.url.com', '/path/to/page.php?var1=value1&var2=value2'));
        $this->assertEqual('http://test.url.com/path/to/page.php?var1=value1&var2=value2&forcedownload=1', $fb->encodepath('http://test.url.com', '/path/to/page.php?var1=value1&var2=value2', true));

        $CFG->slasharguments = false;
        $this->assertEqual('http://test.url.com?file=%2Fpath%2Fto%2Fpage.php', $fb->encodepath('http://test.url.com', '/path/to/page.php'));
        $this->assertEqual('http://test.url.com?file=%2Fpath%2Fto%2Fpage.php&amp;forcedownload=1', $fb->encodepath('http://test.url.com', '/path/to/page.php', true));
        $this->assertEqual('https://test.url.com?file=%2Fpath%2Fto%2Fpage.php&amp;forcedownload=1', $fb->encodepath('http://test.url.com', '/path/to/page.php', true, true));
    }
}


require_once($CFG->libdir.'/file/file_info_course.php'); 
/**
 * Tests for file_info_course class
 * TODO we need a test course for this
 */
class test_file_info_course extends UnitTestCase {
    public function test_get_params_rawencoded() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $course = new stdClass();
        $course->id = 999999999;
        $course->fullname = 'Test course';

        $fic = new file_info_course(new file_browser(), $context, $course);

        $this->assertEqual($course->fullname, $fic->get_visible_name());
        $this->assertEqual(array(), $fic->get_children());
        $this->assertEqual(array(), $fic->get_parent());
    }
}

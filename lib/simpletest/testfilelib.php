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

class file_info_test extends UnitTestCase {
    protected $course;
    protected $section;
    protected $coursecat;
    protected $user;
    protected $module;

    /**
     * Setup the DB fixture data
     */
    public function setup() {
        global $DB, $CFG;
        // Create a coursecat
        $newcategory = new stdClass();
        $newcategory->name = 'test category';
        $newcategory->sortorder = 999;
        if (!$newcategory->id = $DB->insert_record('course_categories', $newcategory)) {
            print_error('cannotcreatecategory', '', '', format_string($newcategory->name));
        } 
        $newcategory->context = get_context_instance(CONTEXT_COURSECAT, $newcategory->id);
        mark_context_dirty($newcategory->context->path);
        fix_course_sortorder(); // Required to build course_categories.depth and .path.
        $this->coursecat = $DB->get_record('course_categories', array('id' => $newcategory->id));

        // Create a course
        $coursedata = new stdClass();
        $coursedata->category = $newcategory->id;
        $coursedata->shortname = 'testcourse'; 
        $coursedata->fullname = 'Test Course';
        
        try {
            $this->course = create_course($coursedata);
        } catch (moodle_exception $e) {
            // Most likely the result of an aborted unit test: the test course was not correctly deleted
            $this->course = $DB->get_record('course', array('shortname' => $coursedata->shortname));
        }
        
        $this->coursecat->coursecount++;

        // Create a user
        require_once($CFG->dirroot.'/user/lib.php');
        $this->user = new stdClass();
        $this->user->username = 'testuser09987654321';
        $this->user->password = 'password';
        $this->user->firstname = 'TestUser';
        $this->user->lastname = 'TestUser';
        $this->user->email = 'fakeemail@fake.org';
        try {
            $this->user->id = create_user($this->user);
        } catch (moodle_exception $e) {
            // Most likely the result of an aborted unit test: the test user was not correctly deleted
            $this->user->id = $DB->get_field('user', 'id', array('username' => $this->user->username));
        }

        // Create a module
        $module = new stdClass();
        $module->description = 'Assignment used for testing filelib API';
        $module->assignmenttype = 'online';
        $module->timedue = time();
        $module->grade = 100;
        $module->course = $this->course->id;
        $module->name = 'Test Assignment'; 
        $this->section = get_course_section(1, $this->course->id);
        $module->section = $this->section->id;
        $module->module = $DB->get_field('modules', 'id', array('name' => 'assignment'));
        $module->modulename = 'assignment';
        $module->add = 'assignment';
        $module->cmidnumber = '';
        $module->coursemodule = add_course_module($module);

        add_mod_to_section($module);

        $module->cmidnumber = set_coursemodule_idnumber($module->coursemodule, '');
        rebuild_course_cache($this->course->id);
        $module_instance = $DB->get_field('course_modules', 'instance', array('id' => $module->coursemodule));
        $this->module= $DB->get_record('assignment', array('id' => $module_instance));
        $this->module->instance = $module_instance;

        // Update local copy of course
        $this->course = $DB->get_record('course', array('id' => $this->course->id));
    }
    
    public function teardown() {
        global $DB;

        // Delete module
        delete_course_module($this->module->instance);

        // Delete course
        delete_course($this->course, false);

        // Delete category
        $DB->delete_records('course_categories', array('id' => $this->coursecat->id));

        // Delete user
        delete_user($this->user);
    }

}

require_once($CFG->libdir.'/file/file_info_course.php');

class test_file_info_system extends file_info_test {
    public function test_get_children() {
        $context = get_context_instance(CONTEXT_SYSTEM);

        $fis = new file_info_system(new file_browser(), $context);
        $children = $fis->get_children();

        $found_coursecat = false;
        $context_coursecat = get_context_instance(CONTEXT_COURSECAT, $this->coursecat->id);
        $file_info_coursecat = new file_info_coursecat(new file_browser(), $context_coursecat, $this->coursecat);

        foreach ($children as $child) {
            if ($child == $file_info_coursecat) {
                $found_coursecat = true;
            }
        }
        $this->assertTrue($found_coursecat);
    }
}

class test_file_info_coursecat extends file_info_test {
    private $fileinfo;

    public function setup() {
        parent::setup();
        $context = get_context_instance(CONTEXT_COURSECAT, $this->coursecat->id);
        $this->fileinfo = new file_info_coursecat(new file_browser(), $context, $this->coursecat);
    }

    public function test_get_children() {
        $children = $this->fileinfo->get_children();
        $this->assertEqual(2, count($children));
        
        $this->assertEqual('Category introduction', $children[0]->get_visible_name());
        $this->assertEqual('', $children[0]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[0]));

        $context_course = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $fic = new file_info_course(new file_browser(), $context_course, $this->course);
        $this->assertEqual($fic, $children[1]);
    }

    public function test_get_parent() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $fis = new file_info_system(new file_browser(), $context);
        $parent = $this->fileinfo->get_parent();
        $this->assertEqual($parent, $fis); 
    }
}

class test_file_info_course extends file_info_test {
    private $fileinfo;

    public function setup() {
        parent::setup();
        $context = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $this->fileinfo = new file_info_course(new file_browser(), $context, $this->course);
    }

    public function test_get_children() {
        $children = $this->fileinfo->get_children();
        $this->assertEqual(4, count($children));
        
        $this->assertEqual('Course introduction', $children[0]->get_visible_name());
        $this->assertEqual('', $children[0]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[0]));

        $context_course = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $fics = new file_info_coursesection(new file_browser(), $context_course, $this->course);
        $this->assertEqual($fics, $children[1]);

        $this->assertEqual('Backups', $children[2]->get_visible_name());
        $this->assertEqual('', $children[2]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[2]));
        
        $this->assertEqual('Course files', $children[3]->get_visible_name());
        $this->assertEqual('', $children[3]->get_url());
        $this->assertEqual('file_info_coursefile', get_class($children[3]));
    }

    public function test_get_parent() {
        $context = get_context_instance(CONTEXT_COURSECAT, $this->coursecat->id);
        $fic = new file_info_coursecat(new file_browser(), $context, $this->coursecat);
        $parent = $this->fileinfo->get_parent();
        $this->assertEqual($parent, $fic); 
    }
}

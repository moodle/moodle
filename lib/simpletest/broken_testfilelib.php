<?php

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

require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');

/**
 * Parent class used only for setup() and teardown() methods, to create and cleanup test data
 */
class filelib_test extends UnitTestCaseUsingDatabase {
    protected $course;
    protected $section;
    protected $coursecat;
    protected $user;
    protected $module;
    public static $includecoverage = array('lib/filelib.php');

    /**
     * Setup the DB fixture data
     */
    public function setup() {
        parent::setUp();
        $tables = array('block_instances', 'cache_flags', 'capabilities', 'context', 'context_temp',
                        'course', 'course_modules', 'course_categories', 'course_sections','files',
                        'grade_items', 'grade_categories', 'groups', 'groups_members',
                        'modules', 'role', 'role_names', 'role_context_levels', 'role_assignments',
                        'role_capabilities', 'user');
        $this->create_test_tables($tables, 'lib');
        $this->create_test_table('forum', 'mod/forum');
        $this->switch_to_test_db();

        global $DB, $CFG;
        // Insert needed capabilities
        $DB->insert_record('capabilities',
            array('id' => 45, 'name' => 'moodle/course:update', 'cattype' => 'write', 'contextlevel' => 50, 'component' => 'moodle', 'riskbitmask' => 4));
        $DB->insert_record('capabilities',
            array('id' => 14, 'name' => 'moodle/backup:backupcourse', 'cattype' => 'write', 'contextlevel' => 50, 'component' => 'moodle', 'riskbitmask' => 28));
        $DB->insert_record('capabilities',
            array('id' => 17, 'name' => 'moodle/restore:restorecourse', 'cattype' => 'write', 'contextlevel' => 50, 'component' => 'moodle', 'riskbitmask' => 28));
        $DB->insert_record('capabilities',
            array('id' => 52, 'name' => 'moodle/course:managefiles', 'cattype' => 'write', 'contextlevel' => 50, 'component' => 'moodle', 'riskbitmask' => 4));
        $DB->insert_record('capabilities',
            array('id' => 73, 'name' => 'moodle/user:editownprofile', 'cattype' => 'write', 'contextlevel' => 10, 'component' => 'moodle', 'riskbitmask' => 16));

        // Insert system context
        $DB->insert_record('context', array('id' => 1, 'contextlevel' => 10, 'instanceid' => 0, 'path' => '/1', 'depth' => 1));
        $DB->insert_record('context', array('id' => 2, 'contextlevel' => 50, 'instanceid' => 1, 'path' => '/1/2', 'depth' => 2));

        // Insert site course
        $DB->insert_record('course', array('category' => 0, 'sortorder' => 1, 'fullname' => 'Test site', 'shortname' => 'test', 'format' => 'site', 'modinfo' => 'a:0:{}'));

        // User and capability stuff (stolen from testaccesslib.php)
        $syscontext = get_system_context(false);

        /// Now is the correct moment to install capabilities - after creation of legacy roles, but before assigning of roles
        update_capabilities('moodle');
        update_capabilities('mod_forum');

        $contexts = $this->load_test_data('context',
                array('contextlevel', 'instanceid', 'path', 'depth'), array(
           1 => array(40, 666, '', 2)));
        $contexts[0] = $syscontext;
        $contexts[1]->path = $contexts[0]->path . '/' . $contexts[1]->id;
        $this->testdb->set_field('context', 'path', $contexts[1]->path, array('id' => $contexts[1]->id));
        $users = $this->load_test_data('user',
                 array('username', 'confirmed', 'deleted'), array(
        'a' =>   array('a',         1,           0)));
        $admin = $this->testdb->get_record('role', array('shortname' => 'admin'));
        $ras = $this->load_test_data('role_assignments', array('userid', 'roleid', 'contextid'), array( 'a' =>  array($users['a']->id, $admin->id, $contexts[0]->id)));

        $this->switch_global_user_id(1);
        accesslib_clear_all_caches_for_unit_testing();

        // Create a coursecat
        $newcategory = new stdClass();
        $newcategory->name = 'test category';
        $newcategory->sortorder = 999;
        $newcategory->id = $DB->insert_record('course_categories', $newcategory);

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

        // Create a user
        $this->user = new stdClass();
        $this->user->username = 'testuser09987654321';
        $this->user->password = 'password';
        $this->user->firstname = 'TestUser';
        $this->user->lastname = 'TestUser';
        $this->user->email = 'fakeemail@fake.org';
        $this->user->id = create_user($this->user);

        // Assign user to course
        // role_assign(5, $this->user->id, get_context_instance(CONTEXT_COURSE, $this->course->id)->id);

        // Create a module
        $module = new stdClass();
        $module->intro = 'Forum used for testing filelib API';
        $module->type = 'general';
        $module->forcesubscribe = 1;
        $module->format = 1;
        $module->name = 'Test Forum';
        $module->module = $DB->get_field('modules', 'id', array('name' => 'forum'));
        $module->modulename = 'forum';
        $module->add = 'forum';
        $module->cmidnumber = '';
        $module->course = $this->course->id;

        $module->instance = forum_add_instance($module, '');

        $this->section = get_course_section(1, $this->course->id);
        $module->section = $this->section->id;
        $module->coursemodule = add_course_module($module);

        add_mod_to_section($module);

        $module->cmidnumber = set_coursemodule_idnumber($module->coursemodule, '');

        rebuild_course_cache($this->course->id);
        $this->module= $DB->get_record('forum', array('id' => $module->instance));
        $this->module->instance = $module->instance;

        // Update local copy of course
        $this->course = $DB->get_record('course', array('id' => $this->course->id));
    }

    public function teardown() {
        parent::tearDown();
    }

    public function createFiles() {

    }
}

/**
 * Tests for file_browser class
 */

class file_browser_test extends filelib_test {

    public function test_encodepath() {
        global $CFG;
        $fb = new file_browser();

        $CFG->slasharguments = true;
        $this->assertEqual('http://test.url.com/path/to/page.php', file_encode_url('http://test.url.com', '/path/to/page.php'));
        $this->assertEqual('http://test.url.com/path/to/page.php?forcedownload=1', file_encode_url('http://test.url.com', '/path/to/page.php', true));
        $this->assertEqual('https://test.url.com/path/to/page.php?forcedownload=1', file_encode_url('http://test.url.com', '/path/to/page.php', true, true));

        // TODO add error checking for malformed path (does method support get variables?)
        $this->assertEqual('http://test.url.com/path/to/page.php?var1=value1&var2=value2', file_encode_url('http://test.url.com', '/path/to/page.php?var1=value1&var2=value2'));
        $this->assertEqual('http://test.url.com/path/to/page.php?var1=value1&var2=value2&forcedownload=1', file_encode_url('http://test.url.com', '/path/to/page.php?var1=value1&var2=value2', true));

        $CFG->slasharguments = false;
        $this->assertEqual('http://test.url.com?file=%2Fpath%2Fto%2Fpage.php', file_encode_url('http://test.url.com', '/path/to/page.php'));
        $this->assertEqual('http://test.url.com?file=%2Fpath%2Fto%2Fpage.php&amp;forcedownload=1', file_encode_url('http://test.url.com', '/path/to/page.php', true));
        $this->assertEqual('https://test.url.com?file=%2Fpath%2Fto%2Fpage.php&amp;forcedownload=1', file_encode_url('http://test.url.com', '/path/to/page.php', true, true));
    }
}

class test_file_info_context_system extends filelib_test {
    public function test_get_children() {
        $context = get_context_instance(CONTEXT_SYSTEM);

        $fis = new file_info_context_system(new file_browser(), $context);
        $children = $fis->get_children();

        $found_coursecat = false;
        $context_coursecat = get_context_instance(CONTEXT_COURSECAT, $this->coursecat->id);
        $file_info_context_coursecat = new file_info_context_coursecat(new file_browser(), $context_coursecat, $this->coursecat);

        foreach ($children as $child) {
            if ($child == $file_info_context_coursecat) {
                $found_coursecat = true;
            }
        }
        $this->assertTrue($found_coursecat);
    }
}

class test_file_info_context_coursecat extends filelib_test {
    private $fileinfo;

    public function setup() {
        parent::setup();
        $context = get_context_instance(CONTEXT_COURSECAT, $this->coursecat->id);
        $this->fileinfo = new file_info_context_coursecat(new file_browser(), $context, $this->coursecat);
    }

    public function test_get_children() {
        $children = $this->fileinfo->get_children();
        $this->assertEqual(2, count($children));

        // Not sure but I think there should be two children: a file_info_stored object and a file_info_course object.
        $this->assertEqual('Category introduction', $children[0]->get_visible_name());
        $this->assertEqual('', $children[0]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[0]));

        $context_course = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $fic = new file_info_context_course(new file_browser(), $context_course, $this->course);
        $this->assertEqual($fic, $children[1]);
    }

    public function test_get_parent() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $fis = new file_info_context_system(new file_browser(), $context);
        $parent = $this->fileinfo->get_parent();
        $this->assertEqual($parent, $fis);
    }
}

class test_file_info_context_course extends filelib_test {
    private $fileinfo;

    public function setup() {
        parent::setup();
        $context = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $this->fileinfo = new file_info_context_course(new file_browser(), $context, $this->course);
    }

    public function test_get_children() {
        global $DB;

        $children = $this->fileinfo->get_children();
        $this->assertEqual(4, count($children));

        $this->assertEqual('Course introduction', $children[0]->get_visible_name());
        $this->assertEqual('', $children[0]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[0]));

        $context_course = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $fics = new file_info_area_course_section(new file_browser(), $context_course, $this->course);
        $this->assertEqual($fics, $children[1]);

        $this->assertEqual('Backups', $children[2]->get_visible_name());
        $this->assertEqual('', $children[2]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[2]));

        $this->assertEqual('Course files', $children[3]->get_visible_name());
        $this->assertEqual('', $children[3]->get_url());
        $this->assertEqual('file_info_area_course_legacy', get_class($children[3]));

    }

    public function test_get_parent() {
        $context = get_context_instance(CONTEXT_COURSECAT, $this->coursecat->id);
        $fic = new file_info_context_coursecat(new file_browser(), $context, $this->coursecat);
        $parent = $this->fileinfo->get_parent();
        $this->assertEqual($parent, $fic);
    }
}

class test_file_info_context_user extends filelib_test {
    private $fileinfo;

    public function setup() {
        parent::setup();
        $context = get_context_instance(CONTEXT_USER, $this->user->id);
        $this->fileinfo = new file_info_context_user(new file_browser(), $context, $this->user);
    }

    public function test_get_parent() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $fic = new file_info_context_system(new file_browser(), $context);
        $parent = $this->fileinfo->get_parent();
        $this->assertEqual($parent, $fic);
    }

    public function test_get_children() {
        $children = $this->fileinfo->get_children();
        $this->assertEqual(2, count($children));

        $this->assertEqual('Personal', $children[0]->get_visible_name());
        $this->assertEqual('', $children[0]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[0]));

        $this->assertEqual('Profile', $children[1]->get_visible_name());
        $this->assertEqual('', $children[1]->get_url());
        $this->assertEqual('file_info_stored', get_class($children[1]));
    }
}

class test_file_info_context_module extends filelib_test {
    private $fileinfo;

    public function setup() {
        global $DB;
        parent::setup();
        $context = get_context_instance(CONTEXT_MODULE, $DB->get_field('course_modules', 'id', array('instance' => $this->module->instance)));
        $this->fileinfo = new file_info_context_module(new file_browser(), $this->course, $this->module->instance, $context, array());
    }

    public function test_get_parent() {
        $context = get_context_instance(CONTEXT_COURSE, $this->course->id);
        $fic = new file_info_context_course(new file_browser(), $context, $this->course);
        $parent = $this->fileinfo->get_parent();
        $this->assertEqual($parent, $fic);
    }

    public function test_get_children() {
        $children = $this->fileinfo->get_children();
        $this->assertEqual(0, count($children));
    }
}

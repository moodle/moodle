<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Unit tests for blog
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/blog/locallib.php');

class bloglib_test extends UnitTestCaseUsingDatabase {

    public static $includecoverage = array('blog/locallib.php');

    public function test_overrides() {
        // Try all the filters at once: Only the entry filter is active
        $blog_listing = new blog_listing(array('site' => 1, 'course' => 1, 'module' => 1, 'group' => 1, 'user' => 1, 'tag' => 1, 'entry' => 1));
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('module', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertFalse(array_key_exists('user', $blog_listing->filters));
        $this->assertFalse(array_key_exists('tag', $blog_listing->filters));
        $this->assertTrue(array_key_exists('entry', $blog_listing->filters));

        // Again, but without the entry filter: This time, the tag, user and module filters are active
        $blog_listing = new blog_listing(array('site' => 1, 'course' => 1, 'module' => 1, 'group' => 1, 'user' => 1, 'tag' => 1));
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('module', $blog_listing->filters));
        $this->assertTrue(array_key_exists('user', $blog_listing->filters));
        $this->assertTrue(array_key_exists('tag', $blog_listing->filters));

        // We should get the same result by removing the 3 inactive filters: site, course and group:
        $blog_listing = new blog_listing(array('module' => 1, 'user' => 1, 'tag' => 1));
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('module', $blog_listing->filters));
        $this->assertTrue(array_key_exists('user', $blog_listing->filters));
        $this->assertTrue(array_key_exists('tag', $blog_listing->filters));

        // Now use the group and module together
        $blog_listing = new blog_listing(array('module' => 1, 'group' => 1, 'tag' => 1));
        $this->assertTrue(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('module', $blog_listing->filters));
        $this->assertFalse(array_key_exists('user', $blog_listing->filters));
        $this->assertTrue(array_key_exists('tag', $blog_listing->filters));

        $blog_listing = new blog_listing(array('course' => 2));
        $this->assertTrue(array_key_exists('course', $blog_listing->filters));

        $blog_listing = new blog_listing(array('course' => 2, 'group' => 12));
        $this->assertFalse(array_key_exists('course', $blog_listing->filters));
        $this->assertTrue(array_key_exists('group', $blog_listing->filters));

        $blog_listing = new blog_listing(array('site' => 2, 'group' => 12));
        $this->assertFalse(array_key_exists('site', $blog_listing->filters));
        $this->assertTrue(array_key_exists('group', $blog_listing->filters));

        $blog_listing = new blog_listing(array('user' => 2, 'group' => 12));
        $this->assertFalse(array_key_exists('group', $blog_listing->filters));
        $this->assertTrue(array_key_exists('user', $blog_listing->filters));

    }

    /**
     * Some user, course, module, group and blog sample data needs to be setup for this test
     */
    public function test_blog_get_headers_case_1() {
        global $CFG, $PAGE, $OUTPUT;
        
        $this->create_test_tables('post', 'tag', 'course', 'user', 'role', 'role_assignments', 'group', 'blog_associations', 
                                  'course_modules', 'role_capabilities', 'assignment', 'tag_correlation', 'tag_instance');
        
        $contexts = $this->load_test_data('context',
                array('contextlevel', 'instanceid', 'path', 'depth'), array(
           1 => array(40, 666, '', 2),
           2 => array(50, 666, '', 3),
           3 => array(70, 666, '', 4),
        ));

        $this->load_test_data('course', 
                              array('id', 'fullname', 'shortname', 'format'), 
                              array(
                                array(1, 'My Moodle Site', 'moodle', 'site'),
                                array(2, 'Course 1', 'course1', 'weeks'),
                                array(3, 'Course 2', 'course2', 'weeks')
                                )
                              );
        $this->load_test_data('user',
                              array('id', 'confirmed', 'username', 'firstname', 'lastname'),
                              array( array(1, 1, 'joebloe', 'Joe', 'Bloe')));

        $this->switch_to_test_db();
        
        $userrole = create_role(get_string('authenticateduser'), 'user', get_string('authenticateduserdescription'), 'moodle/legacy:user');
        $student = $this->testdb->get_record('role', array('shortname' => 'student'));
        
        $ras = $this->load_test_data('role_assignments', array('userid', 'roleid', 'contextid'),
                                     array(array(1, $student->id, $context[2]->id)));

        // Case 1: A single blog entry
        $PAGE->url = new moodle_url($CFG->wwwroot . '/blog/index.php', array('entryid' => 1));
        $blog_headers = blog_get_headers();

        $this->assertEqual($blog_headers['title'], '');
    }
}

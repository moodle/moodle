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
 * PHPUnit Mhaairs advanced test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit mhaairs advanced test case base class.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2015 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class block_mhaairs_testcase extends advanced_testcase {
    protected $page;
    protected $course;
    protected $roles;
    protected $bi;
    protected $block;
    protected $guest;
    protected $teacher;
    protected $assistant;
    protected $student1;
    protected $student2;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        global $DB; //, $PAGE;

        $this->resetAfterTest(true);

        // Create a course we are going to add the block to.
        // This is Test course 1 | tc_1.
        // Add idnumber tc1, so that we can test identity type.
        $record = array('idnumber' => 'tc1');
        $this->course = $this->getDataGenerator()->create_course($record);
        $courseid = $this->course->id;

        // Create users and enroll them in the course.
        $roles = $DB->get_records_menu('role', array(), '', 'shortname,id');
        $this->roles = $roles;

        // Teacher.
        $user = $this->getDataGenerator()->create_user(array('username' => 'teacher'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles['editingteacher']);
        $this->teacher = $user;

        // Assistant.
        $user = $this->getDataGenerator()->create_user(array('username' => 'assistant'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles['teacher']);
        $this->assistant = $user;

        // Student1.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student1'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles['student']);
        $this->student1 = $user;

        // Student2.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student2'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles['student']);
        $this->student2 = $user;

        // Guest.
        $user = $DB->get_record('user', array('username' => 'guest'));
        $this->guest = $user;

        // Add an mhaairs block.
        $this->bi = $this->add_mhaairs_block_in_context(context_course::instance($courseid));

    }

    protected function add_mhaairs_block_in_context(context $context) {
        global $DB, $PAGE;

        $course = null;

        $page = new \moodle_page();
        $page->set_context($context);

        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                $page->set_pagelayout('frontpage');
                $page->set_pagetype('site-index');
                break;
            case CONTEXT_COURSE:
                $page->set_pagelayout('standard');
                $page->set_pagetype('course-view');
                $course = $DB->get_record('course', ['id' => $context->instanceid]);
                $page->set_course($course);
                break;
            case CONTEXT_MODULE:
                $page->set_pagelayout('standard');
                $mod = $DB->get_field_sql("SELECT m.name
                                             FROM {modules} m
                                             JOIN {course_modules} cm on cm.module = m.id
                                            WHERE cm.id = ?", [$context->instanceid]);
                $page->set_pagetype("mod-$mod-view");
                break;
            case CONTEXT_USER:
                $page->set_pagelayout('mydashboard');
                $page->set_pagetype('my-index');
                break;
            default:
                throw new coding_exception('Unsupported context for test');
        }

        $page->blocks->load_blocks();

        $page->blocks->add_block_at_end_of_default_region('mhaairs');

        // We need to use another page object as load_blocks() only loads the blocks once.
        $page2 = new \moodle_page();
        $page2->set_context($page->context);
        $page2->set_pagelayout($page->pagelayout);
        $page2->set_pagetype($page->pagetype);
        if ($course) {
            $page2->set_course($course);
        }

        $page2->blocks->load_blocks();
        $blocks = $page2->blocks->get_blocks_for_region($page2->blocks->get_default_region());
        $block = end($blocks);

        // Set the PAGE so that it recognizes the block.
        $PAGE = $page2;

        return $block->instance;
    }

    /**
     * Sets the user.
     *
     * @return void
     */
    protected function set_user($username) {
        if ($username == 'admin') {
            $this->setAdminUser();
        } else if ($username == 'guest') {
            $this->setGuestUser();
        } else {
            $this->setUser($this->$username);
        }
    }

    /**
     * Adds a grade item via the update grade service.
     *
     * @param string courseid
     * @param string iteminstance
     * @param array options
     * @return void
     */
    protected function add_grade_item_by_service($courseid, $iteminstance, $options = array()) {
        $callback = 'block_mhaairs_gradebookservice_external::update_grade';

        // Service params.
        $serviceparams = array(
            'source' => 'mhaairs',
            'courseid' => $courseid,
            'itemtype' => 'manual',
            'itemmodule' => 'mhaairs',
            'iteminstance' => $iteminstance,
            'itemnumber' => '0',
            'grades' => null,
            'itemdetails' => null,
        );

        $category = !empty($options['category']) ? $options['category'] : '';
        $deleted = !empty($options['deleted']) ? $options['deleted'] : '';
        $identitytype = !empty($options['identitytype']) ? $options['identitytype'] : '';
        $useexisting = !empty($options['useexisting']) ? $options['useexisting'] : '';

        // Item details.
        $itemdetails = array(
            'categoryid' => $category,
            'itemname' => $iteminstance,
            'idnumber' => 0,
            'gradetype' => GRADE_TYPE_VALUE,
            'grademax' => 100,
            'hidden' => '',
            'deleted' => $deleted,
            'identity_type' => $identitytype,
            'needsupdate' => '',
            'useexisting' => $useexisting,
        );

        // Create first grade item.
        $itemdetailsjson = urlencode(json_encode($itemdetails));
        $serviceparams['itemdetails'] = $itemdetailsjson;

        $result = call_user_func_array($callback, $serviceparams);
        return $result;
    }
}

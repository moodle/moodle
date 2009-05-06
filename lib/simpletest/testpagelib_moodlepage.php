<?php // $Id$

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
 * Tests for the parts of ../filterlib.php that handle creating filter objects,
 * and using them to filter strings.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/pagelib.php');

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class moodle_page_test extends UnitTestCase {
    protected $testpage;
    protected $originalcourse;

    public function setUp() {
        global $COURSE;
        $this->originalcourse = $COURSE;
        $this->testpage = new moodle_page();
    }

    public function tearDown() {
        global $COURSE;
        $this->testpage = NULL;
        $COURSE = $this->originalcourse;
    }

    /** Creates an object with all the fields you would expect a $course object to have. */
    protected function create_a_course() {
        $course = new stdClass;
        $course->id = -1;
        $course->category = 2;
        $course->fullname = 'Anonymous test course';
        $course->shortname = 'ANON';
        $course->summary = '';
        return $course;
    }

    /** Creates an object with all the fields you would expect a $course object to have. */
    protected function create_a_context() {
        $context = new stdClass;
        $context->id = 2;
        $context->contextlevel = CONTEXT_COURSECAT;
        $context->instanceid = 1;
        $context->path = '/1/2';
        $context->depth = '2';
        return $context;
    }

    public function test_course_returns_site_before_set() {
        global $SITE;
        // Validate
        $this->assertIdentical($SITE, $this->testpage->course);
    }

    public function test_setting_course_works() {
        // Setup fixture
        $course = $this->create_a_course();
        $this->testpage->set_context(new stdClass); // Avoid trying to set the context.
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($course), $this->testpage->course);
    }

    public function test_global_course_and_page_course_are_same() {
        global $COURSE;
        // Setup fixture
        $course = $this->create_a_course();
        $this->testpage->set_context(new stdClass); // Avoid trying to set the context.
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assertIdentical($this->testpage->course, $COURSE);
    }

    public function test_cannot_set_course_once_output_started() {
        // Setup fixture
        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $course = $this->create_a_course();
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_course($course);
    }

    public function test_set_state_normal_path() {
        $this->assertEqual(moodle_page::STATE_BEFORE_HEADER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->assertEqual(moodle_page::STATE_PRINTING_HEADER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        $this->assertEqual(moodle_page::STATE_IN_BODY, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_PRINTING_FOOTER);
        $this->assertEqual(moodle_page::STATE_PRINTING_FOOTER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_DONE);
        $this->assertEqual(moodle_page::STATE_DONE, $this->testpage->state);
    }

    public function test_set_state_cannot_skip_one() {
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
    }

    public function test_header_printed_false_initially() {
        // Validate
        $this->assertFalse($this->testpage->headerprinted);
    }

    public function test_header_printed_becomes_true() {
        // Exercise SUT
        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        // Validate
        $this->assertTrue($this->testpage->headerprinted);
    }

    public function test_cant_get_context_before_set() {
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->context;
    }

    public function test_set_context() {
        // Setup fixture
        $context = $this->create_a_context();
        // Exercise SUT
        $this->testpage->set_context($context);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($context), $this->testpage->context);
    }
}

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class moodle_page_with_db_test extends UnitTestCaseUsingDatabase {
    protected $testpage;
    protected $originalcourse;

    public function setUp() {
        global $COURSE;
        $this->originalcourse = $COURSE;
        $this->testpage = new moodle_page();
        $this->create_test_table('context', 'lib');
        $this->switch_to_test_db();
    }

    public function tearDown() {
        global $COURSE;
        $this->testpage = NULL;
        $COURSE = $this->originalcourse;
    }

    /** Creates an object with all the fields you would expect a $course object to have. */
    protected function create_a_course_with_context() {
        $course = new stdClass;
        $course->id = -1;
        $course->category = 2;
        $course->fullname = 'Anonymous test course';
        $course->shortname = 'ANON';
        $course->summary = '';

        $context = new stdClass;
        $context->contextlevel = CONTEXT_COURSE;
        $context->instanceid = $course->id;
        $context->path = 'not initialised';
        $context->depth = '-1';
        $this->testdb->insert_record('context', $context);

        return $course;
    }

    public function test_setting_course_sets_context() {
        // Setup fixture
        $course = $this->create_a_course_with_context();
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $expectedcontext = new stdClass;
        $expectedcontext->contextlevel = CONTEXT_COURSE;
        $expectedcontext->instanceid = $course->id;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedcontext), $this->testpage->context);
    }
}
?>

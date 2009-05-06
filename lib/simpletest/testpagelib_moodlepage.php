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

class testable_moodle_page extends moodle_page {
    public function initialise_default_pagetype($script = '') {
        parent::initialise_default_pagetype($script);
    }
    public function url_to_class_name($url) {
        return parent::url_to_class_name($url);
    }
}

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class moodle_page_test extends UnitTestCase {
    protected $testpage;
    protected $originalcourse;

    public function setUp() {
        global $COURSE;
        $this->originalcourse = $COURSE;
        $this->testpage = new testable_moodle_page();
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

    public function test_cannot_set_category_once_output_started() {
        // Setup fixture
        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_category_by_id(123);
    }

    public function test_cannot_set_category_once_course_set() {
        // Setup fixture
        $course = $this->create_a_course();
        $this->testpage->set_context(new stdClass); // Avoid trying to set the context.
        $this->testpage->set_course($course);
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_category_by_id(123);
    }

    public function test_categories_array_empty_for_front_page() {
        // Setup fixture
        $course = $this->create_a_course();
        $course->category = 0;
        $this->testpage->set_context(new stdClass); // Avoid trying to set the context.
        $this->testpage->set_course($course);
        // Exercise SUT and validate.
        $this->assertEqual(array(), $this->testpage->categories);
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

    public function test_pagetype_defaults_to_script() {
        // Exercise SUT and validate
        $this->assertEqual('admin-report-unittest-index', $this->testpage->pagetype);
    }

    public function test_set_pagetype() {
        // Exercise SUT
        $this->testpage->set_pagetype('a-page-type');
        // Validate
        $this->assertEqual('a-page-type', $this->testpage->pagetype);
    }

    public function test_initialise_default_pagetype() {
        // Exercise SUT
        $this->testpage->initialise_default_pagetype('admin/report/unittest/index.php');
        // Validate
        $this->assertEqual('admin-report-unittest-index', $this->testpage->pagetype);
    }

    public function test_initialise_default_pagetype_fp() {
        // Exercise SUT
        $this->testpage->initialise_default_pagetype('index.php');
        // Validate
        $this->assertEqual('site-index', $this->testpage->pagetype);
    }

    public function test_get_body_classes_empty() {
        // Validate
        $this->assertEqual('', $this->testpage->bodyclasses);
    }

    public function test_get_body_classes_single() {
        // Exercise SUT
        $this->testpage->add_body_class('aclassname');
        // Validate
        $this->assertEqual('aclassname', $this->testpage->bodyclasses);
    }

    public function test_get_body_classes() {
        // Exercise SUT
        $this->testpage->add_body_classes(array('aclassname', 'anotherclassname'));
        // Validate
        $this->assertEqual('aclassname anotherclassname', $this->testpage->bodyclasses);
    }

    public function test_url_to_class_name() {
        $this->assertEqual('example-com', $this->testpage->url_to_class_name('http://example.com'));
        $this->assertEqual('example-com--80', $this->testpage->url_to_class_name('http://example.com:80'));
        $this->assertEqual('example-com--moodle', $this->testpage->url_to_class_name('https://example.com/moodle'));
        $this->assertEqual('example-com--8080--nested-moodle', $this->testpage->url_to_class_name('https://example.com:8080/nested/moodle'));
    }

    public function test_set_docs_path() {
        // Exercise SUT
        $this->testpage->set_docs_path('a/file/path');
        // Validate
        $this->assertEqual('a/file/path', $this->testpage->docspath);
    }

    public function test_docs_path_defaults_from_pagetype() {
        // Exercise SUT
        $this->testpage->set_pagetype('a-page-type');
        // Validate
        $this->assertEqual('a/page/type', $this->testpage->docspath);
    }
}

/**
 * Test functions that rely on the context table.
 */
class moodle_page_with_context_table_test extends UnitTestCaseUsingDatabase {
    protected $testpage;
    protected $originalcourse;

    public function setUp() {
        global $COURSE;
        parent::setUp();
        $this->originalcourse = $COURSE;
        $this->testpage = new moodle_page();
        $this->create_test_table('context', 'lib');
        $this->switch_to_test_db();
    }

    public function tearDown() {
        global $COURSE;
        $this->testpage = NULL;
        $COURSE = $this->originalcourse;
        parent::tearDown();
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

/**
 * Test functions that rely on the context table.
 */
class moodle_page_categories_test extends UnitTestCaseUsingDatabase {
    protected $testpage;
    protected $originalcourse;

    public function setUp() {
        global $COURSE, $SITE;
        parent::setUp();
        $this->originalcourse = $COURSE;
        $this->testpage = new moodle_page();
        $this->create_test_tables(array('course_categories', 'context'), 'lib');
        $this->switch_to_test_db();

        $context = new stdClass;
        $context->contextlevel = CONTEXT_COURSE;
        $context->instanceid = $SITE->id;
        $context->path = 'not initialised';
        $context->depth = '-1';
        $this->testdb->insert_record('context', $context);
    }

    public function tearDown() {
        global $COURSE;
        $this->testpage = NULL;
        $COURSE = $this->originalcourse;
        parent::tearDown();
    }

    /** Creates an object with all the fields you would expect a $course object to have. */
    protected function create_a_category_with_context($parentid = 0) {
        if ($parentid) {
            $parent = $this->testdb->get_record('course_categories', array('id' => $parentid));
        } else {
            $parent = new stdClass;
            $parent->depth = 0;
            $parent->path = '';
        }
        $cat = new stdClass;
        $cat->name = 'Anonymous test category';
        $cat->description = '';
        $cat->parent = $parentid;
        $cat->depth = $parent->depth + 1;
        $cat->id = $this->testdb->insert_record('course_categories', $cat);
        $cat->path = $parent->path . '/' . $cat->id;
        $this->testdb->set_field('course_categories', 'path', $cat->path, array('id' => $cat->id));

        $context = new stdClass;
        $context->contextlevel = CONTEXT_COURSECAT;
        $context->instanceid = $cat->id;
        $context->path = 'not initialised';
        $context->depth = '-1';
        $this->testdb->insert_record('context', $context);

        return $cat;
    }

    public function test_set_category_top_level() {
        // Setup fixture
        $cat = $this->create_a_category_with_context();
        // Exercise SUT
        $this->testpage->set_category_by_id($cat->id);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($cat), $this->testpage->category);
        $expectedcontext = new stdClass; // Test it sets the context.
        $expectedcontext->contextlevel = CONTEXT_COURSECAT;
        $expectedcontext->instanceid = $cat->id;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedcontext), $this->testpage->context);
    }

    public function test_set_nested_categories() {
        // Setup fixture
        $topcat = $this->create_a_category_with_context();
        $subcat = $this->create_a_category_with_context($topcat->id);
        // Exercise SUT
        $this->testpage->set_category_by_id($subcat->id);
        // Validate
        $categories = $this->testpage->categories;
        $this->assertEqual(2, count($categories));
        $this->assert(new CheckSpecifiedFieldsExpectation($topcat), array_pop($categories));
        $this->assert(new CheckSpecifiedFieldsExpectation($subcat), array_pop($categories));
    }
}

?>

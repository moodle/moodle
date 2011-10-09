<?php

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
 * Tests for the moodle_page class in ../pagelib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/blocklib.php');

/** Test-specific subclass to make some protected things public. */
class testable_moodle_page extends moodle_page {
    public function initialise_default_pagetype($script = null) {
        parent::initialise_default_pagetype($script);
    }
    public function url_to_class_name($url) {
        return parent::url_to_class_name($url);
    }
    public function all_editing_caps() {
        return parent::all_editing_caps();
    }
}

/**
 * Test functions that don't need to touch the database.
 */
class moodle_page_test extends UnitTestCase {
    protected $testpage;
    protected $originalcourse;
    protected $originalpage;
    public static $includecoverage = array('lib/pagelib.php', 'lib/blocklib.php');

    public function setUp() {
        global $COURSE, $PAGE;
        $this->originalcourse = $COURSE;
        $this->originalpage = $PAGE;
        $this->testpage = new testable_moodle_page();
    }

    public function tearDown() {
        global $COURSE, $PAGE;
        $this->testpage = NULL;
        $COURSE = $this->originalcourse;
        $PAGE = $this->originalpage;
    }

    /** Creates an object with all the fields you would expect a $course object to have. */
    protected function create_a_course() {
        $course = new stdClass;
        $course->id = 13; // Why 13, good question. MDL-21007
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
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM)); // Avoid trying to set the context.
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($course), $this->testpage->course);
    }

    public function test_global_course_and_page_course_are_same_with_global_page() {
        global $COURSE, $PAGE;
        // Setup fixture
        $course = $this->create_a_course();
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM)); // Avoid trying to set the context.
        $PAGE = $this->testpage;
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assertIdentical($this->testpage->course, $COURSE);
    }

    public function test_global_course_not_changed_with_non_global_page() {
        global $COURSE;
        $originalcourse = $COURSE;
        // Setup fixture
        $course = $this->create_a_course();
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM)); // Avoid trying to set the context.
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assertIdentical($originalcourse, $COURSE);
    }

    public function test_cannot_set_course_once_theme_set() {
        // Setup fixture
        $this->testpage->force_theme(theme_config::DEFAULT_THEME);
        $course = $this->create_a_course();
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_course($course);
    }

    public function test_cannot_set_category_once_theme_set() {
        // Setup fixture
        $this->testpage->force_theme(theme_config::DEFAULT_THEME);
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_category_by_id(123);
    }

    public function test_cannot_set_category_once_course_set() {
        // Setup fixture
        $course = $this->create_a_course();
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM)); // Avoid trying to set the context.
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
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM)); // Avoid trying to set the context.
        $this->testpage->set_course($course);
        // Exercise SUT and validate.
        $this->assertEqual(array(), $this->testpage->categories);
    }

    public function test_set_state_normal_path() {
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM));
        $this->testpage->set_course($this->create_a_course());

        $this->assertEqual(moodle_page::STATE_BEFORE_HEADER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->assertEqual(moodle_page::STATE_PRINTING_HEADER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        $this->assertEqual(moodle_page::STATE_IN_BODY, $this->testpage->state);

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
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM));
        $this->testpage->set_course($this->create_a_course());

        // Exercise SUT
        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        // Validate
        $this->assertTrue($this->testpage->headerprinted);
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

    public function test_set_url_root() {
        global $CFG;
        // Exercise SUT
        $this->testpage->set_url('/');
        // Validate
        $this->assertEqual($CFG->wwwroot . '/', $this->testpage->url->out());
    }

    public function test_set_url_one_param() {
        global $CFG;
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123));
        // Validate
        $this->assertEqual($CFG->wwwroot . '/mod/quiz/attempt.php?attempt=123', $this->testpage->url->out());
    }

    public function test_set_url_two_params() {
        global $CFG;
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validate
        $this->assertEqual($CFG->wwwroot . '/mod/quiz/attempt.php?attempt=123&amp;page=7', $this->testpage->url->out());
    }

    public function test_set_url_using_moodle_url() {
        global $CFG;
        // Fixture setup
        $url = new moodle_url('/mod/workshop/allocation.php', array('cmid' => 29, 'method' => 'manual'));
        // Exercise SUT
        $this->testpage->set_url($url);
        // Validate
        $this->assertEqual($CFG->wwwroot . '/mod/workshop/allocation.php?cmid=29&amp;method=manual', $this->testpage->url->out());
    }

    public function test_set_url_sets_page_type() {
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validate
        $this->assertEqual('mod-quiz-attempt', $this->testpage->pagetype);
    }

    public function test_set_url_does_not_change_explicit_page_type() {
        // Setup fixture
        $this->testpage->set_pagetype('a-page-type');
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validate
        $this->assertEqual('a-page-type', $this->testpage->pagetype);
    }

    public function test_set_subpage() {
        // Exercise SUT
        $this->testpage->set_subpage('somestring');
        // Validate
        $this->assertEqual('somestring', $this->testpage->subpage);
    }

    public function test_set_heading() {
        // Exercise SUT
        $this->testpage->set_heading('a heading');
        // Validate
        $this->assertEqual('a heading', $this->testpage->heading);
    }

    public function test_set_title() {
        // Exercise SUT
        $this->testpage->set_title('a title');
        // Validate
        $this->assertEqual('a title', $this->testpage->title);
    }

    public function test_default_pagelayout() {
        // Exercise SUT and Validate
        $this->assertEqual('base', $this->testpage->pagelayout);
    }

    public function test_set_pagelayout() {
        // Exercise SUT
        $this->testpage->set_pagelayout('type');
        // Validate
        $this->assertEqual('type', $this->testpage->pagelayout);
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
        $course->id = 13;
        $course->category = 2;
        $course->fullname = 'Anonymous test course';
        $course->shortname = 'ANON';
        $course->summary = '';

        $context = new stdClass;
        $context->contextlevel = CONTEXT_COURSE;
        $context->instanceid = $course->id;
        $context->path = 'not initialised';
        $context->depth = '13';
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
        $context->depth = '13';
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
        $context->depth = '13';
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

/**
 * Test functions that rely on the context table.
 */
class moodle_page_cm_test extends UnitTestCaseUsingDatabase {
    protected $testpage;
    protected $originalcourse;

    public function setUp() {
        global $COURSE, $SITE;
        parent::setUp();
        $this->originalcourse = $COURSE;
        $this->testpage = new moodle_page();
        $this->create_test_tables(array('course', 'context', 'modules', 'course_modules', 'course_modules_availability', 'grade_items', 'course_sections'), 'lib');
        $this->create_test_table('forum', 'mod/forum');
        $this->switch_to_test_db();

        $context = new stdClass;
        $context->contextlevel = CONTEXT_COURSE;
        $context->instanceid = $SITE->id;
        $context->path = 'not initialised';
        $context->depth = '13';
        $this->testdb->insert_record('context', $context);
    }

    public function tearDown() {
        global $COURSE;
        $this->testpage = NULL;
        $COURSE = $this->originalcourse;
        parent::tearDown();
    }

    /** Creates an object with all the fields you would expect a $course object to have. */
    protected function create_a_forum_with_context() {
        $course = new stdClass;
        $course->category = 2;
        $course->fullname = 'Anonymous test course';
        $course->shortname = 'ANON';
        $course->summary = '';
        $course->modinfo = null;
        $course->id = $this->testdb->insert_record('course', $course);

        $forum = new stdClass;
        $forum->course = $course->id;
        $forum->name = 'Anonymouse test forum';
        $forum->intro = '';
        $forum->id = $this->testdb->insert_record('forum', $forum);

        $module = new stdClass;
        $module->name = 'forum';
        $module->id = $this->testdb->insert_record('modules', $module);

        $cm = new stdClass;
        $cm->course = $course->id;
        $cm->instance = $forum->id;
        $cm->modname = 'forum';
        $cm->module = $module->id;
        $cm->name = $forum->name;
        $cm->id = $this->testdb->insert_record('course_modules', $cm);

        $section = new stdClass;
        $section->course = $course->id;
        $section->section = 0;
        $section->sequence = $cm->id;
        $section->id = $this->testdb->insert_record('course_sections', $section);

        $context = new stdClass;
        $context->contextlevel = CONTEXT_MODULE;
        $context->instanceid = $cm->id;
        $context->path = 'not initialised';
        $context->depth = '13';
        $this->testdb->insert_record('context', $context);

        return array($cm, $course, $forum);
    }

    public function test_cm_null_initially() {
        // Validate
        $this->assertNull($this->testpage->cm);
    }

    public function test_set_cm() {
        // Setup fixture
        list($cm) = $this->create_a_forum_with_context();
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($cm), $this->testpage->cm);
    }

    public function test_cannot_set_activity_record_before_cm() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        // Set expectation
        $this->expectException();
        // Exercise SUT
        $this->testpage->set_activity_record($forum);
    }

    public function test_setting_cm_sets_context() {
        // Setup fixture
        list($cm) = $this->create_a_forum_with_context();
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        $expectedcontext = new stdClass;
        $expectedcontext->contextlevel = CONTEXT_MODULE;
        $expectedcontext->instanceid = $cm->id;
        $this->assert(new CheckSpecifiedFieldsExpectation($expectedcontext), $this->testpage->context);
    }

    public function test_activity_record_loaded_if_not_set() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($forum), $this->testpage->activityrecord);
    }

    public function test_set_activity_record() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        $this->testpage->set_cm($cm);
        // Exercise SUT
        $this->testpage->set_activity_record($forum);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($forum), $this->testpage->activityrecord);
    }

    public function test_cannot_set_inconsistent_activity_record_course() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        $this->testpage->set_cm($cm);
        // Set expectation
        $this->expectException();
        // Exercise SUT
        $forum->course = 13;
        $this->testpage->set_activity_record($forum);
    }

    public function test_cannot_set_inconsistent_activity_record_instance() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        $this->testpage->set_cm($cm);
        // Set expectation
        $this->expectException();
        // Exercise SUT
        $forum->id = 13;
        $this->testpage->set_activity_record($forum);
    }

    public function test_setting_cm_sets_course() {
        // Setup fixture
        list($cm, $course) = $this->create_a_forum_with_context();
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        unset($course->modinfo); // This changed, but we don't care
        $this->assert(new CheckSpecifiedFieldsExpectation($course), $this->testpage->course);
    }

    public function test_set_cm_with_course_and_activity_no_db() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        // This only works without db if we already have modinfo cache
        $modinfo = get_fast_modinfo($course);
        $this->drop_test_table('forum');
        $this->drop_test_table('course');
        // Exercise SUT
        $this->testpage->set_cm($cm, $course, $forum);
        // Validate
        $this->assert(new CheckSpecifiedFieldsExpectation($cm), $this->testpage->cm);
        $this->assert(new CheckSpecifiedFieldsExpectation($course), $this->testpage->course);
        $this->assert(new CheckSpecifiedFieldsExpectation($forum), $this->testpage->activityrecord);
    }

    public function test_cannot_set_cm_with_inconsistent_course() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        // Set expectation
        $this->expectException();
        // Exercise SUT
        $cm->course = 13;
        $this->testpage->set_cm($cm, $course);
    }

    public function test_get_activity_name() {
        // Setup fixture
        list($cm, $course, $forum) = $this->create_a_forum_with_context();
        // Exercise SUT
        $this->testpage->set_cm($cm, $course, $forum);
        // Validate
        $this->assertEqual('forum', $this->testpage->activityname);
    }
}

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class moodle_page_editing_test extends UnitTestCase {
    protected $testpage;
    protected $originaluserediting;

    public function setUp() {
        global $USER;
        $this->originaluserediting = !empty($USER->editing);
        $this->testpage = new testable_moodle_page();
        $this->testpage->set_context(get_context_instance(CONTEXT_SYSTEM));
    }

    public function tearDown() {
        global $USER;
        $this->testpage = NULL;
        $USER->editing = $this->originaluserediting;
    }

    // We are relying on the fact that unit tests are alwyas run by admin, to
    // ensure the user_allows_editing call returns true.
    public function test_user_is_editing_on() {
        // Setup fixture
        global $USER;
        $USER->editing = true;
        // Validate
        $this->assertTrue($this->testpage->user_is_editing());
    }

    // We are relying on the fact that unit tests are alwyas run by admin, to
    // ensure the user_allows_editing call returns true.
    public function test_user_is_editing_off() {
        // Setup fixture
        global $USER;
        $USER->editing = false;
        // Validate
        $this->assertFalse($this->testpage->user_is_editing());
    }

    public function test_default_editing_capabilities() {
        // Validate
        $this->assertEqual(array('moodle/site:manageblocks'), $this->testpage->all_editing_caps());
    }

    public function test_other_block_editing_cap() {
        // Exercise SUT
        $this->testpage->set_blocks_editing_capability('moodle/my:manageblocks');
        // Validate
        $this->assertEqual(array('moodle/my:manageblocks'), $this->testpage->all_editing_caps());
    }

    public function test_other_editing_cap() {
        // Exercise SUT
        $this->testpage->set_other_editing_capability('moodle/course:manageactivities');
        // Validate
        $actualcaps = $this->testpage->all_editing_caps();
        $expectedcaps = array('moodle/course:manageactivities', 'moodle/site:manageblocks');
        $this->assert(new ArraysHaveSameValuesExpectation($expectedcaps), $actualcaps);
    }

    public function test_other_editing_caps() {
        // Exercise SUT
        $this->testpage->set_other_editing_capability(array('moodle/course:manageactivities', 'moodle/site:other'));
        // Validate
        $actualcaps = $this->testpage->all_editing_caps();
        $expectedcaps = array('moodle/course:manageactivities', 'moodle/site:other', 'moodle/site:manageblocks');
        $this->assert(new ArraysHaveSameValuesExpectation($expectedcaps), $actualcaps);
    }
}


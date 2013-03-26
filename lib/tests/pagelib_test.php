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
 * Tests for the block_manager class in ../blocklib.php.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
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
class moodle_page_test extends advanced_testcase {
    protected $testpage;

    public function setUp() {
        parent::setUp();
        $this->resetAfterTest();
        $this->testpage = new testable_moodle_page();
    }

    public function test_course_returns_site_before_set() {
        global $SITE;
        // Validate
        $this->assertSame($SITE, $this->testpage->course);
    }

    public function test_setting_course_works() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assertEquals($course, $this->testpage->course);
    }

    public function test_global_course_and_page_course_are_same_with_global_page() {
        global $COURSE, $PAGE;
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        $PAGE = $this->testpage;
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assertSame($this->testpage->course, $COURSE);
    }

    public function test_global_course_not_changed_with_non_global_page() {
        global $COURSE;
        $originalcourse = $COURSE;
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        // Exercise SUT
        $this->testpage->set_course($course);
        // Validate
        $this->assertSame($originalcourse, $COURSE);
    }

    public function test_cannot_set_course_once_theme_set() {
        // Setup fixture
        $this->testpage->force_theme(theme_config::DEFAULT_THEME);
        $course = $this->getDataGenerator()->create_course();
        // Set expectation.
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $this->testpage->set_course($course);
    }

    public function test_cannot_set_category_once_theme_set() {
        // Setup fixture
        $this->testpage->force_theme(theme_config::DEFAULT_THEME);
        // Set expectation.
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $this->testpage->set_category_by_id(123);
    }

    public function test_cannot_set_category_once_course_set() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        $this->testpage->set_course($course);
        // Set expectation.
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $this->testpage->set_category_by_id(123);
    }

    public function test_categories_array_empty_for_front_page() {
        global $SITE;
        // Setup fixture
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        $this->testpage->set_course($SITE);
        // Exercise SUT and validate.
        $this->assertEquals(array(), $this->testpage->categories);
    }

    public function test_set_state_normal_path() {
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance());
        $this->testpage->set_course($course);

        $this->assertEquals(moodle_page::STATE_BEFORE_HEADER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->assertEquals(moodle_page::STATE_PRINTING_HEADER, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        $this->assertEquals(moodle_page::STATE_IN_BODY, $this->testpage->state);

        $this->testpage->set_state(moodle_page::STATE_DONE);
        $this->assertEquals(moodle_page::STATE_DONE, $this->testpage->state);
    }

    public function test_set_state_cannot_skip_one() {
        // Set expectation.
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
    }

    public function test_header_printed_false_initially() {
        // Validate
        $this->assertFalse($this->testpage->headerprinted);
    }

    public function test_header_printed_becomes_true() {
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance());
        $this->testpage->set_course($course);

        // Exercise SUT
        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        // Validate
        $this->assertTrue($this->testpage->headerprinted);
    }

    public function test_set_context() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        // Exercise SUT
        $this->testpage->set_context($context);
        // Validate
        $this->assertSame($context, $this->testpage->context);
    }

    public function test_pagetype_defaults_to_script() {
        global $SCRIPT;
        // Exercise SUT and validate
        $SCRIPT = '/index.php';
        $this->testpage->initialise_default_pagetype();
        $this->assertEquals('site-index', $this->testpage->pagetype);
    }

    public function test_set_pagetype() {
        // Exercise SUT
        $this->testpage->set_pagetype('a-page-type');
        // Validate
        $this->assertEquals('a-page-type', $this->testpage->pagetype);
    }

    public function test_initialise_default_pagetype() {
        // Exercise SUT
        $this->testpage->initialise_default_pagetype('admin/tool/unittest/index.php');
        // Validate
        $this->assertEquals('admin-tool-unittest-index', $this->testpage->pagetype);
    }

    public function test_initialise_default_pagetype_fp() {
        // Exercise SUT
        $this->testpage->initialise_default_pagetype('index.php');
        // Validate
        $this->assertEquals('site-index', $this->testpage->pagetype);
    }

    public function test_get_body_classes_empty() {
        // Validate
        $this->assertEquals('', $this->testpage->bodyclasses);
    }

    public function test_get_body_classes_single() {
        // Exercise SUT
        $this->testpage->add_body_class('aclassname');
        // Validate
        $this->assertEquals('aclassname', $this->testpage->bodyclasses);
    }

    public function test_get_body_classes() {
        // Exercise SUT
        $this->testpage->add_body_classes(array('aclassname', 'anotherclassname'));
        // Validate
        $this->assertEquals('aclassname anotherclassname', $this->testpage->bodyclasses);
    }

    public function test_url_to_class_name() {
        $this->assertEquals('example-com', $this->testpage->url_to_class_name('http://example.com'));
        $this->assertEquals('example-com--80', $this->testpage->url_to_class_name('http://example.com:80'));
        $this->assertEquals('example-com--moodle', $this->testpage->url_to_class_name('https://example.com/moodle'));
        $this->assertEquals('example-com--8080--nested-moodle', $this->testpage->url_to_class_name('https://example.com:8080/nested/moodle'));
    }

    public function test_set_docs_path() {
        // Exercise SUT
        $this->testpage->set_docs_path('a/file/path');
        // Validate
        $this->assertEquals('a/file/path', $this->testpage->docspath);
    }

    public function test_docs_path_defaults_from_pagetype() {
        // Exercise SUT
        $this->testpage->set_pagetype('a-page-type');
        // Validate
        $this->assertEquals('a/page/type', $this->testpage->docspath);
    }

    public function test_set_url_root() {
        global $CFG;
        // Exercise SUT
        $this->testpage->set_url('/');
        // Validate
        $this->assertEquals($CFG->wwwroot . '/', $this->testpage->url->out());
    }

    public function test_set_url_one_param() {
        global $CFG;
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123));
        // Validate
        $this->assertEquals($CFG->wwwroot . '/mod/quiz/attempt.php?attempt=123', $this->testpage->url->out());
    }

    public function test_set_url_two_params() {
        global $CFG;
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validate
        $this->assertEquals($CFG->wwwroot . '/mod/quiz/attempt.php?attempt=123&amp;page=7', $this->testpage->url->out());
    }

    public function test_set_url_using_moodle_url() {
        global $CFG;
        // Fixture setup
        $url = new moodle_url('/mod/workshop/allocation.php', array('cmid' => 29, 'method' => 'manual'));
        // Exercise SUT
        $this->testpage->set_url($url);
        // Validate
        $this->assertEquals($CFG->wwwroot . '/mod/workshop/allocation.php?cmid=29&amp;method=manual', $this->testpage->url->out());
    }

    public function test_set_url_sets_page_type() {
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validate
        $this->assertEquals('mod-quiz-attempt', $this->testpage->pagetype);
    }

    public function test_set_url_does_not_change_explicit_page_type() {
        // Setup fixture
        $this->testpage->set_pagetype('a-page-type');
        // Exercise SUT
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validate
        $this->assertEquals('a-page-type', $this->testpage->pagetype);
    }

    public function test_set_subpage() {
        // Exercise SUT
        $this->testpage->set_subpage('somestring');
        // Validate
        $this->assertEquals('somestring', $this->testpage->subpage);
    }

    public function test_set_heading() {
        // Exercise SUT
        $this->testpage->set_heading('a heading');
        // Validate
        $this->assertEquals('a heading', $this->testpage->heading);
    }

    public function test_set_title() {
        // Exercise SUT
        $this->testpage->set_title('a title');
        // Validate
        $this->assertEquals('a title', $this->testpage->title);
    }

    public function test_default_pagelayout() {
        // Exercise SUT and Validate
        $this->assertEquals('base', $this->testpage->pagelayout);
    }

    public function test_set_pagelayout() {
        // Exercise SUT
        $this->testpage->set_pagelayout('type');
        // Validate
        $this->assertEquals('type', $this->testpage->pagelayout);
    }
}


/**
 * Test functions that rely on the context table.
 */
class moodle_page_with_context_table_test extends advanced_testcase {
    protected $testpage;

    protected function setUp() {
        parent::setUp();
        $this->testpage = new moodle_page();
        $this->resetAfterTest();
    }

    public function test_setting_course_sets_context() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // Exercise SUT
        $this->testpage->set_course($course);

        // Validate
        $this->assertSame($context, $this->testpage->context);
    }
}


/**
 * Test functions that rely on the context table.
 */
class moodle_page_categories_test extends advanced_testcase {
    protected $testpage;

    protected function setUp() {
        parent::setUp();
        $this->testpage = new moodle_page();

        $this->resetAfterTest();
    }

    public function test_set_category_top_level() {
        global $DB;
        // Setup fixture
        $cat = $this->getDataGenerator()->create_category();
        $catdbrecord = $DB->get_record('course_categories', array('id' => $cat->id));
        // Exercise SUT
        $this->testpage->set_category_by_id($cat->id);
        // Validate
        $this->assertEquals($catdbrecord, $this->testpage->category);
        $this->assertSame(context_coursecat::instance($cat->id), $this->testpage->context);
    }

    public function test_set_nested_categories() {
        global $DB;
        // Setup fixture
        $topcat = $this->getDataGenerator()->create_category();
        $topcatdbrecord = $DB->get_record('course_categories', array('id' => $topcat->id));
        $subcat = $this->getDataGenerator()->create_category(array('parent'=>$topcat->id));
        $subcatdbrecord = $DB->get_record('course_categories', array('id' => $subcat->id));
        // Exercise SUT
        $this->testpage->set_category_by_id($subcat->id);
        // Validate
        $categories = $this->testpage->categories;
        $this->assertEquals(2, count($categories));
        $this->assertEquals($topcatdbrecord, array_pop($categories));
        $this->assertEquals($subcatdbrecord, array_pop($categories));
    }
}


/**
 * Test functions that rely on the context table.
 */
class moodle_page_cm_test extends advanced_testcase {
    protected $testpage;

    protected function setUp() {
        parent::setUp();
        $this->testpage = new moodle_page();
        $this->resetAfterTest();
    }

    public function test_cm_null_initially() {
        // Validate
        $this->assertNull($this->testpage->cm);
    }

    public function test_set_cm() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        $this->assertEquals($cm->id, $this->testpage->cm->id);
    }

    public function test_cannot_set_activity_record_before_cm() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Set expectation
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $this->testpage->set_activity_record($forum);
    }

    public function test_setting_cm_sets_context() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        $this->assertSame(context_module::instance($cm->id), $this->testpage->context);
    }

    public function test_activity_record_loaded_if_not_set() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        unset($forum->cmid);
        $this->assertEquals($forum, $this->testpage->activityrecord);
    }

    public function test_set_activity_record() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        $this->testpage->set_cm($cm);
        // Exercise SUT
        $this->testpage->set_activity_record($forum);
        // Validate
        unset($forum->cmid);
        $this->assertEquals($forum, $this->testpage->activityrecord);
    }

    public function test_cannot_set_inconsistent_activity_record_course() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        $this->testpage->set_cm($cm);
        // Set expectation
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $forum->course = 13;
        $this->testpage->set_activity_record($forum);
    }

    public function test_cannot_set_inconsistent_activity_record_instance() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        $this->testpage->set_cm($cm);
        // Set expectation
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $forum->id = 13;
        $this->testpage->set_activity_record($forum);
    }

    public function test_setting_cm_sets_course() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT
        $this->testpage->set_cm($cm);
        // Validate
        $this->assertEquals($course->id, $this->testpage->course->id);
    }

    public function test_set_cm_with_course_and_activity_no_db() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // This only works without db if we already have modinfo cache
        // Exercise SUT
        $this->testpage->set_cm($cm, $course, $forum);
        // Validate
        $this->assertEquals($cm->id, $this->testpage->cm->id);
        $this->assertEquals($course->id, $this->testpage->course->id);
        unset($forum->cmid);
        $this->assertEquals($forum, $this->testpage->activityrecord);
    }

    public function test_cannot_set_cm_with_inconsistent_course() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Set expectation
        $this->setExpectedException('coding_exception');
        // Exercise SUT
        $cm->course = 13;
        $this->testpage->set_cm($cm, $course);
    }

    public function test_get_activity_name() {
        // Setup fixture
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT
        $this->testpage->set_cm($cm, $course, $forum);
        // Validate
        $this->assertEquals('forum', $this->testpage->activityname);
    }
}


/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class moodle_page_editing_test extends advanced_testcase {
    protected $testpage;
    protected $originaluserediting;

    protected function setUp() {
        parent::setUp();
        $this->setAdminUser();
        $this->testpage = new testable_moodle_page();
        $this->testpage->set_context(context_system::instance());
        $this->resetAfterTest();
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
        $this->assertEquals(array('moodle/site:manageblocks'), $this->testpage->all_editing_caps());
    }

    public function test_other_block_editing_cap() {
        // Exercise SUT
        $this->testpage->set_blocks_editing_capability('moodle/my:manageblocks');
        // Validate
        $this->assertEquals(array('moodle/my:manageblocks'), $this->testpage->all_editing_caps());
    }

    public function test_other_editing_cap() {
        // Exercise SUT
        $this->testpage->set_other_editing_capability('moodle/course:manageactivities');
        // Validate
        $actualcaps = $this->testpage->all_editing_caps();
        $expectedcaps = array('moodle/course:manageactivities', 'moodle/site:manageblocks');
        $this->assertEquals(array_values($expectedcaps), array_values($actualcaps));
    }

    public function test_other_editing_caps() {
        // Exercise SUT
        $this->testpage->set_other_editing_capability(array('moodle/course:manageactivities', 'moodle/site:other'));
        // Validate
        $actualcaps = $this->testpage->all_editing_caps();
        $expectedcaps = array('moodle/course:manageactivities', 'moodle/site:other', 'moodle/site:manageblocks');
        $this->assertEquals(array_values($expectedcaps), array_values($actualcaps));
    }
}


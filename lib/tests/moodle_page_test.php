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
 * Tests for the moodle_page class.
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


class core_moodle_page_testcase extends advanced_testcase {

    /**
     * @var testable_moodle_page
     */
    protected $testpage;

    public function setUp() {
        parent::setUp();
        $this->resetAfterTest();
        $this->testpage = new testable_moodle_page();
    }

    public function test_course_returns_site_before_set() {
        global $SITE;
        // Validated.
        $this->assertSame($SITE, $this->testpage->course);
    }

    public function test_setting_course_works() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        // Exercise SUT.
        $this->testpage->set_course($course);
        // Validated.
        $this->assertEquals($course, $this->testpage->course);
    }

    public function test_global_course_and_page_course_are_same_with_global_page() {
        global $COURSE, $PAGE;
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        $PAGE = $this->testpage;
        // Exercise SUT.
        $this->testpage->set_course($course);
        // Validated.
        $this->assertSame($COURSE, $this->testpage->course);
    }

    public function test_global_course_not_changed_with_non_global_page() {
        global $COURSE;
        $originalcourse = $COURSE;
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        // Exercise SUT.
        $this->testpage->set_course($course);
        // Validated.
        $this->assertSame($originalcourse, $COURSE);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_course_once_theme_set() {
        // Setup fixture.
        $this->testpage->force_theme(theme_config::DEFAULT_THEME);
        $course = $this->getDataGenerator()->create_course();

        // Exercise SUT.
        $this->testpage->set_course($course);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_category_once_theme_set() {
        // Setup fixture.
        $this->testpage->force_theme(theme_config::DEFAULT_THEME);

        // Exercise SUT.
        $this->testpage->set_category_by_id(123);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_category_once_course_set() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance()); // Avoid trying to set the context.
        $this->testpage->set_course($course);

        // Exercise SUT.
        $this->testpage->set_category_by_id(123);
    }

    public function test_categories_array_empty_for_front_page() {
        global $SITE;
        // Setup fixture.
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

    /**
     * @expectedException coding_exception
     */
    public function test_set_state_cannot_skip_one() {
        // Exercise SUT.
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
    }

    public function test_header_printed_false_initially() {
        // Validated.
        $this->assertFalse($this->testpage->headerprinted);
    }

    public function test_header_printed_becomes_true() {
        $course = $this->getDataGenerator()->create_course();
        $this->testpage->set_context(context_system::instance());
        $this->testpage->set_course($course);

        // Exercise SUT.
        $this->testpage->set_state(moodle_page::STATE_PRINTING_HEADER);
        $this->testpage->set_state(moodle_page::STATE_IN_BODY);
        // Validated.
        $this->assertTrue($this->testpage->headerprinted);
    }

    public function test_set_context() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        // Exercise SUT.
        $this->testpage->set_context($context);
        // Validated.
        $this->assertSame($context, $this->testpage->context);
    }

    public function test_pagetype_defaults_to_script() {
        global $SCRIPT;
        // Exercise SUT and validate.
        $SCRIPT = '/index.php';
        $this->testpage->initialise_default_pagetype();
        $this->assertSame('site-index', $this->testpage->pagetype);
    }

    public function test_set_pagetype() {
        // Exercise SUT.
        $this->testpage->set_pagetype('a-page-type');
        // Validated.
        $this->assertSame('a-page-type', $this->testpage->pagetype);
    }

    public function test_initialise_default_pagetype() {
        // Exercise SUT.
        $this->testpage->initialise_default_pagetype('admin/tool/unittest/index.php');
        // Validated.
        $this->assertSame('admin-tool-unittest-index', $this->testpage->pagetype);
    }

    public function test_initialise_default_pagetype_fp() {
        // Exercise SUT.
        $this->testpage->initialise_default_pagetype('index.php');
        // Validated.
        $this->assertSame('site-index', $this->testpage->pagetype);
    }

    public function test_get_body_classes_empty() {
        // Validated.
        $this->assertSame('', $this->testpage->bodyclasses);
    }

    public function test_get_body_classes_single() {
        // Exercise SUT.
        $this->testpage->add_body_class('aclassname');
        // Validated.
        $this->assertSame('aclassname', $this->testpage->bodyclasses);
    }

    public function test_get_body_classes() {
        // Exercise SUT.
        $this->testpage->add_body_classes(array('aclassname', 'anotherclassname'));
        // Validated.
        $this->assertSame('aclassname anotherclassname', $this->testpage->bodyclasses);
    }

    public function test_url_to_class_name() {
        $this->assertSame('example-com', $this->testpage->url_to_class_name('http://example.com'));
        $this->assertSame('example-com--80', $this->testpage->url_to_class_name('http://example.com:80'));
        $this->assertSame('example-com--moodle', $this->testpage->url_to_class_name('https://example.com/moodle'));
        $this->assertSame('example-com--8080--nested-moodle', $this->testpage->url_to_class_name('https://example.com:8080/nested/moodle'));
    }

    public function test_set_docs_path() {
        // Exercise SUT.
        $this->testpage->set_docs_path('a/file/path');
        // Validated.
        $this->assertSame('a/file/path', $this->testpage->docspath);
    }

    public function test_docs_path_defaults_from_pagetype() {
        // Exercise SUT.
        $this->testpage->set_pagetype('a-page-type');
        // Validated.
        $this->assertSame('a/page/type', $this->testpage->docspath);
    }

    public function test_set_url_root() {
        global $CFG;
        // Exercise SUT.
        $this->testpage->set_url('/');
        // Validated.
        $this->assertSame($CFG->wwwroot . '/', $this->testpage->url->out());
    }

    public function test_set_url_one_param() {
        global $CFG;
        // Exercise SUT.
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123));
        // Validated.
        $this->assertSame($CFG->wwwroot . '/mod/quiz/attempt.php?attempt=123', $this->testpage->url->out());
    }

    public function test_set_url_two_params() {
        global $CFG;
        // Exercise SUT.
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validated.
        $this->assertSame($CFG->wwwroot . '/mod/quiz/attempt.php?attempt=123&amp;page=7', $this->testpage->url->out());
    }

    public function test_set_url_using_moodle_url() {
        global $CFG;
        // Fixture setup.
        $url = new moodle_url('/mod/workshop/allocation.php', array('cmid' => 29, 'method' => 'manual'));
        // Exercise SUT.
        $this->testpage->set_url($url);
        // Validated.
        $this->assertSame($CFG->wwwroot . '/mod/workshop/allocation.php?cmid=29&amp;method=manual', $this->testpage->url->out());
    }

    public function test_set_url_sets_page_type() {
        // Exercise SUT.
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validated.
        $this->assertSame('mod-quiz-attempt', $this->testpage->pagetype);
    }

    public function test_set_url_does_not_change_explicit_page_type() {
        // Setup fixture.
        $this->testpage->set_pagetype('a-page-type');
        // Exercise SUT.
        $this->testpage->set_url('/mod/quiz/attempt.php', array('attempt' => 123, 'page' => 7));
        // Validated.
        $this->assertSame('a-page-type', $this->testpage->pagetype);
    }

    public function test_set_subpage() {
        // Exercise SUT.
        $this->testpage->set_subpage('somestring');
        // Validated.
        $this->assertSame('somestring', $this->testpage->subpage);
    }

    public function test_set_heading() {
        // Exercise SUT.
        $this->testpage->set_heading('a heading');
        // Validated.
        $this->assertSame('a heading', $this->testpage->heading);
    }

    public function test_set_title() {
        // Exercise SUT.
        $this->testpage->set_title('a title');
        // Validated.
        $this->assertSame('a title', $this->testpage->title);
    }

    public function test_default_pagelayout() {
        // Exercise SUT and Validate.
        $this->assertSame('base', $this->testpage->pagelayout);
    }

    public function test_set_pagelayout() {
        // Exercise SUT.
        $this->testpage->set_pagelayout('type');
        // Validated.
        $this->assertSame('type', $this->testpage->pagelayout);
    }

    public function test_setting_course_sets_context() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // Exercise SUT.
        $this->testpage->set_course($course);

        // Validated.
        $this->assertSame($context, $this->testpage->context);
    }

    public function test_set_category_top_level() {
        global $DB;
        // Setup fixture.
        $cat = $this->getDataGenerator()->create_category();
        $catdbrecord = $DB->get_record('course_categories', array('id' => $cat->id));
        // Exercise SUT.
        $this->testpage->set_category_by_id($cat->id);
        // Validated.
        $this->assertEquals($catdbrecord, $this->testpage->category);
        $this->assertSame(context_coursecat::instance($cat->id), $this->testpage->context);
    }

    public function test_set_nested_categories() {
        global $DB;
        // Setup fixture.
        $topcat = $this->getDataGenerator()->create_category();
        $topcatdbrecord = $DB->get_record('course_categories', array('id' => $topcat->id));
        $subcat = $this->getDataGenerator()->create_category(array('parent'=>$topcat->id));
        $subcatdbrecord = $DB->get_record('course_categories', array('id' => $subcat->id));
        // Exercise SUT.
        $this->testpage->set_category_by_id($subcat->id);
        // Validated.
        $categories = $this->testpage->categories;
        $this->assertCount(2, $categories);
        $this->assertEquals($topcatdbrecord, array_pop($categories));
        $this->assertEquals($subcatdbrecord, array_pop($categories));
    }

    public function test_cm_null_initially() {
        // Validated.
        $this->assertNull($this->testpage->cm);
    }

    public function test_set_cm() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $this->testpage->set_cm($cm);
        // Validated.
        $this->assertEquals($cm->id, $this->testpage->cm->id);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_activity_record_before_cm() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $this->testpage->set_activity_record($forum);
    }

    public function test_setting_cm_sets_context() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $this->testpage->set_cm($cm);
        // Validated.
        $this->assertSame(context_module::instance($cm->id), $this->testpage->context);
    }

    public function test_activity_record_loaded_if_not_set() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $this->testpage->set_cm($cm);
        // Validated.
        unset($forum->cmid);
        $this->assertEquals($forum, $this->testpage->activityrecord);
    }

    public function test_set_activity_record() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        $this->testpage->set_cm($cm);
        // Exercise SUT.
        $this->testpage->set_activity_record($forum);
        // Validated.
        unset($forum->cmid);
        $this->assertEquals($forum, $this->testpage->activityrecord);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_inconsistent_activity_record_course() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        $this->testpage->set_cm($cm);
        // Exercise SUT.
        $forum->course = 13;
        $this->testpage->set_activity_record($forum);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_inconsistent_activity_record_instance() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        $this->testpage->set_cm($cm);
        // Exercise SUT.
        $forum->id = 13;
        $this->testpage->set_activity_record($forum);
    }

    public function test_setting_cm_sets_course() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $this->testpage->set_cm($cm);
        // Validated.
        $this->assertEquals($course->id, $this->testpage->course->id);
    }

    public function test_set_cm_with_course_and_activity_no_db() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // This only works without db if we already have modinfo cache
        // Exercise SUT.
        $this->testpage->set_cm($cm, $course, $forum);
        // Validated.
        $this->assertEquals($cm->id, $this->testpage->cm->id);
        $this->assertEquals($course->id, $this->testpage->course->id);
        unset($forum->cmid);
        $this->assertEquals($forum, $this->testpage->activityrecord);
    }

    /**
     * @expectedException coding_exception
     */
    public function test_cannot_set_cm_with_inconsistent_course() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $cm->course = 13;
        $this->testpage->set_cm($cm, $course);
    }

    public function test_get_activity_name() {
        // Setup fixture.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));
        $cm = get_coursemodule_from_id('forum', $forum->cmid);
        // Exercise SUT.
        $this->testpage->set_cm($cm, $course, $forum);
        // Validated.
        $this->assertSame('forum', $this->testpage->activityname);
    }

    public function test_user_is_editing_on() {
        // We are relying on the fact that unit tests are always run by admin, to
        // ensure the user_allows_editing call returns true.

        // Setup fixture.
        global $USER;

        $this->testpage->set_context(context_system::instance());
        $this->setAdminUser();

        $USER->editing = true;
        // Validated.
        $this->assertTrue($this->testpage->user_is_editing());
    }

    public function test_user_is_editing_off() {
        // We are relying on the fact that unit tests are always run by admin, to
        // ensure the user_allows_editing call returns true.

        // Setup fixture.
        global $USER;

        $this->testpage->set_context(context_system::instance());
        $this->setAdminUser();

        $USER->editing = false;
        // Validated.
        $this->assertFalse($this->testpage->user_is_editing());
    }

    public function test_default_editing_capabilities() {
        $this->testpage->set_context(context_system::instance());
        $this->setAdminUser();

        // Validated.
        $this->assertEquals(array('moodle/site:manageblocks'), $this->testpage->all_editing_caps());
    }

    public function test_other_block_editing_cap() {
        $this->testpage->set_context(context_system::instance());
        $this->setAdminUser();

        // Exercise SUT.
        $this->testpage->set_blocks_editing_capability('moodle/my:manageblocks');
        // Validated.
        $this->assertEquals(array('moodle/my:manageblocks'), $this->testpage->all_editing_caps());
    }

    public function test_other_editing_cap() {
        $this->testpage->set_context(context_system::instance());
        $this->setAdminUser();

        // Exercise SUT.
        $this->testpage->set_other_editing_capability('moodle/course:manageactivities');
        // Validated.
        $actualcaps = $this->testpage->all_editing_caps();
        $expectedcaps = array('moodle/course:manageactivities', 'moodle/site:manageblocks');
        $this->assertEquals(array_values($expectedcaps), array_values($actualcaps));
    }

    public function test_other_editing_caps() {
        $this->testpage->set_context(context_system::instance());
        $this->setAdminUser();

        // Exercise SUT.
        $this->testpage->set_other_editing_capability(array('moodle/course:manageactivities', 'moodle/site:other'));
        // Validated.
        $actualcaps = $this->testpage->all_editing_caps();
        $expectedcaps = array('moodle/course:manageactivities', 'moodle/site:other', 'moodle/site:manageblocks');
        $this->assertEquals(array_values($expectedcaps), array_values($actualcaps));
    }

    /**
     * Test getting a renderer.
     */
    public function test_get_renderer() {
        global $OUTPUT, $PAGE;
        $oldoutput = $OUTPUT;
        $oldpage = $PAGE;
        $PAGE = $this->testpage;

        $this->testpage->set_pagelayout('standard');
        $this->assertEquals('standard', $this->testpage->pagelayout);
        // Initialise theme and output for the next tests.
        $this->testpage->initialise_theme_and_output();
        // Check the generated $OUTPUT object is a core renderer.
        $this->assertInstanceOf('core_renderer', $OUTPUT);
        // Check we can get a core renderer if we explicitly request one (no component).
        $this->assertInstanceOf('core_renderer', $this->testpage->get_renderer('core'));
        // Check we get a CLI renderer if we request a maintenance renderer. The CLI target should take precedence.
        $this->assertInstanceOf('core_renderer_cli',
            $this->testpage->get_renderer('core', null, RENDERER_TARGET_MAINTENANCE));

        // Check we can get a coures renderer if we explicitly request one (valid component).
        $this->assertInstanceOf('core_course_renderer', $this->testpage->get_renderer('core', 'course'));

        // Check a properly invalid component.
        try {
            $this->testpage->get_renderer('core', 'monkeys');
            $this->fail('Request for renderer with invalid component didn\'t throw expected exception.');
        } catch (coding_exception $exception) {
            $this->assertEquals('monkeys', $exception->debuginfo);
        }

        $PAGE = $oldpage;
        $OUTPUT = $oldoutput;
    }

    /**
     * Tests getting a renderer with a maintenance layout.
     *
     * This layout has special hacks in place in order to deliver a "maintenance" renderer.
     */
    public function test_get_renderer_maintenance() {
        global $OUTPUT, $PAGE;
        $oldoutput = $OUTPUT;
        $oldpage = $PAGE;
        $PAGE = $this->testpage;

        $this->testpage->set_pagelayout('maintenance');
        $this->assertEquals('maintenance', $this->testpage->pagelayout);
        // Initialise theme and output for the next tests.
        $this->testpage->initialise_theme_and_output();
        // Check the generated $OUTPUT object is a core cli renderer.
        // It shouldn't be maintenance because there the cli target should take greater precedence.
        $this->assertInstanceOf('core_renderer_cli', $OUTPUT);
        // Check we can get a core renderer if we explicitly request one (no component).
        $this->assertInstanceOf('core_renderer', $this->testpage->get_renderer('core'));
        // Check we get a CLI renderer if we request a maintenance renderer. The CLI target should take precedence.
        $this->assertInstanceOf('core_renderer_cli',
            $this->testpage->get_renderer('core', null, RENDERER_TARGET_MAINTENANCE));
        // Check we can get a coures renderer if we explicitly request one (valid component).
        $this->assertInstanceOf('core_course_renderer', $this->testpage->get_renderer('core', 'course'));

        try {
            $this->testpage->get_renderer('core', 'monkeys');
            $this->fail('Request for renderer with invalid component didn\'t throw expected exception.');
        } catch (coding_exception $exception) {
            $this->assertEquals('monkeys', $exception->debuginfo);
        }

        $PAGE = $oldpage;
        $OUTPUT = $oldoutput;
    }

    public function test_render_to_cli() {
        global $OUTPUT;

        $footer = $OUTPUT->footer();
        $this->assertEmpty($footer, 'cli output does not have a footer.');
    }

    /**
     * Validate the theme value depending on the user theme and cohorts.
     *
     * @dataProvider get_user_theme_provider
     */
    public function test_cohort_get_user_theme($usertheme, $sitetheme, $cohortthemes, $expected) {
        global $DB, $PAGE, $USER;

        $this->resetAfterTest();

        // Enable cohort themes.
        set_config('allowuserthemes', 1);
        set_config('allowcohortthemes', 1);

        $systemctx = context_system::instance();

        set_config('theme', $sitetheme);
        // Create user.
        $user = $this->getDataGenerator()->create_user(array('theme' => $usertheme));

        // Create cohorts and add user as member.
        $cohorts = array();
        foreach ($cohortthemes as $cohorttheme) {
            $cohort = $this->getDataGenerator()->create_cohort(array('contextid' => $systemctx->id, 'name' => 'Cohort',
                'idnumber' => '', 'description' => '', 'theme' => $cohorttheme));
            $cohorts[] = $cohort;
            cohort_add_member($cohort->id, $user->id);
        }

        // Get the theme and compare to the expected.
        $this->setUser($user);

        // Initialise user theme.
        $USER = get_complete_user_data('id', $user->id);

        // Initialise site theme.
        $PAGE->reset_theme_and_output();
        $PAGE->initialise_theme_and_output();
        $result = $PAGE->theme->name;
        $this->assertEquals($expected, $result);
    }

    /**
     * Some user cases for validating the expected theme depending on the cohorts, site and user values.
     *
     * The result is an array of:
     *     'User case description' => [
     *      'usertheme' => '', // User theme.
     *      'sitetheme' => '', // Site theme.
     *      'cohorts' => [],   // Cohort themes.
     *      'expected' => '',  // Expected value returned by cohort_get_user_cohort_theme.
     *    ]
     *
     * @return array
     */
    public function get_user_theme_provider() {
        return [
            'User not a member of any cohort' => [
                'usertheme' => '',
                'sitetheme' => 'boost',
                'cohorts' => [],
                'expected' => 'boost',
            ],
            'User member of one cohort which has a theme set' => [
                'usertheme' => '',
                'sitetheme' => 'boost',
                'cohorts' => [
                    'clean',
                ],
                'expected' => 'clean',
            ],
            'User member of one cohort which has a theme set, and one without a theme' => [
                'usertheme' => '',
                'sitetheme' => 'boost',
                'cohorts' => [
                    'clean',
                    '',
                ],
                'expected' => 'clean',
            ],
            'User member of one cohort which has a theme set, and one with a different theme' => [
                'usertheme' => '',
                'sitetheme' => 'boost',
                'cohorts' => [
                    'clean',
                    'someother',
                ],
                'expected' => 'boost',
            ],
            'User with a theme but not a member of any cohort' => [
                'usertheme' => 'more',
                'sitetheme' => 'boost',
                'cohorts' => [],
                'expected' => 'more',
            ],
            'User with a theme and member of one cohort which has a theme set' => [
                'usertheme' => 'more',
                'sitetheme' => 'boost',
                'cohorts' => [
                    'clean',
                ],
                'expected' => 'more',
            ],
        ];
    }
}

/**
 * Test-specific subclass to make some protected things public.
 */
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

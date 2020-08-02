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
 * Wiki module external functions tests.
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');

/**
 * Wiki module external functions tests
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_wiki_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $this->course->id));
        $this->context = context_module::instance($this->wiki->cmid);
        $this->cm = get_coursemodule_from_instance('wiki', $this->wiki->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->student2 = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->student2->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');

        // Create first pages.
        $this->firstpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($this->wiki,
            array('tags' => array('Cats', 'Dogs')));
    }

    /**
     * Create two collaborative wikis (separate/visible groups), 2 groups and a first page for each wiki and group.
     */
    private function create_collaborative_wikis_with_groups() {
        // Create groups and add student to one of them.
        if (!isset($this->group1)) {
            $this->group1 = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
            $this->getDataGenerator()->create_group_member(array('userid' => $this->student->id, 'groupid' => $this->group1->id));
            $this->getDataGenerator()->create_group_member(array('userid' => $this->student2->id, 'groupid' => $this->group1->id));
        }
        if (!isset($this->group2)) {
            $this->group2 = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
        }

        // Create two collaborative wikis.
        $this->wikisep = $this->getDataGenerator()->create_module('wiki',
                                                        array('course' => $this->course->id, 'groupmode' => SEPARATEGROUPS));
        $this->wikivis = $this->getDataGenerator()->create_module('wiki',
                                                        array('course' => $this->course->id, 'groupmode' => VISIBLEGROUPS));

        // Create pages.
        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');
        $this->fpsepg1 = $wikigenerator->create_first_page($this->wikisep, array('group' => $this->group1->id));
        $this->fpsepg2 = $wikigenerator->create_first_page($this->wikisep, array('group' => $this->group2->id));
        $this->fpsepall = $wikigenerator->create_first_page($this->wikisep, array('group' => 0)); // All participants.
        $this->fpvisg1 = $wikigenerator->create_first_page($this->wikivis, array('group' => $this->group1->id));
        $this->fpvisg2 = $wikigenerator->create_first_page($this->wikivis, array('group' => $this->group2->id));
        $this->fpvisall = $wikigenerator->create_first_page($this->wikivis, array('group' => 0)); // All participants.
    }

    /**
     * Create two individual wikis (separate/visible groups), 2 groups and a first page for each wiki and group.
     */
    private function create_individual_wikis_with_groups() {
        // Create groups and add student to one of them.
        if (!isset($this->group1)) {
            $this->group1 = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
            $this->getDataGenerator()->create_group_member(array('userid' => $this->student->id, 'groupid' => $this->group1->id));
            $this->getDataGenerator()->create_group_member(array('userid' => $this->student2->id, 'groupid' => $this->group1->id));
        }
        if (!isset($this->group2)) {
            $this->group2 = $this->getDataGenerator()->create_group(array('courseid' => $this->course->id));
        }

        // Create two individual wikis.
        $this->wikisepind = $this->getDataGenerator()->create_module('wiki', array('course' => $this->course->id,
                                                        'groupmode' => SEPARATEGROUPS, 'wikimode' => 'individual'));
        $this->wikivisind = $this->getDataGenerator()->create_module('wiki', array('course' => $this->course->id,
                                                        'groupmode' => VISIBLEGROUPS, 'wikimode' => 'individual'));

        // Create pages. Student can only create pages in his groups.
        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');
        $this->setUser($this->teacher);
        $this->fpsepg1indt = $wikigenerator->create_first_page($this->wikisepind, array('group' => $this->group1->id));
        $this->fpsepg2indt = $wikigenerator->create_first_page($this->wikisepind, array('group' => $this->group2->id));
        $this->fpsepallindt = $wikigenerator->create_first_page($this->wikisepind, array('group' => 0)); // All participants.
        $this->fpvisg1indt = $wikigenerator->create_first_page($this->wikivisind, array('group' => $this->group1->id));
        $this->fpvisg2indt = $wikigenerator->create_first_page($this->wikivisind, array('group' => $this->group2->id));
        $this->fpvisallindt = $wikigenerator->create_first_page($this->wikivisind, array('group' => 0)); // All participants.

        $this->setUser($this->student);
        $this->fpsepg1indstu = $wikigenerator->create_first_page($this->wikisepind, array('group' => $this->group1->id));
        $this->fpvisg1indstu = $wikigenerator->create_first_page($this->wikivisind, array('group' => $this->group1->id));

        $this->setUser($this->student2);
        $this->fpsepg1indstu2 = $wikigenerator->create_first_page($this->wikisepind, array('group' => $this->group1->id));
        $this->fpvisg1indstu2 = $wikigenerator->create_first_page($this->wikivisind, array('group' => $this->group1->id));

    }

    /*
     * Test get wikis by courses
     */
    public function test_mod_wiki_get_wikis_by_courses() {

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second wiki.
        $record = new stdClass();
        $record->course = $course2->id;
        $wiki2 = self::getDataGenerator()->create_module('wiki', $record);

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $this->student->id, $this->studentrole->id);

        self::setUser($this->student);

        $returndescription = mod_wiki_external::get_wikis_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'firstpagetitle',
                                'wikimode', 'defaultformat', 'forceformat', 'editbegin', 'editend', 'section', 'visible',
                                'groupmode', 'groupingid');

        // Add expected coursemodule and data.
        $wiki1 = $this->wiki;
        $wiki1->coursemodule = $wiki1->cmid;
        $wiki1->introformat = 1;
        $wiki1->section = 0;
        $wiki1->visible = true;
        $wiki1->groupmode = 0;
        $wiki1->groupingid = 0;
        $wiki1->introfiles = [];

        $wiki2->coursemodule = $wiki2->cmid;
        $wiki2->introformat = 1;
        $wiki2->section = 0;
        $wiki2->visible = true;
        $wiki2->groupmode = 0;
        $wiki2->groupingid = 0;
        $wiki2->introfiles = [];

        foreach ($expectedfields as $field) {
            $expected1[$field] = $wiki1->{$field};
            $expected2[$field] = $wiki2->{$field};
        }
        // Users can create pages by default.
        $expected1['cancreatepages'] = true;
        $expected2['cancreatepages'] = true;

        $expectedwikis = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_wiki_external::get_wikis_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedwikis, $result['wikis']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_wiki_external::get_wikis_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedwikis, $result['wikis']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected wikis.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedwikis);

        // Call the external function without passing course id.
        $result = mod_wiki_external::get_wikis_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedwikis, $result['wikis']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_wiki_external::get_wikis_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('timecreated', 'timemodified');

        foreach ($additionalfields as $field) {
            $expectedwikis[0][$field] = $wiki1->{$field};
        }

        $result = mod_wiki_external::get_wikis_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedwikis, $result['wikis']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_wiki_external::get_wikis_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedwikis, $result['wikis']);

        // Now, prohibit capabilities.
        $this->setUser($this->student);
        $contextcourse1 = context_course::instance($this->course->id);

        // Default student role allows to view wiki and create pages.
        $wikis = mod_wiki_external::get_wikis_by_courses(array($this->course->id));
        $wikis = external_api::clean_returnvalue(mod_wiki_external::get_wikis_by_courses_returns(), $wikis);
        $this->assertEquals('Test wiki 1', $wikis['wikis'][0]['intro']);
        $this->assertEquals(1, $wikis['wikis'][0]['cancreatepages']);

        // Prohibit capability = mod:wiki:viewpage on Course1 for students.
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache(null);

        $wikis = mod_wiki_external::get_wikis_by_courses(array($this->course->id));
        $wikis = external_api::clean_returnvalue(mod_wiki_external::get_wikis_by_courses_returns(), $wikis);
        $this->assertEquals(0, count($wikis['wikis']));

        // Prohibit capability = mod:wiki:createpage on Course1 for students.
        assign_capability('mod/wiki:viewpage', CAP_ALLOW, $this->studentrole->id, $contextcourse1->id, true);
        assign_capability('mod/wiki:createpage', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id);
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache(null);

        $wikis = mod_wiki_external::get_wikis_by_courses(array($this->course->id));
        $wikis = external_api::clean_returnvalue(mod_wiki_external::get_wikis_by_courses_returns(), $wikis);
        $this->assertFalse($wikis['wikis'][0]['cancreatepages']);

    }

    /**
     * Test view_wiki.
     */
    public function test_view_wiki() {

        // Test invalid instance id.
        try {
            mod_wiki_external::view_wiki(0);
            $this->fail('Exception expected due to invalid mod_wiki instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('incorrectwikiid', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_wiki_external::view_wiki($this->wiki->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_wiki_external::view_wiki($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::view_wiki_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodlewiki = new \moodle_url('/mod/wiki/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodlewiki, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is allowed for students by default.
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_wiki_external::view_wiki($this->wiki->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotviewpage', $e->errorcode);
        }

    }

    /**
     * Test view_page.
     */
    public function test_view_page() {

        // Test invalid page id.
        try {
            mod_wiki_external::view_page(0);
            $this->fail('Exception expected due to invalid view_page page id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('incorrectpageid', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_wiki_external::view_page($this->firstpage->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_wiki_external::view_page($this->firstpage->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::view_page_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $pageurl = new \moodle_url('/mod/wiki/view.php', array('pageid' => $this->firstpage->id));
        $this->assertEquals($pageurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is allowed for students by default.
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_wiki_external::view_page($this->firstpage->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannotviewpage', $e->errorcode);
        }

    }

    /**
     * Test get_subwikis.
     */
    public function test_get_subwikis() {

        // Test invalid wiki id.
        try {
            mod_wiki_external::get_subwikis(0);
            $this->fail('Exception expected due to invalid get_subwikis wiki id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('incorrectwikiid', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_wiki_external::get_subwikis($this->wiki->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Create what we expect to be returned. We only test a basic case because deep testing is already done
        // in the tests for wiki_get_visible_subwikis.
        $expectedsubwikis = array();
        $expectedsubwiki = array(
                'id' => $this->firstpage->subwikiid,
                'wikiid' => $this->wiki->id,
                'groupid' => 0,
                'userid' => 0,
                'canedit' => true
            );
        $expectedsubwikis[] = $expectedsubwiki;

        $result = mod_wiki_external::get_subwikis($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwikis_returns(), $result);
        $this->assertEquals($expectedsubwikis, $result['subwikis']);
        $this->assertCount(0, $result['warnings']);

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is allowed for students by default.
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_wiki_external::get_subwikis($this->wiki->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

    }

    /**
     * Test get_subwiki_pages using an invalid wiki instance.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_invalid_instance() {
        mod_wiki_external::get_subwiki_pages(0);
    }

    /**
     * Test get_subwiki_pages using a user not enrolled in the course.
     *
     * @expectedException require_login_exception
     */
    public function test_get_subwiki_pages_unenrolled_user() {
        // Create and use the user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);

        mod_wiki_external::get_subwiki_pages($this->wiki->id);
    }

    /**
     * Test get_subwiki_pages using a hidden wiki as student.
     *
     * @expectedException require_login_exception
     */
    public function test_get_subwiki_pages_hidden_wiki_as_student() {
        // Create a hidden wiki and try to get the list of pages.
        $hiddenwiki = $this->getDataGenerator()->create_module('wiki',
                            array('course' => $this->course->id, 'visible' => false));

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($hiddenwiki->id);
    }

    /**
     * Test get_subwiki_pages without the viewpage capability.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_without_viewpage_capability() {
        // Prohibit capability = mod/wiki:viewpage on the course for students.
        $contextcourse = context_course::instance($this->course->id);
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $contextcourse->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($this->wiki->id);
    }

    /**
     * Test get_subwiki_pages using an invalid userid.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_invalid_userid() {
        // Create an individual wiki.
        $indwiki = $this->getDataGenerator()->create_module('wiki',
                                array('course' => $this->course->id, 'wikimode' => 'individual'));

        mod_wiki_external::get_subwiki_pages($indwiki->id, 0, -10);
    }

    /**
     * Test get_subwiki_pages using an invalid groupid.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_invalid_groupid() {
        // Create testing data.
        $this->create_collaborative_wikis_with_groups();

        mod_wiki_external::get_subwiki_pages($this->wikisep->id, -111);
    }

    /**
     * Test get_subwiki_pages, check that a student can't see another user pages in an individual wiki without groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_individual_student_see_other_user() {
        // Create an individual wiki.
        $indwiki = $this->getDataGenerator()->create_module('wiki',
                                array('course' => $this->course->id, 'wikimode' => 'individual'));

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($indwiki->id, 0, $this->teacher->id);
    }

    /**
     * Test get_subwiki_pages, check that a student can't get the pages from another group in
     * a collaborative wiki using separate groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_collaborative_separate_groups_student_see_other_group() {
        // Create testing data.
        $this->create_collaborative_wikis_with_groups();

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($this->wikisep->id, $this->group2->id);
    }

    /**
     * Test get_subwiki_pages, check that a student can't get the pages from another group in
     * an individual wiki using separate groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_individual_separate_groups_student_see_other_group() {
        // Create testing data.
        $this->create_individual_wikis_with_groups();

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($this->wikisepind->id, $this->group2->id, $this->teacher->id);
    }

    /**
     * Test get_subwiki_pages, check that a student can't get the pages from all participants in
     * a collaborative wiki using separate groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_collaborative_separate_groups_student_see_all_participants() {
        // Create testing data.
        $this->create_collaborative_wikis_with_groups();

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($this->wikisep->id, 0);
    }

    /**
     * Test get_subwiki_pages, check that a student can't get the pages from all participants in
     * an individual wiki using separate groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_pages_individual_separate_groups_student_see_all_participants() {
        // Create testing data.
        $this->create_individual_wikis_with_groups();

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_pages($this->wikisepind->id, 0, $this->teacher->id);
    }

    /**
     * Test get_subwiki_pages without groups and collaborative wiki.
     */
    public function test_get_subwiki_pages_collaborative() {

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Set expected result: first page.
        $expectedpages = array();
        $expectedfirstpage = (array) $this->firstpage;
        $expectedfirstpage['caneditpage'] = true; // No groups and students have 'mod/wiki:editpage' capability.
        $expectedfirstpage['firstpage'] = true;
        $expectedfirstpage['contentformat'] = 1;
        $expectedfirstpage['tags'] = \core_tag\external\util::get_item_tags('mod_wiki', 'wiki_pages', $this->firstpage->id);
        // Cast to expected.
        $expectedfirstpage['tags'][0]['isstandard'] = (bool) $expectedfirstpage['tags'][0]['isstandard'];
        $expectedfirstpage['tags'][1]['isstandard'] = (bool) $expectedfirstpage['tags'][1]['isstandard'];
        $expectedpages[] = $expectedfirstpage;

        $result = mod_wiki_external::get_subwiki_pages($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that groupid param is ignored since the wiki isn't using groups.
        $result = mod_wiki_external::get_subwiki_pages($this->wiki->id, 1234);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that userid param is ignored since the wiki is collaborative.
        $result = mod_wiki_external::get_subwiki_pages($this->wiki->id, 1234, 1234);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Add a new page to the wiki and test again. We'll use a custom title so it's returned first if sorted by title.
        $newpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_page(
                                $this->wiki, array('title' => 'AAA'));

        $expectednewpage = (array) $newpage;
        $expectednewpage['caneditpage'] = true; // No groups and students have 'mod/wiki:editpage' capability.
        $expectednewpage['firstpage'] = false;
        $expectednewpage['contentformat'] = 1;
        $expectednewpage['tags'] = array();
        array_unshift($expectedpages, $expectednewpage); // Add page to the beginning since it orders by title by default.

        $result = mod_wiki_external::get_subwiki_pages($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Now we'll order by ID. Since first page was created first it'll have a lower ID.
        $expectedpages = array($expectedfirstpage, $expectednewpage);
        $result = mod_wiki_external::get_subwiki_pages($this->wiki->id, 0, 0, array('sortby' => 'id'));
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that WS doesn't return page content if includecontent is false, it returns the size instead.
        foreach ($expectedpages as $i => $expectedpage) {
            if (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) {
                $expectedpages[$i]['contentsize'] = mb_strlen($expectedpages[$i]['cachedcontent'], '8bit');
            } else {
                $expectedpages[$i]['contentsize'] = strlen($expectedpages[$i]['cachedcontent']);
            }
            unset($expectedpages[$i]['cachedcontent']);
            unset($expectedpages[$i]['contentformat']);
        }
        $result = mod_wiki_external::get_subwiki_pages($this->wiki->id, 0, 0, array('sortby' => 'id', 'includecontent' => 0));
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);
    }

    /**
     * Test get_subwiki_pages without groups.
     */
    public function test_get_subwiki_pages_individual() {

        // Create an individual wiki to test userid param.
        $indwiki = $this->getDataGenerator()->create_module('wiki',
                                array('course' => $this->course->id, 'wikimode' => 'individual'));

        // Perform a request before creating any page to check that an empty array is returned if subwiki doesn't exist.
        $result = mod_wiki_external::get_subwiki_pages($indwiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals(array(), $result['pages']);

        // Create first pages as student and teacher.
        $this->setUser($this->student);
        $indfirstpagestudent = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($indwiki);
        $this->setUser($this->teacher);
        $indfirstpageteacher = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($indwiki);

        // Check that teacher can get his pages.
        $expectedteacherpage = (array) $indfirstpageteacher;
        $expectedteacherpage['caneditpage'] = true;
        $expectedteacherpage['firstpage'] = true;
        $expectedteacherpage['contentformat'] = 1;
        $expectedteacherpage['tags'] = array();
        $expectedpages = array($expectedteacherpage);

        $result = mod_wiki_external::get_subwiki_pages($indwiki->id, 0, $this->teacher->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that the teacher can see the student's pages.
        $expectedstudentpage = (array) $indfirstpagestudent;
        $expectedstudentpage['caneditpage'] = true;
        $expectedstudentpage['firstpage'] = true;
        $expectedstudentpage['contentformat'] = 1;
        $expectedstudentpage['tags'] = array();
        $expectedpages = array($expectedstudentpage);

        $result = mod_wiki_external::get_subwiki_pages($indwiki->id, 0, $this->student->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Now check that student can get his pages.
        $this->setUser($this->student);

        $result = mod_wiki_external::get_subwiki_pages($indwiki->id, 0, $this->student->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that not using userid uses current user.
        $result = mod_wiki_external::get_subwiki_pages($indwiki->id, 0);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);
    }

    /**
     * Test get_subwiki_pages with groups and collaborative wikis.
     */
    public function test_get_subwiki_pages_separate_groups_collaborative() {

        // Create testing data.
        $this->create_collaborative_wikis_with_groups();

        $this->setUser($this->student);

        // Try to get pages from a valid group in separate groups wiki.

        $expectedpage = (array) $this->fpsepg1;
        $expectedpage['caneditpage'] = true; // User belongs to group and has 'mod/wiki:editpage' capability.
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikisep->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Let's check that not using groupid returns the same result (current group).
        $result = mod_wiki_external::get_subwiki_pages($this->wikisep->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that teacher can view a group pages without belonging to it.
        $this->setUser($this->teacher);
        $result = mod_wiki_external::get_subwiki_pages($this->wikisep->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that teacher can get the pages from all participants.
        $expectedpage = (array) $this->fpsepall;
        $expectedpage['caneditpage'] = true;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikisep->id, 0);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);
    }

    /**
     * Test get_subwiki_pages with groups and collaborative wikis.
     */
    public function test_get_subwiki_pages_visible_groups_collaborative() {

        // Create testing data.
        $this->create_collaborative_wikis_with_groups();

        $this->setUser($this->student);

        // Try to get pages from a valid group in visible groups wiki.

        $expectedpage = (array) $this->fpvisg1;
        $expectedpage['caneditpage'] = true; // User belongs to group and has 'mod/wiki:editpage' capability.
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikivis->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that with visible groups a student can get the pages of groups he doesn't belong to.
        $expectedpage = (array) $this->fpvisg2;
        $expectedpage['caneditpage'] = false; // User doesn't belong to group so he can't edit the page.
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikivis->id, $this->group2->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that with visible groups a student can get the pages of all participants.
        $expectedpage = (array) $this->fpvisall;
        $expectedpage['caneditpage'] = false;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikivis->id, 0);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);
    }

    /**
     * Test get_subwiki_pages with groups and individual wikis.
     */
    public function test_get_subwiki_pages_separate_groups_individual() {

        // Create testing data.
        $this->create_individual_wikis_with_groups();

        $this->setUser($this->student);

        // Check that student can retrieve his pages from separate wiki.
        $expectedpage = (array) $this->fpsepg1indstu;
        $expectedpage['caneditpage'] = true;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikisepind->id, $this->group1->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that not using userid uses current user.
        $result = mod_wiki_external::get_subwiki_pages($this->wikisepind->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that the teacher can see the student pages.
        $this->setUser($this->teacher);
        $result = mod_wiki_external::get_subwiki_pages($this->wikisepind->id, $this->group1->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that a student can see pages from another user that belongs to his groups.
        $this->setUser($this->student);
        $expectedpage = (array) $this->fpsepg1indstu2;
        $expectedpage['caneditpage'] = false;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikisepind->id, $this->group1->id, $this->student2->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);
    }

    /**
     * Test get_subwiki_pages with groups and individual wikis.
     */
    public function test_get_subwiki_pages_visible_groups_individual() {

        // Create testing data.
        $this->create_individual_wikis_with_groups();

        $this->setUser($this->student);

        // Check that student can retrieve his pages from visible wiki.
        $expectedpage = (array) $this->fpvisg1indstu;
        $expectedpage['caneditpage'] = true;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikivisind->id, $this->group1->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that student can see teacher pages in visible groups, even if the user doesn't belong to the group.
        $expectedpage = (array) $this->fpvisg2indt;
        $expectedpage['caneditpage'] = false;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikivisind->id, $this->group2->id, $this->teacher->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);

        // Check that with visible groups a student can get the pages of all participants.
        $expectedpage = (array) $this->fpvisallindt;
        $expectedpage['caneditpage'] = false;
        $expectedpage['firstpage'] = true;
        $expectedpage['contentformat'] = 1;
        $expectedpage['tags'] = array();
        $expectedpages = array($expectedpage);

        $result = mod_wiki_external::get_subwiki_pages($this->wikivisind->id, 0, $this->teacher->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_pages_returns(), $result);
        $this->assertEquals($expectedpages, $result['pages']);
    }

    /**
     * Test get_page_contents using an invalid pageid.
     *
     * @expectedException moodle_exception
     */
    public function test_get_page_contents_invalid_pageid() {
        mod_wiki_external::get_page_contents(0);
    }

    /**
     * Test get_page_contents using a user not enrolled in the course.
     *
     * @expectedException require_login_exception
     */
    public function test_get_page_contents_unenrolled_user() {
        // Create and use the user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);

        mod_wiki_external::get_page_contents($this->firstpage->id);
    }

    /**
     * Test get_page_contents using a hidden wiki as student.
     *
     * @expectedException require_login_exception
     */
    public function test_get_page_contents_hidden_wiki_as_student() {
        // Create a hidden wiki and try to get a page contents.
        $hiddenwiki = $this->getDataGenerator()->create_module('wiki',
                            array('course' => $this->course->id, 'visible' => false));
        $hiddenpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_page($hiddenwiki);

        $this->setUser($this->student);
        mod_wiki_external::get_page_contents($hiddenpage->id);
    }

    /**
     * Test get_page_contents without the viewpage capability.
     *
     * @expectedException moodle_exception
     */
    public function test_get_page_contents_without_viewpage_capability() {
        // Prohibit capability = mod/wiki:viewpage on the course for students.
        $contextcourse = context_course::instance($this->course->id);
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $contextcourse->id);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->student);
        mod_wiki_external::get_page_contents($this->firstpage->id);
    }

    /**
     * Test get_page_contents, check that a student can't get a page from another group when
     * using separate groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_page_contents_separate_groups_student_see_other_group() {
        // Create testing data.
        $this->create_individual_wikis_with_groups();

        $this->setUser($this->student);
        mod_wiki_external::get_page_contents($this->fpsepg2indt->id);
    }

    /**
     * Test get_page_contents without groups. We won't test all the possible cases because that's already
     * done in the tests for get_subwiki_pages.
     */
    public function test_get_page_contents() {

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Set expected result: first page.
        $expectedpage = array(
            'id' => $this->firstpage->id,
            'wikiid' => $this->wiki->id,
            'subwikiid' => $this->firstpage->subwikiid,
            'groupid' => 0, // No groups.
            'userid' => 0, // Collaborative.
            'title' => $this->firstpage->title,
            'cachedcontent' => $this->firstpage->cachedcontent,
            'contentformat' => 1,
            'caneditpage' => true,
            'version' => 1,
            'tags' => \core_tag\external\util::get_item_tags('mod_wiki', 'wiki_pages', $this->firstpage->id),
        );
        // Cast to expected.
        $expectedpage['tags'][0]['isstandard'] = (bool) $expectedpage['tags'][0]['isstandard'];
        $expectedpage['tags'][1]['isstandard'] = (bool) $expectedpage['tags'][1]['isstandard'];

        $result = mod_wiki_external::get_page_contents($this->firstpage->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_contents_returns(), $result);
        $this->assertEquals($expectedpage, $result['page']);

        // Add a new page to the wiki and test with it.
        $newpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_page($this->wiki);

        $expectedpage['id'] = $newpage->id;
        $expectedpage['title'] = $newpage->title;
        $expectedpage['cachedcontent'] = $newpage->cachedcontent;
        $expectedpage['tags'] = array();

        $result = mod_wiki_external::get_page_contents($newpage->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_contents_returns(), $result);
        $this->assertEquals($expectedpage, $result['page']);
    }

    /**
     * Test get_page_contents with groups. We won't test all the possible cases because that's already
     * done in the tests for get_subwiki_pages.
     */
    public function test_get_page_contents_with_groups() {

        // Create testing data.
        $this->create_individual_wikis_with_groups();

        // Try to get page from a valid group in separate groups wiki.
        $this->setUser($this->student);

        $expectedfpsepg1indstu = array(
            'id' => $this->fpsepg1indstu->id,
            'wikiid' => $this->wikisepind->id,
            'subwikiid' => $this->fpsepg1indstu->subwikiid,
            'groupid' => $this->group1->id,
            'userid' => $this->student->id,
            'title' => $this->fpsepg1indstu->title,
            'cachedcontent' => $this->fpsepg1indstu->cachedcontent,
            'contentformat' => 1,
            'caneditpage' => true,
            'version' => 1,
            'tags' => array(),
        );

        $result = mod_wiki_external::get_page_contents($this->fpsepg1indstu->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_contents_returns(), $result);
        $this->assertEquals($expectedfpsepg1indstu, $result['page']);

        // Check that teacher can view a group pages without belonging to it.
        $this->setUser($this->teacher);
        $result = mod_wiki_external::get_page_contents($this->fpsepg1indstu->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_contents_returns(), $result);
        $this->assertEquals($expectedfpsepg1indstu, $result['page']);
    }

    /**
     * Test get_subwiki_files using a wiki without files.
     */
    public function test_get_subwiki_files_no_files() {
        $result = mod_wiki_external::get_subwiki_files($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_files_returns(), $result);
        $this->assertCount(0, $result['files']);
        $this->assertCount(0, $result['warnings']);
    }

    /**
     * Test get_subwiki_files, check that a student can't get files from another group's subwiki when
     * using separate groups.
     *
     * @expectedException moodle_exception
     */
    public function test_get_subwiki_files_separate_groups_student_see_other_group() {
        // Create testing data.
        $this->create_collaborative_wikis_with_groups();

        $this->setUser($this->student);
        mod_wiki_external::get_subwiki_files($this->wikisep->id, $this->group2->id);
    }

    /**
     * Test get_subwiki_files using a collaborative wiki without groups.
     */
    public function test_get_subwiki_files_collaborative_no_groups() {
        $this->setUser($this->student);

        // Add a file as subwiki attachment.
        $fs = get_file_storage();
        $file = array('component' => 'mod_wiki', 'filearea' => 'attachments',
                'contextid' => $this->context->id, 'itemid' => $this->firstpage->subwikiid,
                'filename' => 'image.jpg', 'filepath' => '/', 'timemodified' => time());
        $content = 'IMAGE';
        $fs->create_file_from_string($file, $content);

        $expectedfile = array(
            'filename' => $file['filename'],
            'filepath' => $file['filepath'],
            'mimetype' => 'image/jpeg',
            'isexternalfile' => false,
            'filesize' => strlen($content),
            'timemodified' => $file['timemodified'],
            'fileurl' => moodle_url::make_webservice_pluginfile_url($file['contextid'], $file['component'],
                            $file['filearea'], $file['itemid'], $file['filepath'], $file['filename']),
        );

        // Call the WS and check that it returns this file.
        $result = mod_wiki_external::get_subwiki_files($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_files_returns(), $result);
        $this->assertCount(1, $result['files']);
        $this->assertEquals($expectedfile, $result['files'][0]);

        // Now add another file to the same subwiki.
        $file['filename'] = 'Another image.jpg';
        $file['timemodified'] = time();
        $content = 'ANOTHER IMAGE';
        $fs->create_file_from_string($file, $content);

        $expectedfile['filename'] = $file['filename'];
        $expectedfile['timemodified'] = $file['timemodified'];
        $expectedfile['filesize'] = strlen($content);
        $expectedfile['fileurl'] = moodle_url::make_webservice_pluginfile_url($file['contextid'], $file['component'],
                            $file['filearea'], $file['itemid'], $file['filepath'], $file['filename']);

        // Call the WS and check that it returns both files file.
        $result = mod_wiki_external::get_subwiki_files($this->wiki->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_files_returns(), $result);
        $this->assertCount(2, $result['files']);
        // The new file is returned first because they're returned in alphabetical order.
        $this->assertEquals($expectedfile, $result['files'][0]);
    }

    /**
     * Test get_subwiki_files using an individual wiki with visible groups.
     */
    public function test_get_subwiki_files_visible_groups_individual() {
        // Create testing data.
        $this->create_individual_wikis_with_groups();

        $this->setUser($this->student);

        // Add a file as subwiki attachment in the student group 1 subwiki.
        $fs = get_file_storage();
        $contextwiki = context_module::instance($this->wikivisind->cmid);
        $file = array('component' => 'mod_wiki', 'filearea' => 'attachments',
                'contextid' => $contextwiki->id, 'itemid' => $this->fpvisg1indstu->subwikiid,
                'filename' => 'image.jpg', 'filepath' => '/', 'timemodified' => time());
        $content = 'IMAGE';
        $fs->create_file_from_string($file, $content);

        $expectedfile = array(
            'filename' => $file['filename'],
            'filepath' => $file['filepath'],
            'mimetype' => 'image/jpeg',
            'isexternalfile' => false,
            'filesize' => strlen($content),
            'timemodified' => $file['timemodified'],
            'fileurl' => moodle_url::make_webservice_pluginfile_url($file['contextid'], $file['component'],
                            $file['filearea'], $file['itemid'], $file['filepath'], $file['filename']),
        );

        // Call the WS and check that it returns this file.
        $result = mod_wiki_external::get_subwiki_files($this->wikivisind->id, $this->group1->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_files_returns(), $result);
        $this->assertCount(1, $result['files']);
        $this->assertEquals($expectedfile, $result['files'][0]);

        // Now check that a teacher can see it too.
        $this->setUser($this->teacher);
        $result = mod_wiki_external::get_subwiki_files($this->wikivisind->id, $this->group1->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_subwiki_files_returns(), $result);
        $this->assertCount(1, $result['files']);
        $this->assertEquals($expectedfile, $result['files'][0]);
    }


    /**
     * Test get_page_for_editing. We won't test all the possible cases because that's already
     * done in the tests for wiki_parser_proxy::get_section.
     */
    public function test_get_page_for_editing() {

        $this->create_individual_wikis_with_groups();

        // We add a <span> in the first title to verify the WS works sending HTML in section.
        $sectioncontent = '<h1><span>Title1</span></h1>Text inside section';
        $pagecontent = $sectioncontent.'<h1>Title2</h1>Text inside section';
        $newpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_page(
                                $this->wiki, array('content' => $pagecontent));

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Set expected result: Full Page content.
        $expected = array(
            'content' => $pagecontent,
            'contentformat' => 'html',
            'version' => '1'
        );

        $result = mod_wiki_external::get_page_for_editing($newpage->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_for_editing_returns(), $result);
        $this->assertEquals($expected, $result['pagesection']);

        // Set expected result: Section Page content.
        $expected = array(
            'content' => $sectioncontent,
            'contentformat' => 'html',
            'version' => '1'
        );

        $result = mod_wiki_external::get_page_for_editing($newpage->id, '<span>Title1</span>');
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_for_editing_returns(), $result);
        $this->assertEquals($expected, $result['pagesection']);
    }

    /**
     * Test test_get_page_locking.
     */
    public function test_get_page_locking() {

        $this->create_individual_wikis_with_groups();

        $pagecontent = '<h1>Title1</h1>Text inside section<h1>Title2</h1>Text inside section';
        $newpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_page(
                                $this->wiki, array('content' => $pagecontent));

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Test Section locking.
        $expected = array(
            'version' => '1'
        );

        $result = mod_wiki_external::get_page_for_editing($newpage->id, 'Title1', true);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_for_editing_returns(), $result);
        $this->assertEquals($expected, $result['pagesection']);

        // Test the section is locked.
        $this->setUser($this->student2);
        try {
            mod_wiki_external::get_page_for_editing($newpage->id, 'Title1', true);
            $this->fail('Exception expected due to not page locking.');
        } catch (moodle_exception $e) {
            $this->assertEquals('pageislocked', $e->errorcode);
        }

        // Test the page is locked.
        try {
            mod_wiki_external::get_page_for_editing($newpage->id, null, true);
            $this->fail('Exception expected due to not page locking.');
        } catch (moodle_exception $e) {
            $this->assertEquals('pageislocked', $e->errorcode);
        }

        // Test the other section is not locked.
        $result = mod_wiki_external::get_page_for_editing($newpage->id, 'Title2', true);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_for_editing_returns(), $result);
        $this->assertEquals($expected, $result['pagesection']);

        // Back to the original user to test version change when editing.
        $this->setUser($this->student);
        $newsectioncontent = '<h1>Title2</h1>New test2';
        $result = mod_wiki_external::edit_page($newpage->id, $newsectioncontent, 'Title1');

        $expected = array(
            'version' => '2'
        );
        $result = mod_wiki_external::get_page_for_editing($newpage->id, 'Title1', true);
        $result = external_api::clean_returnvalue(mod_wiki_external::get_page_for_editing_returns(), $result);
        $this->assertEquals($expected, $result['pagesection']);
    }

    /**
     * Test new_page. We won't test all the possible cases because that's already
     * done in the tests for wiki_create_page.
     */
    public function test_new_page() {

        $this->create_individual_wikis_with_groups();

        $sectioncontent = '<h1>Title1</h1>Text inside section';
        $pagecontent = $sectioncontent.'<h1>Title2</h1>Text inside section';
        $pagetitle = 'Page Title';

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Test on existing subwiki.
        $result = mod_wiki_external::new_page($pagetitle, $pagecontent, 'html', $this->fpsepg1indstu->subwikiid);
        $result = external_api::clean_returnvalue(mod_wiki_external::new_page_returns(), $result);
        $this->assertInternalType('int', $result['pageid']);

        $version = wiki_get_current_version($result['pageid']);
        $this->assertEquals($pagecontent, $version->content);
        $this->assertEquals('html', $version->contentformat);

        $page = wiki_get_page($result['pageid']);
        $this->assertEquals($pagetitle, $page->title);

        // Test existing page creation.
        try {
            mod_wiki_external::new_page($pagetitle, $pagecontent, 'html', $this->fpsepg1indstu->subwikiid);
            $this->fail('Exception expected due to creation of an existing page.');
        } catch (moodle_exception $e) {
            $this->assertEquals('pageexists', $e->errorcode);
        }

        // Test on non existing subwiki. Add student to group2 to have a new subwiki to be created.
        $this->getDataGenerator()->create_group_member(array('userid' => $this->student->id, 'groupid' => $this->group2->id));
        $result = mod_wiki_external::new_page($pagetitle, $pagecontent, 'html', null, $this->wikisepind->id, $this->student->id,
            $this->group2->id);
        $result = external_api::clean_returnvalue(mod_wiki_external::new_page_returns(), $result);
        $this->assertInternalType('int', $result['pageid']);

        $version = wiki_get_current_version($result['pageid']);
        $this->assertEquals($pagecontent, $version->content);
        $this->assertEquals('html', $version->contentformat);

        $page = wiki_get_page($result['pageid']);
        $this->assertEquals($pagetitle, $page->title);

        $subwiki = wiki_get_subwiki($page->subwikiid);
        $expected = new StdClass();
        $expected->id = $subwiki->id;
        $expected->wikiid = $this->wikisepind->id;
        $expected->groupid = $this->group2->id;
        $expected->userid = $this->student->id;
        $this->assertEquals($expected, $subwiki);

        // Check page creation for a user not in course.
        $this->studentnotincourse = self::getDataGenerator()->create_user();
        $this->anothercourse = $this->getDataGenerator()->create_course();
        $this->groupnotincourse = $this->getDataGenerator()->create_group(array('courseid' => $this->anothercourse->id));

        try {
            mod_wiki_external::new_page($pagetitle, $pagecontent, 'html', null, $this->wikisepind->id,
                $this->studentnotincourse->id, $this->groupnotincourse->id);
            $this->fail('Exception expected due to creation of an invalid subwiki creation.');
        } catch (moodle_exception $e) {
            $this->assertEquals('cannoteditpage', $e->errorcode);
        }

    }

    /**
     * Test edit_page. We won't test all the possible cases because that's already
     * done in the tests for wiki_save_section / wiki_save_page.
     */
    public function test_edit_page() {

        $this->create_individual_wikis_with_groups();

        // Test user with full capabilities.
        $this->setUser($this->student);

        $newpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_page($this->wikisepind,
            array('group' => $this->group1->id, 'content' => 'Test'));

        // Test edit whole page.
        // We add <span> in the titles to verify the WS works sending HTML in section.
        $sectioncontent = '<h1><span>Title1</span></h1>Text inside section';
        $newpagecontent = $sectioncontent.'<h1><span>Title2</span></h1>Text inside section';

        $result = mod_wiki_external::edit_page($newpage->id, $newpagecontent);
        $result = external_api::clean_returnvalue(mod_wiki_external::edit_page_returns(), $result);
        $this->assertInternalType('int', $result['pageid']);

        $version = wiki_get_current_version($result['pageid']);
        $this->assertEquals($newpagecontent, $version->content);

        // Test edit section.
        $newsectioncontent = '<h1><span>Title2</span></h1>New test2';
        $section = '<span>Title2</span>';

        $result = mod_wiki_external::edit_page($newpage->id, $newsectioncontent, $section);
        $result = external_api::clean_returnvalue(mod_wiki_external::edit_page_returns(), $result);
        $this->assertInternalType('int', $result['pageid']);

        $expected = $sectioncontent . $newsectioncontent;

        $version = wiki_get_current_version($result['pageid']);
        $this->assertEquals($expected, $version->content);

        // Test locked section.
        $newsectioncontent = '<h1><span>Title2</span></h1>New test2';
        $section = '<span>Title2</span>';

        try {
            // Using user 1 to avoid other users to edit.
            wiki_set_lock($newpage->id, 1, $section, true);
            mod_wiki_external::edit_page($newpage->id, $newsectioncontent, $section);
            $this->fail('Exception expected due to locked section');
        } catch (moodle_exception $e) {
            $this->assertEquals('pageislocked', $e->errorcode);
        }

        // Test edit non existing section.
        $newsectioncontent = '<h1>Title3</h1>New test3';
        $section = 'Title3';

        try {
            mod_wiki_external::edit_page($newpage->id, $newsectioncontent, $section);
            $this->fail('Exception expected due to non existing section in the page.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidsection', $e->errorcode);
        }

    }

}

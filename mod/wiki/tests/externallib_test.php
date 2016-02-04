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
    public function setUp() {
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
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');

        // Create first page.
        $this->firstpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($this->wiki);
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
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'firstpagetitle', 'wikimode',
                                'defaultformat', 'forceformat', 'editbegin', 'editend', 'section', 'visible', 'groupmode',
                                'groupingid');

        // Add expected coursemodule and data.
        $wiki1 = $this->wiki;
        $wiki1->coursemodule = $wiki1->cmid;
        $wiki1->introformat = 1;
        $wiki1->section = 0;
        $wiki1->visible = true;
        $wiki1->groupmode = 0;
        $wiki1->groupingid = 0;

        $wiki2->coursemodule = $wiki2->cmid;
        $wiki2->introformat = 1;
        $wiki2->section = 0;
        $wiki2->visible = true;
        $wiki2->groupmode = 0;
        $wiki2->groupingid = 0;

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
        // Prohibit capability = mod:wiki:viewpage on Course1 for students.
        assign_capability('mod/wiki:viewpage', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id);
        accesslib_clear_all_caches_for_unit_testing();

        $wikis = mod_wiki_external::get_wikis_by_courses(array($this->course->id));
        $wikis = external_api::clean_returnvalue(mod_wiki_external::get_wikis_by_courses_returns(), $wikis);
        $this->assertFalse(isset($wikis['wikis'][0]['intro']));

        // Prohibit capability = mod:wiki:createpage on Course1 for students.
        assign_capability('mod/wiki:createpage', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id);
        accesslib_clear_all_caches_for_unit_testing();

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

}

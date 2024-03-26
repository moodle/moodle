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

namespace core_cohort;

use core_cohort\customfield\cohort_handler;
use core_customfield\data_controller;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->dirroot/cohort/lib.php");


/**
 * Cohort library tests.
 *
 * @package    core_cohort
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * Create Cohort custom field for testing.
     *
     * @return \core_customfield\field_controller
     */
    protected function create_cohort_custom_field(): \core_customfield\field_controller {
        $fieldcategory = self::getDataGenerator()->create_custom_field_category([
            'component' => 'core_cohort',
            'area' => 'cohort',
            'name' => 'Other fields',
        ]);

        return self::getDataGenerator()->create_custom_field([
            'shortname' => 'testfield1',
            'name' => 'Custom field',
            'type' => 'text',
            'categoryid' => $fieldcategory->get('id'),
        ]);
    }

    public function test_cohort_add_cohort() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $this->create_cohort_custom_field();

        $cohort = new \stdClass();
        $cohort->contextid = \context_system::instance()->id;
        $cohort->name = 'test cohort';
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;
        $cohort->customfield_testfield1 = 'Test value 1';

        $id = cohort_add_cohort($cohort);
        $this->assertNotEmpty($id);

        $newcohort = $DB->get_record('cohort', array('id'=>$id));
        $this->assertEquals($cohort->contextid, $newcohort->contextid);
        $this->assertSame($cohort->name, $newcohort->name);
        $this->assertSame($cohort->description, $newcohort->description);
        $this->assertEquals($cohort->descriptionformat, $newcohort->descriptionformat);
        $this->assertNotEmpty($newcohort->timecreated);
        $this->assertSame($newcohort->component, '');
        $this->assertSame($newcohort->theme, '');
        $this->assertSame($newcohort->timecreated, $newcohort->timemodified);

        $handler = cohort_handler::create();
        $customfieldsdata = $handler->export_instance_data_object($id);
        $this->assertEquals('Test value 1', $customfieldsdata->testfield1);
    }

    public function test_cohort_add_cohort_missing_name() {
        $cohort = new \stdClass();
        $cohort->contextid = \context_system::instance()->id;
        $cohort->name = null;
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Missing cohort name in cohort_add_cohort()');
        cohort_add_cohort($cohort);
    }

    public function test_cohort_add_cohort_event() {
        $this->resetAfterTest();

        // Setup cohort data structure.
        $cohort = new \stdClass();
        $cohort->contextid = \context_system::instance()->id;
        $cohort->name = 'test cohort';
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;

        // Catch Events.
        $sink = $this->redirectEvents();

        // Perform the add operation.
        $id = cohort_add_cohort($cohort);

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\cohort_created', $event);
        $this->assertEquals('cohort', $event->objecttable);
        $this->assertEquals($id, $event->objectid);
        $this->assertEquals($cohort->contextid, $event->contextid);
        $url = new \moodle_url('/cohort/index.php', array('contextid' => $event->contextid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($cohort, $event->get_record_snapshot('cohort', $id));
        $this->assertEventContextNotUsed($event);
    }

    public function test_cohort_update_cohort() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $this->create_cohort_custom_field();

        $cohort = new \stdClass();
        $cohort->contextid = \context_system::instance()->id;
        $cohort->name = 'test cohort';
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;
        $cohort->customfield_testfield1 = 'Test value 1';

        $id = cohort_add_cohort($cohort);
        $this->assertNotEmpty($id);
        $DB->set_field('cohort', 'timecreated', $cohort->timecreated - 10, array('id'=>$id));
        $DB->set_field('cohort', 'timemodified', $cohort->timemodified - 10, array('id'=>$id));
        $cohort = $DB->get_record('cohort', array('id'=>$id));

        $cohort->name = 'test cohort 2';
        $cohort->customfield_testfield1 = 'Test value updated';

        cohort_update_cohort($cohort);

        $newcohort = $DB->get_record('cohort', array('id'=>$id));

        $this->assertSame($cohort->contextid, $newcohort->contextid);
        $this->assertSame($cohort->name, $newcohort->name);
        $this->assertSame($cohort->description, $newcohort->description);
        $this->assertSame($cohort->descriptionformat, $newcohort->descriptionformat);
        $this->assertSame($cohort->timecreated, $newcohort->timecreated);
        $this->assertSame($cohort->component, $newcohort->component);
        $this->assertSame($newcohort->theme, '');
        $this->assertGreaterThan($newcohort->timecreated, $newcohort->timemodified);
        $this->assertLessThanOrEqual(time(), $newcohort->timemodified);

        $handler = cohort_handler::create();
        $customfieldsdata = $handler->export_instance_data_object($id);
        $this->assertEquals('Test value updated', $customfieldsdata->testfield1);
    }

    public function test_cohort_update_cohort_event() {
        global $DB;

        $this->resetAfterTest();

        // Setup the cohort data structure.
        $cohort = new \stdClass();
        $cohort->contextid = \context_system::instance()->id;
        $cohort->name = 'test cohort';
        $cohort->idnumber = 'testid';
        $cohort->description = 'test cohort desc';
        $cohort->descriptionformat = FORMAT_HTML;
        $cohort->theme = '';
        $id = cohort_add_cohort($cohort);
        $this->assertNotEmpty($id);

        $cohort->name = 'test cohort 2';

        // Catch Events.
        $sink = $this->redirectEvents();

        // Peform the update.
        cohort_update_cohort($cohort);
        // Add again theme property to the cohort object for comparing it to the event snapshop.
        $cohort->theme = '';

        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = $events[0];
        $updatedcohort = $DB->get_record('cohort', array('id'=>$id));
        $this->assertInstanceOf('\core\event\cohort_updated', $event);
        $this->assertEquals('cohort', $event->objecttable);
        $this->assertEquals($updatedcohort->id, $event->objectid);
        $this->assertEquals($updatedcohort->contextid, $event->contextid);
        $url = new \moodle_url('/cohort/edit.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($cohort, $event->get_record_snapshot('cohort', $id));
        $this->assertEventContextNotUsed($event);
    }

    public function test_cohort_delete_cohort() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $field = $this->create_cohort_custom_field();

        $cohort = $this->getDataGenerator()->create_cohort(['customfield_testfield1' => 'Test value 1']);
        $this->assertTrue($DB->record_exists('customfield_data', ['instanceid' => $cohort->id, 'fieldid' => $field->get('id')]));

        cohort_delete_cohort($cohort);

        $this->assertFalse($DB->record_exists('cohort', array('id'=>$cohort->id)));
        $this->assertFalse($DB->record_exists('customfield_data', ['instanceid' => $cohort->id, 'fieldid' => $field->get('id')]));
    }

    public function test_cohort_delete_cohort_event() {

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();

        // Capture the events.
        $sink = $this->redirectEvents();

        // Perform the delete.
        cohort_delete_cohort($cohort);

        $events = $sink->get_events();
        $sink->close();

        // Validate the event structure.
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\cohort_deleted', $event);
        $this->assertEquals('cohort', $event->objecttable);
        $this->assertEquals($cohort->id, $event->objectid);
        $url = new \moodle_url('/cohort/index.php', array('contextid' => $event->contextid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($cohort, $event->get_record_snapshot('cohort', $cohort->id));
        $this->assertEventContextNotUsed($event);
    }

    public function test_cohort_delete_category() {
        global $DB;

        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category();

        $cohort = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category->id)->id));

        cohort_delete_category($category);

        $this->assertTrue($DB->record_exists('cohort', array('id'=>$cohort->id)));
        $newcohort = $DB->get_record('cohort', array('id'=>$cohort->id));
        $this->assertEquals(\context_system::instance()->id, $newcohort->contextid);
    }

    public function test_cohort_add_member() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        $this->assertFalse($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));
        cohort_add_member($cohort->id, $user->id);
        $this->assertTrue($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));
    }

    public function test_cohort_add_member_event() {
        global $USER;
        $this->resetAfterTest();

        // Setup the data.
        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        // Capture the events.
        $sink = $this->redirectEvents();

        // Peform the add member operation.
        cohort_add_member($cohort->id, $user->id);

        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\cohort_member_added', $event);
        $this->assertEquals('cohort', $event->objecttable);
        $this->assertEquals($cohort->id, $event->objectid);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertEquals($USER->id, $event->userid);
        $url = new \moodle_url('/cohort/assign.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    public function test_cohort_remove_member() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        cohort_add_member($cohort->id, $user->id);
        $this->assertTrue($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));

        cohort_remove_member($cohort->id, $user->id);
        $this->assertFalse($DB->record_exists('cohort_members', array('cohortid'=>$cohort->id, 'userid'=>$user->id)));
    }

    public function test_cohort_remove_member_event() {
        global $USER;
        $this->resetAfterTest();

        // Setup the data.
        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort->id, $user->id);

        // Capture the events.
        $sink = $this->redirectEvents();

        // Peform the remove operation.
        cohort_remove_member($cohort->id, $user->id);
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $this->assertCount(1, $events);
        $event = $events[0];
        $this->assertInstanceOf('\core\event\cohort_member_removed', $event);
        $this->assertEquals('cohort', $event->objecttable);
        $this->assertEquals($cohort->id, $event->objectid);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertEquals($USER->id, $event->userid);
        $url = new \moodle_url('/cohort/assign.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    public function test_cohort_is_member() {
        global $DB;

        $this->resetAfterTest();

        $cohort = $this->getDataGenerator()->create_cohort();
        $user = $this->getDataGenerator()->create_user();

        $this->assertFalse(cohort_is_member($cohort->id, $user->id));
        cohort_add_member($cohort->id, $user->id);
        $this->assertTrue(cohort_is_member($cohort->id, $user->id));
    }

    public function test_cohort_get_cohorts() {
        global $DB;

        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category1->id)->id, 'name'=>'aaagrrryyy', 'idnumber'=>'','description'=>''));
        $cohort2 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category1->id)->id, 'name'=>'bbb', 'idnumber'=>'', 'description'=>'yyybrrr'));
        $cohort3 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category1->id)->id, 'name'=>'ccc', 'idnumber'=>'xxarrrghyyy', 'description'=>'po_us'));
        $cohort4 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_system::instance()->id));

        $result = cohort_get_cohorts(\context_coursecat::instance($category2->id)->id);
        $this->assertEquals(0, $result['totalcohorts']);
        $this->assertEquals(0, count($result['cohorts']));
        $this->assertEquals(0, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id);
        $this->assertEquals(3, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1, $cohort2->id=>$cohort2, $cohort3->id=>$cohort3), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 0, 100, 'arrrgh');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort3->id=>$cohort3), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 0, 100, 'brrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort2->id=>$cohort2), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 0, 100, 'grrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 1, 1, 'yyy');
        $this->assertEquals(3, $result['totalcohorts']);
        $this->assertEquals(array($cohort2->id=>$cohort2), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 0, 100, 'po_us');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort3->id=>$cohort3), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 0, 100, 'pokus');
        $this->assertEquals(0, $result['totalcohorts']);
        $this->assertEquals(array(), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_system::instance()->id);
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort4->id=>$cohort4), $result['cohorts']);
        $this->assertEquals(1, $result['allcohorts']);
    }

    public function test_cohort_get_all_cohorts() {
        global $DB;

        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category1->id)->id, 'name'=>'aaagrrryyy', 'idnumber'=>'','description'=>''));
        $cohort2 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category1->id)->id, 'name'=>'bbb', 'idnumber'=>'', 'description'=>'yyybrrr'));
        $cohort3 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_coursecat::instance($category2->id)->id, 'name'=>'ccc', 'idnumber'=>'xxarrrghyyy', 'description'=>'po_us'));
        $cohort4 = $this->getDataGenerator()->create_cohort(array('contextid'=>\context_system::instance()->id));

        // Get list of all cohorts as admin.
        $this->setAdminUser();

        $result = cohort_get_all_cohorts(0, 100, '');
        $this->assertEquals(4, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1, $cohort2->id=>$cohort2, $cohort3->id=>$cohort3, $cohort4->id=>$cohort4), $result['cohorts']);
        $this->assertEquals(4, $result['allcohorts']);

        $result = cohort_get_all_cohorts(0, 100, 'grrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1), $result['cohorts']);
        $this->assertEquals(4, $result['allcohorts']);

        // Get list of all cohorts as manager who has capability everywhere.
        $user = $this->getDataGenerator()->create_user();
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        role_assign($managerrole->id, $user->id, \context_system::instance()->id);
        $this->setUser($user);

        $result = cohort_get_all_cohorts(0, 100, '');
        $this->assertEquals(4, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1, $cohort2->id=>$cohort2, $cohort3->id=>$cohort3, $cohort4->id=>$cohort4), $result['cohorts']);
        $this->assertEquals(4, $result['allcohorts']);

        $result = cohort_get_all_cohorts(0, 100, 'grrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1), $result['cohorts']);
        $this->assertEquals(4, $result['allcohorts']);

        // Get list of all cohorts as manager who has capability everywhere except category2.
        $context2 = \context_coursecat::instance($category2->id);
        role_change_permission($managerrole->id, $context2, 'moodle/cohort:view', CAP_PROHIBIT);
        role_change_permission($managerrole->id, $context2, 'moodle/cohort:manage', CAP_PROHIBIT);
        $this->assertFalse(has_any_capability(array('moodle/cohort:view', 'moodle/cohort:manage'), $context2));

        $result = cohort_get_all_cohorts(0, 100, '');
        $this->assertEquals(3, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1, $cohort2->id=>$cohort2, $cohort4->id=>$cohort4), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_all_cohorts(0, 100, 'grrr');
        $this->assertEquals(1, $result['totalcohorts']);
        $this->assertEquals(array($cohort1->id=>$cohort1), $result['cohorts']);
        $this->assertEquals(3, $result['allcohorts']);

        $result = cohort_get_cohorts(\context_coursecat::instance($category1->id)->id, 1, 1, 'yyy');
        $this->assertEquals(2, $result['totalcohorts']);
        $this->assertEquals(array($cohort2->id=>$cohort2), $result['cohorts']);
        $this->assertEquals(2, $result['allcohorts']);
    }

    public function test_cohort_get_available_cohorts() {
        global $DB;

        $this->resetAfterTest();

        $category1 = $this->getDataGenerator()->create_category();
        $category2 = $this->getDataGenerator()->create_category();

        $course1 = $this->getDataGenerator()->create_course(array('category' => $category1->id));
        $course2 = $this->getDataGenerator()->create_course(array('category' => $category2->id));

        $category1ctx = \context_coursecat::instance($category1->id);
        $category2ctx = \context_coursecat::instance($category2->id);
        $course1ctx = \context_course::instance(($course1->id));
        $course2ctx = \context_course::instance(($course2->id));
        $systemctx = \context_system::instance();

        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid'=>$category1ctx->id, 'name'=>'aaagrrryyy', 'idnumber'=>'','description'=>''));
        $cohort2 = $this->getDataGenerator()->create_cohort(array('contextid'=>$category1ctx->id, 'name'=>'bbb', 'idnumber'=>'', 'description'=>'yyybrrr', 'visible'=>0));
        $cohort3 = $this->getDataGenerator()->create_cohort(array('contextid'=>$category2ctx->id, 'name'=>'ccc', 'idnumber'=>'xxarrrghyyy', 'description'=>'po_us'));
        $cohort4 = $this->getDataGenerator()->create_cohort(array('contextid'=>$systemctx->id, 'name' => 'ddd'));
        $cohort5 = $this->getDataGenerator()->create_cohort(array('contextid'=>$systemctx->id, 'visible'=>0, 'name' => 'eee'));

        /*
        Structure of generated course categories, courses and cohort:

        system
          -cohort4 (visible, has 3 members)
          -cohort5 (not visible, no members)
          category1
            -cohort1 (visible, no members)
            -cohort2 (not visible, has 1 member)
            course1
          category2
            -cohort3 (visible, has 2 member)
            course2

        In this test we call cohort_get_available_cohorts() for users with different roles
        and with different paramteres ($withmembers, $search, $offset, $limit) to make sure we go
        through all possible options of SQL query.
        */

        // Admin can see visible and invisible cohorts defined in above contexts.
        $this->setAdminUser();

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 0, '');
        $this->assertEquals(array($cohort1->id, $cohort2->id, $cohort4->id, $cohort5->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 2, '');
        $this->assertEquals(array($cohort1->id, $cohort2->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 1, 2, '');
        $this->assertEquals(array($cohort2->id, $cohort4->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 100, 'yyy');
        $this->assertEquals(array($cohort1->id, $cohort2->id), array_keys($result));

        $result = cohort_get_available_cohorts($course2ctx, COHORT_ALL, 0, 0, '');
        $this->assertEquals(array($cohort3->id, $cohort4->id, $cohort5->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_MEMBERS_ONLY);
        $this->assertEmpty($result);

        $result = cohort_get_available_cohorts($course2ctx, COHORT_WITH_MEMBERS_ONLY);
        $this->assertEmpty($result);

        // Get list of available cohorts as a teacher in the course.
        $user1 = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        role_assign($teacherrole->id, $user1->id, $course1ctx->id);
        role_assign($teacherrole->id, $user1->id, $course2ctx->id);
        $this->setUser($user1);

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 0, '');
        $this->assertEquals(array($cohort1->id, $cohort4->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 1, '');
        $this->assertEquals(array($cohort1->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 1, 1, '');
        $this->assertEquals(array($cohort4->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 100, 'yyy');
        $this->assertEquals(array($cohort1->id), array_keys($result));

        $result = cohort_get_available_cohorts($course2ctx, COHORT_ALL, 0, 0, '');
        $this->assertEquals(array($cohort3->id, $cohort4->id), array_keys($result));

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_MEMBERS_ONLY);
        $this->assertEmpty($result);

        // Now add members to cohorts.
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user6 = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort2->id, $user3->id);
        cohort_add_member($cohort3->id, $user2->id);
        cohort_add_member($cohort3->id, $user3->id);
        cohort_add_member($cohort4->id, $user4->id);
        cohort_add_member($cohort4->id, $user5->id);
        cohort_add_member($cohort4->id, $user6->id);

        // Check filtering non-empty cohorts as admin.
        $this->setAdminUser();

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort2->id, $cohort4->id), array_keys($result));
        $this->assertEquals(1, $result[$cohort2->id]->memberscnt);
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        $result = cohort_get_available_cohorts($course2ctx, COHORT_WITH_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort3->id, $cohort4->id), array_keys($result));
        $this->assertEquals(2, $result[$cohort3->id]->memberscnt);
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_MEMBERS_ONLY, 0, 0, 'yyy');
        $this->assertEquals(array($cohort2->id), array_keys($result));
        $this->assertEquals(1, $result[$cohort2->id]->memberscnt);

        // Check filtering non-empty cohorts as teacher.
        $this->setUser($user1);

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort4->id), array_keys($result));
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        $result = cohort_get_available_cohorts($course2ctx, COHORT_WITH_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort3->id, $cohort4->id), array_keys($result));
        $this->assertEquals(2, $result[$cohort3->id]->memberscnt);
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_MEMBERS_ONLY, 0, 0, 'yyy');
        $this->assertEmpty($result);

        // Enrol users.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user5->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user6->id, $course1->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user5->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user6->id, $course2->id, $studentrole->id);

        // Check cohorts with enrolments as admin.
        $this->setAdminUser();

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_ENROLLED_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort2->id, $cohort4->id), array_keys($result));
        $this->assertEquals(1, $result[$cohort2->id]->enrolledcnt);
        $this->assertEquals(2, $result[$cohort4->id]->enrolledcnt);
        $this->assertEquals(1, $result[$cohort2->id]->memberscnt);
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        $result = cohort_get_available_cohorts($course2ctx, COHORT_WITH_ENROLLED_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort3->id, $cohort4->id), array_keys($result));
        $this->assertEquals(1, $result[$cohort3->id]->enrolledcnt);
        $this->assertEquals(3, $result[$cohort4->id]->enrolledcnt);
        $this->assertEquals(2, $result[$cohort3->id]->memberscnt);
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_ENROLLED_MEMBERS_ONLY, 0, 0, 'yyy');
        $this->assertEquals(array($cohort2->id), array_keys($result));
        $this->assertEquals(1, $result[$cohort2->id]->enrolledcnt);
        $this->assertEquals(1, $result[$cohort2->id]->memberscnt);

        $result = cohort_get_available_cohorts($course1ctx, COHORT_WITH_NOTENROLLED_MEMBERS_ONLY, 0, 0, '');
        $this->assertEquals(array($cohort4->id), array_keys($result));
        $this->assertEquals(2, $result[$cohort4->id]->enrolledcnt);
        $this->assertEquals(3, $result[$cohort4->id]->memberscnt);

        // Assign user1 additional 'manager' role in the category context. He can now see hidden cohort in category1
        // but still can not see hidden category in system.
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        role_assign($managerrole->id, $user1->id, \context_coursecat::instance($category1->id));
        $this->setUser($user1);
        $result = cohort_get_available_cohorts($course1ctx, COHORT_ALL, 0, 0, '');
        $this->assertEquals(array($cohort1->id, $cohort2->id, $cohort4->id), array_keys($result));
    }

    /**
     * Test that all get functions return custom fields data.
     *
     * @covers \cohort_get_cohort, \cohort_get_cohorts, \cohort_get_all_cohorts
     * @covers \cohort_get_available_cohorts, \cohort_get_user_cohorts
     */
    public function test_get_functions_return_custom_fields() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = self::getDataGenerator()->create_user();
        $course = self::getDataGenerator()->create_course();
        $coursectx = \context_course::instance(($course->id));

        $this->create_cohort_custom_field();

        $cohort1 = $this->getDataGenerator()->create_cohort(['customfield_testfield1' => 'Test value 1']);
        $cohort2 = $this->getDataGenerator()->create_cohort();

        // Test cohort_get_cohort.
        $result = cohort_get_cohort($cohort1->id, $coursectx, true);
        $this->assertObjectHasProperty('customfields', $result);
        $this->assertCount(1, $result->customfields);
        $field = reset($result->customfields);
        $this->assertInstanceOf(data_controller::class, $field);
        $this->assertEquals('testfield1', $field->get_field()->get('shortname'));
        $this->assertEquals('Test value 1', $field->get_value());

        // Test custom fields are not returned if not needed.
        $result = cohort_get_cohort($cohort1->id, $coursectx);
        $this->assertObjectNotHasProperty('customfields', $result);

        // Test cohort_get_cohorts.
        $result = cohort_get_cohorts(\context_system::instance()->id, 0, 25, '', true);
        $this->assertEquals(2, $result['totalcohorts']);
        $this->assertEquals(2, $result['allcohorts']);
        foreach ($result['cohorts'] as $cohort) {
            $this->assertObjectHasProperty('customfields', $cohort);
            $this->assertCount(1, $cohort->customfields);
            $field = reset($cohort->customfields);
            $this->assertInstanceOf(data_controller::class, $field);
            $this->assertEquals('testfield1', $field->get_field()->get('shortname'));

            if ($cohort->id == $cohort1->id ) {
                $this->assertEquals('Test value 1', $field->get_value());
            } else {
                $this->assertEquals('', $field->get_value());
            }
        }

        // Test custom fields are not returned if not needed.
        $result = cohort_get_cohorts(\context_system::instance()->id, 0, 25, '');
        $this->assertEquals(2, $result['totalcohorts']);
        $this->assertEquals(2, $result['allcohorts']);
        foreach ($result['cohorts'] as $cohort) {
            $this->assertObjectNotHasProperty('customfields', $cohort);
        }

        // Test test_cohort_get_all_cohorts.
        $result = cohort_get_all_cohorts(0, 100, '', true);
        $this->assertEquals(2, $result['totalcohorts']);
        $this->assertEquals(2, $result['allcohorts']);
        foreach ($result['cohorts'] as $cohort) {
            $this->assertObjectHasProperty('customfields', $cohort);
            $this->assertCount(1, $cohort->customfields);
            $field = reset($cohort->customfields);
            $this->assertInstanceOf(data_controller::class, $field);
            $this->assertEquals('testfield1', $field->get_field()->get('shortname'));

            if ($cohort->id == $cohort1->id ) {
                $this->assertEquals('Test value 1', $field->get_value());
            } else {
                $this->assertEquals('', $field->get_value());
            }
        }

        // Test custom fields are not returned if not needed.
        $result = cohort_get_all_cohorts(0, 100, '');
        $this->assertEquals(2, $result['totalcohorts']);
        $this->assertEquals(2, $result['allcohorts']);
        foreach ($result['cohorts'] as $cohort) {
            $this->assertObjectNotHasProperty('customfields', $cohort);
        }

        // Test cohort_get_available_cohorts.
        $result = cohort_get_available_cohorts($coursectx, COHORT_ALL, 0, 25, '', true);
        $this->assertCount(2, $result);
        foreach ($result as $cohort) {
            $this->assertObjectHasProperty('customfields', $cohort);
            $this->assertCount(1, $cohort->customfields);
            $field = reset($cohort->customfields);
            $this->assertInstanceOf(data_controller::class, $field);
            $this->assertEquals('testfield1', $field->get_field()->get('shortname'));

            if ($cohort->id == $cohort1->id ) {
                $this->assertEquals('Test value 1', $field->get_value());
            } else {
                $this->assertEquals('', $field->get_value());
            }
        }

        // Test custom fields are not returned if not needed.
        $result = cohort_get_available_cohorts($coursectx, COHORT_ALL, 0, 25, '');
        $this->assertCount(2, $result);
        foreach ($result as $cohort) {
            $this->assertObjectNotHasProperty('customfields', $cohort);
        }

        // Test cohort_get_user_cohorts.
        cohort_add_member($cohort1->id, $user->id);
        cohort_add_member($cohort2->id, $user->id);

        $result = cohort_get_user_cohorts($user->id, true);
        $this->assertCount(2, $result);
        foreach ($result as $cohort) {
            $this->assertObjectHasProperty('customfields', $cohort);
            $this->assertCount(1, $cohort->customfields);
            $field = reset($cohort->customfields);
            $this->assertInstanceOf(data_controller::class, $field);
            $this->assertEquals('testfield1', $field->get_field()->get('shortname'));

            if ($cohort->id == $cohort1->id ) {
                $this->assertEquals('Test value 1', $field->get_value());
            } else {
                $this->assertEquals('', $field->get_value());
            }
        }

        // Test that there is no custom fields returned if not required.
        $result = cohort_get_user_cohorts($user->id);
        $this->assertCount(2, $result);
        foreach ($result as $cohort) {
            $this->assertObjectNotHasProperty('customfields', $cohort);
        }
    }

    /**
     * Create a cohort with allowcohortthemes enabled/disabled.
     */
    public function test_cohort_add_theme_cohort() {
        global $DB;

        $this->resetAfterTest();

        // Theme is added when allowcohortthemes is enabled.
        set_config('allowcohortthemes', 1);
        set_config('theme', 'boost');

        $systemctx = \context_system::instance();
        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid' => $systemctx->id, 'name' => 'test cohort 1',
            'idnumber' => 'testid1', 'description' => 'test cohort desc', 'descriptionformat' => FORMAT_HTML, 'theme' => 'classic'));

        $id = cohort_add_cohort($cohort1);
        $this->assertNotEmpty($id);
        $newcohort = $DB->get_record('cohort', array('id' => $id));
        $this->assertEquals($cohort1->contextid, $newcohort->contextid);
        $this->assertSame($cohort1->name, $newcohort->name);
        $this->assertSame($cohort1->description, $newcohort->description);
        $this->assertEquals($cohort1->descriptionformat, $newcohort->descriptionformat);
        $this->assertNotEmpty($newcohort->theme);
        $this->assertSame($cohort1->theme, $newcohort->theme);
        $this->assertNotEmpty($newcohort->timecreated);
        $this->assertSame($newcohort->component, '');
        $this->assertSame($newcohort->timecreated, $newcohort->timemodified);

        // Theme is not added when allowcohortthemes is disabled.
        set_config('allowcohortthemes', 0);

        $cohort2 = $this->getDataGenerator()->create_cohort(array('contextid' => $systemctx->id, 'name' => 'test cohort 2',
            'idnumber' => 'testid2', 'description' => 'test cohort desc', 'descriptionformat' => FORMAT_HTML, 'theme' => 'classic'));

        $id = cohort_add_cohort($cohort2);
        $this->assertNotEmpty($id);
        $newcohort = $DB->get_record('cohort', array('id' => $id));
        $this->assertSame($cohort2->name, $newcohort->name);
        $this->assertEmpty($newcohort->theme);
    }

    /**
     * Update a cohort with allowcohortthemes enabled/disabled.
     */
    public function test_cohort_update_theme_cohort() {
        global $DB;

        $this->resetAfterTest();

        // Enable cohort themes.
        set_config('allowcohortthemes', 1);
        set_config('theme', 'boost');

        $systemctx = \context_system::instance();
        $cohort1 = $this->getDataGenerator()->create_cohort(array('contextid' => $systemctx->id, 'name' => 'test cohort 1',
            'idnumber' => 'testid1', 'description' => 'test cohort desc', 'descriptionformat' => FORMAT_HTML, 'theme' => 'classic'));
        $id = cohort_add_cohort($cohort1);
        $this->assertNotEmpty($id);

        // Theme is updated when allowcohortthemes is enabled.
        $cohort1 = $DB->get_record('cohort', array('id' => $id));
        $cohort1->name = 'test cohort 1 updated';
        $cohort1->theme = 'classic';
        cohort_update_cohort($cohort1);
        $updatedcohort = $DB->get_record('cohort', array('id' => $id));
        $this->assertEquals($cohort1->contextid, $updatedcohort->contextid);
        $this->assertSame($cohort1->name, $updatedcohort->name);
        $this->assertSame($cohort1->description, $updatedcohort->description);
        $this->assertNotEmpty($updatedcohort->theme);
        $this->assertSame($cohort1->theme, $updatedcohort->theme);

        // Theme is not updated neither overwritten when allowcohortthemes is disabled.
        set_config('allowcohortthemes', 0);
        $cohort2 = $DB->get_record('cohort', array('id' => $id));
        $cohort2->theme = 'classic';
        cohort_update_cohort($cohort2);
        $updatedcohort = $DB->get_record('cohort', array('id' => $id));
        $this->assertEquals($cohort2->contextid, $updatedcohort->contextid);
        $this->assertNotEmpty($updatedcohort->theme);
        $this->assertSame($cohort1->theme, $updatedcohort->theme);
    }

    /**
     * Test that lib function returns custom field data for a cohorts.
     *
     * @covers \cohort_get_custom_fields_data
     */
    public function test_cohort_get_custom_fields_data() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->create_cohort_custom_field();

        $cohort1 = $this->getDataGenerator()->create_cohort(['customfield_testfield1' => 'Test value 1']);
        $cohort2 = $this->getDataGenerator()->create_cohort();

        $result = cohort_get_custom_fields_data([$cohort1->id, $cohort2->id, 777]);
        $this->assertArrayHasKey($cohort1->id, $result);
        $this->assertArrayHasKey($cohort2->id, $result);
        $this->assertArrayHasKey(777, $result);

        foreach ($result as $cohortid => $fieldcontrollers) {
            foreach ($fieldcontrollers as $fieldcontroller) {
                $this->assertInstanceOf(data_controller::class, $fieldcontroller);
                if ($cohortid == $cohort1->id) {
                    $this->assertSame('Test value 1', $fieldcontroller->export_value());
                } else {
                    $this->assertNull($fieldcontroller->export_value());
                }
            }
        }
    }

    /**
     * Test the behaviour of cohort_get_cohort().
     *
     * @covers ::cohort_get_cohort
     */
    public function test_cohort_get_cohort() {
        $this->resetAfterTest();

        $cat = $this->getDataGenerator()->create_category();
        $cat1 = $this->getDataGenerator()->create_category(['parent' => $cat->id]);
        $cat2 = $this->getDataGenerator()->create_category(['parent' => $cat->id]);

        $course1 = $this->getDataGenerator()->create_course(['category' => $cat1->id, 'shortname' => 'ANON1']);
        $course2 = $this->getDataGenerator()->create_course(['category' => $cat2->id, 'shortname' => 'ANON2']);

        $cohort1 = $this->getDataGenerator()->create_cohort(['contextid' => \context_coursecat::instance($cat1->id)->id]);

        $result = cohort_get_cohort($cohort1->id, \context_course::instance($course2->id));
        $this->assertFalse($result);

        $result = cohort_get_cohort($cohort1->id, \context_course::instance($course2->id), true);
        $this->assertFalse($result);

        $result = cohort_get_cohort($cohort1->id, \context_course::instance($course1->id));
        $this->assertEquals($cohort1->id, $result->id);

        $result = cohort_get_cohort($cohort1->id, \context_course::instance($course1->id), true);
        $this->assertEquals($cohort1->id, $result->id);
    }

}

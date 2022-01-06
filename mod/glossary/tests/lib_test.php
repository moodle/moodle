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
 * Glossary lib tests.
 *
 * @package    mod_glossary
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/glossary/lib.php');
require_once($CFG->dirroot . '/mod/glossary/locallib.php');

/**
 * Glossary lib testcase.
 *
 * @package    mod_glossary
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_glossary_lib_testcase extends advanced_testcase {

    public function test_glossary_view() {
        global $CFG;
        $origcompletion = $CFG->enablecompletion;
        $CFG->enablecompletion = true;
        $this->resetAfterTest(true);

        // Generate all the things.
        $c1 = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $g1 = $this->getDataGenerator()->create_module('glossary', array(
            'course' => $c1->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ));
        $g2 = $this->getDataGenerator()->create_module('glossary', array(
            'course' => $c1->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1
        ));
        $u1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $modinfo = course_modinfo::instance($c1->id);
        $cm1 = $modinfo->get_cm($g1->cmid);
        $cm2 = $modinfo->get_cm($g2->cmid);
        $ctx1 = $cm1->context;
        $completion = new completion_info($c1);

        $this->setUser($u1);

        // Confirm what we've set up.
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completion->get_data($cm1, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm1, false, $u1->id)->completionstate);
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completion->get_data($cm2, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm2, false, $u1->id)->completionstate);

        // Simulate the view call.
        $sink = $this->redirectEvents();
        glossary_view($g1, $c1, $cm1, $ctx1, 'letter');
        $events = $sink->get_events();

        // Assertions.
        $this->assertCount(3, $events);
        $this->assertEquals('\core\event\course_module_completion_updated', $events[0]->eventname);
        $this->assertEquals('\core\event\course_module_completion_updated', $events[1]->eventname);
        $this->assertEquals('\mod_glossary\event\course_module_viewed', $events[2]->eventname);
        $this->assertEquals($g1->id, $events[2]->objectid);
        $this->assertEquals('letter', $events[2]->other['mode']);
        $this->assertEquals(COMPLETION_VIEWED, $completion->get_data($cm1, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_COMPLETE, $completion->get_data($cm1, false, $u1->id)->completionstate);
        $this->assertEquals(COMPLETION_NOT_VIEWED, $completion->get_data($cm2, false, $u1->id)->viewed);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm2, false, $u1->id)->completionstate);

        // Tear down.
        $sink->close();
        $CFG->enablecompletion = $origcompletion;
    }

    public function test_glossary_entry_view() {
        $this->resetAfterTest(true);

        // Generate all the things.
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $c1 = $this->getDataGenerator()->create_course();
        $g1 = $this->getDataGenerator()->create_module('glossary', array('course' => $c1->id));
        $e1 = $gg->create_content($g1);
        $u1 = $this->getDataGenerator()->create_user();
        $ctx = context_module::instance($g1->cmid);
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);

        // Assertions.
        $sink = $this->redirectEvents();
        glossary_entry_view($e1, $ctx);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $this->assertEquals('\mod_glossary\event\entry_viewed', $events[0]->eventname);
        $this->assertEquals($e1->id, $events[0]->objectid);
        $sink->close();
    }

    public function test_glossary_core_calendar_provide_event_action() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $glossary->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_glossary_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_glossary_core_calendar_provide_event_action_as_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $glossary->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_glossary_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_glossary_core_calendar_provide_event_action_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create the activity.
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $glossary->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_glossary_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_glossary_core_calendar_provide_event_action_in_hidden_section() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create the activity.
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $glossary->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Set sections 0 as hidden.
        set_section_visible($course->id, 0, 0);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_glossary_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_glossary_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('glossary', $glossary->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $glossary->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_glossary_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_glossary_core_calendar_provide_event_action_already_completed_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create a course.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create the activity.
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id),
                array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('glossary', $glossary->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $glossary->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the user.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_glossary_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'glossary';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }

    /**
     * Test the callback responsible for returning the completion rule descriptions.
     * This function should work given either an instance of the module (cm_info), such as when checking the active rules,
     * or if passed a stdClass of similar structure, such as when checking the the default completion settings for a mod type.
     */
    public function test_mod_glossary_completion_get_active_rule_descriptions() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $glossary1 = $this->getDataGenerator()->create_module('glossary', [
            'course' => $course->id,
            'completion' => 2,
            'completionentries' => 3
        ]);
        $glossary2 = $this->getDataGenerator()->create_module('glossary', [
            'course' => $course->id,
            'completion' => 2,
            'completionentries' => 0
        ]);
        $cm1 = cm_info::create(get_coursemodule_from_instance('glossary', $glossary1->id));
        $cm2 = cm_info::create(get_coursemodule_from_instance('glossary', $glossary2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new stdClass();
        $moddefaults->customdata = ['customcompletionrules' => ['completionentries' => 3]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [get_string('completionentriesdesc', 'glossary', $glossary1->completionentries)];
        $this->assertEquals(mod_glossary_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_glossary_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_glossary_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_glossary_get_completion_active_rule_descriptions(new stdClass()), []);
    }

    public function test_mod_glossary_get_tagged_entries() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $course3 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();

        // Create and enrol a student.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');

        // Create glossaries and entries.
        $glossary1 = $this->getDataGenerator()->create_module('glossary', array('course' => $course1->id));
        $glossary2 = $this->getDataGenerator()->create_module('glossary', array('course' => $course2->id));
        $glossary3 = $this->getDataGenerator()->create_module('glossary', array('course' => $course3->id));
        $entry11 = $glossarygenerator->create_content($glossary1, array('tags' => array('Cats', 'Dogs')));
        $entry12 = $glossarygenerator->create_content($glossary1, array('tags' => array('Cats', 'mice')));
        $entry13 = $glossarygenerator->create_content($glossary1, array('tags' => array('Cats')));
        $entry14 = $glossarygenerator->create_content($glossary1);
        $entry15 = $glossarygenerator->create_content($glossary1, array('tags' => array('Cats')));
        $entry16 = $glossarygenerator->create_content($glossary1, array('tags' => array('Cats'), 'approved' => false));
        $entry17 = $glossarygenerator->create_content($glossary1, array('tags' => array('Cats'), 'approved' => false, 'userid' => $student->id));
        $entry21 = $glossarygenerator->create_content($glossary2, array('tags' => array('Cats')));
        $entry22 = $glossarygenerator->create_content($glossary2, array('tags' => array('Cats', 'Dogs')));
        $entry23 = $glossarygenerator->create_content($glossary2, array('tags' => array('mice', 'Cats')));
        $entry31 = $glossarygenerator->create_content($glossary3, array('tags' => array('mice', 'Cats')));

        $tag = core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        // Get first page of tagged entries (first 5 entries).
        $res = mod_glossary_get_tagged_entries($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$entry = */0);
        $this->assertRegExp('/'.$entry11->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry12->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry13->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry14->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry15->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry16->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry17->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry21->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry22->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry23->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry31->concept.'</', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertNotEmpty($res->nextpageurl);
        // Get second page of tagged entries (second 5 entries).
        $res = mod_glossary_get_tagged_entries($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$entry = */1);
        $this->assertNotRegExp('/'.$entry11->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry12->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry13->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry14->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry15->concept.'</', $res->content);
        $this->assertNotRegExp('/'.$entry16->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry17->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry21->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry22->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry23->concept.'</', $res->content);
        $this->assertRegExp('/'.$entry31->concept.'</', $res->content);
        $this->assertNotEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        $this->setUser($student);
        core_tag_index_builder::reset_caches();

        // User can not see entries in course 3 because he is not enrolled.
        $res = mod_glossary_get_tagged_entries($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$entry = */1);
        $this->assertRegExp('/'.$entry22->concept.'/', $res->content);
        $this->assertRegExp('/'.$entry23->concept.'/', $res->content);
        $this->assertNotRegExp('/'.$entry31->concept.'/', $res->content);

        // User can search glossary entries inside a course.
        $coursecontext = context_course::instance($course1->id);
        $res = mod_glossary_get_tagged_entries($tag, /*$exclusivemode = */false,
            /*$fromctx = */0, /*$ctx = */$coursecontext->id, /*$rec = */1, /*$entry = */0);
        $this->assertRegExp('/'.$entry11->concept.'/', $res->content);
        $this->assertRegExp('/'.$entry12->concept.'/', $res->content);
        $this->assertRegExp('/'.$entry13->concept.'/', $res->content);
        $this->assertNotRegExp('/'.$entry14->concept.'/', $res->content);
        $this->assertRegExp('/'.$entry15->concept.'/', $res->content);
        $this->assertNotRegExp('/'.$entry21->concept.'/', $res->content);
        $this->assertNotRegExp('/'.$entry22->concept.'/', $res->content);
        $this->assertNotRegExp('/'.$entry23->concept.'/', $res->content);
        $this->assertEmpty($res->nextpageurl);

        // User cannot see unapproved entries unless he is an author.
        $this->assertNotRegExp('/'.$entry16->concept.'/', $res->content);
        $this->assertRegExp('/'.$entry17->concept.'/', $res->content);
    }

    public function test_glossary_get_entries_search() {
        $this->resetAfterTest();
        $this->setAdminUser();
        // Turn on glossary autolinking (usedynalink).
        set_config('glossary_linkentries', 1);
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));
        // Note this entry is not case sensitive by default (casesensitive = 0).
        $entry = $glossarygenerator->create_content($glossary);
        // Check that a search for the concept return the entry.
        $concept = $entry->concept;
        $search = glossary_get_entries_search($concept, $course->id);
        $this->assertCount(1, $search);
        $foundentry = array_shift($search);
        $this->assertEquals($foundentry->concept, $entry->concept);
        // Now try the same search but with a lowercase term.
        $concept = strtolower($entry->concept);
        $search = glossary_get_entries_search($concept, $course->id);
        $this->assertCount(1, $search);
        $foundentry = array_shift($search);
        $this->assertEquals($foundentry->concept, $entry->concept);

        // Make an entry that is case sensitive (casesensitive = 1).
        set_config('glossary_casesensitive', 1);
        $entry = $glossarygenerator->create_content($glossary);
        $concept = $entry->concept;
        $search = glossary_get_entries_search($concept, $course->id);
        $this->assertCount(1, $search);
        $foundentry = array_shift($search);
        $this->assertEquals($foundentry->concept, $entry->concept);
        // Now try the same search but with a lowercase term.
        $concept = strtolower($entry->concept);
        $search = glossary_get_entries_search($concept, $course->id);
        $this->assertCount(0, $search);
    }

    public function test_mod_glossary_can_delete_entry_users() {
        $this->resetAfterTest();

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $anotherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $this->setUser($student);
        $entry = $gg->create_content($glossary);
        $context = context_module::instance($glossary->cmid);

        // Test student can delete.
        $this->assertTrue(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Test teacher can delete.
        $this->setUser($teacher);
        $this->assertTrue(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Test admin can delete.
        $this->setAdminUser();
        $this->assertTrue(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Test a different student is not able to delete.
        $this->setUser($anotherstudent);
        $this->assertFalse(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Test exception.
        $this->expectExceptionMessage(get_string('nopermissiontodelentry', 'error'));
        mod_glossary_can_delete_entry($entry, $glossary, $context, false);
    }

    public function test_mod_glossary_can_delete_entry_edit_period() {
        global $CFG;
        $this->resetAfterTest();

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id, 'editalways' => 1]);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $this->setUser($student);
        $entry = $gg->create_content($glossary);
        $context = context_module::instance($glossary->cmid);

        // Test student can always delete when edit always is set to 1.
        $entry->timecreated = time() - 2 * $CFG->maxeditingtime;
        $this->assertTrue(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Test student cannot delete old entries when edit always is set to 0.
        $glossary->editalways = 0;
        $this->assertFalse(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Test student can delete recent entries when edit always is set to 0.
        $entry->timecreated = time();
        $this->assertTrue(mod_glossary_can_delete_entry($entry, $glossary, $context));

        // Check exception.
        $entry->timecreated = time() - 2 * $CFG->maxeditingtime;
        $this->expectExceptionMessage(get_string('errdeltimeexpired', 'glossary'));
        mod_glossary_can_delete_entry($entry, $glossary, $context, false);
    }

    public function test_mod_glossary_delete_entry() {
        global $DB, $CFG;
        $this->resetAfterTest();
        require_once($CFG->dirroot . '/rating/lib.php');

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $record = new stdClass();
        $record->course = $course->id;
        $record->assessed = RATING_AGGREGATE_AVERAGE;
        $scale = $this->getDataGenerator()->create_scale(['scale' => 'A,B,C,D']);
        $record->scale = "-$scale->id";
        $glossary = $this->getDataGenerator()->create_module('glossary', $record);
        $context = context_module::instance($glossary->cmid);
        $cm = get_coursemodule_from_instance('glossary', $glossary->id);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $this->setUser($student1);

        // Create entry with tags and rating.
        $entry = $gg->create_content(
            $glossary,
            ['approved' => 1, 'userid' => $student1->id, 'tags' => ['Cats', 'Dogs']],
            ['alias1', 'alias2']
        );

        // Rate the entry as user2.
        $rating1 = new stdClass();
        $rating1->contextid = $context->id;
        $rating1->component = 'mod_glossary';
        $rating1->ratingarea = 'entry';
        $rating1->itemid = $entry->id;
        $rating1->rating = 1; // 1 is A.
        $rating1->scaleid = "-$scale->id";
        $rating1->userid = $student2->id;
        $rating1->timecreated = time();
        $rating1->timemodified = time();
        $rating1->id = $DB->insert_record('rating', $rating1);

        $sink = $this->redirectEvents();
        mod_glossary_delete_entry(fullclone($entry), $glossary, $cm, $context, $course);
        $events = $sink->get_events();
        $event = array_pop($events);

        // Check events.
        $this->assertEquals('\mod_glossary\event\entry_deleted', $event->eventname);
        $this->assertEquals($entry->id, $event->objectid);
        $sink->close();

        // No entry, no alias, no ratings, no tags.
        $this->assertEquals(0, $DB->count_records('glossary_entries', ['id' => $entry->id]));
        $this->assertEquals(0, $DB->count_records('glossary_alias', ['entryid' => $entry->id]));
        $this->assertEquals(0, $DB->count_records('rating', ['component' => 'mod_glossary', 'itemid' => $entry->id]));
        $this->assertEmpty(core_tag_tag::get_by_name(0, 'Cats'));
    }

    public function test_mod_glossary_delete_entry_imported() {
        global $DB;
        $this->resetAfterTest();

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $glossary1 = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);
        $glossary2 = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);

        $context = context_module::instance($glossary2->cmid);
        $cm = get_coursemodule_from_instance('glossary', $glossary2->id);

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $this->setUser($student);

        $entry1 = $gg->create_content($glossary1);
        $entry2 = $gg->create_content(
            $glossary2,
            ['approved' => 1, 'userid' => $student->id, 'sourceglossaryid' => $glossary1->id, 'tags' => ['Cats', 'Dogs']]
        );

        $sink = $this->redirectEvents();
        mod_glossary_delete_entry(fullclone($entry2), $glossary2, $cm, $context, $course);
        $events = $sink->get_events();
        $event = array_pop($events);

        // Check events.
        $this->assertEquals('\mod_glossary\event\entry_deleted', $event->eventname);
        $this->assertEquals($entry2->id, $event->objectid);
        $sink->close();

        // Check source.
        $this->assertEquals(0, $DB->get_field('glossary_entries', 'sourceglossaryid', ['id' => $entry2->id]));
        $this->assertEquals($glossary1->id, $DB->get_field('glossary_entries', 'glossaryid', ['id' => $entry2->id]));

        // Tags.
        $this->assertEmpty(core_tag_tag::get_by_name(0, 'Cats'));
    }

    public function test_mod_glossary_can_update_entry_users() {
        $this->resetAfterTest();

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $anotherstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $this->setUser($student);
        $entry = $gg->create_content($glossary);
        $context = context_module::instance($glossary->cmid);
        $cm = get_coursemodule_from_instance('glossary', $glossary->id);

        // Test student can update.
        $this->assertTrue(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Test teacher can update.
        $this->setUser($teacher);
        $this->assertTrue(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Test admin can update.
        $this->setAdminUser();
        $this->assertTrue(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Test a different student is not able to update.
        $this->setUser($anotherstudent);
        $this->assertFalse(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Test exception.
        $this->expectExceptionMessage(get_string('errcannoteditothers', 'glossary'));
        mod_glossary_can_update_entry($entry, $glossary, $context, $cm, false);
    }

    public function test_mod_glossary_can_update_entry_edit_period() {
        global $CFG;
        $this->resetAfterTest();

        // Create required data.
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id, 'editalways' => 1));

        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $this->setUser($student);
        $entry = $gg->create_content($glossary);
        $context = context_module::instance($glossary->cmid);
        $cm = get_coursemodule_from_instance('glossary', $glossary->id);

        // Test student can always update when edit always is set to 1.
        $entry->timecreated = time() - 2 * $CFG->maxeditingtime;
        $this->assertTrue(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Test student cannot update old entries when edit always is set to 0.
        $glossary->editalways = 0;
        $this->assertFalse(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Test student can update recent entries when edit always is set to 0.
        $entry->timecreated = time();
        $this->assertTrue(mod_glossary_can_update_entry($entry, $glossary, $context, $cm));

        // Check exception.
        $entry->timecreated = time() - 2 * $CFG->maxeditingtime;
        $this->expectExceptionMessage(get_string('erredittimeexpired', 'glossary'));
        mod_glossary_can_update_entry($entry, $glossary, $context, $cm, false);
    }

    public function test_prepare_entry_for_edition() {
        global $USER;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');

        $this->setAdminUser();
        $aliases = ['alias1', 'alias2'];
        $entry = $gg->create_content(
            $glossary,
            ['approved' => 1, 'userid' => $USER->id],
            $aliases
        );

        $cat1 = $gg->create_category($glossary, [], [$entry]);
        $gg->create_category($glossary);

        $entry = mod_glossary_prepare_entry_for_edition($entry);
        $this->assertCount(1, $entry->categories);
        $this->assertEquals($cat1->id, $entry->categories[0]);
        $returnedaliases = array_values(explode("\n", trim($entry->aliases)));
        sort($returnedaliases);
        $this->assertEquals($aliases, $returnedaliases);
    }
}

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
}

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
 * Unit tests for mod/lesson/lib.php.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lesson/lib.php');

/**
 * Unit tests for mod/lesson/lib.php.
 *
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class mod_lesson_lib_testcase extends advanced_testcase {
    /**
     * Test for lesson_get_group_override_priorities().
     */
    public function test_lesson_get_group_override_priorities() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $course = $dg->create_course();
        $lessonmodule = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));

        $this->assertNull(lesson_get_group_override_priorities($lessonmodule->id));

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $now = 100;
        $override1 = (object)[
            'lessonid' => $lessonmodule->id,
            'groupid' => $group1->id,
            'available' => $now,
            'deadline' => $now + 20
        ];
        $DB->insert_record('lesson_overrides', $override1);

        $override2 = (object)[
            'lessonid' => $lessonmodule->id,
            'groupid' => $group2->id,
            'available' => $now - 10,
            'deadline' => $now + 10
        ];
        $DB->insert_record('lesson_overrides', $override2);

        $priorities = lesson_get_group_override_priorities($lessonmodule->id);
        $this->assertNotEmpty($priorities);

        $openpriorities = $priorities['open'];
        // Override 2's time open has higher priority since it is sooner than override 1's.
        $this->assertEquals(2, $openpriorities[$override1->available]);
        $this->assertEquals(1, $openpriorities[$override2->available]);

        $closepriorities = $priorities['close'];
        // Override 1's time close has higher priority since it is later than override 2's.
        $this->assertEquals(1, $closepriorities[$override1->deadline]);
        $this->assertEquals(2, $closepriorities[$override2->deadline]);
    }

    /**
     * Test check_updates_since callback.
     */
    public function test_check_updates_since() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = new stdClass();
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        $course = $this->getDataGenerator()->create_course($course);

        // Create user.
        $studentg1 = self::getDataGenerator()->create_user();
        $teacherg1 = self::getDataGenerator()->create_user();
        $studentg2 = self::getDataGenerator()->create_user();

        // User enrolment.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($studentg1->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacherg1->id, $course->id, $teacherrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($studentg2->id, $course->id, $studentrole->id, 'manual');

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1, $studentg1);
        groups_add_member($group2, $studentg2);

        $this->setCurrentTimeStart();
        $record = array(
            'course' => $course->id,
            'custom' => 0,
            'feedback' => 1,
        );
        $lessonmodule = $this->getDataGenerator()->create_module('lesson', $record);
        // Convert to a lesson object.
        $lesson = new lesson($lessonmodule);
        $cm = $lesson->cm;
        $cm = cm_info::create($cm);

        // Check that upon creation, the updates are only about the new configuration created.
        $onehourago = time() - HOURSECS;
        $updates = lesson_check_updates_since($cm, $onehourago);
        foreach ($updates as $el => $val) {
            if ($el == 'configuration') {
                $this->assertTrue($val->updated);
                $this->assertTimeCurrent($val->timeupdated);
            } else {
                $this->assertFalse($val->updated);
            }
        }

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $tfrecord = $generator->create_question_truefalse($lesson);

        // Check now for pages and answers.
        $updates = lesson_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->pages->updated);
        $this->assertCount(1, $updates->pages->itemids);

        $this->assertTrue($updates->answers->updated);
        $this->assertCount(2, $updates->answers->itemids);

        // Now, do something in the lesson with the two users.
        $this->setUser($studentg1);
        mod_lesson_external::launch_attempt($lesson->id);
        $data = array(
            array(
                'name' => 'answerid',
                'value' => $DB->get_field('lesson_answers', 'id', array('pageid' => $tfrecord->id, 'jumpto' => -1)),
            ),
            array(
                'name' => '_qf__lesson_display_answer_form_truefalse',
                'value' => 1,
            )
        );
        mod_lesson_external::process_page($lesson->id, $tfrecord->id, $data);
        mod_lesson_external::finish_attempt($lesson->id);

        $this->setUser($studentg2);
        mod_lesson_external::launch_attempt($lesson->id);
        $data = array(
            array(
                'name' => 'answerid',
                'value' => $DB->get_field('lesson_answers', 'id', array('pageid' => $tfrecord->id, 'jumpto' => -1)),
            ),
            array(
                'name' => '_qf__lesson_display_answer_form_truefalse',
                'value' => 1,
            )
        );
        mod_lesson_external::process_page($lesson->id, $tfrecord->id, $data);
        mod_lesson_external::finish_attempt($lesson->id);

        $this->setUser($studentg1);
        $updates = lesson_check_updates_since($cm, $onehourago);

        // Check question attempts, timers and new grades.
        $this->assertTrue($updates->questionattempts->updated);
        $this->assertCount(1, $updates->questionattempts->itemids);

        $this->assertTrue($updates->grades->updated);
        $this->assertCount(1, $updates->grades->itemids);

        $this->assertTrue($updates->timers->updated);
        $this->assertCount(1, $updates->timers->itemids);

        // Now, as teacher, check that I can see the two users (even in separate groups).
        $this->setUser($teacherg1);
        $updates = lesson_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->userquestionattempts->updated);
        $this->assertCount(2, $updates->userquestionattempts->itemids);

        $this->assertTrue($updates->usergrades->updated);
        $this->assertCount(2, $updates->usergrades->itemids);

        $this->assertTrue($updates->usertimers->updated);
        $this->assertCount(2, $updates->usertimers->itemids);

        // Now, teacher can't access all groups.
        groups_add_member($group1, $teacherg1);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $teacherrole->id, context_module::instance($cm->id));
        accesslib_clear_all_caches_for_unit_testing();
        $updates = lesson_check_updates_since($cm, $onehourago);
        // I will see only the studentg1 updates.
        $this->assertTrue($updates->userquestionattempts->updated);
        $this->assertCount(1, $updates->userquestionattempts->itemids);

        $this->assertTrue($updates->usergrades->updated);
        $this->assertCount(1, $updates->usergrades->itemids);

        $this->assertTrue($updates->usertimers->updated);
        $this->assertCount(1, $updates->usertimers->itemids);
    }

    public function test_lesson_core_calendar_provide_event_action_open() {
        $this->resetAfterTest();
        $this->setAdminUser();
        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        // Create a lesson activity.
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id,
            'available' => time() - DAYSECS, 'deadline' => time() + DAYSECS));
        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lesson->id, LESSON_EVENT_TYPE_OPEN);
        // Create an action factory.
        $factory = new \core_calendar\action_factory();
        // Decorate action event.
        $actionevent = mod_lesson_core_calendar_provide_event_action($event, $factory);
        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('startlesson', 'lesson'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_lesson_core_calendar_provide_event_action_closed() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a lesson activity.
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id,
            'deadline' => time() - DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lesson->id, LESSON_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lesson_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('startlesson', 'lesson'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_lesson_core_calendar_provide_event_action_open_in_future() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a lesson activity.
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id,
            'available' => time() + DAYSECS));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lesson->id, LESSON_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lesson_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('startlesson', 'lesson'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertFalse($actionevent->is_actionable());
    }

    public function test_lesson_core_calendar_provide_event_action_no_time_specified() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a lesson activity.
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lesson->id, LESSON_EVENT_TYPE_OPEN);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lesson_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('startlesson', 'lesson'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_lesson_core_calendar_provide_event_action_after_attempt() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create user.
        $student = self::getDataGenerator()->create_user();

        // Create a lesson activity.
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lesson->id, LESSON_EVENT_TYPE_OPEN);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $tfrecord = $generator->create_question_truefalse($lesson);

        // Now, do something in the lesson.
        $this->setUser($student);
        mod_lesson_external::launch_attempt($lesson->id);
        $data = array(
            array(
                'name' => 'answerid',
                'value' => $DB->get_field('lesson_answers', 'id', array('pageid' => $tfrecord->id, 'jumpto' => -1)),
            ),
            array(
                'name' => '_qf__lesson_display_answer_form_truefalse',
                'value' => 1,
            )
        );
        mod_lesson_external::process_page($lesson->id, $tfrecord->id, $data);
        mod_lesson_external::finish_attempt($lesson->id);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $action = mod_lesson_core_calendar_provide_event_action($event, $factory);

        // Confirm there was no action for the user.
        $this->assertNull($action);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid
     * @param int $instanceid The lesson id.
     * @param string $eventtype The event type. eg. LESSON_EVENT_TYPE_OPEN.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'lesson';
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
    public function test_mod_lesson_completion_get_active_rule_descriptions() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Two activities, both with automatic completion. One has the 'completionsubmit' rule, one doesn't.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 2]);
        $lesson1 = $this->getDataGenerator()->create_module('lesson', [
            'course' => $course->id,
            'completion' => 2,
            'completionendreached' => 1,
            'completiontimespent' => 3600
        ]);
        $lesson2 = $this->getDataGenerator()->create_module('lesson', [
            'course' => $course->id,
            'completion' => 2,
            'completionendreached' => 0,
            'completiontimespent' => 0
        ]);
        $cm1 = cm_info::create(get_coursemodule_from_instance('lesson', $lesson1->id));
        $cm2 = cm_info::create(get_coursemodule_from_instance('lesson', $lesson2->id));

        // Data for the stdClass input type.
        // This type of input would occur when checking the default completion rules for an activity type, where we don't have
        // any access to cm_info, rather the input is a stdClass containing completion and customdata attributes, just like cm_info.
        $moddefaults = new stdClass();
        $moddefaults->customdata = ['customcompletionrules' => [
            'completionendreached' => 1,
            'completiontimespent' => 3600
        ]];
        $moddefaults->completion = 2;

        $activeruledescriptions = [
            get_string('completionendreached_desc', 'lesson'),
            get_string('completiontimespentdesc', 'lesson', format_time(3600)),
        ];
        $this->assertEquals(mod_lesson_get_completion_active_rule_descriptions($cm1), $activeruledescriptions);
        $this->assertEquals(mod_lesson_get_completion_active_rule_descriptions($cm2), []);
        $this->assertEquals(mod_lesson_get_completion_active_rule_descriptions($moddefaults), $activeruledescriptions);
        $this->assertEquals(mod_lesson_get_completion_active_rule_descriptions(new stdClass()), []);
    }

    /**
     * An unknown event type should not change the lesson instance.
     */
    public function test_mod_lesson_core_calendar_event_timestart_updated_unknown_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $lessongenerator = $generator->get_plugin_generator('mod_lesson');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $lesson = $lessongenerator->create_instance(['course' => $course->id]);
        $lesson->available = $timeopen;
        $lesson->deadline = $timeclose;
        $DB->update_record('lesson', $lesson);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'lesson',
            'instance' => $lesson->id,
            'eventtype' => LESSON_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        mod_lesson_core_calendar_event_timestart_updated($event, $lesson);
        $lesson = $DB->get_record('lesson', ['id' => $lesson->id]);
        $this->assertEquals($timeopen, $lesson->available);
        $this->assertEquals($timeclose, $lesson->deadline);
    }

    /**
     * A LESSON_EVENT_TYPE_OPEN event should update the available property of the lesson activity.
     */
    public function test_mod_lesson_core_calendar_event_timestart_updated_open_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $lessongenerator = $generator->get_plugin_generator('mod_lesson');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeopen = $timeopen - DAYSECS;
        $lesson = $lessongenerator->create_instance(['course' => $course->id]);
        $lesson->available = $timeopen;
        $lesson->deadline = $timeclose;
        $lesson->timemodified = $timemodified;
        $DB->update_record('lesson', $lesson);

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'lesson',
            'instance' => $lesson->id,
            'eventtype' => LESSON_EVENT_TYPE_OPEN,
            'timestart' => $newtimeopen,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();
        mod_lesson_core_calendar_event_timestart_updated($event, $lesson);
        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });
        $lesson = $DB->get_record('lesson', ['id' => $lesson->id]);

        // Ensure the available property matches the event timestart.
        $this->assertEquals($newtimeopen, $lesson->available);

        // Ensure the deadline isn't changed.
        $this->assertEquals($timeclose, $lesson->deadline);

        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $lesson->timemodified);

        // Confirm that a module updated event is fired when the module is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * A LESSON_EVENT_TYPE_CLOSE event should update the deadline property of the lesson activity.
     */
    public function test_mod_lesson_core_calendar_event_timestart_updated_close_event() {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/calendar/lib.php");
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $lessongenerator = $generator->get_plugin_generator('mod_lesson');
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $timemodified = 1;
        $newtimeclose = $timeclose + DAYSECS;
        $lesson = $lessongenerator->create_instance(['course' => $course->id]);
        $lesson->available = $timeopen;
        $lesson->deadline = $timeclose;
        $lesson->timemodified = $timemodified;
        $DB->update_record('lesson', $lesson);
        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'lesson',
            'instance' => $lesson->id,
            'eventtype' => LESSON_EVENT_TYPE_CLOSE,
            'timestart' => $newtimeclose,
            'timeduration' => 86400,
            'visible' => 1
        ]);
        // Trigger and capture the event when adding a contact.
        $sink = $this->redirectEvents();
        mod_lesson_core_calendar_event_timestart_updated($event, $lesson);
        $triggeredevents = $sink->get_events();
        $moduleupdatedevents = array_filter($triggeredevents, function($e) {
            return is_a($e, 'core\event\course_module_updated');
        });
        $lesson = $DB->get_record('lesson', ['id' => $lesson->id]);
        // Ensure the deadline property matches the event timestart.
        $this->assertEquals($newtimeclose, $lesson->deadline);
        // Ensure the available isn't changed.
        $this->assertEquals($timeopen, $lesson->available);
        // Ensure the timemodified property has been changed.
        $this->assertNotEquals($timemodified, $lesson->timemodified);
        // Confirm that a module updated event is fired when the module is changed.
        $this->assertNotEmpty($moduleupdatedevents);
    }

    /**
     * An unknown event type should not have any limits.
     */
    public function test_mod_lesson_core_calendar_get_valid_event_timestart_range_unknown_event() {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $lesson = new \stdClass();
        $lesson->available = $timeopen;
        $lesson->deadline = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'lesson',
            'instance' => 1,
            'eventtype' => LESSON_EVENT_TYPE_OPEN . "SOMETHING ELSE",
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        list ($min, $max) = mod_lesson_core_calendar_get_valid_event_timestart_range($event, $lesson);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * The open event should be limited by the lesson's deadline property, if it's set.
     */
    public function test_mod_lesson_core_calendar_get_valid_event_timestart_range_open_event() {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $lesson = new \stdClass();
        $lesson->available = $timeopen;
        $lesson->deadline = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'lesson',
            'instance' => 1,
            'eventtype' => LESSON_EVENT_TYPE_OPEN,
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // The max limit should be bounded by the timeclose value.
        list ($min, $max) = mod_lesson_core_calendar_get_valid_event_timestart_range($event, $lesson);
        $this->assertNull($min);
        $this->assertEquals($timeclose, $max[0]);

        // No timeclose value should result in no upper limit.
        $lesson->deadline = 0;
        list ($min, $max) = mod_lesson_core_calendar_get_valid_event_timestart_range($event, $lesson);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * The close event should be limited by the lesson's available property, if it's set.
     */
    public function test_mod_lesson_core_calendar_get_valid_event_timestart_range_close_event() {
        global $CFG;
        require_once($CFG->dirroot . "/calendar/lib.php");

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $timeopen = time();
        $timeclose = $timeopen + DAYSECS;
        $lesson = new \stdClass();
        $lesson->available = $timeopen;
        $lesson->deadline = $timeclose;

        // Create a valid event.
        $event = new \calendar_event([
            'name' => 'Test event',
            'description' => '',
            'format' => 1,
            'courseid' => $course->id,
            'groupid' => 0,
            'userid' => 2,
            'modulename' => 'lesson',
            'instance' => 1,
            'eventtype' => LESSON_EVENT_TYPE_CLOSE,
            'timestart' => 1,
            'timeduration' => 86400,
            'visible' => 1
        ]);

        // The max limit should be bounded by the timeclose value.
        list ($min, $max) = mod_lesson_core_calendar_get_valid_event_timestart_range($event, $lesson);
        $this->assertEquals($timeopen, $min[0]);
        $this->assertNull($max);

        // No deadline value should result in no upper limit.
        $lesson->available = 0;
        list ($min, $max) = mod_lesson_core_calendar_get_valid_event_timestart_range($event, $lesson);
        $this->assertNull($min);
        $this->assertNull($max);
    }

    /**
     * A user who does not have capabilities to add events to the calendar should be able to create an lesson.
     */
    public function test_creation_with_no_calendar_capabilities() {
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $user = self::getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $roleid = self::getDataGenerator()->create_role();
        self::getDataGenerator()->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context, true);
        $generator = self::getDataGenerator()->get_plugin_generator('mod_lesson');
        // Create an instance as a user without the calendar capabilities.
        $this->setUser($user);
        $time = time();
        $params = array(
            'course' => $course->id,
            'available' => $time + 200,
            'deadline' => $time + 2000,
        );
        $generator->create_instance($params);
    }
}

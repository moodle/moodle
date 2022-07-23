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
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */

namespace mod_bigbluebuttonbn;

use calendar_event;
use context_module;
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use mod_bigbluebuttonbn_mod_form;
use MoodleQuickForm;
use navigation_node;
use ReflectionClass;
use stdClass;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/bigbluebuttonbn/lib.php');

/**
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class lib_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Check support
     *
     * @covers ::bigbluebuttonbn_supports
     */
    public function test_bigbluebuttonbn_supports() {
        $this->resetAfterTest();
        $this->assertTrue(bigbluebuttonbn_supports(FEATURE_IDNUMBER));
        $this->assertTrue(bigbluebuttonbn_supports(FEATURE_MOD_INTRO));
        $this->assertFalse(bigbluebuttonbn_supports(FEATURE_GRADE_HAS_GRADE));
    }

    /**
     * Check add instance
     *
     * @covers ::bigbluebuttonbn_add_instance
     */
    public function test_bigbluebuttonbn_add_instance() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        $id = bigbluebuttonbn_add_instance($bbformdata);
        $this->assertNotNull($id);
    }

    /**
     * Check update instance
     *
     * @covers ::bigbluebuttonbn_update_instance
     */
    public function test_bigbluebuttonbn_update_instance() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $bbformdata = $this->get_form_data_from_instance($bbactivity);
        $result = bigbluebuttonbn_update_instance($bbformdata);
        $this->assertTrue($result);
    }

    /**
     * Check delete instance
     *
     * @covers ::bigbluebuttonbn_delete_instance
     */
    public function test_bigbluebuttonbn_delete_instance() {
        $this->resetAfterTest();
        $this->initialise_mock_server();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $result = bigbluebuttonbn_delete_instance($bbactivity->id);
        $this->assertTrue($result);
    }

    /**
     * Check user outline page
     *
     * @covers ::bigbluebuttonbn_user_outline
     */
    public function test_bigbluebuttonbn_user_outline() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $this->setUser($user);

        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance($this->get_course(),
            ['completion' => 2, 'completionview' => 1]);
        $result = bigbluebuttonbn_user_outline($this->get_course(), $user, $bbactivitycm, $bbactivity);
        $this->assertEquals((object) ['info' => '', 'time' => 0], $result);

        bigbluebuttonbn_view($bbactivity, $this->get_course(), $bbactivitycm, $bbactivitycontext);
        $result = bigbluebuttonbn_user_outline($this->get_course(), $user, $bbactivitycm, $bbactivity);
        $this->assertStringContainsString(get_string('report_room_view', 'mod_bigbluebuttonbn'), $result->info);
    }

    /**
     * Check user completion
     *
     * @covers ::bigbluebuttonbn_user_complete
     */
    public function test_bigbluebuttonbn_user_complete() {
        $this->initialise_mock_server();
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance($this->get_course(),
            ['completion' => 2, 'completionview' => 1]);
        $this->setUser($user);

        // Now create a couple of logs.
        bigbluebuttonbn_view($bbactivity, $this->get_course(), $bbactivitycm, $bbactivitycontext);
        ob_start();
        bigbluebuttonbn_user_complete($this->get_course(), $user, $bbactivitycm, $bbactivity);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString(get_string('report_room_view', 'mod_bigbluebuttonbn'), $output);
    }

    /**
     * Check get recent activity
     *
     * @covers ::bigbluebuttonbn_get_recent_mod_activity
     */
    public function test_bigbluebuttonbn_get_recent_mod_activity() {
        $this->initialise_mock_server();
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        $this->setUser($user);
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        // Now create a couple of logs.
        $instance = instance::get_from_instanceid($bbactivity->id);
        $recordings = $this->create_recordings_for_instance($instance, [['name' => "Pre-Recording 1"]]);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_recording_played_event($instance, $recordings[0]->id);

        $activities = $this->prepare_for_recent_activity_array(0, $user->id, 0);
        $this->assertCount(4, $activities);
        $this->assertEquals(
            ["Meeting joined", "Meeting joined", "Recording viewed"],
            array_values(
                array_filter(
                    array_map(function($activity) {
                        return $activity->eventname ?? "";
                    }, $activities),
                    function($e) {
                        return !empty($e);
                    }
                )
            )
        );
        $this->assertEquals("Pre-Recording 1", $activities[3]->content); // The recording view event should contain the name
        // of the activity.
    }

    /**
     * Prepare the list of activities as is done in course recent activity
     *
     * @param int $date
     * @param int $user
     * @param int $group
     * @return array|void
     */
    protected function prepare_for_recent_activity_array($date, $user, $group) {
        // Same algorithm as in cource/recent.php, but stops at the first bbb activity.
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course->id);
        $sections = array();
        $index = 0;
        foreach ($modinfo->get_section_info_all() as $i => $section) {
            if (!empty($section->uservisible)) {
                $sections[$i] = $section;
            }
        }
        foreach ($sections as $sectionnum => $section) {

            $activity = new stdClass();
            $activity->type = 'section';
            if ($section->section > 0) {
                $activity->name = get_section_name($this->get_course(), $section);
            } else {
                $activity->name = '';
            }

            $activity->visible = $section->visible;
            $activities[$index++] = $activity;

            if (empty($modinfo->sections[$sectionnum])) {
                continue;
            }

            foreach ($modinfo->sections[$sectionnum] as $cmid) {
                $cm = $modinfo->cms[$cmid];

                if (!$cm->uservisible) {
                    continue;
                }

                if (!empty($filter) and $cm->modname != $filter) {
                    continue;
                }

                if (!empty($filtermodid) and $cmid != $filtermodid) {
                    continue;
                }

                if ($cm->modname == 'bigbluebuttonbn') {
                    return bigbluebuttonbn_get_recent_mod_activity($activities,
                        $index,
                        $date,
                        $course->id, $cmid,
                        $user,
                        $group);
                }
            }
        }

    }

    /**
     * Check user recent activity
     *
     * @covers ::bigbluebuttonbn_print_recent_mod_activity
     */
    public function test_bigbluebuttonbn_print_recent_mod_activity() {
        $this->initialise_mock_server();
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        $this->setUser($user);
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        // Now create a couple of logs.
        $instance = instance::get_from_instanceid($bbactivity->id);
        $recordings = $this->create_recordings_for_instance($instance, [['name' => "Pre-Recording 1"]]);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_recording_played_event($instance, $recordings[0]->id);

        $activities = $this->prepare_for_recent_activity_array(0, $user->id, 0);
        ob_start();
        bigbluebuttonbn_print_recent_mod_activity($activities[1], $this->get_course()->id, false, [], false);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('Meeting joined', $output);
    }


    /**
     * Check recent activity for the course
     *
     * @covers ::bigbluebuttonbn_print_recent_activity
     */
    public function test_bigbluebuttonbn_print_recent_activity() {
        global $CFG;
        $this->initialise_mock_server();
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $user = $generator->create_and_enrol($this->get_course());
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        // Now create a couple of logs.
        $timestart = time() - HOURSECS;
        $instance = instance::get_from_instanceid($bbactivity->id);
        $recordings = $this->create_recordings_for_instance($instance, [['name' => "Pre-Recording 1"]]);

        $this->setUser($user); // Important so the logs are set to this user.
        logger::log_meeting_joined_event($instance, 0);
        logger::log_meeting_joined_event($instance, 0);
        logger::log_recording_played_event($instance, $recordings[0]->id);

        $this->setAdminUser();
        // Test that everything is displayed.
        ob_start();
        bigbluebuttonbn_print_recent_activity($this->get_course(), true, $timestart);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('Meeting joined', $output);
        $this->assertStringContainsString(fullname($user), $output);
        // Test that username are displayed in a different format.
        $CFG->alternativefullnameformat = 'firstname lastname firstnamephonetic lastnamephonetic middlename alternatename';
        $expectedname = "$user->firstname $user->lastname $user->firstnamephonetic "
            . "$user->lastnamephonetic $user->middlename $user->alternatename";
        ob_start();
        bigbluebuttonbn_print_recent_activity($this->get_course(), false, $timestart);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('Meeting joined', $output);
        $this->assertStringNotContainsString($expectedname, $output);
        // Test that nothing is displayed as per timestart.
        ob_start();
        bigbluebuttonbn_print_recent_activity($this->get_course(), true, time());
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertEmpty($output);
    }

    /**
     * Check extra capabilities return value
     *
     * @covers ::bigbluebuttonbn_get_extra_capabilities
     */
    public function test_bigbluebuttonbn_get_extra_capabilities() {
        $this->resetAfterTest();
        $this->assertEquals(['moodle/site:accessallgroups'], bigbluebuttonbn_get_extra_capabilities());
    }

    /**
     * Check form definition
     *
     * @covers ::bigbluebuttonbn_reset_course_form_definition
     */
    public function test_bigbluebuttonbn_reset_course_form_definition() {
        global $CFG, $PAGE;
        $this->initialise_mock_server();

        $PAGE->set_course($this->get_course());
        $this->setAdminUser();
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        include_once($CFG->dirroot . '/mod/bigbluebuttonbn/mod_form.php');
        $data = new stdClass();
        $data->instance = $bbactivity;
        $data->id = $bbactivity->id;
        $data->course = $bbactivity->course;

        $form = new mod_bigbluebuttonbn_mod_form($data, 1, $bbactivitycm, $this->get_course());
        $refclass = new ReflectionClass("mod_bigbluebuttonbn_mod_form");
        $formprop = $refclass->getProperty('_form');
        $formprop->setAccessible(true);

        /* @var $mform MoodleQuickForm quickform object definition */
        $mform = $formprop->getValue($form);
        bigbluebuttonbn_reset_course_form_definition($mform);
        $this->assertNotNull($mform->getElement('bigbluebuttonbnheader'));
    }

    /**
     * Check defaults for form
     *
     * @covers ::bigbluebuttonbn_reset_course_form_defaults
     */
    public function test_bigbluebuttonbn_reset_course_form_defaults() {
        global $CFG;
        $this->resetAfterTest();
        $results = bigbluebuttonbn_reset_course_form_defaults($this->get_course());
        $this->assertEquals([
            'reset_bigbluebuttonbn_events' => 0,
            'reset_bigbluebuttonbn_tags' => 0,
            'reset_bigbluebuttonbn_logs' => 0,
            'reset_bigbluebuttonbn_recordings' => 0,
        ], $results);
    }

    /**
     * Reset user data
     *
     * @covers ::bigbluebuttonbn_reset_userdata
     */
    public function test_bigbluebuttonbn_reset_userdata() {
        global $DB;
        $this->resetAfterTest();
        $data = new stdClass();
        $user = $this->getDataGenerator()->create_user();

        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id);

        logger::log_meeting_joined_event(instance::get_from_instanceid($bbactivity->id), 0);
        $data->courseid = $this->get_course()->id;
        $data->reset_bigbluebuttonbn_tags = true;
        $data->reset_bigbluebuttonbn_logs = true;
        $data->course = $bbactivity->course;
        // Add and Join.
        $this->assertCount(2, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity->id]));
        $results = bigbluebuttonbn_reset_userdata($data);
        $this->assertCount(0, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity->id]));
        $this->assertEquals([
            'component' => 'BigBlueButton',
            'item' => 'Deleted tags',
            'error' => false
        ],
            $results[0]
        );
    }

    /**
     * Reset user data in a course and checks it does not delete logs elsewhere
     *
     * @covers ::bigbluebuttonbn_reset_userdata
     */
    public function test_bigbluebuttonbn_reset_userdata_in_a_course() {
        global $DB;
        $this->resetAfterTest();
        $data = new stdClass();
        $user = $this->getDataGenerator()->create_user();

        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id);
        logger::log_meeting_joined_event(instance::get_from_instanceid($bbactivity->id), 0);

        // Now create another activity in a course and add a couple of logs.
        // Aim is to make sure that only logs from one course are deleted.
        $course1 = $this->getDataGenerator()->create_course();
        list($bbactivitycontext1, $bbactivitycm1, $bbactivity1) = $this->create_instance($course1);
        logger::log_meeting_joined_event(instance::get_from_instanceid($bbactivity1->id), 0);

        $data->courseid = $this->get_course()->id;
        $data->reset_bigbluebuttonbn_tags = true;
        $data->reset_bigbluebuttonbn_logs = true;
        $data->course = $bbactivity->course;
        // Add and Join.
        $this->assertCount(2, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity->id]));
        $this->assertCount(2, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity1->id]));
        bigbluebuttonbn_reset_userdata($data);
        $this->assertCount(0, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity->id]));
        $this->assertCount(2, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity1->id]));
    }

    /**
     * Reset user data in a course but do not delete logs
     *
     * @covers ::bigbluebuttonbn_reset_userdata
     */
    public function test_bigbluebuttonbn_reset_userdata_logs_not_deleted() {
        global $DB;
        $this->resetAfterTest();
        $data = new stdClass();
        $user = $this->getDataGenerator()->create_user();

        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $this->getDataGenerator()->enrol_user($user->id, $this->course->id);
        logger::log_meeting_joined_event(instance::get_from_instanceid($bbactivity->id), 0);

        $data->courseid = $this->get_course()->id;
        $data->reset_bigbluebuttonbn_logs = false;
        $data->course = $bbactivity->course;
        // Add and Join.
        $this->assertCount(2, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity->id]));
        bigbluebuttonbn_reset_userdata($data);
        $this->assertCount(2, $DB->get_records('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $bbactivity->id]));
    }

    /**
     * Check course module
     *
     * @covers ::bigbluebuttonbn_get_coursemodule_info
     */
    public function test_bigbluebuttonbn_get_coursemodule_info() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $info = bigbluebuttonbn_get_coursemodule_info($bbactivitycm);
        $this->assertEquals($info->name, $bbactivity->name);
    }

    /**
     * Check update since
     *
     * @covers ::bigbluebuttonbn_check_updates_since
     */
    public function test_bigbluebuttonbn_check_updates_since() {
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $result = bigbluebuttonbn_check_updates_since($bbactivitycm, 0);
        $this->assertEquals(
            '{"configuration":{"updated":false},"contentfiles":{"updated":false},"introfiles":' .
            '{"updated":false},"completion":{"updated":false}}',
            json_encode($result)
        );
    }

    /**
     * Check event action (calendar)
     *
     * @covers ::mod_bigbluebuttonbn_core_calendar_provide_event_action
     */
    public function test_mod_bigbluebuttonbn_core_calendar_provide_event_action() {
        global $DB;
        $this->initialise_mock_server();
        $this->resetAfterTest();
        $this->setAdminUser();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();

        // Standard use case, the meeting start and we want add an action event to join the meeting.
        $event = $this->create_action_event($this->get_course(), $bbactivity, logger::EVENT_MEETING_START);
        $factory = new \core_calendar\action_factory();
        $actionevent = mod_bigbluebuttonbn_core_calendar_provide_event_action($event, $factory);
        $this->assertEquals("Join session", $actionevent->get_name());

        // User has already joined the meeting (there is log event EVENT_JOIN already for this user).
        $instance = instance::get_from_instanceid($bbactivity->id);
        logger::log_meeting_joined_event($instance, 0);

        $bbactivity->closingtime = time() - 1000;
        $bbactivity->openingtime = time() - 2000;
        $DB->update_record('bigbluebuttonbn', $bbactivity);
        $event = $this->create_action_event($this->get_course(), $bbactivity, logger::EVENT_MEETING_START);
        $actionevent = mod_bigbluebuttonbn_core_calendar_provide_event_action($event, $factory);
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param stdClass $course The course the bigbluebutton activity is in
     * @param stdClass $bbbactivity The bigbluebutton activity to create an event for
     * @param string $eventtype The event type. eg. ASSIGN_EVENT_TYPE_DUE.
     * @return bool|calendar_event
     */
    private function create_action_event(stdClass $course, stdClass $bbbactivity, string $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename = 'bigbluebuttonbn';
        $event->courseid = $course->id;
        $event->instance = $bbbactivity->id;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->priority = CALENDAR_EVENT_USER_OVERRIDE_PRIORITY;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }

    /**
     * Test setting navigation admin menu
     *
     * @covers ::bigbluebuttonbn_extend_settings_navigation
     */
    public function test_bigbluebuttonbn_extend_settings_navigation_admin() {
        global $PAGE, $CFG;
        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $CFG->bigbluebuttonbn_meetingevents_enabled = true;

        $PAGE->set_cm($bbactivitycm);
        $PAGE->set_context(context_module::instance($bbactivitycm->id));
        $PAGE->set_url('/mod/bigbluebuttonbn/view.php', ['id' => $bbactivitycm->id]);
        $settingnav = $PAGE->settingsnav;

        $this->setAdminUser();
        $node = navigation_node::create('testnavigationnode');
        bigbluebuttonbn_extend_settings_navigation($settingnav, $node);
        $this->assertCount(1, $node->get_children_key_list());
    }

    /**
     * Check additional setting menu
     *
     * @covers ::bigbluebuttonbn_extend_settings_navigation
     */
    public function test_bigbluebuttonbn_extend_settings_navigation_user() {
        global $PAGE, $CFG;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $user = $generator->create_user();
        $this->setUser($user);
        list($course, $bbactivitycmuser) = get_course_and_cm_from_instance($bbactivity->id, 'bigbluebuttonbn');

        $CFG->bigbluebuttonbn_meetingevents_enabled = true;

        $PAGE->set_cm($bbactivitycmuser);
        $PAGE->set_context(context_module::instance($bbactivitycm->id));
        $PAGE->set_url('/mod/bigbluebuttonbn/view.php', ['id' => $bbactivitycm->id]);

        $settingnav = $PAGE->settingsnav;
        $node = navigation_node::create('testnavigationnode');
        bigbluebuttonbn_extend_settings_navigation($settingnav, $node);
        $this->assertCount(0, $node->get_children_key_list());
    }

    /**
     * Check the visibility on calendar
     * @covers ::mod_bigbluebuttonbn_core_calendar_is_event_visible
     */
    public function test_mod_bigbluebuttonbn_core_calendar_is_event_visible() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $bbactivity->closingtime = time() - 1000;
        $bbactivity->openingtime = time() - 2000;
        $DB->update_record('bigbluebuttonbn', $bbactivity);
        $event = $this->create_action_event($this->get_course(), $bbactivity, logger::EVENT_MEETING_START);
        $this->assertFalse(mod_bigbluebuttonbn_core_calendar_is_event_visible($event));
        $bbactivity->closingtime = time() + 1000;
        $DB->update_record('bigbluebuttonbn', $bbactivity);
        $event = $this->create_action_event($this->get_course(), $bbactivity, logger::EVENT_MEETING_START);
        $this->assertTrue(mod_bigbluebuttonbn_core_calendar_is_event_visible($event));
        $event->instance = 0;
        $this->assertFalse(mod_bigbluebuttonbn_core_calendar_is_event_visible($event));
    }
}

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
 * BBB Library tests class trait.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
namespace mod_bigbluebuttonbn\test;

use context_module;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\proxy\recording_proxy;
use mod_bigbluebuttonbn\meeting;
use stdClass;
use testing_data_generator;
use core\plugininfo\mod;

trait testcase_helper_trait {
    /** @var testing_data_generator|null */
    protected $generator = null;

    /** @var object|null */
    protected $course = null;

    /**
     * Convenience function to create an instance of a bigbluebuttonactivty.
     *
     * @param stdClass|null $course course to add the module to
     * @param array $params Array of parameters to pass to the generator
     * @param array $options Array of options to pass to the generator
     * @return array($context, $cm, $instance) Testable wrapper around the assign class.
     */
    protected function create_instance(?stdClass $course = null, array $params = [], array $options = []): array {
        // Prior to creating the instance, make sure that the BigBlueButton module is enabled.
        $modules = \core_plugin_manager::instance()->get_plugins_of_type('mod');
        if (!$modules['bigbluebuttonbn']->is_enabled()) {
            mod::enable_plugin('bigbluebuttonbn', true);
        }

        if (!$course) {
            $course = $this->get_course();
        }
        $params['course'] = $course->id;
        $options['visible'] = 1;
        $instance = $this->getDataGenerator()->create_module('bigbluebuttonbn', $params, $options);
        list($course, $cm) = get_course_and_cm_from_instance($instance, 'bigbluebuttonbn');
        $context = context_module::instance($cm->id);

        return [$context, $cm, $instance];
    }

    /**
     * Get the matching form data
     *
     * @param stdClass $bbactivity the current bigbluebutton activity
     * @param stdClass|null $course the course or null (taken from $this->get_course() if null)
     * @return mixed
     */
    protected function get_form_data_from_instance(stdClass $bbactivity, ?stdClass $course = null): object {
        global $USER;

        if (!$course) {
            $course = $this->get_course();
        }
        $this->setAdminUser();
        $bbactivitycm = get_coursemodule_from_instance('bigbluebuttonbn', $bbactivity->id);
        list($cm, $context, $module, $data, $cw) = get_moduleinfo_data($bbactivitycm, $course);
        $this->setUser($USER);
        return $data;
    }

    /**
     * Get or create course if it does not exist
     *
     * @return stdClass|null
     */
    protected function get_course(): stdClass {
        if (!$this->course) {
            $this->course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        }
        return $this->course;
    }

    /**
     * Generate a course, several students and several groups
     *
     * @param stdClass $courserecord
     * @param int $numstudents
     * @param int $numteachers
     * @param int $groupsnum
     * @return array
     */
    protected function setup_course_students_teachers(stdClass $courserecord, int $numstudents, int $numteachers,
        int $groupsnum): array {
        global $DB;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course($courserecord);
        $groups = [];
        for ($i = 0; $i < $groupsnum; $i++) {
            $groups[] = $generator->create_group(['courseid' => $course->id]);
        }
        $generator->create_group(['courseid' => $course->id]);
        $generator->create_group(['courseid' => $course->id]);

        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');

        $students = [];
        for ($i = 0; $i < $numstudents; $i++) {
            $student = $generator->create_user();
            $generator->enrol_user($student->id, $course->id, $roleids['student']);
            $groupid = $groups[$i % $groupsnum]->id;
            groups_add_member($groupid, $student->id);
            $students[] = $student;
        }

        $teachers = [];
        for ($i = 0; $i < $numteachers; $i++) {
            $teacher = $generator->create_user();
            $generator->enrol_user($teacher->id, $course->id, $roleids['teacher']);
            $groupid = $groups[$i % $groupsnum]->id;
            groups_add_member($groupid, $teacher->id);
            $teachers[] = $teacher;
        }
        $bbactivity = $generator->create_module(
            'bigbluebuttonbn',
            ['course' => $course->id],
            ['visible' => true]);

        get_fast_modinfo(0, 0, true);
        return [$course, $groups, $students, $teachers, $bbactivity, $roleids];
    }

    /**
     * This test requires mock server to be present.
     */
    protected function initialise_mock_server(): void {
        if (!defined('TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER')) {
            $this->markTestSkipped(
                'The TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER constant must be defined to run mod_bigbluebuttonbn tests'
            );
        }
        try {
            $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn')->reset_mock();
        } catch (\moodle_exception $e) {
            $this->markTestSkipped(
                'Cannot connect to the mock server for this test. Make sure that TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER points
                to an active Mock server'
            );
        }
    }

    /**
     * Create an return an array of recordings
     *
     * @param instance $instance
     * @param array $recordingdata array of recording information
     * @param array $additionalmeetingdata
     * @return array
     */
    protected function create_recordings_for_instance(instance $instance, array $recordingdata = [],
        $additionalmeetingdata = []): array {
        $recordings = [];
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Create the meetings on the mock server, so like this we can find the recordings.
        $meeting = new meeting($instance);
        $meeting->update_cache(); // The meeting has just been created but we need to force fetch info from the server.
        if (!$meeting->is_running()) {
            $additionalmeetingdata = array_merge([
                'instanceid' => $instance->get_instance_id(),
                'groupid' => $instance->get_group_id()
            ], $additionalmeetingdata);
            $bbbgenerator->create_meeting($additionalmeetingdata);
        }
        foreach ($recordingdata as $rindex => $data) {
            $recordings[] = $bbbgenerator->create_recording(
                array_merge([
                    'bigbluebuttonbnid' => $instance->get_instance_id(),
                    'groupid' => $instance->get_group_id()
                ], $data)
            );
        }
        return $recordings;
    }

    /**
     * Create an activity which includes a set of recordings.
     *
     * @param stdClass $course
     * @param int $type
     * @param array $recordingdata array of recording information
     * @param int $groupid
     * @return array
     */
    protected function create_activity_with_recordings(stdClass $course, int $type, array $recordingdata, int $groupid = 0): array {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');

        $activity = $generator->create_instance([
            'course' => $course->id,
            'type' => $type
        ]);

        $instance = instance::get_from_instanceid($activity->id);
        $instance->set_group_id($groupid);
        $recordings = $this->create_recordings_for_instance($instance, $recordingdata);
        return [
            'course' => $course,
            'activity' => $activity,
            'recordings' => $recordings,
        ];
    }

    /**
     * Create a course, users and recording from dataset given in an array form
     *
     * @param array $dataset
     * @return mixed
     */
    protected function create_from_dataset(array $dataset) {
        list('type' => $type, 'recordingsdata' => $recordingsdata, 'groups' => $groups,
            'users' => $users) = $dataset;
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');

        $coursedata = empty($groups) ? [] : ['groupmodeforce' => true, 'groupmode' => $dataset['coursemode'] ?? VISIBLEGROUPS];
        $this->course = $this->getDataGenerator()->create_course($coursedata);

        foreach ($users as $userdata) {
            $this->getDataGenerator()->create_and_enrol($this->course, $userdata['role'], ['username' => $userdata['username']]);
        }

        if ($groups) {
            foreach ($groups as $groupname => $students) {
                $group = $this->getDataGenerator()->create_group(['name' => $groupname, 'courseid' => $this->course->id]);
                foreach ($students as $username) {
                    $user = \core_user::get_user_by_username($username);
                    $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);
                }
            }
        }
        $instancesettings = [
            'course' => $this->course->id,
            'type' => $type,
            'name' => 'Example',
        ];
        if (!empty($dataset['additionalsettings'])) {
            $instancesettings = array_merge($instancesettings, $dataset['additionalsettings']);
        }
        $activity = $plugingenerator->create_instance($instancesettings);
        $instance = instance::get_from_instanceid($activity->id);
        foreach ($recordingsdata as $groupname => $recordings) {
            if ($groups) {
                $groupid = groups_get_group_by_name($this->course->id, $groupname);
                $instance->set_group_id($groupid);
            }
            $this->create_recordings_for_instance($instance, $recordings);
        }
        return $activity->id;
    }

    /**
     * Create the legacy log entries for this task.
     *
     * @param instance $instance
     * @param int $userid
     * @param int $count
     * @param bool $importedrecordings
     * @param bool $withremoterecordings create recording on the mock server ?
     * @return array
     */
    protected function create_legacy_log_entries(
        instance $instance,
        int $userid,
        int $count = 30,
        bool $importedrecordings = false,
        bool $withremoterecordings = true
    ): array {
        // Create log entries for each (30 for the ungrouped, 30 for the grouped).
        $baselogdata = [
            'courseid' => $instance->get_course_id(),
            'userid' => $userid,
            'log' => $importedrecordings ? 'Import' : 'Create',
            'meta' => json_encode(['record' => true]),
            'imported' => $importedrecordings,
        ];
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        for ($i = 0; $i < $count; $i++) {
            if ($withremoterecordings) {
                // Create a recording.
                $starttime = time() - random_int(HOURSECS, WEEKSECS);
                $recording = $plugingenerator->create_recording([
                        'bigbluebuttonbnid' => $instance->get_instance_id(),
                        'groupid' => $instance->get_group_id(),
                        'starttime' => $starttime,
                        'endtime' => $starttime + HOURSECS,
                ], true); // Create them on the server only.

                $baselogdata['meetingid'] = $instance->get_meeting_id();
                if ($importedrecordings) {
                    // Fetch the data.
                    $data = recording_proxy::fetch_recordings([$recording->recordingid]);
                    $data = end($data);
                    if ($data) {
                        $metaonly = array_filter($data, function($key) {
                            return strstr($key, 'meta_');
                        }, ARRAY_FILTER_USE_KEY);
                    } else {
                        $data = [];
                    }
                    $baselogdata['meta'] = json_encode(array_merge([
                            'recording' => array_diff_key($data, $metaonly),
                    ], $metaonly));

                } else {
                    $baselogdata['meta'] = json_encode((object) ['record' => true]);
                }
            }
            // Insert the legacy log entry.
            $logs[] = $plugingenerator->create_log(array_merge($baselogdata, [
                'bigbluebuttonbnid' => $instance->get_instance_id(),
                'timecreated' => time() - random_int(HOURSECS, WEEKSECS) + (HOURSECS * $i),
            ]));
        }

        return $logs;
    }
}

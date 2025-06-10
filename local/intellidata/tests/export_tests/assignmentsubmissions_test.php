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
 * Assignment Submissions migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\export_tests;

use local_intellidata\custom_db_client_testcase;
use local_intellidata\entities\assignments\submission;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;
use local_intellidata\generator;
use local_intellidata\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/custom_db_client_testcase.php');

/**
 * Assignment Submissions migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class assignmentsubmissions_test extends custom_db_client_testcase {

    /**
     * Test assignment submission create.
     *
     * @covers \local_intellidata\entities\assignments\submission
     * @covers \local_intellidata\entities\assignments\migration
     * @covers \local_intellidata\entities\assignments\observer::submission_created
     */
    public function test_create() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        }

        if (!ParamsHelper::compare_release('3.9.0')) {
            return;
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->create_submission_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->create_submission_test(0);
    }

    /**
     * Test assignment submission update.
     *
     * @covers \local_intellidata\entities\assignments\submission
     * @covers \local_intellidata\entities\assignments\migration
     * @covers \local_intellidata\entities\assignments\observer::submission_updated
     */
    public function test_update() {
        if (test_helper::is_new_phpunit()) {
            $this->resetAfterTest(false);
        } else {
            $this->test_create();
        }

        if (!ParamsHelper::compare_release('3.9.0')) {
            return;
        }

        if ($this->newexportavailable) {
            SettingsHelper::set_setting('newtracking', 1);
            $this->update_submission_test(1);
            SettingsHelper::set_setting('newtracking', 0);
        }

        $this->update_submission_test(0);
    }

    /**
     * Update assignment submission test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function update_submission_test($tracking) {
        global $DB;

        $coursedata = [
            'fullname' => 'ibcoursequizquestion1su' . $tracking,
            'idnumber' => '3333333su' . $tracking,
        ];
        $course = generator::create_course($coursedata);
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $activity = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_id('assign', $activity->cmid, 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);

        // Generate submissions.
        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $files = [
            "local/intellidata/assign/tests/fixtures/submissionsample01.txt",
            "local/intellidata/tests/fixtures/submissionsample02.txt",
        ];
        $this->setUser($student1);

        $assignment = new \assign($context, $cm, $course);

        $assign->create_submission([
            'userid' => $student1->id,
            'assignid' => $cm->id,
            'onlinetext_editor' => [
                'text' => 'test text submission',
                'format' => FORMAT_MOODLE,
            ],
        ]);

        $submission = $DB->get_record('assign_submission', ['assignment' => $cm->instance, 'userid' => $student1->id]);

        if ($tracking == 0) {
            $params = [
                'context' => $context,
                'courseid' => $course->id,
                'objectid' => $submission->id,
                'other' => [
                    'submissionid' => $submission->id,
                    'submissionattempt' => $submission->attemptnumber,
                    'submissionstatus' => $submission->status,
                    'filesubmissioncount' => count($files),
                ],
            ];

            $filesubmission = new \stdClass();
            $filesubmission->numfiles = count($files);
            $filesubmission->submission = $submission->id;
            $filesubmission->assignment = $submission->assignment;
            $filesubmission->id = $DB->insert_record('assignsubmission_file', $filesubmission);

            $params['objectid'] = $filesubmission->id;
            $event = \assignsubmission_file\event\submission_updated::create($params);
            $event->set_assign($assignment);
            $event->trigger();
        } else {
            $DB->update_record('assign_submission', $submission);
        }

        $data = [
            'id' => $submission->id,
            "assignment" => $submission->assignment,
            "userid" => $submission->userid,
            'status' => 'new',
        ];

        $entity = new submission((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'assignmentsubmissions']);

        $datarecord = $storage->get_log_entity_data('u', ['id' => $submission->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }

    /**
     * Create assignment submission test.
     *
     * @param int $tracking
     *
     * @return void
     * @throws \invalid_parameter_exception
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function create_submission_test($tracking) {
        global $DB;

        $coursedata = [
            'fullname' => 'ibcoursequizquestion1ad' . $tracking,
            'idnumber' => '3333333ad' . $tracking,
        ];
        $course = generator::create_course($coursedata);
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $activity = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = get_coursemodule_from_id('assign', $activity->cmid, 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);

        // Generate submissions.
        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $files = [
            "local/intellidata/assign/tests/fixtures/submissionsample01.txt",
            "local/intellidata/tests/fixtures/submissionsample02.txt",
        ];
        $this->setUser($student1);

        $assignment = new \assign($context, $cm, $course);

        $assign->create_submission([
            'userid' => $student1->id,
            'assignid' => $cm->id,
            'onlinetext_editor' => [
                'text' => 'test text submission',
                'format' => FORMAT_MOODLE,
            ],
        ]);

        $submission = $DB->get_record('assign_submission', ['assignment' => $cm->instance, 'userid' => $student1->id]);

        if ($tracking == 0) {
            $params = [
                'context' => $context,
                'courseid' => $course->id,
                'objectid' => $submission->id,
                'other' => [
                    'submissionid' => $submission->id,
                    'submissionattempt' => $submission->attemptnumber,
                    'submissionstatus' => $submission->status,
                    'filesubmissioncount' => count($files),
                ],
            ];

            $filesubmission = new \stdClass();
            $filesubmission->numfiles = count($files);
            $filesubmission->submission = $submission->id;
            $filesubmission->assignment = $submission->assignment;
            $filesubmission->id = $DB->insert_record('assignsubmission_file', $filesubmission);

            $params['objectid'] = $filesubmission->id;
            $event = \assignsubmission_file\event\submission_created::create($params);
            $event->set_assign($assignment);
            $event->trigger();
        }

        $data = [
            'id' => $submission->id,
            "assignment" => $submission->assignment,
            "userid" => $submission->userid,
            'status' => 'new',
        ];

        $entity = new submission((object)$data);
        $entitydata = $entity->export();
        $entitydata = test_helper::filter_fields($entitydata, $data);

        $storage = StorageHelper::get_storage_service(['name' => 'assignmentsubmissions']);

        $datarecord = $storage->get_log_entity_data('c', ['id' => $submission->id]);
        $this->assertNotEmpty($datarecord);

        $datarecorddata = test_helper::filter_fields(json_decode($datarecord->data), $data);
        $this->assertEquals($entitydata, $datarecorddata);
    }
}

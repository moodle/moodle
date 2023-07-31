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

namespace mod_qbassign;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/qbassign/externallib.php');
require_once(__DIR__ . '/fixtures/testable_qbassign.php');

/**
 * Base class for unit tests for external functions in mod_qbassign.
 *
 * @package    mod_qbassign
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class externallib_advanced_testcase extends \externallib_advanced_testcase {

    /**
     * Create a submission for testing the get_submission_status function.
     * @param  bool $submitforgrading whether to submit for grading the submission
     * @param  array $params Optional params to use for creating qbassignment instance.
     * @return array an array containing all the required data for testing
     */
    protected function create_submission_for_testing_status(bool $submitforgrading = false, array $params = []): array {
        global $DB;

        // Create a course and qbassignment and users.
        $course = self::getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1]);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_qbassign');
        $params = array_merge([
            'course' => $course->id,
            'qbassignsubmission_file_maxfiles' => 1,
            'qbassignsubmission_file_maxsizebytes' => 1024 * 1024,
            'qbassignsubmission_onlinetex_enabled' => 1,
            'qbassignsubmission_file_enabled' => 1,
            'submissiondrafts' => 1,
            'qbassignfeedback_file_enabled' => 1,
            'qbassignfeedback_comments_enabled' => 1,
            'attemptreopenmethod' => qbassign_ATTEMPT_REOPEN_METHOD_MANUAL,
            'sendnotifications' => 0
        ], $params);

        set_config('submissionreceipts', 0, 'qbassign');

        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('qbassign', $instance->id);
        $context = \context_module::instance($cm->id);

        $qbassign = new \mod_qbassign_testable_qbassign($context, $cm, $course);

        $student1 = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id);
        $teacher = self::getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $student1->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $teacher->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group2->id, 'userid' => $student2->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group2->id, 'userid' => $teacher->id]);

        $this->setUser($student1);

        // Create a student1 with an online text submission.
        // Simulate a submission.
        $qbassign->get_user_submission($student1->id, true);

        $data = new \stdClass();
        $data->onlinetex_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text with a <a href="@@PLUGINFILE@@/intro.txt">link</a>',
            'format' => FORMAT_MOODLE,
        ];

        $draftidfile = file_get_unused_draft_itemid();
        $usercontext = \context_user::instance($student1->id);
        $filerecord = [
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftidfile,
            'filepath'  => '/',
            'filename'  => 't.txt',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'text contents');

        $data->files_filemanager = $draftidfile;

        $notices = [];
        $qbassign->save_submission($data, $notices);

        if ($submitforgrading) {
            // Now, submit the draft for grading.
            $notices = [];

            $data = new \stdClass;
            $data->userid = $student1->id;
            $qbassign->submit_for_grading($data, $notices);
        }

        return [$qbassign, $instance, $student1, $student2, $teacher, $group1, $group2];
    }

    /**
     * Create a course, qbassignment module instance, student and teacher and enrol them in
     * the course.
     *
     * @param array $params parameters to be provided to the qbassignment module creation
     * @return array containing the course, qbassignment module, student and teacher
     */
    protected function create_qbassign_with_student_and_teacher(array $params = []): array {
        global $DB;

        $course = $this->getDataGenerator()->create_course();
        $params = array_merge([
            'course' => $course->id,
            'name' => 'qbassignment',
            'intro' => 'qbassignment intro text',
        ], $params);

        // Create a course and qbassignment and users.
        $qbassign = $this->getDataGenerator()->create_module('qbassign', $params);

        $cm = get_coursemodule_from_instance('qbassign', $qbassign->id);
        $context = \context_module::instance($cm->id);

        $student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        $teacher = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        qbassign_capability('mod/qbassign:view', CAP_ALLOW, $teacherrole->id, $context->id, true);
        qbassign_capability('mod/qbassign:viewgrades', CAP_ALLOW, $teacherrole->id, $context->id, true);
        qbassign_capability('mod/qbassign:grade', CAP_ALLOW, $teacherrole->id, $context->id, true);
        accesslib_clear_all_caches_for_unit_testing();

        return [
            'course' => $course,
            'qbassign' => $qbassign,
            'student' => $student,
            'teacher' => $teacher,
        ];
    }
}

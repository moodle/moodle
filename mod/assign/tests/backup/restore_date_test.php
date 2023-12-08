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

namespace mod_assign\backup;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/phpunit/classes/restore_date_testcase.php');
require_once($CFG->dirroot . '/mod/assign/tests/fixtures/testable_assign.php');

/**
 * Restore date tests.
 *
 * @package    mod_assign
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_date_test extends \restore_date_testcase {

    /**
     * Test restore dates.
     */
    public function test_restore_dates() {
        global $DB, $USER;

        $record = ['cutoffdate' => 100, 'allowsubmissionsfromdate' => 100, 'duedate' => 100, 'timemodified' => 100];
        list($course, $assign) = $this->create_course_and_module('assign', $record);
        $cm = $DB->get_record('course_modules', ['course' => $course->id, 'instance' => $assign->id]);
        $assignobj = new \mod_assign_testable_assign(\context_module::instance($cm->id), $cm, $course);
        $submission = $assignobj->get_user_submission($USER->id, true);
        $grade = $assignobj->get_user_grade($USER->id, true);

        // User override.
        $override = (object)[
            'assignid' => $assign->id,
            'groupid' => 0,
            'userid' => $USER->id,
            'sortorder' => 1,
            'allowsubmissionsfromdate' => 100,
            'duedate' => 200,
            'cutoffdate' => 300
        ];
        $DB->insert_record('assign_overrides', $override);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newassign = $DB->get_record('assign', ['course' => $newcourseid]);

        $this->assertFieldsNotRolledForward($assign, $newassign, ['timemodified']);
        $props = ['allowsubmissionsfromdate', 'duedate', 'cutoffdate'];
        $this->assertFieldsRolledForward($assign, $newassign, $props);

        $newsubmission = $DB->get_record('assign_submission', ['assignment' => $newassign->id]);
        $newoverride = $DB->get_record('assign_overrides', ['assignid' => $newassign->id]);
        $newgrade = $DB->get_record('assign_grades', ['assignment' => $newassign->id]);

        // Assign submission time checks.
        $this->assertEquals($submission->timecreated, $newsubmission->timecreated);
        $this->assertEquals($submission->timemodified, $newsubmission->timemodified);

        // Assign override time checks.
        $diff = $this->get_diff();
        $this->assertEquals($override->duedate + $diff, $newoverride->duedate);
        $this->assertEquals($override->cutoffdate + $diff, $newoverride->cutoffdate);
        $this->assertEquals($override->allowsubmissionsfromdate + $diff, $newoverride->allowsubmissionsfromdate);

        // Assign grade time checks.
        $this->assertEquals($grade->timecreated, $newgrade->timecreated);
        $this->assertEquals($grade->timemodified, $newgrade->timemodified);
    }

    /**
     * Test backup and restore of an assignment with non-default settings.
     */
    public function test_restore_settings() {
        global $DB;
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['startdate' => $this->startdate]);
        $record = [
            'course' => $course->id,
            'name' => random_string(),
            'intro' => random_string(),
            'introformat' => FORMAT_MARKDOWN,
            'alwaysshowdescription' => 1,
            'submissiondrafts' => 1,
            'sendnotifications' => 1,
            'sendlatenotifications' => 1,
            'sendstudentnotifications' => 0,
            'duedate' => time() + 1,
            'cutoffdate' => time(),
            'gradingduedate' => time() + 2,
            'allowsubmissionsfromdate' => time() - 1,
            'grade' => 10,
            'timemodified' => 100,
            'completionsubmit' => 1,
            'requiresubmissionstatement' => 1,
            'teamsubmission' => 1,
            'requireallteammemberssubmit' => 1,
            'teamsubmissiongroupingid' => $generator->create_grouping(['courseid' => $course->id])->id,
            'blindmarking' => 1,
            'hidegrader' => 1,
            'revealidentities' => 1,
            'attemptreopenmethod' => 'manual',
            'maxattempts' => 2,
            'markingworkflow' => 1,
            'markingallocation' => 1,
            'markinganonymous' => 1,
            'preventsubmissionnotingroup' => 1,
            'activityeditor' => [
                'text' => random_string(),
                'format' => FORMAT_MARKDOWN,
            ],
            'timelimit' => DAYSECS,
            'submissionattachments' => 1,
        ];
        $assign = $this->getDataGenerator()->create_module('assign', $record);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course, $this->startdate);
        $newassign = $DB->get_record('assign', ['course' => $newcourseid]);
        $newgrouping = $DB->get_record('groupings', ['courseid' => $newcourseid]);

        // Verify that the settings of the restored assignment are correct.
        foreach ($record as $setting => $value) {
            $newsetting = $newassign->{$setting} ?? null;
            switch ($setting) {
                case 'course':
                    // Should match the new course.
                    $this->assertEquals($newcourseid, $newsetting);
                    break;
                case 'teamsubmissiongroupingid':
                    // Should match the new grouping.
                    $this->assertEquals($newgrouping->id, $newsetting);
                    break;
                case 'revealidentities':
                    // Reset to default for a restore without user data.
                    $this->assertEquals(0, $newsetting);
                    break;
                case 'activityeditor':
                    $this->assertEquals($value['text'], $newassign->activity);
                    $this->assertEquals($value['format'], $newassign->activityformat);
                    break;
                case 'timemodified':
                    $this->assertFieldsNotRolledForward($assign, $newassign, ['timemodified']);
                    break;
                default:
                    // All other settings should match the original assignment.
                    $this->assertEquals($value, $newsetting, "Failed for '{$setting}'");
            }
        }
    }
}

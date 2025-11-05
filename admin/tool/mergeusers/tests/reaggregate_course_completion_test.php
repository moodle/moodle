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

namespace tool_mergeusers;

use advanced_testcase;
use coding_exception;
use completion_completion;
use dml_exception;
use stdClass;
use tool_mergeusers\local\user_merger;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * Testing reaggregation of courses completion.
 *
 * This test covers the case of positive reaggregation of courses completion.
 * Check assign_test.php for the case when no reaggregation exists.
 * Check them out with --group=tool_mergeusers_reaggregate.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reaggregate_course_completion_test extends advanced_testcase {
    /**
     * Tests reaggregate field is set for courses completion.
     *
     * @group tool_mergeusers
     * @group tool_mergeusers_reaggregate
     * @throws dml_exception
     * @throws coding_exception
     */
    public function test_reaggregate_field_is_updated(): void {
        // Inspired by lib/tests/completionlib_test.php::test_aggregate_completions().
        global $DB, $CFG;
        require_once($CFG->dirroot . '/completion/criteria/completion_criteria_activity.php');
        $this->resetAfterTest(true);
        $time = time();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        $fromstudent = $this->getDataGenerator()->create_user();
        $tostudent = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($fromstudent->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($tostudent->id, $course->id, $studentrole->id);

        $data = $this->getDataGenerator()->create_module('data', ['course' => $course->id], ['completion' => 1]);
        $cmdata = get_coursemodule_from_id('data', $data->cmid);

        // Add activity completion criteria.
        $criteriadata = new stdClass();
        $criteriadata->id = $course->id;
        $criteriadata->criteria_activity = [];
        // Some activities.
        $criteriadata->criteria_activity[$cmdata->id] = 1;
        $class = 'completion_criteria_activity';
        $criterion = new $class();
        $criterion->update_config($criteriadata);

        $this->setUser($teacher);

        // Mark activity incomplete for one of the students.
        $cm = get_coursemodule_from_instance('data', $data->id);
        $completioncriteria = $DB->get_record('course_completion_criteria', []);
        $cmcompletionrecord = (object)[
            'coursemoduleid' => $cm->id,
            'userid' => $fromstudent->id,
            'completionstate' => 1,
            'viewed' => 0,
            'overrideby' => null,
            'timemodified' => 0,
        ];

        $usercompletion = (object)[
            'criteriaid' => $completioncriteria->id,
            'userid' => $fromstudent->id,
            'timecompleted' => 0,
        ];

        $cc = [
            'course'    => $course->id,
            'userid'    => $fromstudent->id,
        ];
        $ccompletion = new completion_completion($cc);
        $completion = $ccompletion->mark_inprogress($time);

        $DB->insert_records('course_modules_completion', [$cmcompletionrecord]);
        $DB->insert_records('course_completion_crit_compl', [$usercompletion]);

        // MDL-33320: for instant completions we need aggregate to work in a single run.
        $DB->set_field('course_completions', 'reaggregate', $time - 2);

        $result = $DB->get_record('course_completions', ['userid' => $fromstudent->id]);
        $this->assertIsObject($result);
        $result = $DB->get_record('course_completions', ['userid' => $tostudent->id]);
        $this->assertFalse($result);
        aggregate_completions(0);


        $mut = new user_merger();
        // This merge already invokes the callback for reaggregate course completion.
        [$success, $logs, $logid] = $mut->merge($tostudent->id, $fromstudent->id);
        $this->assertTrue($success);
        // Check that there is reaggregation of course completion.
        $found = '';
        foreach ($logs as $logline) {
            $found = strstr($logline, 'Course completion reaggregated for user');
            if (!empty($found)) {
                break;
            }
        }
        $this->assertNotEmpty($found);

        $this->assertFalse($DB->get_record('course_completions', ['userid' => $fromstudent->id]));
        $record = $DB->get_record('course_completions', ['userid' => $tostudent->id]);
        $this->assertIsObject($record);
        $this->assertEquals(0, $record->reaggregate);
    }
}

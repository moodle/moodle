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

namespace mod_adaptivequiz;

use advanced_testcase;
use cm_info;
use context_module;
use mod_adaptivequiz\completion\custom_completion;
use mod_adaptivequiz\event\attempt_completed;
use mod_adaptivequiz\local\attempt\attempt_state;
use stdClass;

/**
 * Tests observers of the attempt state change.
 *
 * @package    mod_adaptivequiz
 * @copyright  2022 Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers     \mod_adaptivequiz\attempt_state_change_observers::attempt_completed
 */
class attempt_state_change_observers_test extends advanced_testcase {

    public function test_it_handles_completion_state(): void {
        global $DB;

        $this->resetAfterTest();

        // Test it can set the activity as completed.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        $questioncategory = $this->getDataGenerator()
            ->get_plugin_generator('core_question')
            ->create_question_category(['name' => 'My category']);

        $adaptivequiz = $this->getDataGenerator()
            ->get_plugin_generator('mod_adaptivequiz')
            ->create_instance([
                'course' => $course->id,
                'completion' => 1,
                'completionattemptcompleted' => 1,
                'questionpool' => [$questioncategory->id],
            ]);

        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // We don't have any attempt generation yet. Set random data manually.
        $attemptrecordsnapshot = new stdClass();
        $attemptrecordsnapshot->id = 1;
        $attemptrecordsnapshot->instance = $adaptivequiz->id;
        $attemptrecordsnapshot->userid = $user->id;
        $attemptrecordsnapshot->uniqueid = 1;
        $attemptrecordsnapshot->attemptstate = attempt_state::IN_PROGRESS;
        $attemptrecordsnapshot->attemptstopcriteria = 'Unable to fetch a questions for level 1';
        $attemptrecordsnapshot->questionsattempted = 1;
        $attemptrecordsnapshot->difficultysum = 0.0000000;
        $attemptrecordsnapshot->standarderror = 1.51186;
        $attemptrecordsnapshot->measure = 1.94591;
        $attemptrecordsnapshot->timecreated = 1658524979;
        $attemptrecordsnapshot->timemodified = 1658525029;

        $cm = get_coursemodule_from_instance('adaptivequiz', $adaptivequiz->id, $adaptivequiz->course);
        $context = context_module::instance($cm->id);

        $attemptid = 1;

        $event = attempt_completed::create([
            'objectid' => $attemptid,
            'context' => $context,
            'userid' => $user->id,
        ]);
        $event->add_record_snapshot('adaptivequiz_attempt', $attemptrecordsnapshot);
        $event->add_record_snapshot('adaptivequiz', $adaptivequiz);

        attempt_state_change_observers::attempt_completed($event);

        $completion = new custom_completion(cm_info::create($cm), $user->id);
        $this->assertEquals(COMPLETION_COMPLETE, $completion->get_overall_completion_state());
    }
}

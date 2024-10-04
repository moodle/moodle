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

namespace mod_feedback;

use mod_feedback_completion;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/feedback/lib.php');

/**
 * Unit tests to check group membership for teacher access to responses.
 * @package    mod_feedback
 * @copyright  2024 Leon Stringer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class access_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Submit feedback response.
     * @param \stdClass $feedback Mod_feedback instance.
     * @param \stdClass $cm Course module.
     * @param int $itemid ID of a textfield on the form to complete.
     * @param int $userid ID of the user submitting the response.
     * @return mod_feedback_completion
     */
    private function student_response(\stdClass $feedback, \stdClass $cm, int $itemid, int $userid): mod_feedback_completion {
        $completion = new mod_feedback_completion($feedback, $cm,
                    $cm->course, false, null, $userid, $userid);
        $answers = ['textfield_' . $itemid => "test"];
        $completion->save_response_tmp((object) $answers);
        $completion->save_response();
        return $completion;
    }

    /**
     * Test access to feedback responses is allowed or denied correctly for
     * activity group mode and users group membership.
     * @param int $groupmode NOGROUPS, SEPARATEGROUPS, etc.
     * @param int $anonymous FEEDBACK_ANONYMOUS_NO or FEEDBACK_ANONYMOUS_YES.
     * @param array $studentgroups Zero or more names of groups to add the
     * student to, for example, ['group1'].
     * @param array $teacheraccess List of teachers' usernames and whether they
     * should be able to access the submitted feedback, for example,
     * ['teacher1' => true, 'teacher2' => false, ...].
     * @dataProvider response_access_provider
     */
    public function test_response_access(int $groupmode, int $anonymous, array $studentgroups, array $teacheraccesses): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course([
            'groupmode' => $groupmode,
            'groupmodeforce' => true,
        ]);
        $groups['group1'] = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $groups['group2'] = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        /*
         * Participant:  Role:           Groups:
         * teacher1      editingteacher  group1
         * teacher2      teacher         group1
         * teacher3      teacher         (no group)
         * teacher4      editingteacher  (no group)
         * student       student         Set by data provider
         */
        $teachers['teacher1'] = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $this->getDataGenerator()->create_group_member(['groupid' => $groups['group1']->id, 'userid' => $teachers['teacher1']->id]);
        $teachers['teacher2'] = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->getDataGenerator()->create_group_member(['groupid' => $groups['group1']->id, 'userid' => $teachers['teacher2']->id]);
        $teachers['teacher3'] = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $teachers['teacher4'] = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        foreach ($studentgroups as $group) {
            $this->getDataGenerator()->create_group_member(['groupid' => $groups[$group]->id, 'userid' => $student->id]);
        }

        $feedback = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id, 'anonymous' => $anonymous]);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);

        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $item = $feedbackgenerator->create_item_textfield($feedback);
        $completion = $this->student_response($feedback, $cm, $item->id, $student->id);
        $showcompleted = $completion->get_completed()->id;
        $userid = '';   // Value used on mod/feedback/show_entries.php.

        foreach ($teacheraccesses as $teacher => $access) {
            $this->setUser($teachers[$teacher]);

            // moodle_exception should be thrown if teacher doesn't have
            // access.
            try {
                new mod_feedback_completion($feedback, $cm, 0, true, $showcompleted, $userid);
                $this->assertTrue($access);
            } catch (\moodle_exception $ex) {
                $this->assertTrue(!$access);
            }
        }
    }

    /**
     * Group mode and group membership combinations.
     * @return array
     */
    public static function response_access_provider(): array {
        return [
            /*
             * Student is in group1 so response should visible to:
             *   1. Teacher1 (same groups and has accessallgroups)
             *   2. Teachers (same group)
             *   3. Teacher4 (has accessallgroups)
             */
            'separate_groups_student1' => [
                'groupmode' => SEPARATEGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_NO,
                'studentgroups' => ['group1'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => false, 'teacher4' => true],
            ],

            /*
             * Student is in group2 so response should visible to:
             *   1. Teacher1 (same groups and has accessallgroups)
             *   2. Teacher4 (has accessallgroups)
             */
            'separate_groups_student2' => [
                'groupmode' => SEPARATEGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_NO,
                'studentgroups' => ['group2'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => false, 'teacher3' => false, 'teacher4' => true],
            ],

            /*
             * Student is in no groups so response should visible to:
             *   1. Teacher1 (same groups and has accessallgroups)
             *   2. Teacher4 (has accessallgroups)
             */
            'separate_groups_student3' => [
                'groupmode' => SEPARATEGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_NO,
                'studentgroups' => [],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => false, 'teacher3' => false, 'teacher4' => true],
            ],

            /*
             * Same three tests with FEEDBACK_ANONYMOUS_YES.
             */
            'separate_groups_anon_student1' => [
                'groupmode' => SEPARATEGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_YES,
                'studentgroups' => ['group1'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => false, 'teacher4' => true],
            ],
            'separate_groups_anon_student2' => [
                'groupmode' => SEPARATEGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_YES,
                'studentgroups' => ['group2'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => false, 'teacher3' => false, 'teacher4' => true],
            ],
            'separate_groups_anon_student3' => [
                'groupmode' => SEPARATEGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_YES,
                'studentgroups' => [],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => false, 'teacher3' => false, 'teacher4' => true],
            ],

            /*
             * Same three tests with NOGROUPS and FEEDBACK_ANONYMOUS_NO.
             */
            'no_groups_student1' => [
                'groupmode' => NOGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_NO,
                'studentgroups' => ['group1'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => true, 'teacher4' => true],
            ],
            'no_groups_student2' => [
                'groupmode' => NOGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_NO,
                'studentgroups' => ['group2'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => true, 'teacher4' => true],
            ],
            'no_groups_student3' => [
                'groupmode' => NOGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_NO,
                'studentgroups' => [],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => true, 'teacher4' => true],
            ],

            /*
             * Same three tests with NOGROUPS and FEEDBACK_ANONYMOUS_YES.
             */
            'no_groups_anon_student1' => [
                'groupmode' => NOGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_YES,
                'studentgroups' => ['group1'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => true, 'teacher4' => true],
            ],
            'no_groups_anon_student2' => [
                'groupmode' => NOGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_YES,
                'studentgroups' => ['group2'],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => true, 'teacher4' => true],
            ],
            'no_groups_anon_student3' => [
                'groupmode' => NOGROUPS,
                'anonymous' => FEEDBACK_ANONYMOUS_YES,
                'studentgroups' => [],
                'teacheraccesses' => ['teacher1' => true, 'teacher2' => true, 'teacher3' => true, 'teacher4' => true],
            ],
        ];
    }
}

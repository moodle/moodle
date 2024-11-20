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
 * Privacy provider tests
 *
 * @package   mod_realtimequiz
 * @copyright 2018 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_realtimequiz;

use \core_privacy\local\metadata\collection;
use \mod_realtimequiz\privacy\provider;

/**
 * Class mod_realtimequiz_privacy_provider_testcase
 * @covers \mod_realtimequiz\privacy\provider
 */
class privacy_provider_test extends \core_privacy\tests\provider_testcase {
    /** @var \stdClass The student object. */
    protected $student;

    /** @var \stdClass[] The quiz objects. */
    protected $quizzes = [];

    /** @var \stdClass The course object. */
    protected $course;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void {
        $this->resetAfterTest();

        global $DB;
        $gen = self::getDataGenerator();
        $this->course = $gen->create_course();

        // Create 2 quizzes.
        /** @var \mod_realtimequiz_generator $plugingen */
        $plugingen = $gen->get_plugin_generator('mod_realtimequiz');
        $params = [
            'course' => $this->course->id,
        ];
        $this->quizzes = [];
        $this->quizzes[] = $plugingen->create_instance($params);
        $this->quizzes[] = $plugingen->create_instance($params);

        $questions = [
            'First question' => ['Answer A', '*Answer B', 'Answer C'],
            'Second question' => ['*Answer D', 'Answer E'],
            'Third question' => ['Answer F', '*Answer G'],
        ];
        foreach ($this->quizzes as $quiz) {
            $qnum = 1;
            $quiz->questions = [];
            foreach ($questions as $qtext => $answers) {
                $qins = (object)[
                    'quizid' => $quiz->id,
                    'questionnum' => $qnum,
                    'questiontext' => $qtext,
                    'questiontextformat' => FORMAT_PLAIN,
                    'questiontime' => 30,
                ];
                $qins->id = $DB->insert_record('realtimequiz_question', $qins);
                $qins->answers = [];

                foreach ($answers as $atext) {
                    $correct = 0;
                    if ($atext[0] === '*') {
                        $correct = 0;
                        $atext = substr($atext, 1);
                    }
                    $ains = (object)[
                        'questionid' => $qins->id,
                        'answertext' => $atext,
                        'correct' => $correct,
                    ];
                    $ains->id = $DB->insert_record('realtimequiz_answer', $ains);
                    $qins->answers[] = $ains;
                }
                $quiz->questions[] = $qins;
                $qnum++;
            }
        }

        // Create a student who will add data to these quizzes.
        $this->student = $gen->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $gen->enrol_user($this->student->id, $this->course->id, $studentrole->id);

        // The first quiz includes responses from the student.
        $session = (object)[
            'name' => 'Test session',
            'quizid' => $this->quizzes[0]->id,
            'timestamp' => time(),
        ];
        $session->id = $DB->insert_record('realtimequiz_session', $session);
        $ins = (object)[
            'questionid' => $this->quizzes[0]->questions[0]->id,
            'sessionid' => $session->id,
            'userid' => $this->student->id,
            'answerid' => $this->quizzes[0]->questions[0]->answers[2]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);
        $ins = (object)[
            'questionid' => $this->quizzes[0]->questions[1]->id,
            'sessionid' => $session->id,
            'userid' => $this->student->id,
            'answerid' => $this->quizzes[0]->questions[1]->answers[0]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);
        $ins = (object)[
            'questionid' => $this->quizzes[0]->questions[2]->id,
            'sessionid' => $session->id,
            'userid' => $this->student->id,
            'answerid' => $this->quizzes[0]->questions[2]->answers[0]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);

        // The second quiz does not include any user data for the given student.
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata(): void {
        $collection = new collection('mod_realtimequiz');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = array_shift($itemcollection);
        $this->assertEquals('realtimequiz_submitted', $table->get_name());
        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('questionid', $privacyfields);
        $this->assertArrayHasKey('sessionid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('answerid', $privacyfields);
        $this->assertEquals('privacy:metadata:realtimequiz_submitted', $table->get_summary());

        // Make sure all language strings exist.
        foreach ($privacyfields as $langstr) {
            get_string($langstr, 'mod_realtimequiz');
        }
        get_string($table->get_summary(), 'mod_realtimequiz');
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid(): void {
        $cms = [
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[0]->id),
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[1]->id),
        ];
        $expectedctxs = [
            \context_module::instance($cms[0]->id),
        ];
        $expectedctxids = [];
        foreach ($expectedctxs as $ctx) {
            $expectedctxids[] = $ctx->id;
        }
        $contextlist = provider::get_contexts_for_userid($this->student->id);
        $this->assertCount(1, $contextlist);
        $uctxids = [];
        foreach ($contextlist as $uctx) {
            $uctxids[] = $uctx->id;
        }
        $this->assertEmpty(array_diff($expectedctxids, $uctxids));
        $this->assertEmpty(array_diff($uctxids, $expectedctxids));
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context(): void {
        $cms = [
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[0]->id),
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[1]->id),
        ];
        $ctxs = [
            \context_module::instance($cms[0]->id),
            \context_module::instance($cms[1]->id),
        ];

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student->id, $ctxs[0], 'mod_realtimequiz');
        $writer = \core_privacy\local\request\writer::with_context($ctxs[0]);
        $this->assertTrue($writer->has_any_data());

        $this->export_context_data_for_user($this->student->id, $ctxs[1], 'mod_realtimequiz');
        $writer = \core_privacy\local\request\writer::with_context($ctxs[1]);
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $gen = self::getDataGenerator();

        // Create another student who will answer some items in the second quiz.
        $student = $gen->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $gen->enrol_user($student->id, $this->course->id, $studentrole->id);
        $session = (object)[
            'name' => 'Test session 2',
            'quizid' => $this->quizzes[1]->id,
            'timestamp' => time(),
        ];
        $session->id = $DB->insert_record('realtimequiz_session', $session);
        $ins = (object)[
            'questionid' => $this->quizzes[1]->questions[0]->id,
            'sessionid' => $session->id,
            'userid' => $student->id,
            'answerid' => $this->quizzes[1]->questions[0]->answers[1]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);
        $ins = (object)[
            'questionid' => $this->quizzes[1]->questions[1]->id,
            'sessionid' => $session->id,
            'userid' => $student->id,
            'answerid' => $this->quizzes[1]->questions[1]->answers[1]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);

        // Before deletion, we should have 5 submitted responses.
        $this->assertEquals(5, $DB->count_records('realtimequiz_submitted', []));

        // Delete data from the first quiz.
        $cm = get_coursemodule_from_instance('realtimequiz', $this->quizzes[0]->id);
        $cmcontext = \context_module::instance($cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);
        // After deletion, there should be 2 submitted responses.
        $this->assertEquals(2, $DB->count_records('realtimequiz_submitted', []));

        // Delete data from the second quiz.
        $cm = get_coursemodule_from_instance('realtimequiz', $this->quizzes[1]->id);
        $cmcontext = \context_module::instance($cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);
        // After deletion, there should be 0 submitted responses.
        $this->assertEquals(0, $DB->count_records('realtimequiz_submitted', []));
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $gen = self::getDataGenerator();
        $cms = [
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[0]->id),
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[1]->id),
        ];
        $ctxs = [];
        foreach ($cms as $cm) {
            $ctxs[] = \context_module::instance($cm->id);
        }

        // Create a second student who will submit some responses to the first quiz and second quiz.
        $student = $gen->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $gen->enrol_user($this->student->id, $this->course->id, $studentrole->id);
        $session = (object)[
            'name' => 'Test session 2',
            'quizid' => $this->quizzes[0]->id,
            'timestamp' => time(),
        ];
        $session->id = $DB->insert_record('realtimequiz_session', $session);
        $ins = (object)[
            'questionid' => $this->quizzes[0]->questions[0]->id,
            'sessionid' => $session->id,
            'userid' => $student->id,
            'answerid' => $this->quizzes[0]->questions[0]->answers[1]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);
        $ins = (object)[
            'questionid' => $this->quizzes[0]->questions[1]->id,
            'sessionid' => $session->id,
            'userid' => $student->id,
            'answerid' => $this->quizzes[0]->questions[1]->answers[1]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);
        $session2 = (object)[
            'name' => 'Test session 3',
            'quizid' => $this->quizzes[1]->id,
            'timestamp' => time(),
        ];
        $session2->id = $DB->insert_record('realtimequiz_session', $session);
        $ins = (object)[
            'questionid' => $this->quizzes[1]->questions[0]->id,
            'sessionid' => $session2->id,
            'userid' => $student->id,
            'answerid' => $this->quizzes[1]->questions[0]->answers[0]->id,
        ];
        $DB->insert_record('realtimequiz_submitted', $ins);

        // Before deletion, we should have 6 submitted responses.
        $this->assertEquals(6, $DB->count_records('realtimequiz_submitted', []));

        // Delete the data for the first student, for the first quiz.
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student, 'realtimequiz',
                                                                            [$ctxs[0]->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, we should have 3 submitted responses.
        $this->assertEquals(3, $DB->count_records('realtimequiz_submitted', []));
        // Confirm the remaining responses are for the second student.
        $this->assertEquals([$student->id],
                            $DB->get_fieldset_select('realtimequiz_submitted', 'DISTINCT userid', "1=1"));

        // Delete the data for the second student, for all quizzes.
        $contextlist = new \core_privacy\local\request\approved_contextlist($student, 'realtimequiz',
                                                                            [
                                                                                $ctxs[0]->id, $ctxs[1]->id,
                                                                            ]);
        provider::delete_data_for_user($contextlist);

        // After deletion, we should have 0 submitted responses.
        $this->assertEquals(0, $DB->count_records('realtimequiz_submitted', []));
    }

    /**
     * Test provider::get_users_in_context()
     */
    public function test_get_users_in_context(): void {
        $cms = [
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[0]->id),
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[1]->id),
        ];
        $ctxs = [
            \context_module::instance($cms[0]->id),
            \context_module::instance($cms[1]->id),
        ];

        $userlist = new \core_privacy\local\request\userlist($ctxs[0], 'mod_realtimequiz');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);

        $userlist = new \core_privacy\local\request\userlist($ctxs[1], 'mod_realtimequiz');
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test provider::delete_data_for_users()
     */
    public function test_delete_data_for_users(): void {
        $cms = [
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[0]->id),
            get_coursemodule_from_instance('realtimequiz', $this->quizzes[1]->id),
        ];
        $ctxs = [
            \context_module::instance($cms[0]->id),
            \context_module::instance($cms[1]->id),
        ];

        // Delete all data for student.
        $userlist = new \core_privacy\local\request\userlist($ctxs[0], 'mod_realtimequiz');
        provider::get_users_in_context($userlist);
        $approvedlist = new \core_privacy\local\request\approved_userlist($ctxs[0], 'mod_realtimequiz',
                                                                          [$this->student->id]);
        provider::delete_data_for_users($approvedlist);

        // Check user list for checklist 0.
        $userlist = new \core_privacy\local\request\userlist($ctxs[0], 'mod_realtimequiz');
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Check user list for checklist 1.
        $userlist = new \core_privacy\local\request\userlist($ctxs[1], 'mod_realtimequiz');
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }
}

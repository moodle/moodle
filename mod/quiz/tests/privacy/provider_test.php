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
 * Privacy provider tests.
 *
 * @package    mod_quiz
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_quiz\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\writer;
use mod_quiz\privacy\provider;
use mod_quiz\privacy\helper;
use mod_quiz\quiz_attempt;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/tests/privacy_helper.php');

/**
 * Privacy provider tests class.
 *
 * @package    mod_quiz
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\privacy\provider
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    use \core_question_privacy_helper;

    /**
     * Test that a user who has no data gets no contexts
     */
    public function test_get_contexts_for_userid_no_data() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $contextlist = provider::get_contexts_for_userid($USER->id);
        $this->assertEmpty($contextlist);
    }

    /**
     * Test for provider::get_contexts_for_userid() when there is no quiz attempt at all.
     */
    public function test_get_contexts_for_userid_no_attempt_with_override() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        // Make a quiz with an override.
        $this->setUser();
        $quiz = $this->create_test_quiz($course);
        $DB->insert_record('quiz_overrides', [
            'quiz' => $quiz->id,
            'userid' => $user->id,
            'timeclose' => 1300,
            'timelimit' => null,
        ]);

        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $context = \context_module::instance($cm->id);

        // Fetch the contexts - only one context should be returned.
        $this->setUser();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);
        $this->assertEquals($context, $contextlist->current());
    }

    /**
     * The export function should handle an empty contextlist properly.
     */
    public function test_export_user_data_no_data() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($USER->id),
            'mod_quiz',
            []
        );

        provider::export_user_data($approvedcontextlist);
        $this->assertDebuggingNotCalled();

        // No data should have been exported.
        $writer = \core_privacy\local\request\writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data_in_any_context());
    }

    /**
     * The delete function should handle an empty contextlist properly.
     */
    public function test_delete_data_for_user_no_data() {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($USER->id),
            'mod_quiz',
            []
        );

        provider::delete_data_for_user($approvedcontextlist);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Export + Delete quiz data for a user who has made a single attempt.
     */
    public function test_user_with_data() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Make a quiz with an override.
        $this->setUser();
        $quiz = $this->create_test_quiz($course);
        $DB->insert_record('quiz_overrides', [
                'quiz' => $quiz->id,
                'userid' => $user->id,
                'timeclose' => 1300,
                'timelimit' => null,
            ]);

        // Run as the user and make an attempt on the quiz.
        list($quizobj, $quba, $attemptobj) = $this->attempt_quiz($quiz, $user);
        $this->attempt_quiz($quiz, $otheruser);
        $context = $quizobj->get_context();

        // Fetch the contexts - only one context should be returned.
        $this->setUser();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);
        $this->assertEquals($context, $contextlist->current());

        // Perform the export and check the data.
        $this->setUser($user);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'mod_quiz',
            $contextlist->get_contextids()
        );
        provider::export_user_data($approvedcontextlist);

        // Ensure that the quiz data was exported correctly.
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        $quizdata = $writer->get_data([]);
        $this->assertEquals($quizobj->get_quiz_name(), $quizdata->name);

        // Every module has an intro.
        $this->assertTrue(isset($quizdata->intro));

        // Fetch the attempt data.
        $attempt = $attemptobj->get_attempt();
        $attemptsubcontext = [
            get_string('attempts', 'mod_quiz'),
            $attempt->attempt,
        ];
        $attemptdata = writer::with_context($context)->get_data($attemptsubcontext);

        $attempt = $attemptobj->get_attempt();
        $this->assertTrue(isset($attemptdata->state));
        $this->assertEquals(quiz_attempt::state_name($attemptobj->get_state()), $attemptdata->state);
        $this->assertTrue(isset($attemptdata->timestart));
        $this->assertTrue(isset($attemptdata->timefinish));
        $this->assertTrue(isset($attemptdata->timemodified));
        $this->assertFalse(isset($attemptdata->timemodifiedoffline));
        $this->assertFalse(isset($attemptdata->timecheckstate));

        $this->assertTrue(isset($attemptdata->grade));
        $this->assertEquals(100.00, $attemptdata->grade->grade);

        // Check that the exported question attempts are correct.
        $attemptsubcontext = helper::get_quiz_attempt_subcontext($attemptobj->get_attempt(), $user);
        $this->assert_question_attempt_exported(
            $context,
            $attemptsubcontext,
            \question_engine::load_questions_usage_by_activity($attemptobj->get_uniqueid()),
            quiz_get_review_options($quiz, $attemptobj->get_attempt(), $context),
            $user
        );

        // Delete the data and check it is removed.
        $this->setUser();
        provider::delete_data_for_user($approvedcontextlist);
        $this->expectException(\dml_missing_record_exception::class);
        quiz_attempt::create($attemptobj->get_quizid());
    }

    /**
     * Export + Delete quiz data for a user who has made a single attempt.
     */
    public function test_user_with_preview() {
        global $DB;
        $this->resetAfterTest(true);

        // Make a quiz.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance([
                'course' => $course->id,
                'questionsperpage' => 0,
                'grade' => 100.0,
                'sumgrades' => 2,
            ]);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        quiz_add_quiz_question($saq->id, $quiz);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        quiz_add_quiz_question($numq->id, $quiz);

        // Run as the user and make an attempt on the quiz.
        $this->setUser($user);
        $starttime = time();
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id, $user->id);
        $context = $quizobj->get_context();

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        // Start the attempt.
        $attempt = quiz_create_attempt($quizobj, 1, false, $starttime, true, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $starttime);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Answer the questions.
        $attemptobj = quiz_attempt::create($attempt->id);

        $tosubmit = [
            1 => ['answer' => 'frog'],
            2 => ['answer' => '3.14'],
        ];

        $attemptobj->process_submitted_actions($starttime, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($starttime, false);

        // Fetch the contexts - no context should be returned.
        $this->setUser();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Export + Delete quiz data for a user who has made a single attempt.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Make a quiz with an override.
        $this->setUser();
        $quiz = $this->create_test_quiz($course);
        $DB->insert_record('quiz_overrides', [
                'quiz' => $quiz->id,
                'userid' => $user->id,
                'timeclose' => 1300,
                'timelimit' => null,
            ]);

        // Run as the user and make an attempt on the quiz.
        list($quizobj, $quba, $attemptobj) = $this->attempt_quiz($quiz, $user);
        list($quizobj, $quba, $attemptobj) = $this->attempt_quiz($quiz, $otheruser);

        // Create another quiz and questions, and repeat the data insertion.
        $this->setUser();
        $otherquiz = $this->create_test_quiz($course);
        $DB->insert_record('quiz_overrides', [
                'quiz' => $otherquiz->id,
                'userid' => $user->id,
                'timeclose' => 1300,
                'timelimit' => null,
            ]);

        // Run as the user and make an attempt on the quiz.
        list($otherquizobj, $otherquba, $otherattemptobj) = $this->attempt_quiz($otherquiz, $user);
        list($otherquizobj, $otherquba, $otherattemptobj) = $this->attempt_quiz($otherquiz, $otheruser);

        // Delete all data for all users in the context under test.
        $this->setUser();
        $context = $quizobj->get_context();
        provider::delete_data_for_all_users_in_context($context);

        // The quiz attempt should have been deleted from this quiz.
        $this->assertCount(0, $DB->get_records('quiz_attempts', ['quiz' => $quizobj->get_quizid()]));
        $this->assertCount(0, $DB->get_records('quiz_overrides', ['quiz' => $quizobj->get_quizid()]));
        $this->assertCount(0, $DB->get_records('question_attempts', ['questionusageid' => $quba->get_id()]));

        // But not for the other quiz.
        $this->assertNotCount(0, $DB->get_records('quiz_attempts', ['quiz' => $otherquizobj->get_quizid()]));
        $this->assertNotCount(0, $DB->get_records('quiz_overrides', ['quiz' => $otherquizobj->get_quizid()]));
        $this->assertNotCount(0, $DB->get_records('question_attempts', ['questionusageid' => $otherquba->get_id()]));
    }

    /**
     * Export + Delete quiz data for a user who has made a single attempt.
     */
    public function test_wrong_context() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        // Make a choice.
        $this->setUser();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_choice');
        $choice = $plugingenerator->create_instance(['course' => $course->id]);
        $cm = get_coursemodule_from_instance('choice', $choice->id);
        $context = \context_module::instance($cm->id);

        // Fetch the contexts - no context should be returned.
        $this->setUser();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(0, $contextlist);

        // Perform the export and check the data.
        $this->setUser($user);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'mod_quiz',
            [$context->id]
        );
        provider::export_user_data($approvedcontextlist);

        // Ensure that nothing was exported.
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data_in_any_context());

        $this->setUser();

        $dbwrites = $DB->perf_get_writes();

        // Perform a deletion with the approved contextlist containing an incorrect context.
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($user->id),
            'mod_quiz',
            [$context->id]
        );
        provider::delete_data_for_user($approvedcontextlist);
        $this->assertEquals($dbwrites, $DB->perf_get_writes());
        $this->assertDebuggingNotCalled();

        // Perform a deletion of all data in the context.
        provider::delete_data_for_all_users_in_context($context);
        $this->assertEquals($dbwrites, $DB->perf_get_writes());
        $this->assertDebuggingNotCalled();
    }

    /**
     * Create a test quiz for the specified course.
     *
     * @param   \stdClass $course
     * @return  \stdClass
     */
    protected function create_test_quiz($course) {
        global $DB;

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance([
                'course' => $course->id,
                'questionsperpage' => 0,
                'grade' => 100.0,
                'sumgrades' => 2,
            ]);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();

        $saq = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        quiz_add_quiz_question($saq->id, $quiz);
        $numq = $questiongenerator->create_question('numerical', null, ['category' => $cat->id]);
        quiz_add_quiz_question($numq->id, $quiz);

        return $quiz;
    }

    /**
     * Answer questions for a quiz + user.
     *
     * @param   \stdClass   $quiz
     * @param   \stdClass   $user
     * @return  array
     */
    protected function attempt_quiz($quiz, $user) {
        $this->setUser($user);

        $starttime = time();
        $quizobj = \mod_quiz\quiz_settings::create($quiz->id, $user->id);
        $context = $quizobj->get_context();

        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        // Start the attempt.
        $attempt = quiz_create_attempt($quizobj, 1, false, $starttime, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $starttime);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Answer the questions.
        $attemptobj = quiz_attempt::create($attempt->id);

        $tosubmit = [
            1 => ['answer' => 'frog'],
            2 => ['answer' => '3.14'],
        ];

        $attemptobj->process_submitted_actions($starttime, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_finish($starttime, false);

        $this->setUser();

        return [$quizobj, $quba, $attemptobj];
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $anotheruser = $this->getDataGenerator()->create_user();
        $extrauser = $this->getDataGenerator()->create_user();

        // Make a quiz.
        $this->setUser();
        $quiz = $this->create_test_quiz($course);

        // Create an override for user1.
        $DB->insert_record('quiz_overrides', [
            'quiz' => $quiz->id,
            'userid' => $user->id,
            'timeclose' => 1300,
            'timelimit' => null,
        ]);

        // Make an attempt on the quiz as user2.
        list($quizobj, $quba, $attemptobj) = $this->attempt_quiz($quiz, $anotheruser);
        $context = $quizobj->get_context();

        // Fetch users - user1 and user2 should be returned.
        $userlist = new \core_privacy\local\request\userlist($context, 'mod_quiz');
        provider::get_users_in_context($userlist);
        $this->assertEqualsCanonicalizing(
                [$user->id, $anotheruser->id],
                $userlist->get_userids());
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;
        $this->resetAfterTest(true);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Make a quiz in each course.
        $quiz1 = $this->create_test_quiz($course1);
        $quiz2 = $this->create_test_quiz($course2);

        // Attempt quiz1 as user1 and user2.
        list($quiz1obj) = $this->attempt_quiz($quiz1, $user1);
        $this->attempt_quiz($quiz1, $user2);

        // Create an override in quiz1 for user3.
        $DB->insert_record('quiz_overrides', [
            'quiz' => $quiz1->id,
            'userid' => $user3->id,
            'timeclose' => 1300,
            'timelimit' => null,
        ]);

        // Attempt quiz2 as user1.
        $this->attempt_quiz($quiz2, $user1);

        // Delete the data for user1 and user3 in course1 and check it is removed.
        $quiz1context = $quiz1obj->get_context();
        $approveduserlist = new \core_privacy\local\request\approved_userlist($quiz1context, 'mod_quiz',
            [$user1->id, $user3->id]);
        provider::delete_data_for_users($approveduserlist);

        // Only the attempt of user2 should be remained in quiz1.
        $this->assertEquals(
                [$user2->id],
                $DB->get_fieldset_select('quiz_attempts', 'userid', 'quiz = ?', [$quiz1->id])
        );

        // The attempt that user1 made in quiz2 should be remained.
        $this->assertEquals(
                [$user1->id],
                $DB->get_fieldset_select('quiz_attempts', 'userid', 'quiz = ?', [$quiz2->id])
        );

        // The quiz override in quiz1 that we had for user3 should be deleted.
        $this->assertEquals(
                [],
                $DB->get_fieldset_select('quiz_overrides', 'userid', 'quiz = ?', [$quiz1->id])
        );
    }
}

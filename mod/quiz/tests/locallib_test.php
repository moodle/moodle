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
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * Unit tests for (some of) mod/quiz/locallib.php.
 *
 * @copyright  2008 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_locallib_testcase extends advanced_testcase {

    public function test_quiz_rescale_grade() {
        $quiz = new stdClass();
        $quiz->decimalpoints = 2;
        $quiz->questiondecimalpoints = 3;
        $quiz->grade = 10;
        $quiz->sumgrades = 10;
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, false), 0.12345678);
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.12, 2));
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, 'question'),
            format_float(0.123, 3));
        $quiz->sumgrades = 5;
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, false), 0.24691356);
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, true), format_float(0.25, 2));
        $this->assertEquals(quiz_rescale_grade(0.12345678, $quiz, 'question'),
            format_float(0.247, 3));
    }

    public function quiz_attempt_state_data_provider() {
        return [
            [quiz_attempt::IN_PROGRESS, null, null, mod_quiz_display_options::DURING],
            [quiz_attempt::FINISHED, -90, null, mod_quiz_display_options::IMMEDIATELY_AFTER],
            [quiz_attempt::FINISHED, -7200, null, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::FINISHED, -7200, 3600, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::FINISHED, -30, 30, mod_quiz_display_options::IMMEDIATELY_AFTER],
            [quiz_attempt::FINISHED, -90, -30, mod_quiz_display_options::AFTER_CLOSE],
            [quiz_attempt::FINISHED, -7200, -3600, mod_quiz_display_options::AFTER_CLOSE],
            [quiz_attempt::FINISHED, -90, -3600, mod_quiz_display_options::AFTER_CLOSE],
            [quiz_attempt::ABANDONED, -10000000, null, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::ABANDONED, -7200, 3600, mod_quiz_display_options::LATER_WHILE_OPEN],
            [quiz_attempt::ABANDONED, -7200, -3600, mod_quiz_display_options::AFTER_CLOSE],
        ];
    }

    /**
     * @dataProvider quiz_attempt_state_data_provider
     *
     * @param unknown $attemptstate as in the quiz_attempts.state DB column.
     * @param unknown $relativetimefinish time relative to now when the attempt finished, or null for 0.
     * @param unknown $relativetimeclose time relative to now when the quiz closes, or null for 0.
     * @param unknown $expectedstate expected result. One of the mod_quiz_display_options constants/
     */
    public function test_quiz_attempt_state($attemptstate,
            $relativetimefinish, $relativetimeclose, $expectedstate) {

        $attempt = new stdClass();
        $attempt->state = $attemptstate;
        if ($relativetimefinish === null) {
            $attempt->timefinish = 0;
        } else {
            $attempt->timefinish = time() + $relativetimefinish;
        }

        $quiz = new stdClass();
        if ($relativetimeclose === null) {
            $quiz->timeclose = 0;
        } else {
            $quiz->timeclose = time() + $relativetimeclose;
        }

        $this->assertEquals($expectedstate, quiz_attempt_state($quiz, $attempt));
    }

    public function test_quiz_question_tostring() {
        $question = new stdClass();
        $question->qtype = 'multichoice';
        $question->name = 'The question name';
        $question->questiontext = '<p>What sort of <b>inequality</b> is x &lt; y<img alt="?" src="..."></p>';
        $question->questiontextformat = FORMAT_HTML;

        $summary = quiz_question_tostring($question);
        $this->assertEquals('<span class="questionname">The question name</span> ' .
                '<span class="questiontext">What sort of INEQUALITY is x &lt; y[?]' . "\n" . '</span>', $summary);
    }

    /**
     * Test quiz_view
     * @return void
     */
    public function test_quiz_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($quiz->cmid);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        quiz_view($quiz, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_quiz\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/quiz/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    /**
     * Return false when there are not overrides for this quiz instance.
     */
    public function test_quiz_is_overriden_calendar_event_no_override() {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);

        $event = new \calendar_event((object)[
            'modulename' => 'quiz',
            'instance' => $quiz->id,
            'userid' => $user->id
        ]);

        $this->assertFalse(quiz_is_overriden_calendar_event($event));
    }

    /**
     * Return false if the given event isn't an quiz module event.
     */
    public function test_quiz_is_overriden_calendar_event_no_module_event() {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);

        $event = new \calendar_event((object)[
            'userid' => $user->id
        ]);

        $this->assertFalse(quiz_is_overriden_calendar_event($event));
    }

    /**
     * Return false if there is overrides for this use but they belong to another quiz
     * instance.
     */
    public function test_quiz_is_overriden_calendar_event_different_quiz_instance() {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);
        $quiz2 = $quizgenerator->create_instance(['course' => $course->id]);

        $event = new \calendar_event((object) [
            'modulename' => 'quiz',
            'instance' => $quiz->id,
            'userid' => $user->id
        ]);

        $record = (object) [
            'quiz' => $quiz2->id,
            'userid' => $user->id
        ];

        $DB->insert_record('quiz_overrides', $record);

        $this->assertFalse(quiz_is_overriden_calendar_event($event));
    }

    /**
     * Return true if there is a user override for this event and quiz instance.
     */
    public function test_quiz_is_overriden_calendar_event_user_override() {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);

        $event = new \calendar_event((object) [
            'modulename' => 'quiz',
            'instance' => $quiz->id,
            'userid' => $user->id
        ]);

        $record = (object) [
            'quiz' => $quiz->id,
            'userid' => $user->id
        ];

        $DB->insert_record('quiz_overrides', $record);

        $this->assertTrue(quiz_is_overriden_calendar_event($event));
    }

    /**
     * Return true if there is a group override for the event and quiz instance.
     */
    public function test_quiz_is_overriden_calendar_event_group_override() {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(['course' => $course->id]);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $quiz->course));
        $groupid = $group->id;
        $userid = $user->id;

        $event = new \calendar_event((object) [
            'modulename' => 'quiz',
            'instance' => $quiz->id,
            'groupid' => $groupid
        ]);

        $record = (object) [
            'quiz' => $quiz->id,
            'groupid' => $groupid
        ];

        $DB->insert_record('quiz_overrides', $record);

        $this->assertTrue(quiz_is_overriden_calendar_event($event));
    }

    /**
     * Test test_quiz_get_user_timeclose().
     */
    public function test_quiz_get_user_timeclose() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $basetimestamp = time(); // The timestamp we will base the enddates on.

        // Create generator, course and quizzes.
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        // Both quizzes close in two hours.
        $quiz1 = $quizgenerator->create_instance(array('course' => $course->id, 'timeclose' => $basetimestamp + 7200));
        $quiz2 = $quizgenerator->create_instance(array('course' => $course->id, 'timeclose' => $basetimestamp + 7200));
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $student1id = $student1->id;
        $student2id = $student2->id;
        $student3id = $student3->id;
        $teacherid = $teacher->id;

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student1id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student2id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student3id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacherid, $course->id, $teacherrole->id, 'manual');

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group1id = $group1->id;
        $group2id = $group2->id;
        $this->getDataGenerator()->create_group_member(array('userid' => $student1id, 'groupid' => $group1id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2id, 'groupid' => $group2id));

        // Group 1 gets an group override for quiz 1 to close in three hours.
        $record1 = (object) [
            'quiz' => $quiz1->id,
            'groupid' => $group1id,
            'timeclose' => $basetimestamp + 10800 // In three hours.
        ];
        $DB->insert_record('quiz_overrides', $record1);

        // Let's test quiz 1 closes in three hours for user student 1 since member of group 1.
        // Quiz 2 closes in two hours.
        $this->setUser($student1id);
        $params = new stdClass();

        $comparearray = array();
        $object = new stdClass();
        $object->id = $quiz1->id;
        $object->usertimeclose = $basetimestamp + 10800; // The overriden timeclose for quiz 1.

        $comparearray[$quiz1->id] = $object;

        $object = new stdClass();
        $object->id = $quiz2->id;
        $object->usertimeclose = $basetimestamp + 7200; // The unchanged timeclose for quiz 2.

        $comparearray[$quiz2->id] = $object;

        $this->assertEquals($comparearray, quiz_get_user_timeclose($course->id));

        // Let's test quiz 1 closes in two hours (the original value) for user student 3 since member of no group.
        $this->setUser($student3id);
        $params = new stdClass();

        $comparearray = array();
        $object = new stdClass();
        $object->id = $quiz1->id;
        $object->usertimeclose = $basetimestamp + 7200; // The original timeclose for quiz 1.

        $comparearray[$quiz1->id] = $object;

        $object = new stdClass();
        $object->id = $quiz2->id;
        $object->usertimeclose = $basetimestamp + 7200; // The original timeclose for quiz 2.

        $comparearray[$quiz2->id] = $object;

        $this->assertEquals($comparearray, quiz_get_user_timeclose($course->id));

        // User 2 gets an user override for quiz 1 to close in four hours.
        $record2 = (object) [
            'quiz' => $quiz1->id,
            'userid' => $student2id,
            'timeclose' => $basetimestamp + 14400 // In four hours.
        ];
        $DB->insert_record('quiz_overrides', $record2);

        // Let's test quiz 1 closes in four hours for user student 2 since personally overriden.
        // Quiz 2 closes in two hours.
        $this->setUser($student2id);

        $comparearray = array();
        $object = new stdClass();
        $object->id = $quiz1->id;
        $object->usertimeclose = $basetimestamp + 14400; // The overriden timeclose for quiz 1.

        $comparearray[$quiz1->id] = $object;

        $object = new stdClass();
        $object->id = $quiz2->id;
        $object->usertimeclose = $basetimestamp + 7200; // The unchanged timeclose for quiz 2.

        $comparearray[$quiz2->id] = $object;

        $this->assertEquals($comparearray, quiz_get_user_timeclose($course->id));

        // Let's test a teacher sees the original times.
        // Quiz 1 and quiz 2 close in two hours.
        $this->setUser($teacherid);

        $comparearray = array();
        $object = new stdClass();
        $object->id = $quiz1->id;
        $object->usertimeclose = $basetimestamp + 7200; // The unchanged timeclose for quiz 1.

        $comparearray[$quiz1->id] = $object;

        $object = new stdClass();
        $object->id = $quiz2->id;
        $object->usertimeclose = $basetimestamp + 7200; // The unchanged timeclose for quiz 2.

        $comparearray[$quiz2->id] = $object;

        $this->assertEquals($comparearray, quiz_get_user_timeclose($course->id));
    }
}

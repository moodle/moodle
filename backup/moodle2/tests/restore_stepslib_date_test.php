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

namespace core_backup;

use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");
require_once($CFG->libdir . "/badgeslib.php");
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Restore date tests.
 *
 * @package    core_backup
 * @copyright  2017 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class restore_stepslib_date_test extends \restore_date_testcase {

    /**
     * Restoring a manual grade item does not result in the timecreated or
     * timemodified dates being changed.
     */
    public function test_grade_item_date_restore(): void {

        $course = $this->getDataGenerator()->create_course(['startdate' => time()]);

        $params = new \stdClass();
        $params->courseid = $course->id;
        $params->fullname = 'unittestgradecalccategory';
        $params->aggregation = GRADE_AGGREGATE_MEAN;
        $params->aggregateonlygraded = 0;
        $gradecategory = new \grade_category($params, false);
        $gradecategory->insert();

        $gradecategory->load_grade_item();

        $gradeitems = new \grade_item();
        $gradeitems->courseid = $course->id;
        $gradeitems->categoryid = $gradecategory->id;
        $gradeitems->itemname = 'manual grade_item';
        $gradeitems->itemtype = 'manual';
        $gradeitems->itemnumber = 0;
        $gradeitems->needsupdate = false;
        $gradeitems->gradetype = GRADE_TYPE_VALUE;
        $gradeitems->grademin = 0;
        $gradeitems->grademax = 10;
        $gradeitems->iteminfo = 'Manual grade item used for unit testing';
        $gradeitems->timecreated = time();
        $gradeitems->timemodified = time();

        $gradeitems->aggregationcoef = GRADE_AGGREGATE_SUM;

        $gradeitems->insert();

        $gradeitemparams = [
            'itemtype' => 'manual',
            'itemname' => $gradeitems->itemname,
            'courseid' => $course->id,
        ];

        $gradeitem = \grade_item::fetch($gradeitemparams);

        // Do backup and restore.

        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);
        $newgradeitemparams = [
            'itemtype' => 'manual',
            'itemname' => $gradeitems->itemname,
            'courseid' => $course->id,
        ];

        $newgradeitem = \grade_item::fetch($newgradeitemparams);
        $this->assertEquals($gradeitem->timecreated, $newgradeitem->timecreated);
        $this->assertEquals($gradeitem->timemodified, $newgradeitem->timemodified);
    }

    /**
     * The course section timemodified date does not get rolled forward
     * when the course is restored.
     */
    public function test_course_section_date_restore(): void {
        global $DB;
        // Create a course.
        $course = $this->getDataGenerator()->create_course(['startdate' => time()]);
        // Get the second course section.
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => '1']);
        // Do a backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        $newsection = $DB->get_record('course_sections', ['course' => $newcourse->id, 'section' => '1']);
        // Compare dates.
        $this->assertEquals($section->timemodified, $newsection->timemodified);
    }

    /**
     * Test that the timecreated and timemodified dates are not rolled forward when restoring
     * badge data.
     */
    public function test_badge_date_restore(): void {
        global $DB, $USER;
        // Create a course.
        $course = $this->getDataGenerator()->create_course(['startdate' => time()]);
        // Create a badge.
        $fordb = new \stdClass();
        $fordb->id = null;
        $fordb->name = "Test badge";
        $fordb->description = "Testing badges";
        $fordb->timecreated = time();
        $fordb->timemodified = time();
        $fordb->usercreated = $USER->id;
        $fordb->usermodified = $USER->id;
        $fordb->issuername = "Test issuer";
        $fordb->issuerurl = "http://issuer-url.domain.co.nz";
        $fordb->issuercontact = "issuer@example.com";
        $fordb->expiredate = time();
        $fordb->expireperiod = null;
        $fordb->type = BADGE_TYPE_COURSE;
        $fordb->courseid = $course->id;
        $fordb->messagesubject = "Test message subject";
        $fordb->message = "Test message body";
        $fordb->attachment = 1;
        $fordb->notification = 0;
        $fordb->status = BADGE_STATUS_INACTIVE;
        $fordb->nextcron = time();

        $DB->insert_record('badge', $fordb, true);
        // Do a backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        $badges = badges_get_badges(BADGE_TYPE_COURSE, $newcourseid);

        // Compare dates.
        $badge = array_shift($badges);
        $this->assertEquals($fordb->timecreated, $badge->timecreated);
        $this->assertEquals($fordb->timemodified, $badge->timemodified);
        $this->assertEquals($fordb->nextcron, $badge->nextcron);
        // Expire date should be moved forward.
        $this->assertNotEquals($fordb->expiredate, $badge->expiredate);
    }

    /**
     * Test that course calendar events timemodified field is not rolled forward
     * when restoring the course.
     */
    public function test_calendarevents_date_restore(): void {
        global $USER, $DB;
        // Create course.
        $course = $this->getDataGenerator()->create_course(['startdate' => time()]);
        // Create calendar event.
        $starttime = time();
        $event = [
                'name' => 'Start of assignment',
                'description' => '',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => $USER->id,
                'modulename' => 0,
                'instance' => 0,
                'eventtype' => 'course',
                'timestart' => $starttime,
                'timeduration' => 86400,
                'visible' => 1
        ];
        $calendarevent = \calendar_event::create($event, false);

        // Backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        $newevent = $DB->get_record('event', ['courseid' => $newcourseid, 'eventtype' => 'course']);
        // Compare dates.
        $this->assertEquals($calendarevent->timemodified, $newevent->timemodified);
        $this->assertNotEquals($calendarevent->timestart, $newevent->timestart);
    }

    /**
     * Testing that the timeenrolled, timestarted, and timecompleted fields are not rolled forward / back
     * when doing a course restore.
     */
    public function test_course_completion_date_restore(): void {
        global $DB;

        // Create course with course completion enabled.
        $course = $this->getDataGenerator()->create_course(['startdate' => time(), 'enablecompletion' => 1]);

        // Enrol a user in the course.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        // Complete the course with a user.
        $ccompletion = new \completion_completion(['course' => $course->id,
                                                  'userid' => $user->id,
                                                  'timeenrolled' => time(),
                                                  'timestarted' => time()
                                                ]);
        // Now, mark the course as completed.
        $ccompletion->mark_complete();
        $this->assertEquals('100', \core_completion\progress::get_course_progress_percentage($course, $user->id));

        // Back up and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        $newcompletion = \completion_completion::fetch(['course' => $newcourseid, 'userid' => $user->id]);

        // Compare dates.
        $this->assertEquals($ccompletion->timeenrolled, $newcompletion->timeenrolled);
        $this->assertEquals($ccompletion->timestarted, $newcompletion->timestarted);
        $this->assertEquals($ccompletion->timecompleted, $newcompletion->timecompleted);
    }

    /**
     * Testing that the grade grade date information is not changed in the gradebook when a course
     * restore is performed.
     */
    public function test_grade_grade_date_restore(): void {
        global $USER, $DB;
        // Testing the restore of an overridden grade.
        list($course, $assign) = $this->create_course_and_module('assign', []);
        $cm = $DB->get_record('course_modules', ['course' => $course->id, 'instance' => $assign->id]);
        $assignobj = new \mod_assign_testable_assign(\context_module::instance($cm->id), $cm, $course);
        $submission = $assignobj->get_user_submission($USER->id, true);
        $grade = $assignobj->get_user_grade($USER->id, true);
        $grade->grade = 75;
        $assignobj->update_grade($grade);

        // Find the grade item.
        $gradeitemparams = [
            'itemtype' => 'mod',
            'iteminstance' => $assign->id,
            'itemmodule' => 'assign',
            'courseid' => $course->id,
        ];
        $gradeitem = \grade_item::fetch($gradeitemparams);

        // Next the grade grade.
        $gradegrade = \grade_grade::fetch(['itemid' => $gradeitem->id, 'userid' => $USER->id]);
        $gradegrade->set_overridden(true);

        // Back up and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        // Find assignment.
        $assignid = $DB->get_field('assign', 'id', ['course' => $newcourseid]);
        // Find grade item.
        $newgradeitemparams = [
            'itemtype' => 'mod',
            'iteminstance' => $assignid,
            'itemmodule' => 'assign',
            'courseid' => $newcourse->id,
        ];

        $newgradeitem = \grade_item::fetch($newgradeitemparams);
        // Find grade grade.
        $newgradegrade = \grade_grade::fetch(['itemid' => $newgradeitem->id, 'userid' => $USER->id]);
        // Compare dates.
        $this->assertEquals($gradegrade->timecreated, $newgradegrade->timecreated);
        $this->assertEquals($gradegrade->timemodified, $newgradegrade->timemodified);
        $this->assertEquals($gradegrade->overridden, $newgradegrade->overridden);
    }

    /**
     * Checking that the user completion of an activity relating to the timemodified field does not change
     * when doing a course restore.
     */
    public function test_usercompletion_date_restore(): void {
        global $USER, $DB;
        // More completion...
        $course = $this->getDataGenerator()->create_course(['startdate' => time(), 'enablecompletion' => 1]);
        $assign = $this->getDataGenerator()->create_module('assign', [
                'course' => $course->id,
                'completion' => COMPLETION_TRACKING_AUTOMATIC, // Show activity as complete when conditions are met.
                'completionusegrade' => 1 // Student must receive a grade to complete this activity.
            ]);
        $cm = $DB->get_record('course_modules', ['course' => $course->id, 'instance' => $assign->id]);
        $assignobj = new \mod_assign_testable_assign(\context_module::instance($cm->id), $cm, $course);
        $submission = $assignobj->get_user_submission($USER->id, true);
        $grade = $assignobj->get_user_grade($USER->id, true);
        $grade->grade = 75;
        $assignobj->update_grade($grade);

        $coursemodulecompletion = $DB->get_record('course_modules_completion', ['coursemoduleid' => $cm->id]);

        // Back up and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        // Find assignment.
        $assignid = $DB->get_field('assign', 'id', ['course' => $newcourseid]);
        $cm = $DB->get_record('course_modules', ['course' => $newcourse->id, 'instance' => $assignid]);
        $newcoursemodulecompletion = $DB->get_record('course_modules_completion', ['coursemoduleid' => $cm->id]);

        $this->assertEquals($coursemodulecompletion->timemodified, $newcoursemodulecompletion->timemodified);
    }

    /**
     * Checking that the user completion of an activity relating to the view field does not change
     * when doing a course restore.
     * @covers \backup_userscompletion_structure_step
     * @covers \restore_userscompletion_structure_step
     */
    public function test_usercompletion_view_restore(): void {
        global $DB;
        // More completion...
        $course = $this->getDataGenerator()->create_course(['startdate' => time(), 'enablecompletion' => 1]);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $assign = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC, // Show activity as complete when conditions are met.
            'completionview' => 1
        ]);
        $cm = $DB->get_record('course_modules', ['course' => $course->id, 'instance' => $assign->id]);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        $coursemodulecompletion = $DB->get_record('course_modules_viewed', ['coursemoduleid' => $cm->id]);

        // Back up and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        $assignid = $DB->get_field('assign', 'id', ['course' => $newcourseid]);
        $cm = $DB->get_record('course_modules', ['course' => $newcourse->id, 'instance' => $assignid]);
        $newcoursemodulecompletion = $DB->get_record('course_modules_viewed', ['coursemoduleid' => $cm->id]);

        $this->assertEquals($coursemodulecompletion->timecreated, $newcoursemodulecompletion->timecreated);
    }

    /**
     * Ensuring that the timemodified field of the question attempt steps table does not change when
     * a course restore is done.
     */
    public function test_question_attempt_steps_date_restore(): void {
        global $DB;

        $course = $this->getDataGenerator()->create_course(['startdate' => time()]);
        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(array('course' => $course->id, 'questionsperpage' => 0, 'grade' => 100.0,
                                                      'sumgrades' => 2));

        $cm = $DB->get_record('course_modules', ['course' => $course->id, 'instance' => $quiz->id]);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();

        $quizobj = quiz_settings::create($quiz->id, $user1->id);

        // Start the attempt.
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user1->id);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);

        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);

        $prefix1 = $quba->get_field_prefix(1);
        $prefix2 = $quba->get_field_prefix(2);

        $tosubmit = array(1 => array('answer' => 'frog'),
                          2 => array('answer' => '3.14'));

        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submit($timenow, false);
        $attemptobj->process_grade_submission($timenow);

        $questionattemptstepdates = [];
        $originaliterator = $attemptobj->get_question_usage()->get_attempt_iterator();
        foreach ($originaliterator as $questionattempt) {
            $questionattemptstepdates[] = ['originaldate' => $questionattempt->get_last_action_time()];
        }

        // Back up and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newcourse = get_course($newcourseid);

        // Get the quiz for this new restored course.
        $quizdata = $DB->get_record('quiz', ['course' => $newcourseid]);
        $quizobj = \mod_quiz\quiz_settings::create($quizdata->id, $user1->id);

        $questionusage = $DB->get_record('question_usages', [
                'component' => 'mod_quiz',
                'contextid' => $quizobj->get_context()->id
            ]);

        $newquba = \question_engine::load_questions_usage_by_activity($questionusage->id);

        $restorediterator = $newquba->get_attempt_iterator();
        $i = 0;
        foreach ($restorediterator as $restoredquestionattempt) {
            $questionattemptstepdates[$i]['restoredate'] = $restoredquestionattempt->get_last_action_time();
            $i++;
        }

        foreach ($questionattemptstepdates as $dates) {
            $this->assertEquals($dates['originaldate'], $dates['restoredate']);
        }
    }
}

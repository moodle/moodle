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
 * External API for local_masterbuilder.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_masterbuilder;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_course;

/**
 * External service class.
 *
 * @package    local_masterbuilder
 * @copyright  2024 AuST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {
    /**
     * Parameters for create_question.
     *
     * @return external_function_parameters
     */
    public static function create_question_parameters() {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The ID of the quiz module instance'),
            'questionname' => new external_value(PARAM_TEXT, 'The name of the question'),
            'questiontext' => new external_value(PARAM_RAW, 'The question text'),
            'correctanswer' => new external_value(PARAM_BOOL, 'True for True, False for False', VALUE_DEFAULT, true),
        ]);
    }

    /**
     * Create a True/False question and add it to a quiz.
     *
     * @param int $quizid
     * @param string $questionname
     * @param string $questiontext
     * @param bool $correctanswer
     * @return array
     */
    public static function create_question($quizid, $questionname, $questiontext, $correctanswer) {
        global $DB;

        $params = self::validate_parameters(self::create_question_parameters(), [
            'quizid' => $quizid,
            'questionname' => $questionname,
            'questiontext' => $questiontext,
            'correctanswer' => $correctanswer,
        ]);

        // 1. Get Quiz and Course.
        $quiz = $DB->get_record('quiz', ['id' => $params['quizid']], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $quiz->course], '*', MUST_EXIST);

        $context = context_course::instance($course->id);
        self::validate_context($context);

        // 2. Get/Create Question Category.
        $cat = self::get_or_create_question_category($course, $context);

        // 3. Insert Question.
        $questionid = self::insert_question($cat->id, $params['questionname'], $params['questiontext']);
        self::create_question_answers($questionid, $params['correctanswer']);

        // 4. Add to Quiz.
        quiz_add_quiz_question($questionid, $quiz);
        self::fix_quiz_grades($quiz);

        return [
            'questionid' => $questionid,
            'success' => true,
        ];
    }

    /**
     * Helper to get or create a question category.
     *
     * @param \stdClass $course
     * @param \context $context
     * @return \stdClass
     * @throws \moodle_exception
     */
    protected static function get_or_create_question_category($course, $context) {
        global $DB;
        $cat = $DB->get_record('question_categories', ['contextid' => $context->id], '*', IGNORE_MULTIPLE);
        if (!$cat) {
            $categorydata = new \stdClass();
            $categorydata->name = 'Default for ' . $course->shortname;
            $categorydata->contextid = $context->id;
            $categorydata->info = 'Created by MasterBuilder';
            $categorydata->infoformat = FORMAT_HTML;
            $categorydata->stamp = make_unique_id_code();
            $categorydata->parent = 0;
            $categorydata->sortorder = 999;
            $categorydata->idnumber = null;

            $catid = $DB->insert_record('question_categories', $categorydata);
            if (!$catid) {
                throw new \moodle_exception(
                    'errorcreatingquestioncategory',
                    'local_masterbuilder',
                    '',
                    null,
                    'Failed to insert question category'
                );
            }
            $cat = $DB->get_record('question_categories', ['id' => $catid], '*', MUST_EXIST);
        }
        return $cat;
    }

    /**
     * Helper to insert base question data.
     *
     * @param int $catid
     * @param string $name
     * @param string $text
     * @return int
     * @throws \moodle_exception
     */
    protected static function insert_question($catid, $name, $text) {
        global $DB, $USER;

        // Entry.
        $entry = new \stdClass();
        $entry->questioncategoryid = $catid;
        $entry->idnumber = null;
        $entry->ownerid = $USER->id;
        $entryid = $DB->insert_record('question_bank_entries', $entry);

        if (!$entryid) {
            throw new \moodle_exception('errorinsertingentry', 'local_masterbuilder');
        }

        // Question.
        $question = new \stdClass();
        $question->parent = 0;
        $question->name = $name;
        $question->questiontext = '<p>' . $text . '</p>';
        $question->questiontextformat = FORMAT_HTML;
        $question->generalfeedback = '';
        $question->generalfeedbackformat = FORMAT_HTML;
        $question->defaultmark = 1.0;
        $question->penalty = 1.0;
        $question->qtype = 'truefalse';
        $question->length = 1;
        $question->stamp = make_unique_id_code();
        $question->version = make_unique_id_code();
        $question->timecreated = time();
        $question->timemodified = time();
        $question->createdby = $USER->id;
        $question->modifiedby = $USER->id;

        $questionid = $DB->insert_record('question', $question);
        if (!$questionid) {
            throw new \moodle_exception('errorinsertingquestion', 'local_masterbuilder');
        }

        // Version.
        $version = new \stdClass();
        $version->questionbankentryid = $entryid;
        $version->questionid = $questionid;
        $version->version = 1;
        $version->status = 'ready';
        $DB->insert_record('question_versions', $version);

        return $questionid;
    }

    /**
     * Helper to create T/F answers.
     *
     * @param int $questionid
     * @param bool $correctistrue
     */
    protected static function create_question_answers($questionid, $correctistrue) {
        global $DB;

        // True Answer.
        $trueanswer = new \stdClass();
        $trueanswer->question = $questionid;
        $trueanswer->answer = 'True';
        $trueanswer->answerformat = FORMAT_PLAIN;
        $trueanswer->fraction = $correctistrue ? 1.0 : 0.0;
        $trueanswer->feedback = 'Correct! / ¡Correcto!';
        $trueanswer->feedbackformat = FORMAT_HTML;
        $trueanswerid = $DB->insert_record('question_answers', $trueanswer);

        // False Answer.
        $falseanswer = new \stdClass();
        $falseanswer->question = $questionid;
        $falseanswer->answer = 'False';
        $falseanswer->answerformat = FORMAT_PLAIN;
        $falseanswer->fraction = $correctistrue ? 0.0 : 1.0;
        $falseanswer->feedback = 'Please review the material / Por favor revise el material';
        $falseanswer->feedbackformat = FORMAT_HTML;
        $falseanswerid = $DB->insert_record('question_answers', $falseanswer);

        // Link table.
        $truefalse = new \stdClass();
        $truefalse->question = $questionid;
        $truefalse->trueanswer = $trueanswerid;
        $truefalse->falseanswer = $falseanswerid;
        $truefalse->showstandardinstruction = 1;
        $DB->insert_record('question_truefalse', $truefalse);
    }

    /**
     * Fix quiz sumgrades.
     *
     * @param \stdClass $quiz
     */
    protected static function fix_quiz_grades($quiz) {
        global $DB;
        $slot = $DB->get_record('quiz_slots', ['quizid' => $quiz->id, 'slot' => 1]);
        if ($slot) {
            $slot->maxmark = 1.0;
            $DB->update_record('quiz_slots', $slot);

            $quiz->sumgrades = 1.0;
            $DB->update_record('quiz', $quiz);
        }
    }

    /**
     * Returns description for create_question.
     *
     * @return external_single_structure
     */
    public static function create_question_returns() {
        return new external_single_structure([
            'questionid' => new external_value(PARAM_INT, 'The ID of the created question'),
            'success' => new external_value(PARAM_BOOL, 'Success status'),
        ]);
    }

    // ========================================
    // === Configure Course Completion ===
    // ========================================

    /**
     * Parameters for configure_course_completion.
     *
     * @return external_function_parameters
     */
    public static function configure_course_completion_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'The course ID'),
            'requiregrade' => new external_value(PARAM_BOOL, 'Require 100% course grade', VALUE_DEFAULT, true),
            'requireactivity' => new external_value(PARAM_BOOL, 'Require activity completion', VALUE_DEFAULT, true),
        ]);
    }

    /**
     * Configure course completion settings.
     * Sets up: ALL conditions must be met, activity completion required, 100% grade required.
     *
     * @param int $courseid
     * @param bool $requiregrade
     * @param bool $requireactivity
     * @return array
     */
    public static function configure_course_completion($courseid, $requiregrade = true, $requireactivity = true) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/completion/criteria/completion_criteria_activity.php');
        require_once($CFG->dirroot . '/completion/criteria/completion_criteria_grade.php');

        $params = self::validate_parameters(self::configure_course_completion_parameters(), [
            'courseid' => $courseid,
            'requiregrade' => $requiregrade,
            'requireactivity' => $requireactivity,
        ]);

        $context = context_course::instance($params['courseid']);
        self::validate_context($context);

        $courseid = $params['courseid'];

        // Enable completion tracking for the course.
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $course->enablecompletion = 1;
        $DB->update_record('course', $course);

        // Delete existing completion criteria for this course.
        $DB->delete_records('course_completion_criteria', ['course' => $courseid]);
        $DB->delete_records('course_completion_aggr_methd', ['course' => $courseid]);

        // Set aggregation method: ALL conditions must be met.
        $aggr = new \stdClass();
        $aggr->course = $courseid;
        $aggr->criteriatype = null;
        $aggr->method = 1; // All
        $aggr->value = null;
        $DB->insert_record('course_completion_aggr_methd', $aggr);

        // Also set activity aggregation to ALL.
        $activityaggr = new \stdClass();
        $activityaggr->course = $courseid;
        $activityaggr->criteriatype = COMPLETION_CRITERIA_TYPE_ACTIVITY;
        $activityaggr->method = 1; // All
        $activityaggr->value = null;
        $DB->insert_record('course_completion_aggr_methd', $activityaggr);

        $criteriasadded = 0;

        // Add activity completion criteria for all activities with completion enabled.
        if ($params['requireactivity']) {
            $activities = $DB->get_records_sql(
                "SELECT cm.id, cm.module, cm.instance, cm.completion 
                 FROM {course_modules} cm 
                 WHERE cm.course = ? AND cm.completion > 0",
                [$courseid]
            );

            foreach ($activities as $cm) {
                $criteria = new \stdClass();
                $criteria->course = $courseid;
                $criteria->criteriatype = COMPLETION_CRITERIA_TYPE_ACTIVITY;
                $criteria->module = $cm->module;
                $criteria->moduleinstance = $cm->id;
                $criteria->courseinstance = null;
                $criteria->enrolperiod = null;
                $criteria->timeend = null;
                $criteria->gradepass = null;
                $criteria->role = null;
                $DB->insert_record('course_completion_criteria', $criteria);
                $criteriasadded++;
            }
        }

        // Add grade criteria (100%).
        if ($params['requiregrade']) {
            $criteria = new \stdClass();
            $criteria->course = $courseid;
            $criteria->criteriatype = COMPLETION_CRITERIA_TYPE_GRADE;
            $criteria->module = null;
            $criteria->moduleinstance = null;
            $criteria->courseinstance = null;
            $criteria->enrolperiod = null;
            $criteria->timeend = null;
            $criteria->gradepass = 100.0; // 100%
            $criteria->role = null;
            $DB->insert_record('course_completion_criteria', $criteria);
            $criteriasadded++;
        }

        // Rebuild course completion cache.
        $completion = new \completion_info($course);
        rebuild_course_cache($courseid, true);

        return [
            'success' => true,
            'message' => "Course completion configured: {$criteriasadded} criteria added.",
        ];
    }

    /**
     * Returns for configure_course_completion.
     *
     * @return external_single_structure
     */
    public static function configure_course_completion_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }

    // ========================================
    // === Configure Quiz Settings ===
    // ========================================

    /**
     * Parameters for configure_quiz_settings.
     *
     * @return external_function_parameters
     */
    public static function configure_quiz_settings_parameters() {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The quiz instance ID'),
            'gradetopass' => new external_value(PARAM_FLOAT, 'Grade to pass (percentage)', VALUE_DEFAULT, 100.0),
        ]);
    }

    /**
     * Configure quiz settings for passing grade and completion.
     *
     * @param int $quizid
     * @param float $gradetopass
     * @return array
     */
    public static function configure_quiz_settings($quizid, $gradetopass = 100.0) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        require_once($CFG->dirroot . '/mod/quiz/lib.php');

        $params = self::validate_parameters(self::configure_quiz_settings_parameters(), [
            'quizid' => $quizid,
            'gradetopass' => $gradetopass,
        ]);

        // Get quiz and course module.
        $quiz = $DB->get_record('quiz', ['id' => $params['quizid']], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course, false, MUST_EXIST);

        $context = context_course::instance($quiz->course);
        self::validate_context($context);

        // 1. Set Quiz Grade and Sumgrades settings to 10.0.
        $quiz->grade = 10.0;
        $quiz->sumgrades = 10.0;
        
        // 2. Enable Review Options so users can see their grade immediately.
        // Enable 'marks' (grade) for Immediately, Open, and Closed states.
        // Constants used: 0x10000 (Immediate), 0x20000 (Open), 0x40000 (Closed)
        // We broadly enable review permissions to ensure the user isn't blocked from seeing "Finished".
        $reviewoptions = 0x10000 | 0x20000 | 0x40000;
        
        $quiz->reviewattempt = $reviewoptions; // Enable clicking "Review"
        $quiz->reviewmarks = $reviewoptions;
        $quiz->reviewcorrectness = $reviewoptions;
        $quiz->reviewgeneralfeedback = $reviewoptions;
        $quiz->reviewoverallfeedback = $reviewoptions;
        $quiz->reviewrightanswer = $reviewoptions;
        
        $DB->update_record('quiz', $quiz);

        // 3. Update the quiz slot to has maxmark = 10.
        $slot = $DB->get_record('quiz_slots', ['quizid' => $quiz->id, 'slot' => 1]);
        if ($slot) {
            $slot->maxmark = 10.0;
            $DB->update_record('quiz_slots', $slot);
        }

        // 4. Update Grade Items (Check max grade matches quiz grade).
        $gradeitem = $DB->get_record('grade_items', [
            'itemtype' => 'mod',
            'itemmodule' => 'quiz',
            'iteminstance' => $quiz->id,
        ]);
        
        if ($gradeitem) {
            $gradeitem->gradepass = 10.0; // 100% of 10 = 10
            $gradeitem->grademax = 10.0;
            $gradeitem->rawgrademax = 10.0;
            $DB->update_record('grade_items', $gradeitem);
        }
        
        // Force update of quiz grades in Moodle core
        quiz_update_grades($quiz);

        // 5. Update Course Module Completion settings.
        // completion = 2 (auto), completionpassgrade = 1
        $cm->completion = 2; // Automatic completion
        $DB->set_field('course_modules', 'completion', 2, ['id' => $cm->id]);
        $DB->set_field('course_modules', 'completionpassgrade', 1, ['id' => $cm->id]);
        $DB->set_field('course_modules', 'completiongradeitemnumber', 0, ['id' => $cm->id]);

        // Rebuild course cache to reflect completion rules.
        rebuild_course_cache($quiz->course, true);

        return [
            'success' => true,
            'message' => "Quiz configured: grade=10, passing=10, full review=enabled, auto-completion=true.",
        ];
    }

    /**
     * Returns for configure_quiz_settings.
     *
     * @return external_single_structure
     */
    public static function configure_quiz_settings_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }
}



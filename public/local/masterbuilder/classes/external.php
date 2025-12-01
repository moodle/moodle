<?php
namespace local_masterbuilder;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_course;

class external extends external_api {

    public static function create_question_parameters() {
        return new external_function_parameters([
            'quizid' => new external_value(PARAM_INT, 'The ID of the quiz module instance'),
            'questionname' => new external_value(PARAM_TEXT, 'The name of the question'),
            'questiontext' => new external_value(PARAM_RAW, 'The question text'),
            'correctanswer' => new external_value(PARAM_BOOL, 'True for True, False for False', false, true),
        ]);
    }

    public static function create_question($quizid, $questionname, $questiontext, $correctanswer) {
        global $DB, $USER;

        $params = self::validate_parameters(self::create_question_parameters(), [
            'quizid' => $quizid,
            'questionname' => $questionname,
            'questiontext' => $questiontext,
            'correctanswer' => $correctanswer,
        ]);

        // 1. Get Quiz and Course
        $quiz = $DB->get_record('quiz', ['id' => $params['quizid']], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $quiz->course], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id, false, MUST_EXIST);

        $context = context_course::instance($course->id);
        self::validate_context($context);

        // 2. Get/Create Question Category
        $cat = $DB->get_record('question_categories', ['contextid' => $context->id], '*', IGNORE_MULTIPLE);
        if (!$cat) {
            // Create default category for this course
            $categorydata = new \stdClass();
            $categorydata->name = 'Default for ' . $course->shortname;
            $categorydata->contextid = $context->id;
            $categorydata->info = 'Created by MasterBuilder';
            $categorydata->infoformat = FORMAT_HTML;
            $categorydata->stamp = make_unique_id_code();
            $categorydata->parent = 0;  // Top-level category
            $categorydata->sortorder = 999;
            $categorydata->idnumber = null;
            
            $catid = $DB->insert_record('question_categories', $categorydata);
            
            if (!$catid) {
                throw new \moodle_exception('errorcreatingquestioncategory', 'local_masterbuilder', '', null, 
                    'Failed to insert question category');
            }
            
            // Refetch the category we just created
            $cat = $DB->get_record('question_categories', ['id' => $catid], '*', MUST_EXIST);
        }
        
        // Verify we have a valid category
        if (!$cat || !$cat->id) {
            throw new \moodle_exception('invalidquestioncategory', 'local_masterbuilder', '', null,
                'Category object is null or invalid');
        }

        // 3. Insert Question directly into database (Moodle 4.0+ Schema)
        
        // A. Create Question Bank Entry
        $entry = new \stdClass();
        $entry->questioncategoryid = $cat->id;
        $entry->idnumber = null;
        $entry->ownerid = $USER->id;
        $entryid = $DB->insert_record('question_bank_entries', $entry);
        
        if (!$entryid) {
             throw new \moodle_exception('errorinsertingentry', 'local_masterbuilder');
        }

        // B. Create Question Data
        $question = new \stdClass();
        $question->parent = 0;
        $question->name = $params['questionname'];
        $question->questiontext = '<p>' . $params['questiontext'] . '</p>';
        $question->questiontextformat = FORMAT_HTML;
        $question->generalfeedback = '';
        $question->generalfeedbackformat = FORMAT_HTML;
        $question->defaultmark = 1.0000000;
        $question->penalty = 1.0000000;
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
        
        // C. Create Question Version
        $version = new \stdClass();
        $version->questionbankentryid = $entryid;
        $version->questionid = $questionid;
        $version->version = 1;
        $version->status = 'ready';
        $DB->insert_record('question_versions', $version);
        
        // D. Insert true/false answer options
        $trueanswer = new \stdClass();
        $trueanswer->question = $questionid;
        $trueanswer->answer = 'True';
        $trueanswer->answerformat = FORMAT_PLAIN;
        $trueanswer->fraction = $params['correctanswer'] ? 1.0 : 0.0;
        $trueanswer->feedback = 'Correct! / ¡Correcto!';
        $trueanswer->feedbackformat = FORMAT_HTML;
        $trueanswerid = $DB->insert_record('question_answers', $trueanswer);
        
        $falseanswer = new \stdClass();
        $falseanswer->question = $questionid;
        $falseanswer->answer = 'False';
        $falseanswer->answerformat = FORMAT_PLAIN;
        $falseanswer->fraction = $params['correctanswer'] ? 0.0 : 1.0;
        $falseanswer->feedback = 'Please review the material / Por favor revise el material';
        $falseanswer->feedbackformat = FORMAT_HTML;
        $falseanswerid = $DB->insert_record('question_answers', $falseanswer);

        // F. Insert into question_truefalse (Required for True/False questions)
        $truefalse = new \stdClass();
        $truefalse->question = $questionid;
        $truefalse->trueanswer = $trueanswerid;
        $truefalse->falseanswer = $falseanswerid;
        $truefalse->showstandardinstruction = 1;
        $DB->insert_record('question_truefalse', $truefalse);
        
        // E. Add to Quiz (using quiz_add_quiz_question which handles the slot)
        quiz_add_quiz_question($questionid, $quiz);
        
        // F. Fix Grade Mismatch (Moodle 4.0+ slot grade issue)
        // Ensure the slot has a maxmark > 0
        $slot = $DB->get_record('quiz_slots', array('quizid' => $quiz->id, 'slot' => 1));
        if ($slot) {
            $slot->maxmark = 1.0000000;
            $DB->update_record('quiz_slots', $slot);
            
            // Update quiz sumgrades
            $quiz->sumgrades = 1.0000000;
            $DB->update_record('quiz', $quiz);
        }

        return [
            'questionid' => $questionid,
            'success' => true
        ];
    }

    public static function create_question_returns() {
        return new external_single_structure([
            'questionid' => new external_value(PARAM_INT, 'The ID of the created question'),
            'success' => new external_value(PARAM_BOOL, 'Success status'),
        ]);
    }
}

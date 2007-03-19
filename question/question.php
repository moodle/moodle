<?php // $Id$
/**
 * Page for editing questions
 *
 * This page shows the question editing form or processes the following actions:
 * - create new question (category, qtype)
 * - edit question (id, contextquiz (optional))
 * - cancel (cancel)
 *
 * TODO: currently this still treats the quiz as special
 * TODO: question versioning is not currently enabled
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 *//** */

    require_once(dirname(__FILE__) . '/../config.php');
    require_once('editlib.php'); // NOTE - is this correct? This is just about editing screens?
    require_once($CFG->libdir . '/filelib.php');

    $id = optional_param('id', 0, PARAM_INT); // question id

    $qtype = optional_param('qtype', '', PARAM_FILE);
    $category = optional_param('category', 0, PARAM_INT);
    $inpopup = optional_param('inpopup', 0, PARAM_BOOL);

    $CFG->pagepath = 'question/type/'.$qtype;


    // rqp questions set the type to rqp_nn where nn is the rqp_type id
    if (substr($qtype, 0, 4) == 'rqp_') {
        $typeid = (int) substr($qtype, 4);
        $qtype = 'rqp';
    }

    if ($id) {
        if (! $question = get_record("question", "id", $id)) {
            error("This question doesn't exist");
        }
        if (!empty($category)) {
            $question->category = $category;
        }
        if (! $category = get_record("question_categories", "id", $question->category)) {
            error("This question doesn't belong to a valid category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This question category doesn't belong to a valid course!");
        }

        $qtype = $question->qtype;
        if (!isset($QTYPES[$qtype])) {
            $qtype = 'missingtype';
        }

    } else if ($category) { // only for creating new questions
        if (! $category = get_record("question_categories", "id", $category)) {
            error("This wasn't a valid category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This category doesn't belong to a valid course!");
        }

        $question->category = $category->id;
        $question->qtype    = $qtype;

    } else {
        error("Must specify question id or category");
    }

    if (!isset($SESSION->returnurl)) {
        $SESSION->returnurl = 'edit.php?courseid='.$course->id;
    }

    // TODO: generalise this so it works for any activity
    $contextquiz = isset($SESSION->modform->instance) ? $SESSION->modform->instance : 0;

    if (isset($_REQUEST['cancel'])) {
        redirect($SESSION->returnurl);
    }

    if (empty($qtype)) {
        error("No question type was specified!");
    } else if (!isset($QTYPES[$qtype])) {
        error("Could not find question type: '$qtype'");
    }

    if (!file_exists("type/$qtype/editquestion.php")) {
        redirect(str_ireplace('question.php', 'question2.php', me()));
    }

    require_login($course->id, false);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/question:manage', $coursecontext);


    if ($form = data_submitted() and confirm_sesskey()) {

        if (isset($form->versioning) && isset($question->id) and false) { // disable versioning until it is fixed.
            // use new code that handles whether to overwrite or copy a question
            // and keeps track of the versions in the quiz_question_version table

            // $replaceinquiz is an array with the ids of all quizzes in which
            // the teacher has chosen to replace the old version
            $replaceinquiz = array();
            foreach($form as $key => $val) {
                if ($tmp = quiz_parse_fieldname($key, 'q')) {
                    if ($tmp['mode'] == 'replace') {
                        $replaceinquiz[$tmp['id']] = $tmp['id'];
                        unset($form->$key);
                    }
                }
            }

            // $quizlist is an array with the ids of quizzes which use this question
            $quizlist = array();
            if ($instances = get_records('quiz_question_instances', 'question', $question->id)) {
                foreach($instances as $instance) {
                    $quizlist[$instance->quiz] = $instance->quiz;
                }
            }

            if (isset($form->makecopy)) { // explicitly requested copies should be unhidden
                $question->hidden = 0;
            }

            // Logic to determine whether old version should be overwritten
            $makecopy = isset($form->makecopy) || (!$form->id); unset($form->makecopy);
            if ($makecopy) {
                $replaceold = false;
            } else {
                // this should be improved to exclude teacher preview responses and empty responses
                // the current code leaves many unneeded questions in the database
                $hasresponses = record_exists('question_states', 'question', $form->id) or
                         record_exists('question_states', 'originalquestion', $form->id);
                $replaceinall = ($quizlist == $replaceinquiz); // question is being replaced in all quizzes
                $replaceold   = !$hasresponses && $replaceinall;
            }

            $oldquestionid = false;
            if (!$replaceold) { // create a new question
                $oldquestionid = $question->id;
                if (!$makecopy) {
                    if (!set_field("question", 'hidden', 1, 'id', $question->id)) {
                        error("Could not hide question!");
                    }
                }
                unset($question->id);
            }
            unset($makecopy, $hasresponses, $replaceinall, $replaceold);
            $question = $QTYPES[$qtype]->save_question($question, $form, $course);
            if(!isset($question->id)) {
                error("Failed to save the question!");
            }

            if(!empty($oldquestionid)) {
                // create version entries for different quizzes
                $version = new object();
                $version->oldquestion = $oldquestionid;
                $version->newquestion = $question->id;
                $version->userid      = $USER->id;
                $version->timestamp   = time();

                foreach($replaceinquiz as $qid) {
                    $version->quiz = $qid;
                    if(!insert_record("quiz_question_versions", $version)) {
                        error("Could not store version information of question $oldquestionid in quiz $qid!");
                    }
                }

                /// now update the question references in the quizzes
                if (!empty($replaceinquiz) and $quizzes = get_records_list("quiz", "id", implode(',', $replaceinquiz))) {

                    foreach($quizzes as $quiz) {
                        $questionlist = ",$quiz->questions,"; // a little hack with the commas here. not nice but effective
                        $questionlist = str_replace(",$oldquestionid,", ",$question->id,", $questionlist);
                        $questionlist = substr($questionlist, 1, -1); // and get rid of the surrounding commas again
                        if (!set_field("quiz", 'questions', $questionlist, 'id', $quiz->id)) {
                        error("Could not update questionlist in quiz $quiz->id!");
                        }

                        // the quiz_question_instances table needs to be updated too (aah, the joys of duplication :)
                        if (!set_field('quiz_question_instances', 'question', $question->id, 'quiz', $quiz->id, 'question', $oldquestionid)) {
                        error("Could not update question instance!");
                        }
                        if (isset($SESSION->modform) && (int)$SESSION->modform->instance === (int)$quiz->id) {
                        $SESSION->modform->questions = $questionlist;
                        $SESSION->modform->grades[$question->id] = $SESSION->modform->grades[$oldquestionid];
                        unset($SESSION->modform->grades[$oldquestionid]);
                        }
                    }

                    // change question in attempts
                    if ($attempts = get_records_list('quiz_attempts', 'quiz', implode(',', $replaceinquiz))) {
                        foreach ($attempts as $attempt) {

                            // replace question id in $attempt->layout
                            $questionlist = ",$attempt->layout,"; // a little hack with the commas here. not nice but effective
                            $questionlist = str_replace(",$oldquestionid,", ",$question->id,", $questionlist);
                            $questionlist = substr($questionlist, 1, -1); // and get rid of the surrounding commas again
                            if (!set_field('quiz_attempts', 'layout', $questionlist, 'id', $attempt->id)) {
                                error("Could not update layout in attempt $attempt->id!");
                            }

                            // set originalquestion in states
                            set_field('question_states', 'originalquestion', $oldquestionid, 'attempt', $attempt->uniqueid, 'question', $question->id, 'originalquestion', '0');

                            // replace question id in states
                            set_field('question_states', 'question', $question->id, 'attempt', $attempt->uniqueid, 'question', $oldquestionid);

                            // replace question id in sessions
                            set_field('question_sessions', 'questionid', $question->id, 'attemptid', $attempt->uniqueid, 'questionid', $oldquestionid);

                        }

                        // Now do anything question-type specific that is required to replace the question
                        // For example questions that use the question_answers table to hold part of their question will
                        // have to recode the answer ids in the states
                        $QTYPES[$question->qtype]->change_states_question($oldquestionid, $question, $attempts);
                    }
                }
            }
        } else {
            // use the old code which simply overwrites old versions
            // it is also used for creating new questions

            if (isset($form->makecopy)) {
                $question->hidden = 0; // explicitly requested copies should be unhidden
                $question->id = 0;  // This will prompt save_question to create a new question
            }
            $question = $QTYPES[$qtype]->save_question($question, $form, $course);
            $replaceinquiz = 'all';
        }

        if (empty($question->errors) && $QTYPES[$qtype]->finished_edit_wizard($form)) {
            // DISABLED AUTOMATIC REGRADING
            // Automagically regrade all attempts (and states) in the affected quizzes
            //if (!empty($replaceinquiz)) {
            //    $QTYPES[$question->qtype]->get_question_options($question);
            //    quiz_regrade_question_in_quizzes($question, $replaceinquiz);
            //}

            $strsaved = get_string('changessaved');
            if ($inpopup) {
                notify($strsaved, '');
                close_window(3);
            } else {
                echo '</div>';
                redirect($SESSION->returnurl);
            }
        }
    }
    // TODO: remove restriction to quiz
    $streditingquestion = get_string('editingquestion', 'quiz');
    if (isset($SESSION->modform->instance)) {
        $strediting = '<a href="'.$SESSION->returnurl.'">'.get_string('editingquiz', 'quiz').'</a> -> '.
            $streditingquestion;
    } else {
        $strediting = '<a href="edit.php?courseid='.$course->id.'">'.
            get_string("editquestions", "quiz").'</a> -> '.$streditingquestion;
    }

    print_header_simple($streditingquestion, '', $strediting);

    // prepare the grades selector drop-down used by many question types
    $creategrades = get_grade_options();
    $gradeoptions = $creategrades->gradeoptions;
    $gradeoptionsfull = $creategrades->gradeoptionsfull;

    // Initialise defaults if necessary.
    if (empty($question->id)) {
        $question->id = "";
    }
    if (empty($question->name)) {
        $question->name = "";
    }
    if (empty($question->questiontext)) {
        $question->questiontext = "";
    }
    if (empty($question->image)) {
        $question->image = "";
    }
    if (!isset($question->penalty)) {
        $question->penalty = 0.1;
    }
    if (!isset($question->defaultgrade)) {
        $question->defaultgrade = 1;
    }
    if (empty($question->generalfeedback)) {
        $question->generalfeedback = "";
    }

    // Set up some richtext editing if necessary
    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    if (isset($question->errors)) {
        $err = $question->errors;
    }

    // Print the question editing form
    echo '<br />';
    print_simple_box_start('center');
    require_once('type/'.$qtype.'/editquestion.php');
    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor('questiontext');
    }

    print_footer($course);

?>

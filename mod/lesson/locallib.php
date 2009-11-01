<?php

/**
 * Local library file for Lesson.  These are non-standard functions that are used
 * only by Lesson.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

/**
* Next page -> any page not seen before
*/
if (!defined("LESSON_UNSEENPAGE")) {
    define("LESSON_UNSEENPAGE", 1); // Next page -> any page not seen before
}
/**
* Next page -> any page not answered correctly
*/
if (!defined("LESSON_UNANSWEREDPAGE")) {
    define("LESSON_UNANSWEREDPAGE", 2); // Next page -> any page not answered correctly
}

/**
* Define different lesson flows for next page
*/
$LESSON_NEXTPAGE_ACTION = array (0 => get_string("normal", "lesson"),
                          LESSON_UNSEENPAGE => get_string("showanunseenpage", "lesson"),
                          LESSON_UNANSWEREDPAGE => get_string("showanunansweredpage", "lesson") );

// Lesson jump types defined
//  TODO: instead of using define statements, create an array with all the jump values

/**
 * Jump to Next Page
 */
if (!defined("LESSON_NEXTPAGE")) {
    define("LESSON_NEXTPAGE", -1);
}
/**
 * End of Lesson
 */
if (!defined("LESSON_EOL")) {
    define("LESSON_EOL", -9);
}
/**
 * Jump to an unseen page within a branch and end of branch or end of lesson
 */
if (!defined("LESSON_UNSEENBRANCHPAGE")) {
    define("LESSON_UNSEENBRANCHPAGE", -50);
}
/**
 * Jump to Previous Page
 */
if (!defined("LESSON_PREVIOUSPAGE")) {
    define("LESSON_PREVIOUSPAGE", -40);
}
/**
 * Jump to a random page within a branch and end of branch or end of lesson
 */
if (!defined("LESSON_RANDOMPAGE")) {
    define("LESSON_RANDOMPAGE", -60);
}
/**
 * Jump to a random Branch
 */
if (!defined("LESSON_RANDOMBRANCH")) {
    define("LESSON_RANDOMBRANCH", -70);
}
/**
 * Cluster Jump
 */
if (!defined("LESSON_CLUSTERJUMP")) {
    define("LESSON_CLUSTERJUMP", -80);
}
/**
 * Undefined
 */
if (!defined("LESSON_UNDEFINED")) {
    define("LESSON_UNDEFINED", -99);
}

// Lesson question types defined

/**
 * Short answer question type
 */
if (!defined("LESSON_SHORTANSWER")) {
    define("LESSON_SHORTANSWER",   "1");
}
/**
 * True/False question type
 */
if (!defined("LESSON_TRUEFALSE")) {
    define("LESSON_TRUEFALSE",     "2");
}
/**
 * Multichoice question type
 *
 * If you change the value of this then you need
 * to change it in restorelib.php as well.
 */
if (!defined("LESSON_MULTICHOICE")) {
    define("LESSON_MULTICHOICE",   "3");
}
/**
 * Random question type - not used
 */
if (!defined("LESSON_RANDOM")) {
    define("LESSON_RANDOM",        "4");
}
/**
 * Matching question type
 *
 * If you change the value of this then you need
 * to change it in restorelib.php, in mysql.php
 * and postgres7.php as well.
 */
if (!defined("LESSON_MATCHING")) {
    define("LESSON_MATCHING",      "5");
}
/**
 * Not sure - not used
 */
if (!defined("LESSON_RANDOMSAMATCH")) {
    define("LESSON_RANDOMSAMATCH", "6");
}
/**
 * Not sure - not used
 */
if (!defined("LESSON_DESCRIPTION")) {
    define("LESSON_DESCRIPTION",   "7");
}
/**
 * Numerical question type
 */
if (!defined("LESSON_NUMERICAL")) {
    define("LESSON_NUMERICAL",     "8");
}
/**
 * Multichoice with multianswer question type
 */
if (!defined("LESSON_MULTIANSWER")) {
    define("LESSON_MULTIANSWER",   "9");
}
/**
 * Essay question type
 */
if (!defined("LESSON_ESSAY")) {
    define("LESSON_ESSAY", "10");
}

/**
 * Lesson question type array.
 * Contains all question types used
 */
$LESSON_QUESTION_TYPE = array ( LESSON_MULTICHOICE => get_string("multichoice", "quiz"),
                              LESSON_TRUEFALSE     => get_string("truefalse", "quiz"),
                              LESSON_SHORTANSWER   => get_string("shortanswer", "quiz"),
                              LESSON_NUMERICAL     => get_string("numerical", "quiz"),
                              LESSON_MATCHING      => get_string("match", "quiz"),
                              LESSON_ESSAY           => get_string("essay", "lesson")
//                            LESSON_DESCRIPTION   => get_string("description", "quiz"),
//                            LESSON_RANDOM        => get_string("random", "quiz"),
//                            LESSON_RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
//                            LESSON_MULTIANSWER   => get_string("multianswer", "quiz"),
                              );

// Non-question page types

/**
 * Branch Table page
 */
if (!defined("LESSON_BRANCHTABLE")) {
    define("LESSON_BRANCHTABLE",   "20");
}
/**
 * End of Branch page
 */
if (!defined("LESSON_ENDOFBRANCH")) {
    define("LESSON_ENDOFBRANCH",   "21");
}
/**
 * Start of Cluster page
 */
if (!defined("LESSON_CLUSTER")) {
    define("LESSON_CLUSTER",   "30");
}
/**
 * End of Cluster page
 */
if (!defined("LESSON_ENDOFCLUSTER")) {
    define("LESSON_ENDOFCLUSTER",   "31");
}

// other variables...

/**
 * Flag for the editor for the answer textarea.
 */
if (!defined("LESSON_ANSWER_EDITOR")) {
    define("LESSON_ANSWER_EDITOR",   "1");
}
/**
 * Flag for the editor for the response textarea.
 */
if (!defined("LESSON_RESPONSE_EDITOR")) {
    define("LESSON_RESPONSE_EDITOR",   "2");
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lesson functions go here.  Each of them must have a name that
/// starts with lesson_

/**
 * Print the standard header for lesson module
 *
 * This will also print up to three
 * buttons in the breadcrumb, lesson heading
 * lesson tabs, lesson notifications and perhaps
 * a popup with a media file.
 *
 * @param object $cm Course module record object
 * @param object $course Course record object
 * @param object $lesson Lesson record object
 * @param string $currenttab Current tab for the lesson tabs
 * @param boolean $extraeditbuttons Show the extra edit buttons next to the 'Update this lesson' button.
 * @param integer $lessonpageid if $extraeditbuttons is true then you must pass the page id here.
 **/
function lesson_print_header($cm, $course, $lesson, $currenttab = '', $extraeditbuttons = false, $lessonpageid = NULL) {
    global $CFG, $PAGE, $OUTPUT;

    $activityname = format_string($lesson->name, true, $course->id);

    if (empty($title)) {
        $title = "{$course->shortname}: $activityname";
    }

/// Build the buttons
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('mod/lesson:edit', $context)) {
        $buttons = $OUTPUT->update_module_button($cm->id, 'lesson');
        if ($extraeditbuttons) {
            if ($lessonpageid === NULL) {
                print_error('invalidpageid', 'lesson');
            }
            if (!empty($lessonpageid) and $lessonpageid != LESSON_EOL) {
                $buttons .= '<form '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/mod/lesson/lesson.php">'.
                            '<input type="hidden" name="id" value="'.$cm->id.'" />'.
                            '<input type="hidden" name="action" value="editpage" />'.
                            '<input type="hidden" name="redirect" value="navigation" />'.
                            '<input type="hidden" name="pageid" value="'.$lessonpageid.'" />'.
                            '<input type="submit" value="'.get_string('editpagecontent', 'lesson').'" />'.
                            '</form>';
            }
            $buttons = '<span class="edit_buttons">' . $buttons  .'</span>';
        }
    } else {
        $buttons = '&nbsp;';
    }

/// Header setup
    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_button($buttons);
    echo $OUTPUT->header();

    if (has_capability('mod/lesson:manage', $context)) {

        $helpicon = new moodle_help_icon();
        $helpicon->text = $activityname;
        $helpicon->page = "overview";
        $helpicon->module = "lesson";

        echo $OUTPUT->heading_with_help($helpicon);

        if (!empty($currenttab)) {
            include($CFG->dirroot.'/mod/lesson/tabs.php');
        }
    } else {
        echo $OUTPUT->heading($activityname);
    }

    lesson_print_messages();
}

/**
 * Returns course module, course and module instance given
 * either the course module ID or a lesson module ID.
 *
 * @param int $cmid Course Module ID
 * @param int $lessonid Lesson module instance ID
 * @return array array($cm, $course, $lesson)
 **/
function lesson_get_basics($cmid = 0, $lessonid = 0) {
    global $DB;

    if ($cmid) {
        if (!$cm = get_coursemodule_from_id('lesson', $cmid)) {
            print_error('invalidcoursemodule');
        }
        if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
            print_error('coursemisconf');
        }
        if (!$lesson = $DB->get_record('lesson', array('id' => $cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else if ($lessonid) {
        if (!$lesson = $DB->get_record('lesson', array('id' => $lessonid))) {
            print_error('invalidcoursemodule');
        }
        if (!$course = $DB->get_record('course', array('id' => $lesson->course))) {
            print_error('coursemisconf');
        }
        if (!$cm = get_coursemodule_from_instance('lesson', $lesson->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {
        print_error('invalidid', 'lesson');
    }

    return array($cm, $course, $lesson);
}

/**
 * Sets a message to be printed.  Messages are printed
 * by calling {@link lesson_print_messages()}.
 *
 * @uses $SESSION
 * @param string $message The message to be printed
 * @param string $class Class to be passed to {@link notify()}.  Usually notifyproblem or notifysuccess.
 * @param string $align Alignment of the message
 * @return boolean
 **/
function lesson_set_message($message, $class="notifyproblem", $align='center') {
    global $SESSION;

    if (empty($SESSION->lesson_messages) or !is_array($SESSION->lesson_messages)) {
        $SESSION->lesson_messages = array();
    }

    $SESSION->lesson_messages[] = array($message, $class, $align);

    return true;
}

/**
 * Print all set messages.
 *
 * See {@link lesson_set_message()} for setting messages.
 *
 * Uses {@link notify()} to print the messages.
 *
 * @uses $SESSION
 * @return boolean
 **/
function lesson_print_messages() {
    global $SESSION, $OUTPUT;

    if (empty($SESSION->lesson_messages)) {
        // No messages to print
        return true;
    }

    foreach($SESSION->lesson_messages as $message) {
        echo $OUTPUT->notification($message[0], $message[1], $message[2]);
    }

    // Reset
    unset($SESSION->lesson_messages);

    return true;
}

/**
 * Prints a lesson link that submits a form.
 *
 * If Javascript is disabled, then a regular submit button is printed
 *
 * @param string $name Name of the link or button
 * @param string $form The name of the form to be submitted
 * @param string $align Alignment of the button
 * @param string $class Class names to add to the div wrapper
 * @param string $title Title for the link (Not used if javascript is disabled)
 * @param string $id ID tag
 * @param boolean $return Return flag
 * @return mixed boolean/html
 **/
function lesson_print_submit_link($name, $form, $align = 'center', $class='standardbutton', $title = '', $id = '', $return = false) {
    if (!empty($align)) {
        $align = " style=\"text-align:$align\"";
    }
    if (!empty($id)) {
        $id = " id=\"$id\"";
    }
    if (empty($title)) {
        $title = $name;
    }

    $output = "<div class=\"lessonbutton $class\" $align>\n";
    $output .= "<input type=\"submit\" value=\"$name\" $align $id />";
    $output .= "</div>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
        return true;
    }
}

/**
 * Prints a time remaining in the following format: H:MM:SS
 *
 * @param int $starttime Time when the lesson started
 * @param int $maxtime Length of the lesson
 * @param boolean $return Return output switch
 * @return mixed boolean/string
 **/
function lesson_print_time_remaining($starttime, $maxtime, $return = false) {
    // Calculate hours, minutes and seconds
    $timeleft = $starttime + $maxtime * 60 - time();
    $hours = floor($timeleft/3600);
    $timeleft = $timeleft - ($hours * 3600);
    $minutes = floor($timeleft/60);
    $secs = $timeleft - ($minutes * 60);

    if ($minutes < 10) {
        $minutes = "0$minutes";
    }
    if ($secs < 10) {
        $secs = "0$secs";
    }
    $output   = array();
    $output[] = $hours;
    $output[] = $minutes;
    $output[] = $secs;

    $output = implode(':', $output);

    if ($return) {
        return $output;
    } else {
        echo $output;
        return true;
    }
}

/**
 * Prints the page action buttons
 *
 * Move/Edit/Preview/Delete
 *
 * @uses $CFG
 * @param int $cmid Course Module ID
 * @param object $page Page record
 * @param boolean $printmove Flag to print the move button or not
 * @param boolean $printaddpage Flag to print the add page drop-down or not
 * @param boolean $return Return flag
 * @return mixed boolean/string
 **/
function lesson_print_page_actions($cmid, $page, $printmove, $printaddpage = false, $return = false) {
    global $CFG, $OUTPUT;

    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    $actions = array();

    if (has_capability('mod/lesson:edit', $context)) {
        if ($printmove) {
            $actions[] = "<a title=\"".get_string('move')."\" href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;action=move&amp;pageid=$page->id\">
                          <img src=\"" . $OUTPUT->old_icon_url('t/move') . "\" class=\"iconsmall\" alt=\"".get_string('move')."\" /></a>\n";
        }
        $actions[] = "<a title=\"".get_string('update')."\" href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;action=editpage&amp;pageid=$page->id\">
                      <img src=\"" . $OUTPUT->old_icon_url('t/edit') . "\" class=\"iconsmall\" alt=\"".get_string('update')."\" /></a>\n";

        $actions[] = "<a title=\"".get_string('preview')."\" href=\"$CFG->wwwroot/mod/lesson/view.php?id=$cmid&amp;pageid=$page->id\">
                      <img src=\"" . $OUTPUT->old_icon_url('t/preview') . "\" class=\"iconsmall\" alt=\"".get_string('preview')."\" /></a>\n";

        $actions[] = "<a title=\"".get_string('delete')."\" href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;sesskey=".sesskey()."&amp;action=confirmdelete&amp;pageid=$page->id\">
                      <img src=\"" . $OUTPUT->old_icon_url('t/delete') . "\" class=\"iconsmall\" alt=\"".get_string('delete')."\" /></a>\n";

        if ($printaddpage) {
            // Add page drop-down
            $options = array();
            $options['addcluster&amp;sesskey='.sesskey()]      = get_string('clustertitle', 'lesson');
            $options['addendofcluster&amp;sesskey='.sesskey()] = get_string('endofclustertitle', 'lesson');
            $options['addbranchtable']                         = get_string('branchtable', 'lesson');
            $options['addendofbranch&amp;sesskey='.sesskey()]  = get_string('endofbranch', 'lesson');
            $options['addpage']                                = get_string('question', 'lesson');
            // Base url
            $common = "$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&pageid=$page->id";
            $select = html_select::make_popup_form($common, 'action', $options, "addpage_$page->id");
            $select->nothinglabel = get_string('addpage', 'lesson').'...';

            $actions[] = $OUTPUT->select($select);
        }
    }

    $actions = implode(' ', $actions);

    if ($return) {
        return $actions;
    } else {
        echo $actions;
        return false;
    }
}

/**
 * Prints the add links in expanded view or single view when editing
 *
 * @uses $CFG
 * @param int $cmid Course Module ID
 * @param int $prevpageid Previous page id
 * @param boolean $return Return flag
 * @return mixed boolean/string
 * @todo &amp;pageid does not make sense, it is prevpageid
 **/
function lesson_print_add_links($cmid, $prevpageid, $return = false) {
    global $CFG;

    $context = get_context_instance(CONTEXT_MODULE, $cmid);

    $links = '';
    if (has_capability('mod/lesson:edit', $context)) {
        $links = array();
        $links[] = "<a href=\"$CFG->wwwroot/mod/lesson/import.php?id=$cmid&amp;pageid=$prevpageid\">".
                    get_string('importquestions', 'lesson').'</a>';

        $links[] = "<a href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;sesskey=".sesskey()."&amp;action=addcluster&amp;pageid=$prevpageid\">".
                    get_string('addcluster', 'lesson').'</a>';

        if ($prevpageid != 0) {
            $links[] = "<a href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;sesskey=".sesskey()."&amp;action=addendofcluster&amp;pageid=$prevpageid\">".
                        get_string('addendofcluster', 'lesson').'</a>';
        }
        $links[] = "<a href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;action=addbranchtable&amp;pageid=$prevpageid\">".
                    get_string('addabranchtable', 'lesson').'</a>';

        if ($prevpageid != 0) {
            $links[] = "<a href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;sesskey=".sesskey()."&amp;action=addendofbranch&amp;pageid=$prevpageid\">".
                        get_string('addanendofbranch', 'lesson').'</a>';
        }

        $links[] = "<a href=\"$CFG->wwwroot/mod/lesson/lesson.php?id=$cmid&amp;action=addpage&amp;pageid=$prevpageid\">".
                    get_string('addaquestionpagehere', 'lesson').'</a>';

        $links = implode(" | \n", $links);
        $links = "\n<div class=\"addlinks\">\n$links\n</div>\n";
    }

    if ($return) {
        return $links;
    } else {
        echo $links;
        return true;
    }
}

/**
 * Returns the string for a page type
 *
 * @uses $LESSON_QUESTION_TYPE
 * @param int $qtype Page type
 * @return string
 **/
function lesson_get_qtype_name($qtype) {
    global $LESSON_QUESTION_TYPE;
    switch ($qtype) {
        case LESSON_ESSAY :
        case LESSON_SHORTANSWER :
        case LESSON_MULTICHOICE :
        case LESSON_MATCHING :
        case LESSON_TRUEFALSE :
        case LESSON_NUMERICAL :
            return $LESSON_QUESTION_TYPE[$qtype];
            break;
        case LESSON_BRANCHTABLE :
            return get_string("branchtable", "lesson");
            break;
        case LESSON_ENDOFBRANCH :
            return get_string("endofbranch", "lesson");
            break;
        case LESSON_CLUSTER :
            return get_string("clustertitle", "lesson");
            break;
        case LESSON_ENDOFCLUSTER :
            return get_string("endofclustertitle", "lesson");
            break;
        default:
            return '';
            break;
    }
}

/**
 * Returns the string for a jump name
 *
 * @param int $jumpto Jump code or page ID
 * @return string
 **/
function lesson_get_jump_name($jumpto) {
    global $DB;

    if ($jumpto == 0) {
        $jumptitle = get_string('thispage', 'lesson');
    } elseif ($jumpto == LESSON_NEXTPAGE) {
        $jumptitle = get_string('nextpage', 'lesson');
    } elseif ($jumpto == LESSON_EOL) {
        $jumptitle = get_string('endoflesson', 'lesson');
    } elseif ($jumpto == LESSON_UNSEENBRANCHPAGE) {
        $jumptitle = get_string('unseenpageinbranch', 'lesson');
    } elseif ($jumpto == LESSON_PREVIOUSPAGE) {
        $jumptitle = get_string('previouspage', 'lesson');
    } elseif ($jumpto == LESSON_RANDOMPAGE) {
        $jumptitle = get_string('randompageinbranch', 'lesson');
    } elseif ($jumpto == LESSON_RANDOMBRANCH) {
        $jumptitle = get_string('randombranch', 'lesson');
    } elseif ($jumpto == LESSON_CLUSTERJUMP) {
        $jumptitle = get_string('clusterjump', 'lesson');
    } else {
        if (!$jumptitle = $DB->get_field('lesson_pages', 'title', array('id' => $jumpto))) {
            $jumptitle = '<strong>'.get_string('notdefined', 'lesson').'</strong>';
        }
    }

    return format_string($jumptitle,true);
}

/**
 * Given some question info and some data about the the answers
 * this function parses, organises and saves the question
 *
 * This is only used when IMPORTING questions and is only called
 * from format.php
 * Lifted from mod/quiz/lib.php -
 *    1. all reference to oldanswers removed
 *    2. all reference to quiz_multichoice table removed
 *    3. In SHORTANSWER questions usecase is store in the qoption field
 *    4. In NUMERIC questions store the range as two answers
 *    5. TRUEFALSE options are ignored
 *    6. For MULTICHOICE questions with more than one answer the qoption field is true
 *
 * @param opject $question Contains question data like question, type and answers.
 * @return object Returns $result->error or $result->notice.
 **/
function lesson_save_question_options($question) {
    global $DB;

    $timenow = time();
    switch ($question->qtype) {
        case LESSON_SHORTANSWER:

            $answers = array();
            $maxfraction = -1;

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    if ($question->fraction[$key] >=0.5) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $answer->answer   = $dataanswer;
                    $answer->response = $question->feedback[$key];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }


            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
            break;

        case LESSON_NUMERICAL:   // Note similarities to SHORTANSWER

            $answers = array();
            $maxfraction = -1;


            // for each answer store the pair of min and max values even if they are the same
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->jumpto = LESSON_NEXTPAGE;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    $min = $question->answer[$key] - $question->tolerance[$key];
                    $max = $question->answer[$key] + $question->tolerance[$key];
                    $answer->answer   = $min.":".$max;
                    // $answer->answer   = $question->min[$key].":".$question->max[$key]; original line for min/max
                    $answer->response = $question->feedback[$key];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);

                    $answers[] = $answer->id;
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        break;


        case LESSON_TRUEFALSE:

            // the truth
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("true", "quiz");
            $answer->grade = $question->answer * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbacktrue)) {
                $answer->response = $question->feedbacktrue;
            }
            $true->id = $DB->insert_record("lesson_answers", $answer);

            // the lie
            $answer = new stdClass;
            $answer->lessonid   = $question->lessonid;
            $answer->pageid = $question->id;
            $answer->timecreated   = $timenow;
            $answer->answer = get_string("false", "quiz");
            $answer->grade = (1 - (int)$question->answer) * 100;
            if ($answer->grade > 50 ) {
                $answer->jumpto = LESSON_NEXTPAGE;
            }
            if (isset($question->feedbackfalse)) {
                $answer->response = $question->feedbackfalse;
            }
            $false->id = $DB->insert_record("lesson_answers", $answer);

          break;


        case LESSON_MULTICHOICE:

            $totalfraction = 0;
            $maxfraction = -1;

            $answers = array();

            // Insert all the new answers
            foreach ($question->answer as $key => $dataanswer) {
                if ($dataanswer != "") {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->grade = $question->fraction[$key] * 100;
                    // changed some defaults
                    /* Original Code
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    Replaced with:                    */
                    if ($answer->grade > 50 ) {
                        $answer->jumpto = LESSON_NEXTPAGE;
                        $answer->score = 1;
                    }
                    // end Replace
                    $answer->answer   = $dataanswer;
                    $answer->response = $question->feedback[$key];
                    $answer->id = $DB->insert_record("lesson_answers", $answer);
                    // for Sanity checks
                    if ($question->fraction[$key] > 0) {
                        $totalfraction += $question->fraction[$key];
                    }
                    if ($question->fraction[$key] > $maxfraction) {
                        $maxfraction = $question->fraction[$key];
                    }
                }
            }

            /// Perform sanity checks on fractional grades
            if ($question->single) {
                if ($maxfraction != 1) {
                    $maxfraction = $maxfraction * 100;
                    $result->notice = get_string("fractionsnomax", "quiz", $maxfraction);
                    return $result;
                }
            } else {
                $totalfraction = round($totalfraction,2);
                if ($totalfraction != 1) {
                    $totalfraction = $totalfraction * 100;
                    $result->notice = get_string("fractionsaddwrong", "quiz", $totalfraction);
                    return $result;
                }
            }
        break;

        case LESSON_MATCHING:

            $subquestions = array();

            $i = 0;
            // Insert all the new question+answer pairs
            foreach ($question->subquestions as $key => $questiontext) {
                $answertext = $question->subanswers[$key];
                if (!empty($questiontext) and !empty($answertext)) {
                    $answer = new stdClass;
                    $answer->lessonid   = $question->lessonid;
                    $answer->pageid   = $question->id;
                    $answer->timecreated   = $timenow;
                    $answer->answer = $questiontext;
                    $answer->response   = $answertext;
                    if ($i == 0) {
                        // first answer contains the correct answer jump
                        $answer->jumpto = LESSON_NEXTPAGE;
                    }
                    $subquestion->id = $DB->insert_record("lesson_answers", $answer);
                    $subquestions[] = $subquestion->id;
                    $i++;
                }
            }

            if (count($subquestions) < 3) {
                $result->notice = get_string("notenoughsubquestions", "quiz");
                return $result;
            }

            break;


        case LESSON_RANDOMSAMATCH:
            $options->question = $question->id;
            $options->choose = $question->choose;
            if ($existing = $DB->get_record("quiz_randomsamatch", array("question" => $options->question))) {
                $options->id = $existing->id;
                $DB->update_record("quiz_randomsamatch", $options);
            } else {
                $DB->insert_record("quiz_randomsamatch", $options);
            }
        break;

        case LESSON_MULTIANSWER:
            if (!$oldmultianswers = $DB->get_records("quiz_multianswers", array("question" => $question->id), "id ASC")) {
                $oldmultianswers = array();
            }

            // Insert all the new multi answers
            foreach ($question->answers as $dataanswer) {
                if ($oldmultianswer = array_shift($oldmultianswers)) {  // Existing answer, so reuse it
                    $multianswer = $oldmultianswer;
                    $multianswer->positionkey = $dataanswer->positionkey;
                    $multianswer->norm = $dataanswer->norm;
                    $multianswer->answertype = $dataanswer->answertype;

                    if (! $multianswer->answers = quiz_save_multianswer_alternatives
                            ($question->id, $dataanswer->answertype,
                             $dataanswer->alternatives, $oldmultianswer->answers))
                    {
                        $result->error = "Could not update multianswer alternatives! (id=$multianswer->id)";
                        return $result;
                    }
                    $DB->update_record("quiz_multianswers", $multianswer);
                } else {    // This is a completely new answer
                    $multianswer = new stdClass;
                    $multianswer->question = $question->id;
                    $multianswer->positionkey = $dataanswer->positionkey;
                    $multianswer->norm = $dataanswer->norm;
                    $multianswer->answertype = $dataanswer->answertype;

                    if (! $multianswer->answers = quiz_save_multianswer_alternatives
                            ($question->id, $dataanswer->answertype,
                             $dataanswer->alternatives))
                    {
                        $result->error = "Could not insert multianswer alternatives! (questionid=$question->id)";
                        return $result;
                    }
                    $DB->insert_record("quiz_multianswers", $multianswer);
                }
            }
        break;

        case LESSON_RANDOM:
        break;

        case LESSON_DESCRIPTION:
        break;

        default:
            $result->error = "Unsupported question type ($question->qtype)!";
            return $result;
        break;
    }
    return true;
}

/**
 * Determins if a jumpto value is correct or not.
 *
 * returns true if jumpto page is (logically) after the pageid page or
 * if the jumpto value is a special value.  Returns false in all other cases.
 *
 * @param int $pageid Id of the page from which you are jumping from.
 * @param int $jumpto The jumpto number.
 * @return boolean True or false after a series of tests.
 **/
function lesson_iscorrect($pageid, $jumpto) {
    global $DB;

    // first test the special values
    if (!$jumpto) {
        // same page
        return false;
    } elseif ($jumpto == LESSON_NEXTPAGE) {
        return true;
    } elseif ($jumpto == LESSON_UNSEENBRANCHPAGE) {
        return true;
    } elseif ($jumpto == LESSON_RANDOMPAGE) {
        return true;
    } elseif ($jumpto == LESSON_CLUSTERJUMP) {
        return true;
    } elseif ($jumpto == LESSON_EOL) {
        return true;
    }
    // we have to run through the pages from pageid looking for jumpid
    if ($lessonid = $DB->get_field('lesson_pages', 'lessonid', array('id' => $pageid))) {
        if ($pages = $DB->get_records('lesson_pages', array('lessonid' => $lessonid), '', 'id, nextpageid')) {
            $apageid = $pages[$pageid]->nextpageid;
            while ($apageid != 0) {
                if ($jumpto == $apageid) {
                    return true;
                }
                $apageid = $pages[$apageid]->nextpageid;
            }
        }
    }
    return false;
}

/**
 * Checks to see if a page is a branch table or is
 * a page that is enclosed by a branch table and an end of branch or end of lesson.
 * May call this function: {@link lesson_is_page_in_branch()}
 *
 * @param int $lesson Id of the lesson to which the page belongs.
 * @param int $pageid Id of the page.
 * @return boolean True or false.
 **/
function lesson_display_branch_jumps($lessonid, $pageid) {
    global $DB;

    if($pageid == 0) {
        // first page
        return false;
    }
    // get all of the lesson pages
    $params = array ("lessonid" => $lessonid);
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        // adding first page
        return false;
    }

    if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
        return true;
    }

    return lesson_is_page_in_branch($lessonpages, $pageid);
}

/**
 * Checks to see if a page is a cluster page or is
 * a page that is enclosed by a cluster page and an end of cluster or end of lesson
 * May call this function: {@link lesson_is_page_in_cluster()}
 *
 * @param int $lesson Id of the lesson to which the page belongs.
 * @param int $pageid Id of the page.
 * @return boolean True or false.
 **/
function lesson_display_cluster_jump($lesson, $pageid) {
    global $DB;

    if($pageid == 0) {
        // first page
        return false;
    }
    // get all of the lesson pages
    $params = array ("lessonid" => $lesson);
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        // adding first page
        return false;
    }

    if ($lessonpages[$pageid]->qtype == LESSON_CLUSTER) {
        return true;
    }

    return lesson_is_page_in_cluster($lessonpages, $pageid);

}

/**
 * Checks to see if a LESSON_CLUSTERJUMP or
 * a LESSON_UNSEENBRANCHPAGE is used in a lesson.
 *
 * This function is only executed when a teacher is
 * checking the navigation for a lesson.
 *
 * @param int $lesson Id of the lesson that is to be checked.
 * @return boolean True or false.
 **/
function lesson_display_teacher_warning($lesson) {
    global $DB;

    // get all of the lesson answers
    $params = array ("lessonid" => $lesson);
    if (!$lessonanswers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid", $params)) {
        // no answers, then not useing cluster or unseen
        return false;
    }
    // just check for the first one that fulfills the requirements
    foreach ($lessonanswers as $lessonanswer) {
        if ($lessonanswer->jumpto == LESSON_CLUSTERJUMP || $lessonanswer->jumpto == LESSON_UNSEENBRANCHPAGE) {
            return true;
        }
    }

    // if no answers use either of the two jumps
    return false;
}


/**
 * Interprets LESSON_CLUSTERJUMP jumpto value.
 *
 * This will select a page randomly
 * and the page selected will be inbetween a cluster page and end of cluter or end of lesson
 * and the page selected will be a page that has not been viewed already
 * and if any pages are within a branch table or end of branch then only 1 page within
 * the branch table or end of branch will be randomly selected (sub clustering).
 *
 * @param int $lessonid Id of the lesson.
 * @param int $userid Id of the user.
 * @param int $pageid Id of the current page from which we are jumping from.
 * @return int The id of the next page.
 **/
function lesson_cluster_jump($lessonid, $userid, $pageid) {
    global $DB;

    // get the number of retakes
    if (!$retakes = $DB->count_records("lesson_grades", array("lessonid"=>$lessonid, "userid"=>$userid))) {
        $retakes = 0;
    }

    // get all the lesson_attempts aka what the user has seen
    $params = array ("lessonid" => $lessonid, "userid" => $userid, "retry" => $retakes);
    if ($seen = $DB->get_records_select("lesson_attempts", "lessonid = :lessonid AND userid = :userid AND retry = :retry", $params, "timeseen DESC")) {
        foreach ($seen as $value) { // load it into an array that I can more easily use
            $seenpages[$value->pageid] = $value->pageid;
        }
    } else {
        $seenpages = array();
    }

    // get the lesson pages
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        print_error('cannotfindrecords', 'lesson');
    }
    // find the start of the cluster
    while ($pageid != 0) { // this condition should not be satisfied... should be a cluster page
        if ($lessonpages[$pageid]->qtype == LESSON_CLUSTER) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    $pageid = $lessonpages[$pageid]->nextpageid; // move down from the cluster page

    $clusterpages = array();
    while (true) {  // now load all the pages into the cluster that are not already inside of a branch table.
        if ($lessonpages[$pageid]->qtype == LESSON_ENDOFCLUSTER) {
            // store the endofcluster page's jump
            $exitjump = $DB->get_field("lesson_answers", "jumpto", array("pageid" => $pageid, "lessonid" => $lessonid));
            if ($exitjump == LESSON_NEXTPAGE) {
                $exitjump = $lessonpages[$pageid]->nextpageid;
            }
            if ($exitjump == 0) {
                $exitjump = LESSON_EOL;
            }
            break;
        } elseif (!lesson_is_page_in_branch($lessonpages, $pageid) && $lessonpages[$pageid]->qtype != LESSON_ENDOFBRANCH) {
            // load page into array when it is not in a branch table and when it is not an endofbranch
            $clusterpages[] = $lessonpages[$pageid];
        }
        if ($lessonpages[$pageid]->nextpageid == 0) {
            // shouldn't ever get here... should be using endofcluster
            $exitjump = LESSON_EOL;
            break;
        } else {
            $pageid = $lessonpages[$pageid]->nextpageid;
        }
    }

    // filter out the ones we have seen
    $unseen = array();
    foreach ($clusterpages as $clusterpage) {
        if ($clusterpage->qtype == LESSON_BRANCHTABLE) {            // if branchtable, check to see if any pages inside have been viewed
            $branchpages = lesson_pages_in_branch($lessonpages, $clusterpage->id); // get the pages in the branchtable
            $flag = true;
            foreach ($branchpages as $branchpage) {
                if (array_key_exists($branchpage->id, $seenpages)) {  // check if any of the pages have been viewed
                    $flag = false;
                }
            }
            if ($flag && count($branchpages) > 0) {
                // add branch table
                $unseen[] = $clusterpage;
            }
        } else {
            // add any other type of page that has not already been viewed
            if (!array_key_exists($clusterpage->id, $seenpages)) {
                $unseen[] = $clusterpage;
            }
        }
    }

    if (count($unseen) > 0) { // it does not contain elements, then use exitjump, otherwise find out next page/branch
        $nextpage = $unseen[rand(0, count($unseen)-1)];
    } else {
        return $exitjump; // seen all there is to see, leave the cluster
    }

    if ($nextpage->qtype == LESSON_BRANCHTABLE) { // if branch table, then pick a random page inside of it
        $branchpages = lesson_pages_in_branch($lessonpages, $nextpage->id);
        return $branchpages[rand(0, count($branchpages)-1)]->id;
    } else { // otherwise, return the page's id
        return $nextpage->id;
    }
}

/**
 * Returns pages that are within a branch table and another branch table, end of branch or end of lesson
 *
 * @param array $lessonpages An array of lesson page objects.
 * @param int $branchid The id of the branch table that we would like the containing pages for.
 * @return array An array of lesson page objects.
 **/
function lesson_pages_in_branch($lessonpages, $branchid) {
    $pageid = $lessonpages[$branchid]->nextpageid;  // move to the first page after the branch table
    $pagesinbranch = array();

    while (true) {
        if ($pageid == 0) { // EOL
            break;
        } elseif ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            break;
        } elseif ($lessonpages[$pageid]->qtype == LESSON_ENDOFBRANCH) {
            break;
        }
        $pagesinbranch[] = $lessonpages[$pageid];
        $pageid = $lessonpages[$pageid]->nextpageid;
    }

    return $pagesinbranch;
}

/**
 * Interprets the LESSON_UNSEENBRANCHPAGE jump.
 *
 * will return the pageid of a random unseen page that is within a branch
 *
 * @see lesson_pages_in_branch()
 * @param int $lesson Id of the lesson.
 * @param int $userid Id of the user.
 * @param int $pageid Id of the page from which we are jumping.
 * @return int Id of the next page.
 **/
function lesson_unseen_question_jump($lesson, $user, $pageid) {
    global $DB;

    // get the number of retakes
    if (!$retakes = $DB->count_records("lesson_grades", array("lessonid"=>$lesson, "userid"=>$user))) {
        $retakes = 0;
    }

    // get all the lesson_attempts aka what the user has seen
    $params = array ("lessonid" => $lesson, "userid" => $user, "retry" => $retakes);
    if ($viewedpages = $DB->get_records_select("lesson_attempts", "lessonid = :lessonid AND userid = :userid AND retry = :retry", $params, "timeseen DESC")) {
        foreach($viewedpages as $viewed) {
            $seenpages[] = $viewed->pageid;
        }
    } else {
        $seenpages = array();
    }

    // get the lesson pages
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        print_error('cannotfindpages', 'lesson');
    }

    if ($pageid == LESSON_UNSEENBRANCHPAGE) {  // this only happens when a student leaves in the middle of an unseen question within a branch series
        $pageid = $seenpages[0];  // just change the pageid to the last page viewed inside the branch table
    }

    // go up the pages till branch table
    while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page
        if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    $pagesinbranch = lesson_pages_in_branch($lessonpages, $pageid);

    // this foreach loop stores all the pages that are within the branch table but are not in the $seenpages array
    $unseen = array();
    foreach($pagesinbranch as $page) {
        if (!in_array($page->id, $seenpages)) {
            $unseen[] = $page->id;
        }
    }

    if(count($unseen) == 0) {
        if(isset($pagesinbranch)) {
            $temp = end($pagesinbranch);
            $nextpage = $temp->nextpageid; // they have seen all the pages in the branch, so go to EOB/next branch table/EOL
        } else {
            // there are no pages inside the branch, so return the next page
            $nextpage = $lessonpages[$pageid]->nextpageid;
        }
        if ($nextpage == 0) {
            return LESSON_EOL;
        } else {
            return $nextpage;
        }
    } else {
        return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
    }
}

/**
 * Handles the unseen branch table jump.
 *
 * @param int $lessonid Lesson id.
 * @param int $userid User id.
 * @return int Will return the page id of a branch table or end of lesson
 **/
function lesson_unseen_branch_jump($lessonid, $userid) {
    global $DB;

    if (!$retakes = $DB->count_records("lesson_grades", array("lessonid"=>$lessonid, "userid"=>$userid))) {
        $retakes = 0;
    }

    $params = array ("lessonid" => $lessonid, "userid" => $userid, "retry" => $retakes);
    if (!$seenbranches = $DB->get_records_select("lesson_branch", "lessonid = :lessonid AND userid = :userid AND retry = :retry", $params,
                "timeseen DESC")) {
        print_error('cannotfindrecords', 'lesson');
    }

    // get the lesson pages
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        print_error('cannotfindpages', 'lesson');
    }

    // this loads all the viewed branch tables into $seen untill it finds the branch table with the flag
    // which is the branch table that starts the unseenbranch function
    $seen = array();
    foreach ($seenbranches as $seenbranch) {
        if (!$seenbranch->flag) {
            $seen[$seenbranch->pageid] = $seenbranch->pageid;
        } else {
            $start = $seenbranch->pageid;
            break;
        }
    }
    // this function searches through the lesson pages to find all the branch tables
    // that follow the flagged branch table
    $pageid = $lessonpages[$start]->nextpageid; // move down from the flagged branch table
    while ($pageid != 0) {  // grab all of the branch table till eol
        if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            $branchtables[] = $lessonpages[$pageid]->id;
        }
        $pageid = $lessonpages[$pageid]->nextpageid;
    }
    $unseen = array();
    foreach ($branchtables as $branchtable) {
        // load all of the unseen branch tables into unseen
        if (!array_key_exists($branchtable, $seen)) {
            $unseen[] = $branchtable;
        }
    }
    if (count($unseen) > 0) {
        return $unseen[rand(0, count($unseen)-1)];  // returns a random page id for the next page
    } else {
        return LESSON_EOL;  // has viewed all of the branch tables
    }
}

/**
 * Handles the random jump between a branch table and end of branch or end of lesson (LESSON_RANDOMPAGE).
 *
 * @param int $lessonid Lesson id.
 * @param int $pageid The id of the page that we are jumping from (?)
 * @return int The pageid of a random page that is within a branch table
 **/
function lesson_random_question_jump($lessonid, $pageid) {
    global $DB;

    // get the lesson pages
    $params = array ("lessonid" => $lessonid);
    if (!$lessonpages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid", $params)) {
        print_error('cannotfindpages', 'lesson');
    }

    // go up the pages till branch table
    while ($pageid != 0) { // this condition should never be satisfied... only happens if there are no branch tables above this page

        if ($lessonpages[$pageid]->qtype == LESSON_BRANCHTABLE) {
            break;
        }
        $pageid = $lessonpages[$pageid]->prevpageid;
    }

    // get the pages within the branch
    $pagesinbranch = lesson_pages_in_branch($lessonpages, $pageid);

    if(count($pagesinbranch) == 0) {
        // there are no pages inside the branch, so return the next page
        return $lessonpages[$pageid]->nextpageid;
    } else {
        return $pagesinbranch[rand(0, count($pagesinbranch)-1)]->id;  // returns a random page id for the next page
    }
}

/**
 * Check to see if a page is below a branch table (logically).
 *
 * Will return true if a branch table is found logically above the page.
 * Will return false if an end of branch, cluster or the beginning
 * of the lesson is found before a branch table.
 *
 * @param array $pages An array of lesson page objects.
 * @param int $pageid Id of the page for testing.
 * @return boolean
 */
function lesson_is_page_in_branch($pages, $pageid) {
    $pageid = $pages[$pageid]->prevpageid; // move up one

    // go up the pages till branch table
    while (true) {
        if ($pageid == 0) {  // ran into the beginning of the lesson
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_ENDOFBRANCH) { // ran into the end of another branch table
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_CLUSTER) { // do not look beyond a cluster
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_BRANCHTABLE) { // hit a branch table
            return true;
        }
        $pageid = $pages[$pageid]->prevpageid;
    }

}

/**
 * Check to see if a page is below a cluster page (logically).
 *
 * Will return true if a cluster is found logically above the page.
 * Will return false if an end of cluster or the beginning
 * of the lesson is found before a cluster page.
 *
 * @param array $pages An array of lesson page objects.
 * @param int $pageid Id of the page for testing.
 * @return boolean
 */
function lesson_is_page_in_cluster($pages, $pageid) {
    $pageid = $pages[$pageid]->prevpageid; // move up one

    // go up the pages till branch table
    while (true) {
        if ($pageid == 0) {  // ran into the beginning of the lesson
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_ENDOFCLUSTER) { // ran into the end of another branch table
            return false;
        } elseif ($pages[$pageid]->qtype == LESSON_CLUSTER) { // hit a branch table
            return true;
        }
        $pageid = $pages[$pageid]->prevpageid;
    }
}

/**
 * Calculates a user's grade for a lesson.
 *
 * @param object $lesson The lesson that the user is taking.
 * @param int $retries The attempt number.
 * @param int $userid Id of the user (optinal, default current user).
 * @return object { nquestions => number of questions answered
                    attempts => number of question attempts
                    total => max points possible
                    earned => points earned by student
                    grade => calculated percentage grade
                    nmanual => number of manually graded questions
                    manualpoints => point value for manually graded questions }
 */
function lesson_grade($lesson, $ntries, $userid = 0) {
    global $USER, $DB;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    // Zero out everything
    $ncorrect     = 0;
    $nviewed      = 0;
    $score        = 0;
    $nmanual      = 0;
    $manualpoints = 0;
    $thegrade     = 0;
    $nquestions   = 0;
    $total        = 0;
    $earned       = 0;

    $params = array ("lessonid" => $lesson->id, "userid" => $userid, "retry" => $ntries);
    if ($useranswers = $DB->get_records_select("lesson_attempts",  "lessonid = :lessonid AND
            userid = :userid AND retry = :retry", $params, "timeseen")) {
        // group each try with its page
        $attemptset = array();
        foreach ($useranswers as $useranswer) {
            $attemptset[$useranswer->pageid][] = $useranswer;
        }

        // Drop all attempts that go beyond max attempts for the lesson
        foreach ($attemptset as $key => $set) {
            $attemptset[$key] = array_slice($set, 0, $lesson->maxattempts);
        }

        // get only the pages and their answers that the user answered
        list($usql, $parameters) = $DB->get_in_or_equal(array_keys($attemptset));
        $parameters["lessonid"] = $lesson->id;
        $pages = $DB->get_records_select("lesson_pages", "lessonid = :lessonid AND id $usql", $parameters);
        $answers = $DB->get_records_select("lesson_answers", "lessonid = :lessonid AND pageid $usql", $parameters);

        // Number of pages answered
        $nquestions = count($pages);

        foreach ($attemptset as $attempts) {
            if ($lesson->custom) {
                $attempt = end($attempts);
                // If essay question, handle it, otherwise add to score
                if ($pages[$attempt->pageid]->qtype == LESSON_ESSAY) {
                    $essayinfo = unserialize($attempt->useranswer);
                    $earned += $essayinfo->score;
                    $nmanual++;
                    $manualpoints += $answers[$attempt->answerid]->score;
                } else if (!empty($attempt->answerid)) {
                    $earned += $answers[$attempt->answerid]->score;
                }
            } else {
                foreach ($attempts as $attempt) {
                    $earned += $attempt->correct;
                }
                $attempt = end($attempts); // doesn't matter which one
                // If essay question, increase numbers
                if ($pages[$attempt->pageid]->qtype == LESSON_ESSAY) {
                    $nmanual++;
                    $manualpoints++;
                }
            }
            // Number of times answered
            $nviewed += count($attempts);
        }

        if ($lesson->custom) {
            $bestscores = array();
            // Find the highest possible score per page to get our total
            foreach ($answers as $answer) {
                if(!isset($bestscores[$answer->pageid])) {
                    $bestscores[$answer->pageid] = $answer->score;
                } else if ($bestscores[$answer->pageid] < $answer->score) {
                    $bestscores[$answer->pageid] = $answer->score;
                }
            }
            $total = array_sum($bestscores);
        } else {
            // Check to make sure the student has answered the minimum questions
            if ($lesson->minquestions and $nquestions < $lesson->minquestions) {
                // Nope, increase number viewed by the amount of unanswered questions
                $total =  $nviewed + ($lesson->minquestions - $nquestions);
            } else {
                $total = $nviewed;
            }
        }
    }

    if ($total) { // not zero
        $thegrade = round(100 * $earned / $total, 5);
    }

    // Build the grade information object
    $gradeinfo               = new stdClass;
    $gradeinfo->nquestions   = $nquestions;
    $gradeinfo->attempts     = $nviewed;
    $gradeinfo->total        = $total;
    $gradeinfo->earned       = $earned;
    $gradeinfo->grade        = $thegrade;
    $gradeinfo->nmanual      = $nmanual;
    $gradeinfo->manualpoints = $manualpoints;

    return $gradeinfo;
}

/**
 * Prints the on going message to the user.
 *
 * With custom grading On, displays points
 * earned out of total points possible thus far.
 * With custom grading Off, displays number of correct
 * answers out of total attempted.
 *
 * @param object $lesson The lesson that the user is taking.
 * @return void
 **/
function lesson_print_ongoing_score($lesson) {
    global $USER, $DB, $OUTPUT;

    $cm = get_coursemodule_from_instance('lesson', $lesson->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (has_capability('mod/lesson:manage', $context)) {
        echo "<p align=\"center\">".get_string('teacherongoingwarning', 'lesson').'</p>';
    } else {
        $ntries = $DB->count_records("lesson_grades", array("lessonid"=>$lesson->id, "userid"=>$USER->id));
        if (isset($USER->modattempts[$lesson->id])) {
            $ntries--;
        }
        $gradeinfo = lesson_grade($lesson, $ntries);

        $a = new stdClass;
        if ($lesson->custom) {
            $a->score = $gradeinfo->earned;
            $a->currenthigh = $gradeinfo->total;
            echo $OUTPUT->box(get_string("ongoingcustom", "lesson", $a), "generalbox boxaligncenter");
        } else {
            $a->correct = $gradeinfo->earned;
            $a->viewed = $gradeinfo->attempts;
            echo $OUTPUT->box(get_string("ongoingnormal", "lesson", $a), "generalbox boxaligncenter");
        }
    }
}

/**
 * Prints tabs for the editing and adding pages.  Each tab is a question type.
 *
 * @param array $qtypes The question types array (may not need to pass this because it is defined in this file)
 * @param string $selected Current selected tab
 * @param string $link The base href value of the link for the tab
 * @param string $onclick Javascript for the tab link
 * @return void
 */
function lesson_qtype_menu($qtypes, $selected="", $link="", $onclick="") {
    $tabs = array();
    $tabrows = array();

    foreach ($qtypes as $qtype => $qtypename) {
        $tabrows[] = new tabobject($qtype, "$link&amp;qtype=$qtype\" onclick=\"$onclick", $qtypename);
    }
    $tabs[] = $tabrows;
    print_tabs($tabs, $selected);
    echo "<input type=\"hidden\" name=\"qtype\" value=\"$selected\" /> \n";

}

/**
 * Prints out a Progress Bar which depicts a user's progress within a lesson.
 *
 * Currently works best with a linear lesson.  Clusters are counted as a single page.
 * Also, only viewed branch tables and questions that have been answered correctly count
 * toward lesson completion (or progress).  Only Students can see the Progress bar as well.
 *
 * @param object $lesson The lesson that the user is currently taking.
 * @param object $course The course that the to which the lesson belongs.
 * @return boolean The return is not significant as of yet.  Will return true/false.
 **/
function lesson_print_progress_bar($lesson, $course) {
    global $CFG, $USER, $DB, $OUTPUT;

    $cm = get_coursemodule_from_instance('lesson', $lesson->id);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // lesson setting to turn progress bar on or off
    if (!$lesson->progressbar) {
        return false;
    }

    // catch teachers
    if (has_capability('mod/lesson:manage', $context)) {
        echo $OUTPUT->notification(get_string('progressbarteacherwarning2', 'lesson'));
        return false;
    }
    if (!isset($USER->modattempts[$lesson->id])) {
        // all of the lesson pages
        if (!$pages = $DB->get_records('lesson_pages', array('lessonid' => $lesson->id))) {
            return false;
        } else {
            foreach ($pages as $page) {
                if ($page->prevpageid == 0) {
                    $pageid = $page->id;  // find the first page id
                    break;
                }
            }
        }

        // current attempt number
        if (!$ntries = $DB->count_records("lesson_grades", array("lessonid"=>$lesson->id, "userid"=>$USER->id))) {
            $ntries = 0;  // may not be necessary
        }

        $viewedpageids = array();

        // collect all of the correctly answered questions
        $params = array ("lessonid" => $lesson->id, "userid" => $USER->id, "retry" => $ntries);
        if ($viewedpages = $DB->get_records_select("lesson_attempts", "lessonid = :lessonid AND userid = :userid AND retry = :retry AND correct = 1", $params, 'timeseen DESC', 'pageid, id')) {
            $viewedpageids = array_keys($viewedpages);
        }
        // collect all of the branch tables viewed
        if ($viewedbranches = $DB->get_records_select("lesson_branch", "lessonid = :lessonid AND userid = :userid AND retry = :retry", $params, 'timeseen DESC', 'pageid, id')) {
            $viewedpageids = array_merge($viewedpageids, array_keys($viewedbranches));
        }

        // Filter out the following pages:
        //      End of Cluster
        //      End of Branch
        //      Pages found inside of Clusters
        // Do not filter out Cluster Page(s) because we count a cluster as one.
        // By keeping the cluster page, we get our 1
        $validpages = array();
        while ($pageid != 0) {
            if ($pages[$pageid]->qtype == LESSON_CLUSTER) {
                $clusterpageid = $pageid; // copy it
                $validpages[$clusterpageid] = 1;  // add the cluster page as a valid page
                $pageid = $pages[$pageid]->nextpageid;  // get next page

                // now, remove all necessary viewed paged ids from the viewedpageids array.
                while ($pages[$pageid]->qtype != LESSON_ENDOFCLUSTER and $pageid != 0) {
                    if (in_array($pageid, $viewedpageids)) {
                        unset($viewedpageids[array_search($pageid, $viewedpageids)]);  // remove it
                        // since the user did see one page in the cluster, add the cluster pageid to the viewedpageids
                        if (!in_array($clusterpageid, $viewedpageids)) {
                            $viewedpageids[] = $clusterpageid;
                        }
                    }
                    $pageid = $pages[$pageid]->nextpageid;
                }
            } elseif ($pages[$pageid]->qtype == LESSON_ENDOFCLUSTER or $pages[$pageid]->qtype == LESSON_ENDOFBRANCH) {
                // dont count these, just go to next
                $pageid = $pages[$pageid]->nextpageid;
            } else {
                // a counted page
                $validpages[$pageid] = 1;
                $pageid = $pages[$pageid]->nextpageid;
            }
        }

        // progress calculation as a percent
        $progress = round(count($viewedpageids)/count($validpages), 2) * 100;
    } else {
        $progress = 100;
    }

    // print out the Progress Bar.  Attempted to put as much as possible in the style sheets.
    echo '<div class="progress_bar" align="center">';
    echo '<table class="progress_bar_table"><tr>';
    if ($progress != 0) {  // some browsers do not repsect the 0 width.
        echo '<td style="width:'.$progress.'%;" class="progress_bar_completed">';
        echo '</td>';
    }
    echo '<td class="progress_bar_todo">';
    echo '<div class="progress_bar_token"></div>';
    echo '</td>';
    echo '</tr></table>';
    echo '</div>';

    return true;
}

/**
 * Determines if a user can view the left menu.  The determining factor
 * is whether a user has a grade greater than or equal to the lesson setting
 * of displayleftif
 *
 * @param object $lesson Lesson object of the current lesson
 * @return boolean 0 if the user cannot see, or $lesson->displayleft to keep displayleft unchanged
 **/
function lesson_displayleftif($lesson) {
    global $CFG, $USER, $DB;

    if (!empty($lesson->displayleftif)) {
        // get the current user's max grade for this lesson
        $params = array ("userid" => $USER->id, "lessonid" => $lesson->id);
        if ($maxgrade = $DB->get_record_sql('SELECT userid, MAX(grade) AS maxgrade FROM {lesson_grades} WHERE userid = :userid AND lessonid = :lessonid GROUP BY userid', $params)) {
            if ($maxgrade->maxgrade < $lesson->displayleftif) {
                return 0;  // turn off the displayleft
            }
        } else {
            return 0; // no grades
        }
    }

    // if we get to here, keep the original state of displayleft lesson setting
    return $lesson->displayleft;
}

/**
 *
 * @param $cm
 * @param $lesson
 * @param $page
 * @return unknown_type
 */
function lesson_add_pretend_blocks($page, $cm, $lesson, $timer = null) {
    $bc = lesson_menu_block_contents($cm->id, $lesson);
    if (!empty($bc)) {
        $regions = $page->blocks->get_regions();
        $firstregion = reset($regions);
        $page->blocks->add_pretend_block($bc, $firstregion);
    }

    $bc = lesson_mediafile_block_contents($cm->id, $lesson);
    if (!empty($bc)) {
        $page->blocks->add_pretend_block($bc, $page->blocks->get_default_region());
    }

    if (!empty($timer)) {
        $bc = lesson_clock_block_contents($cm->id, $lesson, $timer, $page);
        if (!empty($bc)) {
            $page->blocks->add_pretend_block($bc, $page->blocks->get_default_region());
        }
    }
}

/**
 * If there is a media file associated with this
 * lesson, return a block_contents that displays it.
 *
 * @param int $cmid Course Module ID for this lesson
 * @param object $lesson Full lesson record object
 * @return block_contents
 **/
function lesson_mediafile_block_contents($cmid, $lesson) {
    global $OUTPUT;
    if (empty($lesson->mediafile)) {
        return null;
    }

    $url      = '/mod/lesson/mediafile.php?id='.$cmid;
    $options  = 'menubar=0,location=0,left=5,top=5,scrollbars,resizable,width='. $lesson->mediawidth .',height='. $lesson->mediaheight;
    $name     = 'lessonmediafile';

    $link = html_link::make($url, get_string('mediafilepopup', 'lesson'));
    $link->add_action(new popup_action('click', $link->url, $name, $options));
    $link->title = get_string('mediafilepopup', 'lesson');
    $content .= $OUTPUT->link($link);

    $content .= $OUTPUT->help_icon(moodle_help_icon::make("mediafilestudent", get_string("mediafile", "lesson"), "lesson"));

    $bc = new block_contents();
    $bc->title = get_string('linkedmedia', 'lesson');
    $bc->set_classes('mediafile');
    $bc->content = $content;

    return $bc;
}

/**
 * If a timed lesson and not a teacher, then
 * return a block_contents containing the clock.
 *
 * @param int $cmid Course Module ID for this lesson
 * @param object $lesson Full lesson record object
 * @param object $timer Full timer record object
 * @return block_contents
 **/
function lesson_clock_block_contents($cmid, $lesson, $timer, $page) {
    // Display for timed lessons and for students only
    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    if(!$lesson->timed || has_capability('mod/lesson:manage', $context)) {
        return null;
    }

    $content = '<div class="jshidewhenenabled">';
    $content .= lesson_print_time_remaining($timer->starttime, $lesson->maxtime, true)."\n";
    $content .= '</div>';

    $clocksettings = array('starttime'=>$timer->starttime, 'servertime'=>time(),'testlength'=>($lesson->maxtime * 60));
    $content .= $page->requires->data_for_js('clocksettings', $clocksettings)->now();
    $content .= $page->requires->js('mod/lesson/timer.js')->now();
    $content .= $page->requires->js_function_call('show_clock')->now();

    $bc = new block_contents();
    $bc->title = get_string('timeremaining', 'lesson');
    $bc->set_classes('clock');
    $bc->content = $content;

    return $bc;
}

/**
 * If left menu is turned on, then this will
 * print the menu in a block
 *
 * @param int $cmid Course Module ID for this lesson
 * @param object $lesson Full lesson record object
 * @return void
 **/
function lesson_menu_block_contents($cmid, $lesson) {
    global $CFG, $DB;

    if (!$lesson->displayleft) {
        return null;
    }

    $pageid = $DB->get_field('lesson_pages', 'id', array('lessonid' => $lesson->id, 'prevpageid' => 0));
    $params = array ("lessonid" => $lesson->id);
    $pages  = $DB->get_records_select('lesson_pages', "lessonid = :lessonid", $params);
    $currentpageid = optional_param('pageid', $pageid, PARAM_INT);

    if (!$pageid || !$pages) {
        return null;
    }

    $content = '<a href="#maincontent" class="skip">'.get_string('skip', 'lesson')."</a>\n<div class=\"menuwrapper\">\n<ul>\n";

    while ($pageid != 0) {
        $page = $pages[$pageid];

        // Only process branch tables with display turned on
        if ($page->qtype == LESSON_BRANCHTABLE and $page->display) {
            if ($page->id == $currentpageid) {
                $content .= '<li class="selected">'.format_string($page->title,true)."</li>\n";
            } else {
                $content .= "<li class=\"notselected\"><a href=\"$CFG->wwwroot/mod/lesson/view.php?id=$cmid&amp;pageid=$page->id\">".format_string($page->title,true)."</a></li>\n";
            }

        }
        $pageid = $page->nextpageid;
    }
    $content .= "</ul>\n</div>\n";

    $bc = new block_contents();
    $bc->title = get_string('lessonmenu', 'lesson');
    $bc->set_classes('menu');
    $bc->content = $content;

    return $bc;
}

<?php // $Id$
/**
* Shows the question bank editing interface. To be included by other pages
*
* The script also processes a number of actions:
* Actions affecting the question pool:
* move         Moves a question to a different category
* deleteselected Deletes the selected questions from the category
* Other actions:
* cat          Chooses the category
* displayoptions Sets display options
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by Gustav Delius and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package question
*/

    // Make sure this can only be used from within Moodle scripts
    defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');
    
    require_once($CFG->dirroot.'/question/editlib.php');

    $page      = optional_param('page', -1, PARAM_INT);
    $perpage   = optional_param('perpage', -1, PARAM_INT);
    $sortorder = optional_param('sortorder', '');
    if (preg_match("/[';]/", $sortorder)) {
        error("Incorrect use of the parameter 'sortorder'");
    }

    if ($page > -1) {
        $SESSION->questionpage = $page;
    } else {
        $page = isset($SESSION->questionpage) ? $SESSION->questionpage : 0;
    }

    if ($perpage > -1) {
        $SESSION->questionperpage = $perpage;
    } else {
        $perpage = isset($SESSION->questionperpage) ? $SESSION->questionperpage : DEFAULT_QUESTIONS_PER_PAGE;
    }

    if ($sortorder) {
        $SESSION->questionsortorder = $sortorder;
    } else {
        $sortorder = isset($SESSION->questionsortorder) ? $SESSION->questionsortorder : 'qtype, name ASC';
    }

/// Now, check for commands on this page and modify variables as necessary

    if (isset($_REQUEST['move']) and confirm_sesskey()) { /// Move selected questions to new category
        $tocategoryid = required_param('category', PARAM_INT);
        if (!$tocategory = get_record('question_categories', 'id', $tocategoryid)) {
            error('Invalid category');
        }
        if (!has_capability('moodle/question:managecategory', get_context_instance(CONTEXT_COURSE, $tocategory->course))){
            error(get_string('categorynoedit', 'quiz', $tocategory->name), 'edit.php?courseid=$course->id');
        }
        foreach ($_POST as $key => $value) {    // Parse input for question ids
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                if (!set_field('question', 'category', $tocategory->id, 'id', $key)) {
                    error('Could not update category field');
                }
            }
        }
    }

    if (isset($_REQUEST['deleteselected'])) { // delete selected questions from the category

        if (isset($_REQUEST['confirm']) and confirm_sesskey()) { // teacher has already confirmed the action
            $deleteselected = required_param('deleteselected');
            if ($_REQUEST['confirm'] == md5($deleteselected)) {
                if ($questionlist = explode(',', $deleteselected)) {
                    // for each question either hide it if it is in use or delete it
                    foreach ($questionlist as $questionid) {
                        if (record_exists('quiz_question_instances', 'question', $questionid) or
                            record_exists('question_states', 'originalquestion', $questionid)) {
                            if (!set_field('question', 'hidden', 1, 'id', $questionid)) {
                               error('Was not able to hide question');
                            }
                        } else {
                            delete_question($questionid);
                        }
                    }
                }
                echo '</td></tr>';
                echo '</table>';
                echo '</div>';
                redirect("edit.php?courseid=$course->id");
            } else {
                error("Confirmation string was incorrect");
            }

        } else { // teacher still has to confirm
            // make a list of all the questions that are selected
            $rawquestions = $_REQUEST;
            $questionlist = '';  // comma separated list of ids of questions to be deleted
            $questionnames = ''; // string with names of questions separated by <br /> with
                                 // an asterix in front of those that are in use
            $inuse = false;      // set to true if at least one of the questions is in use
            foreach ($rawquestions as $key => $value) {    // Parse input for question ids
                if (substr($key, 0, 1) == "q") {
                    $key = substr($key,1);
                    $questionlist .= $key.',';
                    if (record_exists('quiz_question_instances', 'question', $key) or
                        record_exists('question_states', 'originalquestion', $key)) {
                        $questionnames .= '* ';
                        $inuse = true;
                    }
                    $questionnames .= get_field('question', 'name', 'id', $key).'<br />';
                }
            }
            if (!$questionlist) { // no questions were selected
                redirect("edit.php?courseid=$course->id");
            }
            $questionlist = rtrim($questionlist, ',');

            // Add an explanation about questions in use
            if ($inuse) {
                $questionnames .= '<br />'.get_string('questionsinuse', 'quiz');
            }
            notice_yesno(get_string("deletequestionscheck", "quiz", $questionnames),
                        "edit.php?courseid=$course->id&amp;sesskey=$USER->sesskey&amp;deleteselected=$questionlist&amp;confirm=".md5($questionlist), "edit.php?courseid=$course->id");

            echo '</td></tr>';
            echo '</table>';
            print_footer($course);
            exit;
        }
    }

    // Unhide a question
    if(isset($_REQUEST['unhide']) && confirm_sesskey()) {
        $unhide = required_param('unhide', PARAM_INT);
        if(!set_field('question', 'hidden', 0, 'id', $unhide)) {
            error("Failed to unhide the question.");
        }
        redirect("edit.php?courseid=$course->id");
    }

    if (isset($_REQUEST['cat'])) { /// coming from category selection drop-down menu
        $SESSION->questioncat = required_param('cat', PARAM_INT);
        $page = 0;
        $SESSION->questionpage = 0;
    }

    if (empty($SESSION->questioncat) or !count_records_select("question_categories", "id = '{$SESSION->questioncat}' AND (course = '{$course->id}' OR publish = '1')")) {
            $category = get_default_question_category($course->id);
        $SESSION->questioncat = $category->id;
    }

    if(($recurse = optional_param('recurse', -1, PARAM_BOOL)) != -1) {
        $SESSION->questionrecurse = $recurse;
    }
    if (!isset($SESSION->questionrecurse)) {
        $SESSION->questionrecurse = 1;
    }

    if(($showhidden = optional_param('showhidden', -1, PARAM_BOOL)) != -1) {
        $SESSION->questionshowhidden = $showhidden;
    }
    if (!isset($SESSION->questionshowhidden)) {
        $SESSION->questionshowhidden = 0;
    }

    if(($showquestiontext = optional_param('showquestiontext', -1, PARAM_BOOL)) != -1) {
        $SESSION->questionshowquestiontext = $showquestiontext;
    }
    if (!isset($SESSION->questionshowquestiontext)) {
        $SESSION->questionshowquestiontext = 0;
    }

    // starts with category selection form
    print_box_start('generalbox questionbank');
    print_heading(get_string('questionbank', 'question'), '', 2);
    question_category_form($course, $SESSION->questioncat, $SESSION->questionrecurse,
            $SESSION->questionshowhidden, $SESSION->questionshowquestiontext);
    
    // continues with list of questions
    question_list($course, $SESSION->questioncat, isset($modform->instance) ? $modform->instance : 0,
            $SESSION->questionrecurse, $page, $perpage, $SESSION->questionshowhidden, $sortorder,
            $SESSION->questionshowquestiontext);

    print_box_end();

?>

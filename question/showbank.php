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
    $perpage   = optional_param('perpage', 20, PARAM_INT);
    $sortorder = optional_param('sortorder', 'qtype, name ASC');
    if (preg_match("/[';]/", $sortorder)) {
        error("Incorrect use of the parameter 'sortorder'");
    }

    if ($page > -1) {
        $SESSION->questionpage = $page;
    } else {
        $page = isset($SESSION->questionpage) ? $SESSION->questionpage : 0;
    }

/// Now, check for commands on this page and modify variables as necessary

    if (isset($_REQUEST['move']) and confirm_sesskey()) { /// Move selected questions to new category
        if (!$tocategory = get_record('question_categories', 'id', $_REQUEST['category'])) {
            error('Invalid category');
        }
        if (!isteacheredit($tocategory->course)) {
            error(get_string('categorynoedit', 'quiz', $tocategory->name), 'edit.php');
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

        if (isset($confirm) and confirm_sesskey()) { // teacher has already confirmed the action
            if ($confirm == md5($deleteselected)) {
                if ($questionlist = explode(',', $deleteselected)) {
                    // for each question either hide it if it is in use or delete it
                    foreach ($questionlist as $questionid) {
                        if (record_exists('quiz_question_instances', 'question', $questionid) or
                            record_exists('question_states', 'originalquestion', $questionid)) {
                            if (!set_field('question', 'hidden', 1, 'id', $questionid)) {
                               error('Was not able to hide question');
                            }
                        } else {
                            delete_records("question", "id", $questionid);
                        }
                    }
                }
                redirect("edit.php");
            } else {
                error("Confirmation string was incorrect");
            }

        } else { // teacher still has to confirm
            // make a list of all the questions that are selected
            $rawquestions = $_POST;
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
                redirect('edit.php');
            }
            $questionlist = rtrim($questionlist, ',');

            // Add an explanation about questions in use
            if ($inuse) {
                $questionnames .= get_string('questionsinuse', 'quiz');
            }
            print_header_simple($streditingquestions, '',
                 "$streditingquestions");
            notice_yesno(get_string("deletequestionscheck", "quiz", $questionnames),
                        "edit.php?sesskey=$USER->sesskey&amp;deleteselected=$questionlist&amp;confirm=".md5($questionlist), "edit.php");
            print_footer($course);
            exit;
        }
    }

    if (isset($_REQUEST['cat'])) { /// coming from category selection drop-down menu
        $SESSION->questioncat = $cat;
        $page = 0;
        $SESSION->questionpage = 0;
    }

    if(isset($_REQUEST['recurse'])) {
        $SESSION->questionrecurse = optional_param('recurse', 0, PARAM_BOOL);
    }

    if(isset($_REQUEST['showhidden'])) {
        $SESSION->questionshowhidden = optional_param('showhidden', 0, PARAM_BOOL);
    }

/// all commands have been dealt with, now print the page

    if (empty($SESSION->questioncat) or !record_exists('question_categories', 'id', $SESSION->questioncat)) {
        $category = get_default_question_category($course->id);
        $SESSION->questioncat = $category->id;
    }
    if (!isset($SESSION->questionrecurse)) {
        $SESSION->questionrecurse = 1;
    }
    if (!isset($SESSION->questionshowhidden)) {
        $SESSION->questionshowhidden = false;
    }

    // starts with category selection form
    print_simple_box_start("center", "100%");
    question_category_form($course, $SESSION->questioncat, $SESSION->questionrecurse, $SESSION->questionshowhidden);
    print_simple_box_end();

    print_spacer(5,1);

    // continues with list of questions
    print_simple_box_start("center", "100%");
    question_list($course, $SESSION->questioncat, isset($modform->instance) ? $modform->instance : 0, $SESSION->questionrecurse, $page, $perpage, $SESSION->questionshowhidden, $sortorder);
    print_simple_box_end();

?>

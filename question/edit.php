<?php // $Id$
/**
* Page to edit the question bank
*
* TODO: add logging
*
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by Gustav Delius and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package questionbank
*/
    require_once("../config.php");
    require_once("editlib.php");

    require_login();

    $courseid  = required_param('courseid', PARAM_INT);

    // The optional parameter 'clean' allows us to clear module information,
    // guaranteeing a module-independent  question bank editing interface
    if (optional_param('clean', false, PARAM_BOOL)) {
        unset($SESSION->modform);
    }

    if (! $course = get_record("course", "id", $courseid)) {
        error("This course doesn't exist");
    }
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    require_login($course->id, false);
  
    $SESSION->returnurl = $FULLME;

    // Print basic page layout.
    $streditingquestions = get_string('editquestions', "quiz");

    // TODO: generalise this to any activity
    $strquizzes = get_string('modulenameplural', 'quiz');
    $streditingquestions = get_string('editquestions', "quiz");
    if (isset($SESSION->modform->instance) and $quiz = get_record('quiz', 'id', $SESSION->modform->instance)) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $course->id))
            ? update_module_button($SESSION->modform->cmid, $course->id, get_string('modulename', 'quiz'))
            : "";
        print_header_simple($streditingquestions, '',
                 "<a href=\"$CFG->wwwroot/mod/quiz/index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"$CFG->wwwroot/mod/quiz/view.php?q={$SESSION->modform->instance}\">".format_string($SESSION->modform->name).'</a>'.
                 " -> $streditingquestions",
                 "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'questions';
        $quiz = &$SESSION->modform;
        include($CFG->dirroot.'/mod/quiz/tabs.php');
    } else {
        print_header_simple($streditingquestions, '',
                 "$streditingquestions");
    
        // print tabs
        $currenttab = 'questions';
        include('tabs.php');
    }

    echo '<table class="boxaligncenter" border="0" cellpadding="2" cellspacing="0">';
    echo '<tr><td valign="top">';

    include($CFG->dirroot.'/question/showbank.php');

    echo '</td></tr>';
    echo '</table>';

    print_footer($course);
?>

<?php // $Id$
/**
* Page to edit the question bank
*
* TODO: add logging
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by Gustav Delius and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package question
*/
    require_once("../config.php");
    require_once("editlib.php");

    require_login();

    $courseid  = required_param('courseid', 0, PARAM_INT);

    if (! $course = get_record("course", "id", $courseid)) {
        error("This course doesn't exist");
    }

    require_login($course->id, false);

    if (!isteacheredit($course->id)) {
        error("You can't modify this course!");
    }

    // Print basic page layout.

    $streditingquestions = get_string('editquestions', "quiz");
    print_header_simple($streditingquestions, '',
             "$streditingquestions");
    print_heading_with_help(get_string('questions', 'quiz'), 'questionbank');
    echo '<table align="center" border="0" cellpadding="2" cellspacing="0">';
    echo '<tr><td valign="top">';

    include($CFG->dirroot.'/question/showbank.php');

    echo '</td></tr>';
    echo '</table>';

    print_footer($course);
?>

<?php // $Id$

/// This creates and handles the whole grader report interface, sans header and footer

require_once($CFG->libdir.'/tablelib.php');
include_once($CFG->libdir.'/gradelib.php');

// get the params
$courseid = required_param('id', PARAM_INT);
if (!$userid = optional_param('user', 0, PARAM_INT)) {
    // current user
    $userid = $USER->id;  
}

$tree = new grade_tree($courseid, true);
echo $tree->display_grades();

print_heading('Grader Report');



?>


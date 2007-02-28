<?php // $Id$
/**
 * The main group management user interface.
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk 
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once('../../config.php');
require_once('../lib/lib.php');
//require_once('../../course/lib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/uploadlib.php');

$error = false;
 
$courseid = required_param('id', PARAM_INTEGER);         

// Get the course information so we can print the header and check the course id
// is valid
$course = groups_get_course_info($courseid);
if (!$course) {
    $error = true;
    print_error('The course id is invalid');
}


if (!$error) {
	// Make sure that the user is a teacher with edit permission for this course
	require_login($courseid);
	if (!isteacheredit($courseid)) {
	    redirect();  
	}

	// Set the session key so we can check this later
	$sesskey = !empty($USER->id) ? $USER->sesskey : '';

	if (!empty($CFG->gdversion)) { //TODO: and $maxbytes)
		$printuploadpicture = true;
	} else {
		$printuploadpicture = false;
	}


	$maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
	$strgroups = get_string('groups');
	$strparticipants = get_string('participants');
	// Print the page and form
	print_header("$course->shortname: $strgroups", 
                 $course->fullname, 
	             "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid\">$course->shortname</a> ".
	             "-> <a href=\"$CFG->wwwroot/user/index.php?id=$courseid\">$strparticipants</a> ".
	             "-> $strgroups", "", "", true, '', user_login_string($course, $USER));

    //TODO: set to false in /course/group.php
    $usehtmleditor = false;

	require_once('form.html');

	print_footer($course);
}

?>
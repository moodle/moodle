<?php 
	require_once("../../config.php");
	require_once("lib.php");

	$next_url = "";
	$msg = '';
	
	$attemptid = required_param("attemptid");
	if (is_numeric($attemptid)) {

		if (! $attempt = get_record("hotpot_attempts", "id", $attemptid)) {
			error("Hot Potatoes attempt record $attemptid could not be accessed: ".$db->ErrorMsg());
		}

		// Check this is the right user
		if ($attempt->userid != $USER->id) {
			error("Incorrect user id");
		}

		// get hotpot, course and course_module records
		if (! $hotpot = get_record("hotpot", "id", $attempt->hotpot)) {
			error("Hot Potatoes ID is incorrect (attempt id = $attempt->id)");
		}
		if (! $course = get_record("course", "id", $hotpot->course)) {
			error("Course ID is incorrect (hotpot id = $hotpot->id)");
		}
		if (! $cm = get_coursemodule_from_instance("hotpot", $hotpot->id, $course->id)) {
			error("Course Module ID is incorrect");
		}

		$next_url = "$CFG->wwwroot/course/view.php?id=$course->id";

		// make sure this user is enrolled in this course
		require_login($course->id);

		if ($attempt->timefinish && false) {

			$msg = 'This attempt has already been submitted';

		} else {
			$time = time();
			$msg = get_string('resultssaved', 'hotpot');

			$attempt->score = isset($_POST['mark']) ? $_POST['mark'] : NULL;
			$attempt->details = isset($_POST['detail']) ? $_POST['detail'] : NULL;
			$attempt->endtime = isset($_POST['endtime']) ? strtotime($_POST['endtime']) : NULL;
			$attempt->starttime = isset($_POST['starttime']) ? strtotime($_POST['starttime']) : NULL;
			$attempt->timefinish = $time;

			// for the rare case where a quiz was "in progress" during an update from hotpot v1 to v2
			if (empty($attempt->timestart) && !empty($attempt->starttime)) {
				$attempt->timestart = $attempt->starttime;
			}

			// remove slashes added by lib/setup.php
			$attempt->details = stripslashes($attempt->details);

			// add details of this attempt 
			hotpot_add_attempt_details($attempt);

			// add slashes again, so the details can be added to the database
			$attempt->details = addslashes($attempt->details);

			if (! update_record("hotpot_attempts", $attempt)) {
				error("Could not update attempt record: ".$db->ErrorMsg(), $next_url);
			}

			// set previously unfinished attempts of this quiz by this user to "finished"
			hotpot_close_previous_attempts($hotpot->id, $USER->id, $time);

			add_to_log($course->id, "hotpot", "submit", "review.php?id=$cm->id&attempt=$attempt->id", "$hotpot->id", "$cm->id");
		}
		if ($hotpot->shownextquiz==HOTPOT_YES && is_numeric($next_cm = hotpot_get_next_cm($cm))) {
			$next_url = "$CFG->wwwroot/mod/hotpot/view.php?id=$next_cm";
		}
	}

	// redirect to the next quiz or the course page 
	redirect($next_url, $msg);


// =================
//	functions
// =================

function hotpot_get_next_cm(&$cm) {
	// gets the next module in this section of the course
	// that is the same type of module as the current module

	$next_mod = false;

	// get a list of $ids of modules in this section
	if ($ids = get_field('course_sections', 'sequence', 'id', $cm->section)) {

		$ids = explode(',', $ids);
		$found = false;
		foreach ($ids as $id) {
			if ($found && ($cm->module==get_field('course_modules', 'module', 'id', $id))) {
				$next_mod = $id;
				break;
			} else if ($cm->id==$id) {
				$found = true;
			}
		}
	}
	return $next_mod;
}
?>
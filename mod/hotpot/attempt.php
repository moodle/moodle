<?php 
	require_once("../../config.php");
	require_once("lib.php");

	$msg = '';
	$next_url = "";
	$quiz_is_finished = true;
	
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

		$time = time();
		$msg = get_string('resultssaved', 'hotpot');

		// update attempt record fields using incoming data
		$attempt->score = optional_param('mark', NULL, PARAM_INT);
		$attempt->status = optional_param('status', NULL, PARAM_INT);
		$attempt->details = optional_param('detail', NULL, PARAM_RAW);
		$attempt->endtime = optional_param('endtime', NULL, PARAM_ALPHA);
		$attempt->starttime = optional_param('starttime', NULL, PARAM_ALPHA);
		$attempt->timefinish = $time;

		if ($attempt->endtime) {
			 $attempt->endtime = strtotime($attempt->endtime);
		}
		if ($attempt->starttime) {
			 $attempt->starttime = strtotime($attempt->starttime);
		}

		// set clickreportid, (for click reporting)
		$attempt->clickreportid = $attempt->id;

		if (empty($attempt->status)) {
			if (empty($attempt->endtime)) {
				$attempt->status = HOTPOT_STATUS_INPROGRESS;
			} else {
				$attempt->status = HOTPOT_STATUS_COMPLETED;
			}
		}

		// for the rare case where a quiz was "in progress" during an update from hotpot v1 to v2
		if (empty($attempt->timestart) && !empty($attempt->starttime)) {
			$attempt->timestart = $attempt->starttime;
		}


		// check if this is the second (or subsequent) click
		if (get_field("hotpot_attempts", "timefinish", "id", $attempt->id)) {

			if ($hotpot->clickreporting==HOTPOT_YES) {
				// add attempt record for each form submission
				// records are linked via the "clickreportid" field

				// update status in previous records in this group
				set_field("hotpot_attempts", "status", $attempt->status, "clickreportid", $attempt->clickreportid);

				// add new attempt record
				unset ($attempt->id);
				$attempt->id = insert_record("hotpot_attempts", $attempt);

				if (empty($attempt->id)) {
					error("Could not insert attempt record: ".$db->ErrorMsg(), $next_url);
				}

				// add attempt details record, if necessary
				if (!empty($attempt->details)) {
					unset($details);
					$details->attempt = $attempt->id;
					$details->details = $attempt->details;
					if (! insert_record("hotpot_details", $details, false)) {
						error("Could not insert attempt details record: ".$db->ErrorMsg(), $next_url);
					}
				}

			} else {
				// remove previous responses for this attempt
				// (N.B. this does NOT remove the attempt record, just the responses)
				$ok = delete_records("hotpot_responses", "attempt", $attempt->id);
			}
		}

		// remove slashes added by lib/setup.php
		$attempt->details = stripslashes($attempt->details);

		// add details of this attempt
		hotpot_add_attempt_details($attempt);

		// add slashes again, so the details can be added to the database
		$attempt->details = addslashes($attempt->details);

		// update the attempt record
		if (! update_record("hotpot_attempts", $attempt)) {
			error("Could not update attempt record: ".$db->ErrorMsg(), $next_url);
		}

		// get previous attempt details record, if any
		$details_exist = record_exists("hotpot_details", "attempt", $attempt->id);

		// delete/update/add the attempt details record
		if (empty($attempt->details)) {
			if ($details_exist) {
				delete_records("hotpot_details", "attempt", $attempt->id);
			}
		} else {
			if ($details_exist) {
				set_field("hotpot_details", "details", $attempt->details, "attempt", $attempt->id);
			} else {
				unset($details);
				$details->attempt = $attempt->id;
				$details->details = $attempt->details;
				if (! insert_record("hotpot_details", $details)) {
					error("Could not insert attempt details record: ".$db->ErrorMsg(), $next_url);
				}
			}
		}

		if ($attempt->status==HOTPOT_STATUS_INPROGRESS) {
			$quiz_is_finished = false;

		} else { // quiz is finished

			add_to_log($course->id, "hotpot", "submit", "review.php?id=$cm->id&attempt=$attempt->id", "$hotpot->id", "$cm->id");

			if ($hotpot->shownextquiz==HOTPOT_YES && is_numeric($next_cm = hotpot_get_next_cm($cm))) {
				$next_url = "$CFG->wwwroot/mod/hotpot/view.php?id=$next_cm";
			}
		}
	}

	if ($quiz_is_finished) {
		// redirect to the next quiz or the course page 
		redirect($next_url, $msg);
	} else {
		// continue the quiz
		header("Status: 204");
		header("HTTP/1.0 204 No Response");
	}


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
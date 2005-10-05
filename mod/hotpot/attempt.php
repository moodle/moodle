<?php 
	require_once("../../config.php");
	require_once("lib.php");

	$attemptid = required_param("attemptid");

	// get attempt, hotpot, course and course_module records
	if (! $attempt = get_record("hotpot_attempts", "id", $attemptid)) {
		error("Hot Potatoes attempt record $attemptid could not be accessed: ".$db->ErrorMsg());
	}
	if ($attempt->userid != $USER->id) {
		error("User ID is incorrect");
	}
	if (! $hotpot = get_record("hotpot", "id", $attempt->hotpot)) {
		error("Hot Potatoes ID is incorrect (attempt id = $attempt->id)");
	}
	if (! $course = get_record("course", "id", $hotpot->course)) {
		error("Course ID is incorrect (hotpot id = $hotpot->id)");
	}
	if (! $cm = get_coursemodule_from_instance("hotpot", $hotpot->id, $course->id)) {
		error("Course Module ID is incorrect");
	}

	// make sure this user is enrolled in this course
	require_login($course->id);

	$next_url = "$CFG->wwwroot/course/view.php?id=$course->id";
	$time = time();

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

	if (empty($attempt->details)) {
		hotpot_set_attempt_details($attempt);
		$javascript_is_off = true;
	} else {
		$javascript_is_off = false;
	}

	if (empty($attempt->status)) {
		if (empty($attempt->endtime)) {
			$attempt->status = HOTPOT_STATUS_INPROGRESS;
		} else {
			$attempt->status = HOTPOT_STATUS_COMPLETED;
		}
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
			// remove previous responses for this attempt, if required
			// (N.B. this does NOT remove the attempt record, just the responses)
			delete_records("hotpot_responses", "attempt", $attempt->id);
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
		if ($javascript_is_off) {
			// regenerate HTML page
			define('HOTPOT_FIRST_ATTEMPT', false);
			include ("$CFG->hotpotroot/view.php");
		} else {
			// continue without reloading the page
			header("Status: 204");
			header("HTTP/1.0 204 No Response");
		}

	} else { // quiz is finished

		add_to_log($course->id, "hotpot", "submit", "review.php?id=$cm->id&attempt=$attempt->id", "$hotpot->id", "$cm->id");

		if ($hotpot->shownextquiz==HOTPOT_YES) {
			if (is_numeric($next_cm = hotpot_get_next_cm($cm))) {
				$next_url = "$CFG->wwwroot/mod/hotpot/view.php?id=$next_cm";
			}
		}

		// redirect to the next quiz or the course page 
		redirect($next_url, get_string('resultssaved', 'hotpot'));
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

		$found = false;
		$ids = explode(',', $ids);
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
function hotpot_set_attempt_details(&$attempt) {

	$attempt->details = '';
	$attempt->score = 0;
	$attempt->status = HOTPOT_STATUS_COMPLETED;

	$buttons = array('clues', 'hints', 'checks');
	$textfields = array('correct', 'wrong', 'ignored');

	$quiztype = optional_param('quiztype', '', PARAM_ALPHA);

	$q = 0;
	while (($responsefield="q{$q}") && isset($_POST[$responsefield])) {
		$responsevalue = optional_param($responsefield, '');

		// initialize $response object
		$response = NULL;
		$response->correct = '';
		$response->wrong   = '';
		$response->ignored = array();
		$response->clues  = 0;
		$response->hints  = 0;
		$response->checks = 0;
		$response->score  = 0;
		$response->weighting  = 0;

		foreach ($buttons as $button) {
			if (($field = "q{$q}_{$button}_button") && isset($_POST[$field])) {
				$value = optional_param($field, '', PARAM_RAW);
				if (!empty($value)) {
					$response->$button++;
				}
			}
		}

		// loop through possible answers to this question
		$firstcorrectvalue = '';
		$i = 0;
		while (($correctfield="q{$q}_correct_{$i}") && isset($_POST[$correctfield])) {
			$correctvalue = optional_param($correctfield, '', PARAM_RAW);

			if (empty($firstcorrectvalue)) {
				$firstcorrectvalue = $correctvalue;
			}

			if ($responsevalue==$correctvalue) {
				$response->correct = $responsevalue;
			} else {
				$response->ignored[] = $correctvalue;
			}
			$i++;
		}

		// if no correct answer was found, then this answer is wrong
		if (empty($response->correct)) {
			$response->wrong = $responsevalue;
			$attempt->status = HOTPOT_STATUS_INPROGRESS;

			// give a hint, if necessary
			if (isset($_POST['HintButton']) && $firstcorrectvalue) {

				// make sure we only come through here once
				unset($_POST['HintButton']);

				$correctlen = strlen($firstcorrectvalue);
				$responselen = strlen($responsevalue);

				$i = 0;
				while ($i<$responselen && $i<$correctlen && $responsevalue{$i}==$firstcorrectvalue{$i}) {
					$i++;
				}

				if ($i<$responselen) {
					// remove incorrect characters on the end of the response
					$responsevalue = substr($responsevalue, 0, $i);
				}
				if ($i<$correctlen) {
					// append next correct letter
					$responsevalue .= $firstcorrectvalue{$i};
				}
				$_POST[$responsefield] = $responsevalue;
				$response->hints++;
			}
		}

		// convert "ignored" array to string
		$response->ignored = implode(',', $response->ignored);

		// get clue text, if any
		if (($field="q{$q}_clue") && isset($_POST[$field])) {
			$response->clue_text = optional_param($field, '', PARAM_RAW);
		}

		$oldresponse = NULL;
		foreach ($buttons as $button) {
			$oldresponse->$button = 0;
		}			
		foreach ($textfields as $field) {
			$oldresponse->$field = array();
			$response->$field = empty($response->$field) ? array() : explode(',', $response->$field);
		}

		// get question name
		$qq = sprintf('%02d', $q); // (a padded, two-digit version of $q)
		if (($field="q{$q}_name") && isset($_POST[$field])) {
			$questionname = optional_param($field, '', PARAM_RAW);
		} else {
			$questionname = $qq;
		}

		// get previous responses to this question (if any)
		$question = get_record('hotpot_questions', 'name', $questionname, 'hotpot', $attempt->hotpot);
		if ($question) {
			$records = get_records_select('hotpot_responses', "attempt=$attempt->clickreportid AND question=$question->id");
		} else {
			$records = false;
		}
		if ($records) {
			foreach ($records as $record) {
				foreach ($buttons as $button) {
					$oldresponse->$button = max($oldresponse->$button, $record->$button);
				}
				foreach ($textfields as $field) {
					if ($record->$field) {
						$values = explode(',', hotpot_strings($record->$field));
						$oldresponse->$field = array_merge($oldresponse->$field, $values);
					}
				}
			}
		}
		// remove "correct" and "wrong" values from "ignored" values
		$response->ignored = array_diff(
			$response->ignored, 
			$response->correct, $response->wrong, $oldresponse->correct, $oldresponse->wrong
		);
		foreach ($buttons as $button) {
			$response->$button += $oldresponse->$button;
		}
		$value_has_changed = false;
		foreach ($textfields as $field) {
			$response->$field = array_merge($response->$field, $oldresponse->$field);
			$response->$field = array_unique($response->$field);
			$response->$field  = implode(',', $response->$field);

			$oldresponse->$field = array_unique($oldresponse->$field);
			$oldresponse->$field  = implode(',', $oldresponse->$field);

			if ($field=='correct' || $field=='wrong') {
				if ($response->$field<>$oldresponse->$field) {
					$value_has_changed = true;
				}
			}
		}
		if ($value_has_changed) {
			$response->checks++;
		}

		// $response now holds amalgamation of all responses so far to this question
		
		// set score and weighting
		if ($response->correct) {
			$strlen = strlen($response->correct);
			$response->score = 100*($strlen-($response->checks-1))/$strlen;
			$attempt->score += $response->score;
		}

		// encode $response fields as XML
		$vars = get_object_vars($response);
		foreach($vars as $name=>$value) {
			if (!empty($value)) {
				$attempt->details .= "<field><fieldname>{$quiztype}_q{$qq}_{$name}</fieldname><fielddata>$value</fielddata></field>";
			}
		}

		$q++;
	}

	if ($q>0) {
		$attempt->score = floor($attempt->score / $q);
	}

	if ($attempt->details) {
		$attempt->details = '<?xml version="1.0"?><hpjsresult><fields>'.$attempt->details.'</fields></hpjsresult>';
	}

	// check there are no unattempted questions
	if (($field="I_{$q}_correct_0") && isset($_POST[$field])) {
		$attempt->status = HOTPOT_STATUS_INPROGRESS;
	}	
}
?>
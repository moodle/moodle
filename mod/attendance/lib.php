<?PHP  // $Id$

/// Library of functions and constants for attendance module

// error_reporting(E_ALL);

function attendance_add_instance(&$attendance) {
     $attendance->timemodified = time();
     $attendance->dynsection = !empty($attendance->dynsection) ? 1 : 0;
     $attendance->day = make_timestamp($attendance->theyear, 
			$attendance->themonth, $attendance->theday); 
     $attendance->name=userdate($attendance->day, get_string("strftimedate"));
	 if ($attendance->notes) { 
	 	$attendance->name = $attendance->name . " - " . $attendance->notes;
	 }
	 // insert the main record first
	 return $attendance->id = insert_record("attendance", $attendance);
}


function attendance_update_instance(&$attendance) {
    $attendance->timemodified = time();
    $attendance->oldid=$attendance->id;
    $attendance->id = $attendance->instance;
    $attendance->dynsection = !empty($attendance->dynsection) ? 1 : 0;

     $attendance->day = make_timestamp($attendance->theyear, 
			$attendance->themonth, $attendance->theday); 
     $attendance->name=userdate($attendance->day, get_string("strftimedate"));
	 if ($attendance->notes) { 
	 	$attendance->name = $attendance->name . " - " . 
	 	  $attendance->notes;
	 }  
	// get the data from the attendance grid
    if ($data = data_submitted()) {      
      // Peel out all the data from variable names.
      $attrec->dayid = $attendance->id;
      foreach ($data as $key => $val) {
        $pieces = explode('_',$key);
        if ($pieces[0] == 'student') {
       	  $attrec->userid=$pieces[1];
          $attrec->hour=$pieces[2];
          $attrec->status=$val;
          // clear out any old records for the student
       	  delete_records("attendance_roll", 
            "dayid",$attrec->dayid,
            "hour", $attrec->hour, 
            "userid",$attrec->userid);
          if ($attrec->status != 0) { 
            // student is registered as absent or tardy
		    insert_record("attendance_roll",$attrec, false);
          }
      	} // if we have a piece of the student roll data
      } // foreach for all form variables
    } // if	
    return  update_record("attendance", $attendance);
}

function attendance_delete_instance($id) {
    if (! $attendance = get_record("attendance", "id", "$id")) {
        return false;
    }

    $result = true;

    /// delete all the rolls for the day
    delete_records("attendance_roll", "dayid", "$attendance->id");
    
    if (! delete_records("attendance", "id", "$attendance->id")) {
        $result = false;
    }

    return $result;
}

function attendance_user_outline($course, $user, $mod, $attendance) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
/// for attendance, this would be a list present and tardy for every hour of the day
	$tardies=count_records("attendance_roll", "dayid", $attendance->id, "userid", $user->id, "status", 1);
	$absences=count_records("attendance_roll", "dayid", $attendance->id, "userid", $user->id, "status", 2);
	
    // build longer string for tardies
	if ($tardies > 0) {
		$tardyrecs=attendance_get_records("attendance_roll", "dayid", $attendance->id, "userid", $user->id, "status", 1, "hour ASC");
		if ($tardies == 1) {
			$tardystring = "Tardy in hour " . $tardyrecs[0]->hour . ". ";
		} elseif ($tardies == $attendance->hours) { 
			$tardystring = "Tardy in all hours. (" . $attendance->hours . ") ";
		} else { 
			// build array of all tardies
			$tarr = array();
 			foreach ($tardyrecs as $tardyrec) {
 				array_push($tarr, $tardyrec->hour);
				$tardystring = $tardystring . ", " . $tardyrec->hour;
 			}
			$end=array_pop($tarr);
			$tardystring = "Tardy in hours " . implode(", ", $tarr) . " and ". $end . ". ";
		}
	} else { $tardystring = "";}
    // build longer string for absences
	if ($absences > 0) {
		$absrecs=attendance_get_records("attendance_roll", "dayid", $attendance->id, "userid", $user->id, "status", 2, "hour ASC");
		if ($absences == 1) {
			$absstring = "Absent in hour " . $absrecs[0]->hour . ".";
		} elseif ($absences == $attendance->hours) { 
			$absstring = "Absent in all hours. (" . $attendance->hours . ")";
		} else { 
			// build array of all absences
			$aarr = array();
 			foreach ($absrecs as $absrec) {
 				array_push($aarr, $absrec->hour);
 			}
 			$end=array_pop($aarr);
 			$absstring = "Absent in hours " . implode(", ", $aarr) . " and ". $end . ".";
		}
	} else { $absstring = "";}
		$return->info=$tardystring . $absstring;
    	if ($return->info == "") $return->info = "No Tardies or Absences";
    return $return;
}

function attendance_user_complete($course, $user, $mod, $attendance) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.
    // get the attendance record for that day and user
    $attrecs=attendance_get_records("attendance_roll", "dayid", $attendance->id, "userid", $user->id, "", "", "hour ASC");
    // fill an array with the absences and tardies, as those are the only records actually stored
    $grid = array();
    foreach ($attrecs as $attrec) { $grid[$attrec->hour]=$attrec->status; }
    echo "<table><tr><th>Hour:</th>\n";
    // echo out the table header
	for($j=1;$j<=$attendance->hours;$j++) {
		echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$j."</th>\n";
	}
	echo "</tr><tr><th>Status:</th>";
	for($j=1;$j<=$attendance->hours;$j++) {
      // set the attendance defaults for each student
  	      if (isset($grid[$j])) {
	        $status = (($grid[$j] == 1) ? "T" : "A");
  	      } else {$status="X";}
      echo "<td align=\"left\" nowrap class=\"generaltablecell\" style=\"border-left: 1px dotted; border-top: 1px solid;\">".$status."</td>\n";
	} /// for loop
    echo "</tr></table>\n";


    return true;
}

function attendance_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity 
/// that has occurred in attendance activities and print it out. 
/// Return true if there was output, or false is there was none.

    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

function attendance_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    global $CFG;

    return true;
}

function attendance_grades($attendanceid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.
/// NOT IMPLEMENTED AT THIS TIME - WILL DO GRADING BY ATTENDANCE STUFF IN A LATER VERSION
    $return->grades = NULL;
    $return->maxgrade = NULL;

    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other attendance functions go here.  Each of them must have a name that 
/// starts with attendance_

/**
* get a list of all students enrolled in a given course - modified version
* 
*  THIS IS JUST THE GET_COURSE_STUDENTS FUNCTION WITH THE INCLUSION OF THE 
*     STUDENT ID INTO THE RECORDSET
* if courseid = 0 then return ALL students in all courses
*
* @param	int	$courseid	the id of the course
* @param	string	$sort	a field name and ASC or DESC for a SQL 'ORDER BY' clause (optional)
* @return	array(recorset)	a list of all students in the specified course
 Returns 
*/
function attendance_get_course_students($courseid, $sort="u.lastaccess DESC") {

    global $CFG;

    return get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat,
                            u.email, u.city, u.country, u.lastaccess, u.lastlogin, u.picture, u.idnumber
                            FROM {$CFG->prefix}user u, 
                                 {$CFG->prefix}user_students s
                            WHERE s.course = '$courseid' AND s.userid = u.id AND u.deleted = '0'
                            ORDER BY $sort");
}


/**
* Find total absences based on number of tardies per absence
* 
* Given a number of tardies and absences, determine the total
* number of equivalent absences it adds up to.
*
* @param	int	$absences	the total number of absences for a span of time
* @param	int	$tardies	the total number of tardies for a span of time
* @return	float	the number of absences it adds up to - may be a decimal!
*/
function tally_overall_absences_decimal($absences, $tardies) {
    global $CFG;
	if (isset($CFG->attendance_tardies_per_absence) && ($CFG->attendance_tardies_per_absence>0)) {
	  return $absences + ($tardies/$CFG->attendance_tardies_per_absence);
	} else { return $absences; }
}

/**
* Find total absences based on number of tardies per absence and put it in a string
* 
* Given a number of tardies and absences, determine the total
* number of equivalent absences it adds up to and express it as a string with
* a possible fractional remainder
*
* @param	int	$absences	the total number of absences for a span of time
* @param	int	$tardies	the total number of tardies for a span of time
* @return	string	the number of absences it adds up to - may have a fractional component!
*/
function tally_overall_absences_fraction($absences, $tardies) {
    global $CFG;
	if (isset($CFG->attendance_tardies_per_absence) && ($CFG->attendance_tardies_per_absence>0)) {
	  $whole = floor($tardies/$CFG->attendance_tardies_per_absence);
	  $fractional=$tardies-($whole * $CFG->attendance_tardies_per_absence);
	  if ($absences + $whole > 0) {
	    return ($absences + $whole) . (($fractional > 0) ? " ". $fractional. "/". $CFG->attendance_tardies_per_absence : "");
	  } else  { 
	    return (($fractional > 0) ? $fractional. "/". $CFG->attendance_tardies_per_absence : "0");
	  }	  	 
	} else { 
	  return $absences.""; 
	}
}

/**
* get a list of records from a table with multiple criteria
* 
* This one is different from the datalib.php one (called get_records) in the sense that it 
* allows for multiple criteria to be easily supplied as parameters, but doesn't
* give the ability to specify sort, fields, or limits
*
*/
function attendance_get_records($table, $field1="", $value1="", $field2="", $value2="", $field3="", $value3="", $sort="", $fields="*", $limitfrom="", $limitnum="") {

    global $CFG;

    if ($field1) {
        $select = "WHERE $field1 = '$value1'";
        if ($field2) {
            $select .= " AND $field2 = '$value2'";
            if ($field3) {
                $select .= " AND $field3 = '$value3'";
            }
        }
    } else {
        $select = "";
    }
    
    if ($limitfrom !== "") {
        switch ($CFG->dbtype) {
            case "mysql":
                 $limit = "LIMIT $limitfrom,$limitnum";
                 break;
            case "postgres7":
                 $limit = "LIMIT $limitnum OFFSET $limitfrom";
                 break;
            default: 
                 $limit = "LIMIT $limitnum,$limitfrom";
        }
    } else {
        $limit = "";
    }

    if ($sort != "") {
        $sort = "ORDER BY $sort";
    }

    return get_records_sql("SELECT $fields FROM $CFG->prefix$table $select $sort $limit");
}

?>

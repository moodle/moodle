<?PHP  // $Id$

/// Library of functions and constants for attendance module

// error_reporting(E_ALL);

function attendance_add_module(&$mod) {
//  global $mod;
  require_once("../../course/lib.php");

  if (! $mod->instance = attendance_add_instance($mod)) {
      error("Could not add a new instance of $mod->modulename"); return 0;
  }
  // course_modules and course_sections each contain a reference 
  // to each other, so we have to update one of them twice.

  if (! $mod->coursemodule = add_course_module($mod) ) {
      error("Could not add a new course module"); return 0;
  }
  if (! $sectionid = add_mod_to_section($mod) ) {
      error("Could not add the new course module to that section"); return 0;
  }
  if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
      error("Could not update the course module with the correct section"); return 0;
  }   
  add_to_log($mod->course, "course", "add mod", 
             "../mod/$mod->modulename/view.php?id=$mod->coursemodule", 
             "$mod->modulename $mod->instance"); 
  rebuild_course_cache($mod->course);
  
}

function attendance_add_instance($attendance) {
	global $mod;
     $attendance->timemodified = time();
     $attendance->dynsection = !empty($attendance->dynsection) ? 1 : 0;
    $attendance->autoattend = !empty($attendance->autoattend) ? 1 : 0;
    $attendance->grade = !empty($attendance->grade) ? 1 : 0;
     if (empty($attendance->day)) {
       $attendance->day = make_timestamp($attendance->theyear, 
			   $attendance->themonth, $attendance->theday);
     }
     $attendance->notes = $attendance->name;
     $attendance->name=userdate($attendance->day, get_string("strftimedate"));
	 if ($attendance->notes) { 
	 	$attendance->name = $attendance->name . " - " .  $attendance->notes;
	 }  
	  $attendance->edited = 0;
if ($attendance->dynsection) { 
	if ($mod->course) {
		if (! $course = get_record("course", "id", $mod->course)) {
			error("Course is misconfigured");
		}
	}
  if ($course->format =="weeks") {
//	floor($date_relative / 604800) + 1
	  $attendance->section = floor(($attendance->day - $course->startdate)/604800) +1;
	  if($attendance->section > $course->numsections){
		  $attendance->section = 0;
	  }
	  $attendance->section = "$attendance->section";
	  $mod->section = "$attendance->section";
  }
}
	 // insert the main record first
	 return $attendance->id = insert_record("attendance", $attendance);
}


function attendance_update_instance($attendance) {
	global $mod;
    $attendance->edited = 1;
    $attendance->timemodified = time();
//    $attendance->oldid=$attendance->id;
    $attendance->id = $attendance->instance;
    $attendance->dynsection = !empty($attendance->dynsection) ? 1 : 0;
    $attendance->autoattend = !empty($attendance->autoattend) ? 1 : 0;
    $attendance->grade = !empty($attendance->grade) ? 1 : 0;

     $attendance->day = make_timestamp($attendance->theyear, 
			$attendance->themonth, $attendance->theday); 
     $attendance->notes = $attendance->name;
     $attendance->name=userdate($attendance->day, get_string("strftimedate"));
	 if ($attendance->notes) { 
	 	$attendance->name = $attendance->name . " - " .  $attendance->notes;
	 }  
  if ($attendance->dynsection) { 
	    //get info about the course
		if ($attendance->course) {
			if (! $course = get_record("course", "id", $attendance->course)) {
				error("Course is misconfigured");
			}
		}		
		//work out which section this should be in
		$attendance->section = floor(($attendance->day - $course->startdate)/604800) +1;
		if (($attendance->section > $course->numsections) || ($attendance->section < 0)){
			$attendance->section = 0;
		}
//		$attendance->section = "$attendance->section";
  }	
	// get the data from the attendance grid
  if ($data = data_submitted()) {      
    // Peel out all the data from variable names.
    $attrec->dayid = $attendance->id;
    if ($data) foreach ($data as $key => $val) {
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


	if(!update_record("attendance", $attendance)){
		error("Couldn't update record");
	}
	
	if ($attendance->dynsection) {
		//get section info
		$section = get_record("course_sections", "course", $attendance->course, "section", $attendance->section);
		
		//remove module from the current section
		if (! delete_mod_from_section($attendance->coursemodule, $mod->section)) {
	       notify("Could not delete module from existing section");
	    }
	    
	    //update course with new module location
	    if(! set_field("course_modules", "section", $section->id, "id", $attendance->coursemodule)){
	    	 notify("Could not update course module list");
	    }
	    
	    //add module to the new section
	    if (! add_mod_to_section($attendance, NULL)) {
	        notify("Could not add module to new section");
	    }
	
		rebuild_course_cache($section->course);
	}
	return true;
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
 			if ($tardyrecs) foreach ($tardyrecs as $tardyrec) {
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
 			if ($absrecs) foreach ($absrecs as $absrec) {
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
    $A = get_string("absentshort","attendance");
    $T = get_string("tardyshort","attendance");
    $P = get_string("presentshort","attendance");

    $attrecs=attendance_get_records("attendance_roll", "dayid", $attendance->id, "userid", $user->id, "", "", "hour ASC");
    // fill an array with the absences and tardies, as those are the only records actually stored
    
    $grid = array();
    if ($attrecs) { foreach ($attrecs as $attrec) { $grid[$attrec->hour]=$attrec->status; } }
    echo "<table><tr><th>Hour:</th>\n";
    // echo out the table header
  	for($j=1;$j<=$attendance->hours;$j++) {
	  	echo "<th valign=\"top\" align=\"center\" nowrap class=\"generaltableheader\">".$j."</th>\n";
	  }
	  echo "</tr><tr><th>Status:</th>";
	for($j=1;$j<=$attendance->hours;$j++) {
      // set the attendance defaults for each student
  	      if (isset($grid[$j])) {
	        $status = (($grid[$j] == 1) ? $T : $A);
  	      } else {$status=$P;}
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
   echo "Attendance: Performing automatic attendance logging\n";
// look for all attendance instances set to autoattend
	if (!$attendances = get_records("attendance", "autoattend", 1, "course ASC")) {
        return true;
  }
	$td = attendance_find_today(time());
	$tm = attendance_find_tomorrow(time());
	if ($attendances) foreach($attendances as $attendance) {
    if (($attendance->day >=$td ) && ($attendance->day < $tm)) {
    echo "Attendance: Taking attendance for $attendance->name\n";

			if(!isset($courses[$attendance->course]->students)) {
			  $courses[$attendance->course]->students = 
			    attendance_get_course_students($attendance->course, "u.lastname ASC");
			}
      if ($courses[$attendance->course]->students) {
				foreach ($courses[$attendance->course]->students as $student) {
					// first, clear out the records that may be there already
	     	  delete_records("attendance_roll", 
	          "dayid",$attendance->id,
	          "userid",$student->id);
					$wc = "userid = " . $student->id . " AND course = " . $attendance->course . 
					  " AND time >= " . $td . " AND time < " . $tm;
				  $count = get_record_select("log",$wc,"COUNT(*) as c");
					if ($count->c == "0") { // then the student hasn't done anything today, so mark him absent
						$attrec->dayid = $attendance->id;
						$attrec->userid = $student->id;
						$attrec->status = 2; // status 2 is absent
						// mark ALL hours as absent for first version
						for ($i=1;$i<=$attendance->hours;$i++) { 
	  					$attrec->hour = $i;
						  insert_record("attendance_roll",$attrec, false);
						} // for loop to mark all hours absent
					} // if student has no activity
				} // foreach student in the list
	    } // if students exist
    } // if the attendance roll is for today 
	} // for each attendance in the system
  return true;
} // function cron


function attendance_grades($attendanceid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.
    $attendance = get_record("attendance", "id", $attendanceid);
    if ($attendance->grade == "1") {
      $students = get_course_students($attendance->course);
      if ($students) {
	      foreach ($students as $student) {
	      	$rolls = attendance_get_records("attendance_roll", 
	          "dayid",$attendance->id,
	          "userid",$student->id);
	        $abs=$tar=0;
	        if ($rolls) {
	          foreach ($rolls as $roll) { 
	            if ($roll->status == 1) {$tar++;}
		        elseif ($roll->status == 2) {$abs++;}
				  } // if rolls
					  $total = $attendance->hours - attendance_tally_overall_absences_decimal($abs, $tar);
					  $percent = ($total != 0)?$total/$attendance->hours:0;
					  $return->grades[$student->id] = ($percent == 0)?0.0:$attendance->maxgrade * $percent;
					} else  { $return->grades[$student->id] = $attendance->maxgrade; }
	      } // foreach student
    } // if students
      $return->maxgrade = $attendance->maxgrade;
    } else {  // if attendance->grade == "1"
      $return = NULL;
    }// else for if attendance->grade == "1"
    return $return;
}

/**
* Returns user records for all users who have DATA in a given attendance instance
*
* This function is present only for the backup routines.  It won't return meaningful data
* for an attendance roll because it only returns records for users who have been counted as
* tardy or absent in the rolls for a single attendance instance, since these are the only
* records I store in the database - for brevity's sake of course.
*
* @param        int     $attendanceid   the id of the attendance record we're looging for student data from
* @return       (object)recordset       associative array of records containing the student records we wanted
*/
function attendance_get_participants($attendanceid) {
//Returns the users with data in one attendance
//(users with records in attendance_roll, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}attendance_roll a
                                 WHERE a.dayid = '$attendanceid' and
                                       u.id = a.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
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
function attendance_tally_overall_absences_decimal($absences, $tardies) {
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
function attendance_tally_overall_absences_fraction($absences, $tardies) {
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

/**
* Return all attendance records that are in the same section as the instance specified
* 
* This function uses course_modules, modules, and attendance tables together to determine
* first what section the specified attendance instance in the course is in, then all the
* attendance records that are in the same section, regardless of the format of the course
*
* @param	int	$instance	id of the attendance instance in course_modules
* @param	int	$courseid	the id of the course for which we're getting records
* @return	(object)recordset	associative array of records containing the attendance records we wanted 
*/
function get_attendance_for_section($instance, $courseid) {
    global $CFG;
    // first, get the section for the instance specified
    $sql = "SELECT cm.section
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md, 
                                {$CFG->prefix}attendance a 
                           WHERE cm.course = '$courseid' AND 
                                 cm.deleted = '0' AND
                                 cm.instance = a.id AND 
                                 md.name = 'attendance' AND 
                                 md.id = cm.module AND
                                 a.id = '$instance'";
    $sectarray = get_record_sql($sql);
//    echo "<pre>$sql \n</pre>";
    $section = $sectarray->section;
/*                                 
select cm.section from 
mdl_course_modules cm, mdl_modules md, mdl_attendance m
where cm.course = '7' AND cm.deleted = '0' AND cm.instance = m.id
AND md.name = 'attendance' AND md.id = cm.module AND m.id =  '119';
*/
    // then get all the attendance instances in that section
    $sql = "SELECT a.*
                           FROM {$CFG->prefix}course_modules cm, 
                                {$CFG->prefix}modules md, 
                                {$CFG->prefix}attendance a 
                           WHERE cm.course = '$courseid' AND 
                                 cm.deleted = '0' AND
                                 cm.section = '$section' AND 
                                 md.name = 'attendance' AND 
                                 md.id = cm.module AND
                                 a.id = cm.instance order by a.day ASC";
//    echo "<pre>$sql \n</pre>";
    return get_records_sql($sql);
/*
select m.* from mdl_course_modules cm, mdl_modules md, mdl_attendance m
where cm.course = '7' AND cm.deleted = '0' AND cm.section = '85' 
AND md.name = 'attendance' AND md.id = cm.module AND m.id = cm.instance;
*/
}

/**
* Return all attendance records that are in the same 7 day span as the instance specified
* 
* This function uses the course and attendance tables together to find all the attendance 
* records that are for days within the same week span as the instance specified.  The week is
* determined based NOT on calendar week, but instead on the week span as it occurs in a 
* weekly formatted course - I find this by starting with the startdate of the course and 
* then skipping ahead by weeks til I find the range that fits the instance, then I use that
* range as min and max to query the attendance table for all the other records.  Note that this
* function will work with non-weekly formatted courses, though the results won't easily 
* correlate with the course view.  But it will work regardless.
*
* @param	int	$id	the id of the attendance record we're using as a basis for the query
* @param	int	$courseid	the id of the course for which we're getting records
* @return	(object)recordset	associative array of records containing the attendance records we wanted 
*/
function get_attendance_for_week($id, $courseid) {
    global $CFG;
  if (! $attendance = get_record("attendance", "id", $id)) {
    error("Course module is incorrect");
  }
  if (! $course = get_record("course", "id", $courseid)) {
    error("Course module is incorrect");
  }
  // the offset is for weeks that don't start on Monday
  $day = $attendance->day;
  // determine the week range for the select, based on the day
  for ($maxday=$course->startdate;$day>$maxday;$maxday=$maxday+604800)
   {;}$minday = $maxday-608400;
  $sql = "SELECT * FROM {$CFG->prefix}attendance 
          WHERE course = '$courseid' AND day<$maxday AND day>=$minday order by day ASC;";
//  echo "<pre>$sql \n</pre>";
  return get_records_sql($sql);
}

/**
* Finds the beginning of the day for the date specified
* 
* This function returns the timestamp for midnight of the day specified in the timestamp
*
* @param	timestamp $d	The time to find the beginning of the day for
* @return	timestamp	midnight for that day
*/
function attendance_find_today($d) {
//  $da = getdate($d);
  $damon = gmdate("m",$d);
  $daday = gmdate("d",$d);
  $dayear = gmdate("Y",$d);
  // now return midnight of that day
  //  return mktime(0,0,0,$da["mon"], $da["mday"], $da["year"]); 
  return gmmktime(0,0,0,$damon, $daday, $dayear);
}

/**
* Finds the beginning of the day following the date specified
* 
* This function returns the timestamp for midnight of the day after the timestamp specified
*
* @param	timestamp $d	The time to find the next day of
* @return	timestamp	midnight of the next day
*/
function attendance_find_tomorrow($d) {
  // add 24 hours to the current time - to solve end of month date issues
  return attendance_find_today($d+86400);
}


?>

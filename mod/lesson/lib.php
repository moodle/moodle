<?PHP  // $Id$

/// Library of functions and constants for module lesson
/// (replace lesson with the name of your module and delete this line)


if (!defined("NEXTPAGE")) {
	define("NEXTPAGE", -1); // Next page
	}
if (!defined("EOL")) {
	define("EOL", -9); // End of Lesson
	}
if (!defined("UNDEFINED")) {
	define("UNDEFINED", -99); // undefined
	}

/*******************************************************************/
function lesson_choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
/// Given an array of value, creates a popup menu to be part of a form
/// $options["value"]["label"]
    
    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<SELECT NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <OPTION VALUE=\"$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
			// stop zero label being replaced by array index value
            // if ($label) {
            //    $output .= ">$label</OPTION>\n";
            // } else {
            //     $output .= ">$value</OPTION>\n";
			//  }
			$output .= ">$label</OPTION>\n";
            
        }
    }
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   


/*******************************************************************/
function lesson_add_instance($lesson) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $lesson->timemodified = time();

    $lesson->available = make_timestamp($lesson->availableyear, 
			$lesson->availablemonth, $lesson->availableday, $lesson->availablehour, 
			$lesson->availableminute);

    $lesson->deadline = make_timestamp($lesson->deadlineyear, 
			$lesson->deadlinemonth, $lesson->deadlineday, $lesson->deadlinehour, 
			$lesson->deadlineminute);

    return insert_record("lesson", $lesson);
}


/*******************************************************************/
function lesson_update_instance($lesson) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $lesson->timemodified = time();
    $lesson->available = make_timestamp($lesson->availableyear, 
			$lesson->availablemonth, $lesson->availableday, $lesson->availablehour, 
			$lesson->availableminute);
    $lesson->deadline = make_timestamp($lesson->deadlineyear, 
			$lesson->deadlinemonth, $lesson->deadlineday, $lesson->deadlinehour, 
			$lesson->deadlineminute);
    $lesson->id = $lesson->instance;

    return update_record("lesson", $lesson);
}


/*******************************************************************/
function lesson_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $lesson = get_record("lesson", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("lesson", "id", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_pages", "lessonid", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_answers", "lessonid", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_attempts", "lessonid", "$lesson->id")) {
        $result = false;
    }
    if (! delete_records("lesson_grades", "lessonid", "$lesson->id")) {
        $result = false;
    }

    return $result;
}

/*******************************************************************/
function lesson_user_outline($course, $user, $mod, $lesson) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $user->id",
                "grade DESC")) {
        foreach ($grades as $grade) {
            $max_grade = number_format($grade->grade * $lesson->grade / 100.0, 1);
            break;
        }
        $return->time = $grade->completed;
        if ($lesson->retake) {
            $return->info = get_string("gradeis", "lesson", $max_grade)." (".
                get_string("attempt", "lesson", count($grades)).")";
        } else {
            $return->info = get_string("gradeis", "lesson", $max_grade);
        }
    } else {
        $return->info = get_string("no")." ".get_string("attempts", "lesson");
    }
    return $return;
}

/*******************************************************************/
function lesson_user_complete($course, $user, $mod, $lesson) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

    if ($attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $user->id",
                "retry, timeseen")) {
        print_simple_box_start();
		$table->head = array (get_string("attempt", "lesson"),  get_string("numberofpagesviewed", "lesson"),
			get_string("numberofcorrectanswers", "lesson"), get_string("time"));
		$table->width = "100%";
		$table->align = array ("center", "center", "center", "center");
		$table->size = array ("*", "*", "*", "*");
		$table->cellpadding = 2;
		$table->cellspacing = 0;

        $retry = 0;
        $npages = 0;
        $ncorrect = 0;
        
		foreach ($attempts as $attempt) {
			if ($attempt->retry == $retry) {
				$npages++;
                if ($attempt->correct) {
                    $ncorrect++;
                }
                $timeseen = $attempt->timeseen;
            } else {
			    $table->data[] = array($retry + 1, $npages, $ncorrect, userdate($timeseen));
                $retry++;
                $npages = 1;
                if ($attempt->correct) {
                    $ncorrect = 1;
                } else {
                    $ncorrect = 0;
                }
			}
		}
        if ($npages) {
			    $table->data[] = array($retry + 1, $npages, $ncorrect, userdate($timeseen));
        }
		print_table($table);
	    print_simple_box_end();
        // also print grade summary
        if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $user->id",
                    "grade DESC")) {
            foreach ($grades as $grade) {
                $max_grade = number_format($grade->grade * $lesson->grade / 100.0, 1);
                break;
            }
            if ($lesson->retake) {
                echo "<p>".get_string("gradeis", "lesson", $max_grade)." (".
                    get_string("attempts", "lesson").": ".count($grades).")</p>";
            } else {
                echo "<p>".get_string("gradeis", "lesson", $max_grade)."</p>";
            }
        }
    } else {
        echo get_string("no")." ".get_string("attempts", "lesson");
    }

    
    return true;
}

/*******************************************************************/
function lesson_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity 
/// that has occurred in lesson activities and print it out. 
/// Return true if there was output, or false is there was none.

    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

/*******************************************************************/
function lesson_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    global $CFG;

    return true;
}

/*******************************************************************/
function lesson_grades($lessonid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.
    global $CFG;

	if (!$lesson = get_record("lesson", "id", $lessonid)) {
		error("Lesson record not found");
	}
    $grades = get_records_sql_menu("SELECT userid,MAX(grade) FROM {$CFG->prefix}lesson_grades WHERE
            lessonid = $lessonid GROUP BY userid");
    
    // convert grades from percentages and tidy the numbers
    if ($grades) {
        foreach ($grades as $userid => $grade) {
            $return->grades[$userid] = number_format($grade * $lesson->grade / 100.0, 1);
        }
    }
    $return->maxgrade = $lesson->grade;

    return $return;
}

/*******************************************************************/
function lesson_get_participants($lessonid) {
//Must return an array of user records (all data) who are participants
//for a given instance of lesson. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)

    global $CFG;
    
    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}lesson_attempts a
                                 WHERE a.lessonid = '$lessonid' and
                                       u.id = a.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other lesson functions go here.  Each of them must have a name that 
/// starts with lesson_

/*******************************************************************/
function lesson_iscorrect($pageid, $jumpto) {
    // returns true is jumpto page is (logically) after the pageid page, other returns false
    
    // first test the special values
    if (!$jumpto) {
        // same page
        return false;
    } elseif ($jumpto == NEXTPAGE) {
        return true;
    } elseif ($jumpto == EOL) {
        return true;
    }
    // we have to run through the pages from pageid looking for jumpid
    $apageid = get_field("lesson_pages", "nextpageid", "id", $pageid);
    while (true) {
        if ($jumpto == $apageid) {
            return true;
        }
        if ($apageid) {
            $apageid = get_field("lesson_pages", "nextpageid", "id", $apageid);
        } else {
            return false;
        }
    }
    return false; // should never be reached
}

?>

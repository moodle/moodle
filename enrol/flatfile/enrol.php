<?php
require_once("$CFG->dirroot/enrol/enrol.class.php");

// The following flags are set in the configuration
// $CFG->enrol_flatfilelocation:       where is the file we are looking for?
// $CFG->enrol_flatfilemailusers:      send email to users when they are enrolled in a course
// $CFG->enrol_flatfilemailadmin:      email the log from the cron job to the admin
// $CFG->enrol_flatfileallowinternal:  allow internal enrolment in courses




class enrolment_plugin extends enrolment_base {

    var $log;    

/// Override the base print_entry() function
function print_entry($course) {
    global $CFG;

    if (! empty($CFG->enrol_flatfileallowinternal) ) {
        parent::print_entry($course);
    } else {
        print_header();
        notice(get_string("enrolmentnointernal"), $CFG->wwwroot);
    }
}


/// Override the base check_entry() function
function check_entry($form, $course) {
    global $CFG;

    if (! empty($CFG->enrol_flatfileallowinternal) ) {
        parent::check_entry($form, $course);
    }
}



/**
* Override the base cron() function to read in a file
*
* Comma separated file assumed to have four fields per line:
*   operation, role, idnumber(user), idnumber(course)
* where:
*   operation        = add | del
*   role             = student | teacher | teacheredit
*   idnumber(user)   = idnumber in the user table NB not id
*   idnumber(course) = idnumber in the course table NB not id
*/
    function cron() {
        global $CFG;

        if (empty($CFG->enrol_flatfilelocation)) {
            $filename = "$CFG->dataroot/1/enrolments.txt";  // Default location
        } else {
            $filename = $CFG->enrol_flatfilelocation;
        }

        if ( file_exists($filename) ) {
            
            $this->log  = userdate(time()) . "\n";
            $this->log .= "Flatfile enrol cron found file: $filename\n\n";

            if (($fh = fopen($filename, "r")) != false) {
            
                $line = 0;
                while (!feof($fh)) {
                
                    $line++;
                    $fields = explode( ",", str_replace( "\r", "", fgets($fh) ) );


                /// If a line is incorrectly formatted ie does not have 4 comma separated fields then ignore it
                    if ( count($fields) != 4) {
                        if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
                            $this->log .= "$line: Line incorrectly formatted - ignoring\n";
                        }
                        continue;
                    }
                    

                    $fields[0] = trim(strtolower($fields[0]));
                    $fields[1] = trim(strtolower($fields[1]));
                    $fields[2] = trim($fields[2]);
                    $fields[3] = trim($fields[3]);


                    $this->log .= "$line: $fields[0] $fields[1] $fields[2] $fields[3]: ";



                /// check correct formatting of operation field
                    if ($fields[0] != "add" and $fields[0] != "del") {
                        $this->log .= "Unknown operation in field 1 - ignoring line\n";
                        continue;
                    }


                /// check correct formatting of role field
                    if ($fields[1] != "student" and $fields[1] != "teacher" and $fields[1] != "teacheredit") {
                        $this->log .= "Unknown role in field2 - ignoring line\n";
                        continue;
                    }


                    if (! $user = get_record("user", "idnumber", $fields[2]) ) {
                        $this->log .= "Unknown user idnumber in field 3 - ignoring line\n";
                        continue;
                    }


                    if (! $course = get_record("course", "idnumber", $fields[3]) ) {
                        $this->log .= "Unknown course idnumber in field 4 - ignoring line\n";
                        continue;
                    }


                    unset($elog);
                    switch ($fields[1]) {
                        case "student":
                            if ($fields[0] == "add") {
                                if (! enrol_student($user->id, $course->id)) {
                                    $elog = "Error enrolling in course\n";
                                }
                            } else {
                                if (! unenrol_student($user->id, $course->id)) {
                                    $elog = "Error unenrolling from course\n";
                                }
                            }
                            break;

                        case "teacher":
                            if ($fields[0] == "add") {
                                if (! add_teacher($user->id, $course->id, 0)) {
                                    $elog = "Error adding teacher to course\n";
                                }
                            } else {
                                if (! remove_teacher($user->id, $course->id)) {
                                    $elog = "Error removing teacher from course\n";
                                }
                            }
                            break;

                        case "teacheredit":
                            if ($fields[0] == "add") {
                                if (! add_teacher($user->id, $course->id, 1)) {
                                    $elog = "Error adding teacher to course\n";
                                }
                            } else {
                                if (! remove_teacher($user->id, $course->id)) {
                                    $elog = "Error removing teacher from course\n";
                                }
                            }
                            break;

                        default: // should never get here as checks made above for correct values of $fields[1]

                    } // end of switch



                    if ( (! empty($CFG->enrol_flatfilemailusers)) and empty($elog) and ($fields[0] == "add") ) {
                        $subject = get_string("welcometocourse", "", $course->fullname);
                        $a->coursename = $course->fullname;
                        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";
                        $message = get_string("welcometocoursetext", "", $a);

                        if ($fields[1] == "student") {
                            if (! $teacher = get_teacher($course->id)) {
                                $teacher = get_admin();
                            }
                        } else {
                            $teacher = get_admin();
                        }

                        email_to_user($user, $teacher, $subject, $message);
                    }



                    if (empty($elog)) {
                        $elog = "OK\n";
                    }
                    $this->log .= $elog;

                } // end of while loop

            fclose($fh);
            } // end of if(file_open)

            if(! @unlink($filename)) {
                email_to_user(get_admin(), get_admin(), get_string("filelockedmailsubject", "enrol_flatfile"), get_string("filelockedmail", "enrol_flatfile", $filename));
                $this->log .= "Error unlinking file $filename\n";
            }

            if (! empty($CFG->enrol_flatfilemailadmin)) {
                email_to_user(get_admin(), get_admin(), "Flatfile Enrolment Log", $this->log);
            }

        } // end of if(file_exists)

    } // end of function

} // end of class

?>

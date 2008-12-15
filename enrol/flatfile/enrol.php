<?php
// The following flags are set in the configuration
// $CFG->enrol_flatfilelocation:       where is the file we are looking for?
// $CFG->enrol_emailstudents:          send email to students when they are enrolled in a course
// $CFG->enrol_emailteachers:          send email to teachers when they are enrolled in a course
// $CFG->enrol_emailadmins:            email the log from the cron job to the admin



class enrolment_plugin_flatfile {

    var $log;    

/// Override the base config_form() function
function config_form($frm) {
    global $CFG;

    $vars = array('enrol_flatfilelocation', 'enrol_mailstudents', 'enrol_mailteachers', 'enrol_mailadmins');
    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        } 
    }

    $roles = get_records('role', '', '', '', 'id, name, shortname');
    $ffconfig = get_config('enrol_flatfile');

    $frm->enrol_flatfilemapping = array();
    foreach($roles as $id => $record) {

        $frm->enrol_flatfilemapping[$id] = array(
            $record->name,
            isset($ffconfig->{"map_{$record->shortname}"}) ? $ffconfig->{"map_{$record->shortname}"} : $record->shortname
        );
    }
    
    include ("$CFG->dirroot/enrol/flatfile/config.html");    
}


/// Override the base process_config() function
function process_config($config) {

    if (!isset($config->enrol_flatfilelocation)) {
        $config->enrol_flatfilelocation = '';
    }
    set_config('enrol_flatfilelocation', $config->enrol_flatfilelocation);

    if (!isset($config->enrol_mailstudents)) {
        $config->enrol_mailstudents = '';
    }
    set_config('enrol_mailstudents', $config->enrol_mailstudents);

    if (!isset($config->enrol_mailteachers)) {
        $config->enrol_mailteachers = '';
    }
    set_config('enrol_mailteachers', $config->enrol_mailteachers);

    if (!isset($config->enrol_mailadmins)) {
        $config->enrol_mailadmins = '';
    }
    set_config('enrol_mailadmins', $config->enrol_mailadmins);

    foreach(get_records('role', '', '', '', 'id, shortname') as $id => $role) {
        if (isset($config->{"enrol_flatfilemapping_{$id}"})) {
            set_config('map_'.$role->shortname, $config->{"enrol_flatfilemapping_{$id}"}, 'enrol_flatfile');
        } else {
            set_config('map_'.$role->shortname, $role->shortname, 'enrol_flatfile');
        }
    }

    return true;

}

/// Override the get_access_icons() function
function get_access_icons($course) {
}

/**
* Override the base cron() function to read in a file
*
* Comma separated file assumed to have four or six fields per line:
*   operation, role, idnumber(user), idnumber(course) [, starttime, endtime]
* where:
*   operation        = add | del
*   role             = student | teacher | teacheredit
*   idnumber(user)   = idnumber in the user table NB not id
*   idnumber(course) = idnumber in the course table NB not id
*   starttime        = start time (in seconds since epoch) - optional
*   endtime          = end time (in seconds since epoch) - optional
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

                list($roles, $rolemap) = $this->get_roles();
                
                $line = 0;
                while (!feof($fh)) {
                
                    $line++;
                    $fields = explode( ",", str_replace( "\r", "", fgets($fh) ) );


                /// If a line is incorrectly formatted ie does not have 4 comma separated fields then ignore it
                    if (count($fields) != 4 and count($fields) !=6) {
                        if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
                            $this->log .= "$line: Line incorrectly formatted - ignoring\n";
                        }
                        continue;
                    }
                    

                    $fields[0] = trim(strtolower($fields[0]));
                    $fields[1] = trim(strtolower($fields[1]));
                    $fields[2] = trim($fields[2]);
                    $fields[3] = trim($fields[3]);
                    
                    $this->log .= "$line: $fields[0] $fields[1] $fields[2] $fields[3] ";
                    
                    if (!empty($fields[5])) {
                        $fields[4] = (int)trim($fields[4]);
                        $fields[5] = (int)trim($fields[5]);
                        $this->log .= "$fields[4] $fields[5]";
                    } else {
                        $fields[4] = 0;
                        $fields[5] = 0;
                    } 

                    $this->log .= ":";



                /// check correct formatting of operation field
                    if ($fields[0] != "add" and $fields[0] != "del") {
                        $this->log .= "Unknown operation in field 1 - ignoring line\n";
                        continue;
                    }


                /// check correct formatting of role field
                    if (!isset($rolemap[$fields[1]]) && !isset($roles[$fields[1]])) {
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

                    if ($fields[4] > $fields[5]) {
                        $this->log .= "Start time was later than end time - ignoring line\n";
                        continue;
                    }


                    unset($elog);

                    // Either field[1] is a name that appears in the mapping,
                    // or it's an actual short name. It has to be one or the
                    // other, or we don't get to this point.
                    $roleid = isset($rolemap[$fields[1]]) ? $roles[$rolemap[$fields[1]]] : $roles[$fields[1]];

                    // Create/resurrect a context object
                    $context = get_context_instance(CONTEXT_COURSE, $course->id);
                    
                    if ($fields[0] == 'add') {
                        role_assign($roleid, $user->id, null, $context->id, $fields[4], $fields[5], 0, 'flatfile');
                    } else {
                        role_unassign($roleid, $user->id, null, $context->id);
                    }
                    
                    /*
                    switch ($fields[1]) {
                        case "student":
                            if ($fields[0] == "add") {
                                if (! enrol_student($user->id, $course->id, $fields[4], $fields[5], 'flatfile')) {
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
                                if (! add_teacher($user->id, $course->id, 0, '', $fields[4], $fields[5], 'flatfile')) {
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
                                if (! add_teacher($user->id, $course->id, 1, '', $fields[4], $fields[5], 'flatfile')) {
                                    $elog = "Error adding teacher to course\n";
                                }
                            } else {
                                if (! remove_teacher($user->id, $course->id)) {
                                    $elog = "Error removing teacher from course\n";
                                }
                            }
                            break;

                        default: // should never get here as checks made above for correct values of $fields[1]

                    } // end of switch*/



                    if ( empty($elog) and ($fields[0] == "add") ) {
   
                        if ($fields[1] == "student") {

                            if ($teachers = get_users_by_capability($context, 'moodle/course:update', 'u.*,ra.hidden', 'ra.sortorder ASC')) {
                                foreach ($teachers as $u) {
                                    if (!$u->hidden || has_capability('moodle/role:viewhiddenassigns', $context)) {
                                        $teacher = $u;
                                        break;
                                    }
                                }
                            }

                            if (!isset($teacher)) {
                                $teacher = get_admin();
                            }
                        } else {
                            $teacher = get_admin();
                        }

                        
                        if (!empty($CFG->enrol_mailstudents)) {
                            $a->coursename = "$course->fullname";
                            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id";
                            email_to_user($user, $teacher, get_string("enrolmentnew", '', $course->shortname), 
                                          get_string('welcometocoursetext', '', $a));
                        }

                        if (!empty($CFG->enrol_mailteachers) && $teachers) {

                            foreach($teachers as $teacher) {
                            
                                if (!$u->hidden || has_capability('moodle/role:viewhiddenassigns', $context)) {
                                    $a->course = "$course->fullname";
                                    $a->user = fullname($user);
                                    email_to_user($teacher, $user, get_string("enrolmentnew", '', $course->shortname), 
                                                  get_string('enrolmentnewuser', '', $a));
                                }
                            }
                        }
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

            if (!empty($CFG->enrol_mailadmins)) {
                email_to_user(get_admin(), get_admin(), "Flatfile Enrolment Log", $this->log);
            }

        } // end of if(file_exists)

    } // end of function

    /**
     * Returns a pair of arrays.  The first is the set of roleids, indexed by
     * their shortnames.  The second is the set of shortnames that have
     * mappings, indexed by those mappings.
     *
     * @return array ($roles, $rolemap)
     */
    function get_roles() {
        // Get a list of all the roles in the database, indexed by their short names.
        $roles = get_records('role', '', '', '', 'shortname, id');
        array_walk($roles, create_function('&$value', '$value = $value->id;'));

        // Get any name mappings. These will be of the form 'map_shortname' => 'flatfilename'.
        $ffconfig = get_config('enrol_flatfile');
        $rolemap = array();
        foreach($ffconfig as $name => $value) {
            if (strpos($name, 'map_') === 0 && isset($roles[$key = substr($name, 4)])) {
                $rolemap[$value] = $key;
            }
        }

        return array($roles, $rolemap);
    }

} // end of class

?>

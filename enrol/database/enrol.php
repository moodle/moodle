<?php  // $Id$

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin extends enrolment_base {

    var $log;    

/// Leave get_teacher_courses() function unchanged for the time being


/// Leave cron() function unchanged



/// Overide the base get_student_courses() function
function get_student_courses(&$user) {
    global $CFG;

    parent::get_student_courses($user);  /// Start with the list of known enrolments
                                         /// If the database fails we can at least use this

    // This is a hack to workaround what seems to be a bug in ADOdb with accessing 
    // two databases of the same kind ... it seems to get confused when trying to access
    // the first database again, after having accessed the second.
    // The following hack will make the database explicit which keeps it happy

    if (strpos($CFG->prefix, $CFG->dbname) === false) {
        $oldprefix = $CFG->prefix;
        $CFG->prefix = "$CFG->dbname.$CFG->prefix";
    }

    // Try to connect to the external database

    $enroldb = &ADONewConnection($CFG->enrol_dbtype);

    if ($enroldb->PConnect($CFG->enrol_dbhost,$CFG->enrol_dbuser,$CFG->enrol_dbpass,$CFG->enrol_dbname)) {

        $courselist = array();      /// Initialise new array
        $newstudent = array();

        /// Get the authoritative list of enrolments from the database

        $useridnumber = $user->{$CFG->enrol_localuserfield};   


        if ($rs = $enroldb->Execute("SELECT $CFG->enrol_remotecoursefield 
                                       FROM $CFG->enrol_dbtable 
                                      WHERE $CFG->enrol_remoteuserfield = '$useridnumber' ")) {

            if ($rs->RecordCount() > 0) {
                while (!$rs->EOF) {
                    $courselist[] = $rs->fields[0];
                    $rs->MoveNext();
                }

                foreach ($courselist as $coursefield) {
                    if ($course = get_record('course', $CFG->enrol_localcoursefield, $coursefield)) {
                        $newstudent[$course->id] = true;             /// Add it to new list
                        if (isset($user->student[$course->id])) {   /// We have it already
                            unset($user->student[$course->id]);       /// Remove from old list
                        } else {
                            enrol_student($user->id, $course->id, 0, 0, 'database');   /// Enrol the student
                        }
                    }
                }
            }

            if (!empty($user->student)) {    /// We have some courses left that we need to unenrol from
                foreach ($user->student as $courseid => $value) {

                    // unenrol only if it's a record pulled from external db
                    if (get_record_select('user_students', 'userid', $user->id, 'courseid', $courseid, 'enrol', 'database')) {
                        unenrol_student($user->id, $courseid);       /// Unenrol the student
                        unset($user->student[$course->id]);           /// Remove from old list
                    }
                }
            }

            $user->student = $newstudent;    /// Overwrite the array with the new one
        }
        
        $enroldb->Close();
    }

    if (!empty($oldprefix)) {
        $CFG->prefix = $oldprefix;           // Restore it just in case
    }
}


/// Override the base print_entry() function
function print_entry($course) {
    global $CFG;

    if (! empty($CFG->enrol_allowinternal) ) {
        parent::print_entry($course);
    } else {
        print_header();
        notice(get_string("enrolmentnointernal"), $CFG->wwwroot);
    }
}


/// Override the base check_entry() function
function check_entry($form, $course) {
    global $CFG;

    if (! empty($CFG->enrol_allowinternal) ) {
        parent::check_entry($form, $course);
    }
}


/// Overide the get_access_icons() function
function get_access_icons($course) {
}


/// Overide the base config_form() function
function config_form($frm) {
    global $CFG;

    $vars = array('enrol_dbhost', 'enrol_dbuser', 'enrol_dbpass', 
                  'enrol_dbname', 'enrol_dbtable', 
                  'enrol_localcoursefield', 'enrol_localuserfield', 
                  'enrol_remotecoursefield', 'enrol_remoteuserfield',
                  'enrol_allowinternal');
    foreach ($vars as $var) {
        if (!isset($frm->$var)) {
            $frm->$var = '';
        } 
    }
    include("$CFG->dirroot/enrol/database/config.html");
}

/// Override the base process_config() function
function process_config($config) {

    if (!isset($config->enrol_dbtype)) {
        $config->enrol_dbtype = 'mysql';
    }
    set_config('enrol_dbtype', $config->enrol_dbtype);

    if (!isset($config->enrol_dbhost)) {
        $config->enrol_dbhost = '';
    }
    set_config('enrol_dbhost', $config->enrol_dbhost);

    if (!isset($config->enrol_dbuser)) {
        $config->enrol_dbuser = '';
    }
    set_config('enrol_dbuser', $config->enrol_dbuser);

    if (!isset($config->enrol_dbpass)) {
        $config->enrol_dbpass = '';
    }
    set_config('enrol_dbpass', $config->enrol_dbpass);

    if (!isset($config->enrol_dbname)) {
        $config->enrol_dbname = '';
    }
    set_config('enrol_dbname', $config->enrol_dbname);

    if (!isset($config->enrol_dbtable)) {
        $config->enrol_dbtable = '';
    }
    set_config('enrol_dbtable', $config->enrol_dbtable);

    if (!isset($config->enrol_localcoursefield)) {
        $config->enrol_localcoursefield = '';
    }
    set_config('enrol_localcoursefield', $config->enrol_localcoursefield);

    if (!isset($config->enrol_localuserfield)) {
        $config->enrol_localuserfield = '';
    }
    set_config('enrol_localuserfield', $config->enrol_localuserfield);

    if (!isset($config->enrol_remotecoursefield)) {
        $config->enrol_remotecoursefield = '';
    }
    set_config('enrol_remotecoursefield', $config->enrol_remotecoursefield);

    if (!isset($config->enrol_remoteuserfield)) {
        $config->enrol_remoteuserfield = '';
    }
    set_config('enrol_remoteuserfield', $config->enrol_remoteuserfield);

    if (!isset($config->enrol_allowinternal)) {
        $config->enrol_allowinternal = '';
    }
    set_config('enrol_allowinternal', $config->enrol_allowinternal);
    
    return true;

}


} // end of class

?>

<?php  // $Id$

require_once($CFG->dirroot.'/enrol/enrol.class.php');

class enrolment_plugin_database {

    var $log;

/*
 * For the given user, let's go out and look in an external database
 * for an authoritative list of enrolments, and then adjust the
 * local Moodle assignments to match.
 */
function setup_enrolments(&$user) {
    global $CFG;

    // NOTE: if $this->enrol_connect() succeeds you MUST remember to call
    // $this->enrol_disconnect() as it is doing some nasty vodoo with $CFG->prefix
    $enroldb = $this->enrol_connect();
    if (!$enroldb) {
        error_log('[ENROL_DB] Could not make a connection');
        return;
    }

    // If we are expecting to get role information from our remote db, then
    // we execute the below code for every role type.  Otherwise we just
    // execute it once with null (hence the dummy array).
    $roles = !empty($CFG->enrol_db_remoterolefield) && !empty($CFG->enrol_db_localrolefield)
        ? get_records('role')
        : array(null);

    //error_log('[ENROL_DB] found ' . count($roles) . ' roles:');

    foreach($roles as $role) {

        //error_log('[ENROL_DB] setting up enrolments for '.$role->shortname);

        /// Get the authoritative list of enrolments from the external database table
        /// We're using the ADOdb functions natively here and not our datalib functions
        /// because we didn't want to mess with the $db global

        $useridfield = $enroldb->quote($user->{$CFG->enrol_localuserfield});

        list($have_role, $remote_role_name, $remote_role_value) = $this->role_fields($enroldb, $role);

        /// Check if a particular role has been forced by the plugin site-wide
        /// (if we aren't doing a role-based select)
        if (!$have_role && $CFG->enrol_db_defaultcourseroleid) {
            $role = get_record('role', 'id', $CFG->enrol_db_defaultcourseroleid);
        }

        /// Whether to fetch the default role on a per-course basis (below) or not.
        $use_default_role = !$role;

        /*
        if ($have_role) {
            error_log('[ENROL_DB] Doing role-specific select from db for role: '.$role->shortname);
        } elseif ($use_default_role) {
            error_log('[ENROL_DB] Using course default for roles - assuming that database lists defaults');
        } else {
            error_log('[ENROL_DB] Using config default for roles: '.$role->shortname);
        }*/

        if ($rs = $enroldb->Execute("SELECT {$CFG->enrol_remotecoursefield} as enrolremotecoursefield
                                       FROM {$CFG->enrol_dbtable}
                                      WHERE {$CFG->enrol_remoteuserfield} = " . $useridfield .
                                        (isset($remote_role_name, $remote_role_value) ? ' AND '.$remote_role_name.' = '.$remote_role_value : ''))) {

            // We'll use this to see what to add and remove
            $existing = $role
                ? get_records_sql("
                    SELECT * FROM {$CFG->prefix}role_assignments
                    WHERE userid = {$user->id}
                     AND roleid = {$role->id}")
                : get_records('role_assignments', 'userid', $user->id);

            if (!$existing) {
                $existing = array();
            }


            if (!$rs->EOF) {   // We found some courses

                //$count = 0;
                $courselist = array();
                while ($fields_obj = rs_fetch_next_record($rs)) {         // Make a nice little array of courses to process
                    $fields_obj = (object)array_change_key_case((array)$fields_obj , CASE_LOWER);
                    $courselist[] = $fields_obj->enrolremotecoursefield;
                    //$count++;
                }
                rs_close($rs);

                //error_log('[ENROL_DB] Found '.count($existing).' existing roles and '.$count.' in external database');

                foreach ($courselist as $coursefield) {   /// Check the list of courses against existing
                    $course = get_record('course', $CFG->enrol_localcoursefield, $coursefield);
                    if (!is_object($course)) {
                        if (empty($CFG->enrol_db_autocreate)) { // autocreation not allowed
                            if (debugging('',DEBUG_ALL)) {
                                error_log( "Course $coursefield does not exist, skipping") ;
                            }
                            continue; // next foreach course
                        }
                        // ok, now then let's create it!
                        // prepare any course properties we actually have
                        $course = new StdClass;
                        $course->{$CFG->enrol_localcoursefield} = $coursefield;
                        $course->fullname  = $coursefield;
                        $course->shortname = $coursefield;
                        if (!($newcourseid = $this->create_course($course, true)
                            and $course = get_record( 'course', 'id', $newcourseid))) {
                            error_log( "Creating course $coursefield failed");
                            continue; // nothing left to do...
                        }
                    }

                    // if the course is hidden and we don't want to enrol in hidden courses
                    // then just skip it
                    if (!$course->visible and $CFG->enrol_db_ignorehiddencourse) {
                        continue;
                    }

                    /// If there's no role specified, we get the default course role (usually student)
                    if ($use_default_role) {
                        $role = get_default_course_role($course);
                    }

                    $context = get_context_instance(CONTEXT_COURSE, $course->id);

                    // Couldn't get a role or context, skip.
                    if (!$role || !$context) {
                        continue;
                    }

                    // Search the role assignments to see if this user
                    // already has this role in this context.  If it is, we
                    // skip to the next course.
                    foreach($existing as $key => $role_assignment) {
                        if ($role_assignment->roleid == $role->id
                            && $role_assignment->contextid == $context->id) {
                            unset($existing[$key]);
                            //error_log('[ENROL_DB] User is already enroled in course '.$course->idnumber);
                            continue 2;
                        }
                    }

                    //error_log('[ENROL_DB] Enrolling user in course '.$course->idnumber);
                    role_assign($role->id, $user->id, 0, $context->id, 0, 0, 0, 'database');
                }
            } // We've processed all external courses found

            /// We have some courses left that we might need to unenrol from
            /// Note: we only process enrolments that we (ie 'database' plugin) made
            /// Do not unenrol anybody if the disableunenrol option is 'yes'
            if (!$CFG->enrol_db_disableunenrol) {
                foreach ($existing as $role_assignment) {
                    if ($role_assignment->enrol == 'database') {
                        //error_log('[ENROL_DB] Removing user from context '.$role_assignment->contextid);
                        role_unassign($role_assignment->roleid, $user->id, '', $role_assignment->contextid);
                    } 
                }
            }
        } else {
            error_log('[ENROL_DB] Couldn\'t get rows from external db: '.$enroldb->ErrorMsg());
        }
    }
    $this->enrol_disconnect($enroldb);
}

/**
 * sync enrolments with database, create courses if required.
 *
 * @param object The role to sync for. If no role is specified, defaults are
 * used.
 */
function sync_enrolments($role = null) {
    global $CFG;
    global $db;
    error_reporting(E_ALL);

    // Connect to the external database
    $enroldb = $this->enrol_connect();
    if (!$enroldb) {
        notify("enrol/database cannot connect to server");
        return false;
    }

    if (isset($role)) {
        echo '=== Syncing enrolments for role: '.$role->shortname." ===\n";
    } else {
        echo "=== Syncing enrolments for default role ===\n";
    }

    // first, pack the sortorder...
    fix_course_sortorder();

    list($have_role, $remote_role_name, $remote_role_value) = $this->role_fields($enroldb, $role);

    if (!$have_role) {
        if (!empty($CFG->enrol_db_defaultcourseroleid)
         and $role = get_record('role', 'id', $CFG->enrol_db_defaultcourseroleid)) {
            echo "=== Using enrol_db_defaultcourseroleid: {$role->id} ({$role->shortname}) ===\n";
        } elseif (isset($role)) {
            echo "!!! WARNING: Role specified by caller, but no (or invalid) role configuration !!!\n";
        }
    }

    // get enrolments per-course
    $sql =  "SELECT DISTINCT {$CFG->enrol_remotecoursefield} " .
        " FROM {$CFG->enrol_dbtable} " .
        " WHERE {$CFG->enrol_remoteuserfield} IS NOT NULL" .
        (isset($remote_role_name, $remote_role_value) ? ' AND '.$remote_role_name.' = '.$remote_role_value : '');

    $rs = $enroldb->Execute($sql);
    if (!$rs) {
        trigger_error($enroldb->ErrorMsg() .' STATEMENT: '. $sql);
        return false;
    }
    if ( $rs->EOF ) { // no courses! outta here...
        return true;
    }

    begin_sql();
    $extcourses = array();
    while ($extcourse_obj = rs_fetch_next_record($rs)) { // there are more course records
        $extcourse_obj = (object)array_change_key_case((array)$extcourse_obj , CASE_LOWER);
        $extcourse = $extcourse_obj->{strtolower($CFG->enrol_remotecoursefield)};
        array_push($extcourses, $extcourse);

        // does the course exist in moodle already?
        $course = false;
        $course = get_record( 'course',
                              $CFG->enrol_localcoursefield,
                              $extcourse );

        if (!is_object($course)) {
            if (empty($CFG->enrol_db_autocreate)) { // autocreation not allowed
                if (debugging('', DEBUG_ALL)) {
                    error_log( "Course $extcourse does not exist, skipping");
                }
                continue; // next foreach course
            }
            // ok, now then let's create it!
            // prepare any course properties we actually have
            $course = new StdClass;
            $course->{$CFG->enrol_localcoursefield} = $extcourse;
            $course->fullname  = $extcourse;
            $course->shortname = $extcourse;
            if (!($newcourseid = $this->create_course($course, true)
             and $course = get_record( 'course', 'id', $newcourseid))) {
                error_log( "Creating course $extcourse failed");
                continue; // nothing left to do...
            }

        }

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        // If we don't have a proper role setup, then we default to the default
        // role for the current course.
        if (!$have_role) {
            $role = get_default_course_role($course);
        }

        // get a list of the student ids the are enrolled
        // in the external db -- hopefully it'll fit in memory...
        $extenrolments = array();
        $sql = "SELECT {$CFG->enrol_remoteuserfield} " .
            " FROM {$CFG->enrol_dbtable} " .
            " WHERE {$CFG->enrol_remotecoursefield} = " . $enroldb->quote($extcourse) .
                ($have_role ? ' AND '.$remote_role_name.' = '.$remote_role_value : '');

        $crs = $enroldb->Execute($sql);
        if (!$crs) {
            trigger_error($enroldb->ErrorMsg() .' STATEMENT: '. $sql);
            return false;
        }
        if ( $crs->EOF ) { // shouldn't happen, but cover all bases
            continue;
        }

        // slurp results into an array
        while ($crs_obj = rs_fetch_next_record($crs)) {
            $crs_obj = (object)array_change_key_case((array)$crs_obj , CASE_LOWER);
            array_push($extenrolments, $crs_obj->{strtolower($CFG->enrol_remoteuserfield)});
        }
        rs_close($crs); // release the handle

        //
        // prune enrolments to users that are no longer in ext auth
        // hopefully they'll fit in the max buffer size for the RDBMS
        //
        // TODO: This doesn't work perfectly.  If we are operating without
        // roles in the external DB, then this doesn't handle changes of role
        // within a course (because the user is still enrolled in the course,
        // so NOT IN misses the course).
        //
        // When the user logs in though, their role list will be updated
        // correctly.
        //
        if (!$CFG->enrol_db_disableunenrol) {
            $to_prune = get_records_sql("
             SELECT ra.*
             FROM {$CFG->prefix}role_assignments ra
              JOIN {$CFG->prefix}user u ON ra.userid = u.id
             WHERE ra.enrol = 'database'
              AND ra.contextid = {$context->id}
              AND ra.roleid = ". $role->id . ($extenrolments
                ? " AND u.{$CFG->enrol_localuserfield} NOT IN (".join(", ", array_map(array(&$db, 'quote'), $extenrolments)).")"
                : ''));

            if ($to_prune) {
                foreach ($to_prune as $role_assignment) {
                    if (role_unassign($role->id, $role_assignment->userid, 0, $role_assignment->contextid)){
                        error_log( "Unassigned {$role->shortname} assignment #{$role_assignment->id} for course {$course->id} (" . format_string($course->shortname) . "); user {$role_assignment->userid}");
                    } else {
                        error_log( "Failed to unassign {$role->shortname} assignment #{$role_assignment->id} for course {$course->id} (" . format_string($course->shortname) . "); user {$role_assignment->userid}");
                    }
                }
            }
        }

        //
        // insert current enrolments
        // bad we can't do INSERT IGNORE with postgres...
        //
        foreach ($extenrolments as $member) {
            // Get the user id and whether is enrolled in one fell swoop
            $sql = "
                SELECT u.id AS userid, ra.id AS enrolmentid
                FROM {$CFG->prefix}user u
                 LEFT OUTER JOIN {$CFG->prefix}role_assignments ra ON u.id = ra.userid
                  AND ra.roleid = {$role->id}
                  AND ra.contextid = {$context->id}
                 WHERE u.{$CFG->enrol_localuserfield} = ".$db->quote($member) .
                 " AND (u.deleted IS NULL OR u.deleted=0) ";

            $ers = $db->Execute($sql);
            if (!$ers) {
                trigger_error($db->ErrorMsg() .' STATEMENT: '. $sql);
                return false;
            }
            if ( $ers->EOF ) { // if this returns empty, it means we don't have the student record.
                                              // should not happen -- but skip it anyway
                trigger_error('weird! no user record entry?');
                continue;
            }
            $user_obj = rs_fetch_record($ers);
            $userid      = $user_obj->userid;
            $enrolmentid = $user_obj->enrolmentid;
            rs_close($ers); // release the handle

            if ($enrolmentid) { // already enrolled - skip
                continue;
            }

            if (role_assign($role->id, $userid, 0, $context->id, 0, 0, 0, 'database')){
                error_log( "Assigned role {$role->shortname} to user {$userid} in course {$course->id} (" . format_string($course->shortname) . ")");
            } else {
                error_log( "Failed to assign role {$role->shortname} to user {$userid} in course {$course->id} (" . format_string($course->shortname) . ")");
            }

        } // end foreach member
    } // end while course records
    rs_close($rs); //Close the main course recordset

    //
    // prune enrolments to courses that are no longer in ext auth
    //
    // TODO: This doesn't work perfectly.  If we are operating without
    // roles in the external DB, then this doesn't handle changes of role
    // within a course (because the user is still enrolled in the course,
    // so NOT IN misses the course).
    //
    // When the user logs in though, their role list will be updated
    // correctly.
    //
    if (!$CFG->enrol_db_disableunenrol) {
        $sql = "
            SELECT ra.roleid, ra.userid, ra.contextid
            FROM {$CFG->prefix}role_assignments ra
                JOIN {$CFG->prefix}context cn ON cn.id = ra.contextid
                JOIN {$CFG->prefix}course c ON c.id = cn.instanceid
            WHERE ra.enrol = 'database'
              AND cn.contextlevel = ".CONTEXT_COURSE." " .
                ($have_role ? ' AND ra.roleid = '.$role->id : '') .
                ($extcourses
                    ? " AND c.{$CFG->enrol_localcoursefield} NOT IN (" . join(",", array_map(array(&$db, 'quote'), $extcourses)) . ")"
                    : '');

        $ers = $db->Execute($sql);
        if (!$ers) {
            trigger_error($db->ErrorMsg() .' STATEMENT: '. $sql);
            return false;
        }
        if ( !$ers->EOF ) {
            while ($user_obj = rs_fetch_next_record($ers)) {
                $user_obj = (object)array_change_key_case((array)$user_obj , CASE_LOWER);
                $roleid     = $user_obj->roleid;
                $user       = $user_obj->userid;
                $contextid  = $user_obj->contextid;
                if (role_unassign($roleid, $user, 0, $contextid)){
                    error_log( "Unassigned role {$roleid} from user $user in context $contextid");
                } else {
                    error_log( "Failed unassign role {$roleid} from user $user in context $contextid");
                }
            }
            rs_close($ers); // release the handle
        }
    }

    commit_sql();

    // we are done now, a bit of housekeeping
    fix_course_sortorder();

    $this->enrol_disconnect($enroldb);
    return true;
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
                  'enrol_db_autocreate', 'enrol_db_category', 'enrol_db_template',
                  'enrol_db_localrolefield', 'enrol_db_remoterolefield',
                  'enrol_remotecoursefield', 'enrol_remoteuserfield',
                  'enrol_db_ignorehiddencourse', 'enrol_db_defaultcourseroleid',
                  'enrol_db_disableunenrol');

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

    if (!isset($config->enrol_db_autocreate)) {
        $config->enrol_db_autocreate = '';
    }
    set_config('enrol_db_autocreate', $config->enrol_db_autocreate);

    if (!isset($config->enrol_db_category)) {
        $config->enrol_db_category = '';
    }
    set_config('enrol_db_category', $config->enrol_db_category);

    if (!isset($config->enrol_db_template)) {
        $config->enrol_db_template = '';
    }
    set_config('enrol_db_template', $config->enrol_db_template);

    if (!isset($config->enrol_db_defaultcourseroleid)) {
        $config->enrol_db_defaultcourseroleid = '';
    }
    set_config('enrol_db_defaultcourseroleid', $config->enrol_db_defaultcourseroleid);

    if (!isset($config->enrol_db_localrolefield)) {
        $config->enrol_db_localrolefield = '';
    }
    set_config('enrol_db_localrolefield', $config->enrol_db_localrolefield);

    if (!isset($config->enrol_db_remoterolefield)) {
        $config->enrol_db_remoterolefield = '';
    }
    set_config('enrol_db_remoterolefield', $config->enrol_db_remoterolefield);

    if (!isset($config->enrol_db_ignorehiddencourse)) {
        $config->enrol_db_ignorehiddencourse = '';
    }
    set_config('enrol_db_ignorehiddencourse', $config->enrol_db_ignorehiddencourse );

    if (!isset($config->enrol_db_disableunenrol)) {
        $config->enrol_db_disableunenrol = '';
    }
    set_config('enrol_db_disableunenrol', $config->enrol_db_disableunenrol );

    return true;
}

// will create the moodle course from the template
// course_ext is an array as obtained from ldap -- flattened somewhat
// NOTE: if you pass true for $skip_fix_course_sortorder
// you will want to call fix_course_sortorder() after your are done
// with course creation
function create_course ($course,$skip_fix_course_sortorder=0){
    global $CFG;

    // define a template
    if(!empty($CFG->enrol_db_template)){
        $template = get_record("course", 'shortname', $CFG->enrol_db_template);
        $template = (array)$template;
    } else {
        $site = get_site();
        $template = array(
                          'startdate'      => time() + 3600 * 24,
                          'summary'        => get_string("defaultcoursesummary"),
                          'format'         => "weeks",
                          'password'       => "",
                          'guest'          => 0,
                          'numsections'    => 10,
                          'idnumber'       => '',
                          'cost'           => '',
                          'newsitems'      => 5,
                          'showgrades'     => 1,
                          'groupmode'      => 0,
                          'groupmodeforce' => 0,
                          'student'  => $site->student,
                          'students' => $site->students,
                          'teacher'  => $site->teacher,
                          'teachers' => $site->teachers,
                          );
    }
    // overlay template
    foreach (array_keys($template) AS $key) {
        if (empty($course->$key)) {
            $course->$key = $template[$key];
        }
    }

    $course->category = 1;     // the misc 'catch-all' category
    if (!empty($CFG->enrol_db_category)){ //category = 0 or undef will break moodle
        $course->category = $CFG->enrol_db_category;
    }

    // define the sortorder
    $sort = get_field_sql('SELECT COALESCE(MAX(sortorder)+1, 100) AS max ' .
                          ' FROM ' . $CFG->prefix . 'course ' .
                          ' WHERE category=' . $course->category);
    $course->sortorder = $sort;

    // override with local data
    $course->startdate   = time() + 3600 * 24;
    $course->timecreated = time();
    $course->visible     = 1;

    // clear out id just in case
    unset($course->id);

    // truncate a few key fields
    $course->idnumber  = substr($course->idnumber, 0, 100);
    $course->shortname = substr($course->shortname, 0, 100);

    // store it and log
    if ($newcourseid = insert_record("course", addslashes_object($course))) {  // Set up new course
        $section = NULL;
        $section->course = $newcourseid;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record("course_sections", $section);
        $page = page_create_object(PAGE_COURSE_VIEW, $newcourseid);
        blocks_repopulate_page($page); // Return value no


        if(!$skip_fix_course_sortorder){
            fix_course_sortorder();
        }
        add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "enrol/database auto-creation");
    } else {
        trigger_error("Could not create new course $extcourse from  from database");
        notify("Serious Error! Could not create the new course!");
        return false;
    }

    return $newcourseid;
}

/// DB Connect
/// NOTE: You MUST remember to disconnect
/// when you stop using it -- as this call will
/// sometimes modify $CFG->prefix for the whole of Moodle!
function enrol_connect() {
    global $CFG;

    // Try to connect to the external database (forcing new connection)
    $enroldb = &ADONewConnection($CFG->enrol_dbtype);
    if ($enroldb->Connect($CFG->enrol_dbhost, $CFG->enrol_dbuser, $CFG->enrol_dbpass, $CFG->enrol_dbname, true)) {
        $enroldb->SetFetchMode(ADODB_FETCH_ASSOC); ///Set Assoc mode always after DB connection
        return $enroldb;
    } else {
        trigger_error("Error connecting to enrolment DB backend with: "
                      . "$CFG->enrol_dbhost,$CFG->enrol_dbuser,$CFG->enrol_dbpass,$CFG->enrol_dbname");
        return false;
    }
}

/// DB Disconnect
function enrol_disconnect($enroldb) {
    global $CFG;

    $enroldb->Close();
}

/**
 * This function returns the name and value of the role field to query the db
 * for, or null if there isn't one.
 *
 * @param object The ADOdb connection
 * @param object The role
 * @return array (boolean, string, db quoted string)
 */
function role_fields($enroldb, $role) {
    global $CFG;

    if ($have_role = !empty($role)
     && !empty($CFG->enrol_db_remoterolefield)
     && !empty($CFG->enrol_db_localrolefield)
     && !empty($role->{$CFG->enrol_db_localrolefield})) {
        $remote_role_name = $CFG->enrol_db_remoterolefield;
        $remote_role_value = $enroldb->quote($role->{$CFG->enrol_db_localrolefield});
    } else {
        $remote_role_name = $remote_role_value = null;
    }

    return array($have_role, $remote_role_name, $remote_role_value);
}

} // end of class

?>

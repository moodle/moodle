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
    if ($enroldb = $this->enrol_connect()) {

        /// Get the authoritative list of enrolments from the external database table
        /// We're using the ADOdb functions natively here and not our datalib functions
        /// because we didn't want to mess with the $db global

        $useridfield = $user->{$CFG->enrol_localuserfield};   

        if ($rs = $enroldb->Execute("SELECT $CFG->enrol_remotecoursefield 
                                       FROM $CFG->enrol_dbtable 
                                      WHERE $CFG->enrol_remoteuserfield = '$useridfield' ")) {

            $existing = get_my_courses($user->id, '', 'id');  // We'll use this to see what to add and remove

            if ($rs->RecordCount() > 0) {   // We found some courses

                $courselist = array();
                while (!$rs->EOF) {         // Make a nice little array of courses to process
                    $courselist[] = $rs->fields[0];
                    $rs->MoveNext();
                }

                foreach ($courselist as $coursefield) {   /// Check the list of courses against existing
                    if ($course = get_record('course', $CFG->enrol_localcoursefield, $coursefield)) {
                    
                        if (isset($existing[$course->id])) {   // Already enrolled so remove from checklist
                            unset($existing[$course->id]);

                        } else {  /// Not enrolled yet so let's do enrol them

                            if ($context = get_context_instance(CONTEXT_COURSE, $course->id)) {  // Get the context
                                $role = NULL;

                            /// Check if a particular role has been forced by the plugin site-wide
                                if ($CFG->enrol_db_defaultcourseroleid) {  
                                    $role = get_record('role', 'id', $CFG->enrol_db_defaultcourseroleid);
                                }

                            /// Otherwise, we get the default course role (usually student)
                                if (empty($role)) {
                                    $role = get_default_course_role($course);
                                }
                                
                            /// If we have a role now then assign it
                                if ($role) {
                                    role_assign($role->id, $user->id, 0, $context->id, 0, 0, 0, 'database');
                                }
                            }
                        }
                    }
                }
            } // We've processed all external courses found

            if (!empty($existing)) {    

                /// We have some courses left that we might need to unenrol from
                /// Note: we only process enrolments that we (ie 'database' plugin) made

                foreach ($existing as $course) {
                    if ($context = get_context_instance(CONTEXT_COURSE, $course->id)) {  // Get the context
                        if ($roles = get_user_roles($context, $user->id, false)) {       // User has some roles here
                            foreach ($roles as $role) {
                                if ($role->enrol == 'database') {                        // Yes! It's one of ours
                                    role_unassign($role->id, $user->id, '', $context->id);
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->enrol_disconnect($enroldb);
    } // end if (enroldb=connect)
}

///
/// sync enrolments with database, create courses if required.
///
/// NOTE: we are currently  ignoring $type, as enrol/database only deasl with student enrolment
function sync_enrolments($type='student') {
    global $CFG;
    global $db;
    error_reporting(E_ALL);

    // force type to student - remove when adding teacher support
    $type = 'student';

    if(!isset($type) || !($type =='student' || $type =='teacher' )){
        error("Bad call to get_all_courses()!");
    }

    // Connect to the external database
    $enroldb = $this->enrol_connect();
    if (!$enroldb) {        
        notify("enrol/database cannot connect to server");
        return false;
    }
    
    // first, pack the sortorder...
    fix_course_sortorder();

    // get enrolments per-course
    $sql =  "SELECT DISTINCT {$CFG->enrol_remotecoursefield} " .
        " FROM {$CFG->enrol_dbtable} " .
        " WHERE {$CFG->enrol_remoteuserfield} IS NOT NULL";

    $rs = $enroldb->Execute($sql);
    if (!$rs) {
        trigger_error($enroldb->ErrorMsg() .' STATEMENT: '. $sql);
        return false;
    }
    if ( $rs->RecordCount() == 0 ) { // no courses! outta here...
        return true;
    }

    $extcourses = array();
    while (!$rs->EOF) { // there are more course records
        $extcourse = $rs->fields[0];
        array_push($extcourses, $extcourse);
        $rs->MoveNext(); // prep the next record

        print "course $extcourse\n";

        // does the course exist in moodle already? 
        $course = false;
        $course = get_record( 'course',
                              $CFG->enrol_localcoursefield,
                              $extcourse );

        if (!is_object($course)) {
            if (empty($CFG->enrol_db_autocreate)) { // autocreation not allowed
                print "Course $extcourse does not exists, skipping\n";
                continue; // next foreach course
            }
            // ok, now then let's create it!
            print "Creating Course $extcourse...";
            // prepare any course properties we actually have
            $course = new StdClass;
            $course->{$CFG->enrol_localcoursefield} = $extcourse;
            $course->fullname  = $extcourse;
            $course->shortname = $extcourse;
            if ($newcourseid = $this->create_course($course, true)) {  // we are skipping fix_course_sortorder()
                $course = get_record( 'course', 'id', $newcourseid); 
            }
            if ($course) {
                print "OK!\n";
            } else {
                print "failed\n";
                continue; // nothing left to do...
            }
        }
        
        // get a list of the student ids the are enrolled
        // in the external db -- hopefully it'll fit in memory...
        $extenrolments = array();
        $sql = "SELECT {$CFG->enrol_remoteuserfield} " .
            " FROM {$CFG->enrol_dbtable} " .
            " WHERE {$CFG->enrol_remotecoursefield} = '$extcourse'";
        
        $crs = $enroldb->Execute($sql);
        if (!$crs) {
            trigger_error($enroldb->ErrorMsg() .' STATEMENT: '. $sql);
            return false;
        }
        if ( $crs->RecordCount() == 0 ) { // shouldn't happen, but cover all bases
            continue;
        }

        // slurp results into an array
        while (!$crs->EOF) {
            array_push($extenrolments,$crs->fields[0]);
            $crs->MoveNext(); 
        }
        unset($crs); // release the handle

        //
        // prune enrolments
        // hopefully they'll fit in the max buffer size for the RDBMS
        //
        $sql = " SELECT enr.userid AS userid " .
            " FROM {$CFG->prefix}user_students enr " .
            "     JOIN {$CFG->prefix}user usr ON usr.id=enr.userid " .
            " WHERE course={$course->id} AND enrol='database' " .
            "       AND {$CFG->enrol_localuserfield} NOT IN ('" . join("','", $extenrolments) ."')";
        
        $ers = $db->Execute($sql);
        if (!$ers) {
            trigger_error($db->ErrorMsg() .' STATEMENT: '. $sql);
            return false;
        }
        if ( $ers->RecordCount() > 0 ) {             
            while (!$ers->EOF) {
                $member = $ers->fields[0];
                $ers->MoveNext(); 
                if (unenrol_student($member, $course->id)){
                    print "Unenrolled $type $member into course $course->id ($course->shortname)\n";
                } else {
                    print "Failed to unenrol $type $member into course $course->id ($course->shortname)\n";
                }
            }
        }
        unset($ers); // release the handle
        
        //
        // insert current enrolments 
        // bad we can't do INSERT IGNORE with postgres...
        //
        begin_sql();
        foreach ($extenrolments as $member) {
            // Get the user id and whether is enrolled in one fell swoop
            $sql = "SELECT u.id AS userid, e.id AS enrolmentid" .
                " FROM {$CFG->prefix}user u " . 
                "      LEFT OUTER JOIN {$CFG->prefix}user_students e ON (u.id = e.userid AND e.course='{$course->id}')" .
                " WHERE u.{$CFG->enrol_localuserfield} = '$member'";

            $ers = $db->Execute($sql);
            if (!$ers) {
                trigger_error($db->ErrorMsg() .' STATEMENT: '. $sql);
                return false;
            }
            if ( $ers->RecordCount() != 1 ) { // if this returns empty, it means we don't have the student record.
                                              // should not happen -- but skip it anyway 
                trigger_error('weird! no user record entry?');
                continue;
            }
            $userid      = $ers->fields[0];
            $enrolmentid = $ers->fields[1];
            unset($ers); // release the handle

            if ($enrolmentid) { // already enrolled - skip
                continue;
            }
            
            if($type === 'student'){
                if (enrol_student($userid, $course->id, 0, 0, 'database')){
                    print "Enrolled $type $member into course $course->id ($course->shortname)\n";
                } else {
                    print "Failed to enrol $type $member into course $course->id ($course->shortname)\n";
                }
            } 

        } // end foreach member
        commit_sql();
    } // end while course records

    //
    // prune enrolments to courses that are no longer in ext auth
    //
    $sql = "SELECT e.userid, e.course " .
        " FROM {$CFG->prefix}user_students e " .
        "      JOIN {$CFG->prefix}course c ON e.course = c.id " .
        " WHERE e.enrol='database' " ;
    if ($extcourses) { 
        $sql .= " AND c.{$CFG->enrol_localcoursefield} NOT IN ('" . join("','", $extcourses) . "')";
    }
    $ers = $db->Execute($sql);
    if (!$ers) {
        trigger_error($db->ErrorMsg() .' STATEMENT: '. $sql);
        return false;
    }
    if ( $ers->RecordCount() > 0 ) {             
        while (!$ers->EOF) {
            $user   = $ers->fields[0];
            $course = $ers->fields[1];
            $ers->MoveNext(); 
            if (unenrol_student($user, $course)){
                print "Unenrolled student $user from course $course\n";
            } else {
                print "Failed to unenrol student $user from course $course\n";
            }
        }
    }
    unset($ers); // release the handle
    
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
                  'enrol_remotecoursefield', 'enrol_remoteuserfield');

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
    $course->shortname = substr($course->shortname, 0, 15);
    
    // store it and log
    if ($newcourseid = insert_record("course", $course)) {  // Set up new course
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

    // This is a hack to workaround what seems to be a bug in ADOdb with accessing 
    // two MySQL databases ... it seems to get confused when trying to access
    // the first database again, after having accessed the second.
    // The following hack will make the database explicit which keeps it happy
    if ($CFG->dbtype === 'mysql' && $CFG->enrol_dbtype === 'mysql') {
        if (strpos($CFG->prefix, $CFG->dbname) === false) {
            $CFG->prefix_old = $CFG->prefix;
            $CFG->prefix = "`$CFG->dbname`.$CFG->prefix";
        }
    }

    // Try to connect to the external database
    $enroldb = &ADONewConnection($CFG->enrol_dbtype);
    if ($enroldb->PConnect($CFG->enrol_dbhost,$CFG->enrol_dbuser,$CFG->enrol_dbpass,$CFG->enrol_dbname)) {
        return $enroldb;
    } else {
        // do a bit of cleanup, and lot the problem
        if (!empty($CFG->prefix_old)) {
            $CFG->prefix =$CFG->prefix_old;           // Restore it just in case
            unset($CFG->prefix_old);
        }
        trigger_error("Error connecting to enrolment DB backend with: "
                      . "$CFG->enrol_dbhost,$CFG->enrol_dbuser,$CFG->enrol_dbpass,$CFG->enrol_dbname");
        return false;
    }    
}

/// DB Disconnect
function enrol_disconnect($enroldb) {
    global $CFG;

    $enroldb->Close();

    // Cleanup the mysql 
    // hack 
    if (!empty($CFG->prefix_old)) {
        $CFG->prefix =$CFG->prefix_old;           // Restore it just in case
        unset($CFG->prefix_old);
    }
}

} // end of class

?>

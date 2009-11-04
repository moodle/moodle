<?php  // $Id$

require_once("$CFG->dirroot/enrol/enrol.class.php");

class enrolment_plugin_ldap {

    var $log;    
    
    var $enrol_localcoursefield = 'idnumber';

/**
 * This function syncs a user's enrolments with those on the LDAP server.
 */
function setup_enrolments(&$user) {
    global $CFG;

    //error_log('[ENROL_LDAP] setup_enrolments called');

    // Connect to the external database
    $ldap_connection = $this->enrol_ldap_connect();
    if (!$ldap_connection) {
        @ldap_close($ldap_connection);
        notify("[ENROL_LDAP] LDAP-module cannot connect to server: {$CFG->enrol_ldap_host_url}");
        return false;
    }
    
    // we are connected OK, continue...

    // Get all the possible roles
    $roles = get_records('role');

    // Make sure the config settings have been upgraded.
    $this->check_legacy_config();

    // Get the entire list of role assignments that currently exist for this user.
    $roleassignments = get_records('role_assignments', 'userid', $user->id);
    if (!$roleassignments) {
        $roleassignments = array();
    }
    
    // Get the ones that came from LDAP
    $ldap_assignments = array_filter($roleassignments, create_function('$x', 'return $x->enrol == \'ldap\';'));

    //error_log('[ENROL_LDAP] ' . count($roleassignments) . ' roles currently associated with this user');

    // Get enrolments for each type of role from LDAP.
    foreach($roles as $role) {
        $enrolments = $this->find_ext_enrolments(
            $ldap_connection, 
            $user->idnumber , 
            $role);

        //error_log('[ENROL_LDAP] LDAP reports ' . count($enrolments) . ' enrolments of type ' . $role->shortname . '.');

        foreach ($enrolments as $enrol){
    
            $course_ext_id = $enrol[$CFG->enrol_ldap_course_idnumber][0];
            if(empty($course_ext_id)){
                error_log("[ENROL_LDAP] The course external id is invalid!\n");
                continue; // next; skip this one!
            }
            
            // create the course  ir required
            $course_obj = get_record( 'course',
                                      $this->enrol_localcoursefield,
                                      $course_ext_id );

            if (empty($course_obj)){ // course doesn't exist
                if($CFG->enrol_ldap_autocreate){ // autocreate
                    error_log("[ENROL_LDAP] CREATE User $user->username enrolled to a nonexistant course $course_ext_id \n");
                    $newcourseid = $this->create_course($enrol);
                    $course_obj = get_record( 'course', 'id', $newcourseid);
                } else {
                    error_log("[ENROL_LDAP] User $user->username enrolled to a nonexistant course $course_ext_id \n");
                }
            }
            
            // deal with enrolment in the moodle db
            if (!empty($course_obj)) { // does course exist now?     
        
                $context = get_context_instance(CONTEXT_COURSE, $course_obj->id);
                //$courseroles = get_user_roles($context, $user->id);
            
                if (!get_record('role_assignments', 'roleid', $role->id, 'userid', $user->id, 'contextid', $context->id)) {
                    //error_log("[ENROL_LDAP] Assigning role '{$role->name}' to {$user->id} ({$user->username}) in course {$course_obj->id} ({$course_obj->shortname})");
                    if (!role_assign($role->id, $user->id, 0, $context->id, 0, 0, 0, 'ldap')){
                        error_log("[ENROL_LDAP] Failed to assign role '{$role->name}' to $user->id ($user->username) into course $course_obj->id ($course_obj->shortname)");
                    } 
                } else {
                    //error_log("[ENROL_LDAP] Role '{$role->name}' already assigned to {$user->id} ({$user->username}) in course {$course_obj->id} ({$course_obj->shortname})");
                }

                // Remove the role from the list we created earlier.  This
                // way we can find those roles that are no longer required.
                foreach($ldap_assignments as $key => $roleassignment) {
                    if ($roleassignment->roleid == $role->id
                     && $roleassignment->contextid == $context->id) {
                        unset($ldap_assignments[$key]);
                        break;
                    }
                }
            }
        }
    }

    // ok, if there's any thing still left in the $roleassignments array we
    // made at the start, we want to remove any of the ldap ones.
    foreach ($ldap_assignments as $ra) {
        if($ra->enrol === 'ldap') {
            error_log("Unassigning role_assignment with id '{$ra->id}' from user {$user->id} ({$user->username})");
            role_unassign($ra->roleid, $user->id, 0, $ra->contextid, 'ldap');
        }
    }

    @ldap_close($ldap_connection);

    //error_log('[ENROL_LDAP] finished with setup_enrolments');
    
    return true;
}

/// sync enrolments with ldap, create courses if required.
function sync_enrolments($type, $enrol = false) {
    global $CFG;

    // Get the role. If it doesn't exist, that is bad.
    $role = get_record('role', 'shortname', $type);
    if (!$role) {
        notify("No such role: $type");
        return false;
    }

    // Connect to the external database
    $ldap_connection = $this->enrol_ldap_connect();
    if (!$ldap_connection) {
        @ldap_close($ldap_connection);
        notify("LDAP-module cannot connect to server: $CFG->ldap_host_url");
        return false;
    }
    
    // we are connected OK, continue...
    $this->enrol_ldap_bind($ldap_connection);

    //get all contexts and look for first matching user
    $ldap_contexts = explode(";",$CFG->{'enrol_ldap_contexts_role'.$role->id});

    // get all the fields we will want for the potential course creation
    // as they are light. don't get membership -- potentially a lot of data. 
    $ldap_fields_wanted = array( 'dn', $CFG->enrol_ldap_course_idnumber);
    if (!empty($CFG->enrol_ldap_course_fullname)){
        array_push($ldap_fields_wanted, $CFG->enrol_ldap_course_fullname);
    }
    if (!empty($CFG->enrol_ldap_course_shortname)){
        array_push($ldap_fields_wanted, $CFG->enrol_ldap_course_shortname);
    }
    if (!empty($CFG->enrol_ldap_course_summary)){
        array_push($ldap_fields_wanted, $CFG->enrol_ldap_course_summary);
    }
    if($enrol){
        array_push($ldap_fields_wanted, $CFG->{'enrol_ldap_memberattribute_role'.$role->id});
    }

    // define the search pattern
    if (!empty($CFG->enrol_ldap_objectclass)){ 
        $ldap_search_pattern='(objectclass='.$CFG->enrol_ldap_objectclass.')';
    } else {
       $ldap_search_pattern="(objectclass=*)";
        
    }

    // first, pack the sortorder...
    fix_course_sortorder();

    foreach ($ldap_contexts as $context) {

        $context = trim($context);
        if ($CFG->enrol_ldap_search_sub){
            //use ldap_search to find first user from subtree
            $ldap_result = @ldap_search($ldap_connection, 
                                        $context, 
                                        $ldap_search_pattern,
                                        $ldap_fields_wanted);

        } else {
            //search only in this context
            $ldap_result = @ldap_list($ldap_connection, 
                                        $context, 
                                        $ldap_search_pattern,
                                        $ldap_fields_wanted,0,0);
        }
 
        // check and push results
        $records = $ldap_result
            ? ldap_get_entries($ldap_connection,$ldap_result)
            : array('count' => 0);

        // ldap libraries return an odd array, really. fix it:
        $flat_records=array();
        for ($c=0;$c<$records['count'];$c++) {
            array_push($flat_records, $records["$c"]); 
        }
        // free mem -- is there a leak? 
        $records=0; $ldap_result=0;

        if (count($flat_records)) {


            foreach($flat_records as $course){
                $idnumber = $course{$CFG->enrol_ldap_course_idnumber}[0];
                print "== Synching $idnumber\n";
                // does the course exist in moodle already? 
                $course_obj = false;
                $course_obj = get_record( 'course',
                                          $this->enrol_localcoursefield,
                                          $idnumber );
                if (!is_object($course_obj)) {
                    if (empty($CFG->enrol_ldap_autocreate)) { // autocreation not allowed
                        print "[ENROL_LDAP] Course $idnumber does not exist, skipping\n";
                        continue; // next foreach course
                    }

                    // ok, now then let's create it!
                    print "Creating Course $idnumber...";
                    $newcourseid = $this->create_course($course, true); // we are skipping fix_course_sortorder()
                    $course_obj = get_record( 'course', 'id', $newcourseid);
                    if (is_object($course_obj)) {
                        print "OK!\n";
                    } else {
                        print "failed\n";
                    }
                }

                // enrol&unenrol if required
                if($enrol && is_object($course_obj)){

                    // Get a context object.
                    $context = get_context_instance(CONTEXT_COURSE, $course_obj->id);

                    // pull the ldap membership into a nice array
                    // this is an odd array -- mix of hash and array -- 
                    $ldapmembers=array();

                    if(array_key_exists('enrol_ldap_memberattribute_role'.$role->id, $CFG)
                     && !empty($CFG->{'enrol_ldap_memberattribute_role'.$role->id})
                     && !empty($course[strtolower($CFG->{'enrol_ldap_memberattribute_role'.$role->id} ) ])){ // may have no membership!

                        $ldapmembers = $course[strtolower($CFG->{'enrol_ldap_memberattribute_role'.$role->id} )]; 
                        unset($ldapmembers['count']); // remove oddity ;)
                        $ldapmembers = addslashes_recursive($ldapmembers);
                    }
                    
                    // prune old ldap enrolments
                    // hopefully they'll fit in the max buffer size for the RDBMS
                    $sql = '
                        SELECT enr.userid AS user, 1
                        FROM '.$CFG->prefix.'role_assignments enr
                        JOIN '.$CFG->prefix.'user usr ON usr.id=enr.userid
                        WHERE enr.roleid = '.$role->id.'
                         AND enr.contextid = '.$context->id.'
                         AND enr.enrol = \'ldap\' ';
                    if (!empty($ldapmembers)) {
                        $sql .= 'AND usr.idnumber NOT IN (\''. join('\',\'', $ldapmembers).'\')';
                    } else {
                        print ("Empty enrolment for $course_obj->shortname \n");
                    }
                    $todelete = get_records_sql($sql);
                    if(!empty($todelete)){
                        foreach ($todelete as $member) {
                            $member = $member->user;

                            if (role_unassign($role->id, $member, 0, $context->id, 'ldap')) {
                                print "Unassigned $type from $member for course $course_obj->id ($course_obj->shortname)\n";
                            } else {
                                print "Failed to unassign $type from $member for course $course_obj->id ($course_obj->shortname)\n";
                            }
                        }
                    }
                    
                    // insert current enrolments 
                    // bad we can't do INSERT IGNORE with postgres...
                    foreach ($ldapmembers as $ldapmember) {
                        $sql = 'SELECT id,1 FROM '.$CFG->prefix.'user '
                                ." WHERE idnumber='$ldapmember'";
                        $member = get_record_sql($sql); 
//                        print "sql: $sql \nidnumber = ".stripslashes($ldapmember)." \n".var_dump($member); 
                        if(empty($member) || empty($member->id)){
                            print "Could not find user ".stripslashes($ldapmember).", skipping\n";
                            continue;
                        }
                        $member = $member->id;
                        if (!get_record('role_assignments', 'roleid', $role->id, 
                                        'contextid', $context->id, 
                                        'userid', $member, 'enrol', 'ldap')){
                            if (role_assign($role->id, $member, 0, $context->id, 0, 0, 0, 'ldap')){
                                print "Assigned role $type to $member (".stripslashes($ldapmember).") for course $course_obj->id ($course_obj->shortname)\n";
                            } else {
                                print "Failed to assign role $type to $member (".stripslashes($ldapmember).") for course $course_obj->id ($course_obj->shortname)\n";
                            }
                        }
                    }
                }
            }
        }
    }
    
    // we are done now, a bit of housekeeping
    fix_course_sortorder();
    
    @ldap_close($ldap_connection);
    return true;
}


/// Overide the get_access_icons() function
function get_access_icons($course) {
}


/// Overrise the base config_form() function
function config_form($frm) {
    global $CFG;

    $this->check_legacy_config();
    
    include("$CFG->dirroot/enrol/ldap/config.html");
}

/// Override the base process_config() function
function process_config($config) {

    $this->check_legacy_config();

    if (!isset ($config->enrol_ldap_host_url)) {
        $config->enrol_ldap_host_url = '';
    }
    set_config('enrol_ldap_host_url', $config->enrol_ldap_host_url);

    if (!isset ($config->enrol_ldap_version)) {
        $config->enrol_ldap_version = '';
    }
    set_config('enrol_ldap_version', $config->enrol_ldap_version);
    
    if (!isset ($config->enrol_ldap_bind_dn)) {
        $config->enrol_ldap_bind_dn = '';
    }
    set_config('enrol_ldap_bind_dn', $config->enrol_ldap_bind_dn);

    if (!isset ($config->enrol_ldap_bind_pw)) {
        $config->enrol_ldap_bind_pw = '';
    }
    set_config('enrol_ldap_bind_pw', $config->enrol_ldap_bind_pw);    
    
    if (!isset ($config->enrol_ldap_objectclass)) {
        $config->enrol_ldap_objectclass = '';
    }
    set_config('enrol_ldap_objectclass', $config->enrol_ldap_objectclass);    
    
    if (!isset ($config->enrol_ldap_category)) {
        $config->enrol_ldap_category  = '';
    }
    set_config('enrol_ldap_category', $config->enrol_ldap_category);    
    
    if (!isset ($config->enrol_ldap_template)) {
        $config->enrol_ldap_template = '';
    }
    set_config('enrol_ldap_template', $config->enrol_ldap_template);    
    
    if (!isset ($config->enrol_ldap_course_fullname)) {
        $config->enrol_ldap_course_fullname = '';
    }
    set_config('enrol_ldap_course_fullname', $config->enrol_ldap_course_fullname);    

    if (!isset ($config->enrol_ldap_course_shortname)) {
        $config->enrol_ldap_course_shortname = '';
    }
    set_config('enrol_ldap_course_shortname', $config->enrol_ldap_course_shortname);    
    
    if (!isset ($config->enrol_ldap_course_summary)) {
        $config->enrol_ldap_course_summary = '';
    }
    set_config('enrol_ldap_course_summary', $config->enrol_ldap_course_summary);    
    
    if (!isset ($config->enrol_ldap_course_idnumber)) {
        $config->enrol_ldap_course_idnumber = '';
    }
    set_config('enrol_ldap_course_idnumber', $config->enrol_ldap_course_idnumber); 
    
    if (!isset ($config->enrol_localcoursefield)) {
        $config->enrol_localcoursefield = '';
    }
    set_config('enrol_localcoursefield', $config->enrol_localcoursefield);
    
    if (!isset ($config->enrol_ldap_user_memberfield)) {
        $config->enrol_ldap_user_memberfield = '';
    }
    set_config('enrol_ldap_user_memberfield', $config->enrol_ldap_user_memberfield); 
    
    if (!isset ($config->enrol_ldap_search_sub)) {
        $config->enrol_ldap_search_sub = '0';
    }
    set_config('enrol_ldap_search_sub', $config->enrol_ldap_search_sub); 
    
    if (!isset ($config->enrol_ldap_autocreate)) {
        $config->enrol_ldap_autocreate = '0';
    }
    set_config('enrol_ldap_autocreate', $config->enrol_ldap_autocreate);

    $roles = get_records('role');
    foreach ($roles as $role) {
        if (!isset($config->{'enrol_ldap_contexts_role'.$role->id})) {
            $config->{'enrol_ldap_contexts_role'.$role->id} = '';
        }
        
        if (!isset($config->{'enrol_ldap_memberattribute_role'.$role->id})) {
            $config->{'enrol_ldap_memberattribute_role'.$role->id} = '';
        }
        
        set_config('enrol_ldap_contexts_role'.$role->id, $config->{'enrol_ldap_contexts_role'.$role->id});
        set_config('enrol_ldap_memberattribute_role'.$role->id, $config->{'enrol_ldap_memberattribute_role'.$role->id});
    }

    return true;
}

function enrol_ldap_connect(){
/// connects to ldap-server
    global $CFG;

    $result = ldap_connect($CFG->enrol_ldap_host_url);

    if ($result) {
        if (!empty($CFG->enrol_ldap_version)) {
            ldap_set_option($result, LDAP_OPT_PROTOCOL_VERSION, $CFG->enrol_ldap_version);
        }

        if (!empty($CFG->enrol_ldap_bind_dn)) {
            $bind = ldap_bind( $result,
                                 $CFG->enrol_ldap_bind_dn, 
                                 $CFG->enrol_ldap_bind_pw );
            if (!$bind) {
                notify("Error in binding to LDAP server");
                trigger_error("Error in binding to LDAP server $!");
            }

        }
        return $result;
    } else {
        notify("LDAP-module cannot connect to server: $CFG->enrol_ldap_host_url");
        return false;
    }
}

function enrol_ldap_bind($ldap_connection){
/// makes bind to ldap for searching users
/// uses ldap_bind_dn or anonymous bind

    global $CFG;

    if ( ! empty($CFG->enrol_ldap_bind_dn) ){
        //bind with search-user
        if (!ldap_bind($ldap_connection, $CFG->enrol_ldap_bind_dn,$CFG->enrol_ldap_bind_pw)){
            notify("Error: could not bind ldap with ldap_bind_dn/pw");
            return false;
        }

    } else {
        //bind anonymously 
        if ( !ldap_bind($ldap_connection)){
            notify("Error: could not bind ldap anonymously");
            return false;
        }  
    }

    return true;
}

function find_ext_enrolments ($ldap_connection, $memberuid, $role){
/// role is a record from the mdl_role table
/// return multidimentional array array with of courses (at least dn and idnumber)
/// 

    global $CFG;

    if(empty($memberuid)) { // No "idnumber" stored for this user, so no LDAP enrolments
        return array();
    } 

    //default return value
    $courses = array();
    $this->enrol_ldap_bind($ldap_connection);

    //get all contexts and look for first matching user
    $ldap_contexts = explode(";",$CFG->{'enrol_ldap_contexts_role'.$role->id});

    // get all the fields we will want for the potential course creation
    // as they are light. don't get membership -- potentially a lot of data. 
    $ldap_fields_wanted = array( 'dn', $CFG->enrol_ldap_course_idnumber);
    if (!empty($CFG->enrol_ldap_course_fullname)){
        array_push($ldap_fields_wanted, $CFG->enrol_ldap_course_fullname);
    }
    if (!empty($CFG->enrol_ldap_course_shortname)){
        array_push($ldap_fields_wanted, $CFG->enrol_ldap_course_shortname);
    }
    if (!empty($CFG->enrol_ldap_course_summary)){
        array_push($ldap_fields_wanted, $CFG->enrol_ldap_course_summary);
    }

    // define the search pattern
    $ldap_search_pattern = "(".$CFG->{'enrol_ldap_memberattribute_role'.$role->id}."=".$this->filter_addslashes($memberuid).")";
    if (!empty($CFG->enrol_ldap_objectclass)){ 
        $ldap_search_pattern='(&(objectclass='.$CFG->enrol_ldap_objectclass.')'.$ldap_search_pattern.')';
    }

    foreach ($ldap_contexts as $context) {
        $context == trim($context);
        if (empty($context)) {
            continue; // next;
        }
        
        if ($CFG->enrol_ldap_search_sub){
            //use ldap_search to find first user from subtree
            $ldap_result = ldap_search($ldap_connection, 
                                        $context, 
                                        $ldap_search_pattern,
                                        $ldap_fields_wanted);

        } else {
            //search only in this context
            $ldap_result = ldap_list($ldap_connection, 
                                        $context, 
                                        $ldap_search_pattern,
                                        $ldap_fields_wanted);
        }
 
        // check and push results
        $records = ldap_get_entries($ldap_connection,$ldap_result);        

        // ldap libraries return an odd array, really. fix it:
        $flat_records=array();
        for ($c=0;$c<$records['count'];$c++) {
            array_push($flat_records, $records["$c"]); 
        }
        
        if (count($flat_records)) {
            $courses = array_merge($courses, $flat_records);
        }
    }

    return $courses;
}

// will create the moodle course from the template
// course_ext is an array as obtained from ldap -- flattened somewhat
// NOTE: if you pass true for $skip_fix_course_sortorder 
// you will want to call fix_course_sortorder() after your are done
// with course creation
function create_course ($course_ext,$skip_fix_course_sortorder=0){
    global $CFG;

    // override defaults with template course
    if(!empty($CFG->enrol_ldap_template)){
        $course = get_record("course", 'shortname', $CFG->enrol_ldap_template);
        unset($course->id); // so we are clear to reinsert the record
        unset($course->sortorder);
    } else {
        // set defaults
        $course = new object();
        $course->student  = get_string('defaultcoursestudent');
        $course->students = get_string('defaultcoursestudents');
        $course->teacher  = get_string('defaultcourseteacher');
        $course->teachers = get_string('defaultcourseteachers');
        $course->format = 'topics';
    }

    // override with required ext data
    $course->idnumber  = $course_ext[$CFG->enrol_ldap_course_idnumber][0];
    $course->fullname  = $course_ext[$CFG->enrol_ldap_course_fullname][0];
    $course->shortname = $course_ext[$CFG->enrol_ldap_course_shortname][0];
    if (   empty($course->idnumber)
        || empty($course->fullname)
        || empty($course->shortname) ) { 
        // we are in trouble!
        error_log("Cannot create course: missing required data from the LDAP record!");
         error_log(var_export($course, true));
        return false;
    }

    $course->summary = empty($CFG->enrol_ldap_course_summary) || empty($course_ext[$CFG->enrol_ldap_course_summary][0])
        ? ''
        : $course_ext[$CFG->enrol_ldap_course_summary][0];
    
    if(!empty($CFG->enrol_ldap_category)){ // optional ... but ensure it is set!
        $course->category = $CFG->enrol_ldap_category;
    } 
    if ($course->category == 0){ // must be avoided as it'll break moodle
        $course->category = 1; // the misc 'catch-all' category
    }

    // define the sortorder (yuck)
    $sort = get_record_sql('SELECT MAX(sortorder) AS max, 1 FROM ' . $CFG->prefix . 'course WHERE category=' . $course->category);
    $sort = $sort->max;
    $sort++;
    $course->sortorder = $sort; 

    // override with local data
    $course->startdate = time();
    $course->timecreated = time();
    $course->visible     = 1;
    
    $course = addslashes_recursive($course);

    // store it and log
    if ($newcourseid = insert_record("course", $course)) {  // Set up new course
        $section = new object();
        $section->course = $newcourseid;   // Create a default section.
        $section->section = 0;
        $section->id = insert_record("course_sections", $section);
        $page = page_create_object(PAGE_COURSE_VIEW, $newcourseid);
        blocks_repopulate_page($page); // Return value no


        if(!$skip_fix_course_sortorder){ 
            fix_course_sortorder(); 
        }
        add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "enrol/ldap auto-creation");
    } else {
        error_log("Could not create new course from LDAP from DN:" . $course_ext['dn']);
        notify("Serious Error! Could not create the new course!");
        return false;
    }
    
    return $newcourseid;
}

/**
 * This function checks for the presence of old 'legacy' config settings.  If
 * they exist, it corrects them.
 *
 * @uses $CFG
 */
function check_legacy_config () {
    global $CFG;

    if (isset($CFG->enrol_ldap_student_contexts)) {
        if ($student_role = get_record('role', 'shortname', 'student')) {
            set_config('enrol_ldap_contexts_role'.$student_role->id, $CFG->enrol_ldap_student_contexts);
        }

        unset_config('enrol_ldap_student_contexts');
    }

    if (isset($CFG->enrol_ldap_student_memberattribute)) {
        if (isset($student_role)
         or $student_role = get_record('role', 'shortname', 'student')) {
            set_config('enrol_ldap_memberattribute_role'.$student_role->id, $CFG->enrol_ldap_student_memberattribute);
        }

        unset_config('enrol_ldap_student_memberattribute');
    }

    if (isset($CFG->enrol_ldap_teacher_contexts)) {
        if ($teacher_role = get_record('role', 'shortname', 'teacher')) {
            set_config('enrol_ldap_contexts_role'.$teacher_role->id, $CFG->enrol_ldap_student_contexts);
        }

        unset_config('enrol_ldap_teacher_contexts');
    }
    
    if (isset($CFG->enrol_ldap_teacher_memberattribute)) {
        if (isset($teacher_role)
         or $teacher_role = get_record('role', 'shortname', 'teacher')) {
            set_config('enrol_ldap_memberattribute_role'.$teacher_role->id, $CFG->enrol_ldap_teacher_memberattribute);
        }

        unset_config('enrol_ldap_teacher_memberattribute');
    }
}

/**
 * Quote control characters in texts used in ldap filters - see RFC 4515/2254
 *
 * @param string
 */
function filter_addslashes($text) {
    $text = str_replace('\\', '\\5c', $text);
    $text = str_replace(array('*',    '(',    ')',    "\0"),
                        array('\\2a', '\\28', '\\29', '\\00'), $text);
    return $text;
}

} // end of class

?>

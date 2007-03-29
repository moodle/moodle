<?php  // $Id$

require_once("$CFG->dirroot/enrol/enrol.class.php");
require_once("$CFG->dirroot/course/lib.php");
require_once("$CFG->dirroot/lib/blocklib.php");
require_once("$CFG->dirroot/lib/pagelib.php");


class enrolment_plugin_ldap {

    var $log;    
    
    var $enrol_localcoursefield = 'idnumber';

/// Overide the base get_student_courses() function
function get_student_courses(&$user) {
    global $CFG;
    return $this->get_user_courses($user, 'student');
}

/// Overide the base get_teacher_courses() function
function get_teacher_courses(&$user) {
    global $CFG;
    return $this->get_user_courses($user, 'teacher');
}

/// Overide the base get_student_courses() function
function get_user_courses(&$user, $type) {
    global $CFG;

    if(!isset($type) || !($type =='student' || $type =='teacher' )){
        error("Bad call to get_user_courses()!");
    }
    

    // Connect to the external database
    $ldap_connection = $this->enrol_ldap_connect();
    if (!$ldap_connection) {
        @ldap_close($ldap_connection);
        notify("LDAP-module cannot connect to server: $CFG->ldap_host_url");
        return false;
    }
    
    // we are connected OK, continue...

    /// Find a record in the external database that matches the local course field and local user field
    /// to the respective remote fields
    $enrolments = $this->find_ext_enrolments( $ldap_connection, 
                                    $user->idnumber , 
                                    $type);
    
    foreach ($enrolments as $enrol){
    
        $course_ext_id = $enrol[$CFG->enrol_ldap_course_idnumber][0];
        if(empty($course_ext_id)){
            error_log("The course external id is invalid!\n");
            continue; // next; skip this one!
        }
        
        // create the course  ir required
        $course_obj = get_record( 'course',
                                  $this->enrol_localcoursefield,
                                  $course_ext_id );

        if (empty($course_obj)){ // course doesn't exist
            if($CFG->enrol_ldap_autocreate){ // autocreate
                error_log("CREATE User $user->username enrolled to a nonexistant course $course_ext_id \n");
                $newcourseid = $this->create_course($enrol);
                $course_obj = get_record( 'course',
                                  $this->enrol_localcoursefield,
                                   $newcourseid);
            } else {
                error_log("User $user->username enrolled to a nonexistant course $course_ext_id \n");
            }
        } else { // the course object exists before we call...
            if ($course_obj->visible==0 && $user->{$type}[$course_obj->id] == 'ldap') {
                // non-visible courses don't show up in the enrolled 
                // array, so we should skip them -- 
                unset($user->{$type}[$course_obj->id]);
                continue;
            }
        }
        
        /// Add it to new list
        $newenrolments[$course_obj->id] = 'ldap';

        // deal with enrolment in the moodle db
        if (!empty($course_obj)) { // does course exist now?     
            if(isset($user->{$type}[$course_obj->id]) && $user->{$type}[$course_obj->id] == 'ldap'){
                unset($user->{$type}[$course_obj->id]); // remove from old list
            } else {
                $CFG->debug=10;
                if ($type === 'student') { // enrol
                   error_log("Enrolling student $user->id ($user->username) in course $course_obj->id ($course_obj->shortname) ");
                   if (!  enrol_student($user->id, $course_obj->id, 0, 0, 'ldap')){
                        error_log("Failed to enrol student $user->id ($user->username) into course $course_obj->id ($course_obj->shortname)");
                   }
                } else if ($type === 'teacher') {
                      error_log("Enrolling teacher $user->id ($user->username) in course $course_obj->id ($course_obj->shortname)");
                    add_teacher($user->id, $course_obj->id, 1, '', 0, 0,'ldap');
                }
                $CFG->debug=0;
            }
        }
    }

    // ok, if there's any thing still left in the $user->student or $user->teacher
    // array, those are old enrolments that we want to remove (or are they?)
    if(!empty($user->{$type})){
        foreach ($user->{$type} as $courseid => $value){
            if($value === 'ldap'){ // this was a legacy 
                if ($type === 'student') { // enrol
                    unenrol_student($user->id, $courseid);
                } else if ($type === 'teacher') {
                    remove_teacher($user->id, $courseid);
                }
                unset($user->{$type}[$course_obj->id]);
            } else {
                // This one is from a non-database
                // enrolment. Add it to the newenrolments
                // array, so we don't loose it.
                $newenrolments[$courseid] = $value;
            }

        }
    }

    /// Overwrite the old array with the new one
    $user->{$type} = $newenrolments;

    @ldap_close($ldap_connection);
    return true;
}

/// sync enrolments with ldap, create courses if required.
function sync_enrolments($type, $enrol) {
    global $CFG;

    if(!isset($type) || !($type =='student' || $type =='teacher' )){
        error("Bad call to get_all_courses()!");
    }
    $table = array( 'teacher' => 'user_teachers',
                    'student' => 'user_students');
    
    if(!isset($enrol)){
        $enrol=false;
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
    $ldap_contexts = explode(";",$CFG->{'enrol_ldap_'.$type.'_contexts'});

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
        array_push($ldap_fields_wanted, $CFG->{'enrol_ldap_'.$type.'_memberattribute'});
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

        $context == trim($context);
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
                                        $ldap_fields_wanted,0,0);
        }
 
        // check and push results
        $records = ldap_get_entries($ldap_connection,$ldap_result);        

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
                    // pull the ldap membership into a nice array
                    // this is an odd array -- mix of hash and array -- 
                    $ldapmembers=array();
                    if(!empty($course{ strtolower($CFG->{'enrol_ldap_'.$type.'_memberattribute'} ) })){ // may have no membership!

                        $ldapmembers = $course{ strtolower($CFG->{'enrol_ldap_'.$type.'_memberattribute'} ) }; 
                        unset($ldapmembers{'count'}); // remove oddity ;)
                    }
                    
                    // prune old ldap enrolments
                    // hopefully they'll fit in the max buffer size for the RDBMS
                    $sql = ' SELECT enr.userid AS user, 1
                             FROM ' . $CFG->prefix . $table{$type} . ' enr ' .
                           ' JOIN ' . $CFG->prefix . 'user  usr ON usr.id=enr.userid ' .
                           " WHERE course=$course_obj->id 
                                  AND enrol='ldap' ";
                    if (!empty($ldapmembers)) {
                        $sql .= 'AND usr.idnumber NOT IN (\''. join('\',\'', $ldapmembers).'\')';
                    } else {
                        print ("Empty enrolment for $course_obj->shortname \n");
                    }
                    $todelete = get_records_sql($sql);
                    if(!empty($todelete)){
                        foreach ($todelete as $member) {
                            $member = $member->{'user'};
                            if($type==='student'){
                                if (unenrol_student($member, $course_obj->id)){
                                    print "Unenrolled $type $member into course $course_obj->id ($course_obj->shortname)\n";
                                } else {
                                    print "Failed to unenrol $type $member into course $course_obj->id ($course_obj->shortname)\n";
                                }
                            } else {
                                if (remove_teacher($member, $course_obj->id)){
                                    print "Unenrolled $type $member into course $course_obj->id ($course_obj->shortname)\n";
                                } else {
                                    print "Failed to unenrol $type $member into course $course_obj->id ($course_obj->shortname)\n";
                                }
                            }
                        }
                    }
                    
                    // insert current enrolments 
                    // bad we can't do INSERT IGNORE with postgres...
                    foreach ($ldapmembers as $ldapmember) {
                        $sql = 'SELECT id,1 FROM '.$CFG->prefix.'user '
                                ." WHERE idnumber='$ldapmember'";
                        $member = get_record_sql($sql); 
//                        print "sql: $sql \nidnumber = $ldapmember \n" . var_dump($member); 
                        $member = $member->id;
                        if(empty($member)){
                            print "Could not find user $ldapmember, skipping\n";
                            continue;
                        }
                        if (!get_record($table{$type}, 'course', $course_obj->id, 
                                        'userid', $member, 'enrol', 'ldap')){
                            if($type === 'student'){
                                if (enrol_student($member, $course_obj->id, 0, 0, 'ldap')){
                                    print "Enrolled $type $member ($ldapmember) into course $course_obj->id ($course_obj->shortname)\n";
                                } else {
                                    print "Failed to enrol $type $member ($ldapmember) into course $course_obj->id ($course_obj->shortname)\n";
                                }
                            } else { // teacher
                                if (add_teacher($member, $course_obj->id, 1,'',0,0,'ldap')){
                                    print "Enrolled $type $member ($ldapmember) into course $course_obj->id ($course_obj->shortname)\n";
                                } else {
                                    print "Failed to enrol $type $member ($ldapmember) into course $course_obj->id ($course_obj->shortname)\n";
                                }
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
    include("$CFG->dirroot/enrol/ldap/config.html");
}

/// Override the base process_config() function
function process_config($config) {

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
    
    if (!isset ($config->enrol_ldap_student_contexts)) {
         $config->enrol_ldap_student_contexts = '';
    }
    set_config('enrol_ldap_student_contexts', $config->enrol_ldap_student_contexts);    
    
    if (!isset ($config->enrol_ldap_student_memberattribute)) {
         $config->enrol_ldap_student_memberattribute = '';
    }
    set_config('enrol_ldap_student_memberattribute', $config->enrol_ldap_student_memberattribute);    
    if (!isset ($config->enrol_ldap_teacher_contexts)) {
         $config->enrol_ldap_teacher_contexts = '';
    }
    set_config('enrol_ldap_teacher_contexts', $config->enrol_ldap_teacher_contexts);   
    
    if (!isset ($config->enrol_ldap_teacher_memberattribute)) {
         $config->enrol_ldap_teacher_memberattribute = '';
    }
    set_config('enrol_ldap_teacher_memberattribute', $config->enrol_ldap_teacher_memberattribute);    
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

function find_ext_enrolments ($ldap_connection, $memberuid, $type){
/// type is teacher or student
/// return multidimentional array array with of courses (at least dn and idnumber)
/// 

    global $CFG;

    //default return value
    $courses = array();
    $this->enrol_ldap_bind($ldap_connection);

    //get all contexts and look for first matching user
    $ldap_contexts = explode(";",$CFG->{'enrol_ldap_'.$type.'_contexts'});

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
    $ldap_search_pattern = "(".$CFG->{'enrol_ldap_'.$type.'_memberattribute'}."=".$memberuid.")";
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
    
    // set defaults
    $course = NULL;
    $course->student  = 'Student';
    $course->students = 'Students';
    $course->teacher  = 'Teacher';
    $course->teachers = 'Teachers';
    $course->format = 'topics';
    
    // override defaults with template course
    if(!empty($CFG->enrol_ldap_template)){
        $course = get_record("course", 'shortname', $CFG->enrol_ldap_template);
        unset($course->id); // so we are clear to reinsert the record
        unset($course->sortorder);
    }

    // override with required ext data
    $course->idnumber  = $course_ext[$CFG->enrol_ldap_course_idnumber][0];
    $course->fullname  = addslashes($course_ext[$CFG->enrol_ldap_course_fullname][0]);
    $course->shortname = addslashes($course_ext[$CFG->enrol_ldap_course_shortname][0]);
    if (   empty($course->idnumber)
        || empty($course->fullname)
        || empty($course->shortname) ) { 
        // we are in trouble!
        error_log("Cannot create course: missing required data from the LDAP record!");
         error_log(var_export($course, true));
        return false;
    }

    if(!empty($CFG->enrol_ldap_course_summary)){ // optional
        $course->summary   = addslashes($course_ext[$CFG->enrol_ldap_course_summary][0]);
    }
    if(!empty($CFG->enrol_ldap_category)){ // optional ... but ensure it is set!
        $course->category   = $CFG->enrol_ldap_category;
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
        add_to_log($newcourseid, "course", "new", "view.php?id=$newcourseid", "enrol/ldap auto-creation");
    } else {
        error_log("Could not create new course from LDAP from DN:" . $course_ext['dn']);
        notify("Serious Error! Could not create the new course!");
        return false;
    }
    
    return $newcourseid;
}

} // end of class

?>

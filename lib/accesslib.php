<?php
 /* capability session information format
  * 2 x 2 array
  * [context][capability]
  * where context is the context id of the table 'context'
  * and capability is a string defining the capability
  * e.g.
  *
  * [Capabilities] => [26][mod/forum:viewpost] = 1
  *                   [26][mod/forum:startdiscussion] = -8990
  *                   [26][mod/forum:editallpost] = -1
  *                   [273][moodle:blahblah] = 1
  *                   [273][moodle:blahblahblah] = 2
  */
 

// permission definitions
define('CAP_ALLOW', 1);
define('CAP_PREVENT', -1);
define('CAP_PROHIBIT', -1000);


// context definitions
define('CONTEXT_SYSTEM', 10);
define('CONTEXT_PERSONAL', 20);
define('CONTEXT_USERID', 30);
define('CONTEXT_COURSECAT', 40);
define('CONTEXT_COURSE', 50);
define('CONTEXT_GROUP', 60);
define('CONTEXT_MODULE', 70);
define('CONTEXT_BLOCK', 80);


/**
 * This functions get all the course categories in proper order
 * @param int $contextid
 * @param int $type
 * @return array of contextids
 */
function get_parent_cats($contextid, $type) {
    
    $parents = array();
    $context = get_record('context', 'id', $contextid);
    
    switch($type) {

        case CONTEXT_COURSECAT:
            
            $cat = get_record('course_categories','id',$context->instanceid);
            while ($cat->parent) {
              
                $context = get_context_instance(CONTEXT_COURSECAT, $cat->parent);
                $parents[] = $context->id;
                $cat = get_record('course_categories','id',$cat->parent);
            }

        break;
        
        case CONTEXT_COURSE:
        
            $course = get_record('course', 'id', $context->instanceid);
            $cat = get_record('course_categories','id',$course->category);        
            $catinstance = get_context_instance(CONTEXT_COURSECAT, $course->category);
            $parents[] = $catinstance->id;
            
            // what to do with cat 0?
            while ($cat->parent) {
                $context = get_context_instance(CONTEXT_COURSECAT, $cat->parent);
                $parents[] = $context->id;
                $cat = get_record('course_categories','id',$cat->parent);
            }
        break;
        
        default:
        break;

    }
    
    return array_reverse($parents);
}


/* Functions for Roles & Capabilites */


/**
 * This function returns whether the current user has the capability of performing a function
 * For example, we can do has_capability('mod/forum:replypost',$cm) in forum
 * only one of the 4 (moduleinstance, courseid, site, userid) would be set at 1 time
 * This is a recursive funciton.
 * Might change to require_capability, and throw an error if not authorized.
 * @uses $USER
 * @param string $capability - name of the capability
 * @param int $contextid
 * @param kill bool - if set, kill when the user has no capability
 * @return bool
 */
function has_capability($capability, $contextid, $kill=false, $userid=NULL) {

    global $USER;

    if ($userid && $userid != $USER->id) { // loading other user's capability
          $capabilities = load_user_capability($capability, $contextid, $userid);
    } else {
        $capabilities = $USER->capabilities;  
    }
    
    $context = get_record('context','id',$contextid);

    // Check site
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    if (isset($capabilities[$sitecontext->id]['moodle/site:doanything'])) {
        return ($capabilities[$sitecontext->id]['moodle/site:doanything']);
    }
    
    switch (context_level($contextid)) {
        
        case CONTEXT_COURSECAT:
            // Check parent cats.
            $parentcats = get_parent_cats($contextid, CONTEXT_COURSECAT);
            foreach ($parentcats as $parentcat) {
                if (isset($capabilities[$parentcat]['moodle/site:doanything'])) {
                    return ($capabilities[$parentcat]['moodle/site:doanything']);
                }      
            }
        break;

        case CONTEXT_COURSE:
            // Check parent cat.
            $parentcats = get_parent_cats($contextid, CONTEXT_COURSE);

            foreach ($parentcats as $parentcat) {
                if (isset($capabilities[$parentcat]['do_anything'])) {
                    return ($capabilities[$parentcat]['do_anything']);
                }      
            }
        break;

        case CONTEXT_GROUP:
            // Find course.
            $group = get_record('groups','id',$context->instanceid);
            $courseinstance = get_context_instance(CONTEXT_COURSE, $group->courseid);
            
            $parentcats = get_parent_cats($courseinstance->id, CONTEXT_COURSE);
            foreach ($parentcats as $parentcat) {
                if (isset($capabilities[$parentcat->id]['do_anything'])) {
                    return ($capabilities[$parentcat->id]['do_anything']);
                }      
            }    
            
            $coursecontext = '';
            if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                return ($capabilities[$courseinstance->id]['do_anything']);
            }            
            
        break;

        case CONTEXT_MODULE:
            // Find course.
            $cm = get_record('course_modules', 'id', $context->instanceid);
            $courseinstance = get_context_instance(CONTEXT_COURSE, $cm->course);
        
            if ($parentcats = get_parent_cats($courseinstance->id, CONTEXT_COURSE)) {
                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['do_anything'])) {
                        return ($capabilities[$parentcat]['do_anything']);
                    }      
                }    
            }    

            if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                return ($capabilities[$courseinstance->id]['do_anything']);
            }  

        break;

        case CONTEXT_BLOCK:
            // 1 to 1 to course.
            // Find course.
            $block = get_record('block_instance','id',$context->instanceid);
            $courseinstance = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            
            $parentcats = get_parent_cats($courseinstance->id, CONTEXT_COURSE);
            foreach ($parentcats as $parentcat) {
                if (isset($capabilities[$parentcat]['do_anything'])) {
                    return ($capabilities[$parentcat]['do_anything']);
                }      
            }    
            
            if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                return ($capabilities[$courseinstance->id]['do_anything']);
            }  
        break;

        default:
            // CONTEXT_SYSTEM: CONTEXT_PERSONAL: CONTEXT_USERID:
            // Do nothing.
        break;
    }

    // Last: check self.
    if (isset($capabilities[$contextid]['do_anything'])) {
        return ($capabilities[$contextid]['do_anything']);
    }
    
    // do_anything has not been set, we now look for it the normal way.
    return capability_search($capability, $contextid, $kill, $capabilities);

}    


/**
 * In a separate function so that we won't have to deal with do_anything.
 * again. Used by function has_capability.
 * @param $capability - capability string
 * @param $contextid - the context id
 * @param $kill - boolean. Error out and exit if the user doesn't have the
 *                capability?
 * @param $capabilities - either $USER->capability or loaded array
 * @return permission (int)
 */
function capability_search($capability, $contextid, $kill=false, $capabilities) {
    global $USER, $CFG;
    
    if ($CFG->debug) {
        notify("We are looking for $capability in context $contextid", 'notifytiny');
    }
    
    if (isset($capabilities[$contextid][$capability])) {
        return ($capabilities[$contextid][$capability]);
    }
    
    /* Then, we check the cache recursively */
    $context = get_record('context','id',$contextid); // shared
    $permission = 0;    
    
    switch (context_level($contextid)) {

        case CONTEXT_SYSTEM: // by now it's a definite an inherit
            $permission = 0;
        break;

        case CONTEXT_PERSONAL:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;
        
        case CONTEXT_USERID:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;
        
        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            $coursecat = get_record('course_categories','id',$context->instanceid);
            if ($coursecat->parent) { // return parent value if exist
                $parent = get_context_instance(CONTEXT_COURSECAT, $coursecat->parent);
            } else { // else return site value
                $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            }
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            // find the course cat, and return its value
            $course = get_record('course','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSECAT, $course->category);
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            $group = get_record('groups','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $group->courseid);
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            $cm = get_record('course_modules','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $cm->course);
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;

        case CONTEXT_BLOCK: // 1 to 1 to course
            $block = get_record('block_instance','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            $permission = (capability_search($capability, $parent->id, false, $capabilities));
        break;

        default:
            error ('This is an unknown context!');
        return false;
    }
    
    if ($kill && ($permission <= 0)) {
        error ('You do not have the required capability '.$capability);
      }
    return $permission;
}


/**
 * This function should be called immediately after a login, when $USER is set.
 * It will build an array of all the capabilities at each level
 * i.e. site/metacourse/course_category/course/moduleinstance
 * Note we should only load capabilities if they are explicitly assigned already,
 * we should not load all module's capability!
 * @param $userid - the id of the user whose capabilities we want to load
 * @return array
 * possible just s simple 2D array with [contextid][capabilityname]
 * [Capabilities] => [26][forum_post] = 1
 *                   [26][forum_start] = -8990
 *                   [26][forum_edit] = -1
 *                   [273][blah blah] = 1
 *                   [273][blah blah blah] = 2
 */
function load_user_capability($capability='', $contextid ='', $userid='') {
    
    global $USER, $CFG;

    if (empty($userid)) {
        $userid = $USER->id;
    } else {
        $otheruserid = $userid;  
    }
    
    if ($capability) {
        $capsearch = ' AND rc.capability = '.$capability.' ';
    } else {
        $capsearch ='';  
    }
    // First we generate a list of all relevant contexts of the user

    if ($contextid) { // if context is specified
        $context = get_record('context', 'id', $contextid);
    
        $usercontexts = get_parent_contexts($context->id);          
        $listofcontexts = '('.implode(',', $usercontexts).')';
    } else { // else, we load everything
        $usercontexts = get_records('role_assignments','userid',$userid);    
        $listofcontexts = '(';
        foreach ($usercontexts as $usercontext) {
            $listofcontexts .= $usercontext->contextid;
            $listofcontexts .= ',';
        }
        $listofcontexts = rtrim ($listofcontexts, ",");
        $listofcontexts .= ')';
    }
    
    // Then we use 1 giant SQL to bring out all relevant capabilities.
    // The first part gets the capabilities of orginal role.
    // The second part gets the capabilities of overriden roles.

    $siteinstance = get_context_instance(CONTEXT_SYSTEM, SITEID);

    $SQL = " SELECT  rc.capability, c1.id, (c1.level * 100) AS aggregatelevel,
                     SUM(rc.permission) AS sum
                     FROM
                     {$CFG->prefix}role_assignments AS ra, 
					 {$CFG->prefix}role_capabilities AS rc,
					 {$CFG->prefix}context AS c1
                     WHERE
					 ra.contextid=c1.id AND
					 ra.roleid=rc.roleid AND
                     ra.userid=$userid AND
                     c1.id IN $listofcontexts AND
                     rc.contextid=$siteinstance->id 
                     $capsearch
              GROUP BY
                     rc.capability,aggregatelevel,c1.id
                     HAVING
                     SUM(rc.permission) != 0
              UNION

              SELECT rc.capability, c1.id, (c1.level * 100 + c2.level) AS aggregatelevel,
                     SUM(rc.permission) AS sum
                     FROM
                     {$CFG->prefix}role_assignments AS ra,
                     {$CFG->prefix}role_capabilities AS rc,
                     {$CFG->prefix}context AS c1,
                     {$CFG->prefix}context AS c2
                     WHERE
					 ra.contextid=c1.id AND
					 ra.roleid=rc.roleid AND 
					 ra.userid=$userid AND		 
					 rc.contextid=c2.id AND             
                     c1.id IN $listofcontexts AND
                     c2.id IN $listofcontexts AND rc.contextid != $siteinstance->id
                     $capsearch
                  
              GROUP BY
                     rc.capability, aggregatelevel, c1.id
                     HAVING
                     SUM(rc.permission) != 0
              ORDER BY
                     aggregatelevel ASC
            ";


    $capabilities = array();  // Reinitialize.
    $rs = get_recordset_sql($SQL);
    
    if ($rs && $rs->RecordCount() > 0) {
        while (!$rs->EOF) {
              $array = $rs->fields;
              $temprecord = new object;
              
            foreach ($array as $key=>$val) {
                  $temprecord->{$key} = $val;
            }
            $capabilities[] = $temprecord;
            $rs->MoveNext();
        }
    }

    /* so up to this point we should have somethign like this
     * $capabilities[1]    ->aggregatelevel = 1000
                           ->module = SITEID
                           ->capability = do_anything
                           ->id = 1 (id is the context id)
                           ->sum = 0
                           
     * $capabilities[2]     ->aggregatelevel = 1000
                            ->module = SITEID
                            ->capability = post_messages
                            ->id = 1
                            ->sum = -9000

     * $capabilittes[3]     ->aggregatelevel = 3000
                            ->module = course
                            ->capability = view_course_activities
                            ->id = 25
                            ->sum = 1

     * $capabilittes[4]     ->aggregatelevel = 3000
                            ->module = course
                            ->capability = view_course_activities
                            ->id = 26
                            ->sum = 0 (this is another course)
                            
     * $capabilities[5]     ->aggregatelevel = 3050
                            ->module = course
                            ->capability = view_course_activities
                            ->id = 25 (override in course 25)
                            ->sum = -1
     * ....
     * now we proceed to write the session array, going from top to bottom
     * at anypoint, we need to go up and check parent to look for prohibit
     */
    // print_object($capabilities);

    /* This is where we write to the actualy capabilities array
     * what we need to do from here on is
     * going down the array from lowest level to highest level
     * 1) recursively check for prohibit,
     *  if any, we write prohibit
     *  else, we write the value
     * 2) at an override level, we overwrite current level
     *  if it's not set to prohibit already, and if different
     *  ........ that should be it ........
     */
    $usercap = array(); // for other user's capabilities
    foreach ($capabilities as $capability) {

        if (!empty($otheruserid)) { // we are pulling out other user's capabilities, do not write to session
            
            if (capability_prohibits($capability->capability, $capability->id, $capability->sum, $usercap)) {
                $usercap[$capability->id][$capability->capability] = -9000;
                continue;
            }

            $usercap[$capability->id][$capability->capability] = $capability->sum;          
          
        } else {

            if (capability_prohibits($capability->capability, $capability->id, $capability->sum)) { // if any parent or parent's parent is set to prohibit
                $USER->capabilities[$capability->id][$capability->capability] = -9000;
                continue;
            }
    
            // if no parental prohibit set
            // just write to session, i am not sure this is correct yet
            // since 3050 shows up after 3000, and 3070 shows up after 3050,
            // it should be ok just to overwrite like this, provided that there's no
            // parental prohibits
            // no point writing 0, since 0 = inherit
            // we need to write even if it's 0, because it could be an inherit override
            $USER->capabilities[$capability->id][$capability->capability] = $capability->sum;
        }
    }
    
    // now we don't care about the huge array anymore, we can dispose it.
    unset($capabilities);
    
    if (!empty($otheruseid)) {
        return $usercap; // return the array  
    }
    // see array in session to see what it looks like

}


/**
 * This is a recursive function that checks whether the capability in this
 * context, or the parent capabilities are set to prohibit.
 *
 * At this point, we can probably just use the values already set in the
 * session variable, since we are going down the level. Any prohit set in
 * parents would already reflect in the session.
 *
 * @param $capability - capability name
 * @param $sum - sum of all capabilities values
 * @param $contextid - the context id
 * @param $array - when loading another user caps, their caps are not stored in session but an array
 */
function capability_prohibits($capability, $contextid, $sum='', $array='') {
    global $USER;
    if ($sum < -8000) {
        // If this capability is set to prohibit.
        return true;
    }
    
    if (isset($array)) {
        if (isset($array[$contextid][$capability]) 
                && $array[$contextid][$capability] < -8000) {
            return true;
        }    
    } else {
        // Else if set in session.
        if (isset($USER->capabilities[$contextid][$capability]) 
                && $USER->capabilities[$contextid][$capability] < -8000) {
            return true;
        }
    }
    $context = get_record('context', 'id', $contextid);
    switch (context_level($contextid)) {
        
        case CONTEXT_SYSTEM:
            // By now it's a definite an inherit.
            return 0;
        break;

        case CONTEXT_PERSONAL:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            return (capability_prohibits($capability, $parent->id));
        break;

        case CONTEXT_USERID:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            return (capability_prohibits($capability, $parent->id));
        break;

        case CONTEXT_COURSECAT:
            // Coursecat -> coursecat or site.
            $coursecat = get_record('course_categories','id',$context->instanceid);
            if (!empty($coursecat->parent)) {
                // return parent value if exist.
                $parent = get_context_instance(CONTEXT_COURSECAT, $coursecat->parent);
            } else {
                // Return site value.
                $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            }
            return (capability_prohibits($capability, $parent->id));
        break;

        case CONTEXT_COURSE:
            // 1 to 1 to course cat.
            // Find the course cat, and return its value.
            $course = get_record('course','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSECAT, $course->category);
            return (capability_prohibits($capability, $parent->id));
        break;

        case CONTEXT_GROUP:
            // 1 to 1 to course.
            $group = get_record('groups','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $group->courseid);
            return (capability_prohibits($capability, $parent->id));
        break;

        case CONTEXT_MODULE:
            // 1 to 1 to course.
            $cm = get_record('course_modules','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $cm->course);
            return (capability_prohibits($capability, $parent->id));
        break;

        case CONTEXT_BLOCK:
            // 1 to 1 to course.
            $block = get_record('block_instance','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            return (capability_prohibits($capability, $parent->id));
        break;

        default:
            error ('This is an unknown context!');
        return false;
    }
}


/**
 * A print form function. This should either grab all the capabilities from
 * files or a central table for that particular module instance, then present
 * them in check boxes. Only relevant capabilities should print for known
 * context.
 * @param $mod - module id of the mod
 */
function print_capabilities($modid=0) {
    global $CFG;
    
    $capabilities = array();

    if ($modid) {
        // We are in a module specific context.

        // Get the mod's name.
        // Call the function that grabs the file and parse.
        $cm = get_record('course_modules', 'id', $modid);
        $module = get_record('modules', 'id', $cm->module);
        
    } else {
        // Print all capabilities.
        foreach ($capabilities as $capability) {
            // Prints the check box component.
        }
    }
}


/**
 * Installs the roles system.
 * This function runs on a fresh install as well as on an upgrade from the old
 * hard-coded student/teacher/admin etc. roles to the new roles system.
 */
function moodle_install_roles() {

    global $CFG, $db;
    
    // Create a system wide context for assignemnt.
    $systemcontext = $context = get_context_instance(CONTEXT_SYSTEM, SITEID);


    // Create default/legacy roles and capabilities.
    // (1 legacy capability per legacy role at system level).
    $adminrole = create_role(get_string('administrator'), get_string('administratordescription'), 'moodle/legacy:admin');   
    if (!assign_capability('moodle/site:doanything', CAP_ALLOW, $adminrole, $systemcontext->id)) {
        error('Could not assign moodle/site:doanything to the admin role');
    }
    $coursecreatorrole = create_role(get_string('coursecreators'), get_string('coursecreatorsdescription'), 'moodle/legacy:coursecreator');   
    $noneditteacherrole = create_role(get_string('noneditingteacher'), get_string('noneditingteacherdescription'), 'moodle/legacy:teacher');    
    $editteacherrole = create_role(get_string('defaultcourseteacher'), get_string('defaultcourseteacherdescription'), 'moodle/legacy:editingteacher');    
    $studentrole = create_role(get_string('defaultcoursestudent'), get_string('defaultcoursestudentdescription'), 'moodle/legacy:student');
    $guestrole = create_role(get_string('guest'), get_string('guestdescription'), 'moodle/legacy:guest');


    // Look inside user_admin, user_creator, user_teachers, user_students and
    // assign above new roles. If a user has both teacher and student role,
    // only teacher role is assigned. The assignment should be system level.
    $dbtables = $db->MetaTables('TABLES');
    

    /**
     * Upgrade the admins.
     * Sort using id ASC, first one is primary admin.
     */
    if (in_array($CFG->prefix.'user_admins', $dbtables)) {
        if ($useradmins = get_records_sql('SELECT * from '.$CFG->prefix.'user_admins ORDER BY ID ASC')) { 
            foreach ($useradmins as $admin) {
                role_assign($adminrole, $admin->userid, 0, $systemcontext->id);
            }
        }
    } else {
        // This is a fresh install.
    }


    /**
     * Upgrade course creators.
     */
    if (in_array($CFG->prefix.'user_coursecreators', $dbtables)) {
        if ($usercoursecreators = get_records('user_coursecreators')) {
            foreach ($usercoursecreators as $coursecreator) {
                role_assign($$coursecreatorrole, $coursecreator->userid, 0, $systemcontext->id);
            }
        }
    }


    /**
     * Upgrade editting teachers and non-editting teachers.
     */
    if (in_array($CFG->prefix.'user_teachers', $dbtables)) {
        if ($userteachers = get_records('user_teachers')) {
            foreach ($userteachers as $teacher) {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $teacher->course); // needs cache
                if ($teacher->editall) { // editting teacher
                    role_assign($editteacherrole, $teacher->userid, 0, $coursecontext->id);
                } else {
                    role_assign($noneditteacherrole, $teacher->userid, 0, $coursecontext->id);
                }
            }
        }
    }


    /**
     * Upgrade students.
     */
    if (in_array($CFG->prefix.'user_students', $dbtables)) {
        if ($userstudents = get_records('user_students')) {
            foreach ($userstudents as $student) {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $student->course);
                role_assign($studentrole, $student->userid, 0, $coursecontext->id);
            }
        }
    }


    /**
     * Upgrade guest (only 1 entry).
     */
    if ($guestuser = get_record('user', 'username', 'guest')) {
        role_assign($guestrole, $guestuser->id, 0, $systemcontext->id);
    }


    // Should we delete the tables after we are done? Not yet.
}


/**
 * Assign the defaults found in this capabality definition to roles that have
 * the corresponding legacy capabilities assigned to them.
 * @param $legacyperms - an array in the format (example):
 *                      'guest' => CAP_PREVENT,
 *                      'student' => CAP_ALLOW,
 *                      'teacher' => CAP_ALLOW,
 *                      'editingteacher' => CAP_ALLOW,
 *                      'coursecreator' => CAP_ALLOW,
 *                      'admin' => CAP_ALLOW
 * @return boolean - success or failure.
 */
function assign_legacy_capabilities($capability, $legacyperms) {
    
    foreach ($legacyperms as $type => $perm) {
        
        $systemcontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        
        // The legacy capabilities are:
        //   'moodle/legacy:guest'
        //   'moodle/legacy:student'
        //   'moodle/legacy:teacher'
        //   'moodle/legacy:editingteacher'
        //   'moodle/legacy:coursecreator'
        //   'moodle/legacy:admin'
        
        if (!$roles = get_roles_with_capability('moodle/legacy:'.$type, CAP_ALLOW)) {
            return false;
        }
        
        foreach ($roles as $role) {
            // Assign a site level capability.
            if(!assign_capability($capability, $perm, $role->id, $systemcontext->id)) {
                return false;
            }
        }
    }
    return true;
}


// checks to see if a capability is a legacy capability, returns bool
function islegacy($capabilityname) {
    if (strstr($capabilityname, 'legacy') === false) {
        return false;  
    } else {
        return true;  
    }
}

/************************************
 * Context Manipulation functions *
 **********************************/


/**
 * This should be called prolly everytime a user, group, module, course,
 * coursecat or site is set up maybe?
 * @param $level
 * @param $instanceid
 */
function create_context($level, $instanceid) {
    if (!get_record('context','level',$level,'instanceid',$instanceid)) {
        $context = new object;
        $context->level = $level;
        $context->instanceid = $instanceid;
        return insert_record('context',$context);
    }
}


/**
 * Get the context instance as an object. This function will create the
 * context instance if it does not exist yet.
 * @param $level
 * @param $instance
 */
function get_context_instance($level, $instance=SITEID) {

    static $contexts;   // Cache context lookups per page for performance

    if (!isset($contexts)) {
        $contexts = array();
    }

    if (isset($contexts[$level][$instance])) {  // Already cached
        return $contexts[$level][$instance];
    }

    if (!$context = get_record('context', 'level', $level, 'instanceid', $instance)) {
        create_context($level, $instance);
        $context = get_record('context', 'level', $level, 'instanceid', $instance);
    }

    $contexts[$level][$instance] = $context;    // Cache it for later

    return $context;
}


/**
 * Looks up the context level.
 * @param int $contextid
 * @return int
 */
function context_level($contextid) {
    $context = get_record('context','id',$contextid);
    return ($context->level);
}


/**
 * Get the local override (if any) for a given capability in a role in a context
 * @param $roleid
 * @param $instance
 */
function get_local_override($roleid, $contextid, $capability) {
    return get_record('role_capabilities', 'roleid', $roleid, 'capability', $capability, 'contextid', $contextid);
}



/************************************
 *    DB TABLE RELATED FUNCTIONS    *
 ************************************/

/**********************************************
 * function that creates a role
 * @param name - role name
 * @param description - role description
 * @param legacy - optional legacy capability
 * @return id or false
 */
function create_role($name, $description, $legacy='') {
          
    // check for duplicate role name
                
    if ($role = get_record('role','name', $name)) {
          print_object($role);
        error('there is already a role with this name!');  
    }
    
    $role->name = $name;
    $role->description = $description;
                                
    if ($id = insert_record('role', $role)) {
        if ($legacy) {
            $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
            assign_capability($legacy, CAP_ALLOW, $id, $context->id);            
        }
        return $id;
    } else {
        return false;  
    }
  
}

/**
 * Function to write context specific overrides, or default capabilities.
 * @param module - string name
 * @param capability - string name
 * @param contextid - context id
 * @param roleid - role id
 * @param permission - int 1,-1 or -1000
 */
function assign_capability($capability, $permission, $roleid, $contextid) {
    
    global $USER;
    
    if (empty($permission) || $permission == 0) { // if permission is not set
        unassign_capability($capability, $roleid, $contextid);      
    }
    
    $cap = new object;
    $cap->contextid = $contextid;
    $cap->roleid = $roleid;
    $cap->capability = $capability;
    $cap->permission = $permission;
    $cap->timemodified = time();
    if ($USER->id) {
        $cap->modifierid = $USER->id;
    } else {
        $cap->modifierid = -1;  // Happens during fresh install or Moodle.
    }
    
    return insert_record('role_capabilities', $cap);
}


/**
 * Unassign a capability from a role.
 * @param $roleid - the role id
 * @param $capability - the name of the capability
 * @return boolean - success or failure
 */
function unassign_capability($capability, $roleid, $contextid=NULL) {
    
    if (isset($contextid)) {
        $status = delete_records('role_capabilities', 'capability', $capability,
                'roleid', $roleid, 'contextid', $contextid);
    } else {
        $status = delete_records('role_capabilities', 'capability', $capability,
                'roleid', $roleid);
    }
    return $status;
}


/**
 * Get the roles that have a given capability.
 * @param $capability - capability name (string)
 * @param $permission - optional, the permission defined for this capability
 *                      either CAP_ALLOW, CAP_PREVENT or CAP_PROHIBIT
 * @return array or role objects
 */
function get_roles_with_capability($capability, $permission=NULL) {
    
    global $CFG;
    
    $selectroles = "SELECT r.* 
                      FROM {$CFG->prefix}role AS r,
                           {$CFG->prefix}role_capabilities AS rc
                     WHERE rc.capability = '$capability'
                       AND rc.roleid = r.id";

    if (isset($permission)) {
        $selectroles .= " AND rc.permission = '$permission'";
    }
    return get_records_sql($selectroles);
}


/**
 * This function makes a role-assignment (user to a role)
 * @param $roleid - the role of the id
 * @param $userid - userid
 * @param $groupid - group id
 * @param $contextid - id of the context
 * @param $timestart - time this assignment becomes effective
 * @param $timeend - time this assignemnt ceases to be effective
 * @uses $USER
 * @return id - new id of the assigment
 */
function role_assign($roleid, $userid, $groupid, $contextid, $timestart=0, $timeend=0, $hidden=0) {
    global $USER, $CFG;

    if ($CFG->debug) {
        notify("Assign roleid $roleid userid $userid contextid $contextid", 'notifytiny');
    }

    if (empty($roleid)) {
        error ('you need to select a role');
    }

    if (empty($userid) && empty($groupid)) {
        error ('you need to assign this role to a user or a group');
    }

    if (empty($contextid)) {
        error ('you need to assign this role to a context, e.g. a course, or an activity');
    }

    $ra = new object;
    $ra->roleid = $roleid;
    $ra->contextid = $contextid;
    $ra->userid = $userid;
    $ra->hidden = $hidden;
    $ra->groupid = $groupid;
    $ra->timestart = $timestart;
    $ra->timeend = $timeend;
    $ra->timemodified = time();
    $ra->modifier = $USER->id;
    
    return insert_record('role_assignments', $ra);

}


/**
 * Deletes a role assignment.
 * @param $roleid
 * @param $userid
 * @param $groupid
 * @param $contextid
 * @return boolean - success or failure
 */
function role_unassign($roleid, $userid, $groupid, $contextid) {
    if ($groupid) {
        // do nothing yet as this is not implemented
    } 
    else {
          return delete_records('role_assignments', 'userid', $userid, 
                  'roleid', $roleid, 'contextid', $contextid); 
    } 
}


/**
 * Loads the capability definitions for the component (from file). If no
 * capabilities are defined for the component, we simply return an empty array.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of capabilities
 */
function load_capability_def($component) {
    global $CFG;

    if ($component == 'moodle') {
        $defpath = $CFG->libdir.'/db/access.php';
        $varprefix = 'moodle';
    } else {
        $defpath = $CFG->dirroot.'/'.$component.'/db/access.php';
        $varprefix = str_replace('/', '_', $component);
    }
    $capabilities = array();
    
    if (file_exists($defpath)) {
        require_once($defpath);
        $capabilities = ${$varprefix.'_capabilities'};
    }
    return $capabilities;
}


/**
 * Gets the capabilities that have been cached in the database for this
 * component.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of capabilities
 */
function get_cached_capabilities($component='moodle') {
    if ($component == 'moodle') {
        $storedcaps = get_records_select('capabilities',
                        "name LIKE 'moodle/%:%'");
    } else {
        $storedcaps = get_records_select('capabilities',
                        "name LIKE '$component:%'");
    }
    return $storedcaps;
}


/**
 * Updates the capabilities table with the component capability definitions.
 * If no parameters are given, the function updates the core moodle
 * capabilities.
 *
 * Note that the absence of the db/access.php capabilities definition file
 * will cause any stored capabilities for the component to be removed from
 * the database. 
 *
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean
 */
function update_capabilities($component='moodle') {
    
    $storedcaps = array();
    $filecaps = array();
    
    $cachedcaps = get_cached_capabilities($component);
    if ($cachedcaps) {
        foreach ($cachedcaps as $cachedcap) {
            array_push($storedcaps, $cachedcap->name);
        }
    }
    
    $filecaps = load_capability_def($component);
    
    // Are there new capabilities in the file definition?
    $newcaps = array();
    
    foreach ($filecaps as $filecap => $def) {
        if (!$storedcaps || 
                ($storedcaps && in_array($filecap, $storedcaps) === false)) {
            $newcaps[$filecap] = $def;
        }
    }
    // Add new capabilities to the stored definition.
    foreach ($newcaps as $capname => $capdef) {
        $capability = new object;
        $capability->name = $capname;
        $capability->captype = $capdef['captype'];
        $capability->contextlevel = $capdef['contextlevel'];
        $capability->component = $component;
        
        if (!insert_record('capabilities', $capability, false, 'id')) {
            return false;
        }
        // Do we need to assign the new capabilities to roles that have the
        // legacy capabilities moodle/legacy:* as well?
        if (isset($capdef['legacy']) && is_array($capdef['legacy']) &&
                    !assign_legacy_capabilities($capname, $capdef['legacy'])) {
            error('Could not assign legacy capabilities');
            return false;
        }
    }
    // Are there any capabilities that have been removed from the file
    // definition that we need to delete from the stored capabilities and
    // role assignments?
    capabilities_cleanup($component, $filecaps);
    
    return true;
}


/**
 * Deletes cached capabilities that are no longer needed by the component.
 * Also unassigns these capabilities from any roles that have them.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @param $newcapdef - array of the new capability definitions that will be
 *                     compared with the cached capabilities
 * @return int - number of deprecated capabilities that have been removed
 */
function capabilities_cleanup($component, $newcapdef=NULL) {
    
    $removedcount = 0;
    
    if ($cachedcaps = get_cached_capabilities($component)) {
        foreach ($cachedcaps as $cachedcap) {
            if (empty($newcapdef) ||
                        array_key_exists($cachedcap->name, $newcapdef) === false) {
            
                // Remove from capabilities cache.
                if (!delete_records('capabilities', 'name', $cachedcap->name)) {
                    error('Could not delete deprecated capability '.$cachedcap->name);
                } else {
                    $removedcount++;
                }
                // Delete from roles.
                if($roles = get_roles_with_capability($cachedcap->name)) {
                    foreach($roles as $role) {
                        if (!unassign_capability($role->id, $cachedcap->name)) {
                            error('Could not unassign deprecated capability '.
                                    $cachedcap->name.' from role '.$role->name);
                        }
                    }
                }
            } // End if.
        }
    }
    return $removedcount;
}




/************************************************************
 *                     * UI FUNCTIONS *                     *
 ************************************************************/


/**
 * prints human readable context identifier.
 */
function print_context_name($contextid) {
  
    $name = '';

    $context = get_record('context', 'id', $contextid);  

      switch ($context->level) {
      
        case CONTEXT_SYSTEM: // by now it's a definite an inherit
            $name = get_string('site');
        break;

        case CONTEXT_PERSONAL:
            $name = get_string('personal');
        break;
        
        case CONTEXT_USERID:
            if ($user = get_record('user', 'id', $context->instanceid)) {
                $name = get_string('user').': '.fullname($user);
            }
        break;
        
        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            if ($category = get_record('course_categories', 'id', $context->instanceid)) {
                $name = get_string('category').': '.$category->name;
            }
        break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            if ($course = get_record('course', 'id', $context->instanceid)) {
                $name = get_string('course').': '.$course->fullname;
            }
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            if ($group = get_record('groups', 'id', $context->instanceid)) {
                $name = get_string('group').': '.$group->name;
            }
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            if ($cm = get_record('course_modules','id',$context->instanceid)) {
                if ($module = get_record('modules','id',$cm->module)) {
                    if ($mod = get_record($module->name, 'id', $cm->instance)) {
                        $name = get_string('activitymodule').': '.$mod->name;
                    }
                }
            }
        break;

        case CONTEXT_BLOCK: // 1 to 1 to course
            if ($blockinstance = get_record('block_instance','id',$context->instanceid)) {
                if ($block = get_record('block','id',$blockinstance->blockid)) {
                    $name = get_string('blocks').': '.get_string($block->name, 'block_'.$block->name);
                }
            }
        break;

        default:
            error ('This is an unknown context!');
        return false;
              
      }
  
      return $name;
}


/**
 * Extracts the relevant capabilities given a contextid. 
 * All case based, example an instance of forum context.
 * Will fetch all forum related capabilities, while course contexts
 * Will fetch all capabilities
 * @param int contextid
 * @return array();
 *
 *  capabilities
 * `name` varchar(150) NOT NULL,
 * `captype` varchar(50) NOT NULL,
 * `contextlevel` int(10) NOT NULL,
 * `component` varchar(100) NOT NULL,
 */
function fetch_context_capabilities($contextid) {
      
    global $CFG;

    $sort = 'ORDER BY contextlevel,component,id';   // To group them sensibly for display
      
    switch (context_level($contextid)) {

        case CONTEXT_SYSTEM: // all
            $SQL = "select * from {$CFG->prefix}capabilities";
        break;

        case CONTEXT_PERSONAL:
        break;
        
        case CONTEXT_USERID:
        break;
        
        case CONTEXT_COURSECAT: // all
            $SQL = "select * from {$CFG->prefix}capabilities";
        break;

        case CONTEXT_COURSE: // all
            $SQL = "select * from {$CFG->prefix}capabilities";
        break;

        case CONTEXT_GROUP: // group caps
        break;

        case CONTEXT_MODULE: // mod caps
            $context = get_record('context','id',$contextid);
            $cm = get_record('course_modules', 'id', $context->instanceid);
            $module = get_record('modules', 'id', $cm->module);
        
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_MODULE."
                    and component = 'mod/$module->name'";
        break;

        case CONTEXT_BLOCK: // block caps
            $context = get_record('context','id',$contextid);
            $cb = get_record('block_instance', 'id', $context->instanceid);
            $block = get_record('block', 'id', $cb->blockid);
        
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_BLOCK."
                    and component = 'block/$block->name'";
        break;

        default:
        return false;
    }

    $records = get_records_sql($SQL.' '.$sort);
    return $records;
    
}


/**
 * This function pulls out all the resolved capabilities (overrides and
 * defaults) of a role used in capability overrieds in contexts at a given
 * context.
 * @param int $contextid
 * @param int $roleid
 * @return array
 */
function role_context_capabilities($roleid, $contextid) {
    global $CFG; 
    
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    if ($sitecontext->id == $contextid) {
        return array();  
    }
    
    // first of all, figure out all parental contexts
    $context = get_record('context', 'id', $contextid);
    $contexts = array_reverse(get_parent_contexts($context));
    $contexts = '('.implode(',', $contexts).')';
    
    $SQL = "SELECT rc.* FROM {$CFG->prefix}role_capabilities rc, {$CFG->prefix}context c
            where rc.contextid in $contexts
            and rc.roleid = $roleid
            and rc.contextid = c.id
            ORDER BY c.level DESC, rc.capability DESC";
            
    $records = get_records_sql($SQL);
    
    $capabilities = array();
    
    // We are traversing via reverse order.
    foreach ($records as $record) {
          // If not set yet (i.e. inherit or not set at all), or currently we have a prohibit
        if (!isset($capabilities[$record->capability]) || $record->permission<-500) {
            $capabilities[$record->capability] = $record->permission;
        }  
    }
    return $capabilities;
}


/**
 * Recursive function which, given a contextid, find all parent context ids, 
 * and return the array in reverse order, i.e. parent first, then grand
 * parent, etc.
 * @param object $context
 * @return array()
 */
 
 
function get_parent_contexts($context) {
  
    switch (context_level($context->id)) {

        case CONTEXT_SYSTEM: // no parent
            return null;
        break;

        case CONTEXT_PERSONAL:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            return array($parent->id);
        break;
        
        case CONTEXT_USERID:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            return array($parent->id);
        break;
        
        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            $coursecat = get_record('course_categories','id',$context->instanceid);
            if ($coursecat->parent) { // return parent value if exist
                $parent = get_context_instance(CONTEXT_COURSECAT, $coursecat->parent);
                return array_merge(array($parent->id), get_parent_contexts($parent));
            } else { // else return site value
                $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
                return array($parent->id);
            }
        break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            // find the course cat, and return its value
            $course = get_record('course','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSECAT, $course->category);
            return array_merge(array($parent->id), get_parent_contexts($parent));
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            $group = get_record('groups','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $group->courseid);
            return array_merge(array($parent->id), get_parent_contexts($parent));
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            $cm = get_record('course_modules','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $cm->course);
            return array_merge(array($parent->id), get_parent_contexts($parent));
        break;

        case CONTEXT_BLOCK: // 1 to 1 to course
            $block = get_record('block_instance','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            return array_merge(array($parent->id), get_parent_contexts($parent));
        break;

        default:
            error ('This is an unknown context!');
        return false;
    }
  
}


/**
 * This function gets the capability of a role in a given context.
 * It is needed when printing override forms.
 * @param int $contextid
 * @param int $roleid // no need? since role is used in extraction in $capability
 * @param string $capability
 * @param array $capabilities - array loaded using role_context_capabilities
 * @return int (allow, prevent, prohibit, inherit)
 */
 
 
function get_role_context_capability($contextid, $capability, $capabilities) {
    return $capabilities[$contextid][$capability];
}


// a big switch statement
function get_capability_string($capabilityname) {

    // Typical capabilityname is:   mod/choice:readresponses

    $names = split('/', $capabilityname);
    $stringname = $names[1];                 // choice:readresponses
    $components = split(':', $stringname);   
    $componentname = $components[0];               // choice

    switch ($names[0]) {
        case 'mod':
            $string = get_string($stringname, $componentname);
        break;
        
        case 'block':
            $string = get_string($stringname, 'block_'.$componentname);
        break;

        case 'moodle':
            $string = get_string($stringname, 'role');
        break;
        
        case 'enrol':
            $string = get_string($stringname, 'enrol_'.$componentname);
        break;  
        
        default:
            $string = get_string($stringname);
        break;  
      
    }

    return $string;
}


// this gets the mod/block/course/core etc strings
function get_component_string($component, $contextlevel) {

    switch ($contextlevel) {

        case CONTEXT_SYSTEM:
            $string = get_string('coresystem');
        break;

        case CONTEXT_PERSONAL:
            $string = get_string('personal');
        break;

        case CONTEXT_USERID:
            $string = get_string('users');
        break;

        case CONTEXT_COURSECAT:
            $string = get_string('categories');
        break;

        case CONTEXT_COURSE:
            $string = get_string('course');
        break;

        case CONTEXT_GROUP:
            $string = get_string('group');
        break;

        case CONTEXT_MODULE:
            $string = get_string('modulename', basename($component));
        break;

        case CONTEXT_BLOCK:
            $string = get_string('blockname', 'block_'.$component.'.php');
        break;

        default:
            error ('This is an unknown context!');
        return false;
      
    }
    
    return $string;
  
}
?>

<?php
 /**
  * Capability session information format
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

$context_cache    = array();    // Cache of all used context objects for performance (by level and instance)
$context_cache_id = array();    // Index to above cache by id


/**
 * Loads the capabilities for the default guest role to the current user in a specific context
 * @return object
 */
function load_guest_role($context=NULL) {
    global $USER;

    static $guestrole;

    if (!isloggedin()) {
        return false;
    }

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID)) {
        return false;
    }

    if (empty($context)) {
        $context = $sitecontext;
    }

    if (empty($guestrole)) {
        if ($roles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
            $guestrole = array_shift($roles);   // Pick the first one
        } else {
            return false;
        }
    }

    if ($capabilities = get_records_select('role_capabilities', 
                                           "roleid = $guestrole->id AND contextid = $sitecontext->id")) {
        foreach ($capabilities as $capability) {
            $USER->capabilities[$context->id][$capability->capability] = $capability->permission;     
        }
    }

    return true;
}

/**
 * Load default not logged in role capabilities when user is not logged in
 * @return bool 
 */
function load_notloggedin_role() {
    global $CFG, $USER;

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID)) {
        return false;
    }

    if (empty($CFG->notloggedinroleid)) {    // Let's set the default to the guest role
        if ($roles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
            $role = array_shift($roles);   // Pick the first one
            set_config('notloggedinroleid', $role->id);
        } else {
            return false;
        }
    }

    if ($capabilities = get_records_select('role_capabilities', 
                                     "roleid = $CFG->notloggedinroleid AND contextid = $sitecontext->id")) {
        foreach ($capabilities as $capability) {
            $USER->capabilities[$sitecontext->id][$capability->capability] = $capability->permission;     
        }
    }

    return true;
}

/**
 * This functions get all the course categories in proper order
 * @param int $context
 * @param int $type
 * @return array of contextids
 */
function get_parent_cats($context, $type) {
    
    $parents = array();
    
    switch ($type) {

        case CONTEXT_COURSECAT:
            
            if (!$cat = get_record('course_categories','id',$context->instanceid)) {
                break;
            }

            while (!empty($cat->parent)) {
                if (!$context = get_context_instance(CONTEXT_COURSECAT, $cat->parent)) {
                    break;
                }
                $parents[] = $context->id;
                $cat = get_record('course_categories','id',$cat->parent);
            }

        break;
        
        case CONTEXT_COURSE:
        
            if (!$course = get_record('course', 'id', $context->instanceid)) {
                break;
            }
            if (!$catinstance = get_context_instance(CONTEXT_COURSECAT, $course->category)) {
                break;
            }

            $parents[] = $catinstance->id;

            if (!$cat = get_record('course_categories','id',$course->category)) {
                break;
            }

            while (!empty($cat->parent)) {
                if (!$context = get_context_instance(CONTEXT_COURSECAT, $cat->parent)) {
                    break;
                }
                $parents[] = $context->id;
                $cat = get_record('course_categories','id',$cat->parent);
            }
        break;
        
        default:
        break;

    }
    
    return array_reverse($parents);
}



/*************************************
 * Functions for Roles & Capabilites *
 *************************************/


/**
 * This function checks for a capability assertion being true.  If it isn't
 * then the page is terminated neatly with a standard error message
 * @param string $capability - name of the capability
 * @param object $context - a context object (record from context table)
 * @param integer $userid - a userid number
 * @param bool $doanything - if false, ignore do anything
 * @param string $errorstring - an errorstring
 * @param string $stringfile - which stringfile to get it from
 */
function require_capability($capability, $context=NULL, $userid=NULL, $doanything=true, 
                            $errormessage="nopermissions", $stringfile='') {

    global $USER;

/// If the current user is not logged in, then make sure they are

    if (empty($userid) and empty($USER->id)) {
        if ($context && ($context->aggregatelevel == CONTEXT_COURSE)) {
            require_login($context->instanceid);
        } else {
            require_login();
        }
    }
   
/// OK, if they still don't have the capability then print a nice error message

    if (!has_capability($capability, $context, $userid, $doanything)) {
        $capabilityname = get_capability_string($capability);
        print_error($errormessage, $stringfile, '', $capabilityname);
    }
}


/**
 * This function returns whether the current user has the capability of performing a function
 * For example, we can do has_capability('mod/forum:replypost',$cm) in forum
 * only one of the 4 (moduleinstance, courseid, site, userid) would be set at 1 time
 * This is a recursive funciton.
 * @uses $USER
 * @param string $capability - name of the capability
 * @param object $context - a context object (record from context table)
 * @param integer $userid - a userid number
 * @param bool $doanything - if false, ignore do anything
 * @return bool
 */
function has_capability($capability, $context=NULL, $userid=NULL, $doanything=true) {

    global $USER, $CONTEXT, $CFG;

    if (empty($userid) && !isloggedin() && !isset($USER->capabilities)) {
        load_notloggedin_role();
    }

    if ($userid && $userid != $USER->id) {
        if (empty($USER->id) or ($userid != $USER->id)) {
            $capabilities = load_user_capability($capability, $context, $userid);
        } else { //$USER->id == $userid
            $capabilities = empty($USER->capabilities) ? NULL : $USER->capabilities;
        }
    } else { // no userid
        $capabilities = empty($USER->capabilities) ? NULL : $USER->capabilities;
    }

    if (empty($context)) {                 // Use default CONTEXT if none specified
        if (empty($CONTEXT)) {
            return false;
        } else {
            $context = $CONTEXT;
        }
    } else {                               // A context was given to us
        if (empty($CONTEXT)) {
            $CONTEXT = $context;           // Store FIRST used context in this global as future default
        }
    }

    if ($doanything) {
        // Check site
        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        if (isset($capabilities[$sitecontext->id]['moodle/site:doanything'])) {
            return (0 < $capabilities[$sitecontext->id]['moodle/site:doanything']);
        }
    
        switch ($context->aggregatelevel) {
        
            case CONTEXT_COURSECAT:
                // Check parent cats.
                $parentcats = get_parent_cats($context, CONTEXT_COURSECAT);
                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['moodle/site:doanything'])) {
                        return (0 < $capabilities[$parentcat]['moodle/site:doanything']);
                    }
                }
            break;

            case CONTEXT_COURSE:
                // Check parent cat.
                $parentcats = get_parent_cats($context, CONTEXT_COURSE);

                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['do_anything'])) {
                        return (0 < $capabilities[$parentcat]['do_anything']);
                    }
                }
            break;

            case CONTEXT_GROUP:
                // Find course.
                $group = get_record('groups','id',$context->instanceid);
                $courseinstance = get_context_instance(CONTEXT_COURSE, $group->courseid);

                $parentcats = get_parent_cats($courseinstance, CONTEXT_COURSE);
                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat->id]['do_anything'])) {
                        return (0 < $capabilities[$parentcat->id]['do_anything']);
                    }
                }

                $coursecontext = '';
                if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                    return (0 < $capabilities[$courseinstance->id]['do_anything']);
                }

            break;

            case CONTEXT_MODULE:
                // Find course.
                $cm = get_record('course_modules', 'id', $context->instanceid);
                $courseinstance = get_context_instance(CONTEXT_COURSE, $cm->course);

                if ($parentcats = get_parent_cats($courseinstance, CONTEXT_COURSE)) {
                    foreach ($parentcats as $parentcat) {
                        if (isset($capabilities[$parentcat]['do_anything'])) {
                            return (0 < $capabilities[$parentcat]['do_anything']);
                        }
                    }
                }

                if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                    return (0 < $capabilities[$courseinstance->id]['do_anything']);
                }

            break;

            case CONTEXT_BLOCK:
                // 1 to 1 to course.
                // Find course.
                $block = get_record('block_instance','id',$context->instanceid);
                $courseinstance = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check

                $parentcats = get_parent_cats($courseinstance, CONTEXT_COURSE);
                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['do_anything'])) {
                        return (0 < $capabilities[$parentcat]['do_anything']);
                    }
                }

                if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                    return (0 < $capabilities[$courseinstance->id]['do_anything']);
                }
            break;

            default:
                // CONTEXT_SYSTEM: CONTEXT_PERSONAL: CONTEXT_USERID:
                // Do nothing.
            break;
        }

        // Last: check self.
        if (isset($capabilities[$context->id]['do_anything'])) {
            return (0 < $capabilities[$context->id]['do_anything']);
        }
    }
    // do_anything has not been set, we now look for it the normal way.
    return (0 < capability_search($capability, $context, $capabilities));

}


/**
 * In a separate function so that we won't have to deal with do_anything.
 * again. Used by function has_capability.
 * @param $capability - capability string
 * @param $context - the context object
 * @param $capabilities - either $USER->capability or loaded array
 * @return permission (int)
 */
function capability_search($capability, $context, $capabilities) {
   
    global $USER, $CFG;

    if (isset($capabilities[$context->id][$capability])) {
        debugging("Found $capability in context $context->id at level $context->aggregatelevel: ".$capabilities[$context->id][$capability], E_ALL);
        return ($capabilities[$context->id][$capability]);
    }

    /* Then, we check the cache recursively */
    $permission = 0;

    switch ($context->aggregatelevel) {

        case CONTEXT_SYSTEM: // by now it's a definite an inherit
            $permission = 0;
        break;

        case CONTEXT_PERSONAL:
            $parentcontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        case CONTEXT_USERID:
            $parentcontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            $coursecat = get_record('course_categories','id',$context->instanceid);
            if (!empty($coursecat->parent)) { // return parent value if it exists
                $parentcontext = get_context_instance(CONTEXT_COURSECAT, $coursecat->parent);
            } else { // else return site value
                $parentcontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
            }
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            // find the course cat, and return its value
            $course = get_record('course','id',$context->instanceid);
            $parentcontext = get_context_instance(CONTEXT_COURSECAT, $course->category);
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            $group = get_record('groups','id',$context->instanceid);
            $parentcontext = get_context_instance(CONTEXT_COURSE, $group->courseid);
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            $cm = get_record('course_modules','id',$context->instanceid);
            $parentcontext = get_context_instance(CONTEXT_COURSE, $cm->course);
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        case CONTEXT_BLOCK: // 1 to 1 to course
            $block = get_record('block_instance','id',$context->instanceid);
            $parentcontext = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            $permission = capability_search($capability, $parentcontext, $capabilities);
        break;

        default:
            error ('This is an unknown context!');
        return false;
    }
    debugging("Found $capability recursively from context $context->id at level $context->aggregatelevel: $permission", E_ALL);

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
function load_user_capability($capability='', $context ='', $userid='') {

    global $USER, $CFG;

    
    if (empty($userid)) {
        if (empty($USER->id)) {               // We have no user to get capabilities for
            return false;
        }
        if (!empty($USER->capabilities)) {    // make sure it's cleaned when loaded (again)
            unset($USER->capabilities);  
        }
        $userid = $USER->id;
        $otheruserid = false;
    } else {
        $otheruserid = $userid;
    }

    if ($capability) {
        $capsearch = " AND rc.capability = '$capability' ";
    } else {
        $capsearch ="";  
    }

/// First we generate a list of all relevant contexts of the user

    $usercontexts = array();

    if ($context) { // if context is specified
        $usercontexts = get_parent_contexts($context);          
    } else { // else, we load everything
        if ($userroles = get_records('role_assignments','userid',$userid)) {
            foreach ($userroles as $userrole) {
                $usercontexts[] = $userrole->contextid;
            }
        }
    }

/// Set up SQL fragments for searching contexts

    if ($usercontexts) {
        $listofcontexts = '('.implode(',', $usercontexts).')';
        $searchcontexts1 = "c1.id IN $listofcontexts AND";
        $searchcontexts2 = "c2.id IN $listofcontexts AND";
    } else {
        $listofcontexts = $searchcontexts1 = $searchcontexts2 = '';
    }

/// Then we use 1 giant SQL to bring out all relevant capabilities.
/// The first part gets the capabilities of orginal role.
/// The second part gets the capabilities of overriden roles.

    $siteinstance = get_context_instance(CONTEXT_SYSTEM, SITEID);

    $SQL = " SELECT  rc.capability, c1.id, (c1.aggregatelevel * 100) AS aggrlevel,
                     SUM(rc.permission) AS sum
                     FROM
                     {$CFG->prefix}role_assignments ra, 
                     {$CFG->prefix}role_capabilities rc,
                     {$CFG->prefix}context c1
                     WHERE
                     ra.contextid=c1.id AND
                     ra.roleid=rc.roleid AND
                     ra.userid=$userid AND
                     $searchcontexts1
                     rc.contextid=$siteinstance->id 
                     $capsearch
              GROUP BY
                     rc.capability, (c1.aggregatelevel * 100), c1.id
                     HAVING
                     SUM(rc.permission) != 0
              UNION

              SELECT rc.capability, c1.id, (c1.aggregatelevel * 100 + c2.aggregatelevel) AS aggrlevel,
                     SUM(rc.permission) AS sum
                     FROM
                     {$CFG->prefix}role_assignments ra,
                     {$CFG->prefix}role_capabilities rc,
                     {$CFG->prefix}context c1,
                     {$CFG->prefix}context c2
                     WHERE
                     ra.contextid=c1.id AND
                     ra.roleid=rc.roleid AND 
                     ra.userid=$userid AND         
                     rc.contextid=c2.id AND             
                     $searchcontexts1
                     $searchcontexts2
                     rc.contextid != $siteinstance->id
                     $capsearch
                  
              GROUP BY
                     rc.capability, (c1.aggregatelevel * 100 + c2.aggregatelevel), c1.id
                     HAVING
                     SUM(rc.permission) != 0
              ORDER BY
                     aggrlevel ASC
            ";

    $capabilities = array();  // Reinitialize.
    if (!$rs = get_recordset_sql($SQL)) {
        error("Query failed in load_user_capability.");
    }

    if ($rs && $rs->RecordCount() > 0) {
        while (!$rs->EOF) {
            $array = $rs->fields;
            $temprecord = new object;
              
            foreach ($array as $key=>$val) {
                if ($key == 'aggrlevel') {
                    $temprecord->aggregatelevel = $val;
                } else {
                    $temprecord->{$key} = $val;
                }
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

        $context = get_context_instance_by_id($capability->id);

        if (!empty($otheruserid)) { // we are pulling out other user's capabilities, do not write to session
            
            if (capability_prohibits($capability->capability, $context, $capability->sum, $usercap)) {
                $usercap[$capability->id][$capability->capability] = -9000;
                continue;
            }

            $usercap[$capability->id][$capability->capability] = $capability->sum;          
          
        } else {

            if (capability_prohibits($capability->capability, $context, $capability->sum)) { // if any parent or parent's parent is set to prohibit
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
    
    if (!empty($otheruserid)) {
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
 * @param $context - the context object
 * @param $array - when loading another user caps, their caps are not stored in session but an array
 */
function capability_prohibits($capability, $context, $sum='', $array='') {
    global $USER;

    if ($sum < -8000) {
        // If this capability is set to prohibit.
        return true;
    }
    
    if (isset($array)) {
        if (isset($array[$context->id][$capability]) 
                && $array[$context->id][$capability] < -8000) {
            return true;
        }    
    } else {
        // Else if set in session.
        if (isset($USER->capabilities[$context->id][$capability]) 
                && $USER->capabilities[$context->id][$capability] < -8000) {
            return true;
        }
    }
    switch ($context->aggregatelevel) {
        
        case CONTEXT_SYSTEM:
            // By now it's a definite an inherit.
            return 0;
        break;

        case CONTEXT_PERSONAL:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            return capability_prohibits($capability, $parent);
        break;

        case CONTEXT_USERID:
            $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
            return capability_prohibits($capability, $parent);
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
            return capability_prohibits($capability, $parent);
        break;

        case CONTEXT_COURSE:
            // 1 to 1 to course cat.
            // Find the course cat, and return its value.
            $course = get_record('course','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSECAT, $course->category);
            return capability_prohibits($capability, $parent);
        break;

        case CONTEXT_GROUP:
            // 1 to 1 to course.
            $group = get_record('groups','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $group->courseid);
            return capability_prohibits($capability, $parent);
        break;

        case CONTEXT_MODULE:
            // 1 to 1 to course.
            $cm = get_record('course_modules','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $cm->course);
            return capability_prohibits($capability, $parent);
        break;

        case CONTEXT_BLOCK:
            // 1 to 1 to course.
            $block = get_record('block_instance','id',$context->instanceid);
            $parent = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            return capability_prohibits($capability, $parent);
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
                role_assign($coursecreatorrole, $coursecreator->userid, 0, $systemcontext->id);
            }
        }
    }


    /**
     * Upgrade editting teachers and non-editting teachers.
     */
    if (in_array($CFG->prefix.'user_teachers', $dbtables)) {
        if ($userteachers = get_records('user_teachers')) {
            foreach ($userteachers as $teacher) {
                // populate the user_lastaccess table
                unset($access);
                $access->timeaccess = $teacher->timeaccess;
                $access->userid = $teacher->userid;
                $access->courseid = $teacher->course;
                insert_record('user_lastaccess', $access);
                // assign the default student role
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
                // populate the user_lastaccess table
                unset($access);
                $access->timeaccess = $student->timeaccess;
                $access->userid = $student->userid;
                $access->courseid = $student->course;
                insert_record('user_lastaccess', $access);
                // assign the default student role
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

    /**
     * Insert the correct records for legacy roles 
     */
    allow_assign($adminrole, $adminrole);
    allow_assign($adminrole, $coursecreatorrole);
    allow_assign($adminrole, $noneditteacherrole);
    allow_assign($adminrole, $editteacherrole);   
    allow_assign($adminrole, $studentrole);
    allow_assign($adminrole, $guestrole);
    
    allow_assign($coursecreatorrole, $noneditteacherrole);
    allow_assign($coursecreatorrole, $editteacherrole);
    allow_assign($coursecreatorrole, $studentrole);     
    allow_assign($coursecreatorrole, $guestrole);
    
    allow_assign($editteacherrole, $noneditteacherrole);     
    allow_assign($editteacherrole, $studentrole);      
    allow_assign($editteacherrole, $guestrole);
    
    /// overrides
    allow_override($adminrole, $adminrole);
    allow_override($adminrole, $coursecreatorrole);
    allow_override($adminrole, $noneditteacherrole);
    allow_override($adminrole, $editteacherrole);   
    allow_override($adminrole, $studentrole);
    allow_override($adminrole, $guestrole);    

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
        
        if ($roles = get_roles_with_capability('moodle/legacy:'.$type, CAP_ALLOW)) {
            foreach ($roles as $role) {
                // Assign a site level capability.
                if (!assign_capability($capability, $perm, $role->id, $systemcontext->id)) {
                    return false;
                }
            }
        }
    }
    return true;
}


/**
 * Checks to see if a capability is a legacy capability.
 * @param $capabilityname
 * @return boolean
 */
function islegacy($capabilityname) {
    if (strstr($capabilityname, 'legacy') === false) {
        return false;  
    } else {
        return true;  
    }
}



/**********************************
 * Context Manipulation functions *
 **********************************/

/**
 * This should be called prolly everytime a user, group, module, course,
 * coursecat or site is set up maybe?
 * @param $level
 * @param $instanceid
 */
function create_context($aggregatelevel, $instanceid) {
    if (!get_record('context','aggregatelevel',$aggregatelevel,'instanceid',$instanceid)) {
        $context = new object;
        $context->aggregatelevel = $aggregatelevel;
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
function get_context_instance($aggregatelevel=NULL, $instance=SITEID) {

    global $context_cache, $context_cache_id, $CONTEXT;

/// If no level is supplied then return the current global context if there is one
    if (empty($aggregatelevel)) {
        if (empty($CONTEXT)) {
            debugging("Error: get_context_instance() called without a context");
        } else {
            return $CONTEXT;
        }
    }

/// Check the cache
    if (isset($context_cache[$aggregatelevel][$instance])) {  // Already cached
        return $context_cache[$aggregatelevel][$instance];
    }

/// Get it from the database, or create it
    if (!$context = get_record('context', 'aggregatelevel', $aggregatelevel, 'instanceid', $instance)) {
        create_context($aggregatelevel, $instance);
        $context = get_record('context', 'aggregatelevel', $aggregatelevel, 'instanceid', $instance);
    }

/// Only add to cache if context isn't empty.
    if (!empty($context)) {
        $context_cache[$aggregatelevel][$instance] = $context;    // Cache it for later
        $context_cache_id[$context->id] = $context;      // Cache it for later
    }

    return $context;
}


/**
 * Get a context instance as an object, from a given id.
 * @param $id
 */
function get_context_instance_by_id($id) {

    global $context_cache, $context_cache_id;

    if (isset($context_cache_id[$id])) {  // Already cached
        return $context_cache_id[$id];
    }

    if ($context = get_record('context', 'id', $id)) {   // Update the cache and return
        $context_cache[$context->aggregatelevel][$context->instanceid] = $context;
        $context_cache_id[$context->id] = $context;
        return $context;
    }

    return false;
}


/**
 * Get the local override (if any) for a given capability in a role in a context
 * @param $roleid
 * @param $contextid
 * @param $capability
 */
function get_local_override($roleid, $contextid, $capability) {
    return get_record('role_capabilities', 'roleid', $roleid, 'capability', $capability, 'contextid', $contextid);
}



/************************************
 *    DB TABLE RELATED FUNCTIONS    *
 ************************************/

/**
 * function that creates a role
 * @param name - role name
 * @param description - role description
 * @param legacy - optional legacy capability
 * @return id or false
 */
function create_role($name, $description, $legacy='') {
          
    // check for duplicate role name
                
    if ($role = get_record('role','name', $name)) {
        error('there is already a role with this name!');  
    }
    
    $role->name = $name;
    $role->description = $description;
    
    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);                           
    
    if ($id = insert_record('role', $role)) {
        if ($legacy) {        
            assign_capability($legacy, CAP_ALLOW, $id, $context->id);            
        }
        
        /// By default, users with role:manage at site level
        /// should be able to assign users to this new role, and override this new role's capabilities
        
        // find all admin roles
        if ($adminroles = get_roles_with_capability('moodle/role:manage', CAP_ALLOW, $context)) {
            // foreach admin role
            foreach ($adminroles as $arole) {
                // write allow_assign and allow_overrid
                allow_assign($arole->id, $id);
                allow_override($arole->id, $id);  
            }
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
function assign_capability($capability, $permission, $roleid, $contextid, $overwrite=false) {
    
    global $USER;
    
    if (empty($permission) || $permission == 0) { // if permission is not set
        unassign_capability($capability, $roleid, $contextid);      
    }
    
    $existing = get_record('role_capabilities', 'contextid', $contextid, 'roleid', $roleid, 'capability', $capability);

    if ($existing and !$overwrite) {   // We want to keep whatever is there already
        return true;
    }

    $cap = new object;
    $cap->contextid = $contextid;
    $cap->roleid = $roleid;
    $cap->capability = $capability;
    $cap->permission = $permission;
    $cap->timemodified = time();
    $cap->modifierid = empty($USER->id) ? 0 : $USER->id;

    if ($existing) {
        $cap->id = $existing->id;
        return update_record('role_capabilities', $cap);
    } else {
        return insert_record('role_capabilities', $cap);
    }
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
function get_roles_with_capability($capability, $permission=NULL, $context='') {

    global $CFG;
    
    if ($context) {
        if ($contexts = get_parent_contexts($context)) {
            $listofcontexts = '('.implode(',', $contexts).')';
        } else {
            $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
            $listofcontexts = '('.$sitecontext->id.')'; // must be site  
        }  
        $contextstr = "AND (rc.contextid = '$context->id' OR  rc.contextid IN $listofcontexts)";
    } else {
        $contextstr = '';
    }
    
    $selectroles = "SELECT r.* 
                      FROM {$CFG->prefix}role r,
                           {$CFG->prefix}role_capabilities rc
                     WHERE rc.capability = '$capability'
                       AND rc.roleid = r.id $contextstr";

    if (isset($permission)) {
        $selectroles .= " AND rc.permission = '$permission'";
    }
    return get_records_sql($selectroles);
}


/**
 * This function makes a role-assignment (a role for a user or group in a particular context)
 * @param $roleid - the role of the id
 * @param $userid - userid
 * @param $groupid - group id
 * @param $contextid - id of the context
 * @param $timestart - time this assignment becomes effective
 * @param $timeend - time this assignemnt ceases to be effective
 * @uses $USER
 * @return id - new id of the assigment
 */
function role_assign($roleid, $userid, $groupid, $contextid, $timestart=0, $timeend=0, $hidden=0, $enrol='manual') {
    global $USER, $CFG;

    debugging("Assign roleid $roleid userid $userid contextid $contextid", E_ALL);

/// Do some data validation

    if (empty($roleid)) {
        notify('Role ID not provided');
        return false;
    }

    if (empty($userid) && empty($groupid)) {
        notify('Either userid or groupid must be provided');
        return false;
    }
    
    if ($userid && !record_exists('user', 'id', $userid)) {
        notify('User does not exist!');
        return false;
    }

    if ($groupid && !record_exists('groups', 'id', $groupid)) {
        notify('Group does not exist!');
        return false;
    }

    if (!$context = get_context_instance_by_id($contextid)) {
        notify('A valid context must be provided');
        return false;
    }

    if (($timestart and $timeend) and ($timestart > $timeend)) {
        notify('The end time can not be earlier than the start time');
        return false;
    }


/// Check for existing entry
    if ($userid) {
        $ra = get_record('role_assignments', 'roleid', $roleid, 'contextid', $context->id, 'userid', $userid);
    } else {
        $ra = get_record('role_assignments', 'roleid', $roleid, 'contextid', $context->id, 'groupid', $groupid);
    }


    $newra = new object;

    if (empty($ra)) {             // Create a new entry
        $newra->roleid = $roleid;
        $newra->contextid = $context->id;
        $newra->userid = $userid;
        $newra->groupid = $groupid;

        $newra->hidden = $hidden;
        $newra->enrol = $enrol;
        $newra->timestart = $timestart;
        $newra->timeend = $timeend;
        $newra->timemodified = time();
        $newra->modifier = empty($USER->id) ? 0 : $USER->id;

        $success = insert_record('role_assignments', $newra);

    } else {                      // We already have one, just update it

        $newra->id = $ra->id;
        $newra->hidden = $hidden;
        $newra->enrol = $enrol;
        $newra->timestart = $timestart;
        $newra->timeend = $timeend;
        $newra->timemodified = time();
        $newra->modifier = empty($USER->id) ? 0 : $USER->id;

        $success = update_record('role_assignments', $newra);
    }

    if ($success) {   /// Role was assigned, so do some other things

    /// If the user is the current user, then reload the capabilities too.
        if (!empty($USER->id) && $USER->id == $userid) {
            load_user_capability();
        }

    /// Ask all the modules if anything needs to be done for this user
        if ($mods = get_list_of_plugins('mod')) {
            foreach ($mods as $mod) {
                include_once($CFG->dirroot.'/mod/'.$mod.'/lib.php');
                $functionname = $mod.'_role_assign';
                if (function_exists($functionname)) {
                    $functionname($userid, $context);
                }
            }
        }

    /// Make sure they have an entry in user_lastaccess for courses they can access
    //    role_add_lastaccess_entries($userid, $context);
    }

    return $success;
}


/**
 * Deletes one or more role assignments.   You must specify at least one parameter.
 * @param $roleid
 * @param $userid
 * @param $groupid
 * @param $contextid
 * @return boolean - success or failure
 */
function role_unassign($roleid=0, $userid=0, $groupid=0, $contextid=0) {

    global $USER, $CFG;

    $args = array('roleid', 'userid', 'groupid', 'contextid');
    $select = array();
    foreach ($args as $arg) {
        if ($$arg) {
            $select[] = $arg.' = '.$$arg;
        }
    }

    if ($select) {
        if (delete_records_select('role_assignments', implode(' AND ', $select))) {

        /// If the user is the current user, then reload the capabilities too.
            if (!empty($USER->id) && $USER->id == $userid) {
                load_user_capability();
            }
    
            if ($contextid) {
                if ($context = get_record('context', 'id', $contextid)) {

                /// Ask all the modules if anything needs to be done for this user
                    if ($mods = get_list_of_plugins('mod')) {
                        foreach ($mods as $mod) {
                            include_once($CFG->dirroot.'/mod/'.$mod.'/lib.php');
                            $functionname = $mod.'_role_unassign';
                            if (function_exists($functionname)) {
                                $functionname($userid, $context);
                            }
                        }
                    }
        
                /// Remove entries from user_lastaccess for courses they can no longer access
                    //role_add_lastaccess_entries($userid, $context);
                }
            }

            return true;
        }
        return false;
    }
    return true;
}

/**
 * Add last access times to user_lastaccess as required
 * @param $userid
 * @param $context
 * @return boolean - success or failure
 */
function role_add_lastaccess_entries($userid, $context) {

    global $USER, $CFG;

    if (empty($context->aggregatelevel)) {
        return false;
    }

    $lastaccess = new object;        // Reusable object below
    $lastaccess->userid = $userid;
    $lastaccess->timeaccess = 0;

    switch ($context->aggregatelevel) {

        case CONTEXT_SYSTEM:   // For the whole site
             if ($courses = get_record('course')) {
                 foreach ($courses as $course) {
                     $lastaccess->courseid = $course->id;
                     role_set_lastaccess($lastaccess);
                 }
             }
             break;

        case CONTEXT_CATEGORY:   // For a whole category
             if ($courses = get_record('course', 'category', $context->instanceid)) {
                 foreach ($courses as $course) {
                     $lastaccess->courseid = $course->id;
                     role_set_lastaccess($lastaccess);
                 }
             }
             if ($categories = get_record('course_categories', 'parent', $context->instanceid)) {
                 foreach ($categories as $category) {
                     $subcontext = get_context_instance(CONTEXT_CATEGORY, $category->id);
                     role_add_lastaccess_entries($userid, $subcontext);
                 }
             }
             break;
    

        case CONTEXT_COURSE:   // For a whole course
             if ($course = get_record('course', 'id', $context->instanceid)) {
                 $lastaccess->courseid = $course->id;
                 role_set_lastaccess($lastaccess);
             }
             break;
    }
}

/**
 * Delete last access times from user_lastaccess as required
 * @param $userid
 * @param $context
 * @return boolean - success or failure
 */
function role_remove_lastaccess_entries($userid, $context) {

    global $USER, $CFG;

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
        $compparts = explode('/', $component);
        
        if ($compparts[0] == 'block') {
            // Blocks are an exception. Blocks directory is 'blocks', and not
            // 'block'. So we need to jump through hoops.
            $defpath = $CFG->dirroot.'/'.$compparts[0].
                                's/'.$compparts[1].'/db/access.php';
            $varprefix = $compparts[0].'_'.$compparts[1];
        } else {
            $defpath = $CFG->dirroot.'/'.$component.'/db/access.php';
            $varprefix = str_replace('/', '_', $component);
        }
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

    $filecaps = load_capability_def($component);
    $cachedcaps = get_cached_capabilities($component);
    if ($cachedcaps) {
        foreach ($cachedcaps as $cachedcap) {
            array_push($storedcaps, $cachedcap->name);
            // update risk bitmasks in existing capabilitites if needed
            if (array_key_exists($cachedcap->name, $filecaps)) {
                if (!array_key_exists('riskbitmask', $filecaps[$cachedcap->name])) {
                    $filecaps[$cachedcap->name]['riskbitmask'] = 0; // no risk if not specified
                }
                if ($cachedcap->riskbitmask != $filecaps[$cachedcap->name]['riskbitmask']) {
                    $updatecap = new object;
                    $updatecap->id = $cachedcap->id;
                    $updatecap->riskbitmask = $filecaps[$cachedcap->name]['riskbitmask'];
                    if (!update_record('capabilities', $updatecap)) {
                        return false;
                    }
                }
            }
        }
    }

    // Are there new capabilities in the file definition?
    $newcaps = array();
    
    foreach ($filecaps as $filecap => $def) {
        if (!$storedcaps || 
                ($storedcaps && in_array($filecap, $storedcaps) === false)) {
            if (!array_key_exists('riskbitmask', $def)) {
                $def['riskbitmask'] = 0; // no risk if not specified
            }
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
        $capability->riskbitmask = $capdef['riskbitmask'];
        
        if (!insert_record('capabilities', $capability, false, 'id')) {
            return false;
        }
        
        global $db;
        $db->debug= 999;
        // Do we need to assign the new capabilities to roles that have the
        // legacy capabilities moodle/legacy:* as well?
        if (isset($capdef['legacy']) && is_array($capdef['legacy']) &&
                    !assign_legacy_capabilities($capname, $capdef['legacy'])) {
            notify('Could not assign legacy capabilities for '.$capname);
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
                        if (!unassign_capability($cachedcap->name, $role->id)) {
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



/****************
 * UI FUNCTIONS *
 ****************/


/**
 * prints human readable context identifier.
 */
function print_context_name($context) {

    $name = '';
    switch ($context->aggregatelevel) {

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
                    global $CFG;
                    require_once("$CFG->dirroot/blocks/moodleblock.class.php");
                    require_once("$CFG->dirroot/blocks/$block->name/block_$block->name.php");
                    $blockname = "block_$block->name";
                    if ($blockobject = new $blockname()) {
                        $name = $blockobject->title.' ('.get_string('block').')';
                    }
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
 * @param object context
 * @return array();
 *
 *  capabilities
 * `name` varchar(150) NOT NULL,
 * `captype` varchar(50) NOT NULL,
 * `contextlevel` int(10) NOT NULL,
 * `component` varchar(100) NOT NULL,
 */
function fetch_context_capabilities($context) {
      
    global $CFG;

    $sort = 'ORDER BY contextlevel,component,id';   // To group them sensibly for display
      
    switch ($context->aggregatelevel) {

        case CONTEXT_SYSTEM: // all
            $SQL = "select * from {$CFG->prefix}capabilities";
        break;

        case CONTEXT_PERSONAL:
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_PERSONAL;
        break;
        
        case CONTEXT_USERID:
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_USERID;
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
            $cm = get_record('course_modules', 'id', $context->instanceid);
            $module = get_record('modules', 'id', $cm->module);
        
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_MODULE."
                    and component = 'mod/$module->name'";
        break;

        case CONTEXT_BLOCK: // block caps
            $cb = get_record('block_instance', 'id', $context->instanceid);
            $block = get_record('block', 'id', $cb->blockid);
        
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_BLOCK."
                    and component = 'block/$block->name'";
        break;

        default:
        return false;
    }

    $records = get_records_sql($SQL.' '.$sort);

    // special sorting of core system capabiltites and enrollments
    if ($context->aggregatelevel == CONTEXT_SYSTEM) {
        $first = array();
        foreach ($records as $key=>$record) {
            if (preg_match('|^moodle/|', $record->name) and $record->contextlevel == CONTEXT_SYSTEM) {
                $first[$key] = $record;
                unset($records[$key]);
            } else if (count($first)){
                break;
            }
        }
        if (count($first)) {
           $records = $first + $records; // merge the two arrays keeping the keys
        }
    }
    // end of special sorting

    return $records;
    
}


/**
 * This function pulls out all the resolved capabilities (overrides and
 * defaults) of a role used in capability overrieds in contexts at a given
 * context.
 * @param obj $context
 * @param int $roleid
 * @return array
 */
function role_context_capabilities($roleid, $context, $cap='') {
    global $CFG; 
    
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
    if ($sitecontext->id == $context->id) {
        $contexts = array($sitecontext->id);  
    } else {
        // first of all, figure out all parental contexts
        $contexts = array_reverse(get_parent_contexts($context));
    }
    $contexts = '('.implode(',', $contexts).')';
    
    if ($cap) {
        $search = " AND rc.capability = '$cap' ";
    } else {
        $search = '';  
    }
    
    $SQL = "SELECT rc.* FROM {$CFG->prefix}role_capabilities rc, {$CFG->prefix}context c
            where rc.contextid in $contexts
            and rc.roleid = $roleid
            and rc.contextid = c.id $search
            ORDER BY c.aggregatelevel DESC, rc.capability DESC";
  
    $capabilities = array();
    
    if ($records = get_records_sql($SQL)) {
        // We are traversing via reverse order.
        foreach ($records as $record) {
            // If not set yet (i.e. inherit or not set at all), or currently we have a prohibit
            if (!isset($capabilities[$record->capability]) || $record->permission<-500) {
                $capabilities[$record->capability] = $record->permission;
            }  
        }
    }
    return $capabilities;
}

/**
 * Recursive function which, given a context, find all parent context ids, 
 * and return the array in reverse order, i.e. parent first, then grand
 * parent, etc.
 * @param object $context
 * @return array()
 */
function get_parent_contexts($context) {
  
    switch ($context->aggregatelevel) {

        case CONTEXT_SYSTEM: // no parent
            return array();
        break;

        case CONTEXT_PERSONAL:
            if (!$parent = get_context_instance(CONTEXT_SYSTEM, SITEID)) {
                return array();
            } else {
                return array($parent->id);
            }
        break;
        
        case CONTEXT_USERID:
            if (!$parent = get_context_instance(CONTEXT_SYSTEM, SITEID)) {
                return array();
            } else {
                return array($parent->id);
            }
        break;
        
        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            if (!$coursecat = get_record('course_categories','id',$context->instanceid)) {
                return array();
            }
            if (!empty($coursecat->parent)) { // return parent value if exist
                $parent = get_context_instance(CONTEXT_COURSECAT, $coursecat->parent);
                return array_merge(array($parent->id), get_parent_contexts($parent));
            } else { // else return site value
                $parent = get_context_instance(CONTEXT_SYSTEM, SITEID);
                return array($parent->id);
            }
        break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            if (!$course = get_record('course','id',$context->instanceid)) {
                return array();
            }
            if (!empty($course->category)) {
                $parent = get_context_instance(CONTEXT_COURSECAT, $course->category);
                return array_merge(array($parent->id), get_parent_contexts($parent));
            } else {
                return array();
            }
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            if (!$group = get_record('groups','id',$context->instanceid)) {
                return array();
            }
            if ($parent = get_context_instance(CONTEXT_COURSE, $group->courseid)) {
                return array_merge(array($parent->id), get_parent_contexts($parent));
            } else {
                return array();
            }
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            if (!$cm = get_record('course_modules','id',$context->instanceid)) {
                return array();
            }
            if ($parent = get_context_instance(CONTEXT_COURSE, $cm->course)) {
                return array_merge(array($parent->id), get_parent_contexts($parent));
            } else {
                return array();
            }
        break;

        case CONTEXT_BLOCK: // 1 to 1 to course
            if (!$block = get_record('block_instance','id',$context->instanceid)) {
                return array();
            }
            if ($parent = get_context_instance(CONTEXT_COURSE, $block->pageid)) {
                return array_merge(array($parent->id), get_parent_contexts($parent));
            } else {
                return array();
            }
        break;

        default:
            error('This is an unknown context!');
        return false;
    }
}

/** gets a string for sql calls, searching for stuff
 * in this context or above
 * @param object $context
 * @return string
 */
function get_related_contexts_string($context) {
    if ($parents = get_parent_contexts($context)) {
        return (' IN ('.$context->id.','.implode(',', $parents).')');    
    } else {
        return (' ='.$context->id);
    }
}
/**
 * This function gets the capability of a role in a given context.
 * It is needed when printing override forms.
 * @param int $contextid
 * @param string $capability
 * @param array $capabilities - array loaded using role_context_capabilities
 * @return int (allow, prevent, prohibit, inherit)
 */
function get_role_context_capability($contextid, $capability, $capabilities) {
    return $capabilities[$contextid][$capability];
}


/**
 * Returns the human-readable, translated version of the capability.
 * Basically a big switch statement.
 * @param $capabilityname - e.g. mod/choice:readresponses
 */
function get_capability_string($capabilityname) {
    
    // Typical capabilityname is mod/choice:readresponses

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


/**
 * This gets the mod/block/course/core etc strings.
 * @param $component
 * @param $contextlevel
 */
function get_component_string($component, $contextlevel) {

    switch ($contextlevel) {

        case CONTEXT_SYSTEM:
            if (preg_match('|^enrol/|', $component)) {
                $langname = str_replace('/', '_', $component);
                $string = get_string('enrolname', $langname);
            } else {
                $string = get_string('coresystem');
            }
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

/** gets the list of roles assigned to this context
 * @param object $context
 * @return array
 */
function get_roles_used_in_context($context) {

    global $CFG;

    return get_records_sql('SELECT distinct r.id, r.name 
                              FROM '.$CFG->prefix.'role_assignments ra,
                                   '.$CFG->prefix.'role r 
                             WHERE r.id = ra.roleid 
                               AND ra.contextid = '.$context->id.' 
                             ORDER BY r.sortorder ASC');
}

/** this function is used to print roles column in user profile page. 
 * @param int userid
 * @param int contextid
 * @return string
 */
function get_user_roles_in_context($userid, $contextid){
    global $CFG;
    
    $rolestring = '';
    $SQL = 'select * from '.$CFG->prefix.'role_assignments ra, '.$CFG->prefix.'role r where ra.userid='.$userid.' and ra.contextid='.$contextid.' and ra.roleid = r.id';
    if ($roles = get_records_sql($SQL)) {
        foreach ($roles as $userrole) {
            $rolestring .= '<a href="'.$CFG->wwwroot.'/user/index.php?contextid='.$userrole->contextid.'&amp;roleid='.$userrole->roleid.'">'.$userrole->name.'</a>, ';
        }   
        
    }
    return rtrim($rolestring, ', ');
}


/**
 * Checks if a user can override capabilities of a particular role in this context
 * @param object $context
 * @param int targetroleid - the id of the role you want to override
 * @return boolean
 */
function user_can_override($context, $targetroleid) {
    // first check if user has override capability
    // if not return false;
    if (!has_capability('moodle/role:override', $context)) {
        return false;  
    }
    // pull out all active roles of this user from this context(or above)
    if ($userroles = get_user_roles($context)) {
        foreach ($userroles as $userrole) {
            // if any in the role_allow_override table, then it's ok
            if (get_record('role_allow_override', 'roleid', $userrole->roleid, 'allowoverride', $targetroleid)) {
                return true;
            }
        }
    }
    
    return false;
  
}

/**
 * Checks if a user can assign users to a particular role in this context
 * @param object $context
 * @param int targetroleid - the id of the role you want to assign users to
 * @return boolean
 */
function user_can_assign($context, $targetroleid) {
    
    // first check if user has override capability
    // if not return false;
    if (!has_capability('moodle/role:assign', $context)) {
        return false;  
    }
    // pull out all active roles of this user from this context(or above)
    if ($userroles = get_user_roles($context)) {
        foreach ($userroles as $userrole) {
            // if any in the role_allow_override table, then it's ok
            if (get_record('role_allow_assign', 'roleid', $userrole->roleid, 'allowassign', $targetroleid)) {
                return true;
            }
        }
    }
    
    return false; 
}

/**
 * gets all the user roles assigned in this context, or higher contexts
 * this is mainly used when checking if a user can assign a role, or overriding a role
 * i.e. we need to know what this user holds, in order to verify against allow_assign and
 * allow_override tables
 * @param object $context
 * @param int $userid
 * @return array
 */
function get_user_roles($context, $userid=0) {

    global $USER, $CFG, $db;

    if (empty($userid)) {
        if (empty($USER->id)) {
            return array();
        }
        $userid = $USER->id;
    }

    if ($parents = get_parent_contexts($context)) {
        $contexts = ' AND ra.contextid IN ('.implode(',' , $parents).')';
    } else {
        $contexts = ' AND ra.contextid = \''.$context->id.'\'';
    }

    return get_records_sql('SELECT *
                             FROM '.$CFG->prefix.'role_assignments ra
                             WHERE ra.userid = '.$userid.
                             $contexts);
}

/**
 * Creates a record in the allow_override table 
 * @param int sroleid - source roleid
 * @param int troleid - target roleid
 * @return int - id or false
 */
function allow_override($sroleid, $troleid) {
    $record->roleid = $sroleid;
    $record->allowoverride = $troleid;
    return insert_record('role_allow_override', $record);
}

/**
 * Creates a record in the allow_assign table 
 * @param int sroleid - source roleid
 * @param int troleid - target roleid
 * @return int - id or false
 */
function allow_assign($sroleid, $troleid) {
    $record->roleid = $sroleid;
    $record->allowassign = $troleid;
    return insert_record('role_allow_assign', $record);
}

/**
 * gets a list of roles assignalbe in this context for this user
 * @param object $context
 * @return array
 */
function get_assignable_roles ($context) {

    $role = get_records('role');
    $options = array();
    foreach ($role as $rolex) {
        if (user_can_assign($context, $rolex->id)) {
            $options[$rolex->id] = $rolex->name;
        }
    }
    return $options;
}

/**
 * gets a list of roles that can be overriden in this context by this user
 * @param object $context
 * @return array
 */
function get_overridable_roles ($context) {

    $role = get_records('role');
    $options = array();
    foreach ($role as $rolex) {
        if (user_can_override($context, $rolex->id)) {
            $options[$rolex->id] = $rolex->name;
        }
    } 
    
    return $options;  
  
}


/**
 * who has this capability in this context
 * does not handling user level resolving!!!
 * i.e 1 person has 2 roles 1 allow, 1 prevent, this will not work properly
 * @param $context - object
 * @param $capability - string capability
 * @param $fields - fields to be pulled
 * @param $sort - the sort order
 * @param $limitfrom - number of records to skip (offset)
 * @param $limitnum - number of records to fetch 
 * @param $groups - single group or array of groups - group(s) user is in
 */
function get_users_by_capability($context, $capability, $fields='u.*', $sort='', $limitfrom='', $limitnum='', $groups='') {
    
    global $CFG;
    
    if ($groups) {
      
        $groupjoin = 'LEFT JOIN '.$CFG->prefix.'groups_members gm ON gm.userid = ra.userid';
        
        if (is_array($groups)) {
            $groupsql = 'AND gm.id IN ('.implode(',', $groups).')';
        } else {
            $groupsql = 'AND gm.id = '.$groups; 
        }
    } else {
        $groupjoin = '';
        $groupsql = '';  
    }
    
    // first get all roles with this capability in this context, or above
    $possibleroles = get_roles_with_capability($capability, CAP_ALLOW, $context);
    $validroleids = array();
    foreach ($possibleroles as $prole) {
        $caps = role_context_capabilities($prole->id, $context, $capability); // resolved list
        if ($caps[$capability] > 0) { // resolved capability > 0
            $validroleids[] = $prole->id;
        }
    }
    
    /// the following few lines may not be needed
    if ($usercontexts = get_parent_contexts($context)) {
        $listofcontexts = '('.implode(',', $usercontexts).')';
    } else {
        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        $listofcontexts = '('.$sitecontext->id.')'; // must be site  
    }
    
    $roleids =  '('.implode(',', $validroleids).')';
    
    $select = ' SELECT '.$fields;
    $from   = ' FROM '.$CFG->prefix.'user u LEFT JOIN '.$CFG->prefix.'role_assignments ra ON ra.userid = u.id '.$groupjoin;
    $where  = ' WHERE (ra.contextid = '.$context->id.' OR ra.contextid in '.$listofcontexts.') AND u.deleted = 0 AND ra.roleid in '.$roleids.' '.$groupsql;

    return get_records_sql($select.$from.$where.$sort, $limitfrom, $limitnum);  

}

/**
 * gets all the users assigned this role in this context or higher
 * @param int roleid
 * @param int contextid
 * @param bool parent if true, get list of users assigned in higher context too
 * @return array()
 */
function get_role_users($roleid, $context, $parent=false) {
    global $CFG;
    
    if ($parent) {
        if ($contexts = get_parent_contexts($context)) {
            $parentcontexts = 'r.contextid IN ('.implode(',', $contexts).')';
        } else {
            $parentcontexts = ''; 
        }
    } else {
        $parentcontexts = '';  
    }
    
    $SQL = "select u.* 
            from {$CFG->prefix}role_assignments r, 
                 {$CFG->prefix}user u 
            where (r.contextid = $context->id $parentcontexts) 
            and r.roleid = $roleid 
            and u.id = r.userid"; // join now so that we can just use fullname() later
    
    return get_records_sql($SQL);
}

?>

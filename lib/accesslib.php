<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-2004  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

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

require_once $CFG->dirroot.'/lib/blocklib.php';

// permission definitions
define('CAP_INHERIT', 0);
define('CAP_ALLOW', 1);
define('CAP_PREVENT', -1);
define('CAP_PROHIBIT', -1000);

// context definitions
define('CONTEXT_SYSTEM', 10);
define('CONTEXT_PERSONAL', 20);
define('CONTEXT_USER', 30);
define('CONTEXT_COURSECAT', 40);
define('CONTEXT_COURSE', 50);
define('CONTEXT_GROUP', 60);
define('CONTEXT_MODULE', 70);
define('CONTEXT_BLOCK', 80);

// capability risks - see http://docs.moodle.org/en/Hardening_new_Roles_system
define('RISK_MANAGETRUST', 0x0001);
define('RISK_CONFIG',      0x0002);
define('RISK_XSS',         0x0004);
define('RISK_PERSONAL',    0x0008);
define('RISK_SPAM',        0x0010);

require_once($CFG->dirroot.'/group/lib.php');

$context_cache    = array();    // Cache of all used context objects for performance (by level and instance)
$context_cache_id = array();    // Index to above cache by id


function get_role_context_caps($roleid, $context) {
    //this is really slow!!!! - do not use above course context level!
    $result = array();
    $result[$context->id] = array();

    // first emulate the parent context capabilities merging into context
    $searchcontexts = array_reverse(get_parent_contexts($context));
    array_push($searchcontexts, $context->id);
    foreach ($searchcontexts as $cid) {
        if ($capabilities = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $cid")) {
            foreach ($capabilities as $cap) {
                if (!array_key_exists($cap->capability, $result[$context->id])) {
                    $result[$context->id][$cap->capability] = 0;
                }
                $result[$context->id][$cap->capability] += $cap->permission;
            }
        }
    }

    // now go through the contexts bellow given context
    $searchcontexts = get_child_contexts($context);
    foreach ($searchcontexts as $cid) {
        if ($capabilities = get_records_select('role_capabilities', "roleid = $roleid AND contextid = $cid")) {
            foreach ($capabilities as $cap) {
                if (!array_key_exists($cap->contextid, $result)) {
                    $result[$cap->contextid] = array();
                }
                $result[$cap->contextid][$cap->capability] = $cap->permission;
            }
        }
    }

    return $result;
}

function get_role_caps($roleid) {
    $result = array();
    if ($capabilities = get_records_select('role_capabilities',"roleid = $roleid")) {
        foreach ($capabilities as $cap) {
            if (!array_key_exists($cap->contextid, $result)) {
                $result[$cap->contextid] = array();
            }
            $result[$cap->contextid][$cap->capability] = $cap->permission;
        }
    }
    return $result;
}

function merge_role_caps($caps, $mergecaps) {
    if (empty($mergecaps)) {
        return $caps;
    }

    if (empty($caps)) {
        return $mergecaps;
    }

    foreach ($mergecaps as $contextid=>$capabilities) {
        if (!array_key_exists($contextid, $caps)) {
            $caps[$contextid] = array();
        }
        foreach ($capabilities as $capability=>$permission) {
            if (!array_key_exists($capability, $caps[$contextid])) {
                $caps[$contextid][$capability] = 0;
            }
            $caps[$contextid][$capability] += $permission;
        }
    }
    return $caps;
}

/**
 * Loads the capabilities for the default guest role to the current user in a
 * specific context.
 * @return object
 */
function load_guest_role($return=false) {
    global $USER;

    static $guestrole = false;

    if ($guestrole === false) {
        if (!$guestrole = get_guest_role()) {
            return false;
        }
    }

    if ($return) {
        return get_role_caps($guestrole->id);
    } else {
        has_capability('clearcache');
        $USER->capabilities = get_role_caps($guestrole->id);
        return true;
    }
}

/**
 * Load default not logged in role capabilities when user is not logged in
 * @return bool
 */
function load_notloggedin_role($return=false) {
    global $CFG, $USER;

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM)) {
        return false;
    }

    if (empty($CFG->notloggedinroleid)) {    // Let's set the default to the guest role
        if ($role = get_guest_role()) {
            set_config('notloggedinroleid', $role->id);
        } else {
            return false;
        }
    }

    if ($return) {
        return get_role_caps($CFG->notloggedinroleid);
    } else {
        has_capability('clearcache');
        $USER->capabilities = get_role_caps($CFG->notloggedinroleid);
        return true;
    }
}

/**
 * Load default logged in role capabilities for all logged in users
 * @return bool
 */
function load_defaultuser_role($return=false) {
    global $CFG, $USER;

    if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM)) {
        return false;
    }

    if (empty($CFG->defaultuserroleid)) {    // Let's set the default to the guest role
        if ($role = get_guest_role()) {
            set_config('defaultuserroleid', $role->id);
        } else {
            return false;
        }
    }

    $capabilities = get_role_caps($CFG->defaultuserroleid);

    // fix the guest user heritage:
    // If the default role is a guest role, then don't copy legacy:guest,
    // otherwise this user could get confused with a REAL guest. Also don't copy
    // course:view, which is a hack that's necessary because guest roles are 
    // not really handled properly (see MDL-7513)
    if (!empty($capabilities[$sitecontext->id]['moodle/legacy:guest'])) {
        unset($capabilities[$sitecontext->id]['moodle/legacy:guest']);
        unset($capabilities[$sitecontext->id]['moodle/course:view']);
    }

    if ($return) {
        return $capabilities;
    } else {
        has_capability('clearcache');
        $USER->capabilities = $capabilities;
        return true;
    }
}


/**
 * Get the default guest role
 * @return object role
 */
function get_guest_role() {
    global $CFG;

    if (empty($CFG->guestroleid)) {
        if ($roles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
            $guestrole = array_shift($roles);   // Pick the first one
            set_config('guestroleid', $guestrole->id);
            return $guestrole;
        } else {
            debugging('Can not find any guest role!');
            return false;
        }
    } else {
        if ($guestrole = get_record('role','id', $CFG->guestroleid)) {
            return $guestrole;
        } else {
            //somebody is messing with guest roles, remove incorrect setting and try to find a new one
            set_config('guestroleid', '');
            return get_guest_role();
        }
    }
}


/**
 * This functions get all the course categories in proper order
 * (!)note this only gets course category contexts, and not the site
 * context
 * @param object $context
 * @return array of contextids
 */
function get_parent_cats($context) {
    global $COURSE;

    switch ($context->contextlevel) {
        // a category can be the parent of another category
        // there is no limit of depth in this case
        case CONTEXT_COURSECAT:
            static $categoryparents = null; // cache for parent categories
            if (!isset($categoryparents)) {
                $categoryparents = array();
            }
            if (array_key_exists($context->instanceid, $categoryparents)) {
                return $categoryparents[$context->instanceid];
            }

            if (!$cat = get_record('course_categories','id',$context->instanceid)) {
                //error?
                return array();
            }
            $parents = array();
            while (!empty($cat->parent)) {
                if (!$catcontext = get_context_instance(CONTEXT_COURSECAT, $cat->parent)) {
                    debugging('Incorrect category parent');
                    break;
                }
                $parents[] = $catcontext->id;
                $cat = get_record('course_categories','id',$cat->parent);
            }
           return $categoryparents[$context->instanceid] = array_reverse($parents);
        break;
        
        // a course always fall into a category, unless it's a site course
        // this happens when SITEID == $course->id
        // in this case the parent of the course is site context
        case CONTEXT_COURSE:
            static $courseparents = null; // cache course parents
            if (!isset($courseparents)) {
                $courseparents = array();
            }
            if (array_key_exists($context->instanceid, $courseparents)) {
                return $courseparents[$context->instanceid];
            }

            if (count($courseparents) > 1000) {
                $courseparents = array();   // max cache size when looping through thousands of courses
            }
            if ($context->instanceid == SITEID) {
                return $courseparents[$context->instanceid] = array(); // frontpage course does not have parent cats
            }
            if ($context->instanceid == $COURSE->id) {
                $course = $COURSE;
            } else if (!$course = get_record('course', 'id', $context->instanceid)) {
                //error?
                return array();;
            }

            if (empty($course->category)) {
                // this should not happen
                return $courseparents[$context->instanceid] = array();
            }
            
            if (!$catcontext = get_context_instance(CONTEXT_COURSECAT, $course->category)) {
                debugging('Incorect course category');
                return array();;
            }

            return $courseparents[$context->instanceid] = array_merge(get_parent_cats($catcontext), array($catcontext->id)); //recursion :-)
        break;

        default:
            // something is very wrong!
            return array();
        break;
    }
}



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
                            $errormessage='nopermissions', $stringfile='') {

    global $USER, $CFG;

/// If the current user is not logged in, then make sure they are (if needed)

    if (empty($userid) and empty($USER->capabilities)) {
        if ($context && ($context->contextlevel == CONTEXT_COURSE)) {
            require_login($context->instanceid);
        } else if ($context && ($context->contextlevel == CONTEXT_MODULE)) {
            if ($cm = get_record('course_modules','id',$context->instanceid)) {
                if (!$course = get_record('course', 'id', $cm->course)) {
                    error('Incorrect course.');
                }
                require_course_login($course, true, $cm);

            } else {
                require_login();
            }
        } else if ($context && ($context->contextlevel == CONTEXT_SYSTEM)) {
            if (!empty($CFG->forcelogin)) {
                require_login();
            }

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
 * Cheks if current user has allowed permission for any of submitted capabilities
 * in given or child contexts.
 * @param object $context - a context object (record from context table)
 * @param array $capabilitynames array of strings, capability names
 * @return boolean
 */
function has_capability_including_child_contexts($context, $capabilitynames) {
    global $USER;

    foreach ($capabilitynames as $capname) {
        if (has_capability($capname, $context)) {
            return true;
        }
    }

    if ($children = get_child_contexts($context)) {
        foreach ($capabilitynames as $capname) {
            foreach ($children as $child) {
                if (isset($USER->capabilities[$child][$capname]) and $USER->capabilities[$child][$capname] > 0) {
                    // extra check for inherited prevent and prohibit
                    if (has_capability($capname, get_context_instance_by_id($child), $USER->id, false)) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

/**
 * This function returns whether the current user has the capability of performing a function
 * For example, we can do has_capability('mod/forum:replypost',$context) in forum
 * This is a recursive function.
 * @uses $USER
 * @param string $capability - name of the capability (or debugcache or clearcache)
 * @param object $context - a context object (record from context table)
 * @param integer $userid - a userid number
 * @param bool $doanything - if false, ignore do anything
 * @return bool
 */
function has_capability($capability, $context=NULL, $userid=NULL, $doanything=true) {

    global $USER, $CONTEXT, $CFG;

    static $capcache = array();   // Cache of capabilities 


/// Cache management

    if ($capability == 'clearcache') {
        $capcache = array();             // Clear ALL the capability cache
        return false;
    }

/// Some sanity checks
    if (debugging('',DEBUG_DEVELOPER)) {
        if ($capability == 'debugcache') {
            print_object($capcache);
            return true;
        }
        if (!record_exists('capabilities', 'name', $capability)) {
            debugging('Capability "'.$capability.'" was not found! This should be fixed in code.');
        }
        if ($doanything != true and $doanything != false) {
            debugging('Capability parameter "doanything" is wierd ("'.$doanything.'"). This should be fixed in code.');
        }
        if (!is_object($context) && $context !== NULL) {
            debugging('Incorrect context parameter "'.$context.'" for has_capability(), object expected! This should be fixed in code.');
        }
    }

/// Make sure we know the current context
    if (empty($context)) {              // Use default CONTEXT if none specified
        if (empty($CONTEXT)) {
            return false;
        } else {
            $context = $CONTEXT;
        }
    } else {                            // A context was given to us
        if (empty($CONTEXT)) {
            $CONTEXT = $context;        // Store FIRST used context in this global as future default
        }
    }

/// Check and return cache in case we've processed this one before.
    $requsteduser = empty($userid) ? $USER->id : $userid; // find out the requested user id, $USER->id might have been changed
    $cachekey = $capability.'_'.$context->id.'_'.intval($requsteduser).'_'.intval($doanything);

    if (isset($capcache[$cachekey])) {
        return $capcache[$cachekey];
    }


/// Load up the capabilities list or item as necessary
    if ($userid) {
        if (empty($USER->id) or ($userid != $USER->id) or empty($USER->capabilities)) {

            //caching - helps user switching in cron
            static $guestuserid = false; // guest user id
            static $guestcaps   = false; // guest caps
            static $defcaps     = false; // default user caps - this might help cron

            if ($guestuserid === false) {
                $guestuserid = get_field('user', 'id', 'username', 'guest');
            }

            if ($userid == $guestuserid) {
                if ($guestcaps === false) {
                    $guestcaps = load_guest_role(true);
                }
                $capabilities = $guestcaps;

            } else {
                // This big SQL is expensive!  We reduce it a little by avoiding checking for changed enrolments (false)
                $capabilities = load_user_capability($capability, $context, $userid, false); 
                if ($defcaps === false) {
                    $defcaps = load_defaultuser_role(true);
                }
                $capabilities = merge_role_caps($capabilities, $defcaps);
            }

        } else { //$USER->id == $userid and needed capabilities already present
            $capabilities = $USER->capabilities;
        }

    } else { // no userid
        if (empty($USER->capabilities)) {
            load_all_capabilities(); // expensive - but we have to do it once anyway
        }
        $capabilities = $USER->capabilities;
        $userid = $USER->id;
    }

/// We act a little differently when switchroles is active

    $switchroleactive = false;             // Assume it isn't active in this context


/// First deal with the "doanything" capability

    if ($doanything) {

    /// First make sure that we aren't in a "switched role"

        if (!empty($USER->switchrole)) {       // Switchrole is active somewhere!
            if (!empty($USER->switchrole[$context->id])) {  // Because of current context
                $switchroleactive = true;   
            } else {                                        // Check parent contexts
                if ($parentcontextids = get_parent_contexts($context)) {
                    foreach ($parentcontextids as $parentcontextid) {
                        if (!empty($USER->switchrole[$parentcontextid])) {  // Yep, switchroles active here
                            $switchroleactive = true;   
                            break;
                        }
                    }
                }
            }
        }

    /// Check the site context for doanything (most common) first 

        if (empty($switchroleactive)) {  // Ignore site setting if switchrole is active
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
            if (isset($capabilities[$sitecontext->id]['moodle/site:doanything'])) {
                $result = (0 < $capabilities[$sitecontext->id]['moodle/site:doanything']);
                $capcache[$cachekey] = $result;
                return $result;
            }
        }
    /// If it's not set at site level, it is possible to be set on other levels
    /// Though this usage is not common and can cause risks
        switch ($context->contextlevel) {

            case CONTEXT_COURSECAT:
                // Check parent cats.
                $parentcats = get_parent_cats($context);
                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['moodle/site:doanything'])) {
                        $result = (0 < $capabilities[$parentcat]['moodle/site:doanything']);
                        $capcache[$cachekey] = $result;
                        return $result;
                    }
                }
            break;

            case CONTEXT_COURSE:
                // Check parent cat.
                $parentcats = get_parent_cats($context);

                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['do_anything'])) {
                        $result = (0 < $capabilities[$parentcat]['do_anything']);
                        $capcache[$cachekey] = $result;
                        return $result;
                    }
                }
            break;

            case CONTEXT_GROUP:
                // Find course.
                $courseid = groups_get_course($context->instanceid);
                $courseinstance = get_context_instance(CONTEXT_COURSE, $courseid);

                $parentcats = get_parent_cats($courseinstance);
                foreach ($parentcats as $parentcat) {
                    if (isset($capabilities[$parentcat]['do_anything'])) {
                        $result = (0 < $capabilities[$parentcat]['do_anything']);
                        $capcache[$cachekey] = $result;
                        return $result;
                    }
                }

                $coursecontext = '';
                if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                    $result = (0 < $capabilities[$courseinstance->id]['do_anything']);
                    $capcache[$cachekey] = $result;
                    return $result;
                }

            break;

            case CONTEXT_MODULE:
                // Find course.
                $cm = get_record('course_modules', 'id', $context->instanceid);
                $courseinstance = get_context_instance(CONTEXT_COURSE, $cm->course);

                if ($parentcats = get_parent_cats($courseinstance)) {
                    foreach ($parentcats as $parentcat) {
                        if (isset($capabilities[$parentcat]['do_anything'])) {
                            $result = (0 < $capabilities[$parentcat]['do_anything']);
                            $capcache[$cachekey] = $result;
                            return $result;
                        }
                    }
                }

                if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                    $result = (0 < $capabilities[$courseinstance->id]['do_anything']);
                    $capcache[$cachekey] = $result;
                    return $result;
                }

            break;

            case CONTEXT_BLOCK:
                // not necessarily 1 to 1 to course.
                $block = get_record('block_instance','id',$context->instanceid);
                if ($block->pagetype == 'course-view') {
                    $courseinstance = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
                    $parentcats = get_parent_cats($courseinstance);
                
                    foreach ($parentcats as $parentcat) {
                        if (isset($capabilities[$parentcat]['do_anything'])) {
                            $result = (0 < $capabilities[$parentcat]['do_anything']);
                            $capcache[$cachekey] = $result;
                            return $result;
                        }
                    }
                
                    if (isset($capabilities[$courseinstance->id]['do_anything'])) {
                        $result = (0 < $capabilities[$courseinstance->id]['do_anything']);
                        $capcache[$cachekey] = $result;
                        return $result;
                    }
                }
                // blocks that do not have course as parent do not need to do any more checks - already done above

            break;

            default:
                // CONTEXT_SYSTEM: CONTEXT_PERSONAL: CONTEXT_USER:
                // Do nothing, because the parents are site context
                // which has been checked already
            break;
        }

        // Last: check self.
        if (isset($capabilities[$context->id]['do_anything'])) {
            $result = (0 < $capabilities[$context->id]['do_anything']);
            $capcache[$cachekey] = $result;
            return $result;
        }
    }
    // do_anything has not been set, we now look for it the normal way.
    $result = (0 < capability_search($capability, $context, $capabilities, $switchroleactive));
    $capcache[$cachekey] = $result;
    return $result;

}


/**
 * In a separate function so that we won't have to deal with do_anything.
 * again. Used by function has_capability().
 * @param $capability - capability string
 * @param $context - the context object
 * @param $capabilities - either $USER->capability or loaded array (for other users)
 * @return permission (int)
 */
function capability_search($capability, $context, $capabilities, $switchroleactive=false) {

    global $USER, $CFG, $COURSE;

    if (!isset($context->id)) {
        return 0;
    }
    // if already set in the array explicitly, no need to look for it in parent 
    // context any longer
    if (isset($capabilities[$context->id][$capability])) {
        return ($capabilities[$context->id][$capability]);
    }

    /* Then, we check the cache recursively */
    $permission = 0;

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM: // by now it's a definite an inherit
            $permission = 0;
        break;

        case CONTEXT_PERSONAL:
            $parentcontext = get_context_instance(CONTEXT_SYSTEM);
            $permission = capability_search($capability, $parentcontext, $capabilities, $switchroleactive);
        break;

        case CONTEXT_USER:
            $parentcontext = get_context_instance(CONTEXT_SYSTEM);
            $permission = capability_search($capability, $parentcontext, $capabilities, $switchroleactive);
        break;

        case CONTEXT_COURSE:
            if ($switchroleactive) {
                // if switchrole active, do not check permissions above the course context, blocks are an exception
                break;
            }
            // break is not here intentionally - because the code is the same for category and course
        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            $parents = get_parent_cats($context); // cached internally

            // non recursive - should be faster
            foreach ($parents as $parentid) {
                $parentcontext = get_context_instance_by_id($parentid);
                if (isset($capabilities[$parentcontext->id][$capability])) {
                    return ($capabilities[$parentcontext->id][$capability]);
                }
            }
            // finally check system context
            $parentcontext = get_context_instance(CONTEXT_SYSTEM);
            $permission = capability_search($capability, $parentcontext, $capabilities, $switchroleactive);
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            $courseid = groups_get_course($context->instanceid);
            $parentcontext = get_context_instance(CONTEXT_COURSE, $courseid);
            $permission = capability_search($capability, $parentcontext, $capabilities, $switchroleactive);
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            $cm = get_record('course_modules','id',$context->instanceid);
            $parentcontext = get_context_instance(CONTEXT_COURSE, $cm->course);
            $permission = capability_search($capability, $parentcontext, $capabilities, $switchroleactive);
        break;

        case CONTEXT_BLOCK: // not necessarily 1 to 1 to course
            $block = get_record('block_instance','id',$context->instanceid);
            if ($block->pagetype == 'course-view') {
                $parentcontext = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            } else {
                $parentcontext = get_context_instance(CONTEXT_SYSTEM); 
            }           
            // ignore the $switchroleactive beause we want the real block view capability defined in system context
            $permission = capability_search($capability, $parentcontext, $capabilities, false);
        break;

        default:
            error ('This is an unknown context (' . $context->contextlevel . ') in capability_search!');
        return false;
    }

    return $permission;
}

/**
 * auxillary function for load_user_capabilities()
 * checks if context c1 is a parent (or itself) of context c2
 * @param int $c1 - context id of context 1
 * @param int $c2 - context id of context 2
 * @return bool
 */
function is_parent_context($c1, $c2) {
    static $parentsarray;
    
    // context can be itself and this is ok
    if ($c1 == $c2) {
        return true;  
    }
    // hit in cache?
    if (isset($parentsarray[$c1][$c2])) {
        return $parentsarray[$c1][$c2];
    }
    
    if (!$co2 = get_record('context', 'id', $c2)) {
        return false;
    }

    if (!$parents = get_parent_contexts($co2)) {
        return false;
    }
    
    foreach ($parents as $parent) {
        $parentsarray[$parent][$c2] = true;
    }

    if (in_array($c1, $parents)) {
        return true;  
    } else { // else not a parent, set the cache anyway
        $parentsarray[$c1][$c2] = false;
        return false;
    }
}


/**
 * auxillary function for load_user_capabilities()
 * handler in usort() to sort contexts according to level
 * @param object contexta
 * @param object contextb
 * @return int
 */
function roles_context_cmp($contexta, $contextb) {
   if ($contexta->contextlevel == $contextb->contextlevel) {
       return 0;
   }
   return ($contexta->contextlevel < $contextb->contextlevel) ? -1 : 1;
}

/**
 * It will build an array of all the capabilities at each level
 * i.e. site/metacourse/course_category/course/moduleinstance
 * Note we should only load capabilities if they are explicitly assigned already,
 * we should not load all module's capability!
 *
 * [Capabilities] => [26][forum_post] = 1
 *                   [26][forum_start] = -8990
 *                   [26][forum_edit] = -1
 *                   [273][blah blah] = 1
 *                   [273][blah blah blah] = 2
 *
 * @param $capability string - Only get a specific capability (string)
 * @param $context object - Only get capabilities for a specific context object
 * @param $userid integer - the id of the user whose capabilities we want to load
 * @param $checkenrolments boolean - Should we checkenrolment plugins (potentially expensive)
 * @return array of permissions (or nothing if they get assigned to $USER)
 */
function load_user_capability($capability='', $context=NULL, $userid=NULL, $checkenrolments=true) {

    global $USER, $CFG;

    // this flag has not been set! 
    // (not clean install, or upgraded successfully to 1.7 and up)
    if (empty($CFG->rolesactive)) {
        return false;
    }

    if (empty($userid)) {
        if (empty($USER->id)) {               // We have no user to get capabilities for
            debugging('User not logged in for load_user_capability!');
            return false;
        }
        unset($USER->capabilities);           // We don't want possible older capabilites hanging around

        if ($checkenrolments) {               // Call "enrol" system to ensure that we have the correct picture
            check_enrolment_plugins($USER); 
        }

        $userid = $USER->id;
        $otheruserid = false;
    } else {
        if (!$user = get_record('user', 'id', $userid)) {
            debugging('Non-existent userid in load_user_capability!');
            return false;
        }

        if ($checkenrolments) {               // Call "enrol" system to ensure that we have the correct picture
            check_enrolment_plugins($user);
        }

        $otheruserid = $userid;
    }


/// First we generate a list of all relevant contexts of the user

    $usercontexts = array();

    if ($context) { // if context is specified
        $usercontexts = get_parent_contexts($context);
        $usercontexts[] = $context->id;  // Add the current context as well
    } else { // else, we load everything
        if ($userroles = get_records('role_assignments','userid',$userid)) {
            foreach ($userroles as $userrole) {
                if (!in_array($userrole->contextid, $usercontexts)) {
                    $usercontexts[] = $userrole->contextid;
                }
            }
        }
    }

/// Set up SQL fragments for searching contexts

    if ($usercontexts) {
        $listofcontexts = '('.implode(',', $usercontexts).')';
        $searchcontexts1 = "c1.id IN $listofcontexts AND";
    } else {
        $searchcontexts1 = '';
    }

    if ($capability) {
        // the doanything may override the requested capability
        $capsearch = " AND (rc.capability = '$capability' OR rc.capability = 'moodle/site:doanything') ";
    } else {
        $capsearch ="";
    }

/// Then we use 1 giant SQL to bring out all relevant capabilities.
/// The first part gets the capabilities of orginal role.
/// The second part gets the capabilities of overriden roles.

    $siteinstance = get_context_instance(CONTEXT_SYSTEM);
    $capabilities = array();  // Reinitialize.
    
    // SQL for normal capabilities
    $SQL1 = "SELECT rc.capability, c1.id as id1, c1.id as id2, (c1.contextlevel * 100) AS aggrlevel,
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
                     rc.capability, c1.id, c1.contextlevel * 100
                     HAVING
                     SUM(rc.permission) != 0          
            
              UNION ALL
             
              SELECT rc.capability, c1.id as id1, c2.id as id2, (c1.contextlevel * 100 + c2.contextlevel) AS aggrlevel,                    
                     SUM(rc.permission) AS sum
                     FROM
                     {$CFG->prefix}role_assignments ra LEFT JOIN
                     {$CFG->prefix}role_capabilities rc on ra.roleid = rc.roleid LEFT JOIN
                     {$CFG->prefix}context c1 on ra.contextid = c1.id LEFT JOIN
                     {$CFG->prefix}context c2 on rc.contextid = c2.id LEFT JOIN
                     {$CFG->prefix}context_rel cr on cr.c1 = c2.id
                     WHERE
                     ra.userid=$userid AND
                     $searchcontexts1
                     rc.contextid != $siteinstance->id
                     $capsearch
                     AND cr.c2 = c1.id
              GROUP BY
                     rc.capability, c1.id, c2.id, c1.contextlevel * 100 + c2.contextlevel
                     HAVING
                     SUM(rc.permission) != 0
              ORDER BY
                     aggrlevel ASC";

    if (!$rs = get_recordset_sql($SQL1)) {
        error("Query failed in load_user_capability.");
    }

    if ($rs && $rs->RecordCount() > 0) {
        while ($caprec = rs_fetch_next_record($rs)) {
            $array = (array)$caprec;
            $temprecord = new object;

            foreach ($array as $key=>$val) {
                if ($key == 'aggrlevel') {
                    $temprecord->contextlevel = $val;
                } else {
                    $temprecord->{$key} = $val;
                }
            }
            $capabilities[] = $temprecord;
        }
        rs_close($rs);
    }

    // SQL for overrides
    // this is take out because we have no way of making sure c1 is indeed related to c2 (parent)
    // if we do not group by sum, it is possible to have multiple records of rc.capability, c1.id, c2.id, tuple having
    // different values, we can maually sum it when we go through the list
    
   /* 
    
    $SQL2 = "SELECT rc.capability, c1.id as id1, c2.id as id2, (c1.contextlevel * 100 + c2.contextlevel) AS aggrlevel,
                     rc.permission AS sum
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
                     rc.contextid != $siteinstance->id
                     $capsearch

              GROUP BY
                     rc.capability, (c1.contextlevel * 100 + c2.contextlevel), c1.id, c2.id, rc.permission
              ORDER BY
                     aggrlevel ASC
            ";*/

/*
    if (!$rs = get_recordset_sql($SQL2)) {
        error("Query failed in load_user_capability.");
    }

    if ($rs && $rs->RecordCount() > 0) {
        while ($caprec = rs_fetch_next_record($rs)) {
            $array = (array)$caprec;
            $temprecord = new object;

            foreach ($array as $key=>$val) {
                if ($key == 'aggrlevel') {
                    $temprecord->contextlevel = $val;
                } else {
                    $temprecord->{$key} = $val;
                }
            }
            // for overrides, we have to make sure that context2 is a child of context1
            // otherwise the combination makes no sense
            //if (is_parent_context($temprecord->id1, $temprecord->id2)) {
                $capabilities[] = $temprecord;
            //} // only write if relevant
        }
        rs_close($rs);
    }

    // this step sorts capabilities according to the contextlevel
    // it is very important because the order matters when we 
    // go through each capabilities later. (i.e. higher level contextlevel
    // will override lower contextlevel settings
    usort($capabilities, 'roles_context_cmp');
*/
    /* so up to this point we should have somethign like this
     * $capabilities[1]    ->contextlevel = 1000
                           ->module = 0 // changed from SITEID in 1.8 (??)
                           ->capability = do_anything
                           ->id = 1 (id is the context id)
                           ->sum = 0

     * $capabilities[2]     ->contextlevel = 1000
                            ->module = 0 // changed from SITEID in 1.8 (??)
                            ->capability = post_messages
                            ->id = 1
                            ->sum = -9000

     * $capabilittes[3]     ->contextlevel = 3000
                            ->module = course
                            ->capability = view_course_activities
                            ->id = 25
                            ->sum = 1

     * $capabilittes[4]     ->contextlevel = 3000
                            ->module = course
                            ->capability = view_course_activities
                            ->id = 26
                            ->sum = 0 (this is another course)

     * $capabilities[5]     ->contextlevel = 3050
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
     
    // This is the flag used for detecting the current context level. Since we are going through
    // the array in ascending order of context level. For normal capabilities, there should only 
    // be 1 value per (capability,  contextlevel, context), because they are already summed. But, 
    // for overrides, since we are processing them separate, we need to sum the relevcant entries. 
    // We set this flag when we hit a new level.
    // If the flag is already set, we keep adding (summing), otherwise, we just override previous 
    // settings (from lower level contexts)     
    $capflags = array(); // (contextid, contextlevel, capability)
    $usercap = array(); // for other user's capabilities
    foreach ($capabilities as $capability) {

        if (!$context = get_context_instance_by_id($capability->id2)) {
            continue; // incorrect stale context
        }

        if (!empty($otheruserid)) { // we are pulling out other user's capabilities, do not write to session

            if (capability_prohibits($capability->capability, $context, $capability->sum, $usercap)) {
                $usercap[$capability->id2][$capability->capability] = CAP_PROHIBIT;
                continue;
            }
            if (isset($usercap[$capability->id2][$capability->capability])) { // use isset because it can be sum 0
                if (!empty($capflags[$capability->id2][$capability->contextlevel][$capability->capability])) {
                    $usercap[$capability->id2][$capability->capability] += $capability->sum;
                } else { // else we override, and update flag
                    $usercap[$capability->id2][$capability->capability] = $capability->sum;
                    $capflags[$capability->id2][$capability->contextlevel][$capability->capability] = true;
                }
            } else {
                $usercap[$capability->id2][$capability->capability] = $capability->sum;
                $capflags[$capability->id2][$capability->contextlevel][$capability->capability] = true;
            }

        } else {

            if (capability_prohibits($capability->capability, $context, $capability->sum)) { // if any parent or parent's parent is set to prohibit
                $USER->capabilities[$capability->id2][$capability->capability] = CAP_PROHIBIT;
                continue;
            }

            // if no parental prohibit set
            // just write to session, i am not sure this is correct yet
            // since 3050 shows up after 3000, and 3070 shows up after 3050,
            // it should be ok just to overwrite like this, provided that there's no
            // parental prohibits
            // we need to write even if it's 0, because it could be an inherit override
            if (isset($USER->capabilities[$capability->id2][$capability->capability])) {
                if (!empty($capflags[$capability->id2][$capability->contextlevel][$capability->capability])) {
                    $USER->capabilities[$capability->id2][$capability->capability] += $capability->sum;
                } else { // else we override, and update flag
                    $USER->capabilities[$capability->id2][$capability->capability] = $capability->sum;
                    $capflags[$capability->id2][$capability->contextlevel][$capability->capability] = true;
                }
            } else {
                $USER->capabilities[$capability->id2][$capability->capability] = $capability->sum;
                $capflags[$capability->id2][$capability->contextlevel][$capability->capability] = true;
            }
        }
    }

    // now we don't care about the huge array anymore, we can dispose it.
    unset($capabilities);
    unset($capflags);

    if (!empty($otheruserid)) {
        return $usercap; // return the array
    }
}


/**
 *  A convenience function to completely load all the capabilities 
 *  for the current user.   This is what gets called from login, for example.
 */
function load_all_capabilities() {
    global $USER;

    //caching - helps user switching in cron
    static $defcaps = false;

    unset($USER->mycourses);        // Reset a cache used by get_my_courses

    if (isguestuser()) {
        load_guest_role();          // All non-guest users get this by default

    } else if (isloggedin()) {
        if ($defcaps === false) {
            $defcaps = load_defaultuser_role(true);
        }

        load_user_capability();

        // when in "course login as" - load only course caqpabilitites (it may not always work as expected)
        if (!empty($USER->realuser) and $USER->loginascontext->contextlevel != CONTEXT_SYSTEM) {
            $children = get_child_contexts($USER->loginascontext);
            $children[] = $USER->loginascontext->id;
            foreach ($USER->capabilities as $conid => $caps) {
                if (!in_array($conid, $children)) {
                    unset($USER->capabilities[$conid]);
                }
            }
        }

        // handle role switching in courses
        if (!empty($USER->switchrole)) {
            foreach ($USER->switchrole as $contextid => $roleid) {
                $context = get_context_instance_by_id($contextid);

                // first prune context and any child contexts
                $children = get_child_contexts($context);
                foreach ($children as $childid) {
                    unset($USER->capabilities[$childid]);
                }
                unset($USER->capabilities[$contextid]);

                // now merge all switched role caps in context and bellow
                $swithccaps = get_role_context_caps($roleid, $context);
                $USER->capabilities = merge_role_caps($USER->capabilities, $swithccaps);
            }
        }

        if (isset($USER->capabilities)) {
            $USER->capabilities = merge_role_caps($USER->capabilities, $defcaps);
        } else {
            $USER->capabilities = $defcaps;
        }

    } else {
        load_notloggedin_role();
    }
}


/**
 * Check all the login enrolment information for the given user object
 * by querying the enrolment plugins
 */
function check_enrolment_plugins(&$user) {
    global $CFG;

    static $inprogress;  // To prevent this function being called more than once in an invocation

    if (!empty($inprogress[$user->id])) {
        return;
    }

    $inprogress[$user->id] = true;  // Set the flag

    require_once($CFG->dirroot .'/enrol/enrol.class.php');

    if (!($plugins = explode(',', $CFG->enrol_plugins_enabled))) {
        $plugins = array($CFG->enrol);
    }

    foreach ($plugins as $plugin) {
        $enrol = enrolment_factory::factory($plugin);
        if (method_exists($enrol, 'setup_enrolments')) {  /// Plugin supports Roles (Moodle 1.7 and later)
            $enrol->setup_enrolments($user);
        } else {                                          /// Run legacy enrolment methods
            if (method_exists($enrol, 'get_student_courses')) {
                $enrol->get_student_courses($user);
            }
            if (method_exists($enrol, 'get_teacher_courses')) {
                $enrol->get_teacher_courses($user);
            }

        /// deal with $user->students and $user->teachers stuff
            unset($user->student);
            unset($user->teacher);
        }
        unset($enrol);
    }

    unset($inprogress[$user->id]);  // Unset the flag
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

    // caching, mainly to save unnecessary sqls
    static $prohibits; //[capability][contextid]
    if (isset($prohibits[$capability][$context->id])) {
        return $prohibits[$capability][$context->id];
    }
    
    if (empty($context->id)) {
        $prohibits[$capability][$context->id] = false;
        return false;
    }

    if (empty($capability)) {
        $prohibits[$capability][$context->id] = false;
        return false;
    }

    if ($sum < (CAP_PROHIBIT/2)) {
        // If this capability is set to prohibit.
        $prohibits[$capability][$context->id] = true;
        return true;
    }

    if (!empty($array)) {
        if (isset($array[$context->id][$capability])
                && $array[$context->id][$capability] < (CAP_PROHIBIT/2)) {
            $prohibits[$capability][$context->id] = true;
            return true;
        }
    } else {
        // Else if set in session.
        if (isset($USER->capabilities[$context->id][$capability])
                && $USER->capabilities[$context->id][$capability] < (CAP_PROHIBIT/2)) {
            $prohibits[$capability][$context->id] = true;
            return true;
        }
    }
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:
            // By now it's a definite an inherit.
            return 0;
        break;

        case CONTEXT_PERSONAL:
            $parent = get_context_instance(CONTEXT_SYSTEM);
            $prohibits[$capability][$context->id] = capability_prohibits($capability, $parent);
            return $prohibits[$capability][$context->id];
        break;

        case CONTEXT_USER:
            $parent = get_context_instance(CONTEXT_SYSTEM);
            $prohibits[$capability][$context->id] = capability_prohibits($capability, $parent);
            return $prohibits[$capability][$context->id];
        break;

        case CONTEXT_COURSECAT:
        case CONTEXT_COURSE:
            $parents = get_parent_cats($context); // cached internally
            // no workaround for recursion now - it needs some more work and maybe fixing

            if (empty($parents)) {
                // system context - this is either top category or frontpage course
                $parent = get_context_instance(CONTEXT_SYSTEM);
            } else {
                // parent context - recursion
                $parentid = array_pop($parents);
                $parent = get_context_instance_by_id($parentid);
            }
            $prohibits[$capability][$context->id] = capability_prohibits($capability, $parent);
            return $prohibits[$capability][$context->id];
        break;

        case CONTEXT_GROUP:
            // 1 to 1 to course.
            if (!$courseid = groups_get_course($context->instanceid)) {
                $prohibits[$capability][$context->id] = false;
                return false;
            }
            $parent = get_context_instance(CONTEXT_COURSE, $courseid);
            $prohibits[$capability][$context->id] = capability_prohibits($capability, $parent);
            return $prohibits[$capability][$context->id];
        break;

        case CONTEXT_MODULE:
            // 1 to 1 to course.
            if (!$cm = get_record('course_modules','id',$context->instanceid)) {
                $prohibits[$capability][$context->id] = false;
                return false;
            }
            $parent = get_context_instance(CONTEXT_COURSE, $cm->course);
            $prohibits[$capability][$context->id] = capability_prohibits($capability, $parent);
            return $prohibits[$capability][$context->id];
        break;

        case CONTEXT_BLOCK:
            // 1 to 1 to course.
            if (!$block = get_record('block_instance','id',$context->instanceid)) {
                $prohibits[$capability][$context->id] = false;
                return false;
            }
            if ($block->pagetype == 'course-view') {
                $parent = get_context_instance(CONTEXT_COURSE, $block->pageid); // needs check
            } else {
                $parent = get_context_instance(CONTEXT_SYSTEM); 
            }           
            $prohibits[$capability][$context->id] = capability_prohibits($capability, $parent);
            return $prohibits[$capability][$context->id];
        break;

        default:
            print_error('unknowncontext');
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

/// Create a system wide context for assignemnt.
    $systemcontext = $context = get_context_instance(CONTEXT_SYSTEM);


/// Create default/legacy roles and capabilities.
/// (1 legacy capability per legacy role at system level).

    $adminrole          = create_role(addslashes(get_string('administrator')), 'admin',
                                      addslashes(get_string('administratordescription')), 'moodle/legacy:admin');
    $coursecreatorrole  = create_role(addslashes(get_string('coursecreators')), 'coursecreator',
                                      addslashes(get_string('coursecreatorsdescription')), 'moodle/legacy:coursecreator');
    $editteacherrole    = create_role(addslashes(get_string('defaultcourseteacher')), 'editingteacher',
                                      addslashes(get_string('defaultcourseteacherdescription')), 'moodle/legacy:editingteacher');
    $noneditteacherrole = create_role(addslashes(get_string('noneditingteacher')), 'teacher',
                                      addslashes(get_string('noneditingteacherdescription')), 'moodle/legacy:teacher');
    $studentrole        = create_role(addslashes(get_string('defaultcoursestudent')), 'student',
                                      addslashes(get_string('defaultcoursestudentdescription')), 'moodle/legacy:student');
    $guestrole          = create_role(addslashes(get_string('guest')), 'guest',
                                      addslashes(get_string('guestdescription')), 'moodle/legacy:guest');
    $userrole           = create_role(addslashes(get_string('authenticateduser')), 'user',
                                      addslashes(get_string('authenticateduserdescription')), 'moodle/legacy:user');
    
/// Now is the correct moment to install capabilities - after creation of legacy roles, but before assigning of roles

    if (!assign_capability('moodle/site:doanything', CAP_ALLOW, $adminrole, $systemcontext->id)) {
        error('Could not assign moodle/site:doanything to the admin role');
    }
    if (!update_capabilities()) {
        error('Had trouble upgrading the core capabilities for the Roles System');
    }

/// Look inside user_admin, user_creator, user_teachers, user_students and
/// assign above new roles. If a user has both teacher and student role,
/// only teacher role is assigned. The assignment should be system level.

    $dbtables = $db->MetaTables('TABLES');

/// Set up the progress bar

    $usertables = array('user_admins', 'user_coursecreators', 'user_teachers', 'user_students');

    $totalcount = $progresscount = 0;
    foreach ($usertables as $usertable) {
        if (in_array($CFG->prefix.$usertable, $dbtables)) {
             $totalcount += count_records($usertable);
        }
    }

    print_progress(0, $totalcount, 5, 1, 'Processing role assignments');

/// Upgrade the admins.
/// Sort using id ASC, first one is primary admin.

    if (in_array($CFG->prefix.'user_admins', $dbtables)) {
        if ($rs = get_recordset_sql('SELECT * from '.$CFG->prefix.'user_admins ORDER BY ID ASC')) {
            while ($admin = rs_fetch_next_record($rs)) {
                role_assign($adminrole, $admin->userid, 0, $systemcontext->id);
                $progresscount++;
                print_progress($progresscount, $totalcount, 5, 1, 'Processing role assignments');
            }
            rs_close($rs);
        }
    } else {
        // This is a fresh install.
    }


/// Upgrade course creators.
    if (in_array($CFG->prefix.'user_coursecreators', $dbtables)) {
        if ($rs = get_recordset('user_coursecreators')) {
            while ($coursecreator = rs_fetch_next_record($rs)) {
                role_assign($coursecreatorrole, $coursecreator->userid, 0, $systemcontext->id);
                $progresscount++;
                print_progress($progresscount, $totalcount, 5, 1, 'Processing role assignments');
            }
            rs_close($rs);
        }
    }


/// Upgrade editting teachers and non-editting teachers.
    if (in_array($CFG->prefix.'user_teachers', $dbtables)) {
        if ($rs = get_recordset('user_teachers')) {
            while ($teacher = rs_fetch_next_record($rs)) {
                
                // removed code here to ignore site level assignments
                // since the contexts are separated now
                
                // populate the user_lastaccess table
                $access = new object();
                $access->timeaccess = $teacher->timeaccess;
                $access->userid = $teacher->userid;
                $access->courseid = $teacher->course;
                insert_record('user_lastaccess', $access);

                // assign the default student role
                $coursecontext = get_context_instance(CONTEXT_COURSE, $teacher->course); // needs cache
                // hidden teacher
                if ($teacher->authority == 0) {
                    $hiddenteacher = 1;  
                } else {
                    $hiddenteacher = 0;  
                }              
                
                if ($teacher->editall) { // editting teacher
                    role_assign($editteacherrole, $teacher->userid, 0, $coursecontext->id, 0, 0, $hiddenteacher);
                } else {
                    role_assign($noneditteacherrole, $teacher->userid, 0, $coursecontext->id, 0, 0, $hiddenteacher);
                }
                $progresscount++;
                print_progress($progresscount, $totalcount, 5, 1, 'Processing role assignments');
            }
            rs_close($rs);
        }
    }


/// Upgrade students.
    if (in_array($CFG->prefix.'user_students', $dbtables)) {
        if ($rs = get_recordset('user_students')) {
            while ($student = rs_fetch_next_record($rs)) {

                // populate the user_lastaccess table
                $access = new object;
                $access->timeaccess = $student->timeaccess;
                $access->userid = $student->userid;
                $access->courseid = $student->course;
                insert_record('user_lastaccess', $access);

                // assign the default student role
                $coursecontext = get_context_instance(CONTEXT_COURSE, $student->course);
                role_assign($studentrole, $student->userid, 0, $coursecontext->id);
                $progresscount++;
                print_progress($progresscount, $totalcount, 5, 1, 'Processing role assignments');
            }
            rs_close($rs);
        }
    }


/// Upgrade guest (only 1 entry).
    if ($guestuser = get_record('user', 'username', 'guest')) {
        role_assign($guestrole, $guestuser->id, 0, $systemcontext->id);
    }
    print_progress($totalcount, $totalcount, 5, 1, 'Processing role assignments');


/// Insert the correct records for legacy roles
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

/// Set up default permissions for overrides
    allow_override($adminrole, $adminrole);
    allow_override($adminrole, $coursecreatorrole);
    allow_override($adminrole, $noneditteacherrole);
    allow_override($adminrole, $editteacherrole);
    allow_override($adminrole, $studentrole);
    allow_override($adminrole, $guestrole);
    allow_override($adminrole, $userrole);


/// Delete the old user tables when we are done

    drop_table(new XMLDBTable('user_students'));
    drop_table(new XMLDBTable('user_teachers'));
    drop_table(new XMLDBTable('user_coursecreators'));
    drop_table(new XMLDBTable('user_admins'));

}

/**
 * Returns array of all legacy roles.
 */
function get_legacy_roles() {
    return array(
        'admin'          => 'moodle/legacy:admin',
        'coursecreator'  => 'moodle/legacy:coursecreator',
        'editingteacher' => 'moodle/legacy:editingteacher',
        'teacher'        => 'moodle/legacy:teacher',
        'student'        => 'moodle/legacy:student',
        'guest'          => 'moodle/legacy:guest',
        'user'           => 'moodle/legacy:user'
    );
}

function get_legacy_type($roleid) {
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    $legacyroles = get_legacy_roles();

    $result = '';
    foreach($legacyroles as $ltype=>$lcap) {
        $localoverride = get_local_override($roleid, $sitecontext->id, $lcap);
        if (!empty($localoverride->permission) and $localoverride->permission == CAP_ALLOW) {
            //choose first selected legacy capability - reset the rest
            if (empty($result)) {
                $result = $ltype;
            } else {
                unassign_capability($lcap, $roleid);
            } 
        }
    }

    return $result;
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

    $legacyroles = get_legacy_roles();

    foreach ($legacyperms as $type => $perm) {

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);

        if (!array_key_exists($type, $legacyroles)) {
            error('Incorrect legacy role definition for type: '.$type);
        }

        if ($roles = get_roles_with_capability($legacyroles[$type], CAP_ALLOW)) {
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
    if (strpos($capabilityname, 'moodle/legacy') === 0) {
        return true;
    } else {
        return false;
    }
}



/**********************************
 * Context Manipulation functions *
 **********************************/

/**
 * Create a new context record for use by all roles-related stuff
 * @param $level
 * @param $instanceid
 *
 * @return object newly created context (or existing one with a debug warning)
 */
function create_context($contextlevel, $instanceid) {
    if (!$context = get_record('context','contextlevel',$contextlevel,'instanceid',$instanceid)) {
        if (!validate_context($contextlevel, $instanceid)) {
            debugging('Error: Invalid context creation request for level "'.s($contextlevel).'", instance "'.s($instanceid).'".');
            return NULL;
        }
        if ($contextlevel == CONTEXT_SYSTEM) {
            return create_system_context();
            
        }
        $context = new object();
        $context->contextlevel = $contextlevel;
        $context->instanceid = $instanceid;
        if ($id = insert_record('context',$context)) {
            // we need to populate context_rel for every new context inserted
            $c = get_record('context','id',$id);
            insert_context_rel ($c);           
            return $c;
        } else {
            debugging('Error: could not insert new context level "'.s($contextlevel).'", instance "'.s($instanceid).'".');
            return NULL;
        }
    } else {
        debugging('Warning: Context id "'.s($context->id).'" not created, because it already exists.');
        return $context;
    }
}

/**
 * This hacky function is needed because we can not change system context instanceid using normal upgrade routine.
 */
function create_system_context() {
    if ($context = get_record('context', 'contextlevel', CONTEXT_SYSTEM, 'instanceid', SITEID)) {
        // we are going to change instanceid of system context to 0 now
        $context->instanceid = 0;
        update_record('context', $context);
        //context rel not affected
        return $context;

    } else {
        $context = new object();
        $context->contextlevel = CONTEXT_SYSTEM;
        $context->instanceid = 0;
        if ($context->id = insert_record('context',$context)) {
            // we need not to populate context_rel for system context
            return $context;
        } else {
            debugging('Can not create system context');
            return NULL;
        }
    }
}
/**
 * Create a new context record for use by all roles-related stuff
 * @param $level
 * @param $instanceid
 *
 * @return true if properly deleted
 */
function delete_context($contextlevel, $instanceid) {
    if ($context = get_context_instance($contextlevel, $instanceid)) {        
        delete_records('context_rel', 'c2', $context->id); // might not be a parent
        return delete_records('context', 'id', $context->id) &&
               delete_records('role_assignments', 'contextid', $context->id) &&
               delete_records('role_capabilities', 'contextid', $context->id) && 
               delete_records('context_rel', 'c1', $context->id);
    }
    return true;
}

/**
 * Validate that object with instanceid really exists in given context level.
 *
 * return if instanceid object exists
 */
function validate_context($contextlevel, $instanceid) {
    switch ($contextlevel) {

        case CONTEXT_SYSTEM:
            return ($instanceid == 0);

        case CONTEXT_PERSONAL:
            return (boolean)count_records('user', 'id', $instanceid);

        case CONTEXT_USER:
            return (boolean)count_records('user', 'id', $instanceid);

        case CONTEXT_COURSECAT:
            if ($instanceid == 0) {
                return true; // site course category
            }
            return (boolean)count_records('course_categories', 'id', $instanceid);

        case CONTEXT_COURSE:
            return (boolean)count_records('course', 'id', $instanceid);

        case CONTEXT_GROUP:
            return groups_group_exists($instanceid);

        case CONTEXT_MODULE:
            return (boolean)count_records('course_modules', 'id', $instanceid);

        case CONTEXT_BLOCK:
            return (boolean)count_records('block_instance', 'id', $instanceid);

        default:
            return false;
    }
}

/**
 * Get the context instance as an object. This function will create the
 * context instance if it does not exist yet.
 * @param integer $level The context level, for example CONTEXT_COURSE, or CONTEXT_MODULE.
 * @param integer $instance The instance id. For $level = CONTEXT_COURSE, this would be $course->id,
 *      for $level = CONTEXT_MODULE, this would be $cm->id. And so on.
 * @return object The context object.
 */
function get_context_instance($contextlevel=NULL, $instance=0) {

    global $context_cache, $context_cache_id, $CONTEXT;
    static $allowed_contexts = array(CONTEXT_SYSTEM, CONTEXT_PERSONAL, CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_GROUP, CONTEXT_MODULE, CONTEXT_BLOCK);

    // Yu: Separating site and site course context - removed CONTEXT_COURSE override when SITEID
    
    // fix for MDL-9016
    if ($contextlevel == 'clearcache') {
        // Clear ALL cache
        $context_cache = array();
        $context_cache_id = array();
        $CONTEXT = '';       
        return false;
    }

/// If no level is supplied then return the current global context if there is one
    if (empty($contextlevel)) {
        if (empty($CONTEXT)) {
            //fatal error, code must be fixed
            error("Error: get_context_instance() called without a context");
        } else {
            return $CONTEXT;
        }
    }

/// Backwards compatibility with obsoleted (CONTEXT_SYSTEM, SITEID)
    if ($contextlevel == CONTEXT_SYSTEM) {
        $instance = 0;
    }

/// check allowed context levels
    if (!in_array($contextlevel, $allowed_contexts)) {
        // fatal error, code must be fixed - probably typo or switched parameters
        error('Error: get_context_instance() called with incorrect context level "'.s($contextlevel).'"');
    }

/// Check the cache
    if (isset($context_cache[$contextlevel][$instance])) {  // Already cached
        return $context_cache[$contextlevel][$instance];
    }

/// Get it from the database, or create it
    if (!$context = get_record('context', 'contextlevel', $contextlevel, 'instanceid', $instance)) {
        create_context($contextlevel, $instance);
        $context = get_record('context', 'contextlevel', $contextlevel, 'instanceid', $instance);
    }

/// Only add to cache if context isn't empty.
    if (!empty($context)) {
        $context_cache[$contextlevel][$instance] = $context;    // Cache it for later
        $context_cache_id[$context->id] = $context;      // Cache it for later
    }

    return $context;
}


/**
 * Get a context instance as an object, from a given context id.
 * @param $id a context id.
 * @return object The context object.
 */
function get_context_instance_by_id($id) {

    global $context_cache, $context_cache_id;

    if (isset($context_cache_id[$id])) {  // Already cached
        return $context_cache_id[$id];
    }

    if ($context = get_record('context', 'id', $id)) {   // Update the cache and return
        $context_cache[$context->contextlevel][$context->instanceid] = $context;
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
 * @param shortname - role short name
 * @param description - role description
 * @param legacy - optional legacy capability
 * @return id or false
 */
function create_role($name, $shortname, $description, $legacy='') {

    // check for duplicate role name

    if ($role = get_record('role','name', $name)) {
        error('there is already a role with this name!');
    }

    if ($role = get_record('role','shortname', $shortname)) {
        error('there is already a role with this shortname!');
    }

    $role = new object();
    $role->name = $name;
    $role->shortname = $shortname;
    $role->description = $description;

    //find free sortorder number
    $role->sortorder = count_records('role');
    while (get_record('role','sortorder', $role->sortorder)) {
        $role->sortorder += 1;
    }

    if (!$context = get_context_instance(CONTEXT_SYSTEM)) {
        return false;
    }

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
 * function that deletes a role and cleanups up after it
 * @param roleid - id of role to delete
 * @return success
 */
function delete_role($roleid) {
    $success = true;

// mdl 10149, check if this is the last active admin role
// if we make the admin role not deletable then this part can go
    
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    
    if ($role = get_record('role', 'id', $roleid)) {
        if (record_exists('role_capabilities', 'contextid', $systemcontext->id, 'roleid', $roleid, 'capability', 'moodle/site:doanything')) {
            // deleting an admin role
            $status = false;
            if ($adminroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $systemcontext)) {
                foreach ($adminroles as $adminrole) {
                    if ($adminrole->id != $roleid) {
                        // some other admin role
                        if (record_exists('role_assignments', 'roleid', $adminrole->id, 'contextid', $systemcontext->id)) {
                            // found another admin role with at least 1 user assigned  
                            $status = true;
                            break;
                        }
                    }
                }        
            } 
            if ($status !== true) {
                error ('You can not delete this role because there is no other admin roles with users assigned');  
            }
        }  
    }

// first unssign all users
    if (!role_unassign($roleid)) {
        debugging("Error while unassigning all users from role with ID $roleid!");
        $success = false;
    }

// cleanup all references to this role, ignore errors
    if ($success) {
        delete_records('role_capabilities', 'roleid', $roleid);
        delete_records('role_allow_assign', 'roleid', $roleid);
        delete_records('role_allow_assign', 'allowassign', $roleid);
        delete_records('role_allow_override', 'roleid', $roleid);
        delete_records('role_allow_override', 'allowoverride', $roleid);
        delete_records('role_names', 'roleid', $roleid);
    }

// finally delete the role itself
    if ($success and !delete_records('role', 'id', $roleid)) {
        debugging("Could not delete role record with ID $roleid!");
        $success = false;
    }

    return $success;
}

/**
 * Function to write context specific overrides, or default capabilities.
 * @param module - string name
 * @param capability - string name
 * @param contextid - context id
 * @param roleid - role id
 * @param permission - int 1,-1 or -1000
 * should not be writing if permission is 0
 */
function assign_capability($capability, $permission, $roleid, $contextid, $overwrite=false) {

    global $USER;

    if (empty($permission) || $permission == CAP_INHERIT) { // if permission is not set
        unassign_capability($capability, $roleid, $contextid);
        return true;
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
 * Get the roles that have a given capability assigned to it. This function
 * does not resolve the actual permission of the capability. It just checks
 * for assignment only.
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
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
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
function role_assign($roleid, $userid, $groupid, $contextid, $timestart=0, $timeend=0, $hidden=0, $enrol='manual',$timemodified='') {
    global $USER, $CFG;

    debugging("Assign roleid $roleid userid $userid contextid $contextid", DEBUG_DEVELOPER);

/// Do some data validation

    if (empty($roleid)) {
        debugging('Role ID not provided');
        return false;
    }

    if (empty($userid) && empty($groupid)) {
        debugging('Either userid or groupid must be provided');
        return false;
    }

    if ($userid && !record_exists('user', 'id', $userid)) {
        debugging('User ID '.intval($userid).' does not exist!');
        return false;
    }

    if ($groupid && !groups_group_exists($groupid)) {
        debugging('Group ID '.intval($groupid).' does not exist!');
        return false;
    }

    if (!$context = get_context_instance_by_id($contextid)) {
        debugging('Context ID '.intval($contextid).' does not exist!');
        return false;
    }

    if (($timestart and $timeend) and ($timestart > $timeend)) {
        debugging('The end time can not be earlier than the start time');
        return false;
    }

    if (!$timemodified) {
        $timemodified = time();  
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
        $newra->hidden = $hidden;
        $newra->enrol = $enrol;
    /// Always round timestart downto 100 secs to help DBs to use their own caching algorithms 
    /// by repeating queries with the same exact parameters in a 100 secs time window
        $newra->timestart = round($timestart, -2);
        $newra->timeend = $timeend;
        $newra->timemodified = $timemodified;
        $newra->modifierid = empty($USER->id) ? 0 : $USER->id;

        $success = insert_record('role_assignments', $newra);

    } else {                      // We already have one, just update it

        $newra->id = $ra->id;
        $newra->hidden = $hidden;
        $newra->enrol = $enrol;
    /// Always round timestart downto 100 secs to help DBs to use their own caching algorithms 
    /// by repeating queries with the same exact parameters in a 100 secs time window
        $newra->timestart = round($timestart, -2);
        $newra->timeend = $timeend;
        $newra->timemodified = $timemodified;
        $newra->modifierid = empty($USER->id) ? 0 : $USER->id;

        $success = update_record('role_assignments', $newra);
    }

    if ($success) {   /// Role was assigned, so do some other things

    /// If the user is the current user, then reload the capabilities too.
        if (!empty($USER->id) && $USER->id == $userid) {
            load_all_capabilities();
        }
        
    /// Ask all the modules if anything needs to be done for this user
        if ($mods = get_list_of_plugins('mod')) {
            foreach ($mods as $mod) {
                include_once($CFG->dirroot.'/mod/'.$mod.'/lib.php');
                $functionname = $mod.'_role_assign';
                if (function_exists($functionname)) {
                    $functionname($userid, $context, $roleid);
                }
            }
        }

    /// Make sure they have an entry in user_lastaccess for courses they can access
    //    role_add_lastaccess_entries($userid, $context);
    }

    /// now handle metacourse role assignments if in course context
    if ($success and $context->contextlevel == CONTEXT_COURSE) {
        if ($parents = get_records('course_meta', 'child_course', $context->instanceid)) {
            foreach ($parents as $parent) {
                sync_metacourse($parent->parent_course);
            }
        }
    }

    return $success;
}


/**
 * Deletes one or more role assignments.   You must specify at least one parameter.
 * @param $roleid
 * @param $userid
 * @param $groupid
 * @param $contextid
 * @param $enrol unassign only if enrolment type matches, NULL means anything
 * @return boolean - success or failure
 */
function role_unassign($roleid=0, $userid=0, $groupid=0, $contextid=0, $enrol=NULL) {

    global $USER, $CFG;

    $success = true;

    $args = array('roleid', 'userid', 'groupid', 'contextid');
    $select = array();
    foreach ($args as $arg) {
        if ($$arg) {
            $select[] = $arg.' = '.$$arg;
        }
    }
    if (!empty($enrol)) {
        $select[] = "enrol='$enrol'";
    }

    if ($select) {
        if ($ras = get_records_select('role_assignments', implode(' AND ', $select))) {
            $mods = get_list_of_plugins('mod');
            foreach($ras as $ra) {
                /// infinite loop protection when deleting recursively
                if (!$ra = get_record('role_assignments', 'id', $ra->id)) {
                    continue;
                }
                $success = delete_records('role_assignments', 'id', $ra->id) and $success;

                /// If the user is the current user, then reload the capabilities too.
                if (!empty($USER->id) && $USER->id == $ra->userid) {
                    load_all_capabilities();
                }
                $context = get_record('context', 'id', $ra->contextid);

                /// Ask all the modules if anything needs to be done for this user
                foreach ($mods as $mod) {
                    include_once($CFG->dirroot.'/mod/'.$mod.'/lib.php');
                    $functionname = $mod.'_role_unassign';
                    if (function_exists($functionname)) {
                        $functionname($ra->userid, $context); // watch out, $context might be NULL if something goes wrong
                    }
                }

                /// now handle metacourse role unassigment and removing from goups if in course context
                if (!empty($context) and $context->contextlevel == CONTEXT_COURSE) {

                    // cleanup leftover course groups/subscriptions etc when user has 
                    // no capability to view course
                    // this may be slow, but this is the proper way of doing it
                    if (!has_capability('moodle/course:view', $context, $ra->userid)) {
                        // remove from groups
                        if ($groups = get_groups($context->instanceid, $ra->userid)) {
                            foreach ($groups as $group) {
                                delete_records('groups_members', 'groupid', $group->id, 'userid', $ra->userid);
                            }
                        }

                        // delete lastaccess records
                        delete_records('user_lastaccess', 'userid', $ra->userid, 'courseid', $context->instanceid);
                    }

                    //unassign roles in metacourses if needed
                    if ($parents = get_records('course_meta', 'child_course', $context->instanceid)) {
                        foreach ($parents as $parent) {
                            sync_metacourse($parent->parent_course);
                        }
                    }
                }
            }
        }
    }

    return $success;
}

/**
 * A convenience function to take care of the common case where you
 * just want to enrol someone using the default role into a course
 *
 * @param object $course
 * @param object $user
 * @param string $enrol - the plugin used to do this enrolment
 */
function enrol_into_course($course, $user, $enrol) {

    if ($course->enrolperiod) {
        $timestart = time();
        $timeend = time() + $course->enrolperiod;
    } else {
        $timestart = $timeend = 0;
    }

    if ($role = get_default_course_role($course)) {

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if (!role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, $enrol)) {
            return false;
        }

        email_welcome_message_to_user($course, $user);

        add_to_log($course->id, 'course', 'enrol', 'view.php?id='.$course->id, $user->id);

        return true;
    }

    return false;
}

/**
 * Add last access times to user_lastaccess as required
 * @param $userid
 * @param $context
 * @return boolean - success or failure
 */
function role_add_lastaccess_entries($userid, $context) {

    global $USER, $CFG;

    if (empty($context->contextlevel)) {
        return false;
    }

    $lastaccess = new object;        // Reusable object below
    $lastaccess->userid = $userid;
    $lastaccess->timeaccess = 0;

    switch ($context->contextlevel) {

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
        } else if ($compparts[0] == 'format') {
            // Similar to the above, course formats are 'format' while they 
            // are stored in 'course/format'.
            $defpath = $CFG->dirroot.'/course/'.$component.'/db/access.php';
            $varprefix = $compparts[0].'_'.$compparts[1];
        } else {
            $defpath = $CFG->dirroot.'/'.$component.'/db/access.php';
            $varprefix = str_replace('/', '_', $component);
        }
    }
    $capabilities = array();

    if (file_exists($defpath)) {
        require($defpath);
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
 * Returns default capabilities for given legacy role type.
 *
 * @param string legacy role name
 * @return array
 */
function get_default_capabilities($legacyrole) {
    if (!$allcaps = get_records('capabilities')) {
        error('Error: no capabilitites defined!');
    }
    $alldefs = array();
    $defaults = array();
    $components = array();
    foreach ($allcaps as $cap) {
        if (!in_array($cap->component, $components)) {
            $components[] = $cap->component;
            $alldefs = array_merge($alldefs, load_capability_def($cap->component));
        }
    }
    foreach($alldefs as $name=>$def) {
        if (isset($def['legacy'][$legacyrole])) {
            $defaults[$name] = $def['legacy'][$legacyrole];
        }
    }

    //some exceptions
    $defaults['moodle/legacy:'.$legacyrole] = CAP_ALLOW;
    if ($legacyrole == 'admin') {
        $defaults['moodle/site:doanything'] = CAP_ALLOW;
    }
    return $defaults;
}

/**
 * Reset role capabilitites to default according to selected legacy capability.
 * If several legacy caps selected, use the first from get_default_capabilities.
 * If no legacy selected, removes all capabilities.
 *
 * @param int @roleid
 */
function reset_role_capabilities($roleid) {
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    $legacyroles = get_legacy_roles();

    $defaultcaps = array();
    foreach($legacyroles as $ltype=>$lcap) {
        $localoverride = get_local_override($roleid, $sitecontext->id, $lcap);
        if (!empty($localoverride->permission) and $localoverride->permission == CAP_ALLOW) {
            //choose first selected legacy capability
            $defaultcaps = get_default_capabilities($ltype);
            break;
        }
    }

    delete_records('role_capabilities', 'roleid', $roleid);
    if (!empty($defaultcaps)) {
        foreach($defaultcaps as $cap=>$permission) {
            assign_capability($cap, $permission, $roleid, $sitecontext->id);
        }
    }
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
            // update risk bitmasks and context levels in existing capabilities if needed
            if (array_key_exists($cachedcap->name, $filecaps)) {
                if (!array_key_exists('riskbitmask', $filecaps[$cachedcap->name])) {
                    $filecaps[$cachedcap->name]['riskbitmask'] = 0; // no risk if not specified
                }
                if ($cachedcap->riskbitmask != $filecaps[$cachedcap->name]['riskbitmask']) {
                    $updatecap = new object();
                    $updatecap->id = $cachedcap->id;
                    $updatecap->riskbitmask = $filecaps[$cachedcap->name]['riskbitmask'];
                    if (!update_record('capabilities', $updatecap)) {
                        return false;
                    }
                }

                if (!array_key_exists('contextlevel', $filecaps[$cachedcap->name])) {
                    $filecaps[$cachedcap->name]['contextlevel'] = 0; // no context level defined
                }
                if ($cachedcap->contextlevel != $filecaps[$cachedcap->name]['contextlevel']) {
                    $updatecap = new object();
                    $updatecap->id = $cachedcap->id;
                    $updatecap->contextlevel = $filecaps[$cachedcap->name]['contextlevel'];
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
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM: // by now it's a definite an inherit
            $name = get_string('coresystem');
            break;

        case CONTEXT_PERSONAL:
            $name = get_string('personal');
            break;

        case CONTEXT_USER:
            if ($user = get_record('user', 'id', $context->instanceid)) {
                $name = get_string('user').': '.fullname($user);
            }
            break;

        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            if ($category = get_record('course_categories', 'id', $context->instanceid)) {
                $name = get_string('category').': '. format_string($category->name);
            }
            break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            if ($course = get_record('course', 'id', $context->instanceid)) {
              
                if ($context->instanceid == SITEID) {
                    $name = get_string('site').': '. format_string($course->fullname);
                } else {
                    $name = get_string('course').': '. format_string($course->fullname);
                }
            }
            break;

        case CONTEXT_GROUP: // 1 to 1 to course
            if ($name = groups_get_group_name($context->instanceid)) {
                $name = get_string('group').': '. $name;
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
            error ('This is an unknown context (' . $context->contextlevel . ') in print_context_name!');
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

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM: // all
            $SQL = "select * from {$CFG->prefix}capabilities";
        break;

        case CONTEXT_PERSONAL:
            $SQL = "select * from {$CFG->prefix}capabilities where contextlevel = ".CONTEXT_PERSONAL;
        break;

        case CONTEXT_USER:
            $SQL = "SELECT * 
                    FROM {$CFG->prefix}capabilities 
                    WHERE contextlevel = ".CONTEXT_USER;
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

    if (!$records = get_records_sql($SQL.' '.$sort)) {
        $records = array();
    }

/// the rest of code is a bit hacky, think twice before modifying it :-(

    // special sorting of core system capabiltites and enrollments
    if (in_array($context->contextlevel, array(CONTEXT_SYSTEM, CONTEXT_COURSECAT, CONTEXT_COURSE))) {
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
    } else {
        $contextindependentcaps = fetch_context_independent_capabilities();
        $records = array_merge($contextindependentcaps, $records);
    }

    return $records;

}


/**
 * Gets the context-independent capabilities that should be overrridable in
 * any context.
 * @return array of capability records from the capabilities table.
 */
function fetch_context_independent_capabilities() {

    //only CONTEXT_SYSTEM capabilities here or it will break the hack in fetch_context_capabilities()
    $contextindependentcaps = array(
        'moodle/site:accessallgroups'
        );

    $records = array();

    foreach ($contextindependentcaps as $capname) {
        $record = get_record('capabilities', 'name', $capname);
        array_push($records, $record);
    }
    return $records;
}


/**
 * This function pulls out all the resolved capabilities (overrides and
 * defaults) of a role used in capability overrides in contexts at a given
 * context.
 * @param obj $context
 * @param int $roleid
 * @param bool self - if set to true, resolve till this level, else stop at immediate parent level
 * @return array
 */
function role_context_capabilities($roleid, $context, $cap='') {
    global $CFG;

    $contexts = get_parent_contexts($context);
    $contexts[] = $context->id;
    $contexts = '('.implode(',', $contexts).')';

    if ($cap) {
        $search = " AND rc.capability = '$cap' ";
    } else {
        $search = '';
    }

    $SQL = "SELECT rc.*
            FROM {$CFG->prefix}role_capabilities rc,
                 {$CFG->prefix}context c
            WHERE rc.contextid in $contexts
                 AND rc.roleid = $roleid
                 AND rc.contextid = c.id $search
            ORDER BY c.contextlevel DESC,
                     rc.capability DESC";

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

    static $pcontexts; // cache
    if (isset($pcontexts[$context->id])) {
        return ($pcontexts[$context->id]);  
    }

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM: // no parent
            return array();
        break;

        case CONTEXT_PERSONAL:
            if (!$parent = get_context_instance(CONTEXT_SYSTEM)) {
                return array();
            } else {
                $res = array($parent->id);
                $pcontexts[$context->id] = $res;  
                return $res;
            }
        break;

        case CONTEXT_USER:
            if (!$parent = get_context_instance(CONTEXT_SYSTEM)) {
                return array();
            } else {
                $res = array($parent->id);
                $pcontexts[$context->id] = $res;  
                return $res;
            }
        break;

        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
        case CONTEXT_COURSE: // 1 to 1 to course cat
            $parents = get_parent_cats($context);
            $parents = array_reverse($parents);
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            return $pcontexts[$context->id] = array_merge($parents, array($systemcontext->id));
        break;

        case CONTEXT_GROUP: // 1 to 1 to course
            if (! $group = groups_get_group($context->instanceid)) {
                return array();
            }
            if ($parent = get_context_instance(CONTEXT_COURSE, $group->courseid)) {
                $res = array_merge(array($parent->id), get_parent_contexts($parent));
                $pcontexts[$context->id] = $res;
                return $res;
            } else {
                return array();
            }
        break;

        case CONTEXT_MODULE: // 1 to 1 to course
            if (!$cm = get_record('course_modules','id',$context->instanceid)) {
                return array();
            }
            if ($parent = get_context_instance(CONTEXT_COURSE, $cm->course)) {
                $res = array_merge(array($parent->id), get_parent_contexts($parent));
                $pcontexts[$context->id] = $res;
                return $res;
            } else {
                return array();
            }
        break;

        case CONTEXT_BLOCK: // not necessarily 1 to 1 to course
            if (!$block = get_record('block_instance','id',$context->instanceid)) {
                return array();
            }
            // fix for MDL-9656, block parents are not necessarily courses
            if ($block->pagetype == 'course-view') {
                $parent = get_context_instance(CONTEXT_COURSE, $block->pageid);
            } else {
                $parent = get_context_instance(CONTEXT_SYSTEM); 
            }                       
            
            if ($parent) {
                $res = array_merge(array($parent->id), get_parent_contexts($parent));
                $pcontexts[$context->id] = $res;
                return $res;
            } else {
                return array();              
            }
        break;

        default:
            error('This is an unknown context (' . $context->contextlevel . ') in get_parent_contexts!');
        return false;
    }
}


/**
 * Recursive function which, given a context, find all its children context ids.
 * @param object $context.
 * @return array of children context ids.
 */
function get_child_contexts($context) {

    global $CFG;
    $children = array();

    switch ($context->contextlevel) {

        case CONTEXT_BLOCK:
            // No children.
            return array();
        break;

        case CONTEXT_MODULE:
            // No children.
            return array();
        break;

        case CONTEXT_GROUP:
            // No children.
            return array();
        break;

        case CONTEXT_COURSE:
            // Find all block instances for the course.
            $page = new page_course;
            $page->id = $context->instanceid;
            $page->type = 'course-view';
            if ($blocks = blocks_get_by_page_pinned($page)) {
                foreach ($blocks['l'] as $leftblock) {
                    if ($child = get_context_instance(CONTEXT_BLOCK, $leftblock->id)) {
                        array_push($children, $child->id);
                    }
                }
                foreach ($blocks['r'] as $rightblock) {
                    if ($child = get_context_instance(CONTEXT_BLOCK, $rightblock->id)) {
                        array_push($children, $child->id);
                    }
                }
            }
            // Find all module instances for the course.
            if ($modules = get_records('course_modules', 'course', $context->instanceid)) {
                foreach ($modules as $module) {
                    if ($child = get_context_instance(CONTEXT_MODULE, $module->id)) {
                        array_push($children, $child->id);
                    }
                }
            }
            // Find all group instances for the course.
            if ($groupids = groups_get_groups($context->instanceid)) {
                foreach ($groupids as $groupid) {
                    if ($child = get_context_instance(CONTEXT_GROUP, $groupid)) {
                        array_push($children, $child->id);
                    }
                }
            }
            return $children;
        break;

        case CONTEXT_COURSECAT:
            // We need to get the contexts for:
            //   1) The subcategories of the given category
            //   2) The courses in the given category and all its subcategories
            //   3) All the child contexts for these courses

            $categories = get_all_subcategories($context->instanceid);

            // Add the contexts for all the subcategories.
            foreach ($categories as $catid) {
                if ($catci = get_context_instance(CONTEXT_COURSECAT, $catid)) {
                    array_push($children, $catci->id);
                }
            }

            // Add the parent category as well so we can find the contexts
            // for its courses.
            array_unshift($categories, $context->instanceid);

            foreach ($categories as $catid) {
                // Find all courses for the category.
                if ($courses = get_records('course', 'category', $catid)) {
                    foreach ($courses as $course) {
                        if ($courseci = get_context_instance(CONTEXT_COURSE, $course->id)) {
                            array_push($children, $courseci->id);
                            $children = array_merge($children, get_child_contexts($courseci));
                        }
                    }
                }
            }
            return $children;
        break;

        case CONTEXT_USER:
            // No children.
            return array();
        break;

        case CONTEXT_PERSONAL:
            // No children.
            return array();
        break;

        case CONTEXT_SYSTEM:
            // Just get all the contexts except for CONTEXT_SYSTEM level.
            $sql = 'SELECT c.id '.
                     'FROM '.$CFG->prefix.'context AS c '.
                    'WHERE contextlevel != '.CONTEXT_SYSTEM;

            $contexts = get_records_sql($sql);
            foreach ($contexts as $cid) {
                array_push($children, $cid->id);
            }
            return $children;
        break;

        default:
            error('This is an unknown context (' . $context->contextlevel . ') in get_child_contexts!');
        return false;
    }
}


/**
 * Gets a string for sql calls, searching for stuff in this context or above
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
    if (isset($capabilities[$contextid][$capability])) {
        return $capabilities[$contextid][$capability];
    }
    else {
        return false;
    }
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
        
        case 'format':
            $string = get_string($stringname, 'format_'.$componentname);
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
            } else if (preg_match('|^block/|', $component)) {
                $langname = str_replace('/', '_', $component);
                $string = get_string('blockname', $langname);
            } else {
                $string = get_string('coresystem');
            }
        break;

        case CONTEXT_PERSONAL:
            $string = get_string('personal');
        break;

        case CONTEXT_USER:
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
            $string = get_string('blockname', 'block_'.basename($component));
        break;

        default:
            error ('This is an unknown context $contextlevel (' . $contextlevel . ') in get_component_string!');
        return false;

    }
    return $string;
}

/**
 * Gets the list of roles assigned to this context and up (parents)
 * @param object $context
 * @param view - set to true when roles are pulled for display only
 *               this is so that we can filter roles with no visible 
 *               assignment, for example, you might want to "hide" all
 *               course creators when browsing the course participants
 *               list.
 * @return array
 */
function get_roles_used_in_context($context, $view = false) {

    global $CFG;
    
    // filter for roles with all hidden assignments
    // no need to return when only pulling roles for reviewing
    // e.g. participants page.
    $hiddensql = ($view && !has_capability('moodle/role:viewhiddenassigns', $context))? ' AND ra.hidden = 0 ':'';   
    $contextlist = get_related_contexts_string($context);

    $sql = "SELECT DISTINCT r.id,
                   r.name,
                   r.shortname,
                   r.sortorder
              FROM {$CFG->prefix}role_assignments ra,
                   {$CFG->prefix}role r
             WHERE r.id = ra.roleid
               AND ra.contextid $contextlist
                   $hiddensql
          ORDER BY r.sortorder ASC";

    return get_records_sql($sql);
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

/** Returns all site roles in correct sort order.
 *
 */
function get_all_roles() {
    return get_records('role', '', '', 'sortorder ASC');
}

/**
 * gets all the user roles assigned in this context, or higher contexts
 * this is mainly used when checking if a user can assign a role, or overriding a role
 * i.e. we need to know what this user holds, in order to verify against allow_assign and
 * allow_override tables
 * @param object $context
 * @param int $userid
 * @param view - set to true when roles are pulled for display only
 *               this is so that we can filter roles with no visible 
 *               assignment, for example, you might want to "hide" all
 *               course creators when browsing the course participants
 *               list.
 * @return array
 */
function get_user_roles($context, $userid=0, $checkparentcontexts=true, $order='c.contextlevel DESC, r.sortorder ASC', $view=false) {

    global $USER, $CFG, $db;

    if (empty($userid)) {
        if (empty($USER->id)) {
            return array();
        }
        $userid = $USER->id;
    }
    // set up hidden sql
    $hiddensql = ($view && !has_capability('moodle/role:viewhiddenassigns', $context))? ' AND ra.hidden = 0 ':'';

    if ($checkparentcontexts && ($parents = get_parent_contexts($context))) {
        $contexts = ' ra.contextid IN ('.implode(',' , $parents).','.$context->id.')';
    } else {
        $contexts = ' ra.contextid = \''.$context->id.'\'';
    }

    return get_records_sql('SELECT ra.*, r.name, r.shortname
                             FROM '.$CFG->prefix.'role_assignments ra,
                                  '.$CFG->prefix.'role r,
                                  '.$CFG->prefix.'context c
                             WHERE ra.userid = '.$userid.
                           '   AND ra.roleid = r.id
                               AND ra.contextid = c.id
                               AND '.$contexts . $hiddensql .
                           ' ORDER BY '.$order);
}

/**
 * Creates a record in the allow_override table
 * @param int sroleid - source roleid
 * @param int troleid - target roleid
 * @return int - id or false
 */
function allow_override($sroleid, $troleid) {
    $record = new object();
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
    $record = new object;
    $record->roleid = $sroleid;
    $record->allowassign = $troleid;
    return insert_record('role_allow_assign', $record);
}

/**
 * Gets a list of roles that this user can assign in this context
 * @param object $context
 * @return array
 */
function get_assignable_roles ($context, $field="name") {

    $options = array();

    if ($roles = get_all_roles()) {
        foreach ($roles as $role) {
            if (user_can_assign($context, $role->id)) {
                $options[$role->id] = strip_tags(format_string($role->{$field}, true));
            }
        }
    }
    return $options;
}

/**
 * Gets a list of roles that this user can override in this context
 * @param object $context
 * @return array
 */
function get_overridable_roles($context) {

    $options = array();

    if ($roles = get_all_roles()) {
        foreach ($roles as $role) {
            if (user_can_override($context, $role->id)) {
                $options[$role->id] = strip_tags(format_string($role->name, true));
            }
        }
    }

    return $options;
}

/**
 *  Returns a role object that is the default role for new enrolments
 *  in a given course
 *
 *  @param object $course
 *  @return object $role
 */
function get_default_course_role($course) {
    global $CFG;

/// First let's take the default role the course may have
    if (!empty($course->defaultrole)) {
        if ($role = get_record('role', 'id', $course->defaultrole)) {
            return $role;
        }
    }

/// Otherwise the site setting should tell us
    if ($CFG->defaultcourseroleid) {
        if ($role = get_record('role', 'id', $CFG->defaultcourseroleid)) {
            return $role;
        }
    }

/// It's unlikely we'll get here, but just in case, try and find a student role
    if ($studentroles = get_roles_with_capability('moodle/legacy:student', CAP_ALLOW)) {
        return array_shift($studentroles);   /// Take the first one
    }

    return NULL;
}


/**
 * who has this capability in this context
 * does not handling user level resolving!!!
 * (!)pleaes note if $fields is empty this function attempts to get u.*
 * which can get rather large.
 * i.e 1 person has 2 roles 1 allow, 1 prevent, this will not work properly
 * @param $context - object
 * @param $capability - string capability
 * @param $fields - fields to be pulled
 * @param $sort - the sort order
 * @param $limitfrom - number of records to skip (offset)
 * @param $limitnum - number of records to fetch
 * @param $groups - single group or array of groups - group(s) user is in
 * @param $exceptions - list of users to exclude
 * @param view - set to true when roles are pulled for display only
 *               this is so that we can filter roles with no visible 
 *               assignment, for example, you might want to "hide" all
 *               course creators when browsing the course participants
 *               list.
 */
function get_users_by_capability($context, $capability, $fields='', $sort='',
                                 $limitfrom='', $limitnum='', $groups='', $exceptions='', $doanything=true, $view=false) {
    global $CFG;

/// Sorting out groups
    if ($groups) {
        $groupjoin = 'INNER JOIN '.$CFG->prefix.'groups_members gm ON gm.userid = ra.userid';

        if (is_array($groups)) {
            $groupsql = 'AND gm.groupid IN ('.implode(',', $groups).')';
        } else {
            $groupsql = 'AND gm.groupid = '.$groups;
        }
    } else {
        $groupjoin = '';
        $groupsql = '';
    }

/// Sorting out exceptions
    $exceptionsql = $exceptions ? "AND u.id NOT IN ($exceptions)" : '';

/// Set up default fields
    if (empty($fields)) {
        $fields = 'u.*, ul.timeaccess as lastaccess, ra.hidden';
    }

/// Set up default sort
    if (empty($sort)) {
        $sort = 'ul.timeaccess';
    }

    $sortby = $sort ? " ORDER BY $sort " : '';
/// Set up hidden sql
    $hiddensql = ($view && !has_capability('moodle/role:viewhiddenassigns', $context))? ' AND ra.hidden = 0 ':'';

/// If context is a course, then construct sql for ul
    if ($context->contextlevel == CONTEXT_COURSE) {
        $courseid = $context->instanceid;
        $coursesql1 = "AND ul.courseid = $courseid";
    } else {
        $coursesql1 = '';
    }

/// Sorting out roles with this capability set
    if ($possibleroles = get_roles_with_capability($capability, CAP_ALLOW, $context)) {
        if (!$doanything) {
            if (!$sitecontext = get_context_instance(CONTEXT_SYSTEM)) {
                return false;    // Something is seriously wrong
            }
            $doanythingroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $sitecontext);
        }

        $validroleids = array();
        foreach ($possibleroles as $possiblerole) {
            if (!$doanything) {
                if (isset($doanythingroles[$possiblerole->id])) {  // We don't want these included
                    continue;
                }
            }
            if ($caps = role_context_capabilities($possiblerole->id, $context, $capability)) { // resolved list
                if (isset($caps[$capability]) && $caps[$capability] > 0) { // resolved capability > 0
                    $validroleids[] = $possiblerole->id;
                }
            }
        }
        if (empty($validroleids)) {
            return false;
        }
        $roleids =  '('.implode(',', $validroleids).')';
    } else {
        return false;  // No need to continue, since no roles have this capability set
    }

/// Construct the main SQL
    $select = " SELECT $fields";
    $from   = " FROM {$CFG->prefix}user u
                INNER JOIN {$CFG->prefix}role_assignments ra ON ra.userid = u.id
                INNER JOIN {$CFG->prefix}role r ON r.id = ra.roleid
                LEFT OUTER JOIN {$CFG->prefix}user_lastaccess ul ON (ul.userid = u.id $coursesql1)
                $groupjoin";
    $where  = " WHERE ra.contextid ".get_related_contexts_string($context)."
                  AND u.deleted = 0
                  AND ra.roleid in $roleids
                      $exceptionsql
                      $groupsql
                      $hiddensql";
        
    return get_records_sql($select.$from.$where.$sortby, $limitfrom, $limitnum);
}

/**
 * gets all the users assigned this role in this context or higher
 * @param int roleid
 * @param int contextid
 * @param bool parent if true, get list of users assigned in higher context too
 * @return array()
 */
function get_role_users($roleid, $context, $parent=false, $fields='', $sort='u.lastname ASC', $view=false) {
    global $CFG;

    if (empty($fields)) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.emailstop, u.lang, u.timezone';
    }

    // whether this assignment is hidden
    $hiddensql = ($view && !has_capability('moodle/role:viewhiddenassigns', $context))? ' AND r.hidden = 0 ':'';
    if ($parent) {
        if ($contexts = get_parent_contexts($context)) {
            $parentcontexts = ' OR r.contextid IN ('.implode(',', $contexts).')';
        } else {
            $parentcontexts = '';
        }
    } else {
        $parentcontexts = '';
    }

    if ($roleid) {
        $roleselect = "AND r.roleid = $roleid";
    } else {
        $roleselect = '';
    }

    $SQL = "SELECT $fields
            FROM {$CFG->prefix}role_assignments r,
                 {$CFG->prefix}user u
            WHERE (r.contextid = $context->id $parentcontexts)
            AND u.id = r.userid $roleselect
            $hiddensql
            ORDER BY $sort
            ";                  // join now so that we can just use fullname() later

    return get_records_sql($SQL);
}

/**
 * Counts all the users assigned this role in this context or higher
 * @param int roleid
 * @param int contextid
 * @param bool parent if true, get list of users assigned in higher context too
 * @return array()
 */
function count_role_users($roleid, $context, $parent=false) {
    global $CFG;

    if ($parent) {
        if ($contexts = get_parent_contexts($context)) {
            $parentcontexts = ' OR r.contextid IN ('.implode(',', $contexts).')';
        } else {
            $parentcontexts = '';
        }
    } else {
        $parentcontexts = '';
    }

    $SQL = "SELECT count(*)
            FROM {$CFG->prefix}role_assignments r
            WHERE (r.contextid = $context->id $parentcontexts)
            AND r.roleid = $roleid";

    return count_records_sql($SQL);
}

/**
 * This function gets the list of courses that this user has a particular capability in
 * This is not the most efficient way of doing this
 * @param string capability
 * @param int $userid
 * @return array
 */
function get_user_capability_course($capability, $userid=NULL) {

    $usercourses = array();
    $courses = get_records_select('course', '', '', 'id, id');

    foreach ($courses as $course) {
        if (has_capability($capability, get_context_instance(CONTEXT_COURSE, $course->id), $userid)) {
            $usercourses[] = $course;
        }
    }
    return $usercourses;
}


/** This function finds the roles assigned directly to this context only
 * i.e. no parents role
 * @param object $context
 * @return array
 */
function get_roles_on_exact_context($context) {

    global $CFG;

    return get_records_sql("SELECT r.*
                            FROM {$CFG->prefix}role_assignments ra,
                                 {$CFG->prefix}role r
                            WHERE ra.roleid = r.id
                                  AND ra.contextid = $context->id");

}

/**
 * Switches the current user to another role for the current session and only
 * in the given context.  If roleid is not valid (eg 0) or the current user
 * doesn't have permissions to be switching roles then the user's session
 * is compltely reset to have their normal roles.
 * @param integer $roleid
 * @param object $context
 * @return bool
 */
function role_switch($roleid, $context) {
    global $USER, $CFG;

/// If we can't use this or are already using it or no role was specified then bail completely and reset
    if (empty($roleid) || !has_capability('moodle/role:switchroles', $context)
        || !empty($USER->switchrole[$context->id])  || !confirm_sesskey()) {

        unset($USER->switchrole[$context->id]);  // Delete old capabilities
        unset($USER->courseeditallowed);               // drop cache for course edit button
        load_all_capabilities();   //reload user caps
        return true;
    }

/// We're allowed to switch but can we switch to the specified role?  Use assignable roles to check.
    if (!$roles = get_assignable_roles($context)) {
        return false;
    }

/// unset default user role - it would not work anyway
    unset($roles[$CFG->defaultuserroleid]);

    if (empty($roles[$roleid])) {   /// We can't switch to this particular role
        return false;
    }

/// We have a valid roleid that this user can switch to, so let's set up the session

    $USER->switchrole[$context->id] = $roleid;     // So we know later what state we are in
    unset($USER->courseeditallowed);                     // drop cache for course edit button

    load_all_capabilities();   //reload switched role caps

/// Add some permissions we are really going to always need, even if the role doesn't have them!

    $USER->capabilities[$context->id]['moodle/course:view'] = CAP_ALLOW;

    return true;

}


// get any role that has an override on exact context
function get_roles_with_override_on_context($context) {

    global $CFG;

    return get_records_sql("SELECT r.*
                            FROM {$CFG->prefix}role_capabilities rc,
                                 {$CFG->prefix}role r
                            WHERE rc.roleid = r.id
                            AND rc.contextid = $context->id");
}

// get all capabilities for this role on this context (overrids)
function get_capabilities_from_role_on_context($role, $context) {

    global $CFG;

    return get_records_sql("SELECT *
                            FROM {$CFG->prefix}role_capabilities
                            WHERE contextid = $context->id
                                  AND roleid = $role->id");
}

// find out which roles has assignment on this context
function get_roles_with_assignment_on_context($context) {

    global $CFG;

    return get_records_sql("SELECT r.*
                            FROM {$CFG->prefix}role_assignments ra,
                                 {$CFG->prefix}role r
                            WHERE ra.roleid = r.id
                            AND ra.contextid = $context->id");
}



/**
 * Find all user assignemnt of users for this role, on this context
 */
function get_users_from_role_on_context($role, $context) {

    global $CFG;

    return get_records_sql("SELECT *
                            FROM {$CFG->prefix}role_assignments
                            WHERE contextid = $context->id
                                  AND roleid = $role->id");
}

/**
 * Simple function returning a boolean true if roles exist, otherwise false
 */
function user_has_role_assignment($userid, $roleid, $contextid=0) {

    if ($contextid) {
        return record_exists('role_assignments', 'userid', $userid, 'roleid', $roleid, 'contextid', $contextid);
    } else {
        return record_exists('role_assignments', 'userid', $userid, 'roleid', $roleid);
    }
}

/** 
 * Inserts all parental context and self into context_rel table
 *
 * @param object $context-context to be deleted
 * @param bool deletechild - deltes child contexts dependencies
 */
function insert_context_rel($context, $deletechild=true, $deleteparent=true) {
    
    // first check validity
    // MDL-9057
    if (!validate_context($context->contextlevel, $context->instanceid)) {
        debugging('Error: Invalid context creation request for level "' .
                s($context->contextlevel) . '", instance "' . s($context->instanceid) . '".');
        return NULL;  
    }
    
    // removes all parents 
    if ($deletechild) {
        delete_records('context_rel', 'c2', $context->id);
    }
    
    if ($deleteparent) {
        delete_records('context_rel', 'c1', $context->id);
    }
    // insert all parents
    if ($parents = get_parent_contexts($context)) {
        $parents[] = $context->id;
        foreach ($parents as $parent) {
            $rec = new object;
            $rec ->c1 = $context->id;
            $rec ->c2 = $parent;
            insert_record('context_rel', $rec);
        }
    }  
}

/**
 * rebuild context_rel table without deleting
 */
function build_context_rel() {
  
    global $db;
    $savedb = $db->debug;
  
    // total number of records
    $total = count_records('context');
    // processed records
    $done = 0;
    print_progress($done, $total, 10, 0, 'Processing context relations');
    $db->debug = false;
    if ($contexts = get_records('context')) {
        foreach ($contexts as $context) {
            // no need to delete because it's all empty
            insert_context_rel($context, false, false);
            $db->debug = true;
            print_progress(++$done, $total, 10, 0, 'Processing context relations');
            $db->debug = false;
        }
    }
    
    $db->debug = $savedb;
}
?>

<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains functions for managing user access
 *
 * <b>Public API vs internals</b>
 *
 * General users probably only care about
 *
 * Context handling
 * - get_context_instance()
 * - get_context_instance_by_id()
 * - get_parent_contexts()
 * - get_child_contexts()
 *
 * Whether the user can do something...
 * - has_capability()
 * - has_any_capability()
 * - has_all_capabilities()
 * - require_capability()
 * - require_login() (from moodlelib)
 *
 * What courses has this user access to?
 * - get_user_courses_bycap()
 *
 * What users can do X in this context?
 * - get_users_by_capability()
 *
 * Enrol/unenrol
 * - enrol_into_course()
 * - role_assign()/role_unassign()
 *
 *
 * Advanced use
 * - load_all_capabilities()
 * - reload_all_capabilities()
 * - has_capability_in_accessdata()
 * - is_siteadmin()
 * - get_user_access_sitewide()
 * - load_subcontext()
 * - get_role_access_bycontext()
 *
 * <b>Name conventions</b>
 *
 * "ctx" means context
 *
 * <b>accessdata</b>
 *
 * Access control data is held in the "accessdata" array
 * which - for the logged-in user, will be in $USER->access
 *
 * For other users can be generated and passed around (but may also be cached
 * against userid in $ACCESSLIB_PRIVATE->accessdatabyuser.
 *
 * $accessdata is a multidimensional array, holding
 * role assignments (RAs), role-capabilities-perm sets
 * (role defs) and a list of courses we have loaded
 * data for.
 *
 * Things are keyed on "contextpaths" (the path field of
 * the context table) for fast walking up/down the tree.
 * <code>
 * $accessdata[ra][$contextpath]= array($roleid)
 *                [$contextpath]= array($roleid)
 *                [$contextpath]= array($roleid)
 * </code>
 *
 * Role definitions are stored like this
 * (no cap merge is done - so it's compact)
 *
 * <code>
 * $accessdata[rdef][$contextpath:$roleid][mod/forum:viewpost] = 1
 *                                        [mod/forum:editallpost] = -1
 *                                        [mod/forum:startdiscussion] = -1000
 * </code>
 *
 * See how has_capability_in_accessdata() walks up/down the tree.
 *
 * Normally - specially for the logged-in user, we only load
 * rdef and ra down to the course level, but not below. This
 * keeps accessdata small and compact. Below-the-course ra/rdef
 * are loaded as needed. We keep track of which courses we
 * have loaded ra/rdef in
 * <code>
 * $accessdata[loaded] = array($contextpath, $contextpath)
 * </code>
 *
 * <b>Stale accessdata</b>
 *
 * For the logged-in user, accessdata is long-lived.
 *
 * On each pageload we load $ACCESSLIB_PRIVATE->dirtycontexts which lists
 * context paths affected by changes. Any check at-or-below
 * a dirty context will trigger a transparent reload of accessdata.
 *
 * Changes at the sytem level will force the reload for everyone.
 *
 * <b>Default role caps</b>
 * The default role assignment is not in the DB, so we
 * add it manually to accessdata.
 *
 * This means that functions that work directly off the
 * DB need to ensure that the default role caps
 * are dealt with appropriately.
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** permission definitions */
define('CAP_INHERIT', 0);
/** permission definitions */
define('CAP_ALLOW', 1);
/** permission definitions */
define('CAP_PREVENT', -1);
/** permission definitions */
define('CAP_PROHIBIT', -1000);

/** context definitions */
define('CONTEXT_SYSTEM', 10);
/** context definitions */
define('CONTEXT_USER', 30);
/** context definitions */
define('CONTEXT_COURSECAT', 40);
/** context definitions */
define('CONTEXT_COURSE', 50);
/** context definitions */
define('CONTEXT_MODULE', 70);
/** context definitions */
define('CONTEXT_BLOCK', 80);

/** capability risks - see {@link http://docs.moodle.org/en/Development:Hardening_new_Roles_system} */
define('RISK_MANAGETRUST', 0x0001);
/** capability risks - see {@link http://docs.moodle.org/en/Development:Hardening_new_Roles_system} */
define('RISK_CONFIG',      0x0002);
/** capability risks - see {@link http://docs.moodle.org/en/Development:Hardening_new_Roles_system} */
define('RISK_XSS',         0x0004);
/** capability risks - see {@link http://docs.moodle.org/en/Development:Hardening_new_Roles_system} */
define('RISK_PERSONAL',    0x0008);
/** capability risks - see {@link http://docs.moodle.org/en/Development:Hardening_new_Roles_system} */
define('RISK_SPAM',        0x0010);
/** capability risks - see {@link http://docs.moodle.org/en/Development:Hardening_new_Roles_system} */
define('RISK_DATALOSS',    0x0020);

/** rolename displays - the name as defined in the role definition */
define('ROLENAME_ORIGINAL', 0);
/** rolename displays - the name as defined by a role alias */
define('ROLENAME_ALIAS', 1);
/** rolename displays - Both, like this:  Role alias (Original)*/
define('ROLENAME_BOTH', 2);
/** rolename displays - the name as defined in the role definition and the shortname in brackets*/
define('ROLENAME_ORIGINALANDSHORT', 3);
/** rolename displays - the name as defined by a role alias, in raw form suitable for editing*/
define('ROLENAME_ALIAS_RAW', 4);

/** size limit for context cache */
if (!defined('MAX_CONTEXT_CACHE_SIZE')) {
    define('MAX_CONTEXT_CACHE_SIZE', 5000);
}

/**
 * Although this looks like a global variable, it isn't really.
 *
 * It is just a private implementation detail to accesslib that MUST NOT be used elsewhere.
 * It is used to cache various bits of data between function calls for performance reasons.
 * Sadly, a PHP global variale is the only way to impleemnt this, withough rewriting everything
 * as methods of a class, instead of functions.
 *
 * @global stdClass $ACCESSLIB_PRIVATE
 * @name $ACCESSLIB_PRIVATE
 */
$ACCESSLIB_PRIVATE = new stdClass;
$ACCESSLIB_PRIVATE->contexts = array(); // Cache of context objects by level and instance
$ACCESSLIB_PRIVATE->contextsbyid = array(); // Cache of context objects by id
$ACCESSLIB_PRIVATE->systemcontext = null; // Used in get_system_context
$ACCESSLIB_PRIVATE->dirtycontexts = null; // Dirty contexts cache
$ACCESSLIB_PRIVATE->accessdatabyuser = array(); // Holds the $accessdata structure for users other than $USER
$ACCESSLIB_PRIVATE->roledefinitions = array(); // role definitions cache - helps a lot with mem usage in cron
$ACCESSLIB_PRIVATE->croncache = array(); // Used in get_role_access
$ACCESSLIB_PRIVATE->preloadedcourses = array(); // Used in preload_course_contexts.
$ACCESSLIB_PRIVATE->capabilitynames = null; // Used in is_valid_capability (only in developer debug mode)

/**
 * Clears accesslib's private caches. ONLY BE USED BY UNIT TESTS
 *
 * This method should ONLY BE USED BY UNIT TESTS. It clears all of
 * accesslib's private caches. You need to do this before setting up test data,
 * and also at the end fo the tests.
 * @global object
 * @global object
 * @global object
 */
function accesslib_clear_all_caches_for_unit_testing() {
    global $UNITTEST, $USER, $ACCESSLIB_PRIVATE;
    if (empty($UNITTEST->running)) {
        throw new coding_exception('You must not call clear_all_caches outside of unit tests.');
    }
    $ACCESSLIB_PRIVATE->contexts = array();
    $ACCESSLIB_PRIVATE->contextsbyid = array();
    $ACCESSLIB_PRIVATE->systemcontext = null;
    $ACCESSLIB_PRIVATE->dirtycontexts = null;
    $ACCESSLIB_PRIVATE->accessdatabyuser = array();
    $ACCESSLIB_PRIVATE->roledefinitions = array();
    $ACCESSLIB_PRIVATE->croncache = array();
    $ACCESSLIB_PRIVATE->preloadedcourses = array();
    $ACCESSLIB_PRIVATE->capabilitynames = null;

    unset($USER->access);
}

/**
 * Private function. Add a context object to accesslib's caches.
 * @global object
 * @param object $context
 */
function cache_context($context) {
    global $ACCESSLIB_PRIVATE;

    // If there are too many items in the cache already, remove items until
    // there is space
    while (count($ACCESSLIB_PRIVATE->contextsbyid) >= MAX_CONTEXT_CACHE_SIZE) {
        $first = reset($ACCESSLIB_PRIVATE->contextsbyid);
        unset($ACCESSLIB_PRIVATE->contextsbyid[$first->id]);
        unset($ACCESSLIB_PRIVATE->contexts[$first->contextlevel][$first->instanceid]);
    }

    $ACCESSLIB_PRIVATE->contexts[$context->contextlevel][$context->instanceid] = $context;
    $ACCESSLIB_PRIVATE->contextsbyid[$context->id] = $context;
}

/**
 * This is really slow!!! do not use above course context level
 *
 * @global object
 * @param int $roleid
 * @param object $context
 * @return array
 */
function get_role_context_caps($roleid, $context) {
    global $DB;

    //this is really slow!!!! - do not use above course context level!
    $result = array();
    $result[$context->id] = array();

    // first emulate the parent context capabilities merging into context
    $searchcontexts = array_reverse(get_parent_contexts($context));
    array_push($searchcontexts, $context->id);
    foreach ($searchcontexts as $cid) {
        if ($capabilities = $DB->get_records('role_capabilities', array('roleid'=>$roleid, 'contextid'=>$cid))) {
            foreach ($capabilities as $cap) {
                if (!array_key_exists($cap->capability, $result[$context->id])) {
                    $result[$context->id][$cap->capability] = 0;
                }
                $result[$context->id][$cap->capability] += $cap->permission;
            }
        }
    }

    // now go through the contexts bellow given context
    $searchcontexts = array_keys(get_child_contexts($context));
    foreach ($searchcontexts as $cid) {
        if ($capabilities = $DB->get_records('role_capabilities', array('roleid'=>$roleid, 'contextid'=>$cid))) {
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

/**
 * Gets the accessdata for role "sitewide" (system down to course)
 *
 * @global object
 * @global object
 * @param int $roleid
 * @param array $accessdata defaults to null
 * @return array
 */
function get_role_access($roleid, $accessdata=NULL) {

    global $CFG, $DB;

    /* Get it in 1 cheap DB query...
     * - relevant role caps at the root and down
     *   to the course level - but not below
     */
    if (is_null($accessdata)) {
        $accessdata           = array(); // named list
        $accessdata['ra']     = array();
        $accessdata['rdef']   = array();
        $accessdata['loaded'] = array();
    }

    //
    // Overrides for the role IN ANY CONTEXTS
    // down to COURSE - not below -
    //
    $sql = "SELECT ctx.path,
                   rc.capability, rc.permission
              FROM {context} ctx
              JOIN {role_capabilities} rc
                   ON rc.contextid=ctx.id
             WHERE rc.roleid = ?
                   AND ctx.contextlevel <= ".CONTEXT_COURSE."
          ORDER BY ctx.depth, ctx.path";
    $params = array($roleid);

    // we need extra caching in CLI scripts and cron
    if (CLI_SCRIPT) {
        global $ACCESSLIB_PRIVATE;

        if (!isset($ACCESSLIB_PRIVATE->croncache[$roleid])) {
            $ACCESSLIB_PRIVATE->croncache[$roleid] = array();
            if ($rs = $DB->get_recordset_sql($sql, $params)) {
                foreach ($rs as $rd) {
                    $ACCESSLIB_PRIVATE->croncache[$roleid][] = $rd;
                }
                $rs->close();
            }
        }

        foreach ($ACCESSLIB_PRIVATE->croncache[$roleid] as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }

    } else {
        if ($rs = $DB->get_recordset_sql($sql, $params)) {
            foreach ($rs as $rd) {
                $k = "{$rd->path}:{$roleid}";
                $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
            }
            unset($rd);
            $rs->close();
        }
    }

    return $accessdata;
}

/**
 * Gets the accessdata for role "sitewide" (system down to course)
 *
 * @global object
 * @global object
 * @param int $roleid
 * @param array $accessdata defaults to null
 * @return array
 */
function get_default_frontpage_role_access($roleid, $accessdata=NULL) {

    global $CFG, $DB;

    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    $base = '/'. SYSCONTEXTID .'/'. $frontpagecontext->id;

    //
    // Overrides for the role in any contexts related to the course
    //
    $sql = "SELECT ctx.path,
                   rc.capability, rc.permission
              FROM {context} ctx
              JOIN {role_capabilities} rc
                   ON rc.contextid=ctx.id
             WHERE rc.roleid = ?
                   AND (ctx.id = ".SYSCONTEXTID." OR ctx.path LIKE ?)
                   AND ctx.contextlevel <= ".CONTEXT_COURSE."
          ORDER BY ctx.depth, ctx.path";
    $params = array($roleid, "$base/%");

    if ($rs = $DB->get_recordset_sql($sql, $params)) {
        foreach ($rs as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
        $rs->close();
    }

    return $accessdata;
}


/**
 * Get the default guest role
 *
 * @global object
 * @global object
 * @return object role
 */
function get_guest_role() {
    global $CFG, $DB;

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
        if ($guestrole = $DB->get_record('role', array('id'=>$CFG->guestroleid))) {
            return $guestrole;
        } else {
            //somebody is messing with guest roles, remove incorrect setting and try to find a new one
            set_config('guestroleid', '');
            return get_guest_role();
        }
    }
}

/**
 * Check whether a user has a paritcular capability in a given context.
 *
 * For example::
 *      $context = get_context_instance(CONTEXT_MODULE, $cm->id);
 *      has_capability('mod/forum:replypost',$context)
 *
 * By default checks the capabilties of the current user, but you can pass a
 * different userid. By default will return true for admin-like users who have the
 * moodle/site:doanything capability, but you can override that with the fourth argument.
 *
 * @param string $capability the name of the capability to check. For example mod/forum:view
 * @param object $context the context to check the capability in. You normally get this with {@link get_context_instance}.
 * @param integer $userid A user id. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignore the special moodle/site:doanything capability that admin-like roles have.
 * @return boolean true if the user has this capability. Otherwise false.
 */
function has_capability($capability, $context, $userid=NULL, $doanything=true) {
    global $USER, $CFG, $DB, $SCRIPT, $ACCESSLIB_PRIVATE;

    if (during_initial_install()) {
        if ($SCRIPT === "/$CFG->admin/index.php" or $SCRIPT === "/$CFG->admin/cliupgrade.php") {
            // we are in an installer - roles can not work yet
            return true;
        } else {
            return false;
        }
    }

    // the original $CONTEXT here was hiding serious errors
    // for security reasons do not reuse previous context
    if (empty($context)) {
        debugging('Incorrect context specified');
        return false;
    }

/// Some sanity checks
    if (debugging('',DEBUG_DEVELOPER)) {
        if (!is_valid_capability($capability)) {
            debugging('Capability "'.$capability.'" was not found! This should be fixed in code.');
        }
        if (!is_bool($doanything)) {
            debugging('Capability parameter "doanything" is wierd ("'.$doanything.'"). This should be fixed in code.');
        }
    }

    if (empty($userid)) { // we must accept null, 0, '0', '' etc. in $userid
        if (empty($USER->id)) {
            // Session not set up yet.
            $userid = 0;
        } else {
            $userid = $USER->id;
        }
    }

    if (is_null($context->path) or $context->depth == 0) {
        //this should not happen
        $contexts = array(SYSCONTEXTID, $context->id);
        $context->path = '/'.SYSCONTEXTID.'/'.$context->id;
        debugging('Context id '.$context->id.' does not have valid path, please use build_context_path()', DEBUG_DEVELOPER);

    } else {
        $contexts = explode('/', $context->path);
        array_shift($contexts);
    }

    if (CLI_SCRIPT && !isset($USER->access)) {
        // In cron, some modules setup a 'fake' $USER,
        // ensure we load the appropriate accessdata.
        if (isset($ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
            $ACCESSLIB_PRIVATE->dirtycontexts = NULL; //load fresh dirty contexts
        } else {
            load_user_accessdata($userid);
            $ACCESSLIB_PRIVATE->dirtycontexts = array();
        }
        $USER->access = $ACCESSLIB_PRIVATE->accessdatabyuser[$userid];

    } else if (isset($USER->id) && ($USER->id == $userid) && !isset($USER->access)) {
        // caps not loaded yet - better to load them to keep BC with 1.8
        // not-logged-in user or $USER object set up manually first time here
        load_all_capabilities();
        $ACCESSLIB_PRIVATE->accessdatabyuser = array(); // reset the cache for other users too, the dirty contexts are empty now
        $ACCESSLIB_PRIVATE->roledefinitions = array();
    }

    // Load dirty contexts list if needed
    if (!isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
        if (isset($USER->access['time'])) {
            $ACCESSLIB_PRIVATE->dirtycontexts = get_dirty_contexts($USER->access['time']);
        }
        else {
            $ACCESSLIB_PRIVATE->dirtycontexts = array();
        }
    }

    // Careful check for staleness...
    if (count($ACCESSLIB_PRIVATE->dirtycontexts) !== 0 and is_contextpath_dirty($contexts, $ACCESSLIB_PRIVATE->dirtycontexts)) {
        // reload all capabilities - preserving loginas, roleswitches, etc
        // and then cleanup any marks of dirtyness... at least from our short
        // term memory! :-)
        $ACCESSLIB_PRIVATE->accessdatabyuser = array();
        $ACCESSLIB_PRIVATE->roledefinitions = array();

        if (CLI_SCRIPT) {
            load_user_accessdata($userid);
            $USER->access = $ACCESSLIB_PRIVATE->accessdatabyuser[$userid];
            $ACCESSLIB_PRIVATE->dirtycontexts = array();

        } else {
            reload_all_capabilities();
        }
    }

    // divulge how many times we are called
    //// error_log("has_capability: id:{$context->id} path:{$context->path} userid:$userid cap:$capability");

    if (isset($USER->id) && ($USER->id == $userid)) { // we must accept strings and integers in $userid
        //
        // For the logged in user, we have $USER->access
        // which will have all RAs and caps preloaded for
        // course and above contexts.
        //
        // Contexts below courses && contexts that do not
        // hang from courses are loaded into $USER->access
        // on demand, and listed in $USER->access[loaded]
        //
        if ($context->contextlevel <= CONTEXT_COURSE) {
            // Course and above are always preloaded
            return has_capability_in_accessdata($capability, $context, $USER->access, $doanything);
        }
        // Load accessdata for below-the-course contexts
        if (!path_inaccessdata($context->path,$USER->access)) {
            // error_log("loading access for context {$context->path} for $capability at {$context->contextlevel} {$context->id}");
            // $bt = debug_backtrace();
            // error_log("bt {$bt[0]['file']} {$bt[0]['line']}");
            load_subcontext($USER->id, $context, $USER->access);
        }
        return has_capability_in_accessdata($capability, $context, $USER->access, $doanything);
    }

    if (!isset($ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
        load_user_accessdata($userid);
    }
    if ($context->contextlevel <= CONTEXT_COURSE) {
        // Course and above are always preloaded
        return has_capability_in_accessdata($capability, $context, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid], $doanything);
    }
    // Load accessdata for below-the-course contexts as needed
    if (!path_inaccessdata($context->path, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
        // error_log("loading access for context {$context->path} for $capability at {$context->contextlevel} {$context->id}");
        // $bt = debug_backtrace();
        // error_log("bt {$bt[0]['file']} {$bt[0]['line']}");
        load_subcontext($userid, $context, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid]);
    }
    return has_capability_in_accessdata($capability, $context, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid], $doanything);
}

/**
 * Check if the user has any one of several capabilities from a list.
 *
 * This is just a utility method that calls has_capability in a loop. Try to put
 * the capabilities that most users are likely to have first in the list for best
 * performance.
 *
 * There are probably tricks that could be done to improve the performance here, for example,
 * check the capabilities that are already cached first.
 *
 * @see has_capability()
 * @param array $capabilities an array of capability names.
 * @param object $context the context to check the capability in. You normally get this with {@link get_context_instance}.
 * @param integer $userid A user id. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignore the special moodle/site:doanything capability that admin-like roles have.
 * @return boolean true if the user has any of these capabilities. Otherwise false.
 */
function has_any_capability($capabilities, $context, $userid=NULL, $doanything=true) {
    if (!is_array($capabilities)) {
        debugging('Incorrect $capabilities parameter in has_any_capabilities() call - must be an array');
        return false;
    }
    foreach ($capabilities as $capability) {
        if (has_capability($capability, $context, $userid, $doanything)) {
            return true;
        }
    }
    return false;
}

/**
 * Check if the user has all the capabilities in a list.
 *
 * This is just a utility method that calls has_capability in a loop. Try to put
 * the capabilities that fewest users are likely to have first in the list for best
 * performance.
 *
 * There are probably tricks that could be done to improve the performance here, for example,
 * check the capabilities that are already cached first.
 *
 * @see has_capability()
 * @param array $capabilities an array of capability names.
 * @param object $context the context to check the capability in. You normally get this with {@link get_context_instance}.
 * @param integer $userid A user id. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignore the special moodle/site:doanything capability that admin-like roles have.
 * @return boolean true if the user has all of these capabilities. Otherwise false.
 */
function has_all_capabilities($capabilities, $context, $userid=NULL, $doanything=true) {
    if (!is_array($capabilities)) {
        debugging('Incorrect $capabilities parameter in has_all_capabilities() call - must be an array');
        return false;
    }
    foreach ($capabilities as $capability) {
        if (!has_capability($capability, $context, $userid, $doanything)) {
            return false;
        }
    }
    return true;
}

/**
 * Check if the user is an admin at the site level
 *
 * Uses 1 DB query to answer whether a user is an admin at the sitelevel.
 * It depends on DB schema >=1.7 but does not depend on the new datastructures
 * in v1.9 (context.path, or $USER->access)
 *
 * Will return true if the userid has any of
 *  - moodle/site:config
 *  - moodle/legacy:admin
 *  - moodle/site:doanything
 *
 * @global object
 * @global object
 * @param   int  $userid
 * @returns bool true is user can administer server settings
 */
function is_siteadmin($userid) {
    global $CFG, $DB;

    $sql = "SELECT SUM(rc.permission)
              FROM {role_capabilities} rc
              JOIN {context} ctx
                   ON ctx.id=rc.contextid
              JOIN {role_assignments} ra
                   ON ra.roleid=rc.roleid AND ra.contextid=ctx.id
             WHERE ctx.contextlevel=10
                   AND ra.userid=?
                   AND rc.capability IN (?, ?, ?)
          GROUP BY rc.capability
            HAVING SUM(rc.permission) > 0";
    $params = array($userid, 'moodle/site:config', 'moodle/legacy:admin', 'moodle/site:doanything');

    return $DB->record_exists_sql($sql, $params);
}

/**
 * Check whether a role is an admin at the site level
 *
 * Will return true if the userid has any of
 *  - moodle/site:config
 *  - moodle/legacy:admin
 *  - moodle/site:doanything
 *
 * @global object
 * @param integer $roleid a role id.
 * @return boolean, whether this role is an admin role.
 */
function is_admin_role($roleid) {
    global $DB;

    $sql = "SELECT 1
              FROM {role_capabilities} rc
              JOIN {context} ctx ON ctx.id = rc.contextid
             WHERE ctx.contextlevel = 10
                   AND rc.roleid = ?
                   AND rc.capability IN (?, ?, ?)
          GROUP BY rc.capability
            HAVING SUM(rc.permission) > 0";
    $params = array($roleid, 'moodle/site:config', 'moodle/legacy:admin', 'moodle/site:doanything');

    return $DB->record_exists_sql($sql, $params);
}

/**
 * Returns all the roles for which is_admin_role($role->id) is true.
 *
 * @global object
 * @return array
 */
function get_admin_roles() {
    global $DB;

    $sql = "SELECT *
              FROM {role} r
             WHERE EXISTS (
                    SELECT 1
                      FROM {role_capabilities} rc
                      JOIN {context} ctx ON ctx.id = rc.contextid
                     WHERE ctx.contextlevel = 10
                           AND rc.roleid = r.id
                           AND rc.capability IN (?, ?, ?)
                  GROUP BY rc.capability
                    HAVING SUM(rc.permission) > 0
             )
          ORDER BY r.sortorder";
    $params = array('moodle/site:config', 'moodle/legacy:admin', 'moodle/site:doanything');

    return $DB->get_records_sql($sql, $params);
}

/**
 * @param string $path
 * @return string
 */
function get_course_from_path ($path) {
    // assume that nothing is more than 1 course deep
    if (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        return $matches[1];
    }
    return false;
}

/**
 * @param string $path
 * @param array $accessdata
 * @return bool
 */
function path_inaccessdata($path, $accessdata) {

    // assume that contexts hang from sys or from a course
    // this will only work well with stuff that hangs from a course
    if (in_array($path, $accessdata['loaded'], true)) {
            // error_log("found it!");
        return true;
    }
    $base = '/' . SYSCONTEXTID;
    while (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        $path = $matches[1];
        if ($path === $base) {
            return false;
        }
        if (in_array($path, $accessdata['loaded'], true)) {
            return true;
        }
    }
    return false;
}

/**
 *
 * Walk the accessdata array and return true/false
 *
 * Walk the accessdata array and return true/false.
 * Deals with prohibits, roleswitching, aggregating
 * capabilities, etc.
 *
 * The main feature of here is being FAST and with no
 * side effects.
 *
 * Notes:
 *
 * Switch Roles exits early
 * -----------------------
 * cap checks within a switchrole need to exit early
 * in our bottom up processing so they don't "see" that
 * there are real RAs that can do all sorts of things.
 *
 * Switch Role merges with default role
 * ------------------------------------
 * If you are a teacher in course X, you have at least
 * teacher-in-X + defaultloggedinuser-sitewide. So in the
 * course you'll have techer+defaultloggedinuser.
 * We try to mimic that in switchrole.
 *
 * Local-most role definition and role-assignment wins
 * ---------------------------------------------------
 * So if the local context has said 'allow', it wins
 * over a high-level context that says 'deny'.
 * This is applied when walking rdefs, and RAs.
 * Only at the same context the values are SUM()med.
 *
 * The exception is CAP_PROHIBIT.
 *
 * "Guest default role" exception
 * ------------------------------
 *
 * See MDL-7513 and $ignoreguest below for details.
 *
 * The rule is that
 *
 *    IF we are being asked about moodle/legacy:guest
 *                             OR moodle/course:view
 *    FOR a real, logged-in user
 *    AND we reached the top of the path in ra and rdef
 *    AND that role has moodle/legacy:guest === 1...
 *    THEN we act as if we hadn't seen it.
 *
 * Note that this function must be kept in synch with has_capability_in_accessdata.
 *
 * To Do:
 * @todo Document how it works
 * @todo Rewrite in ASM
 *
 * @global object
 * @param string $capability
 * @param object $context
 * @param array $accessdata
 * @param bool $doanything
 * @return bool
 */
function has_capability_in_accessdata($capability, $context, $accessdata, $doanything) {

    global $CFG;

    $path = $context->path;

    // build $contexts as a list of "paths" of the current
    // contexts and parents with the order top-to-bottom
    $contexts = array($path);
    while (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        $path = $matches[1];
        array_unshift($contexts, $path);
    }

    $ignoreguest = false;
    if (isset($accessdata['dr'])
        && ($capability    == 'moodle/course:view'
            || $capability == 'moodle/legacy:guest')) {
        // At the base, ignore rdefs where moodle/legacy:guest
        // is set
        $ignoreguest = $accessdata['dr'];
    }

    // Coerce it to an int
    $CAP_PROHIBIT = (int)CAP_PROHIBIT;

    $cc = count($contexts);

    $can = 0;
    $capdepth = 0;

    //
    // role-switches loop
    //
    if (isset($accessdata['rsw'])) {
        // check for isset() is fast
        // empty() is slow...
        if (empty($accessdata['rsw'])) {
            unset($accessdata['rsw']); // keep things fast and unambiguous
            break;
        }
        // From the bottom up...
        for ($n=$cc-1;$n>=0;$n--) {
            $ctxp = $contexts[$n];
            if (isset($accessdata['rsw'][$ctxp])) {
                // Found a switchrole assignment
                // check for that role _plus_ the default user role
                $ras = array($accessdata['rsw'][$ctxp],$CFG->defaultuserroleid);
                for ($rn=0;$rn<2;$rn++) {
                    $roleid = (int)$ras[$rn];
                    // Walk the path for capabilities
                    // from the bottom up...
                    for ($m=$cc-1;$m>=0;$m--) {
                        $capctxp = $contexts[$m];
                        if (isset($accessdata['rdef']["{$capctxp}:$roleid"][$capability])) {
                            $perm = (int)$accessdata['rdef']["{$capctxp}:$roleid"][$capability];

                            // The most local permission (first to set) wins
                            // the only exception is CAP_PROHIBIT
                            if ($can === 0) {
                                $can = $perm;
                            } elseif ($perm === $CAP_PROHIBIT) {
                                $can = $perm;
                                break;
                            }
                        }
                    }
                }
                // As we are dealing with a switchrole,
                // we return _here_, do _not_ walk up
                // the hierarchy any further
                if ($can < 1) {
                    if ($doanything) {
                        // didn't find it as an explicit cap,
                        // but maybe the user can doanything in this context...
                        return has_capability_in_accessdata('moodle/site:doanything', $context, $accessdata, false);
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }

            }
        }
    }

    //
    // Main loop for normal RAs
    // From the bottom up...
    //
    for ($n=$cc-1;$n>=0;$n--) {
        $ctxp = $contexts[$n];
        if (isset($accessdata['ra'][$ctxp])) {
            // Found role assignments on this leaf
            $ras = $accessdata['ra'][$ctxp];

            $rc          = count($ras);
            $ctxcan      = 0;
            $ctxcapdepth = 0;
            for ($rn=0;$rn<$rc;$rn++) {
                $roleid  = (int)$ras[$rn];
                $rolecan = 0;
                $rolecapdepth = 0;
                // Walk the path for capabilities
                // from the bottom up...
                for ($m=$cc-1;$m>=0;$m--) {
                    $capctxp = $contexts[$m];
                    // ignore some guest caps
                    // at base ra and rdef
                    if ($ignoreguest == $roleid
                        && $n === 0
                        && $m === 0
                        && isset($accessdata['rdef']["{$capctxp}:$roleid"]['moodle/legacy:guest'])
                        && $accessdata['rdef']["{$capctxp}:$roleid"]['moodle/legacy:guest'] > 0) {
                            continue;
                    }
                    if (isset($accessdata['rdef']["{$capctxp}:$roleid"][$capability])) {
                        $perm = (int)$accessdata['rdef']["{$capctxp}:$roleid"][$capability];
                        // The most local permission (first to set) wins
                        // the only exception is CAP_PROHIBIT
                        if ($rolecan === 0) {
                            $rolecan      = $perm;
                            $rolecapdepth = $m;
                        } elseif ($perm === $CAP_PROHIBIT) {
                            $rolecan      = $perm;
                            $rolecapdepth = $m;
                            break;
                        }
                    }
                }
                // Rules for RAs at the same context...
                // - prohibits always wins
                // - permissions at the same ctxlevel & capdepth are added together
                // - deeper capdepth wins
                if ($ctxcan === $CAP_PROHIBIT || $rolecan === $CAP_PROHIBIT) {
                    $ctxcan      = $CAP_PROHIBIT;
                    $ctxcapdepth = 0;
                } elseif ($ctxcapdepth === $rolecapdepth) {
                    $ctxcan += $rolecan;
                } elseif ($ctxcapdepth < $rolecapdepth) {
                    $ctxcan      = $rolecan;
                    $ctxcapdepth = $rolecapdepth;
                } else { // ctxcaptdepth is deeper
                    // rolecap ignored
                }
            }
            // The most local RAs with a defined
            // permission ($ctxcan) win, except
            // for CAP_PROHIBIT
            // NOTE: If we want the deepest RDEF to
            // win regardless of the depth of the RA,
            // change the elseif below to read
            // ($can === 0 || $capdepth < $ctxcapdepth) {
            if ($ctxcan === $CAP_PROHIBIT) {
                $can = $ctxcan;
                break;
            } elseif ($can === 0) { // see note above
                $can      = $ctxcan;
                $capdepth = $ctxcapdepth;
            }
        }
    }

    if ($can < 1) {
        if ($doanything) {
            // didn't find it as an explicit cap,
            // but maybe the user can doanything in this context...
            return has_capability_in_accessdata('moodle/site:doanything', $context, $accessdata, false);
        } else {
            return false;
        }
    } else {
        return true;
    }

}

/**
 * @param object $context
 * @param array $accessdata
 * @return array
 */
function aggregate_roles_from_accessdata($context, $accessdata) {

    $path = $context->path;

    // build $contexts as a list of "paths" of the current
    // contexts and parents with the order top-to-bottom
    $contexts = array($path);
    while (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        $path = $matches[1];
        array_unshift($contexts, $path);
    }

    $cc = count($contexts);

    $roles = array();
    // From the bottom up...
    for ($n=$cc-1;$n>=0;$n--) {
        $ctxp = $contexts[$n];
        if (isset($accessdata['ra'][$ctxp]) && count($accessdata['ra'][$ctxp])) {
            // Found assignments on this leaf
            $addroles = $accessdata['ra'][$ctxp];
            $roles    = array_merge($roles, $addroles);
        }
    }

    return array_unique($roles);
}

/**
 * A convenience function that tests has_capability, and displays an error if
 * the user does not have that capability.
 *
 * NOTE before Moodle 2.0, this function attempted to make an appropriate
 * require_login call before checking the capability. This is no longer the case.
 * You must call require_login (or one of its variants) if you want to check the
 * user is logged in, before you call this function.
 *
 * @see has_capability()
 *
 * @param string $capability the name of the capability to check. For example mod/forum:view
 * @param object $context the context to check the capability in. You normally get this with {@link get_context_instance}.
 * @param integer $userid A user id. By default (null) checks the permissions of the current user.
 * @param bool $doanything If false, ignore the special moodle/site:doanything capability that admin-like roles have.
 * @param string $errorstring The error string to to user. Defaults to 'nopermissions'.
 * @param string $stringfile The language file to load the error string from. Defaults to 'error'.
 * @return void terminates with an error if the user does not have the given capability.
 */
function require_capability($capability, $context, $userid = NULL, $doanything = true,
                            $errormessage = 'nopermissions', $stringfile = '') {
    if (!has_capability($capability, $context, $userid, $doanything)) {
        throw new required_capability_exception($context, $capability, $errormessage, $stringfile);
    }
}

/**
 * Get an array of courses where cap requested is available
 *
 * Get an array of courses (with magic extra bits)
 * where the accessdata and in DB enrolments show
 * that the cap requested is available.
 *
 * The main use is for get_my_courses().
 *
 * Notes
 *
 * - $fields is an array of fieldnames to ADD
 *   so name the fields you really need, which will
 *   be added and uniq'd
 *
 * - the course records have $c->context which is a fully
 *   valid context object. Saves you a query per course!
 *
 * - the course records have $c->categorypath to make
 *   category lookups cheap
 *
 * - current implementation is split in -
 *
 *   - if the user has the cap systemwide, stupidly
 *     grab *every* course for a capcheck. This eats
 *     a TON of bandwidth, specially on large sites
 *     with separate DBs...
 *
 *   - otherwise, fetch "likely" courses with a wide net
 *     that should get us _cheaply_ at least the courses we need, and some
 *     we won't - we get courses that...
 *      - are in a category where user has the cap
 *      - or where use has a role-assignment (any kind)
 *      - or where the course has an override on for this cap
 *
 *   - walk the courses recordset checking the caps oneach one
 *     the checks are all in memory and quite fast
 *     (though we could implement a specialised variant of the
 *     has_capability_in_accessdata() code to speed it up)
 *
 * @global object
 * @global object
 * @param string $capability - name of the capability
 * @param array  $accessdata - accessdata session array
 * @param bool   $doanything - if false, ignore do anything
 * @param string $sort - sorting fields - prefix each fieldname with "c."
 * @param array  $fields - additional fields you are interested in...
 * @param int    $limit  - set if you want to limit the number of courses
 * @return array $courses - ordered array of course objects - see notes above
 */
function get_user_courses_bycap($userid, $cap, $accessdata, $doanything, $sort='c.sortorder ASC', $fields=NULL, $limit=0) {

    global $CFG, $DB;

    // Slim base fields, let callers ask for what they need...
    $basefields = array('id', 'sortorder', 'shortname', 'idnumber');

    if (!is_null($fields)) {
        $fields = array_merge($basefields, $fields);
        $fields = array_unique($fields);
    } else {
        $fields = $basefields;
    }
    // If any of the fields is '*', leave it alone, discarding the rest
    // to avoid ambiguous columns under some silly DBs. See MDL-18746 :-D
    if (in_array('*', $fields)) {
        $fields = array('*');
    }
    $coursefields = 'c.' .implode(',c.', $fields);

    $sort = trim($sort);
    if ($sort !== '') {
        $sort = "ORDER BY $sort";
    }

    $sysctx = get_context_instance(CONTEXT_SYSTEM);
    if (has_capability_in_accessdata($cap, $sysctx, $accessdata, $doanything)) {
        //
        // Apparently the user has the cap sitewide, so walk *every* course
        // (the cap checks are moderately fast, but this moves massive bandwidth w the db)
        // Yuck.
        //
        $sql = "SELECT $coursefields,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel,
                       cc.path AS categorypath
                  FROM {course} c
                  JOIN {course_categories} cc
                       ON c.category=cc.id
                  JOIN {context} ctx
                       ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                 $sort ";
        $rs = $DB->get_recordset_sql($sql);
    } else {
        //
        // narrow down where we have the caps to a few contexts
        // this will be a combination of
        // - courses    where user has an explicit enrolment
        // - courses    that have an override (any status) on that capability
        // - categories where user has the rights (granted status) on that capability
        //
        $sql = "SELECT ctx.*
                  FROM {context} ctx
                 WHERE ctx.contextlevel=".CONTEXT_COURSECAT."
              ORDER BY ctx.depth";
        $rs = $DB->get_recordset_sql($sql);
        $catpaths = array();
        foreach ($rs as $catctx) {
            if ($catctx->path != ''
                && has_capability_in_accessdata($cap, $catctx, $accessdata, $doanything)) {
                $catpaths[] = $catctx->path;
            }
        }
        $rs->close();
        $catclause = '';
        $params = array();
        if (count($catpaths)) {
            $cc = count($catpaths);
            for ($n=0;$n<$cc;$n++) {
                $catpaths[$n] = "ctx.path LIKE '{$catpaths[$n]}/%'";
            }
            $catclause = 'WHERE (' . implode(' OR ', $catpaths) .')';
        }
        unset($catpaths);

        $capany = '';
        if ($doanything) {
            $capany = " OR rc.capability=:doany";
            $params['doany'] = 'moodle/site:doanything';
        }

        /// UNION 3 queries:
        /// - user role assignments in courses
        /// - user capability (override - any status) in courses
        /// - user right (granted status) in categories (optionally executed)
        /// Enclosing the 3-UNION into an inline_view to avoid column names conflict and making the ORDER BY cross-db
        /// and to allow selection of TEXT columns in the query (MSSQL and Oracle limitation). MDL-16209
        $sql = "
            SELECT $coursefields, ctxid, ctxpath, ctxdepth, ctxlevel, categorypath
              FROM (
                    SELECT c.id,
                           ctx.id AS ctxid, ctx.path AS ctxpath,
                           ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel,
                           cc.path AS categorypath
                    FROM {course} c
                    JOIN {course_categories} cc
                      ON c.category=cc.id
                    JOIN {context} ctx
                      ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    JOIN {role_assignments} ra
                      ON (ra.contextid=ctx.id AND ra.userid=:userid)
                    UNION
                    SELECT c.id,
                           ctx.id AS ctxid, ctx.path AS ctxpath,
                           ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel,
                           cc.path AS categorypath
                    FROM {course} c
                    JOIN {course_categories} cc
                      ON c.category=cc.id
                    JOIN {context} ctx
                      ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    JOIN {role_capabilities} rc
                      ON (rc.contextid=ctx.id AND (rc.capability=:cap $capany)) ";

        if (!empty($catclause)) { /// If we have found the right in categories, add child courses here too
            $sql .= "
                    UNION
                    SELECT c.id,
                           ctx.id AS ctxid, ctx.path AS ctxpath,
                           ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel,
                           cc.path AS categorypath
                    FROM {course} c
                    JOIN {course_categories} cc
                      ON c.category=cc.id
                    JOIN {context} ctx
                      ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    $catclause";
        }

    /// Close the inline_view and join with courses table to get requested $coursefields
        $sql .= "
                ) inline_view
                INNER JOIN {course} c
                    ON inline_view.id = c.id";

    /// To keep cross-db we need to strip any prefix in the ORDER BY clause for queries using UNION
        $sql .= "
                " . preg_replace('/[a-z]+\./i', '', $sort); /// Add ORDER BY clause

        $params['userid'] = $userid;
        $params['cap']    = $cap;
        $rs = $DB->get_recordset_sql($sql, $params);
    }

/// Confirm rights (granted capability) for each course returned
    $courses = array();
    $cc = 0; // keep count
    if ($rs) {
        foreach ($rs as $c) {
            // build the context obj
            $c = make_context_subobj($c);

            if (has_capability_in_accessdata($cap, $c->context, $accessdata, $doanything)) {
                if ($limit > 0 && $cc >= $limit) {
                    break;
                }

                $courses[] = $c;
                $cc++;
            }
        }
        $rs->close();
    }

    return $courses;
}


/**
 * Return a nested array showing role assignments
 * all relevant role capabilities for the user at
 * site/metacourse/course_category/course levels
 *
 * We do _not_ delve deeper than courses because the number of
 * overrides at the module/block levels is HUGE.
 *
 * [ra]   => [/path/][]=roleid
 * [rdef] => [/path/:roleid][capability]=permission
 * [loaded] => array('/path', '/path')
 *
 * @global object
 * @global object
 * @param $userid integer - the id of the user
 */
function get_user_access_sitewide($userid) {

    global $CFG, $DB;

    /* Get in 3 cheap DB queries...
     * - role assignments
     * - relevant role caps
     *   - above and within this user's RAs
     *   - below this user's RAs - limited to course level
     */

    $accessdata           = array(); // named list
    $accessdata['ra']     = array();
    $accessdata['rdef']   = array();
    $accessdata['loaded'] = array();

    //
    // Role assignments
    //
    $sql = "SELECT ctx.path, ra.roleid
              FROM {role_assignments} ra
              JOIN {context} ctx ON ctx.id=ra.contextid
             WHERE ra.userid = ? AND ctx.contextlevel <= ".CONTEXT_COURSE;
    $params = array($userid);
    $rs = $DB->get_recordset_sql($sql, $params);

    //
    // raparents collects paths & roles we need to walk up
    // the parenthood to build the rdef
    //
    $raparents = array();
    if ($rs) {
        foreach ($rs as $ra) {
            // RAs leafs are arrays to support multi
            // role assignments...
            if (!isset($accessdata['ra'][$ra->path])) {
                $accessdata['ra'][$ra->path] = array();
            }
            array_push($accessdata['ra'][$ra->path], $ra->roleid);

            // Concatenate as string the whole path (all related context)
            // for this role. This is damn faster than using array_merge()
            // Will unique them later
            if (isset($raparents[$ra->roleid])) {
                $raparents[$ra->roleid] .= $ra->path;
            } else {
                $raparents[$ra->roleid] = $ra->path;
            }
        }
        unset($ra);
        $rs->close();
    }

    // Walk up the tree to grab all the roledefs
    // of interest to our user...
    //
    // NOTE: we use a series of IN clauses here - which
    // might explode on huge sites with very convoluted nesting of
    // categories... - extremely unlikely that the number of categories
    // and roletypes is so large that we hit the limits of IN()
    $clauses = '';
    $cparams = array();
    foreach ($raparents as $roleid=>$strcontexts) {
        $contexts = implode(',', array_unique(explode('/', trim($strcontexts, '/'))));
        if ($contexts ==! '') {
            if ($clauses) {
                $clauses .= ' OR ';
            }
            $clauses .= "(roleid=? AND contextid IN ($contexts))";
            $cparams[] = $roleid;
        }
    }

    if ($clauses !== '') {
        $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
                FROM {role_capabilities} rc
                JOIN {context} ctx
                  ON rc.contextid=ctx.id
                WHERE $clauses";

        unset($clauses);
        $rs = $DB->get_recordset_sql($sql, $cparams);

        if ($rs) {
            foreach ($rs as $rd) {
                $k = "{$rd->path}:{$rd->roleid}";
                $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
            }
            unset($rd);
            $rs->close();
        }
    }

    //
    // Overrides for the role assignments IN SUBCONTEXTS
    // (though we still do _not_ go below the course level.
    //
    // NOTE that the JOIN w sctx is with 3-way triangulation to
    // catch overrides to the applicable role in any subcontext, based
    // on the path field of the parent.
    //
    $sql = "SELECT sctx.path, ra.roleid,
                   ctx.path AS parentpath,
                   rco.capability, rco.permission
              FROM {role_assignments} ra
              JOIN {context} ctx
                   ON ra.contextid=ctx.id
              JOIN {context} sctx
                   ON (sctx.path LIKE " . $DB->sql_concat('ctx.path',"'/%'"). " )
              JOIN {role_capabilities} rco
                   ON (rco.roleid=ra.roleid AND rco.contextid=sctx.id)
             WHERE ra.userid = ?
               AND ctx.contextlevel <= ".CONTEXT_COURSECAT."
               AND sctx.contextlevel <= ".CONTEXT_COURSE."
          ORDER BY sctx.depth, sctx.path, ra.roleid";
    $params = array($userid);
    $rs = $DB->get_recordset_sql($sql, $params);
    if ($rs) {
        foreach ($rs as $rd) {
            $k = "{$rd->path}:{$rd->roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
        $rs->close();
    }
    return $accessdata;
}

/**
 * Add to the access ctrl array the data needed by a user for a given context
 *
 * @global object
 * @global object
 * @param integer $userid the id of the user
 * @param object $context needs path!
 * @param array $accessdata accessdata array
 */
function load_subcontext($userid, $context, &$accessdata) {

    global $CFG, $DB;

    /* Get the additional RAs and relevant rolecaps
     * - role assignments - with role_caps
     * - relevant role caps
     *   - above this user's RAs
     *   - below this user's RAs - limited to course level
     */

    $base = "/" . SYSCONTEXTID;

    //
    // Replace $context with the target context we will
    // load. Normally, this will be a course context, but
    // may be a different top-level context.
    //
    // We have 3 cases
    //
    // - Course
    // - BLOCK/PERSON/USER/COURSE(sitecourse) hanging from SYSTEM
    // - BLOCK/MODULE/GROUP hanging from a course
    //
    // For course contexts, we _already_ have the RAs
    // but the cost of re-fetching is minimal so we don't care.
    //
    if ($context->contextlevel !== CONTEXT_COURSE
        && $context->path !== "$base/{$context->id}") {
        // Case BLOCK/MODULE/GROUP hanging from a course
        // Assumption: the course _must_ be our parent
        // If we ever see stuff nested further this needs to
        // change to do 1 query over the exploded path to
        // find out which one is the course
        $courses = explode('/',get_course_from_path($context->path));
        $targetid = array_pop($courses);
        $context = get_context_instance_by_id($targetid);

    }

    //
    // Role assignments in the context and below
    //
    $sql = "SELECT ctx.path, ra.roleid
              FROM {role_assignments} ra
              JOIN {context} ctx
                   ON ra.contextid=ctx.id
             WHERE ra.userid = ?
                   AND (ctx.path = ? OR ctx.path LIKE ?)
          ORDER BY ctx.depth, ctx.path, ra.roleid";
    $params = array($userid, $context->path, $context->path."/%");
    $rs = $DB->get_recordset_sql($sql, $params);

    //
    // Read in the RAs, preventing duplicates
    //
    if ($rs) {
        $localroles = array();
        $lastseen  = '';
        foreach ($rs as $ra) {
            if (!isset($accessdata['ra'][$ra->path])) {
                $accessdata['ra'][$ra->path] = array();
            }
            // only add if is not a repeat caused
            // by capability join...
            // (this check is cheaper than in_array())
            if ($lastseen !== $ra->path.':'.$ra->roleid) {
                $lastseen = $ra->path.':'.$ra->roleid;
                array_push($accessdata['ra'][$ra->path], $ra->roleid);
                array_push($localroles,           $ra->roleid);
            }
        }
        $rs->close();
    }

    //
    // Walk up and down the tree to grab all the roledefs
    // of interest to our user...
    //
    // NOTES
    // - we use IN() but the number of roles is very limited.
    //
    $courseroles    = aggregate_roles_from_accessdata($context, $accessdata);

    // Do we have any interesting "local" roles?
    $localroles = array_diff($localroles,$courseroles); // only "new" local roles
    $wherelocalroles='';
    if (count($localroles)) {
        // Role defs for local roles in 'higher' contexts...
        $contexts = substr($context->path, 1); // kill leading slash
        $contexts = str_replace('/', ',', $contexts);
        $localroleids = implode(',',$localroles);
        $wherelocalroles="OR (rc.roleid IN ({$localroleids})
                              AND ctx.id IN ($contexts))" ;
    }

    // We will want overrides for all of them
    $whereroles = '';
    if ($roleids  = implode(',',array_merge($courseroles,$localroles))) {
        $whereroles = "rc.roleid IN ($roleids) AND";
    }
    $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
              FROM {role_capabilities} rc
              JOIN {context} ctx
                   ON rc.contextid=ctx.id
             WHERE ($whereroles
                    (ctx.id=? OR ctx.path LIKE ?))
                   $wherelocalroles
          ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";
    $params = array($context->id, $context->path."/%");

    $newrdefs = array();
    if ($rs = $DB->get_recordset_sql($sql, $params)) {
        foreach ($rs as $rd) {
            $k = "{$rd->path}:{$rd->roleid}";
            if (!array_key_exists($k, $newrdefs)) {
                $newrdefs[$k] = array();
            }
            $newrdefs[$k][$rd->capability] = $rd->permission;
        }
        $rs->close();
    } else {
        debugging('Bad SQL encountered!');
    }

    compact_rdefs($newrdefs);
    foreach ($newrdefs as $key=>$value) {
        $accessdata['rdef'][$key] =& $newrdefs[$key];
    }

    // error_log("loaded {$context->path}");
    $accessdata['loaded'][] = $context->path;
}

/**
 * Add to the access ctrl array the data needed by a role for a given context.
 *
 * The data is added in the rdef key.
 *
 * This role-centric function is useful for role_switching
 * and to get an overview of what a role gets under a
 * given context and below...
 *
 * @global object
 * @global object
 * @param integer $roleid the id of the user
 * @param object $context needs path!
 * @param array $accessdata accessdata array null by default
 * @return array
 */
function get_role_access_bycontext($roleid, $context, $accessdata=NULL) {

    global $CFG, $DB;

    /* Get the relevant rolecaps into rdef
     * - relevant role caps
     *   - at ctx and above
     *   - below this ctx
     */

    if (is_null($accessdata)) {
        $accessdata           = array(); // named list
        $accessdata['ra']     = array();
        $accessdata['rdef']   = array();
        $accessdata['loaded'] = array();
    }

    $contexts = substr($context->path, 1); // kill leading slash
    $contexts = str_replace('/', ',', $contexts);

    //
    // Walk up and down the tree to grab all the roledefs
    // of interest to our role...
    //
    // NOTE: we use an IN clauses here - which
    // might explode on huge sites with very convoluted nesting of
    // categories... - extremely unlikely that the number of nested
    // categories is so large that we hit the limits of IN()
    //
    $sql = "SELECT ctx.path, rc.capability, rc.permission
              FROM {role_capabilities} rc
              JOIN {context} ctx
                   ON rc.contextid=ctx.id
             WHERE rc.roleid=? AND
                   ( ctx.id IN ($contexts) OR
                    ctx.path LIKE ? )
          ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";
    $params = array($roleid, $context->path."/%");

    if ($rs = $DB->get_recordset_sql($sql, $params)) {
        foreach ($rs as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        $rs->close();
    }

    return $accessdata;
}

/**
 * Load accessdata for a user into the $ACCESSLIB_PRIVATE->accessdatabyuser global
 *
 * Used by has_capability() - but feel free
 * to call it if you are about to run a BIG
 * cron run across a bazillion users.
 *
 * @global object
 * @global object
 * @param int $userid
 * @return array returns ACCESSLIB_PRIVATE->accessdatabyuser[userid]
 */
function load_user_accessdata($userid) {
    global $CFG, $ACCESSLIB_PRIVATE;

    $base = '/'.SYSCONTEXTID;

    $accessdata = get_user_access_sitewide($userid);
    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    //
    // provide "default role" & set 'dr'
    //
    if (!empty($CFG->defaultuserroleid)) {
        $accessdata = get_role_access($CFG->defaultuserroleid, $accessdata);
        if (!isset($accessdata['ra'][$base])) {
            $accessdata['ra'][$base] = array($CFG->defaultuserroleid);
        } else {
            array_push($accessdata['ra'][$base], $CFG->defaultuserroleid);
        }
        $accessdata['dr'] = $CFG->defaultuserroleid;
    }

    //
    // provide "default frontpage role"
    //
    if (!empty($CFG->defaultfrontpageroleid)) {
        $base = '/'. SYSCONTEXTID .'/'. $frontpagecontext->id;
        $accessdata = get_default_frontpage_role_access($CFG->defaultfrontpageroleid, $accessdata);
        if (!isset($accessdata['ra'][$base])) {
            $accessdata['ra'][$base] = array($CFG->defaultfrontpageroleid);
        } else {
            array_push($accessdata['ra'][$base], $CFG->defaultfrontpageroleid);
        }
    }
    // for dirty timestamps in cron
    $accessdata['time'] = time();

    $ACCESSLIB_PRIVATE->accessdatabyuser[$userid] = $accessdata;
    compact_rdefs($ACCESSLIB_PRIVATE->accessdatabyuser[$userid]['rdef']);

    return $ACCESSLIB_PRIVATE->accessdatabyuser[$userid];
}

/**
 * Use shared copy of role definistions stored in ACCESSLIB_PRIVATE->roledefinitions;
 *
 * @global object
 * @param array $rdefs array of role definitions in contexts
 */
function compact_rdefs(&$rdefs) {
    global $ACCESSLIB_PRIVATE;

    /*
     * This is a basic sharing only, we could also
     * use md5 sums of values. The main purpose is to
     * reduce mem in cron jobs - many users in $ACCESSLIB_PRIVATE->accessdatabyuser array.
     */

    foreach ($rdefs as $key => $value) {
        if (!array_key_exists($key, $ACCESSLIB_PRIVATE->roledefinitions)) {
            $ACCESSLIB_PRIVATE->roledefinitions[$key] = $rdefs[$key];
        }
        $rdefs[$key] =& $ACCESSLIB_PRIVATE->roledefinitions[$key];
    }
}

/**
 * A convenience function to completely load all the capabilities
 * for the current user.   This is what gets called from complete_user_login()
 * for example. Call it only _after_ you've setup $USER and called
 * check_enrolment_plugins();
 * @see check_enrolment_plugins()
 *
 * @global object
 * @global object
 * @global object
 */
function load_all_capabilities() {
    global $USER, $CFG, $ACCESSLIB_PRIVATE;

    // roles not installed yet - we are in the middle of installation
    if (during_initial_install()) {
        return;
    }

    $base = '/'.SYSCONTEXTID;

    if (isguestuser()) {
        $guest = get_guest_role();

        // Load the rdefs
        $USER->access = get_role_access($guest->id);
        // Put the ghost enrolment in place...
        $USER->access['ra'][$base] = array($guest->id);


    } else if (isloggedin()) {

        $accessdata = get_user_access_sitewide($USER->id);

        //
        // provide "default role" & set 'dr'
        //
        if (!empty($CFG->defaultuserroleid)) {
            $accessdata = get_role_access($CFG->defaultuserroleid, $accessdata);
            if (!isset($accessdata['ra'][$base])) {
                $accessdata['ra'][$base] = array($CFG->defaultuserroleid);
            } else {
                array_push($accessdata['ra'][$base], $CFG->defaultuserroleid);
            }
            $accessdata['dr'] = $CFG->defaultuserroleid;
        }

        $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);

        //
        // provide "default frontpage role"
        //
        if (!empty($CFG->defaultfrontpageroleid)) {
            $base = '/'. SYSCONTEXTID .'/'. $frontpagecontext->id;
            $accessdata = get_default_frontpage_role_access($CFG->defaultfrontpageroleid, $accessdata);
            if (!isset($accessdata['ra'][$base])) {
                $accessdata['ra'][$base] = array($CFG->defaultfrontpageroleid);
            } else {
                array_push($accessdata['ra'][$base], $CFG->defaultfrontpageroleid);
            }
        }
        $USER->access = $accessdata;

    } else if (!empty($CFG->notloggedinroleid)) {
        $USER->access = get_role_access($CFG->notloggedinroleid);
        $USER->access['ra'][$base] = array($CFG->notloggedinroleid);
    }

    // Timestamp to read dirty context timestamps later
    $USER->access['time'] = time();
    $ACCESSLIB_PRIVATE->dirtycontexts = array();

    // Clear to force a refresh
    unset($USER->mycourses);
}

/**
 * A convenience function to completely reload all the capabilities
 * for the current user when roles have been updated in a relevant
 * context -- but PRESERVING switchroles and loginas.
 *
 * That is - completely transparent to the user.
 *
 * Note: rewrites $USER->access completely.
 *
 * @global object
 * @global object
 */
function reload_all_capabilities() {
    global $USER, $DB;

    // error_log("reloading");
    // copy switchroles
    $sw = array();
    if (isset($USER->access['rsw'])) {
        $sw = $USER->access['rsw'];
        // error_log(print_r($sw,1));
    }

    unset($USER->access);
    unset($USER->mycourses);

    load_all_capabilities();

    foreach ($sw as $path => $roleid) {
        $context = $DB->get_record('context', array('path'=>$path));
        role_switch($roleid, $context);
    }

}

/**
 * Adds a temp role to an accessdata array.
 *
 * Useful for the "temporary guest" access
 * we grant to logged-in users.
 *
 * Note - assumes a course context!
 *
 * @global object
 * @global object
 * @param object $content
 * @param int $roleid
 * @param array $accessdata
 * @return array Returns access data
 */
function load_temp_role($context, $roleid, $accessdata) {

    global $CFG, $DB;

    //
    // Load rdefs for the role in -
    // - this context
    // - all the parents
    // - and below - IOWs overrides...
    //

    // turn the path into a list of context ids
    $contexts = substr($context->path, 1); // kill leading slash
    $contexts = str_replace('/', ',', $contexts);

    $sql = "SELECT ctx.path, rc.capability, rc.permission
              FROM {context} ctx
              JOIN {role_capabilities} rc
                   ON rc.contextid=ctx.id
             WHERE (ctx.id IN ($contexts)
                    OR ctx.path LIKE ?)
                   AND rc.roleid = ?
          ORDER BY ctx.depth, ctx.path";
    $params = array($context->path."/%", $roleid);
    if ($rs = $DB->get_recordset_sql($sql, $params)) {
        foreach ($rs as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        $rs->close();
    }

    //
    // Say we loaded everything for the course context
    // - which we just did - if the user gets a proper
    // RA in this session, this data will need to be reloaded,
    // but that is handled by the complete accessdata reload
    //
    array_push($accessdata['loaded'], $context->path);

    //
    // Add the ghost RA
    //
    if (isset($accessdata['ra'][$context->path])) {
        array_push($accessdata['ra'][$context->path], $roleid);
    } else {
        $accessdata['ra'][$context->path] = array($roleid);
    }

    return $accessdata;
}


/**
 * Check all the login enrolment information for the given user object
 * by querying the enrolment plugins
 *
 * @global object
 * @param object $user
 * @return void
 */
function check_enrolment_plugins(&$user) {
    global $CFG;

    if (empty($user->id) or isguestuser($user)) {
        // shortcut - there is no enrolment work for guests and not-logged-in users
        return;
    }

    static $inprogress = array();  // To prevent this function being called more than once in an invocation

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
 * Returns array of all legacy roles.
 *
 * @return array
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

/**
 * @param int roleid
 * @return string
 */
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
 *
 * @param string $capability
 * @param array $legacyperms an array in the format (example):
 *                      'guest' => CAP_PREVENT,
 *                      'student' => CAP_ALLOW,
 *                      'teacher' => CAP_ALLOW,
 *                      'editingteacher' => CAP_ALLOW,
 *                      'coursecreator' => CAP_ALLOW,
 *                      'admin' => CAP_ALLOW
 * @return boolean success or failure.
 */
function assign_legacy_capabilities($capability, $legacyperms) {

    $legacyroles = get_legacy_roles();

    foreach ($legacyperms as $type => $perm) {

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);

        if (!array_key_exists($type, $legacyroles)) {
            print_error('invalidlegacy', '', '', $type);
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
 * Checks to see if a capability is one of the special capabilities
 *
 * Checks to see if a capability is one of the special capabilities
 *      (either a legacy capability, or moodle/site:doanything).
 *
 * @param string $capabilityname the capability name, e.g. mod/forum:view.
 * @return boolean whether this is one of the special capabilities.
 */
function is_legacy($capabilityname) {
    if ($capabilityname == 'moodle/site:doanything' || strpos($capabilityname, 'moodle/legacy') === 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param object $capability a capbility - a row from the capabilitites table.
 * @return boolean whether this capability is safe - that is, wether people with the
 *      safeoverrides capability should be allowed to change it.
 */
function is_safe_capability($capability) {
    return !((RISK_DATALOSS | RISK_MANAGETRUST | RISK_CONFIG | RISK_XSS | RISK_PERSONAL) & $capability->riskbitmask);
}

/**********************************
 * Context Manipulation functions *
 **********************************/

/**
 * Create a new context record for use by all roles-related stuff
 *
 * Create a new context record for use by all roles-related stuff
 * assumes that the caller has done the homework.
 *
 * @global object
 * @global object
 * @param int $contextlevel
 * @param int $instanceid
 * @return object newly created context
 */
function create_context($contextlevel, $instanceid) {

    global $CFG, $DB;

    if ($contextlevel == CONTEXT_SYSTEM) {
        return create_system_context();
    }

    $context = new object();
    $context->contextlevel = $contextlevel;
    $context->instanceid = $instanceid;

    // Define $context->path based on the parent
    // context. In other words... Who is your daddy?
    $basepath  = '/' . SYSCONTEXTID;
    $basedepth = 1;

    $result = true;
    $error_message = null;

    switch ($contextlevel) {
        case CONTEXT_COURSECAT:
            $sql = "SELECT ctx.path, ctx.depth
                      FROM {context}           ctx
                      JOIN {course_categories} cc
                           ON (cc.parent=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT.")
                     WHERE cc.id=?";
            $params = array($instanceid);
            if ($p = $DB->get_record_sql($sql, $params)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($category = $DB->get_record('course_categories', array('id'=>$instanceid))) {
                if (empty($category->parent)) {
                    // ok - this is a top category
                } else if ($parent = get_context_instance(CONTEXT_COURSECAT, $category->parent)) {
                    $basepath  = $parent->path;
                    $basedepth = $parent->depth;
                } else {
                    // wrong parent category - no big deal, this can be fixed later
                    $basepath  = null;
                    $basedepth = 0;
                }
            } else {
                // incorrect category id
                $error_message = "incorrect course category id ($instanceid)";
                $result = false;
            }
            break;

        case CONTEXT_COURSE:
            $sql = "SELECT ctx.path, ctx.depth
                      FROM {context} ctx
                      JOIN {course}  c
                           ON (c.category=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT.")
                     WHERE c.id=? AND c.id !=" . SITEID;
            $params = array($instanceid);
            if ($p = $DB->get_record_sql($sql, $params)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($course = $DB->get_record('course', array('id'=>$instanceid))) {
                if ($course->id == SITEID) {
                    //ok - no parent category
                } else if ($parent = get_context_instance(CONTEXT_COURSECAT, $course->category)) {
                    $basepath  = $parent->path;
                    $basedepth = $parent->depth;
                } else {
                    // wrong parent category of course - no big deal, this can be fixed later
                    $basepath  = null;
                    $basedepth = 0;
                }
            } else if ($instanceid == SITEID) {
                // no errors for missing site course during installation
                return false;
            } else {
                // incorrect course id
                $error_message = "incorrect course id ($instanceid)";
                $result = false;
            }
            break;

        case CONTEXT_MODULE:
            $sql = "SELECT ctx.path, ctx.depth
                      FROM {context}        ctx
                      JOIN {course_modules} cm
                           ON (cm.course=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                     WHERE cm.id=?";
            $params = array($instanceid);
            if ($p = $DB->get_record_sql($sql, $params)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($cm = $DB->get_record('course_modules', array('id'=>$instanceid))) {
                if ($parent = get_context_instance(CONTEXT_COURSE, $cm->course)) {
                    $basepath  = $parent->path;
                    $basedepth = $parent->depth;
                } else {
                    // course does not exist - modules can not exist without a course
                    $error_message = "course does not exist ($cm->course) - modules can not exist without a course";
                    $result = false;
                }
            } else {
                // cm does not exist
                $error_message = "cm with id $instanceid does not exist";
                $result = false;
            }
            break;

        case CONTEXT_BLOCK:
            $sql = "SELECT ctx.path, ctx.depth
                      FROM {context} ctx
                      JOIN {block_instances} bi ON (bi.parentcontextid=ctx.id)
                     WHERE bi.id = ?";
            $params = array($instanceid, CONTEXT_COURSE);
            if ($p = $DB->get_record_sql($sql, $params)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else {
                // block does not exist
                $error_message = 'block or parent context does not exist';
                $result = false;
            }
            break;
        case CONTEXT_USER:
            // default to basepath
            break;
    }

    // if grandparents unknown, maybe rebuild_context_path() will solve it later
    if ($basedepth != 0) {
        $context->depth = $basedepth+1;
    }

    if ($result and $id = $DB->insert_record('context', $context)) {
        // can't set the full path till we know the id!
        if ($basedepth != 0 and !empty($basepath)) {
            $DB->set_field('context', 'path', $basepath.'/'. $id, array('id'=>$id));
        }
        return get_context_instance_by_id($id);

    } else {
        debugging('Error: could not insert new context level "'.
                  s($contextlevel).'", instance "'.
                  s($instanceid).'". ' . $error_message);

        return false;
    }
}

/**
 * Returns system context or null if can not be created yet.
 *
 * @todo can not use get_record() because we do not know if query failed :-(
 * switch to get_record() later
 *
 * @global object
 * @global object
 * @param bool $cache use caching
 * @return mixed system context or null
 */
function get_system_context($cache=true) {
    global $DB, $ACCESSLIB_PRIVATE;
    if ($cache and defined('SYSCONTEXTID')) {
        if (is_null($ACCESSLIB_PRIVATE->systemcontext)) {
            $ACCESSLIB_PRIVATE->systemcontext = new object();
            $ACCESSLIB_PRIVATE->systemcontext->id           = SYSCONTEXTID;
            $ACCESSLIB_PRIVATE->systemcontext->contextlevel = CONTEXT_SYSTEM;
            $ACCESSLIB_PRIVATE->systemcontext->instanceid   = 0;
            $ACCESSLIB_PRIVATE->systemcontext->path         = '/'.SYSCONTEXTID;
            $ACCESSLIB_PRIVATE->systemcontext->depth        = 1;
        }
        return $ACCESSLIB_PRIVATE->systemcontext;
    }
    try {
        $context = $DB->get_record('context', array('contextlevel'=>CONTEXT_SYSTEM));
    } catch (dml_exception $e) {
        //table does not exist yet, sorry
        return null;
    }

    if (!$context) {
        $context = new object();
        $context->contextlevel = CONTEXT_SYSTEM;
        $context->instanceid   = 0;
        $context->depth        = 1;
        $context->path         = NULL; //not known before insert

        try {
            $context->id = $DB->insert_record('context', $context);
        } catch (dml_exception $e) {
            // can not create context yet, sorry
            return null;
        }
    }

    if (!isset($context->depth) or $context->depth != 1 or $context->instanceid != 0 or $context->path != '/'.$context->id) {
        $context->instanceid   = 0;
        $context->path         = '/'.$context->id;
        $context->depth        = 1;
        $DB->update_record('context', $context);
    }

    if (!defined('SYSCONTEXTID')) {
        define('SYSCONTEXTID', $context->id);
    }

    $ACCESSLIB_PRIVATE->systemcontext = $context;
    return $ACCESSLIB_PRIVATE->systemcontext;
}

/**
 * Remove a context record and any dependent entries,
 * removes context from static context cache too
 *
 * @global object
 * @global object
 * @param int $level
 * @param int $instanceid
 * @return bool properly deleted
 */
function delete_context($contextlevel, $instanceid) {
    global $DB, $ACCESSLIB_PRIVATE, $CFG;

    // do not use get_context_instance(), because the related object might not exist,
    // or the context does not exist yet and it would be created now
    if ($context = $DB->get_record('context', array('contextlevel'=>$contextlevel, 'instanceid'=>$instanceid))) {
        $result = $DB->delete_records('role_assignments', array('contextid'=>$context->id)) &&
                  $DB->delete_records('role_capabilities', array('contextid'=>$context->id)) &&
                  $DB->delete_records('context', array('id'=>$context->id)) &&
                  $DB->delete_records('role_names', array('contextid'=>$context->id));

        // do not mark dirty contexts if parents unknown
        if (!is_null($context->path) and $context->depth > 0) {
            mark_context_dirty($context->path);
        }

        // purge static context cache if entry present
        unset($ACCESSLIB_PRIVATE->contexts[$contextlevel][$instanceid]);
        unset($ACCESSLIB_PRIVATE->contextsbyid[$context->id]);

        blocks_delete_all_for_context($context->id);
        filter_delete_all_for_context($context->id);

        // TODO: MDL-20635 Replace with a means to delete during a cron run
        require_once($CFG->libdir.'/filelib.php');
        $fs = get_file_storage();
        $fs->delete_area_files($context->id);

        return $result;
    } else {

        return true;
    }
}

/**
 * Precreates all contexts including all parents
 *
 * @global object
 * @param int $contextlevel empty means all
 * @param bool $buildpaths update paths and depths
 * @return void
 */
function create_contexts($contextlevel=null, $buildpaths=true) {
    global $DB;

    //make sure system context exists
    $syscontext = get_system_context(false);

    if (empty($contextlevel) or $contextlevel == CONTEXT_COURSECAT
                             or $contextlevel == CONTEXT_COURSE
                             or $contextlevel == CONTEXT_MODULE
                             or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_COURSECAT.", cc.id
                  FROM {course}_categories cc
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE cc.id = cx.instanceid AND cx.contextlevel=".CONTEXT_COURSECAT.")";
        $DB->execute($sql);

    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_COURSE
                             or $contextlevel == CONTEXT_MODULE
                             or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_COURSE.", c.id
                  FROM {course} c
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE c.id = cx.instanceid AND cx.contextlevel=".CONTEXT_COURSE.")";
        $DB->execute($sql);

    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_MODULE
                             or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_MODULE.", cm.id
                  FROM {course}_modules cm
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE cm.id = cx.instanceid AND cx.contextlevel=".CONTEXT_MODULE.")";
        $DB->execute($sql);
    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_USER
                             or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_USER.", u.id
                  FROM {user} u
                 WHERE u.deleted=0
                   AND NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE u.id = cx.instanceid AND cx.contextlevel=".CONTEXT_USER.")";
        $DB->execute($sql);

    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_BLOCK.", bi.id
                  FROM {block_instances} bi
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE bi.id = cx.instanceid AND cx.contextlevel=".CONTEXT_BLOCK.")";
        $DB->execute($sql);
    }

    if ($buildpaths) {
        build_context_path(false);
    }
}

/**
 * Remove stale context records
 *
 * @global object
 * @return bool
 */
function cleanup_contexts() {
    global $DB;

    $sql = "  SELECT c.contextlevel,
                     c.instanceid AS instanceid
                FROM {context} c
                LEFT OUTER JOIN {course}_categories t
                     ON c.instanceid = t.id
               WHERE t.id IS NULL AND c.contextlevel = ".CONTEXT_COURSECAT."
            UNION
              SELECT c.contextlevel,
                     c.instanceid
                FROM {context} c
                LEFT OUTER JOIN {course} t
                     ON c.instanceid = t.id
               WHERE t.id IS NULL AND c.contextlevel = ".CONTEXT_COURSE."
            UNION
              SELECT c.contextlevel,
                     c.instanceid
                FROM {context} c
                LEFT OUTER JOIN {course}_modules t
                     ON c.instanceid = t.id
               WHERE t.id IS NULL AND c.contextlevel = ".CONTEXT_MODULE."
            UNION
              SELECT c.contextlevel,
                     c.instanceid
                FROM {context} c
                LEFT OUTER JOIN {user} t
                     ON c.instanceid = t.id
               WHERE t.id IS NULL AND c.contextlevel = ".CONTEXT_USER."
            UNION
              SELECT c.contextlevel,
                     c.instanceid
                FROM {context} c
                LEFT OUTER JOIN {block_instances} t
                     ON c.instanceid = t.id
               WHERE t.id IS NULL AND c.contextlevel = ".CONTEXT_BLOCK."
           ";
    if ($rs = $DB->get_recordset_sql($sql)) {
        $DB->begin_sql();
        $ok = true;
        foreach ($rs as $ctx) {
            if (!delete_context($ctx->contextlevel, $ctx->instanceid)) {
                $ok = false;
                break;
            }
        }
        $rs->close();
        if ($ok) {
            $DB->commit_sql();
            return true;
        } else {
            $DB->rollback_sql();
            return false;
        }
    }
    return true;
}

/**
 * Preloads all contexts relating to a course: course, modules. Block contexts
 * are no longer loaded here. The contexts for all the blocks on the current
 * page are now efficiently loaded by {@link block_manager::load_blocks()}.
 *
 * @param int $courseid Course ID
 * @return void
 */
function preload_course_contexts($courseid) {
    global $DB, $ACCESSLIB_PRIVATE;

    // Users can call this multiple times without doing any harm
    global $ACCESSLIB_PRIVATE;
    if (array_key_exists($courseid, $ACCESSLIB_PRIVATE->preloadedcourses)) {
        return;
    }

    $params = array($courseid, $courseid, $courseid);
    $sql = "SELECT x.instanceid, x.id, x.contextlevel, x.path, x.depth
              FROM {course_modules} cm
              JOIN {context} x ON x.instanceid=cm.id
             WHERE cm.course=? AND x.contextlevel=".CONTEXT_MODULE."

         UNION ALL

            SELECT x.instanceid, x.id, x.contextlevel, x.path, x.depth
              FROM {context} x
             WHERE x.instanceid=? AND x.contextlevel=".CONTEXT_COURSE."";

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $context) {
        cache_context($context);
    }
    $rs->close();
    $ACCESSLIB_PRIVATE->preloadedcourses[$courseid] = true;
}

/**
 * Get the context instance as an object. This function will create the
 * context instance if it does not exist yet.
 *
 * @todo Remove code branch from previous fix MDL-9016 which is no longer needed
 *
 * @param integer $level The context level, for example CONTEXT_COURSE, or CONTEXT_MODULE.
 * @param integer $instance The instance id. For $level = CONTEXT_COURSE, this would be $course->id,
 *      for $level = CONTEXT_MODULE, this would be $cm->id. And so on. Defaults to 0
 * @return object The context object.
 */
function get_context_instance($contextlevel, $instance=0) {

    global $DB, $ACCESSLIB_PRIVATE;
    static $allowed_contexts = array(CONTEXT_SYSTEM, CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE, CONTEXT_BLOCK);

    if ($contextlevel === 'clearcache') {
        // TODO: Remove for v2.0
        // No longer needed, but we'll catch it to avoid erroring out on custom code.
        // This used to be a fix for MDL-9016
        // "Restoring into existing course, deleting first
        //  deletes context and doesn't recreate it"
        return false;
    }

/// System context has special cache
    if ($contextlevel == CONTEXT_SYSTEM) {
        return get_system_context();
    }

/// check allowed context levels
    if (!in_array($contextlevel, $allowed_contexts)) {
        // fatal error, code must be fixed - probably typo or switched parameters
        print_error('invalidcourselevel');
    }

    if (!is_array($instance)) {
    /// Check the cache
        if (isset($ACCESSLIB_PRIVATE->contexts[$contextlevel][$instance])) {  // Already cached
            return $ACCESSLIB_PRIVATE->contexts[$contextlevel][$instance];
        }

    /// Get it from the database, or create it
        if (!$context = $DB->get_record('context', array('contextlevel'=>$contextlevel, 'instanceid'=>$instance))) {
            $context = create_context($contextlevel, $instance);
        }

    /// Only add to cache if context isn't empty.
        if (!empty($context)) {
            cache_context($context);
        }

        return $context;
    }


/// ok, somebody wants to load several contexts to save some db queries ;-)
    $instances = $instance;
    $result = array();

    foreach ($instances as $key=>$instance) {
    /// Check the cache first
        if (isset($ACCESSLIB_PRIVATE->contexts[$contextlevel][$instance])) {  // Already cached
            $result[$instance] = $ACCESSLIB_PRIVATE->contexts[$contextlevel][$instance];
            unset($instances[$key]);
            continue;
        }
    }

    if ($instances) {
        list($instanceids, $params) = $DB->get_in_or_equal($instances, SQL_PARAMS_QM);
        array_unshift($params, $contextlevel);
        $sql = "SELECT instanceid, id, contextlevel, path, depth
                  FROM {context}
                 WHERE contextlevel=? AND instanceid $instanceids";

        if (!$contexts = $DB->get_records_sql($sql, $params)) {
            $contexts = array();
        }

        foreach ($instances as $instance) {
            if (isset($contexts[$instance])) {
                $context = $contexts[$instance];
            } else {
                $context = create_context($contextlevel, $instance);
            }

            if (!empty($context)) {
                cache_context($context);
            }

            $result[$instance] = $context;
        }
    }

    return $result;
}


/**
 * Get a context instance as an object, from a given context id.
 *
 * @global object
 * @global object
 * @param mixed $id a context id or array of ids.
 * @return mixed object, array of the context object, or false.
 */
function get_context_instance_by_id($id) {
    global $DB, $ACCESSLIB_PRIVATE;

    if ($id == SYSCONTEXTID) {
        return get_system_context();
    }

    if (isset($ACCESSLIB_PRIVATE->contextsbyid[$id])) {  // Already cached
        return $ACCESSLIB_PRIVATE->contextsbyid[$id];
    }

    if ($context = $DB->get_record('context', array('id'=>$id))) {
        cache_context($context);
        return $context;
    }

    return false;
}


/**
 * Get the local override (if any) for a given capability in a role in a context
 *
 * @global object
 * @param int $roleid
 * @param int $contextid
 * @param string $capability
 */
function get_local_override($roleid, $contextid, $capability) {
    global $DB;
    return $DB->get_record('role_capabilities', array('roleid'=>$roleid, 'capability'=>$capability, 'contextid'=>$contextid));
}



//////////////////////////////////////
//    DB TABLE RELATED FUNCTIONS    //
//////////////////////////////////////

/**
 * function that creates a role
 *
 * @global object
 * @param string $name role name
 * @param string $shortname role short name
 * @param string $description role description
 * @param string $legacy optional legacy capability
 * @return mixed id or dml_exception
 */
function create_role($name, $shortname, $description, $legacy='') {
    global $DB;

    // Get the system context.
    $context = get_context_instance(CONTEXT_SYSTEM);

    // Insert the role record.
    $role = new object();
    $role->name        = $name;
    $role->shortname   = $shortname;
    $role->description = $description;

    //find free sortorder number
    $role->sortorder = $DB->get_field('role', 'MAX(sortorder) + 1', array());
    if (empty($role->sortorder)) {
        $role->sortorder = 1;
    }
    $id = $DB->insert_record('role', $role);

    if ($legacy) {
        assign_capability($legacy, CAP_ALLOW, $id, $context->id);
    }

    return $id;
}

/**
 * Function that deletes a role and cleanups up after it
 *
 * @global object
 * @global object
 * @param int $roleid id of role to delete
 * @return bool
 */
function delete_role($roleid) {
    global $CFG, $DB;
    $success = true;

// mdl 10149, check if this is the last active admin role
// if we make the admin role not deletable then this part can go

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    if ($role = $DB->get_record('role', array('id'=>$roleid))) {
        if ($DB->record_exists('role_capabilities', array('contextid'=>$systemcontext->id, 'roleid'=>$roleid, 'capability'=>'moodle/site:doanything'))) {
            // deleting an admin role
            $status = false;
            if ($adminroles = get_roles_with_capability('moodle/site:doanything', CAP_ALLOW, $systemcontext)) {
                foreach ($adminroles as $adminrole) {
                    if ($adminrole->id != $roleid) {
                        // some other admin role
                        if ($DB->record_exists('role_assignments', array('roleid'=>$adminrole->id, 'contextid'=>$systemcontext->id))) {
                            // found another admin role with at least 1 user assigned
                            $status = true;
                            break;
                        }
                    }
                }
            }
            if ($status !== true) {
                print_error('cannotdeleterolenoadmin', 'access');
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
        $DB->delete_records('role_capabilities',   array('roleid'=>$roleid));
        $DB->delete_records('role_allow_assign',   array('roleid'=>$roleid));
        $DB->delete_records('role_allow_assign',   array('allowassign'=>$roleid));
        $DB->delete_records('role_allow_override', array('roleid'=>$roleid));
        $DB->delete_records('role_allow_override', array('allowoverride'=>$roleid));
        $DB->delete_records('role_names',          array('roleid'=>$roleid));
        $DB->delete_records('role_context_levels', array('roleid'=>$roleid));
    }

// finally delete the role itself
    // get this before the name is gone for logging
    $rolename = $DB->get_field('role', 'name', array('id'=>$roleid));

    if ($success and !$DB->delete_records('role', array('id'=>$roleid))) {
        debugging("Could not delete role record with ID $roleid!");
        $success = false;
    }

    if ($success) {
        add_to_log(SITEID, 'role', 'delete', 'admin/roles/action=delete&roleid='.$roleid, $rolename, '');
    }

    return $success;
}

/**
 * Function to write context specific overrides, or default capabilities.
 *
 * @global object
 * @global object
 * @param string module string name
 * @param string capability string name
 * @param int contextid context id
 * @param int roleid role id
 * @param int permission int 1,-1 or -1000 should not be writing if permission is 0
 * @return bool
 */
function assign_capability($capability, $permission, $roleid, $contextid, $overwrite=false) {

    global $USER, $DB;

    if (empty($permission) || $permission == CAP_INHERIT) { // if permission is not set
        unassign_capability($capability, $roleid, $contextid);
        return true;
    }

    $existing = $DB->get_record('role_capabilities', array('contextid'=>$contextid, 'roleid'=>$roleid, 'capability'=>$capability));

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
        return $DB->update_record('role_capabilities', $cap);
    } else {
        $c = $DB->get_record('context', array('id'=>$contextid));
        return $DB->insert_record('role_capabilities', $cap);
    }
}

/**
 * Unassign a capability from a role.
 *
 * @global object
 * @param int $roleid the role id
 * @param string $capability the name of the capability
 * @return boolean success or failure
 */
function unassign_capability($capability, $roleid, $contextid=NULL) {
    global $DB;

    if (isset($contextid)) {
        // delete from context rel, if this is the last override in this context
        $status = $DB->delete_records('role_capabilities', array('capability'=>$capability,
                'roleid'=>$roleid, 'contextid'=>$contextid));
    } else {
        $status = $DB->delete_records('role_capabilities', array('capability'=>$capability,
                'roleid'=>$roleid));
    }
    return $status;
}


/**
 * Get the roles that have a given capability assigned to it
 * Get the roles that have a given capability assigned to it. This function
 * does not resolve the actual permission of the capability. It just checks
 * for assignment only.
 *
 * @global object
 * @global object
 * @param string $capability - capability name (string)
 * @param null $permission - optional, the permission defined for this capability
 *                      either CAP_ALLOW, CAP_PREVENT or CAP_PROHIBIT. Defaults to NULL
 * @param object $contect
 * @return mixed array or role objects
 */
function get_roles_with_capability($capability, $permission=NULL, $context='') {

    global $CFG, $DB;

    $params = array();

    if ($context) {
        if ($contexts = get_parent_contexts($context)) {
            $listofcontexts = '('.implode(',', $contexts).')';
        } else {
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
            $listofcontexts = '('.$sitecontext->id.')'; // must be site
        }
        $contextstr = "AND (rc.contextid = ? OR  rc.contextid IN $listofcontexts)";
        $params[] = $context->id;
    } else {
        $contextstr = '';
    }

    $selectroles = "SELECT r.*
                      FROM {role} r,
                           {role_capabilities} rc
                     WHERE rc.capability = ?
                           AND rc.roleid = r.id $contextstr";

    array_unshift($params, $capability);

    if (isset($permission)) {
        $selectroles .= " AND rc.permission = ?";
        $params[] = $permission;
    }
    return $DB->get_records_sql($selectroles, $params);
}


/**
 * This function makes a role-assignment (a role for a user or group in a particular context)
 *
 * @global object
 * @global object
 * @global object
 * @param int $roleid the role of the id
 * @param int $userid userid
 * @param int $groupid group id
 * @param int $contextid id of the context
 * @param int $timestart time this assignment becomes effective defaults to 0
 * @param int $timeend time this assignemnt ceases to be effective defaults to 0
 * @param int $hidden defaults to 0
 * @param string $enrol defaults to 'manual'
 * @param string $timemodified defaults to ''
 * @return int new id of the assigment
 */
function role_assign($roleid, $userid, $groupid, $contextid, $timestart=0, $timeend=0, $hidden=0, $enrol='manual',$timemodified='') {
    global $USER, $CFG, $DB;

/// Do some data validation

    if (empty($roleid)) {
        debugging('Role ID not provided');
        return false;
    }

    if (empty($userid) && empty($groupid)) {
        debugging('Either userid or groupid must be provided');
        return false;
    }

    if ($userid && !$DB->record_exists('user', array('id'=>$userid))) {
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
        $ra = $DB->get_record('role_assignments', array('roleid'=>$roleid, 'contextid'=>$context->id, 'userid'=>$userid));
    } else {
        $ra = $DB->get_record('role_assignments', array('roleid'=>$roleid, 'contextid'=>$context->id, 'groupid'=>$groupid));
    }

    if (empty($ra)) {             // Create a new entry
        $ra = new object();
        $ra->roleid = $roleid;
        $ra->contextid = $context->id;
        $ra->userid = $userid;
        $ra->hidden = $hidden;
        $ra->enrol = $enrol;
    /// Always round timestart downto 100 secs to help DBs to use their own caching algorithms
    /// by repeating queries with the same exact parameters in a 100 secs time window
        $ra->timestart = round($timestart, -2);
        $ra->timeend = $timeend;
        $ra->timemodified = $timemodified;
        $ra->modifierid = empty($USER->id) ? 0 : $USER->id;

        $ra->id = $DB->insert_record('role_assignments', $ra);

    } else {                      // We already have one, just update it
        $ra->id = $ra->id;
        $ra->hidden = $hidden;
        $ra->enrol = $enrol;
    /// Always round timestart downto 100 secs to help DBs to use their own caching algorithms
    /// by repeating queries with the same exact parameters in a 100 secs time window
        $ra->timestart = round($timestart, -2);
        $ra->timeend = $timeend;
        $ra->timemodified = $timemodified;
        $ra->modifierid = empty($USER->id) ? 0 : $USER->id;

        $DB->update_record('role_assignments', $ra);
    }

/// mark context as dirty - modules might use has_capability() in xxx_role_assing()
/// again expensive, but needed
    mark_context_dirty($context->path);

    if (!empty($USER->id) && $USER->id == $userid) {
/// If the user is the current user, then do full reload of capabilities too.
        load_all_capabilities();
    }

/// Ask all the modules if anything needs to be done for this user
    $mods = get_plugin_list('mod');
    foreach ($mods as $mod => $moddir) {
        include_once($moddir.'/lib.php');
        $functionname = $mod.'_role_assign';
        if (function_exists($functionname)) {
            $functionname($userid, $context, $roleid);
        }
    }

    /// now handle metacourse role assignments if in course context
    if ($context->contextlevel == CONTEXT_COURSE) {
        if ($parents = $DB->get_records('course_meta', array('child_course'=>$context->instanceid))) {
            foreach ($parents as $parent) {
                sync_metacourse($parent->parent_course);
            }
        }
    }

    events_trigger('role_assigned', $ra);

    return $ra->id;
}


/**
 * Deletes one or more role assignments.   You must specify at least one parameter.
 *
 * @global object
 * @global object
 * @global object
 * @param int $roleid defaults to 0
 * @param int $userid defaults to 0
 * @param int $groupid defaults to 0
 * @param int $contextid defaults to 0
 * @param mixed $enrol unassign only if enrolment type matches, NULL means anything. Defaults to NULL
 * @return boolean success or failure
 */
function role_unassign($roleid=0, $userid=0, $groupid=0, $contextid=0, $enrol=NULL) {

    global $USER, $CFG, $DB;
    require_once($CFG->dirroot.'/group/lib.php');

    $success = true;

    $args = array('roleid', 'userid', 'groupid', 'contextid');
    $select = array();
    $params = array();

    foreach ($args as $arg) {
        if ($$arg) {
            $select[] = "$arg = ?";
            $params[] = $$arg;
        }
    }
    if (!empty($enrol)) {
        $select[] = "enrol=?";
        $params[] = $enrol;
    }

    if ($select) {
        if ($ras = $DB->get_records_select('role_assignments', implode(' AND ', $select), $params)) {
            $mods = get_plugin_list('mod');
            foreach($ras as $ra) {
                $fireevent = false;
                /// infinite loop protection when deleting recursively
                if (!$ra = $DB->get_record('role_assignments', array('id'=>$ra->id))) {
                    continue;
                }
                if ($DB->delete_records('role_assignments', array('id'=>$ra->id))) {
                    $fireevent = true;
                } else {
                    $success = false;
                }

                if (!$context = get_context_instance_by_id($ra->contextid)) {
                    // strange error, not much to do
                    continue;
                }

                /* mark contexts as dirty here, because we need the refreshed
                 * caps bellow to delete group membership and user_lastaccess!
                 * and yes, this is very expensive for bulk operations :-(
                 */
                mark_context_dirty($context->path);

                /// If the user is the current user, then do full reload of capabilities too.
                if (!empty($USER->id) && $USER->id == $ra->userid) {
                    load_all_capabilities();
                }

                /// Ask all the modules if anything needs to be done for this user
                foreach ($mods as $mod=>$moddir) {
                    include_once($moddir.'/lib.php');
                    $functionname = $mod.'_role_unassign';
                    if (function_exists($functionname)) {
                        $functionname($ra->userid, $context); // watch out, $context might be NULL if something goes wrong
                    }
                }

                /// now handle metacourse role unassigment and removing from goups if in course context
                if ($context->contextlevel == CONTEXT_COURSE) {

                    // cleanup leftover course groups/subscriptions etc when user has
                    // no capability to view course
                    // this may be slow, but this is the proper way of doing it
                    if (!has_capability('moodle/course:view', $context, $ra->userid)) {
                        // remove from groups
                        groups_delete_group_members($context->instanceid, $ra->userid);

                        // delete lastaccess records
                        $DB->delete_records('user_lastaccess', array('userid'=>$ra->userid, 'courseid'=>$context->instanceid));
                    }

                    //unassign roles in metacourses if needed
                    if ($parents = $DB->get_records('course_meta', array('child_course'=>$context->instanceid))) {
                        foreach ($parents as $parent) {
                            sync_metacourse($parent->parent_course);
                        }
                    }
                }

                if ($fireevent) {
                    events_trigger('role_unassigned', $ra);
                }
            }
        }
    }

    return $success;
}

/**
 * Enrol someone without using the default role in a course
 *
 * A convenience function to take care of the common case where you
 * just want to enrol someone using the default role into a course
 *
 * @param object $course
 * @param object $user
 * @param string $enrol the plugin used to do this enrolment
 * @return bool
 */
function enrol_into_course($course, $user, $enrol) {

    $timestart = time();
    // remove time part from the timestamp and keep only the date part
    $timestart = make_timestamp(date('Y', $timestart), date('m', $timestart), date('d', $timestart), 0, 0, 0);
    if ($course->enrolperiod) {
        $timeend = $timestart + $course->enrolperiod;
    } else {
        $timeend = 0;
    }

    if ($role = get_default_course_role($course)) {

        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if (!role_assign($role->id, $user->id, 0, $context->id, $timestart, $timeend, 0, $enrol)) {
            return false;
        }

        // force accessdata refresh for users visiting this context...
        mark_context_dirty($context->path);

        email_welcome_message_to_user($course, $user);

        add_to_log($course->id, 'course', 'enrol',
                'view.php?id='.$course->id, $course->id);

        return true;
    }

    return false;
}

/**
 * Loads the capability definitions for the component (from file).
 *
 * Loads the capability definitions for the component (from file). If no
 * capabilities are defined for the component, we simply return an empty array.
 *
 * @global object
 * @param string $component full plugin name, examples: 'moodle', 'mod_forum'
 * @return array array of capabilities
 */
function load_capability_def($component) {
    $defpath = get_component_directory($component).'/db/access.php';

    $capabilities = array();
    if (file_exists($defpath)) {
        require($defpath);
        if (!empty(${$component.'_capabilities'})) {
            // legacy capability array name
            // since 2.0 we prefer $capabilities instead - it is easier to use and matches db/* files
            $capabilities = ${$component.'_capabilities'};
        }
    }

    return $capabilities;
}


/**
 * Gets the capabilities that have been cached in the database for this component.
 * @param string $component - examples: 'moodle', 'mod_forum'
 * @return array array of capabilities
 */
function get_cached_capabilities($component='moodle') {
    global $DB;
    return $DB->get_records('capabilities', array('component'=>$component));
}

/**
 * Returns default capabilities for given legacy role type.
 * @param string $legacyrole legacy role name
 * @return array
 */
function get_default_capabilities($legacyrole) {
    global $DB;
    $allcaps = $DB->get_records('capabilities');
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
 * @param int @roleid
 */
function reset_role_capabilities($roleid) {
    global $DB;

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

    $DB->delete_records('role_capabilities', array('roleid'=>$roleid));
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
 * @global object
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean true if success, exception in case of any problems
 */
function update_capabilities($component='moodle') {
    global $DB, $OUTPUT;

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
                    $DB->update_record('capabilities', $updatecap);
                }

                if (!array_key_exists('contextlevel', $filecaps[$cachedcap->name])) {
                    $filecaps[$cachedcap->name]['contextlevel'] = 0; // no context level defined
                }
                if ($cachedcap->contextlevel != $filecaps[$cachedcap->name]['contextlevel']) {
                    $updatecap = new object();
                    $updatecap->id = $cachedcap->id;
                    $updatecap->contextlevel = $filecaps[$cachedcap->name]['contextlevel'];
                    $DB->update_record('capabilities', $updatecap);
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

        $DB->insert_record('capabilities', $capability, false);

        if (isset($capdef['clonepermissionsfrom']) && in_array($capdef['clonepermissionsfrom'], $storedcaps)){
            if ($rolecapabilities = $DB->get_records('role_capabilities', array('capability'=>$capdef['clonepermissionsfrom']))){
                foreach ($rolecapabilities as $rolecapability){
                    //assign_capability will update rather than insert if capability exists
                    if (!assign_capability($capname, $rolecapability->permission,
                                            $rolecapability->roleid, $rolecapability->contextid, true)){
                         echo $OUTPUT->notification('Could not clone capabilities for '.$capname);
                    }
                }
            }
        // Do we need to assign the new capabilities to roles that have the
        // legacy capabilities moodle/legacy:* as well?
        // we ignore legacy key if we have cloned permissions
        } else if (isset($capdef['legacy']) && is_array($capdef['legacy']) &&
                    !assign_legacy_capabilities($capname, $capdef['legacy'])) {
            echo $OUTPUT->notification('Could not assign legacy capabilities for '.$capname);
        }
    }
    // Are there any capabilities that have been removed from the file
    // definition that we need to delete from the stored capabilities and
    // role assignments?
    capabilities_cleanup($component, $filecaps);

    // reset static caches
    is_valid_capability('reset', false);

    return true;
}


/**
 * Deletes cached capabilities that are no longer needed by the component.
 * Also unassigns these capabilities from any roles that have them.
 *
 * @global object
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @param array $newcapdef array of the new capability definitions that will be
 *                     compared with the cached capabilities
 * @return int number of deprecated capabilities that have been removed
 */
function capabilities_cleanup($component, $newcapdef=NULL) {
    global $DB;

    $removedcount = 0;

    if ($cachedcaps = get_cached_capabilities($component)) {
        foreach ($cachedcaps as $cachedcap) {
            if (empty($newcapdef) ||
                        array_key_exists($cachedcap->name, $newcapdef) === false) {

                // Remove from capabilities cache.
                $DB->delete_records('capabilities', array('name'=>$cachedcap->name));
                $removedcount++;
                // Delete from roles.
                if ($roles = get_roles_with_capability($cachedcap->name)) {
                    foreach($roles as $role) {
                        if (!unassign_capability($cachedcap->name, $role->id)) {
                            print_error('cannotunassigncap', 'error', '', (object)array('cap'=>$cachedcap->name, 'role'=>$role->name));
                        }
                    }
                }
            } // End if.
        }
    }
    return $removedcount;
}



//////////////////
// UI FUNCTIONS //
//////////////////

/**
 * @param integer $contextlevel $context->context level. One of the CONTEXT_... constants.
 * @return string the name for this type of context.
 */
function get_contextlevel_name($contextlevel) {
    static $strcontextlevels = null;
    if (is_null($strcontextlevels)) {
        $strcontextlevels = array(
            CONTEXT_SYSTEM => get_string('coresystem'),
            CONTEXT_USER => get_string('user'),
            CONTEXT_COURSECAT => get_string('category'),
            CONTEXT_COURSE => get_string('course'),
            CONTEXT_MODULE => get_string('activitymodule'),
            CONTEXT_BLOCK => get_string('block')
        );
    }
    return $strcontextlevels[$contextlevel];
}

/**
 * Prints human readable context identifier.
 *
 * @global object
 * @param object $context the context.
 * @param boolean $withprefix whether to prefix the name of the context with the
 *      type of context, e.g. User, Course, Forum, etc.
 * @param boolean $short whether to user the short name of the thing. Only applies
 *      to course contexts
 * @return string the human readable context name.
 */
function print_context_name($context, $withprefix = true, $short = false) {
    global $DB;

    $name = '';
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:
            $name = get_string('coresystem');
            break;

        case CONTEXT_USER:
            if ($user = $DB->get_record('user', array('id'=>$context->instanceid))) {
                if ($withprefix){
                    $name = get_string('user').': ';
                }
                $name .= fullname($user);
            }
            break;

        case CONTEXT_COURSECAT:
            if ($category = $DB->get_record('course_categories', array('id'=>$context->instanceid))) {
                if ($withprefix){
                    $name = get_string('category').': ';
                }
                $name .=format_string($category->name);
            }
            break;

        case CONTEXT_COURSE:
            if ($context->instanceid == SITEID) {
                $name = get_string('frontpage', 'admin');
            } else {
                if ($course = $DB->get_record('course', array('id'=>$context->instanceid))) {
                    if ($withprefix){
                        $name = get_string('course').': ';
                    }
                    if ($short){
                        $name .= format_string($course->shortname);
                    } else {
                        $name .= format_string($course->fullname);
                   }
                }
            }
            break;

        case CONTEXT_MODULE:
            if ($cm = $DB->get_record_sql('SELECT cm.*, md.name AS modname FROM {course_modules} cm ' .
                    'JOIN {modules} md ON md.id = cm.module WHERE cm.id = ?', array($context->instanceid))) {
                if ($mod = $DB->get_record($cm->modname, array('id' => $cm->instance))) {
                        if ($withprefix){
                        $name = get_string('modulename', $cm->modname).': ';
                        }
                        $name .= $mod->name;
                    }
                }
            break;

        case CONTEXT_BLOCK:
            if ($blockinstance = $DB->get_record('block_instances', array('id'=>$context->instanceid))) {
                global $CFG;
                require_once("$CFG->dirroot/blocks/moodleblock.class.php");
                require_once("$CFG->dirroot/blocks/$blockinstance->blockname/block_$blockinstance->blockname.php");
                $blockname = "block_$blockinstance->blockname";
                if ($blockobject = new $blockname()) {
                    if ($withprefix){
                        $name = get_string('block').': ';
                    }
                    $name .= $blockobject->title;
                }
            }
            break;

        default:
            print_error('unknowncontext');
            return false;
    }

    return $name;
}

/**
 * Get a URL for a context, if there is a natural one. For example, for
 * CONTEXT_COURSE, this is the course page. For CONTEXT_USER it is the
 * user profile page.
 *
 * First three parameters as for
 *
 * @global object
 * @global object
 * @global object
 * @param object $context the context.
 * @return string a suitable URL, or blank.
 */
function get_context_url($context) {
    global $CFG, $COURSE, $DB;

    $url = '';
    switch ($context->contextlevel) {
        case CONTEXT_USER:
            $url = $CFG->wwwroot . '/user/view.php?id=' . $context->instanceid;
            if ($COURSE->id != SITEID) {
                $url .= '&amp;courseid=' . $COURSE->id;
            }
            break;

        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            $url = $CFG->wwwroot . '/course/category.php?id=' . $context->instanceid;
            break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            if ($context->instanceid == SITEID) {
                $url = $CFG->wwwroot . '/';
            } else {
                $url = $CFG->wwwroot . '/course/view.php?id=' . $context->instanceid;
            }
            break;

        case CONTEXT_MODULE: // 1 to 1 to course
            if ($modname = $DB->get_field_sql('SELECT md.name AS modname FROM {course_modules} cm ' .
                    'JOIN {modules} md ON md.id = cm.module WHERE cm.id = ?', array($context->instanceid))) {
                $url = $CFG->wwwroot . '/mod/' . $modname . '/view.php?id=' . $context->instanceid;
            }
            break;

        case CONTEXT_SYSTEM:
        case CONTEXT_BLOCK:
        default:
            $url = '';
    }

    return $url;
}

/**
 * Returns an array of all the known types of risk
 * The array keys can be used, for example as CSS class names, or in calls to
 * print_risk_icon. The values are the corresponding RISK_ constants.
 *
 * @return array all the known types of risk.
 */
function get_all_risks() {
    return array(
        'riskmanagetrust' => RISK_MANAGETRUST,
        'riskconfig' => RISK_CONFIG,
        'riskxss' => RISK_XSS,
        'riskpersonal' => RISK_PERSONAL,
        'riskspam' => RISK_SPAM,
        'riskdataloss' => RISK_DATALOSS,
    );
}

/**
 * Return a link to moodle docs for a given capability name
 *
 * @global object
 * @param object $capability a capability - a row from the mdl_capabilities table.
 * @return string the human-readable capability name as a link to Moodle Docs.
 */
function get_capability_docs_link($capability) {
    global $CFG;
    $url = get_docs_url('Capabilities/' . $capability->name);
    return '<a onclick="this.target=\'docspopup\'" href="' . $url . '">' . get_capability_string($capability->name) . '</a>';
}

/**
 * Extracts the relevant capabilities given a contextid.
 * All case based, example an instance of forum context.
 * Will fetch all forum related capabilities, while course contexts
 * Will fetch all capabilities
 *
 * capabilities
 * `name` varchar(150) NOT NULL,
 * `captype` varchar(50) NOT NULL,
 * `contextlevel` int(10) NOT NULL,
 * `component` varchar(100) NOT NULL,
 *
 * @global object
 * @global object
 * @param object context
 * @return array
 */
function fetch_context_capabilities($context) {
    global $DB, $CFG;

    $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

    $params = array();

    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM: // all
            $SQL = "SELECT *
                      FROM {capabilities}";
        break;

        case CONTEXT_USER:
            $extracaps = array('moodle/grade:viewall');
            list($extra, $params) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap0');
            $SQL = "SELECT *
                      FROM {capabilities}
                     WHERE contextlevel = ".CONTEXT_USER."
                           OR name $extra";
        break;

        case CONTEXT_COURSECAT: // course category context and bellow
            $SQL = "SELECT *
                      FROM {capabilities}
                     WHERE contextlevel IN (".CONTEXT_COURSECAT.",".CONTEXT_COURSE.",".CONTEXT_MODULE.",".CONTEXT_BLOCK.")";
        break;

        case CONTEXT_COURSE: // course context and bellow
            $SQL = "SELECT *
                      FROM {capabilities}
                     WHERE contextlevel IN (".CONTEXT_COURSE.",".CONTEXT_MODULE.",".CONTEXT_BLOCK.")";
        break;

        case CONTEXT_MODULE: // mod caps
            $cm = $DB->get_record('course_modules', array('id'=>$context->instanceid));
            $module = $DB->get_record('modules', array('id'=>$cm->module));

            $modfile = "$CFG->dirroot/mod/$module->name/lib.php";
            if (file_exists($modfile)) {
                include_once($modfile);
                $modfunction = $module->name.'_get_extra_capabilities';
                if (function_exists($modfunction)) {
                    $extracaps = $modfunction();
                }
            }
            if(empty($extracaps)) {
                $extracaps = array();
            }

            // All modules allow viewhiddenactivities. This is so you can hide
            // the module then override to allow specific roles to see it.
            // The actual check is in course page so not module-specific
            $extracaps[]="moodle/course:viewhiddenactivities";
            list($extra, $params) = $DB->get_in_or_equal(
                $extracaps, SQL_PARAMS_NAMED, 'cap0');
            $extra = "OR name $extra";

            $SQL = "SELECT *
                      FROM {capabilities}
                     WHERE (contextlevel = ".CONTEXT_MODULE."
                           AND component = :component)
                           $extra";
            $params['component'] = "mod/$module->name";
        break;

        case CONTEXT_BLOCK: // block caps
            $bi = $DB->get_record('block_instances', array('id' => $context->instanceid));

            $extra = '';
            $extracaps = block_method_result($bi->blockname, 'get_extra_capabilities');
            if ($extracaps) {
                list($extra, $params) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap0');
                $extra = "OR name $extra";
            }

            $SQL = "SELECT *
                      FROM {capabilities}
                     WHERE (contextlevel = ".CONTEXT_BLOCK."
                           AND component = :component)
                           $extra";
            $params['component'] = 'block/' . $bi->blockname;
        break;

        default:
        return false;
    }

    if (!$records = $DB->get_records_sql($SQL.' '.$sort, $params)) {
        $records = array();
    }

    return $records;
}


/**
 * This function pulls out all the resolved capabilities (overrides and
 * defaults) of a role used in capability overrides in contexts at a given
 * context.
 *
 * @global object
 * @param obj $context
 * @param int $roleid
 * @param string $cap capability, optional, defaults to ''
 * @param bool if set to true, resolve till this level, else stop at immediate parent level
 * @return array
 */
function role_context_capabilities($roleid, $context, $cap='') {
    global $DB;

    $contexts = get_parent_contexts($context);
    $contexts[] = $context->id;
    $contexts = '('.implode(',', $contexts).')';

    $params = array($roleid);

    if ($cap) {
        $search = " AND rc.capability = ? ";
        $params[] = $cap;
    } else {
        $search = '';
    }

    $sql = "SELECT rc.*
              FROM {role_capabilities} rc, {context} c
             WHERE rc.contextid in $contexts
                   AND rc.roleid = ?
                   AND rc.contextid = c.id $search
          ORDER BY c.contextlevel DESC, rc.capability DESC";

    $capabilities = array();

    if ($records = $DB->get_records_sql($sql, $params)) {
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
 *
 * @param object $context
 * @param bool $capability optional, defaults to false
 * @return array
 */
function get_parent_contexts($context, $includeself = false) {

    if ($context->path == '') {
        return array();
    }

    $parentcontexts = substr($context->path, 1); // kill leading slash
    $parentcontexts = explode('/', $parentcontexts);
    if (!$includeself) {
        array_pop($parentcontexts); // and remove its own id
    }

    return array_reverse($parentcontexts);
}

/**
 * Return the id of the parent of this context, or false if there is no parent (only happens if this
 * is the site context.)
 *
 * @param object $context
 * @return integer the id of the parent context.
 */
function get_parent_contextid($context) {
    $parentcontexts = get_parent_contexts($context);
    if (count($parentcontexts) == 0) {
        return false;
    }
    return array_shift($parentcontexts);
}

/**
 * Check if contect is the front page context or a context inside it
 *
 * Returns true if this context is the front page context, or a context inside it,
 * otherwise false.
 *
 * @param object $context a context object.
 * @return bool
 */
function is_inside_frontpage($context) {
    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    return strpos($context->path . '/', $frontpagecontext->path . '/') === 0;
}

/**
 * Runs get_records select on context table and returns the result
 * Does get_records_select on the context table, and returns the results ordered
 * by contextlevel, and then the natural sort order within each level.
 * for the purpose of $select, you need to know that the context table has been
 * aliased to ctx, so for example, you can call get_sorted_contexts('ctx.depth = 3');
 *
 * @global object
 * @param string $select the contents of the WHERE clause. Remember to do ctx.fieldname.
 * @param array $params any parameters required by $select.
 * @return array the requested context records.
 */
function get_sorted_contexts($select, $params = array()) {
    global $DB;
    if ($select) {
        $select = 'WHERE ' . $select;
    }
    return $DB->get_records_sql("
            SELECT ctx.*
            FROM {context} ctx
            LEFT JOIN {user} u ON ctx.contextlevel = " . CONTEXT_USER . " AND u.id = ctx.instanceid
            LEFT JOIN {course_categories} cat ON ctx.contextlevel = " . CONTEXT_COURSECAT . " AND cat.id = ctx.instanceid
            LEFT JOIN {course} c ON ctx.contextlevel = " . CONTEXT_COURSE . " AND c.id = ctx.instanceid
            LEFT JOIN {course_modules} cm ON ctx.contextlevel = " . CONTEXT_MODULE . " AND cm.id = ctx.instanceid
            LEFT JOIN {block_instances} bi ON ctx.contextlevel = " . CONTEXT_BLOCK . " AND bi.id = ctx.instanceid
            $select
            ORDER BY ctx.contextlevel, bi.defaultregion, COALESCE(cat.sortorder, c.sortorder, cm.section, bi.defaultweight), u.lastname, u.firstname, cm.id
            ", $params);
}

/**
 * Recursive function which, given a context, find all its children context ids.
 *
 * When called for a course context, it will return the modules and blocks
 * displayed in the course page.
 *
 * For course category contexts it will return categories and courses. It will
 * NOT recurse into courses, nor return blocks on the category pages. If you
 * want to do that, call it on the returned courses.
 *
 * If called on a course context it _will_ populate the cache with the appropriate
 * contexts ;-)
 *
 * @param object $context.
 * @return array Array of child records
 */
function get_child_contexts($context) {

    global $DB, $ACCESSLIB_PRIVATE;

    // We *MUST* populate the context_cache as the callers
    // will probably ask for the full record anyway soon after
    // soon after calling us ;-)

    switch ($context->contextlevel) {

        case CONTEXT_BLOCK:
            // No children.
            return array();
        break;

        case CONTEXT_MODULE:
            // Find
            // - blocks under this context path.
            $sql = " SELECT ctx.*
                       FROM {context} ctx
                      WHERE ctx.path LIKE ?
                            AND ctx.contextlevel = ".CONTEXT_BLOCK;
            $params = array("{$context->path}/%", $context->instanceid);
            $records = $DB->get_recordset_sql($sql, $params);
            foreach ($records as $rec) {
                cache_context($rec);
            }
            return $records;
            break;

        case CONTEXT_COURSE:
            // Find
            // - modules and blocks under this context path.
            $sql = " SELECT ctx.*
                       FROM {context} ctx
                      WHERE ctx.path LIKE ?
                            AND ctx.contextlevel IN (".CONTEXT_MODULE.",".CONTEXT_BLOCK.")";
            $params = array("{$context->path}/%", $context->instanceid);
            $records = $DB->get_recordset_sql($sql, $params);
            foreach ($records as $rec) {
                cache_context($rec);
            }
            return $records;
        break;

        case CONTEXT_COURSECAT:
            // Find
            // - categories
            // - courses
            $sql = " SELECT ctx.*
                       FROM {context} ctx
                      WHERE ctx.path LIKE ?
                            AND ctx.contextlevel IN (".CONTEXT_COURSECAT.",".CONTEXT_COURSE.")";
            $params = array("{$context->path}/%");
            $records = $DB->get_recordset_sql($sql, $params);
            foreach ($records as $rec) {
                cache_context($rec);
            }
            return $records;
        break;

        case CONTEXT_USER:
            // Find
            // - blocks under this context path.
            $sql = " SELECT ctx.*
                       FROM {context} ctx
                      WHERE ctx.path LIKE ?
                            AND ctx.contextlevel = ".CONTEXT_BLOCK;
            $params = array("{$context->path}/%", $context->instanceid);
            $records = $DB->get_recordset_sql($sql, $params);
            foreach ($records as $rec) {
                cache_context($rec);
            }
            return $records;
            break;
            break;

        case CONTEXT_SYSTEM:
            // Just get all the contexts except for CONTEXT_SYSTEM level
            // and hope we don't OOM in the process - don't cache
            $sql = "SELECT c.*
                      FROM {context} c
                     WHERE contextlevel != ".CONTEXT_SYSTEM;

            return $DB->get_records_sql($sql);
        break;

        default:
            print_error('unknowcontext', '', '', $context->contextlevel);
            return false;
    }
}


/**
 * Gets a string for sql calls, searching for stuff in this context or above
 *
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
 * Verifies if given capability installed.
 *
 * @global object
 * @param string $capabilityname
 * @param bool $cached
 * @return book true if capability exists
 */
function is_valid_capability($capabilityname, $cached = true) {
    global $ACCESSLIB_PRIVATE; // one request per page only

    if (is_null($ACCESSLIB_PRIVATE->capabilitynames) or !$cached) {
        global $DB;
        $ACCESSLIB_PRIVATE->capabilitynames = $DB->get_records_menu('capabilities', null, '', 'name, 1');
    }

    return array_key_exists($capabilityname, $ACCESSLIB_PRIVATE->capabilitynames);
}

/**
 * Returns the human-readable, translated version of the capability.
 * Basically a big switch statement.
 *
 * @param string $capabilityname e.g. mod/choice:readresponses
 * @return string
 */
function get_capability_string($capabilityname) {

    // Typical capabilityname is mod/choice:readresponses

    $names = split('/', $capabilityname);
    $stringname = $names[1];                 // choice:readresponses
    $components = split(':', $stringname);
    $componentname = $components[0];               // choice

    switch ($names[0]) {
        case 'report':
            $string = get_string($stringname, 'report_'.$componentname);
        break;

        case 'mod':
            $string = get_string($stringname, $componentname);
        break;

        case 'block':
            $string = get_string($stringname, 'block_'.$componentname);
        break;

        case 'moodle':
            if ($componentname == 'local') {
                $string = get_string($stringname, 'local');
            } else {
                $string = get_string($stringname, 'role');
            }
        break;

        case 'enrol':
            $string = get_string($stringname, 'enrol_'.$componentname);
        break;

        case 'format':
            $string = get_string($stringname, 'format_'.$componentname);
        break;

        case 'format':
            $string = get_string($stringname, 'editor_'.$componentname);
        break;

        case 'gradeexport':
            $string = get_string($stringname, 'gradeexport_'.$componentname);
        break;

        case 'gradeimport':
            $string = get_string($stringname, 'gradeimport_'.$componentname);
        break;

        case 'gradereport':
            $string = get_string($stringname, 'gradereport_'.$componentname);
        break;

        case 'coursereport':
            $string = get_string($stringname, 'coursereport_'.$componentname);
        break;

        case 'quizreport':
            $string = get_string($stringname, 'quiz_'.$componentname);
        break;

        case 'repository':
            $string = get_string($stringname, 'repository_'.$componentname);
        break;

        case 'local':
            $string = get_string($stringname, 'local_'.$componentname);
        break;

        case 'webservice':
            $string = get_string($stringname, 'webservice_'.$componentname);
        break;

        default:
            $string = get_string($stringname);
        break;

    }
    return $string;
}


/**
 * This gets the mod/block/course/core etc strings.
 *
 * @param string $component
 * @param int $contextlevel
 * @return string|bool String is success, false if failed
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
            } else if (preg_match('|^local|', $component)) {
                $langname = str_replace('/', '_', $component);
                $string = get_string('local');
            } else if (preg_match('|^report/|', $component)) {
                $string = get_string('reports');
            } else {
                $string = get_string('coresystem');
            }
        break;

        case CONTEXT_USER:
            $string = get_string('users');
        break;

        case CONTEXT_COURSECAT:
            $string = get_string('categories');
        break;

        case CONTEXT_COURSE:
            if (preg_match('|^gradeimport/|', $component)
                || preg_match('|^gradeexport/|', $component)
                || preg_match('|^gradereport/|', $component)) {
                $string = get_string('gradebook', 'admin');
            } else if (preg_match('|^coursereport/|', $component)) {
                $string = get_string('coursereports');
            } else if (preg_match('|^webservice/|', $component)) {
                $string = get_string('webservices', 'webservice');
            } else {
                $string = get_string('course');
            }
        break;

        case CONTEXT_MODULE:
            if (preg_match('|^quiz_([a-z_]*)|', $component, $matches)){
                $langname = 'quiz_'.$matches[1];
                $string = get_string($matches[1].':componentname', $langname);
            } else {
                $string = get_string('modulename', preg_replace('#(\w+_)#', '', basename($component)));
            }
        break;

        case CONTEXT_BLOCK:
            if( $component == 'moodle' ){
                $string = get_string('block');
            }else{
                $string = get_string('blockname', basename($component));
            }
        break;

        default:
            print_error('unknowncontext');
        return false;

    }
    return $string;
}

/**
 * Gets the list of roles assigned to this context and up (parents)
 *
 * set $view to true when roles are pulled for display only
 * this is so that we can filter roles with no visible
 * assignment, for example, you might want to "hide" all
 * course creators when browsing the course participants
 * list.
 *
 * @global object
 * @param object $context
 * @param bool $view
 * @return array
 */
function get_roles_used_in_context($context, $view = false) {
    global $DB;

    // filter for roles with all hidden assignments
    // no need to return when only pulling roles for reviewing
    // e.g. participants page.
    $hiddensql = ($view && !has_capability('moodle/role:viewhiddenassigns', $context))? ' AND ra.hidden = 0 ':'';
    $contextlist = get_related_contexts_string($context);

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder
              FROM {role_assignments} ra, {role} r
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
                   $hiddensql
          ORDER BY r.sortorder ASC";

    return $DB->get_records_sql($sql);
}

/**
 * This function is used to print roles column in user profile page.
 *
 * @global object
 * @global object
 * @global object
 * @param int $userid
 * @param object $context
 * @param bool $view
 * @return string
 */
function get_user_roles_in_context($userid, $context, $view=true){
    global $CFG, $DB,$USER;

    $rolestring = '';
    $sql = "SELECT *
            FROM {role_assignments} ra, {role} r
           WHERE ra.userid = ? and ra.contextid = ? and ra.roleid = r.id";
    $params = array($userid, $context->id);
    $rolenames = array();
    if ($roles = $DB->get_records_sql($sql, $params)) {
        foreach ($roles as $userrole) {
            // MDL-12544, if we are in view mode and current user has no capability to view hidden assignment, skip it
            if ($userrole->hidden && $view && !has_capability('moodle/role:viewhiddenassigns', $context)) {
                continue;
            }
            $rolenames[$userrole->roleid] = $userrole->name;
        }

        $rolenames = role_fix_names($rolenames, $context);   // Substitute aliases

        foreach ($rolenames as $roleid => $rolename) {
            $rolenames[$roleid] = '<a href="'.$CFG->wwwroot.'/user/index.php?contextid='.$context->id.'&amp;roleid='.$roleid.'">'.$rolename.'</a>';
        }
        $rolestring = implode(',', $rolenames);
    }
    return $rolestring;
}


/**
 * Checks if a user can override capabilities of a particular role in this context
 *
 * @deprecated As of version 2.0
 * @todo not needed anymore, remove in 2.0
 * @param object $context
 * @param int $targetroleid the id of the role you want to override
 * @return boolean
 */
function user_can_override($context, $targetroleid) {

// TODO: not needed anymore, remove in 2.0

    global $DB;
    // first check if user has override capability
    // if not return false;
    if (!has_capability('moodle/role:override', $context)) {
        return false;
    }
    // pull out all active roles of this user from this context(or above)
    if ($userroles = get_user_roles($context)) {
        foreach ($userroles as $userrole) {
            // if any in the role_allow_override table, then it's ok
            if ($DB->get_record('role_allow_override', array('roleid'=>$userrole->roleid, 'allowoverride'=>$targetroleid))) {
                return true;
            }
        }
    }

    return false;

}

/**
 * Checks if a user can assign users to a particular role in this context
 *
 * @global object
 * @param object $context
 * @param int $targetroleid - the id of the role you want to assign users to
 * @return boolean
 */
function user_can_assign($context, $targetroleid) {
    global $DB;

    // first check if user has override capability
    // if not return false;
    if (!has_capability('moodle/role:assign', $context)) {
        return false;
    }
    // pull out all active roles of this user from this context(or above)
    if ($userroles = get_user_roles($context)) {
        foreach ($userroles as $userrole) {
            // if any in the role_allow_override table, then it's ok
            if ($DB->get_record('role_allow_assign', array('roleid'=>$userrole->roleid, 'allowassign'=>$targetroleid))) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Returns all site roles in correct sort order.
 *
 * @global object
 * @return array
 */
function get_all_roles() {
    global $DB;
    return $DB->get_records('role', null, 'sortorder ASC');
}

/**
 * gets all the user roles assigned in this context, or higher contexts
 * this is mainly used when checking if a user can assign a role, or overriding a role
 * i.e. we need to know what this user holds, in order to verify against allow_assign and
 * allow_override tables
 *
 * set $view to true when roles are pulled for display only
 * this is so that we can filter roles with no visible
 * assignment, for example, you might want to "hide" all
 * course creators when browsing the course participants
 * list.
 *
 * @global object
 * @global object
 * @param object $context
 * @param int $userid
 * @param bool $checkparentcontexts defaults to true
 * @param string $order defaults to 'c.contextlevel DESC, r.sortorder ASC'
 * @param bool $view
 * @return array
 */
function get_user_roles($context, $userid=0, $checkparentcontexts=true, $order='c.contextlevel DESC, r.sortorder ASC', $view=false) {
    global $USER, $DB;

    if (empty($userid)) {
        if (empty($USER->id)) {
            return array();
        }
        $userid = $USER->id;
    }
    // set up hidden sql
    $hiddensql = ($view && !has_capability('moodle/role:viewhiddenassigns', $context)) ? "AND ra.hidden = 0" : "";

    if ($checkparentcontexts) {
        $contextids = get_parent_contexts($context);
    } else {
        $contextids = array();
    }
    $contextids[] = $context->id;

    list($contextids, $params) = $DB->get_in_or_equal($contextids, SQL_PARAMS_QM);

    array_unshift($params, $userid);

    $sql = "SELECT ra.*, r.name, r.shortname
              FROM {role_assignments} ra, {role} r, {context} c
             WHERE ra.userid = ?
                   AND ra.roleid = r.id
                   AND ra.contextid = c.id
                   AND ra.contextid $contextids
                   $hiddensql
          ORDER BY $order";

    return $DB->get_records_sql($sql ,$params);
}

/**
 * Creates a record in the role_allow_override table
 *
 * @global object
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
 * @return int id or false
 */
function allow_override($sroleid, $troleid) {
    global $DB;

    $record = new object();
    $record->roleid        = $sroleid;
    $record->allowoverride = $troleid;
    $DB->insert_record('role_allow_override', $record);
}

/**
 * Creates a record in the role_allow_assign table
 *
 * @global object
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
 * @return int id or false
 */
function allow_assign($fromroleid, $targetroleid) {
    global $DB;

    $record = new object;
    $record->roleid      = $fromroleid;
    $record->allowassign = $targetroleid;
    $DB->insert_record('role_allow_assign', $record);
}

/**
 * Creates a record in the role_allow_switch table
 *
 * @global object
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
 * @return int id or false
 */
function allow_switch($fromroleid, $targetroleid) {
    global $DB;

    $record = new object;
    $record->roleid      = $fromroleid;
    $record->allowswitch = $targetroleid;
    $DB->insert_record('role_allow_switch', $record);
}

/**
 * Gets a list of roles that this user can assign in this context
 *
 * @global object
 * @global object
 * @param object $context the context.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @param bool $withusercounts if true, count the number of users with each role.
 * @return array if $withusercounts is false, then an array $roleid => $rolename.
 *      if $withusercounts is true, returns a list of three arrays,
 *      $rolenames, $rolecounts, and $nameswithcounts.
 */
function get_assignable_roles($context, $rolenamedisplay = ROLENAME_ALIAS, $withusercounts = false) {
    global $USER, $DB;

    if (!has_capability('moodle/role:assign', $context)) {
        if ($withusercounts) {
            return array(array(), array(), array());
        } else {
            return array();
        }
    }

    $parents = get_parent_contexts($context);
    $parents[] = $context->id;
    $contexts = implode(',' , $parents);

    $params = array();
    $extrafields = '';
    if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
        $extrafields .= ', ro.shortname';
    }

    if ($withusercounts) {
        $extrafields = ', (SELECT count(u.id)
                             FROM {role_assignments} cra JOIN {user} u ON cra.userid = u.id
                            WHERE cra.roleid = ro.id AND cra.contextid = :conid AND u.deleted = 0
                          ) AS usercount';
        $params['conid'] = $context->id;
    }

    $raafrom  = ", {role_allow_assign} raa";
    $raawhere = "AND raa.roleid = ra.roleid AND r.id = raa.allowassign";
    if (has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
        // show all roles allowed in this context to admins
        $raafrom  = "";
        $raawhere = "";
    }

    $params['userid'] = $USER->id;
    $params['contextlevel'] = $context->contextlevel;
    $roles = $DB->get_records_sql("
             SELECT ro.id, ro.name$extrafields
               FROM {role} ro
               JOIN (SELECT DISTINCT r.id
                       FROM {role} r,
                            {role_assignments} ra $raafrom
                      WHERE ra.userid = :userid AND ra.contextid IN ($contexts)
                            $raawhere
                    ) inline_view ON ro.id = inline_view.id
               JOIN {role_context_levels} rcl ON ro.id = rcl.roleid
              WHERE rcl.contextlevel = :contextlevel
           ORDER BY ro.sortorder ASC", $params);

    $rolenames = array();
    foreach ($roles as $role) {
        $rolenames[$role->id] = $role->name;
        if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
            $rolenames[$role->id] .= ' (' . $role->shortname . ')';
    }
    }
    if ($rolenamedisplay != ROLENAME_ORIGINALANDSHORT) {
        $rolenames = role_fix_names($rolenames, $context, $rolenamedisplay);
    }

    if (!$withusercounts) {
        return $rolenames;
    }

    $rolecounts = array();
    $nameswithcounts = array();
    foreach ($roles as $role) {
        $nameswithcounts[$role->id] = $rolenames[$role->id] . ' (' . $roles[$role->id]->usercount . ')';
        $rolecounts[$role->id] = $roles[$role->id]->usercount;
    }
    return array($rolenames, $rolecounts, $nameswithcounts);
}

/**
 * Gets a list of roles that this user can switch to in a context
 *
 * Gets a list of roles that this user can switch to in a context, for the switchrole menu.
 * This function just process the contents of the role_allow_switch table. You also need to
 * test the moodle/role:switchroles to see if the user is allowed to switch in the first place.
 *
 * @global object
 * @global object
 * @param object $context a context.
 * @return array an array $roleid => $rolename.
 */
function get_switchable_roles($context) {
    global $USER, $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $params = array();
    $extrajoins = '';
    $extrawhere = '';
    if (!has_capability('moodle/site:doanything', $systemcontext)) {
        // Admins are allowed to switch to any role with 'moodle/course:view' in the
        // role definition, and without 'moodle/site:doanything' anywhere.
        // Others are subject to the additional constraint that the switch-to role must be allowed by
        // 'role_allow_switch' for some role they have assigned in this context or any parent.
        $parents = get_parent_contexts($context);
        $parents[] = $context->id;
        $contexts = implode(',' , $parents);

        $extrajoins = "JOIN {role_allow_switch} ras ON ras.allowswitch = rc.roleid
        JOIN {role_assignments} ra ON ra.roleid = ras.roleid";
        $extrawhere = "AND ra.userid = :userid
              AND ra.contextid IN ($contexts)";
        $params['userid'] = $USER->id;
    }

    $query = "
        SELECT r.id, r.name
        FROM (
            SELECT DISTINCT rc.roleid
            FROM {role_capabilities} rc
            $extrajoins
            WHERE rc.capability = :viewcap
              AND rc.permission = " . CAP_ALLOW . "
              AND rc.contextid = :syscontextid
              $extrawhere
              AND NOT EXISTS (
                 SELECT 1 FROM {role_capabilities} irc WHERE irc.roleid = rc.roleid AND
                     irc.capability = :anythingcap AND irc.permission = " . CAP_ALLOW . ")
        ) idlist
        JOIN {role} r ON r.id = idlist.roleid
        ORDER BY r.sortorder";
    $params['syscontextid'] = $systemcontext->id;
    $params['viewcap'] = 'moodle/course:view';
    $params['anythingcap'] = 'moodle/site:doanything';

    $rolenames = $DB->get_records_sql_menu($query, $params);
    return role_fix_names($rolenames, $context, ROLENAME_ALIAS);
}

/**
 * Get an array of role ids that might possibly be the target of a switchrole.
 * Our policy is that you cannot switch to a role with moodle/site:doanything
 * and you can only switch to a role with moodle/course:view. This method returns
 * a list of those role ids.
 *
 * @global object
 * @return array an array whose keys are the allowed role ids.
 */
function get_allowed_switchable_roles() {
    global $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $query = "
        SELECT DISTINCT rc.roleid, 1
        FROM {role_capabilities} rc
        WHERE rc.capability = :viewcap
          AND rc.permission = " . CAP_ALLOW . "
          AND rc.contextid = :syscontextid
          AND NOT EXISTS (
             SELECT 1 FROM {role_capabilities} irc WHERE irc.roleid = rc.roleid AND
                 irc.capability = :anythingcap AND irc.permission = " . CAP_ALLOW . ")";
    $params = array('syscontextid' => $systemcontext->id,
            'viewcap' => 'moodle/course:view', 'anythingcap' => 'moodle/site:doanything');

    return $DB->get_records_sql_menu($query, $params);
}

/**
 * Gets a list of roles that this user can override in this context.
 *
 * @global object
 * @global object
 * @param object $context the context.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @param bool $withcounts if true, count the number of overrides that are set for each role.
 * @return array if $withcounts is false, then an array $roleid => $rolename.
 *      if $withusercounts is true, returns a list of three arrays,
 *      $rolenames, $rolecounts, and $nameswithcounts.
 */
function get_overridable_roles($context, $rolenamedisplay = ROLENAME_ALIAS, $withcounts = false) {
    global $USER, $DB;

    if (!has_any_capability(array('moodle/role:safeoverride', 'moodle/role:override'), $context)) {
        if ($withcounts) {
            return array(array(), array(), array());
        } else {
            return array();
        }
    }

    $parents = get_parent_contexts($context);
    $parents[] = $context->id;
    $contexts = implode(',' , $parents);

    $params = array();
    $extrafields = '';
    if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
        $extrafields .= ', ro.shortname';
    }

    $params['userid'] = $USER->id;
    if ($withcounts) {
        $extrafields = ', (SELECT count(rc.id) FROM {role_capabilities} rc
                WHERE rc.roleid = ro.id AND rc.contextid = :conid) AS overridecount';
        $params['conid'] = $context->id;
    }

    if (has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) {
        // show all roles to admins
        $roles = $DB->get_records_sql("
            SELECT ro.id, ro.name$extrafields
              FROM {role} ro
          ORDER BY ro.sortorder ASC", $params);

    } else {
        $roles = $DB->get_records_sql("
            SELECT ro.id, ro.name$extrafields
              FROM {role} ro
              JOIN (SELECT DISTINCT r.id
                      FROM {role} r
                      JOIN {role_allow_override} rao ON r.id = rao.allowoverride
                      JOIN {role_assignments} ra ON rao.roleid = ra.roleid
                     WHERE ra.userid = :userid AND ra.contextid IN ($contexts)
                   ) inline_view ON ro.id = inline_view.id
          ORDER BY ro.sortorder ASC", $params);
    }

    $rolenames = array();
    foreach ($roles as $role) {
        $rolenames[$role->id] = $role->name;
        if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
            $rolenames[$role->id] .= ' (' . $role->shortname . ')';
        }
    }
    if ($rolenamedisplay != ROLENAME_ORIGINALANDSHORT) {
        $rolenames = role_fix_names($rolenames, $context, $rolenamedisplay);
    }

    if (!$withcounts) {
        return $rolenames;
}

    $rolecounts = array();
    $nameswithcounts = array();
    foreach ($roles as $role) {
        $nameswithcounts[$role->id] = $rolenames[$role->id] . ' (' . $roles[$role->id]->overridecount . ')';
        $rolecounts[$role->id] = $roles[$role->id]->overridecount;
    }
    return array($rolenames, $rolecounts, $nameswithcounts);
}

/**
 * @global object
 * @param integer $roleid the id of a role.
 * @return array list of the context levels at which this role may be assigned.
 */
function get_role_contextlevels($roleid) {
    global $DB;
    return $DB->get_records_menu('role_context_levels', array('roleid' => $roleid),
            'contextlevel', 'id,contextlevel');
}

/**
 * @global object
 * @param integer $contextlevel a contextlevel.
 * @return array list of role ids that are assignable at this context level.
 */
function get_roles_for_contextlevels($contextlevel) {
    global $DB;
    return $DB->get_records_menu('role_context_levels', array('contextlevel' => $contextlevel),
            '', 'id,roleid');
}

/**
 * @param string $roleid one of the legacy role types - that is, one of the keys
 *      from the array returned by get_legacy_roles();
 * @return array list of the context levels at which this type of role may be assigned by default.
 */
function get_default_contextlevels($roletype) {
    static $defaults = array(
        'admin' => array(CONTEXT_SYSTEM),
        'coursecreator' => array(CONTEXT_SYSTEM, CONTEXT_COURSECAT),
        'editingteacher' => array(CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE),
        'teacher' => array(CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE),
        'student' => array(CONTEXT_COURSE, CONTEXT_MODULE),
        'guest' => array(),
        'user' => array()
    );
    if (isset($defaults[$roletype])) {
        return $defaults[$roletype];
    } else {
        return array();
    }
}

/**
 * Set the context levels at which a particular role can be assigned.
 * Throws exceptions in case of error.
 *
 * @global object
 * @param integer $roleid the id of a role.
 * @param array $contextlevels the context levels at which this role should be assignable.
 */
function set_role_contextlevels($roleid, array $contextlevels) {
    global $DB;
    $DB->delete_records('role_context_levels', array('roleid' => $roleid));
    $rcl = new stdClass;
    $rcl->roleid = $roleid;
    foreach ($contextlevels as $level) {
        $rcl->contextlevel = $level;
        $DB->insert_record('role_context_levels', $rcl, false, true);
    }
}

/**
 *  Returns a role object that is the default role for new enrolments
 *  in a given course
 *
 * @global object
 * @global object
 *  @param object $course
 *  @return object returns a role or NULL if none set
 */
function get_default_course_role($course) {
    global $DB, $CFG;

/// First let's take the default role the course may have
    if (!empty($course->defaultrole)) {
        if ($role = $DB->get_record('role', array('id'=>$course->defaultrole))) {
            return $role;
        }
    }

/// Otherwise the site setting should tell us
    if ($CFG->defaultcourseroleid) {
        if ($role = $DB->get_record('role', array('id'=>$CFG->defaultcourseroleid))) {
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
 * Who has this capability in this context?
 *
 * This can be a very expensive call - use sparingly and keep
 * the results if you are going to need them again soon.
 *
 * Note if $fields is empty this function attempts to get u.*
 * which can get rather large - and has a serious perf impact
 * on some DBs.
 *
 * @global object
 * @global object
 * @param object $context
 * @param string $capability - string capability, or an array of capabilities, in which
 *               case users having any of those capabilities will be returned.
 *               For performance reasons, you are advised to put the capability
 *               that the user is most likely to have first.
 * @param string $fields - fields to be pulled. The user table is aliased to 'u'. u.id MUST be included.
 * @param string $sort - the sort order. Default is lastaccess time.
 * @param mixed $limitfrom - number of records to skip (offset)
 * @param mixed $limitnum - number of records to fetch
 * @param mixed $groups - single group or array of groups - only return
 *               users who are in one of these group(s).
 * @param mixed $exceptions - list of users to exclude, comma separated or array
 * @param bool $view - set to true when roles are pulled for display only
 *               this is so that we can filter roles with no visible
 *               assignment, for example, you might want to "hide" all
 *               course creators when browsing the course participants
 *               list.
 * @param bool $useviewallgroups if $groups is set the return users who
 *               have capability both $capability and moodle/site:accessallgroups
 *               in this context, as well as users who have $capability and who are
 *               in $groups.
 * @return mixed
 */
function get_users_by_capability($context, $capability, $fields='', $sort='',
        $limitfrom='', $limitnum='', $groups='', $exceptions='', $doanything=true,
        $view=false, $useviewallgroups=false) {
    global $CFG, $DB;

    $ctxids = substr($context->path, 1); // kill leading slash
    $ctxids = str_replace('/', ',', $ctxids);

    // Context is the frontpage
    $isfrontpage = false;
    $iscoursepage = false; // coursepage other than fp
    if ($context->contextlevel == CONTEXT_COURSE) {
        if ($context->instanceid == SITEID) {
            $isfrontpage = true;
        } else {
            $iscoursepage = true;
        }
    }

    // What roles/rolecaps are interesting?
    if (is_array($capability)) {
        $caps = $capability;
    } else {
        $caps = array($capability);
    }
    if ($doanything === true) {
        $caps[] = 'moodle/site:doanything';
        $doanything_join='';
        $doanything_cond='';

    } else {
        // This is an outer join against
        // admin-ish roleids. Any row that succeeds
        // in JOINing here ends up removed from
        // the resultset. This means we remove
        // rolecaps from roles that also have
        // 'doanything' capabilities.
        $doanything_join="LEFT OUTER JOIN (
                              SELECT DISTINCT rc.roleid
                              FROM {role_capabilities} rc
                              WHERE rc.capability=:capany
                                    AND rc.permission=".CAP_ALLOW."
                                    AND rc.contextid IN ($ctxids)
                          ) dar
                             ON rc.roleid=dar.roleid";
        $doanything_cond="AND dar.roleid IS NULL";
    }

    // fetch all capability records - we'll walk several
    // times over them, and should be a small set

    $negperm = false; // has any negative (<0) permission?
    $roleids = array();

    list($capstest, $params) = $DB->get_in_or_equal($caps, SQL_PARAMS_NAMED, 'cap0');
    $params['capany'] = 'moodle/site:doanything';

    $sql = "SELECT rc.id, rc.roleid, rc.permission, rc.capability,
                   ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
              FROM {role_capabilities} rc
              JOIN {context} ctx on rc.contextid = ctx.id
    $doanything_join
             WHERE rc.capability $capstest AND ctx.id IN ($ctxids)
                   $doanything_cond
          ORDER BY rc.roleid ASC, ctx.depth ASC";

    if ($capdefs = $DB->get_records_sql($sql, $params)) {
        foreach ($capdefs AS $rcid=>$rc) {
            $roleids[] = (int)$rc->roleid;
            if ($rc->permission < 0) {
                $negperm = true;
            }
        }
    }

    $roleids = array_unique($roleids);

    if (count($roleids)===0) { // noone here!
        return array();
    }

    // is the default role interesting? does it have
    // a relevant rolecap? (we use this a lot later)
    if (isset($CFG->defaultuserroleid) and in_array((int)$CFG->defaultuserroleid, $roleids, true)) {
        $defaultroleinteresting = true;
    } else {
        $defaultroleinteresting = false;
    }

    // is the default role interesting? does it have
    // a relevant rolecap? (we use this a lot later)
    if (($isfrontpage or is_inside_frontpage($context)) and !empty($CFG->defaultfrontpageroleid) and in_array((int)$CFG->defaultfrontpageroleid, $roleids, true)) {
        if (!empty($CFG->fullusersbycapabilityonfrontpage)) {
            // new in 1.9.6 - full support for defaultfrontpagerole MDL-19039
            $frontpageroleinteresting = true;
        } else {
            // old style 1.9.0-1.9.5 - much faster + fewer negative override problems on frontpage
            $frontpageroleinteresting = ($context->contextlevel == CONTEXT_COURSE);
        }
    } else {
        $frontpageroleinteresting = false;
    }

    //
    // Prepare query clauses
    //
    $wherecond = array();

    // Non-deleted users. We never return deleted users.
    $wherecond['nondeleted'] = 'u.deleted = 0';

    /// Groups
    if ($groups) {
        if (is_array($groups)) {
            $grouptest = 'gm.groupid IN (' . implode(',', $groups) . ')';
        } else {
            $grouptest = 'gm.groupid = ' . (int)$groups;
        }
        $grouptest = 'ra.userid IN (SELECT userid FROM ' .
            '{groups_members} gm WHERE ' . $grouptest . ')';

        if ($useviewallgroups) {
            $viewallgroupsusers = get_users_by_capability($context,
                    'moodle/site:accessallgroups', 'u.id, u.id', '', '', '', '', $exceptions);
            $wherecond['groups'] =  '('. $grouptest . ' OR ra.userid IN (' .
                                    implode(',', array_keys($viewallgroupsusers)) . '))';
        } else {
            $wherecond['groups'] =  '(' . $grouptest .')';
        }
    }

    /// User exceptions
    if (!empty($exceptions)) {
        if (is_array($exceptions)) {
            $exceptions = implode(',', $exceptions);
        }
        $wherecond['userexceptions'] = ' u.id NOT IN ('.$exceptions.')';
    }

    /// Set up hidden role-assignments sql
    if ($view && !has_capability('moodle/role:viewhiddenassigns', $context)) {
        $condhiddenra = 'AND ra.hidden = 0 ';
        $sscondhiddenra = 'AND ssra.hidden = 0 ';
    } else {
        $condhiddenra = '';
        $sscondhiddenra = '';
    }

    // Collect WHERE conditions
    $where = implode(' AND ', array_values($wherecond));
    if ($where != '') {
        $where = 'WHERE ' . $where;
    }

    /// Set up default fields
    if (empty($fields)) {
        if ($iscoursepage) {
            $fields = 'u.*, ul.timeaccess as lastaccess';
        } else {
            $fields = 'u.*';
        }
    } else {
        if (debugging('', DEBUG_DEVELOPER) && strpos($fields, 'u.*') === false &&
                strpos($fields, 'u.id') === false) {
            debugging('u.id must be included in the list of fields passed to get_users_by_capability.', DEBUG_DEVELOPER);
        }
    }

    /// Set up default sort
    if (empty($sort)) { // default to course lastaccess or just lastaccess
        if ($iscoursepage) {
            $sort = 'ul.timeaccess';
        } else {
            $sort = 'u.lastaccess';
        }
    }
    $sortby = $sort ? " ORDER BY $sort " : '';

    // User lastaccess JOIN
    if ((strpos($sort, 'ul.timeaccess') === FALSE) and (strpos($fields, 'ul.timeaccess') === FALSE)) {  // user_lastaccess is not required MDL-13810
        $uljoin = '';
    } else {
        $uljoin = "LEFT OUTER JOIN {user_lastaccess} ul
                         ON (ul.userid = u.id AND ul.courseid = {$context->instanceid})";
    }

    //
    // Simple cases - No negative permissions means we can take shortcuts
    //
    if (!$negperm) {

        // at the frontpage, and all site users have it - easy!
        if ($frontpageroleinteresting) {
            return $DB->get_records_sql("SELECT $fields
                                           FROM {user} u
                                          WHERE u.deleted = 0
                                       ORDER BY $sort",
                                       $limitfrom, $limitnum);
        }

        // all site users have it, anyway
        // TODO: NOT ALWAYS!  Check this case because this gets run for cases like this:
        // 1) Default role has the permission for a module thing like mod/choice:choose
        // 2) We are checking for an activity module context in a course
        // 3) Thus all users are returned even though course:view is also required
        if ($defaultroleinteresting) {
            $sql = "SELECT $fields
                      FROM {user} u
                   $uljoin
                    $where
                  ORDER BY $sort";
            return $DB->get_records_sql($sql, null, $limitfrom, $limitnum);
        }

        /// Simple SQL assuming no negative rolecaps.
        /// We use a subselect to grab the role assignments
        /// ensuring only one row per user -- even if they
        /// have many "relevant" role assignments.
        $select = " SELECT $fields";
        $from   = " FROM {user} u
                    JOIN (SELECT DISTINCT ssra.userid
                          FROM {role_assignments} ssra
                          WHERE ssra.contextid IN ($ctxids)
                                AND ssra.roleid IN (".implode(',',$roleids) .")
                                $sscondhiddenra
                          ) ra ON ra.userid = u.id
                    $uljoin ";
        return $DB->get_records_sql($select.$from.$where.$sortby, null, $limitfrom, $limitnum);
    }

    //
    // If there are any negative rolecaps, we need to
    // work through a subselect that will bring several rows
    // per user (one per RA).
    // Since we cannot do the job in pure SQL (not without SQL stored
    // procedures anyway), we end up tied to processing the data in PHP
    // all the way down to pagination.
    //
    // In some cases, this will mean bringing across a ton of data --
    // when paginating, we have to walk the permisisons of all the rows
    // in the _previous_ pages to get the pagination correct in the case
    // of users that end up not having the permission - this removed.
    //

    // Prepare the role permissions datastructure for fast lookups
    $roleperms = array(); // each role cap and depth
    foreach ($capdefs AS $rcid=>$rc) {

        $rid       = (int)$rc->roleid;
        $perm      = (int)$rc->permission;
        $rcdepth   = (int)$rc->ctxdepth;
        if (!isset($roleperms[$rc->capability][$rid])) {
            $roleperms[$rc->capability][$rid] = (object)array('perm'  => $perm,
                                                              'rcdepth' => $rcdepth);
        } else {
            if ($roleperms[$rc->capability][$rid]->perm == CAP_PROHIBIT) {
                continue;
            }
            // override - as we are going
            // from general to local perms
            // (as per the ORDER BY...depth ASC above)
            // and local perms win...
            $roleperms[$rc->capability][$rid] = (object)array('perm'  => $perm,
                                                              'rcdepth' => $rcdepth);
        }

    }

    if ($context->contextlevel == CONTEXT_SYSTEM
        || $isfrontpage
        || $defaultroleinteresting) {

        // Handle system / sitecourse / defaultrole-with-perhaps-neg-overrides
        // with a SELECT FROM user LEFT OUTER JOIN against ra -
        // This is expensive on the SQL and PHP sides -
        // moves a ton of data across the wire.
        $ss = "SELECT u.id as userid, ra.roleid,
                      ctx.depth
               FROM {user} u
               LEFT OUTER JOIN {role_assignments} ra
                 ON (ra.userid = u.id
                     AND ra.contextid IN ($ctxids)
                     AND ra.roleid IN (".implode(',',$roleids) .")
                     $condhiddenra)
               LEFT OUTER JOIN {context} ctx
                 ON ra.contextid=ctx.id
               WHERE u.deleted=0";
    } else {
        // "Normal complex case" - the rolecaps we are after will
        // be defined in a role assignment somewhere.
        $ss = "SELECT ra.userid as userid, ra.roleid,
                      ctx.depth
               FROM {role_assignments} ra
               JOIN {context} ctx
                 ON ra.contextid=ctx.id
               WHERE ra.contextid IN ($ctxids)
                     $condhiddenra
                     AND ra.roleid IN (".implode(',',$roleids) .")";
    }

    $select = "SELECT $fields ,ra.roleid, ra.depth ";
    $from   = "FROM ($ss) ra
               JOIN {user} u
                 ON ra.userid=u.id
               $uljoin ";

    // Each user's entries MUST come clustered together
    // and RAs ordered in depth DESC - the role/cap resolution
    // code depends on this.
    $sort .= ' , ra.userid ASC, ra.depth DESC';
    $sortby .= ' , ra.userid ASC, ra.depth DESC ';

    if (!$rs = $DB->get_recordset_sql($select.$from.$where.$sortby)) {
        return array();
    }

    //
    // Process the user accounts+RAs, folding repeats together...
    //
    // The processing for this recordset is tricky - to fold
    // the role/perms of users with multiple role-assignments
    // correctly while still processing one-row-at-a-time
    // we need to add a few additional 'private' fields to
    // the results array - so we can treat the rows as a
    // state machine to track the cap/perms and at what RA-depth
    // and RC-depth they were defined.
    //
    // So what we do here is:
    // - loop over rows, checking pagination limits
    // - when we find a new user, if we are in the page add it to the
    //   $results, and start building $ras array with its role-assignments
    // - when we are dealing with the next user, or are at the end of the userlist
    //   (last rec or last in page), trigger the check-permission idiom
    // - the check permission idiom will
    //   - add the default enrolment if needed
    //   - call has_any_capability_from_rarc(), which based on RAs and RCs will return a bool
    //     (should be fairly tight code ;-) )
    // - if the user has permission, all is good, just $c++ (counter)
    // - ...else, decrease the counter - so pagination is kept straight,
    //      and (if we are in the page) remove from the results
    //
    $results = array();

    // pagination controls
    $c = 0;
    $limitfrom = (int)$limitfrom;
    $limitnum = (int)$limitnum;

    //
    // Track our last user id so we know when we are dealing
    // with a new user...
    //
    $lastuserid  = 0;
    //
    // In this loop, we
    // $ras: role assignments, multidimensional array
    // treat as a stack - going from local to general
    // $ras = (( roleid=> x, $depth=>y) , ( roleid=> x, $depth=>y))
    //
    foreach($rs as $user) {

        //error_log(" Record: " . print_r($user,1));

        //
        // Pagination controls
        // Note that we might end up removing a user
        // that ends up _not_ having the rights,
        // therefore rolling back $c
        //
        if ($lastuserid != $user->id) {

            // Did the last user end up with a positive permission?
            if ($lastuserid !=0) {
                if ($frontpageroleinteresting) {
                    // add frontpage role if interesting
                    $ras[] = array('roleid' => $CFG->defaultfrontpageroleid,
                                   'depth'  => $context->depth);
                }
                if ($defaultroleinteresting) {
                    // add the role at the end of $ras
                    $ras[] = array( 'roleid' => $CFG->defaultuserroleid,
                                    'depth'  => 1 );
                }
                if (has_any_capability_from_rarc($ras, $roleperms, $caps)) {
                    $c++;
                } else {
                    // remove the user from the result set,
                    // only if we are 'in the page'
                    if ($limitfrom === 0 || $c >= $limitfrom) {
                        unset($results[$lastuserid]);
                    }
                }
            }

            // Did we hit pagination limit?
            if ($limitnum !==0 && $c >= ($limitfrom+$limitnum)) { // we are done!
                break;
            }

            // New user setup, and $ras reset
            $lastuserid = $user->id;
            $ras = array();
            if (!empty($user->roleid)) {
                $ras[] = array( 'roleid' => (int)$user->roleid,
                                'depth'  => (int)$user->depth );
            }

            // if we are 'in the page', also add the rec
            // to the results...
            if ($limitfrom === 0 || $c >= $limitfrom) {
                $results[$user->id] = $user; // trivial
            }
        } else {
            // Additional RA for $lastuserid
            $ras[] = array( 'roleid'=>(int)$user->roleid,
                            'depth'=>(int)$user->depth );
        }

    } // end while(fetch)
    $rs->close();

    // Prune last entry if necessary
    if ($lastuserid !=0) {
        if ($frontpageroleinteresting) {
            // add frontpage role if interesting
            $ras[] = array('roleid' => $CFG->defaultfrontpageroleid,
                           'depth'  => $context->depth);
        }
        if ($defaultroleinteresting) {
            // add the role at the end of $ras
            $ras[] = array( 'roleid' => $CFG->defaultuserroleid,
                            'depth'  => 1 );
        }
        if (!has_any_capability_from_rarc($ras, $roleperms, $caps)) {
            // remove the user from the result set,
            // only if we are 'in the page'
            if ($limitfrom === 0 || $c >= $limitfrom) {
                if (isset($results[$lastuserid])) {
                    unset($results[$lastuserid]);
                }
            }
        }
    }

    return $results;
}

/**
 * Check if any of a list of capabilities is granted
 *
 * Fast (fast!) utility function to resolve if any of a list of capabilities is
 * granted, based on Role Assignments and Role Capabilities.
 *
 * Used (at least) by get_users_by_capability().
 *
 * If PHP had fast built-in memoize functions, we could
 * add a $contextid parameter and memoize the return values.
 *
 * Note that this function must be kept in synch with has_capability_in_accessdata.
 *
 * @param array $ras role assignments
 * @param array $roleperms role permissions
 * @param string $capabilities array of capability names
 * @return bool
 */
function has_any_capability_from_rarc($ras, $roleperms, $caps) {
    // Mini-state machine, using $hascap
    // $hascap[ 'moodle/foo:bar' ]->perm = CAP_SOMETHING (numeric constant)
    // $hascap[ 'moodle/foo:bar' ]->radepth = depth of the role assignment that set it
    // $hascap[ 'moodle/foo:bar' ]->rcdepth = depth of the rolecap that set it
    // -- when resolving conflicts, we need to look into radepth first, if unresolved

    $hascap = array();

    //
    // Compute which permission/roleassignment/rolecap
    // wins for each capability we are walking
    //
    foreach ($ras as $ra) {
        foreach ($caps as $cap) {
            if (!isset($roleperms[$cap][$ra['roleid']])) {
                // nothing set for this cap - skip
                continue;
            }
            // We explicitly clone here as we
            // add more properties to it
            // that must stay separate from the
            // original roleperm data structure
            $rp = clone($roleperms[$cap][$ra['roleid']]);
            $rp->radepth = $ra['depth'];

            // Trivial case, we are the first to set
            if (!isset($hascap[$cap])) {
                $hascap[$cap] = $rp;
            }

            //
            // Resolve who prevails, in order of precendence
            // - Prohibits always wins
            // - Locality of RA
            // - Locality of RC
            //
            //// Prohibits...
            if ($rp->perm === CAP_PROHIBIT) {
                $hascap[$cap] = $rp;
                continue;
            }
            if ($hascap[$cap]->perm === CAP_PROHIBIT) {
                continue;
            }

            // Locality of RA - the look is ordered by depth DESC
            // so from local to general -
            // Higher RA loses to local RA... unless perm===0
            /// Thanks to the order of the records, $rp->radepth <= $hascap[$cap]->radepth
            if ($rp->radepth > $hascap[$cap]->radepth) {
                error_log('Should not happen @ ' . __FUNCTION__.':'.__LINE__);
            }
            if ($rp->radepth < $hascap[$cap]->radepth) {
                if ($hascap[$cap]->perm!==0) {
                    // Wider RA loses to local RAs...
                    continue;
                } else {
                    // "Higher RA resolves conflict" case,
                    // local RAs had cancelled eachother
                    $hascap[$cap] = $rp;
                    continue;
                }
            }
            // Same ralevel - locality of RC wins
            if ($rp->rcdepth  > $hascap[$cap]->rcdepth) {
                $hascap[$cap] = $rp;
                continue;
            }
            if ($rp->rcdepth  > $hascap[$cap]->rcdepth) {
                continue;
            }
            // We match depth - add them
            $hascap[$cap]->perm += $rp->perm;
        }
    }
    foreach ($caps as $capability) {
        if (isset($hascap[$capability]) && $hascap[$capability]->perm > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Re-sort a users array based on a sorting policy
 *
 * Will re-sort a $users results array (from get_users_by_capability(), usually)
 * based on a sorting policy. This is to support the odd practice of
 * sorting teachers by 'authority', where authority was "lowest id of the role
 * assignment".
 *
 * Will execute 1 database query. Only suitable for small numbers of users, as it
 * uses an u.id IN() clause.
 *
 * Notes about the sorting criteria.
 *
 * As a default, we cannot rely on role.sortorder because then
 * admins/coursecreators will always win. That is why the sane
 * rule "is locality matters most", with sortorder as 2nd
 * consideration.
 *
 * If you want role.sortorder, use the 'sortorder' policy, and
 * name explicitly what roles you want to cover. It's probably
 * a good idea to see what roles have the capabilities you want
 * (array_diff() them against roiles that have 'can-do-anything'
 * to weed out admin-ish roles. Or fetch a list of roles from
 * variables like $CFG->coursemanagers .
 *
 * @global object
 * @param array $users Users array, keyed on userid
 * @param object $context
 * @param array $roles ids of the roles to include, optional
 * @param string $policy defaults to locality, more about
 * @return array sorted copy of the array
 */
function sort_by_roleassignment_authority($users, $context, $roles=array(), $sortpolicy='locality') {
    global $DB;

    $userswhere = ' ra.userid IN (' . implode(',',array_keys($users)) . ')';
    $contextwhere = 'AND ra.contextid IN ('.str_replace('/', ',',substr($context->path, 1)).')';
    if (empty($roles)) {
        $roleswhere = '';
    } else {
        $roleswhere = ' AND ra.roleid IN ('.implode(',',$roles).')';
    }

    $sql = "SELECT ra.userid
              FROM {role_assignments} ra
              JOIN {role} r
                   ON ra.roleid=r.id
              JOIN {context} ctx
                   ON ra.contextid=ctx.id
             WHERE $userswhere
                   $contextwhere
                   $roleswhere";

    // Default 'locality' policy -- read PHPDoc notes
    // about sort policies...
    $orderby = 'ORDER BY '
                    .'ctx.depth DESC, '  /* locality wins */
                    .'r.sortorder ASC, ' /* rolesorting 2nd criteria */
                    .'ra.id';            /* role assignment order tie-breaker */
    if ($sortpolicy === 'sortorder') {
        $orderby = 'ORDER BY '
                        .'r.sortorder ASC, ' /* rolesorting 2nd criteria */
                        .'ra.id';            /* role assignment order tie-breaker */
    }

    $sortedids = $DB->get_fieldset_sql($sql . $orderby);
    $sortedusers = array();
    $seen = array();

    foreach ($sortedids as $id) {
        // Avoid duplicates
        if (isset($seen[$id])) {
            continue;
        }
        $seen[$id] = true;

        // assign
        $sortedusers[$id] = $users[$id];
    }
    return $sortedusers;
}

/**
 * Gets all the users assigned this role in this context or higher
 *
 * @global object
 * @param int $roleid (can also be an array of ints!)
 * @param object $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @param string $fields fields from user (u.) , role assignment (ra) or role (r.)
 * @param string $sort sort from user (u.) , role assignment (ra) or role (r.)
 * @param bool $gethidden whether to fetch hidden enrolments too
 * @param string $group defaults to ''
 * @param mixed $limitfrom defaults to ''
 * @param mixed $limitnum defaults to ''
 * @param string $extrawheretest defaults to ''
 * @param string $whereparams defaults to ''
 * @return array
 */
function get_role_users($roleid, $context, $parent=false, $fields='',
        $sort='u.lastname, u.firstname', $gethidden=true, $group='',
        $limitfrom='', $limitnum='', $extrawheretest='', $whereparams=array()) {
    global $DB;

    if (empty($fields)) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.emailstop, u.lang, u.timezone, u.lastaccess, u.mnethostid, r.name as rolename';
    }

    // whether this assignment is hidden
    $hiddensql = $gethidden ? '': ' AND ra.hidden = 0 ';

    $parentcontexts = '';
    if ($parent) {
        $parentcontexts = substr($context->path, 1); // kill leading slash
        $parentcontexts = str_replace('/', ',', $parentcontexts);
        if ($parentcontexts !== '') {
            $parentcontexts = ' OR ra.contextid IN ('.$parentcontexts.' )';
        }
    }

    if ($roleid) {
        list($rids, $params) = $DB->get_in_or_equal($roleid, SQL_PARAMS_QM);
        $roleselect = "AND ra.roleid $rids";
    } else {
        $params = array();
        $roleselect = '';
    }

    if ($group) {
        $groupjoin   = "JOIN {groups_members} gm ON gm.userid = u.id";
        $groupselect = " AND gm.groupid = ? ";
        $params[] = $group;
    } else {
        $groupjoin   = '';
        $groupselect = '';
    }

    array_unshift($params, $context->id);

    if ($extrawheretest) {
        $extrawheretest = ' AND ' . $extrawheretest;
        $params = array_merge($params, $whereparams);
    }

    $sql = "SELECT $fields, ra.roleid
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
              JOIN {role} r ON ra.roleid = r.id
        $groupjoin
             WHERE (ra.contextid = ? $parentcontexts)
                   $roleselect
                   $groupselect
                   $hiddensql
                   $extrawheretest
          ORDER BY $sort";                  // join now so that we can just use fullname() later

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Counts all the users assigned this role in this context or higher
 *
 * @global object
 * @param mixed $roleid either int or an array of ints
 * @param object $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @return int Returns the result count
 */
function count_role_users($roleid, $context, $parent=false) {
    global $DB;

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
        list($rids, $params) = $DB->get_in_or_equal($roleid, SQL_PARAMS_QM);
        $roleselect = "AND r.roleid $rids";
    } else {
        $params = array();
        $roleselect = '';
    }

    array_unshift($params, $context->id);

    $sql = "SELECT count(u.id)
              FROM {role_assignments} r
              JOIN {user} u ON u.id = r.userid
             WHERE (r.contextid = ? $parentcontexts)
                   $roleselect
                   AND u.deleted = 0";

    return $DB->count_records_sql($sql, $params);
}

/**
 * This function gets the list of courses that this user has a particular capability in.
 * It is still not very efficient.
 *
 * @global object
 * @param string $capability Capability in question
 * @param int $userid User ID or null for current user
 * @param bool $doanything True if 'doanything' is permitted (default)
 * @param string $fieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id
 * @param string $orderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed
 * @return array Array of courses, may have zero entries. Or false if query failed.
 */
function get_user_capability_course($capability, $userid=NULL, $doanything=true, $fieldsexceptid='', $orderby='') {
    global $DB;

    // Convert fields list and ordering
    $fieldlist = '';
    if ($fieldsexceptid) {
        $fields = explode(',', $fieldsexceptid);
        foreach($fields as $field) {
            $fieldlist .= ',c.'.$field;
        }
    }
    if ($orderby) {
        $fields = explode(',', $orderby);
        $orderby = '';
        foreach($fields as $field) {
            if($orderby) {
                $orderby .= ',';
            }
            $orderby .= 'c.'.$field;
        }
        $orderby = 'ORDER BY '.$orderby;
    }

    // Obtain a list of everything relevant about all courses including context.
    // Note the result can be used directly as a context (we are going to), the course
    // fields are just appended.

    if (!$rs = $DB->get_recordset_sql("SELECT x.*, c.id AS courseid $fieldlist
                                         FROM {course} c
                                        INNER JOIN {context} x
                                              ON (c.id=x.instanceid AND x.contextlevel=".CONTEXT_COURSE.")
                                     $orderby")) {
         return false;
    }

    // Check capability for each course in turn
    $courses = array();
    foreach ($rs as $coursecontext) {
        if(has_capability($capability, $coursecontext, $userid, $doanything)) {
            // We've got the capability. Make the record look like a course record
            // and store it
            $coursecontext->id = $coursecontext->courseid;
            unset($coursecontext->courseid);
            unset($coursecontext->contextlevel);
            unset($coursecontext->instanceid);
            $courses[] = $coursecontext;
        }
    }
    $rs->close();
    return $courses;
}

/** This function finds the roles assigned directly to this context only
 * i.e. no parents role
 *
 * @global object
 * @param object $context
 * @return array
 */
function get_roles_on_exact_context($context) {
    global $DB;

    return $DB->get_records_sql("SELECT r.*
                                   FROM {role_assignments} ra, {role} r
                                  WHERE ra.roleid = r.id AND ra.contextid = ?",
                                array($context->id));

}

/**
 * Switches the current user to another role for the current session and only
 * in the given context.
 *
 * The caller *must* check
 * - that this op is allowed
 * - that the requested role can be switched to in this context (use get_switchable_roles)
 * - that the requested role is NOT $CFG->defaultuserroleid
 *
 * To "unswitch" pass 0 as the roleid.
 *
 * This function *will* modify $USER->access - beware
 *
 * @global object
 * @param integer $roleid the role to switch to.
 * @param object $context the context in which to perform the switch.
 * @return bool success or failure.
 */
function role_switch($roleid, $context) {
    global $USER;

    //
    // Plan of action
    //
    // - Add the ghost RA to $USER->access
    //   as $USER->access['rsw'][$path] = $roleid
    //
    // - Make sure $USER->access['rdef'] has the roledefs
    //   it needs to honour the switcheroo
    //
    // Roledefs will get loaded "deep" here - down to the last child
    // context. Note that
    //
    // - When visiting subcontexts, our selective accessdata loading
    //   will still work fine - though those ra/rdefs will be ignored
    //   appropriately while the switch is in place
    //
    // - If a switcheroo happens at a category with tons of courses
    //   (that have many overrides for switched-to role), the session
    //   will get... quite large. Sometimes you just can't win.
    //
    // To un-switch just unset($USER->access['rsw'][$path])
    //
    // Note: it is not possible to switch to roles that do not have course:view

    // Add the switch RA
    if (!isset($USER->access['rsw'])) {
        $USER->access['rsw'] = array();
    }

    if ($roleid == 0) {
        unset($USER->access['rsw'][$context->path]);
        if (empty($USER->access['rsw'])) {
            unset($USER->access['rsw']);
        }
        return true;
    }

    $USER->access['rsw'][$context->path]=$roleid;

    // Load roledefs
    $USER->access = get_role_access_bycontext($roleid, $context,
                                              $USER->access);

    return true;
}


/**
 * Get any role that has an override on exact context
 *
 * @global object
 * @param object $context
 * @return array
 */
function get_roles_with_override_on_context($context) {
    global $DB;

    return $DB->get_records_sql("SELECT r.*
                                   FROM {role_capabilities} rc, {role} r
                                  WHERE rc.roleid = r.id AND rc.contextid = ?",
                                array($context->id));
}

/**
 * Get all capabilities for this role on this context (overrides)
 *
 * @global object
 * @param object $role
 * @param object $context
 * @return array
 */
function get_capabilities_from_role_on_context($role, $context) {
    global $DB;

    return $DB->get_records_sql("SELECT *
                                   FROM {role_capabilities}
                                  WHERE contextid = ? AND roleid = ?",
                                array($context->id, $role->id));
}

/**
 * Find out which roles has assignment on this context
 *
 * @global object
 * @param object $context
 * @return array
 *
 */
function get_roles_with_assignment_on_context($context) {
    global $DB;

    return $DB->get_records_sql("SELECT r.*
                                   FROM {role_assignments} ra, {role} r
                                  WHERE ra.roleid = r.id AND ra.contextid = ?",
                                array($context->id));
}



/**
 * Find all user assignemnt of users for this role, on this context
 *
 * @global object
 * @param object $role
 * @param object $context
 * @return array
 */
function get_users_from_role_on_context($role, $context) {
    global $DB;

    return $DB->get_records_sql("SELECT *
                                   FROM {role_assignments}
                                  WHERE contextid = ? AND roleid = ?",
                                array($context->id, $role->id));
}

/**
 * Simple function returning a boolean true if roles exist, otherwise false
 *
 * @global object
 * @param int $userid
 * @param int $roleid
 * @param int $contextid
 * @return bool
 */
function user_has_role_assignment($userid, $roleid, $contextid=0) {
    global $DB;

    if ($contextid) {
        return $DB->record_exists('role_assignments', array('userid'=>$userid, 'roleid'=>$roleid, 'contextid'=>$contextid));
    } else {
        return $DB->record_exists('role_assignments', array('userid'=>$userid, 'roleid'=>$roleid));
    }
}

/**
 * Get role name or alias if exists and format the text.
 *
 * @global object
 * @param object $role role object
 * @param object $coursecontext
 * @return string name of role in course context
 */
function role_get_name($role, $coursecontext) {
    global $DB;

    if ($r = $DB->get_record('role_names', array('roleid'=>$role->id, 'contextid'=>$coursecontext->id))) {
        return strip_tags(format_string($r->name));
    } else {
        return strip_tags(format_string($role->name));
    }
}

/**
 * Prepare list of roles for display, apply aliases and format text
 *
 * @global object
 * @param array $roleoptions array roleid => rolename or roleid => roleobject
 * @param object $context a context
 * @return array Array of context-specific role names, or role objexts with a ->localname field added.
 */
function role_fix_names($roleoptions, $context, $rolenamedisplay=ROLENAME_ALIAS) {
    global $DB;

    // Make sure we are working with an array roleid => name. Normally we
    // want to use the unlocalised name if the localised one is not present.
    $newnames = array();
    foreach ($roleoptions as $rid => $roleorname) {
        if ($rolenamedisplay != ROLENAME_ALIAS_RAW) {
            if (is_object($roleorname)) {
                $newnames[$rid] = $roleorname->name;
            } else {
                $newnames[$rid] = $roleorname;
            }
        } else {
            $newnames[$rid] = '';
        }
    }

    // If necessary, get the localised names.
    if ($rolenamedisplay != ROLENAME_ORIGINAL && !empty($context->id)) {
        // Make sure we have a course context.
        if ($context->contextlevel == CONTEXT_MODULE) {
            if ($parentcontextid = array_shift(get_parent_contexts($context))) {
                $context = get_context_instance_by_id($parentcontextid);
            }
        } else if ($context->contextlevel == CONTEXT_BLOCK) {
            do {
                if ($parentcontextid = array_shift(get_parent_contexts($context))) {
                    $context = get_context_instance_by_id($parentcontextid);
                }
            } while ($parentcontextid && $context->contextlevel != CONTEXT_COURSE);
        }

        // The get the relevant renames, and use them.
        $aliasnames = $DB->get_records('role_names', array('contextid'=>$context->id));
        foreach ($aliasnames as $alias) {
            if (isset($newnames[$alias->roleid])) {
                if ($rolenamedisplay == ROLENAME_ALIAS || $rolenamedisplay == ROLENAME_ALIAS_RAW) {
                    $newnames[$alias->roleid] = $alias->name;
                } else if ($rolenamedisplay == ROLENAME_BOTH) {
                    $newnames[$alias->roleid] = $alias->name . ' (' . $roleoptions[$alias->roleid] . ')';
                }
            }
        }
    }

    // Finally, apply format_string and put the result in the right place.
    foreach ($roleoptions as $rid => $roleorname) {
        if ($rolenamedisplay != ROLENAME_ALIAS_RAW) {
            $newnames[$rid] = strip_tags(format_string($newnames[$rid]));
        }
        if (is_object($roleorname)) {
            $roleoptions[$rid]->localname = $newnames[$rid];
        } else {
            $roleoptions[$rid] = $newnames[$rid];
        }
    }
    return $roleoptions;
}

/**
 * Aids in detecting if a new line is required when reading a new capability
 *
 * This function helps admin/roles/manage.php etc to detect if a new line should be printed
 * when we read in a new capability.
 * Most of the time, if the 2 components are different we should print a new line, (e.g. course system->rss client)
 * but when we are in grade, all reports/import/export capabilites should be together
 *
 * @param string $cap component string a
 * @param string $comp component string b
 * @param mixed $contextlevel
 * @return bool whether 2 component are in different "sections"
 */
function component_level_changed($cap, $comp, $contextlevel) {

    if ($cap->component == 'enrol/authorize' && $comp =='enrol/authorize') {
        return false;
    }

    if (strstr($cap->component, '/') && strstr($comp, '/')) {
        $compsa = explode('/', $cap->component);
        $compsb = explode('/', $comp);

        // list of system reports
        if (($compsa[0] == 'report') && ($compsb[0] == 'report')) {
            return false;
        }

        // we are in gradebook, still
        if (($compsa[0] == 'gradeexport' || $compsa[0] == 'gradeimport' || $compsa[0] == 'gradereport') &&
            ($compsb[0] == 'gradeexport' || $compsb[0] == 'gradeimport' || $compsb[0] == 'gradereport')) {
            return false;
        }

        if (($compsa[0] == 'coursereport') && ($compsb[0] == 'coursereport')) {
            return false;
        }
    }

    return ($cap->component != $comp || $cap->contextlevel != $contextlevel);
}

/**
 * Rebuild all related context depth and path caches
 *
 * @global object
 * @param array $fixcontexts array of contexts, strongtyped
 */
function rebuild_contexts(array $fixcontexts) {
    global $DB;

    foreach ($fixcontexts as $context) {
        if ($context->path) {
            mark_context_dirty($context->path);
        }
        $DB->set_field_select('context', 'depth', 0, "path LIKE '%/$context->id/%'");
        $DB->set_field('context', 'depth', 0, array('id'=>$context->id));
    }
    build_context_path(false);
}

/**
 * Populate context.path and context.depth where missing.
 *
 * @param bool $force force a complete rebuild of the path and depth fields, defaults to false
 */
function build_context_path($force=false) {
    global $CFG, $DB;

    // System context
    $sitectx = get_system_context(!$force);
    $base    = '/'.$sitectx->id;

    // Sitecourse
    $sitecoursectx = $DB->get_record('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>SITEID));
    if ($force || $sitecoursectx->path !== "$base/{$sitecoursectx->id}") {
        $DB->set_field('context', 'path',  "$base/{$sitecoursectx->id}", array('id'=>$sitecoursectx->id));
        $DB->set_field('context', 'depth', 2, array('id'=>$sitecoursectx->id));
        $sitecoursectx = $DB->get_record('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>SITEID));
    }

    $ctxemptyclause = " AND (ctx.path IS NULL
                              OR ctx.depth=0) ";
    $emptyclause    = " AND ({context}.path IS NULL
                              OR {context}.depth=0) ";
    if ($force) {
        $ctxemptyclause = $emptyclause = '';
    }

    /* MDL-11347:
     *  - mysql does not allow to use FROM in UPDATE statements
     *  - using two tables after UPDATE works in mysql, but might give unexpected
     *    results in pg 8 (depends on configuration)
     *  - using table alias in UPDATE does not work in pg < 8.2
     *
     * Different code for each database - mostly for performance reasons
     */
    $dbfamily = $DB->get_dbfamily();
    if ($dbfamily == 'mysql') {
        $updatesql = "UPDATE {context} ct, {context_temp} temp
                         SET ct.path  = temp.path,
                             ct.depth = temp.depth
                       WHERE ct.id = temp.id";
    } else if ($dbfamily == 'oracle') {
        $updatesql = "UPDATE {context} ct
                         SET (ct.path, ct.depth) =
                             (SELECT temp.path, temp.depth
                                FROM {context_temp} temp
                               WHERE temp.id=ct.id)
                       WHERE EXISTS (SELECT 'x'
                                       FROM {context_temp} temp
                                       WHERE temp.id = ct.id)";
    } else if ($dbfamily == 'postgres' or $dbfamily == 'mssql') {
        $updatesql = "UPDATE {context}
                         SET path  = temp.path,
                             depth = temp.depth
                        FROM {context_temp} temp
                       WHERE temp.id={context}.id";
    } else {
        // sqlite and others
        $updatesql = "UPDATE {context}
                         SET path  = (SELECT path FROM {context_temp} WHERE id = {context}.id),
                             depth = (SELECT depth FROM {context_temp} WHERE id = {context}.id)
                         WHERE id IN (SELECT id FROM mdl_context_temp)";
    }

    // Top level categories
    $sql = "UPDATE {context}
               SET depth=2, path=" . $DB->sql_concat("'$base/'", 'id') . "
             WHERE contextlevel=".CONTEXT_COURSECAT."
                   AND EXISTS (SELECT 'x'
                                 FROM {course_categories} cc
                                WHERE cc.id = {context}.instanceid
                                      AND cc.depth=1)
                   $emptyclause";

    $DB->execute($sql);
    $DB->delete_records('context_temp');

    // Deeper categories - one query per depthlevel
    $maxdepth = $DB->get_field_sql("SELECT MAX(depth)
                               FROM {course_categories}");
    for ($n=2; $n<=$maxdepth; $n++) {
        $sql = "INSERT INTO {context_temp} (id, path, depth)
                SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", $n+1
                  FROM {context} ctx
                  JOIN {course_categories} c ON ctx.instanceid=c.id
                  JOIN {context} pctx ON c.parent=pctx.instanceid
                 WHERE ctx.contextlevel=".CONTEXT_COURSECAT."
                       AND pctx.contextlevel=".CONTEXT_COURSECAT."
                       AND c.depth=$n
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {context_temp} temp
                                       WHERE temp.id = ctx.id)
                       $ctxemptyclause";
        $DB->execute($sql);

        // this is needed after every loop
        // MDL-11532
        $DB->execute($updatesql);
        $DB->delete_records('context_temp');
    }

    // Courses -- except sitecourse
    $sql = "INSERT INTO {context_temp} (id, path, depth)
            SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {context} ctx
              JOIN {course} c ON ctx.instanceid=c.id
              JOIN {context} pctx ON c.category=pctx.instanceid
             WHERE ctx.contextlevel=".CONTEXT_COURSE."
                   AND c.id!=".SITEID."
                   AND pctx.contextlevel=".CONTEXT_COURSECAT."
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {context_temp} temp
                                       WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    $DB->execute($sql);

    $DB->execute($updatesql);
    $DB->delete_records('context_temp');

    // Module instances
    $sql = "INSERT INTO {context_temp} (id, path, depth)
            SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {context} ctx
              JOIN {course_modules} cm ON ctx.instanceid=cm.id
              JOIN {context} pctx ON cm.course=pctx.instanceid
             WHERE ctx.contextlevel=".CONTEXT_MODULE."
                   AND pctx.contextlevel=".CONTEXT_COURSE."
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {context_temp} temp
                                       WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    $DB->execute($sql);

    $DB->execute($updatesql);
    $DB->delete_records('context_temp');

    // User
    $sql = "UPDATE {context}
               SET depth=2, path=".$DB->sql_concat("'$base/'", 'id')."
             WHERE contextlevel=".CONTEXT_USER."
                   AND EXISTS (SELECT 'x'
                                 FROM {user} u
                                WHERE u.id = {context}.instanceid)
                   $emptyclause ";
    $DB->execute($sql);

    // Blocks
    $sql = "INSERT INTO {context_temp} (id, path, depth)
            SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {context} ctx
              JOIN {block_instances} bi ON ctx.instanceid = bi.id
              JOIN {context} pctx ON bi.parentcontextid = pctx.id
             WHERE ctx.contextlevel=".CONTEXT_BLOCK."
                   AND NOT EXISTS (SELECT 'x'
                                   FROM {context_temp} temp
                                   WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    $DB->execute($sql);

    $DB->execute($updatesql);
    $DB->delete_records('context_temp');

    // reset static course cache - it might have incorrect cached data
    global $ACCESSLIB_PRIVATE;
    $ACCESSLIB_PRIVATE->contexts = array();
    $ACCESSLIB_PRIVATE->contextsbyid = array();
}

/**
 * Update the path field of the context and all dep. subcontexts that follow
 *
 * Update the path field of the context and
 * all the dependent subcontexts that follow
 * the move.
 *
 * The most important thing here is to be as
 * DB efficient as possible. This op can have a
 * massive impact in the DB.
 *
 * @global object
 * @param obj $current context obj
 * @param obj $newparent new parent obj
 *
 */
function context_moved($context, $newparent) {
    global $DB;

    $frompath = $context->path;
    $newpath  = $newparent->path . '/' . $context->id;

    $setdepth = '';
    if (($newparent->depth +1) != $context->depth) {
        $diff = $newparent->depth - $context->depth + 1;
        $setdepth = ", depth = depth + $diff";
    }
    $sql = "UPDATE {context}
               SET path = ?
                   $setdepth
             WHERE path = ?";
    $params = array($newpath, $frompath);
    $DB->execute($sql, $params);

    $sql = "UPDATE {context}
               SET path = ".$DB->sql_concat("?", $DB->sql_substr("path", strlen($frompath)+1))."
                   $setdepth
             WHERE path LIKE ?";
    $params = array($newpath, "{$frompath}/%");
    $DB->execute($sql, $params);

    mark_context_dirty($frompath);
    mark_context_dirty($newpath);
}


/**
 * Turn the ctx* fields in an objectlike record into a context subobject
 * This allows us to SELECT from major tables JOINing with
 * context at no cost, saving a ton of context
 * lookups...
 *
 * @param object $rec
 * @return object
 */
function make_context_subobj($rec) {
    $ctx = new StdClass;
    $ctx->id           = $rec->ctxid;    unset($rec->ctxid);
    $ctx->path         = $rec->ctxpath;  unset($rec->ctxpath);
    $ctx->depth        = $rec->ctxdepth; unset($rec->ctxdepth);
    $ctx->contextlevel = $rec->ctxlevel; unset($rec->ctxlevel);
    $ctx->instanceid   = $rec->id;

    $rec->context = $ctx;
    return $rec;
}

/**
 * Do some basic, quick checks to see whether $rec->context looks like a valid context object.
 *
 * @param object $rec a think that has a context, for example a course,
 *      course category, course modules, etc.
 * @param int $contextlevel the type of thing $rec is, one of the CONTEXT_... constants.
 * @return bool whether $rec->context looks like the correct context object
 *      for this thing.
 */
function is_context_subobj_valid($rec, $contextlevel) {
    return isset($rec->context) && isset($rec->context->id) &&
            isset($rec->context->path) && isset($rec->context->depth) &&
            isset($rec->context->contextlevel) && isset($rec->context->instanceid) &&
            $rec->context->contextlevel == $contextlevel && $rec->context->instanceid == $rec->id;
}

/**
 * Ensure that $rec->context is present and correct before you continue
 *
 * When you have a record (for example a $category, $course, $user or $cm that may,
 * or may not, have come from a place that does make_context_subobj, you can use
 * this method to ensure that $rec->context is present and correct before you continue.
 *
 * @param object $rec a thing that has an associated context.
 * @param integer $contextlevel the type of thing $rec is, one of the CONTEXT_... constants.
 */
function ensure_context_subobj_present(&$rec, $contextlevel) {
    if (!is_context_subobj_valid($rec, $contextlevel)) {
        $rec->context = get_context_instance($contextlevel, $rec->id);
    }
}

/**
 * Fetch recent dirty contexts to know cheaply whether our $USER->access
 * is stale and needs to be reloaded.
 *
 * Uses cache_flags
 * @param int $time
 * @return array Array of dirty contexts
 */
function get_dirty_contexts($time) {
    return get_cache_flags('accesslib/dirtycontexts', $time-2);
}

/**
 * Mark a context as dirty (with timestamp)
 * so as to force reloading of the context.
 *
 * @global object
 * @global object
 * @param string $path context path
 */
function mark_context_dirty($path) {
    global $CFG, $ACCESSLIB_PRIVATE;

    if (during_initial_install()) {
        return;
    }

    // only if it is a non-empty string
    if (is_string($path) && $path !== '') {
        set_cache_flag('accesslib/dirtycontexts', $path, 1, time()+$CFG->sessiontimeout);
        if (isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
            $ACCESSLIB_PRIVATE->dirtycontexts[$path] = 1;
        }
    }
}

/**
 * Will walk the contextpath to answer whether
 * the contextpath is dirty
 *
 * @param array $contexts array of strings
 * @param obj|array $dirty Dirty contexts from get_dirty_contexts()
 * @return bool
 */
function is_contextpath_dirty($pathcontexts, $dirty) {
    $path = '';
    foreach ($pathcontexts as $ctx) {
        $path = $path.'/'.$ctx;
        if (isset($dirty[$path])) {
            return true;
        }
    }
    return false;
}

/**
 * Fix the roles.sortorder field in the database, so it contains sequential integers,
 * and return an array of roleids in order.
 *
 * @param array $allroles array of roles, as returned by get_all_roles();
 * @return array $role->sortorder =-> $role->id with the keys in ascending order.
 */
function fix_role_sortorder($allroles) {
    $rolesort = array();
    $i = 0;
    foreach ($allroles as $role) {
        $rolesort[$i] = $role->id;
        if ($role->sortorder != $i) {
            $r = new object();
            $r->id = $role->id;
            $r->sortorder = $i;
            $DB->update_record('role', $r);
            $allroles[$role->id]->sortorder = $i;
        }
        $i++;
    }
    return $rolesort;
}

/**
 * Switch the sort order of two roles (used in admin/roles/manage.php).
 *
 * @global object
 * @param object $first The first role. Actually, only ->sortorder is used.
 * @param object $second The second role. Actually, only ->sortorder is used.
 * @return boolean success or failure
 */
function switch_roles($first, $second) {
    global $DB;
    $temp = $DB->get_field('role', 'MAX(sortorder) + 1', array());
    $result = $DB->set_field('role', 'sortorder', $temp, array('sortorder' => $first->sortorder));
    $result = $result && $DB->set_field('role', 'sortorder', $first->sortorder, array('sortorder' => $second->sortorder));
    $result = $result && $DB->set_field('role', 'sortorder', $second->sortorder, array('sortorder' => $temp));
    return $result;
}

/**
 * duplicates all the base definitions of a role
 *
 * @global object
 * @param object $sourcerole role to copy from
 * @param int $targetrole id of role to copy to
 */
function role_cap_duplicate($sourcerole, $targetrole) {
    global $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    $caps = $DB->get_records_sql("SELECT *
                                    FROM {role_capabilities}
                                   WHERE roleid = ? AND contextid = ?",
                                 array($sourcerole->id, $systemcontext->id));
    // adding capabilities
    foreach ($caps as $cap) {
        unset($cap->id);
        $cap->roleid = $targetrole;
        $DB->insert_record('role_capabilities', $cap);
    }
}

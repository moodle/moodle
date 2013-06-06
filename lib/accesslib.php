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
 * - context_course::instance($courseid), context_module::instance($cm->id), context_coursecat::instance($catid)
 * - context::instance_by_id($contextid)
 * - $context->get_parent_contexts();
 * - $context->get_child_contexts();
 *
 * Whether the user can do something...
 * - has_capability()
 * - has_any_capability()
 * - has_all_capabilities()
 * - require_capability()
 * - require_login() (from moodlelib)
 * - is_enrolled()
 * - is_viewing()
 * - is_guest()
 * - is_siteadmin()
 * - isguestuser()
 * - isloggedin()
 *
 * What courses has this user access to?
 * - get_enrolled_users()
 *
 * What users can do X in this context?
 * - get_enrolled_users() - at and bellow course context
 * - get_users_by_capability() - above course context
 *
 * Modify roles
 * - role_assign()
 * - role_unassign()
 * - role_unassign_all()
 *
 * Advanced - for internal use only
 * - load_all_capabilities()
 * - reload_all_capabilities()
 * - has_capability_in_accessdata()
 * - get_user_access_sitewide()
 * - load_course_context()
 * - load_role_access_by_context()
 * - etc.
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
 * against userid in $ACCESSLIB_PRIVATE->accessdatabyuser).
 *
 * $accessdata is a multidimensional array, holding
 * role assignments (RAs), role-capabilities-perm sets
 * (role defs) and a list of courses we have loaded
 * data for.
 *
 * Things are keyed on "contextpaths" (the path field of
 * the context table) for fast walking up/down the tree.
 * <code>
 * $accessdata['ra'][$contextpath] = array($roleid=>$roleid)
 *                  [$contextpath] = array($roleid=>$roleid)
 *                  [$contextpath] = array($roleid=>$roleid)
 * </code>
 *
 * Role definitions are stored like this
 * (no cap merge is done - so it's compact)
 *
 * <code>
 * $accessdata['rdef']["$contextpath:$roleid"]['mod/forum:viewpost'] = 1
 *                                            ['mod/forum:editallpost'] = -1
 *                                            ['mod/forum:startdiscussion'] = -1000
 * </code>
 *
 * See how has_capability_in_accessdata() walks up the tree.
 *
 * First we only load rdef and ra down to the course level, but not below.
 * This keeps accessdata small and compact. Below-the-course ra/rdef
 * are loaded as needed. We keep track of which courses we have loaded ra/rdef in
 * <code>
 * $accessdata['loaded'] = array($courseid1=>1, $courseid2=>1)
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
 * Changes at the system level will force the reload for everyone.
 *
 * <b>Default role caps</b>
 * The default role assignment is not in the DB, so we
 * add it manually to accessdata.
 *
 * This means that functions that work directly off the
 * DB need to ensure that the default role caps
 * are dealt with appropriately.
 *
 * @package    core_access
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** No capability change */
define('CAP_INHERIT', 0);
/** Allow permission, overrides CAP_PREVENT defined in parent contexts */
define('CAP_ALLOW', 1);
/** Prevent permission, overrides CAP_ALLOW defined in parent contexts */
define('CAP_PREVENT', -1);
/** Prohibit permission, overrides everything in current and child contexts */
define('CAP_PROHIBIT', -1000);

/** System context level - only one instance in every system */
define('CONTEXT_SYSTEM', 10);
/** User context level -  one instance for each user describing what others can do to user */
define('CONTEXT_USER', 30);
/** Course category context level - one instance for each category */
define('CONTEXT_COURSECAT', 40);
/** Course context level - one instances for each course */
define('CONTEXT_COURSE', 50);
/** Course module context level - one instance for each course module */
define('CONTEXT_MODULE', 70);
/**
 * Block context level - one instance for each block, sticky blocks are tricky
 * because ppl think they should be able to override them at lower contexts.
 * Any other context level instance can be parent of block context.
 */
define('CONTEXT_BLOCK', 80);

/** Capability allow management of trusts - NOT IMPLEMENTED YET - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_MANAGETRUST', 0x0001);
/** Capability allows changes in system configuration - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_CONFIG',      0x0002);
/** Capability allows user to add scripted content - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_XSS',         0x0004);
/** Capability allows access to personal user information - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_PERSONAL',    0x0008);
/** Capability allows users to add content others may see - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_SPAM',        0x0010);
/** capability allows mass delete of data belonging to other users - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_DATALOSS',    0x0020);

/** rolename displays - the name as defined in the role definition, localised if name empty */
define('ROLENAME_ORIGINAL', 0);
/** rolename displays - the name as defined by a role alias at the course level, falls back to ROLENAME_ORIGINAL if alias not present */
define('ROLENAME_ALIAS', 1);
/** rolename displays - Both, like this:  Role alias (Original) */
define('ROLENAME_BOTH', 2);
/** rolename displays - the name as defined in the role definition and the shortname in brackets */
define('ROLENAME_ORIGINALANDSHORT', 3);
/** rolename displays - the name as defined by a role alias, in raw form suitable for editing */
define('ROLENAME_ALIAS_RAW', 4);
/** rolename displays - the name is simply short role name */
define('ROLENAME_SHORT', 5);

if (!defined('CONTEXT_CACHE_MAX_SIZE')) {
    /** maximum size of context cache - it is possible to tweak this config.php or in any script before inclusion of context.php */
    define('CONTEXT_CACHE_MAX_SIZE', 2500);
}

/**
 * Although this looks like a global variable, it isn't really.
 *
 * It is just a private implementation detail to accesslib that MUST NOT be used elsewhere.
 * It is used to cache various bits of data between function calls for performance reasons.
 * Sadly, a PHP global variable is the only way to implement this, without rewriting everything
 * as methods of a class, instead of functions.
 *
 * @access private
 * @global stdClass $ACCESSLIB_PRIVATE
 * @name $ACCESSLIB_PRIVATE
 */
global $ACCESSLIB_PRIVATE;
$ACCESSLIB_PRIVATE = new stdClass();
$ACCESSLIB_PRIVATE->dirtycontexts    = null;    // Dirty contexts cache, loaded from DB once per page
$ACCESSLIB_PRIVATE->accessdatabyuser = array(); // Holds the cache of $accessdata structure for users (including $USER)
$ACCESSLIB_PRIVATE->rolepermissions  = array(); // role permissions cache - helps a lot with mem usage
$ACCESSLIB_PRIVATE->capabilities     = null;    // detailed information about the capabilities

/**
 * Clears accesslib's private caches. ONLY BE USED BY UNIT TESTS
 *
 * This method should ONLY BE USED BY UNIT TESTS. It clears all of
 * accesslib's private caches. You need to do this before setting up test data,
 * and also at the end of the tests.
 *
 * @access private
 * @return void
 */
function accesslib_clear_all_caches_for_unit_testing() {
    global $USER;
    if (!PHPUNIT_TEST) {
        throw new coding_exception('You must not call clear_all_caches outside of unit tests.');
    }

    accesslib_clear_all_caches(true);

    unset($USER->access);
}

/**
 * Clears accesslib's private caches. ONLY BE USED FROM THIS LIBRARY FILE!
 *
 * This reset does not touch global $USER.
 *
 * @access private
 * @param bool $resetcontexts
 * @return void
 */
function accesslib_clear_all_caches($resetcontexts) {
    global $ACCESSLIB_PRIVATE;

    $ACCESSLIB_PRIVATE->dirtycontexts    = null;
    $ACCESSLIB_PRIVATE->accessdatabyuser = array();
    $ACCESSLIB_PRIVATE->rolepermissions  = array();
    $ACCESSLIB_PRIVATE->capabilities     = null;

    if ($resetcontexts) {
        context_helper::reset_caches();
    }
}

/**
 * Gets the accessdata for role "sitewide" (system down to course)
 *
 * @access private
 * @param int $roleid
 * @return array
 */
function get_role_access($roleid) {
    global $DB, $ACCESSLIB_PRIVATE;

    /* Get it in 1 DB query...
     * - relevant role caps at the root and down
     *   to the course level - but not below
     */

    //TODO: MUC - this could be cached in shared memory to speed up first page loading, web crawlers, etc.

    $accessdata = get_empty_accessdata();

    $accessdata['ra']['/'.SYSCONTEXTID] = array((int)$roleid => (int)$roleid);

    //
    // Overrides for the role IN ANY CONTEXTS
    // down to COURSE - not below -
    //
    $sql = "SELECT ctx.path,
                   rc.capability, rc.permission
              FROM {context} ctx
              JOIN {role_capabilities} rc ON rc.contextid = ctx.id
         LEFT JOIN {context} cctx
                   ON (cctx.contextlevel = ".CONTEXT_COURSE." AND ctx.path LIKE ".$DB->sql_concat('cctx.path',"'/%'").")
             WHERE rc.roleid = ? AND cctx.id IS NULL";
    $params = array($roleid);

    // we need extra caching in CLI scripts and cron
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $rd) {
        $k = "{$rd->path}:{$roleid}";
        $accessdata['rdef'][$k][$rd->capability] = (int)$rd->permission;
    }
    $rs->close();

    // share the role definitions
    foreach ($accessdata['rdef'] as $k=>$unused) {
        if (!isset($ACCESSLIB_PRIVATE->rolepermissions[$k])) {
            $ACCESSLIB_PRIVATE->rolepermissions[$k] = $accessdata['rdef'][$k];
        }
        $accessdata['rdef_count']++;
        $accessdata['rdef'][$k] =& $ACCESSLIB_PRIVATE->rolepermissions[$k];
    }

    return $accessdata;
}

/**
 * Get the default guest role, this is used for guest account,
 * search engine spiders, etc.
 *
 * @return stdClass role record
 */
function get_guest_role() {
    global $CFG, $DB;

    if (empty($CFG->guestroleid)) {
        if ($roles = $DB->get_records('role', array('archetype'=>'guest'))) {
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
            // somebody is messing with guest roles, remove incorrect setting and try to find a new one
            set_config('guestroleid', '');
            return get_guest_role();
        }
    }
}

/**
 * Check whether a user has a particular capability in a given context.
 *
 * For example:
 *      $context = context_module::instance($cm->id);
 *      has_capability('mod/forum:replypost', $context)
 *
 * By default checks the capabilities of the current user, but you can pass a
 * different userid. By default will return true for admin users, but you can override that with the fourth argument.
 *
 * Guest and not-logged-in users can never get any dangerous capability - that is any write capability
 * or capabilities with XSS, config or data loss risks.
 *
 * @category access
 *
 * @param string $capability the name of the capability to check. For example mod/forum:view
 * @param context $context the context to check the capability in. You normally get this with instance method of a context class.
 * @param integer|stdClass $user A user id or object. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignores effect of admin role assignment
 * @return boolean true if the user has this capability. Otherwise false.
 */
function has_capability($capability, context $context, $user = null, $doanything = true) {
    global $USER, $CFG, $SCRIPT, $ACCESSLIB_PRIVATE;

    if (during_initial_install()) {
        if ($SCRIPT === "/$CFG->admin/index.php" or $SCRIPT === "/$CFG->admin/cli/install.php" or $SCRIPT === "/$CFG->admin/cli/install_database.php") {
            // we are in an installer - roles can not work yet
            return true;
        } else {
            return false;
        }
    }

    if (strpos($capability, 'moodle/legacy:') === 0) {
        throw new coding_exception('Legacy capabilities can not be used any more!');
    }

    if (!is_bool($doanything)) {
        throw new coding_exception('Capability parameter "doanything" is wierd, only true or false is allowed. This has to be fixed in code.');
    }

    // capability must exist
    if (!$capinfo = get_capability_info($capability)) {
        debugging('Capability "'.$capability.'" was not found! This has to be fixed in code.');
        return false;
    }

    if (!isset($USER->id)) {
        // should never happen
        $USER->id = 0;
    }

    // make sure there is a real user specified
    if ($user === null) {
        $userid = $USER->id;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }

    // make sure forcelogin cuts off not-logged-in users if enabled
    if (!empty($CFG->forcelogin) and $userid == 0) {
        return false;
    }

    // make sure the guest account and not-logged-in users never get any risky caps no matter what the actual settings are.
    if (($capinfo->captype === 'write') or ($capinfo->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
        if (isguestuser($userid) or $userid == 0) {
            return false;
        }
    }

    // somehow make sure the user is not deleted and actually exists
    if ($userid != 0) {
        if ($userid == $USER->id and isset($USER->deleted)) {
            // this prevents one query per page, it is a bit of cheating,
            // but hopefully session is terminated properly once user is deleted
            if ($USER->deleted) {
                return false;
            }
        } else {
            if (!context_user::instance($userid, IGNORE_MISSING)) {
                // no user context == invalid userid
                return false;
            }
        }
    }

    // context path/depth must be valid
    if (empty($context->path) or $context->depth == 0) {
        // this should not happen often, each upgrade tries to rebuild the context paths
        debugging('Context id '.$context->id.' does not have valid path, please use build_context_path()');
        if (is_siteadmin($userid)) {
            return true;
        } else {
            return false;
        }
    }

    // Find out if user is admin - it is not possible to override the doanything in any way
    // and it is not possible to switch to admin role either.
    if ($doanything) {
        if (is_siteadmin($userid)) {
            if ($userid != $USER->id) {
                return true;
            }
            // make sure switchrole is not used in this context
            if (empty($USER->access['rsw'])) {
                return true;
            }
            $parts = explode('/', trim($context->path, '/'));
            $path = '';
            $switched = false;
            foreach ($parts as $part) {
                $path .= '/' . $part;
                if (!empty($USER->access['rsw'][$path])) {
                    $switched = true;
                    break;
                }
            }
            if (!$switched) {
                return true;
            }
            //ok, admin switched role in this context, let's use normal access control rules
        }
    }

    // Careful check for staleness...
    $context->reload_if_dirty();

    if ($USER->id == $userid) {
        if (!isset($USER->access)) {
            load_all_capabilities();
        }
        $access =& $USER->access;

    } else {
        // make sure user accessdata is really loaded
        get_user_accessdata($userid, true);
        $access =& $ACCESSLIB_PRIVATE->accessdatabyuser[$userid];
    }


    // Load accessdata for below-the-course context if necessary,
    // all contexts at and above all courses are already loaded
    if ($context->contextlevel != CONTEXT_COURSE and $coursecontext = $context->get_course_context(false)) {
        load_course_context($userid, $coursecontext, $access);
    }

    return has_capability_in_accessdata($capability, $context, $access);
}

/**
 * Check if the user has any one of several capabilities from a list.
 *
 * This is just a utility method that calls has_capability in a loop. Try to put
 * the capabilities that most users are likely to have first in the list for best
 * performance.
 *
 * @category access
 * @see has_capability()
 *
 * @param array $capabilities an array of capability names.
 * @param context $context the context to check the capability in. You normally get this with instance method of a context class.
 * @param integer|stdClass $user A user id or object. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignore effect of admin role assignment
 * @return boolean true if the user has any of these capabilities. Otherwise false.
 */
function has_any_capability(array $capabilities, context $context, $user = null, $doanything = true) {
    foreach ($capabilities as $capability) {
        if (has_capability($capability, $context, $user, $doanything)) {
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
 * @category access
 * @see has_capability()
 *
 * @param array $capabilities an array of capability names.
 * @param context $context the context to check the capability in. You normally get this with instance method of a context class.
 * @param integer|stdClass $user A user id or object. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignore effect of admin role assignment
 * @return boolean true if the user has all of these capabilities. Otherwise false.
 */
function has_all_capabilities(array $capabilities, context $context, $user = null, $doanything = true) {
    foreach ($capabilities as $capability) {
        if (!has_capability($capability, $context, $user, $doanything)) {
            return false;
        }
    }
    return true;
}

/**
 * Check if the user is an admin at the site level.
 *
 * Please note that use of proper capabilities is always encouraged,
 * this function is supposed to be used from core or for temporary hacks.
 *
 * @category access
 *
 * @param  int|stdClass  $user_or_id user id or user object
 * @return bool true if user is one of the administrators, false otherwise
 */
function is_siteadmin($user_or_id = null) {
    global $CFG, $USER;

    if ($user_or_id === null) {
        $user_or_id = $USER;
    }

    if (empty($user_or_id)) {
        return false;
    }
    if (!empty($user_or_id->id)) {
        $userid = $user_or_id->id;
    } else {
        $userid = $user_or_id;
    }

    // Because this script is called many times (150+ for course page) with
    // the same parameters, it is worth doing minor optimisations. This static
    // cache stores the value for a single userid, saving about 2ms from course
    // page load time without using significant memory. As the static cache
    // also includes the value it depends on, this cannot break unit tests.
    static $knownid, $knownresult, $knownsiteadmins;
    if ($knownid === $userid && $knownsiteadmins === $CFG->siteadmins) {
        return $knownresult;
    }
    $knownid = $userid;
    $knownsiteadmins = $CFG->siteadmins;

    $siteadmins = explode(',', $CFG->siteadmins);
    $knownresult = in_array($userid, $siteadmins);
    return $knownresult;
}

/**
 * Returns true if user has at least one role assign
 * of 'coursecontact' role (is potentially listed in some course descriptions).
 *
 * @param int $userid
 * @return bool
 */
function has_coursecontact_role($userid) {
    global $DB, $CFG;

    if (empty($CFG->coursecontact)) {
        return false;
    }
    $sql = "SELECT 1
              FROM {role_assignments}
             WHERE userid = :userid AND roleid IN ($CFG->coursecontact)";
    return $DB->record_exists_sql($sql, array('userid'=>$userid));
}

/**
 * Does the user have a capability to do something?
 *
 * Walk the accessdata array and return true/false.
 * Deals with prohibits, role switching, aggregating
 * capabilities, etc.
 *
 * The main feature of here is being FAST and with no
 * side effects.
 *
 * Notes:
 *
 * Switch Role merges with default role
 * ------------------------------------
 * If you are a teacher in course X, you have at least
 * teacher-in-X + defaultloggedinuser-sitewide. So in the
 * course you'll have techer+defaultloggedinuser.
 * We try to mimic that in switchrole.
 *
 * Permission evaluation
 * ---------------------
 * Originally there was an extremely complicated way
 * to determine the user access that dealt with
 * "locality" or role assignments and role overrides.
 * Now we simply evaluate access for each role separately
 * and then verify if user has at least one role with allow
 * and at the same time no role with prohibit.
 *
 * @access private
 * @param string $capability
 * @param context $context
 * @param array $accessdata
 * @return bool
 */
function has_capability_in_accessdata($capability, context $context, array &$accessdata) {
    global $CFG;

    // Build $paths as a list of current + all parent "paths" with order bottom-to-top
    $path = $context->path;
    $paths = array($path);
    while($path = rtrim($path, '0123456789')) {
        $path = rtrim($path, '/');
        if ($path === '') {
            break;
        }
        $paths[] = $path;
    }

    $roles = array();
    $switchedrole = false;

    // Find out if role switched
    if (!empty($accessdata['rsw'])) {
        // From the bottom up...
        foreach ($paths as $path) {
            if (isset($accessdata['rsw'][$path])) {
                // Found a switchrole assignment - check for that role _plus_ the default user role
                $roles = array($accessdata['rsw'][$path]=>null, $CFG->defaultuserroleid=>null);
                $switchedrole = true;
                break;
            }
        }
    }

    if (!$switchedrole) {
        // get all users roles in this context and above
        foreach ($paths as $path) {
            if (isset($accessdata['ra'][$path])) {
                foreach ($accessdata['ra'][$path] as $roleid) {
                    $roles[$roleid] = null;
                }
            }
        }
    }

    // Now find out what access is given to each role, going bottom-->up direction
    $allowed = false;
    foreach ($roles as $roleid => $ignored) {
        foreach ($paths as $path) {
            if (isset($accessdata['rdef']["{$path}:$roleid"][$capability])) {
                $perm = (int)$accessdata['rdef']["{$path}:$roleid"][$capability];
                if ($perm === CAP_PROHIBIT) {
                    // any CAP_PROHIBIT found means no permission for the user
                    return false;
                }
                if (is_null($roles[$roleid])) {
                    $roles[$roleid] = $perm;
                }
            }
        }
        // CAP_ALLOW in any role means the user has a permission, we continue only to detect prohibits
        $allowed = ($allowed or $roles[$roleid] === CAP_ALLOW);
    }

    return $allowed;
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
 * @param context $context the context to check the capability in. You normally get this with {@link get_context_instance}.
 * @param int $userid A user id. By default (null) checks the permissions of the current user.
 * @param bool $doanything If false, ignore effect of admin role assignment
 * @param string $errormessage The error string to to user. Defaults to 'nopermissions'.
 * @param string $stringfile The language file to load the error string from. Defaults to 'error'.
 * @return void terminates with an error if the user does not have the given capability.
 */
function require_capability($capability, context $context, $userid = null, $doanything = true,
                            $errormessage = 'nopermissions', $stringfile = '') {
    if (!has_capability($capability, $context, $userid, $doanything)) {
        throw new required_capability_exception($context, $capability, $errormessage, $stringfile);
    }
}

/**
 * Return a nested array showing role assignments
 * all relevant role capabilities for the user at
 * site/course_category/course levels
 *
 * We do _not_ delve deeper than courses because the number of
 * overrides at the module/block levels can be HUGE.
 *
 * [ra]   => [/path][roleid]=roleid
 * [rdef] => [/path:roleid][capability]=permission
 *
 * @access private
 * @param int $userid - the id of the user
 * @return array access info array
 */
function get_user_access_sitewide($userid) {
    global $CFG, $DB, $ACCESSLIB_PRIVATE;

    /* Get in a few cheap DB queries...
     * - role assignments
     * - relevant role caps
     *   - above and within this user's RAs
     *   - below this user's RAs - limited to course level
     */

    // raparents collects paths & roles we need to walk up the parenthood to build the minimal rdef
    $raparents = array();
    $accessdata = get_empty_accessdata();

    // start with the default role
    if (!empty($CFG->defaultuserroleid)) {
        $syscontext = context_system::instance();
        $accessdata['ra'][$syscontext->path][(int)$CFG->defaultuserroleid] = (int)$CFG->defaultuserroleid;
        $raparents[$CFG->defaultuserroleid][$syscontext->id] = $syscontext->id;
    }

    // load the "default frontpage role"
    if (!empty($CFG->defaultfrontpageroleid)) {
        $frontpagecontext = context_course::instance(get_site()->id);
        if ($frontpagecontext->path) {
            $accessdata['ra'][$frontpagecontext->path][(int)$CFG->defaultfrontpageroleid] = (int)$CFG->defaultfrontpageroleid;
            $raparents[$CFG->defaultfrontpageroleid][$frontpagecontext->id] = $frontpagecontext->id;
        }
    }

    // preload every assigned role at and above course context
    $sql = "SELECT ctx.path, ra.roleid, ra.contextid
              FROM {role_assignments} ra
              JOIN {context} ctx
                   ON ctx.id = ra.contextid
         LEFT JOIN {block_instances} bi
                   ON (ctx.contextlevel = ".CONTEXT_BLOCK." AND bi.id = ctx.instanceid)
         LEFT JOIN {context} bpctx
                   ON (bpctx.id = bi.parentcontextid)
             WHERE ra.userid = :userid
                   AND (ctx.contextlevel <= ".CONTEXT_COURSE." OR bpctx.contextlevel < ".CONTEXT_COURSE.")";
    $params = array('userid'=>$userid);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $ra) {
        // RAs leafs are arrays to support multi-role assignments...
        $accessdata['ra'][$ra->path][(int)$ra->roleid] = (int)$ra->roleid;
        $raparents[$ra->roleid][$ra->contextid] = $ra->contextid;
    }
    $rs->close();

    if (empty($raparents)) {
        return $accessdata;
    }

    // now get overrides of interesting roles in all interesting child contexts
    // hopefully we will not run out of SQL limits here,
    // users would have to have very many roles at/above course context...
    $sqls = array();
    $params = array();

    static $cp = 0;
    foreach ($raparents as $roleid=>$ras) {
        $cp++;
        list($sqlcids, $cids) = $DB->get_in_or_equal($ras, SQL_PARAMS_NAMED, 'c'.$cp.'_');
        $params = array_merge($params, $cids);
        $params['r'.$cp] = $roleid;
        $sqls[] = "(SELECT ctx.path, rc.roleid, rc.capability, rc.permission
                     FROM {role_capabilities} rc
                     JOIN {context} ctx
                          ON (ctx.id = rc.contextid)
                     JOIN {context} pctx
                          ON (pctx.id $sqlcids
                              AND (ctx.id = pctx.id
                                   OR ctx.path LIKE ".$DB->sql_concat('pctx.path',"'/%'")."
                                   OR pctx.path LIKE ".$DB->sql_concat('ctx.path',"'/%'")."))
                LEFT JOIN {block_instances} bi
                          ON (ctx.contextlevel = ".CONTEXT_BLOCK." AND bi.id = ctx.instanceid)
                LEFT JOIN {context} bpctx
                          ON (bpctx.id = bi.parentcontextid)
                    WHERE rc.roleid = :r{$cp}
                          AND (ctx.contextlevel <= ".CONTEXT_COURSE." OR bpctx.contextlevel < ".CONTEXT_COURSE.")
                   )";
    }

    // fixed capability order is necessary for rdef dedupe
    $rs = $DB->get_recordset_sql(implode("\nUNION\n", $sqls). "ORDER BY capability", $params);

    foreach ($rs as $rd) {
        $k = $rd->path.':'.$rd->roleid;
        $accessdata['rdef'][$k][$rd->capability] = (int)$rd->permission;
    }
    $rs->close();

    // share the role definitions
    foreach ($accessdata['rdef'] as $k=>$unused) {
        if (!isset($ACCESSLIB_PRIVATE->rolepermissions[$k])) {
            $ACCESSLIB_PRIVATE->rolepermissions[$k] = $accessdata['rdef'][$k];
        }
        $accessdata['rdef_count']++;
        $accessdata['rdef'][$k] =& $ACCESSLIB_PRIVATE->rolepermissions[$k];
    }

    return $accessdata;
}

/**
 * Add to the access ctrl array the data needed by a user for a given course.
 *
 * This function injects all course related access info into the accessdata array.
 *
 * @access private
 * @param int $userid the id of the user
 * @param context_course $coursecontext course context
 * @param array $accessdata accessdata array (modified)
 * @return void modifies $accessdata parameter
 */
function load_course_context($userid, context_course $coursecontext, &$accessdata) {
    global $DB, $CFG, $ACCESSLIB_PRIVATE;

    if (empty($coursecontext->path)) {
        // weird, this should not happen
        return;
    }

    if (isset($accessdata['loaded'][$coursecontext->instanceid])) {
        // already loaded, great!
        return;
    }

    $roles = array();

    if (empty($userid)) {
        if (!empty($CFG->notloggedinroleid)) {
            $roles[$CFG->notloggedinroleid] = $CFG->notloggedinroleid;
        }

    } else if (isguestuser($userid)) {
        if ($guestrole = get_guest_role()) {
            $roles[$guestrole->id] = $guestrole->id;
        }

    } else {
        // Interesting role assignments at, above and below the course context
        list($parentsaself, $params) = $DB->get_in_or_equal($coursecontext->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'pc_');
        $params['userid'] = $userid;
        $params['children'] = $coursecontext->path."/%";
        $sql = "SELECT ra.*, ctx.path
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ra.contextid = ctx.id
                 WHERE ra.userid = :userid AND (ctx.id $parentsaself OR ctx.path LIKE :children)";
        $rs = $DB->get_recordset_sql($sql, $params);

        // add missing role definitions
        foreach ($rs as $ra) {
            $accessdata['ra'][$ra->path][(int)$ra->roleid] = (int)$ra->roleid;
            $roles[$ra->roleid] = $ra->roleid;
        }
        $rs->close();

        // add the "default frontpage role" when on the frontpage
        if (!empty($CFG->defaultfrontpageroleid)) {
            $frontpagecontext = context_course::instance(get_site()->id);
            if ($frontpagecontext->id == $coursecontext->id) {
                $roles[$CFG->defaultfrontpageroleid] = $CFG->defaultfrontpageroleid;
            }
        }

        // do not forget the default role
        if (!empty($CFG->defaultuserroleid)) {
            $roles[$CFG->defaultuserroleid] = $CFG->defaultuserroleid;
        }
    }

    if (!$roles) {
        // weird, default roles must be missing...
        $accessdata['loaded'][$coursecontext->instanceid] = 1;
        return;
    }

    // now get overrides of interesting roles in all interesting contexts (this course + children + parents)
    $params = array('c'=>$coursecontext->id);
    list($parentsaself, $rparams) = $DB->get_in_or_equal($coursecontext->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'pc_');
    $params = array_merge($params, $rparams);
    list($roleids, $rparams) = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, 'r_');
    $params = array_merge($params, $rparams);

    $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
                 FROM {role_capabilities} rc
                 JOIN {context} ctx
                      ON (ctx.id = rc.contextid)
                 JOIN {context} cctx
                      ON (cctx.id = :c
                          AND (ctx.id $parentsaself OR ctx.path LIKE ".$DB->sql_concat('cctx.path',"'/%'")."))
                WHERE rc.roleid $roleids
             ORDER BY rc.capability"; // fixed capability order is necessary for rdef dedupe
    $rs = $DB->get_recordset_sql($sql, $params);

    $newrdefs = array();
    foreach ($rs as $rd) {
        $k = $rd->path.':'.$rd->roleid;
        if (isset($accessdata['rdef'][$k])) {
            continue;
        }
        $newrdefs[$k][$rd->capability] = (int)$rd->permission;
    }
    $rs->close();

    // share new role definitions
    foreach ($newrdefs as $k=>$unused) {
        if (!isset($ACCESSLIB_PRIVATE->rolepermissions[$k])) {
            $ACCESSLIB_PRIVATE->rolepermissions[$k] = $newrdefs[$k];
        }
        $accessdata['rdef_count']++;
        $accessdata['rdef'][$k] =& $ACCESSLIB_PRIVATE->rolepermissions[$k];
    }

    $accessdata['loaded'][$coursecontext->instanceid] = 1;

    // we want to deduplicate the USER->access from time to time, this looks like a good place,
    // because we have to do it before the end of session
    dedupe_user_access();
}

/**
 * Add to the access ctrl array the data needed by a role for a given context.
 *
 * The data is added in the rdef key.
 * This role-centric function is useful for role_switching
 * and temporary course roles.
 *
 * @access private
 * @param int $roleid the id of the user
 * @param context $context needs path!
 * @param array $accessdata accessdata array (is modified)
 * @return array
 */
function load_role_access_by_context($roleid, context $context, &$accessdata) {
    global $DB, $ACCESSLIB_PRIVATE;

    /* Get the relevant rolecaps into rdef
     * - relevant role caps
     *   - at ctx and above
     *   - below this ctx
     */

    if (empty($context->path)) {
        // weird, this should not happen
        return;
    }

    list($parentsaself, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'pc_');
    $params['roleid'] = $roleid;
    $params['childpath'] = $context->path.'/%';

    $sql = "SELECT ctx.path, rc.capability, rc.permission
              FROM {role_capabilities} rc
              JOIN {context} ctx ON (rc.contextid = ctx.id)
             WHERE rc.roleid = :roleid AND (ctx.id $parentsaself OR ctx.path LIKE :childpath)
          ORDER BY rc.capability"; // fixed capability order is necessary for rdef dedupe
    $rs = $DB->get_recordset_sql($sql, $params);

    $newrdefs = array();
    foreach ($rs as $rd) {
        $k = $rd->path.':'.$roleid;
        if (isset($accessdata['rdef'][$k])) {
            continue;
        }
        $newrdefs[$k][$rd->capability] = (int)$rd->permission;
    }
    $rs->close();

    // share new role definitions
    foreach ($newrdefs as $k=>$unused) {
        if (!isset($ACCESSLIB_PRIVATE->rolepermissions[$k])) {
            $ACCESSLIB_PRIVATE->rolepermissions[$k] = $newrdefs[$k];
        }
        $accessdata['rdef_count']++;
        $accessdata['rdef'][$k] =& $ACCESSLIB_PRIVATE->rolepermissions[$k];
    }
}

/**
 * Returns empty accessdata structure.
 *
 * @access private
 * @return array empt accessdata
 */
function get_empty_accessdata() {
    $accessdata               = array(); // named list
    $accessdata['ra']         = array();
    $accessdata['rdef']       = array();
    $accessdata['rdef_count'] = 0;       // this bloody hack is necessary because count($array) is slooooowwww in PHP
    $accessdata['rdef_lcc']   = 0;       // rdef_count during the last compression
    $accessdata['loaded']     = array(); // loaded course contexts
    $accessdata['time']       = time();
    $accessdata['rsw']        = array();

    return $accessdata;
}

/**
 * Get accessdata for a given user.
 *
 * @access private
 * @param int $userid
 * @param bool $preloadonly true means do not return access array
 * @return array accessdata
 */
function get_user_accessdata($userid, $preloadonly=false) {
    global $CFG, $ACCESSLIB_PRIVATE, $USER;

    if (!empty($USER->acces['rdef']) and empty($ACCESSLIB_PRIVATE->rolepermissions)) {
        // share rdef from USER session with rolepermissions cache in order to conserve memory
        foreach($USER->acces['rdef'] as $k=>$v) {
            $ACCESSLIB_PRIVATE->rolepermissions[$k] =& $USER->acces['rdef'][$k];
        }
        $ACCESSLIB_PRIVATE->accessdatabyuser[$USER->id] = $USER->acces;
    }

    if (!isset($ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
        if (empty($userid)) {
            if (!empty($CFG->notloggedinroleid)) {
                $accessdata = get_role_access($CFG->notloggedinroleid);
            } else {
                // weird
                return get_empty_accessdata();
            }

        } else if (isguestuser($userid)) {
            if ($guestrole = get_guest_role()) {
                $accessdata = get_role_access($guestrole->id);
            } else {
                //weird
                return get_empty_accessdata();
            }

        } else {
            $accessdata = get_user_access_sitewide($userid); // includes default role and frontpage role
        }

        $ACCESSLIB_PRIVATE->accessdatabyuser[$userid] = $accessdata;
    }

    if ($preloadonly) {
        return;
    } else {
        return $ACCESSLIB_PRIVATE->accessdatabyuser[$userid];
    }
}

/**
 * Try to minimise the size of $USER->access by eliminating duplicate override storage,
 * this function looks for contexts with the same overrides and shares them.
 *
 * @access private
 * @return void
 */
function dedupe_user_access() {
    global $USER;

    if (CLI_SCRIPT) {
        // no session in CLI --> no compression necessary
        return;
    }

    if (empty($USER->access['rdef_count'])) {
        // weird, this should not happen
        return;
    }

    // the rdef is growing only, we never remove stuff from it, the rdef_lcc helps us to detect new stuff in rdef
    if ($USER->access['rdef_count'] - $USER->access['rdef_lcc'] > 10) {
        // do not compress after each change, wait till there is more stuff to be done
        return;
    }

    $hashmap = array();
    foreach ($USER->access['rdef'] as $k=>$def) {
        $hash = sha1(serialize($def));
        if (isset($hashmap[$hash])) {
            $USER->access['rdef'][$k] =& $hashmap[$hash];
        } else {
            $hashmap[$hash] =& $USER->access['rdef'][$k];
        }
    }

    $USER->access['rdef_lcc'] = $USER->access['rdef_count'];
}

/**
 * A convenience function to completely load all the capabilities
 * for the current user. It is called from has_capability() and functions change permissions.
 *
 * Call it only _after_ you've setup $USER and called check_enrolment_plugins();
 * @see check_enrolment_plugins()
 *
 * @access private
 * @return void
 */
function load_all_capabilities() {
    global $USER;

    // roles not installed yet - we are in the middle of installation
    if (during_initial_install()) {
        return;
    }

    if (!isset($USER->id)) {
        // this should not happen
        $USER->id = 0;
    }

    unset($USER->access);
    $USER->access = get_user_accessdata($USER->id);

    // deduplicate the overrides to minimize session size
    dedupe_user_access();

    // Clear to force a refresh
    unset($USER->mycourses);

    // init/reset internal enrol caches - active course enrolments and temp access
    $USER->enrol = array('enrolled'=>array(), 'tempguest'=>array());
}

/**
 * A convenience function to completely reload all the capabilities
 * for the current user when roles have been updated in a relevant
 * context -- but PRESERVING switchroles and loginas.
 * This function resets all accesslib and context caches.
 *
 * That is - completely transparent to the user.
 *
 * Note: reloads $USER->access completely.
 *
 * @access private
 * @return void
 */
function reload_all_capabilities() {
    global $USER, $DB, $ACCESSLIB_PRIVATE;

    // copy switchroles
    $sw = array();
    if (!empty($USER->access['rsw'])) {
        $sw = $USER->access['rsw'];
    }

    accesslib_clear_all_caches(true);
    unset($USER->access);
    $ACCESSLIB_PRIVATE->dirtycontexts = array(); // prevent dirty flags refetching on this page

    load_all_capabilities();

    foreach ($sw as $path => $roleid) {
        if ($record = $DB->get_record('context', array('path'=>$path))) {
            $context = context::instance_by_id($record->id);
            role_switch($roleid, $context);
        }
    }
}

/**
 * Adds a temp role to current USER->access array.
 *
 * Useful for the "temporary guest" access we grant to logged-in users.
 * This is useful for enrol plugins only.
 *
 * @since 2.2
 * @param context_course $coursecontext
 * @param int $roleid
 * @return void
 */
function load_temp_course_role(context_course $coursecontext, $roleid) {
    global $USER, $SITE;

    if (empty($roleid)) {
        debugging('invalid role specified in load_temp_course_role()');
        return;
    }

    if ($coursecontext->instanceid == $SITE->id) {
        debugging('Can not use temp roles on the frontpage');
        return;
    }

    if (!isset($USER->access)) {
        load_all_capabilities();
    }

    $coursecontext->reload_if_dirty();

    if (isset($USER->access['ra'][$coursecontext->path][$roleid])) {
        return;
    }

    // load course stuff first
    load_course_context($USER->id, $coursecontext, $USER->access);

    $USER->access['ra'][$coursecontext->path][(int)$roleid] = (int)$roleid;

    load_role_access_by_context($roleid, $coursecontext, $USER->access);
}

/**
 * Removes any extra guest roles from current USER->access array.
 * This is useful for enrol plugins only.
 *
 * @since 2.2
 * @param context_course $coursecontext
 * @return void
 */
function remove_temp_course_roles(context_course $coursecontext) {
    global $DB, $USER, $SITE;

    if ($coursecontext->instanceid == $SITE->id) {
        debugging('Can not use temp roles on the frontpage');
        return;
    }

    if (empty($USER->access['ra'][$coursecontext->path])) {
        //no roles here, weird
        return;
    }

    $sql = "SELECT DISTINCT ra.roleid AS id
              FROM {role_assignments} ra
             WHERE ra.contextid = :contextid AND ra.userid = :userid";
    $ras = $DB->get_records_sql($sql, array('contextid'=>$coursecontext->id, 'userid'=>$USER->id));

    $USER->access['ra'][$coursecontext->path] = array();
    foreach($ras as $r) {
        $USER->access['ra'][$coursecontext->path][(int)$r->id] = (int)$r->id;
    }
}

/**
 * Returns array of all role archetypes.
 *
 * @return array
 */
function get_role_archetypes() {
    return array(
        'manager'        => 'manager',
        'coursecreator'  => 'coursecreator',
        'editingteacher' => 'editingteacher',
        'teacher'        => 'teacher',
        'student'        => 'student',
        'guest'          => 'guest',
        'user'           => 'user',
        'frontpage'      => 'frontpage'
    );
}

/**
 * Assign the defaults found in this capability definition to roles that have
 * the corresponding legacy capabilities assigned to them.
 *
 * @param string $capability
 * @param array $legacyperms an array in the format (example):
 *                      'guest' => CAP_PREVENT,
 *                      'student' => CAP_ALLOW,
 *                      'teacher' => CAP_ALLOW,
 *                      'editingteacher' => CAP_ALLOW,
 *                      'coursecreator' => CAP_ALLOW,
 *                      'manager' => CAP_ALLOW
 * @return boolean success or failure.
 */
function assign_legacy_capabilities($capability, $legacyperms) {

    $archetypes = get_role_archetypes();

    foreach ($legacyperms as $type => $perm) {

        $systemcontext = context_system::instance();
        if ($type === 'admin') {
            debugging('Legacy type admin in access.php was renamed to manager, please update the code.');
            $type = 'manager';
        }

        if (!array_key_exists($type, $archetypes)) {
            print_error('invalidlegacy', '', '', $type);
        }

        if ($roles = get_archetype_roles($type)) {
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
 * Verify capability risks.
 *
 * @param stdClass $capability a capability - a row from the capabilities table.
 * @return boolean whether this capability is safe - that is, whether people with the
 *      safeoverrides capability should be allowed to change it.
 */
function is_safe_capability($capability) {
    return !((RISK_DATALOSS | RISK_MANAGETRUST | RISK_CONFIG | RISK_XSS | RISK_PERSONAL) & $capability->riskbitmask);
}

/**
 * Get the local override (if any) for a given capability in a role in a context
 *
 * @param int $roleid
 * @param int $contextid
 * @param string $capability
 * @return stdClass local capability override
 */
function get_local_override($roleid, $contextid, $capability) {
    global $DB;
    return $DB->get_record('role_capabilities', array('roleid'=>$roleid, 'capability'=>$capability, 'contextid'=>$contextid));
}

/**
 * Returns context instance plus related course and cm instances
 *
 * @param int $contextid
 * @return array of ($context, $course, $cm)
 */
function get_context_info_array($contextid) {
    global $DB;

    $context = context::instance_by_id($contextid, MUST_EXIST);
    $course  = null;
    $cm      = null;

    if ($context->contextlevel == CONTEXT_COURSE) {
        $course = $DB->get_record('course', array('id'=>$context->instanceid), '*', MUST_EXIST);

    } else if ($context->contextlevel == CONTEXT_MODULE) {
        $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

    } else if ($context->contextlevel == CONTEXT_BLOCK) {
        $parent = $context->get_parent_context();

        if ($parent->contextlevel == CONTEXT_COURSE) {
            $course = $DB->get_record('course', array('id'=>$parent->instanceid), '*', MUST_EXIST);
        } else if ($parent->contextlevel == CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id('', $parent->instanceid, 0, false, MUST_EXIST);
            $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
        }
    }

    return array($context, $course, $cm);
}

/**
 * Function that creates a role
 *
 * @param string $name role name
 * @param string $shortname role short name
 * @param string $description role description
 * @param string $archetype
 * @return int id or dml_exception
 */
function create_role($name, $shortname, $description, $archetype = '') {
    global $DB;

    if (strpos($archetype, 'moodle/legacy:') !== false) {
        throw new coding_exception('Use new role archetype parameter in create_role() instead of old legacy capabilities.');
    }

    // verify role archetype actually exists
    $archetypes = get_role_archetypes();
    if (empty($archetypes[$archetype])) {
        $archetype = '';
    }

    // Insert the role record.
    $role = new stdClass();
    $role->name        = $name;
    $role->shortname   = $shortname;
    $role->description = $description;
    $role->archetype   = $archetype;

    //find free sortorder number
    $role->sortorder = $DB->get_field('role', 'MAX(sortorder) + 1', array());
    if (empty($role->sortorder)) {
        $role->sortorder = 1;
    }
    $id = $DB->insert_record('role', $role);

    return $id;
}

/**
 * Function that deletes a role and cleanups up after it
 *
 * @param int $roleid id of role to delete
 * @return bool always true
 */
function delete_role($roleid) {
    global $DB;

    // first unssign all users
    role_unassign_all(array('roleid'=>$roleid));

    // cleanup all references to this role, ignore errors
    $DB->delete_records('role_capabilities',   array('roleid'=>$roleid));
    $DB->delete_records('role_allow_assign',   array('roleid'=>$roleid));
    $DB->delete_records('role_allow_assign',   array('allowassign'=>$roleid));
    $DB->delete_records('role_allow_override', array('roleid'=>$roleid));
    $DB->delete_records('role_allow_override', array('allowoverride'=>$roleid));
    $DB->delete_records('role_names',          array('roleid'=>$roleid));
    $DB->delete_records('role_context_levels', array('roleid'=>$roleid));

    // finally delete the role itself
    // get this before the name is gone for logging
    $rolename = $DB->get_field('role', 'name', array('id'=>$roleid));

    $DB->delete_records('role', array('id'=>$roleid));

    add_to_log(SITEID, 'role', 'delete', 'admin/roles/action=delete&roleid='.$roleid, $rolename, '');

    return true;
}

/**
 * Function to write context specific overrides, or default capabilities.
 *
 * NOTE: use $context->mark_dirty() after this
 *
 * @param string $capability string name
 * @param int $permission CAP_ constants
 * @param int $roleid role id
 * @param int|context $contextid context id
 * @param bool $overwrite
 * @return bool always true or exception
 */
function assign_capability($capability, $permission, $roleid, $contextid, $overwrite = false) {
    global $USER, $DB;

    if ($contextid instanceof context) {
        $context = $contextid;
    } else {
        $context = context::instance_by_id($contextid);
    }

    if (empty($permission) || $permission == CAP_INHERIT) { // if permission is not set
        unassign_capability($capability, $roleid, $context->id);
        return true;
    }

    $existing = $DB->get_record('role_capabilities', array('contextid'=>$context->id, 'roleid'=>$roleid, 'capability'=>$capability));

    if ($existing and !$overwrite) {   // We want to keep whatever is there already
        return true;
    }

    $cap = new stdClass();
    $cap->contextid    = $context->id;
    $cap->roleid       = $roleid;
    $cap->capability   = $capability;
    $cap->permission   = $permission;
    $cap->timemodified = time();
    $cap->modifierid   = empty($USER->id) ? 0 : $USER->id;

    if ($existing) {
        $cap->id = $existing->id;
        $DB->update_record('role_capabilities', $cap);
    } else {
        if ($DB->record_exists('context', array('id'=>$context->id))) {
            $DB->insert_record('role_capabilities', $cap);
        }
    }
    return true;
}

/**
 * Unassign a capability from a role.
 *
 * NOTE: use $context->mark_dirty() after this
 *
 * @param string $capability the name of the capability
 * @param int $roleid the role id
 * @param int|context $contextid null means all contexts
 * @return boolean true or exception
 */
function unassign_capability($capability, $roleid, $contextid = null) {
    global $DB;

    if (!empty($contextid)) {
        if ($contextid instanceof context) {
            $context = $contextid;
        } else {
            $context = context::instance_by_id($contextid);
        }
        // delete from context rel, if this is the last override in this context
        $DB->delete_records('role_capabilities', array('capability'=>$capability, 'roleid'=>$roleid, 'contextid'=>$context->id));
    } else {
        $DB->delete_records('role_capabilities', array('capability'=>$capability, 'roleid'=>$roleid));
    }
    return true;
}

/**
 * Get the roles that have a given capability assigned to it
 *
 * This function does not resolve the actual permission of the capability.
 * It just checks for permissions and overrides.
 * Use get_roles_with_cap_in_context() if resolution is required.
 *
 * @param string $capability capability name (string)
 * @param string $permission optional, the permission defined for this capability
 *                      either CAP_ALLOW, CAP_PREVENT or CAP_PROHIBIT. Defaults to null which means any.
 * @param stdClass $context null means any
 * @return array of role records
 */
function get_roles_with_capability($capability, $permission = null, $context = null) {
    global $DB;

    if ($context) {
        $contexts = $context->get_parent_context_ids(true);
        list($insql, $params) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED, 'ctx');
        $contextsql = "AND rc.contextid $insql";
    } else {
        $params = array();
        $contextsql = '';
    }

    if ($permission) {
        $permissionsql = " AND rc.permission = :permission";
        $params['permission'] = $permission;
    } else {
        $permissionsql = '';
    }

    $sql = "SELECT r.*
              FROM {role} r
             WHERE r.id IN (SELECT rc.roleid
                              FROM {role_capabilities} rc
                             WHERE rc.capability = :capname
                                   $contextsql
                                   $permissionsql)";
    $params['capname'] = $capability;


    return $DB->get_records_sql($sql, $params);
}

/**
 * This function makes a role-assignment (a role for a user in a particular context)
 *
 * @param int $roleid the role of the id
 * @param int $userid userid
 * @param int|context $contextid id of the context
 * @param string $component example 'enrol_ldap', defaults to '' which means manual assignment,
 * @param int $itemid id of enrolment/auth plugin
 * @param string $timemodified defaults to current time
 * @return int new/existing id of the assignment
 */
function role_assign($roleid, $userid, $contextid, $component = '', $itemid = 0, $timemodified = '') {
    global $USER, $DB;

    // first of all detect if somebody is using old style parameters
    if ($contextid === 0 or is_numeric($component)) {
        throw new coding_exception('Invalid call to role_assign(), code needs to be updated to use new order of parameters');
    }

    // now validate all parameters
    if (empty($roleid)) {
        throw new coding_exception('Invalid call to role_assign(), roleid can not be empty');
    }

    if (empty($userid)) {
        throw new coding_exception('Invalid call to role_assign(), userid can not be empty');
    }

    if ($itemid) {
        if (strpos($component, '_') === false) {
            throw new coding_exception('Invalid call to role_assign(), component must start with plugin type such as"enrol_" when itemid specified', 'component:'.$component);
        }
    } else {
        $itemid = 0;
        if ($component !== '' and strpos($component, '_') === false) {
            throw new coding_exception('Invalid call to role_assign(), invalid component string', 'component:'.$component);
        }
    }

    if (!$DB->record_exists('user', array('id'=>$userid, 'deleted'=>0))) {
        throw new coding_exception('User ID does not exist or is deleted!', 'userid:'.$userid);
    }

    if ($contextid instanceof context) {
        $context = $contextid;
    } else {
        $context = context::instance_by_id($contextid, MUST_EXIST);
    }

    if (!$timemodified) {
        $timemodified = time();
    }

    // Check for existing entry
    // TODO: Revisit this sql_empty() use once Oracle bindings are improved. MDL-29765
    $component = ($component === '') ? $DB->sql_empty() : $component;
    $ras = $DB->get_records('role_assignments', array('roleid'=>$roleid, 'contextid'=>$context->id, 'userid'=>$userid, 'component'=>$component, 'itemid'=>$itemid), 'id');

    if ($ras) {
        // role already assigned - this should not happen
        if (count($ras) > 1) {
            // very weird - remove all duplicates!
            $ra = array_shift($ras);
            foreach ($ras as $r) {
                $DB->delete_records('role_assignments', array('id'=>$r->id));
            }
        } else {
            $ra = reset($ras);
        }

        // actually there is no need to update, reset anything or trigger any event, so just return
        return $ra->id;
    }

    // Create a new entry
    $ra = new stdClass();
    $ra->roleid       = $roleid;
    $ra->contextid    = $context->id;
    $ra->userid       = $userid;
    $ra->component    = $component;
    $ra->itemid       = $itemid;
    $ra->timemodified = $timemodified;
    $ra->modifierid   = empty($USER->id) ? 0 : $USER->id;

    $ra->id = $DB->insert_record('role_assignments', $ra);

    // mark context as dirty - again expensive, but needed
    $context->mark_dirty();

    if (!empty($USER->id) && $USER->id == $userid) {
        // If the user is the current user, then do full reload of capabilities too.
        reload_all_capabilities();
    }

    events_trigger('role_assigned', $ra);

    return $ra->id;
}

/**
 * Removes one role assignment
 *
 * @param int $roleid
 * @param int  $userid
 * @param int|context  $contextid
 * @param string $component
 * @param int  $itemid
 * @return void
 */
function role_unassign($roleid, $userid, $contextid, $component = '', $itemid = 0) {
    // first make sure the params make sense
    if ($roleid == 0 or $userid == 0 or $contextid == 0) {
        throw new coding_exception('Invalid call to role_unassign(), please use role_unassign_all() when removing multiple role assignments');
    }

    if ($itemid) {
        if (strpos($component, '_') === false) {
            throw new coding_exception('Invalid call to role_assign(), component must start with plugin type such as "enrol_" when itemid specified', 'component:'.$component);
        }
    } else {
        $itemid = 0;
        if ($component !== '' and strpos($component, '_') === false) {
            throw new coding_exception('Invalid call to role_assign(), invalid component string', 'component:'.$component);
        }
    }

    role_unassign_all(array('roleid'=>$roleid, 'userid'=>$userid, 'contextid'=>$contextid, 'component'=>$component, 'itemid'=>$itemid), false, false);
}

/**
 * Removes multiple role assignments, parameters may contain:
 *   'roleid', 'userid', 'contextid', 'component', 'enrolid'.
 *
 * @param array $params role assignment parameters
 * @param bool $subcontexts unassign in subcontexts too
 * @param bool $includemanual include manual role assignments too
 * @return void
 */
function role_unassign_all(array $params, $subcontexts = false, $includemanual = false) {
    global $USER, $CFG, $DB;

    if (!$params) {
        throw new coding_exception('Missing parameters in role_unsassign_all() call');
    }

    $allowed = array('roleid', 'userid', 'contextid', 'component', 'itemid');
    foreach ($params as $key=>$value) {
        if (!in_array($key, $allowed)) {
            throw new coding_exception('Unknown role_unsassign_all() parameter key', 'key:'.$key);
        }
    }

    if (isset($params['component']) and $params['component'] !== '' and strpos($params['component'], '_') === false) {
        throw new coding_exception('Invalid component paramter in role_unsassign_all() call', 'component:'.$params['component']);
    }

    if ($includemanual) {
        if (!isset($params['component']) or $params['component'] === '') {
            throw new coding_exception('include manual parameter requires component parameter in role_unsassign_all() call');
        }
    }

    if ($subcontexts) {
        if (empty($params['contextid'])) {
            throw new coding_exception('subcontexts paramtere requires component parameter in role_unsassign_all() call');
        }
    }

    // TODO: Revisit this sql_empty() use once Oracle bindings are improved. MDL-29765
    if (isset($params['component'])) {
        $params['component'] = ($params['component'] === '') ? $DB->sql_empty() : $params['component'];
    }
    $ras = $DB->get_records('role_assignments', $params);
    foreach($ras as $ra) {
        $DB->delete_records('role_assignments', array('id'=>$ra->id));
        if ($context = context::instance_by_id($ra->contextid, IGNORE_MISSING)) {
            // this is a bit expensive but necessary
            $context->mark_dirty();
            // If the user is the current user, then do full reload of capabilities too.
            if (!empty($USER->id) && $USER->id == $ra->userid) {
                reload_all_capabilities();
            }
        }
        events_trigger('role_unassigned', $ra);
    }
    unset($ras);

    // process subcontexts
    if ($subcontexts and $context = context::instance_by_id($params['contextid'], IGNORE_MISSING)) {
        if ($params['contextid'] instanceof context) {
            $context = $params['contextid'];
        } else {
            $context = context::instance_by_id($params['contextid'], IGNORE_MISSING);
        }

        if ($context) {
            $contexts = $context->get_child_contexts();
            $mparams = $params;
            foreach($contexts as $context) {
                $mparams['contextid'] = $context->id;
                $ras = $DB->get_records('role_assignments', $mparams);
                foreach($ras as $ra) {
                    $DB->delete_records('role_assignments', array('id'=>$ra->id));
                    // this is a bit expensive but necessary
                    $context->mark_dirty();
                    // If the user is the current user, then do full reload of capabilities too.
                    if (!empty($USER->id) && $USER->id == $ra->userid) {
                        reload_all_capabilities();
                    }
                    events_trigger('role_unassigned', $ra);
                }
            }
        }
    }

    // do this once more for all manual role assignments
    if ($includemanual) {
        $params['component'] = '';
        role_unassign_all($params, $subcontexts, false);
    }
}

/**
 * Determines if a user is currently logged in
 *
 * @category   access
 *
 * @return bool
 */
function isloggedin() {
    global $USER;

    return (!empty($USER->id));
}

/**
 * Determines if a user is logged in as real guest user with username 'guest'.
 *
 * @category   access
 *
 * @param int|object $user mixed user object or id, $USER if not specified
 * @return bool true if user is the real guest user, false if not logged in or other user
 */
function isguestuser($user = null) {
    global $USER, $DB, $CFG;

    // make sure we have the user id cached in config table, because we are going to use it a lot
    if (empty($CFG->siteguest)) {
        if (!$guestid = $DB->get_field('user', 'id', array('username'=>'guest', 'mnethostid'=>$CFG->mnet_localhost_id))) {
            // guest does not exist yet, weird
            return false;
        }
        set_config('siteguest', $guestid);
    }
    if ($user === null) {
        $user = $USER;
    }

    if ($user === null) {
        // happens when setting the $USER
        return false;

    } else if (is_numeric($user)) {
        return ($CFG->siteguest == $user);

    } else if (is_object($user)) {
        if (empty($user->id)) {
            return false; // not logged in means is not be guest
        } else {
            return ($CFG->siteguest == $user->id);
        }

    } else {
        throw new coding_exception('Invalid user parameter supplied for isguestuser() function!');
    }
}

/**
 * Does user have a (temporary or real) guest access to course?
 *
 * @category   access
 *
 * @param context $context
 * @param stdClass|int $user
 * @return bool
 */
function is_guest(context $context, $user = null) {
    global $USER;

    // first find the course context
    $coursecontext = $context->get_course_context();

    // make sure there is a real user specified
    if ($user === null) {
        $userid = isset($USER->id) ? $USER->id : 0;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }

    if (isguestuser($userid)) {
        // can not inspect or be enrolled
        return true;
    }

    if (has_capability('moodle/course:view', $coursecontext, $user)) {
        // viewing users appear out of nowhere, they are neither guests nor participants
        return false;
    }

    // consider only real active enrolments here
    if (is_enrolled($coursecontext, $user, '', true)) {
        return false;
    }

    return true;
}

/**
 * Returns true if the user has moodle/course:view capability in the course,
 * this is intended for admins, managers (aka small admins), inspectors, etc.
 *
 * @category   access
 *
 * @param context $context
 * @param int|stdClass $user if null $USER is used
 * @param string $withcapability extra capability name
 * @return bool
 */
function is_viewing(context $context, $user = null, $withcapability = '') {
    // first find the course context
    $coursecontext = $context->get_course_context();

    if (isguestuser($user)) {
        // can not inspect
        return false;
    }

    if (!has_capability('moodle/course:view', $coursecontext, $user)) {
        // admins are allowed to inspect courses
        return false;
    }

    if ($withcapability and !has_capability($withcapability, $context, $user)) {
        // site admins always have the capability, but the enrolment above blocks
        return false;
    }

    return true;
}

/**
 * Returns true if user is enrolled (is participating) in course
 * this is intended for students and teachers.
 *
 * Since 2.2 the result for active enrolments and current user are cached.
 *
 * @package   core_enrol
 * @category  access
 *
 * @param context $context
 * @param int|stdClass $user if null $USER is used, otherwise user object or id expected
 * @param string $withcapability extra capability name
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return bool
 */
function is_enrolled(context $context, $user = null, $withcapability = '', $onlyactive = false) {
    global $USER, $DB;

    // first find the course context
    $coursecontext = $context->get_course_context();

    // make sure there is a real user specified
    if ($user === null) {
        $userid = isset($USER->id) ? $USER->id : 0;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }

    if (empty($userid)) {
        // not-logged-in!
        return false;
    } else if (isguestuser($userid)) {
        // guest account can not be enrolled anywhere
        return false;
    }

    if ($coursecontext->instanceid == SITEID) {
        // everybody participates on frontpage
    } else {
        // try cached info first - the enrolled flag is set only when active enrolment present
        if ($USER->id == $userid) {
            $coursecontext->reload_if_dirty();
            if (isset($USER->enrol['enrolled'][$coursecontext->instanceid])) {
                if ($USER->enrol['enrolled'][$coursecontext->instanceid] > time()) {
                    if ($withcapability and !has_capability($withcapability, $context, $userid)) {
                        return false;
                    }
                    return true;
                }
            }
        }

        if ($onlyactive) {
            // look for active enrolments only
            $until = enrol_get_enrolment_end($coursecontext->instanceid, $userid);

            if ($until === false) {
                return false;
            }

            if ($USER->id == $userid) {
                if ($until == 0) {
                    $until = ENROL_MAX_TIMESTAMP;
                }
                $USER->enrol['enrolled'][$coursecontext->instanceid] = $until;
                if (isset($USER->enrol['tempguest'][$coursecontext->instanceid])) {
                    unset($USER->enrol['tempguest'][$coursecontext->instanceid]);
                    remove_temp_course_roles($coursecontext);
                }
            }

        } else {
            // any enrolment is good for us here, even outdated, disabled or inactive
            $sql = "SELECT 'x'
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                      JOIN {user} u ON u.id = ue.userid
                     WHERE ue.userid = :userid AND u.deleted = 0";
            $params = array('userid'=>$userid, 'courseid'=>$coursecontext->instanceid);
            if (!$DB->record_exists_sql($sql, $params)) {
                return false;
            }
        }
    }

    if ($withcapability and !has_capability($withcapability, $context, $userid)) {
        return false;
    }

    return true;
}

/**
 * Returns true if the user is able to access the course.
 *
 * This function is in no way, shape, or form a substitute for require_login.
 * It should only be used in circumstances where it is not possible to call require_login
 * such as the navigation.
 *
 * This function checks many of the methods of access to a course such as the view
 * capability, enrollments, and guest access. It also makes use of the cache
 * generated by require_login for guest access.
 *
 * The flags within the $USER object that are used here should NEVER be used outside
 * of this function can_access_course and require_login. Doing so WILL break future
 * versions.
 *
 * @param stdClass $course record
 * @param stdClass|int|null $user user record or id, current user if null
 * @param string $withcapability Check for this capability as well.
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return boolean Returns true if the user is able to access the course
 */
function can_access_course(stdClass $course, $user = null, $withcapability = '', $onlyactive = false) {
    global $DB, $USER;

    // this function originally accepted $coursecontext parameter
    if ($course instanceof context) {
        if ($course instanceof context_course) {
            debugging('deprecated context parameter, please use $course record');
            $coursecontext = $course;
            $course = $DB->get_record('course', array('id'=>$coursecontext->instanceid));
        } else {
            debugging('Invalid context parameter, please use $course record');
            return false;
        }
    } else {
        $coursecontext = context_course::instance($course->id);
    }

    if (!isset($USER->id)) {
        // should never happen
        $USER->id = 0;
    }

    // make sure there is a user specified
    if ($user === null) {
        $userid = $USER->id;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }
    unset($user);

    if ($withcapability and !has_capability($withcapability, $coursecontext, $userid)) {
        return false;
    }

    if ($userid == $USER->id) {
        if (!empty($USER->access['rsw'][$coursecontext->path])) {
            // the fact that somebody switched role means they can access the course no matter to what role they switched
            return true;
        }
    }

    if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext, $userid)) {
        return false;
    }

    if (is_viewing($coursecontext, $userid)) {
        return true;
    }

    if ($userid != $USER->id) {
        // for performance reasons we do not verify temporary guest access for other users, sorry...
        return is_enrolled($coursecontext, $userid, '', $onlyactive);
    }

    // === from here we deal only with $USER ===

    $coursecontext->reload_if_dirty();

    if (isset($USER->enrol['enrolled'][$course->id])) {
        if ($USER->enrol['enrolled'][$course->id] > time()) {
            return true;
        }
    }
    if (isset($USER->enrol['tempguest'][$course->id])) {
        if ($USER->enrol['tempguest'][$course->id] > time()) {
            return true;
        }
    }

    if (is_enrolled($coursecontext, $USER, '', $onlyactive)) {
        return true;
    }

    // if not enrolled try to gain temporary guest access
    $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder, id ASC');
    $enrols = enrol_get_plugins(true);
    foreach($instances as $instance) {
        if (!isset($enrols[$instance->enrol])) {
            continue;
        }
        // Get a duration for the guest access, a timestamp in the future, 0 (always) or false.
        $until = $enrols[$instance->enrol]->try_guestaccess($instance);
        if ($until !== false and $until > time()) {
            $USER->enrol['tempguest'][$course->id] = $until;
            return true;
        }
    }
    if (isset($USER->enrol['tempguest'][$course->id])) {
        unset($USER->enrol['tempguest'][$course->id]);
        remove_temp_course_roles($coursecontext);
    }

    return false;
}

/**
 * Returns array with sql code and parameters returning all ids
 * of users enrolled into course.
 *
 * This function is using 'eu[0-9]+_' prefix for table names and parameters.
 *
 * @package   core_enrol
 * @category  access
 *
 * @param context $context
 * @param string $withcapability
 * @param int $groupid 0 means ignore groups, any other value limits the result by group id
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return array list($sql, $params)
 */
function get_enrolled_sql(context $context, $withcapability = '', $groupid = 0, $onlyactive = false) {
    global $DB, $CFG;

    // use unique prefix just in case somebody makes some SQL magic with the result
    static $i = 0;
    $i++;
    $prefix = 'eu'.$i.'_';

    // first find the course context
    $coursecontext = $context->get_course_context();

    $isfrontpage = ($coursecontext->instanceid == SITEID);

    $joins  = array();
    $wheres = array();
    $params = array();

    list($contextids, $contextpaths) = get_context_info_list($context);

    // get all relevant capability info for all roles
    if ($withcapability) {
        list($incontexts, $cparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED, 'ctx');
        $cparams['cap'] = $withcapability;

        $defs = array();
        $sql = "SELECT rc.id, rc.roleid, rc.permission, ctx.path
                  FROM {role_capabilities} rc
                  JOIN {context} ctx on rc.contextid = ctx.id
                 WHERE rc.contextid $incontexts AND rc.capability = :cap";
        $rcs = $DB->get_records_sql($sql, $cparams);
        foreach ($rcs as $rc) {
            $defs[$rc->path][$rc->roleid] = $rc->permission;
        }

        $access = array();
        if (!empty($defs)) {
            foreach ($contextpaths as $path) {
                if (empty($defs[$path])) {
                    continue;
                }
                foreach($defs[$path] as $roleid => $perm) {
                    if ($perm == CAP_PROHIBIT) {
                        $access[$roleid] = CAP_PROHIBIT;
                        continue;
                    }
                    if (!isset($access[$roleid])) {
                        $access[$roleid] = (int)$perm;
                    }
                }
            }
        }

        unset($defs);

        // make lists of roles that are needed and prohibited
        $needed     = array(); // one of these is enough
        $prohibited = array(); // must not have any of these
        foreach ($access as $roleid => $perm) {
            if ($perm == CAP_PROHIBIT) {
                unset($needed[$roleid]);
                $prohibited[$roleid] = true;
            } else if ($perm == CAP_ALLOW and empty($prohibited[$roleid])) {
                $needed[$roleid] = true;
            }
        }

        $defaultuserroleid      = isset($CFG->defaultuserroleid) ? $CFG->defaultuserroleid : 0;
        $defaultfrontpageroleid = isset($CFG->defaultfrontpageroleid) ? $CFG->defaultfrontpageroleid : 0;

        $nobody = false;

        if ($isfrontpage) {
            if (!empty($prohibited[$defaultuserroleid]) or !empty($prohibited[$defaultfrontpageroleid])) {
                $nobody = true;
            } else if (!empty($needed[$defaultuserroleid]) or !empty($needed[$defaultfrontpageroleid])) {
                // everybody not having prohibit has the capability
                $needed = array();
            } else if (empty($needed)) {
                $nobody = true;
            }
        } else {
            if (!empty($prohibited[$defaultuserroleid])) {
                $nobody = true;
            } else if (!empty($needed[$defaultuserroleid])) {
                // everybody not having prohibit has the capability
                $needed = array();
            } else if (empty($needed)) {
                $nobody = true;
            }
        }

        if ($nobody) {
            // nobody can match so return some SQL that does not return any results
            $wheres[] = "1 = 2";

        } else {

            if ($needed) {
                $ctxids = implode(',', $contextids);
                $roleids = implode(',', array_keys($needed));
                $joins[] = "JOIN {role_assignments} {$prefix}ra3 ON ({$prefix}ra3.userid = {$prefix}u.id AND {$prefix}ra3.roleid IN ($roleids) AND {$prefix}ra3.contextid IN ($ctxids))";
            }

            if ($prohibited) {
                $ctxids = implode(',', $contextids);
                $roleids = implode(',', array_keys($prohibited));
                $joins[] = "LEFT JOIN {role_assignments} {$prefix}ra4 ON ({$prefix}ra4.userid = {$prefix}u.id AND {$prefix}ra4.roleid IN ($roleids) AND {$prefix}ra4.contextid IN ($ctxids))";
                $wheres[] = "{$prefix}ra4.id IS NULL";
            }

            if ($groupid) {
                $joins[] = "JOIN {groups_members} {$prefix}gm ON ({$prefix}gm.userid = {$prefix}u.id AND {$prefix}gm.groupid = :{$prefix}gmid)";
                $params["{$prefix}gmid"] = $groupid;
            }
        }

    } else {
        if ($groupid) {
            $joins[] = "JOIN {groups_members} {$prefix}gm ON ({$prefix}gm.userid = {$prefix}u.id AND {$prefix}gm.groupid = :{$prefix}gmid)";
            $params["{$prefix}gmid"] = $groupid;
        }
    }

    $wheres[] = "{$prefix}u.deleted = 0 AND {$prefix}u.id <> :{$prefix}guestid";
    $params["{$prefix}guestid"] = $CFG->siteguest;

    if ($isfrontpage) {
        // all users are "enrolled" on the frontpage
    } else {
        $joins[] = "JOIN {user_enrolments} {$prefix}ue ON {$prefix}ue.userid = {$prefix}u.id";
        $joins[] = "JOIN {enrol} {$prefix}e ON ({$prefix}e.id = {$prefix}ue.enrolid AND {$prefix}e.courseid = :{$prefix}courseid)";
        $params[$prefix.'courseid'] = $coursecontext->instanceid;

        if ($onlyactive) {
            $wheres[] = "{$prefix}ue.status = :{$prefix}active AND {$prefix}e.status = :{$prefix}enabled";
            $wheres[] = "{$prefix}ue.timestart < :{$prefix}now1 AND ({$prefix}ue.timeend = 0 OR {$prefix}ue.timeend > :{$prefix}now2)";
            $now = round(time(), -2); // rounding helps caching in DB
            $params = array_merge($params, array($prefix.'enabled'=>ENROL_INSTANCE_ENABLED,
                                                 $prefix.'active'=>ENROL_USER_ACTIVE,
                                                 $prefix.'now1'=>$now, $prefix.'now2'=>$now));
        }
    }

    $joins = implode("\n", $joins);
    $wheres = "WHERE ".implode(" AND ", $wheres);

    $sql = "SELECT DISTINCT {$prefix}u.id
              FROM {user} {$prefix}u
            $joins
           $wheres";

    return array($sql, $params);
}

/**
 * Returns list of users enrolled into course.
 *
 * @package   core_enrol
 * @category  access
 *
 * @param context $context
 * @param string $withcapability
 * @param int $groupid 0 means ignore groups, any other value limits the result by group id
 * @param string $userfields requested user record fields
 * @param string $orderby
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return array of user records
 */
function get_enrolled_users(context $context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = null, $limitfrom = 0, $limitnum = 0) {
    global $DB;

    list($esql, $params) = get_enrolled_sql($context, $withcapability, $groupid);
    $sql = "SELECT $userfields
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
             WHERE u.deleted = 0";

    if ($orderby) {
        $sql = "$sql ORDER BY $orderby";
    } else {
        list($sort, $sortparams) = users_order_by_sql('u');
        $sql = "$sql ORDER BY $sort";
        $params = array_merge($params, $sortparams);
    }

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Counts list of users enrolled into course (as per above function)
 *
 * @package   core_enrol
 * @category  access
 *
 * @param context $context
 * @param string $withcapability
 * @param int $groupid 0 means ignore groups, any other value limits the result by group id
 * @return array of user records
 */
function count_enrolled_users(context $context, $withcapability = '', $groupid = 0) {
    global $DB;

    list($esql, $params) = get_enrolled_sql($context, $withcapability, $groupid);
    $sql = "SELECT count(u.id)
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
             WHERE u.deleted = 0";

    return $DB->count_records_sql($sql, $params);
}

/**
 * Loads the capability definitions for the component (from file).
 *
 * Loads the capability definitions for the component (from file). If no
 * capabilities are defined for the component, we simply return an empty array.
 *
 * @access private
 * @param string $component full plugin name, examples: 'moodle', 'mod_forum'
 * @return array array of capabilities
 */
function load_capability_def($component) {
    $defpath = get_component_directory($component).'/db/access.php';

    $capabilities = array();
    if (file_exists($defpath)) {
        require($defpath);
        if (!empty(${$component.'_capabilities'})) {
            // BC capability array name
            // since 2.0 we prefer $capabilities instead - it is easier to use and matches db/* files
            debugging('componentname_capabilities array is deprecated, please use $capabilities array only in access.php files');
            $capabilities = ${$component.'_capabilities'};
        }
    }

    return $capabilities;
}

/**
 * Gets the capabilities that have been cached in the database for this component.
 *
 * @access private
 * @param string $component - examples: 'moodle', 'mod_forum'
 * @return array array of capabilities
 */
function get_cached_capabilities($component = 'moodle') {
    global $DB;
    return $DB->get_records('capabilities', array('component'=>$component));
}

/**
 * Returns default capabilities for given role archetype.
 *
 * @param string $archetype role archetype
 * @return array
 */
function get_default_capabilities($archetype) {
    global $DB;

    if (!$archetype) {
        return array();
    }

    $alldefs = array();
    $defaults = array();
    $components = array();
    $allcaps = $DB->get_records('capabilities');

    foreach ($allcaps as $cap) {
        if (!in_array($cap->component, $components)) {
            $components[] = $cap->component;
            $alldefs = array_merge($alldefs, load_capability_def($cap->component));
        }
    }
    foreach($alldefs as $name=>$def) {
        // Use array 'archetypes if available. Only if not specified, use 'legacy'.
        if (isset($def['archetypes'])) {
            if (isset($def['archetypes'][$archetype])) {
                $defaults[$name] = $def['archetypes'][$archetype];
            }
        // 'legacy' is for backward compatibility with 1.9 access.php
        } else {
            if (isset($def['legacy'][$archetype])) {
                $defaults[$name] = $def['legacy'][$archetype];
            }
        }
    }

    return $defaults;
}

/**
 * Reset role capabilities to default according to selected role archetype.
 * If no archetype selected, removes all capabilities.
 *
 * @param int $roleid
 * @return void
 */
function reset_role_capabilities($roleid) {
    global $DB;

    $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);
    $defaultcaps = get_default_capabilities($role->archetype);

    $systemcontext = context_system::instance();

    $DB->delete_records('role_capabilities', array('roleid'=>$roleid));

    foreach($defaultcaps as $cap=>$permission) {
        assign_capability($cap, $permission, $roleid, $systemcontext->id);
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
 * @access private
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean true if success, exception in case of any problems
 */
function update_capabilities($component = 'moodle') {
    global $DB, $OUTPUT;

    $storedcaps = array();

    $filecaps = load_capability_def($component);
    foreach($filecaps as $capname=>$unused) {
        if (!preg_match('|^[a-z]+/[a-z_0-9]+:[a-z_0-9]+$|', $capname)) {
            debugging("Coding problem: Invalid capability name '$capname', use 'clonepermissionsfrom' field for migration.");
        }
    }

    $cachedcaps = get_cached_capabilities($component);
    if ($cachedcaps) {
        foreach ($cachedcaps as $cachedcap) {
            array_push($storedcaps, $cachedcap->name);
            // update risk bitmasks and context levels in existing capabilities if needed
            if (array_key_exists($cachedcap->name, $filecaps)) {
                if (!array_key_exists('riskbitmask', $filecaps[$cachedcap->name])) {
                    $filecaps[$cachedcap->name]['riskbitmask'] = 0; // no risk if not specified
                }
                if ($cachedcap->captype != $filecaps[$cachedcap->name]['captype']) {
                    $updatecap = new stdClass();
                    $updatecap->id = $cachedcap->id;
                    $updatecap->captype = $filecaps[$cachedcap->name]['captype'];
                    $DB->update_record('capabilities', $updatecap);
                }
                if ($cachedcap->riskbitmask != $filecaps[$cachedcap->name]['riskbitmask']) {
                    $updatecap = new stdClass();
                    $updatecap->id = $cachedcap->id;
                    $updatecap->riskbitmask = $filecaps[$cachedcap->name]['riskbitmask'];
                    $DB->update_record('capabilities', $updatecap);
                }

                if (!array_key_exists('contextlevel', $filecaps[$cachedcap->name])) {
                    $filecaps[$cachedcap->name]['contextlevel'] = 0; // no context level defined
                }
                if ($cachedcap->contextlevel != $filecaps[$cachedcap->name]['contextlevel']) {
                    $updatecap = new stdClass();
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
    $existingcaps = $DB->get_records_menu('capabilities', array(), 'id', 'id, name');
    foreach ($newcaps as $capname => $capdef) {
        $capability = new stdClass();
        $capability->name         = $capname;
        $capability->captype      = $capdef['captype'];
        $capability->contextlevel = $capdef['contextlevel'];
        $capability->component    = $component;
        $capability->riskbitmask  = $capdef['riskbitmask'];

        $DB->insert_record('capabilities', $capability, false);

        if (isset($capdef['clonepermissionsfrom']) && in_array($capdef['clonepermissionsfrom'], $existingcaps)){
            if ($rolecapabilities = $DB->get_records('role_capabilities', array('capability'=>$capdef['clonepermissionsfrom']))){
                foreach ($rolecapabilities as $rolecapability){
                    //assign_capability will update rather than insert if capability exists
                    if (!assign_capability($capname, $rolecapability->permission,
                                            $rolecapability->roleid, $rolecapability->contextid, true)){
                         echo $OUTPUT->notification('Could not clone capabilities for '.$capname);
                    }
                }
            }
        // we ignore archetype key if we have cloned permissions
        } else if (isset($capdef['archetypes']) && is_array($capdef['archetypes'])) {
            assign_legacy_capabilities($capname, $capdef['archetypes']);
        // 'legacy' is for backward compatibility with 1.9 access.php
        } else if (isset($capdef['legacy']) && is_array($capdef['legacy'])) {
            assign_legacy_capabilities($capname, $capdef['legacy']);
        }
    }
    // Are there any capabilities that have been removed from the file
    // definition that we need to delete from the stored capabilities and
    // role assignments?
    capabilities_cleanup($component, $filecaps);

    // reset static caches
    accesslib_clear_all_caches(false);

    return true;
}

/**
 * Deletes cached capabilities that are no longer needed by the component.
 * Also unassigns these capabilities from any roles that have them.
 *
 * @access private
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @param array $newcapdef array of the new capability definitions that will be
 *                     compared with the cached capabilities
 * @return int number of deprecated capabilities that have been removed
 */
function capabilities_cleanup($component, $newcapdef = null) {
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
        'riskconfig'      => RISK_CONFIG,
        'riskxss'         => RISK_XSS,
        'riskpersonal'    => RISK_PERSONAL,
        'riskspam'        => RISK_SPAM,
        'riskdataloss'    => RISK_DATALOSS,
    );
}

/**
 * Return a link to moodle docs for a given capability name
 *
 * @param stdClass $capability a capability - a row from the mdl_capabilities table.
 * @return string the human-readable capability name as a link to Moodle Docs.
 */
function get_capability_docs_link($capability) {
    $url = get_docs_url('Capabilities/' . $capability->name);
    return '<a onclick="this.target=\'docspopup\'" href="' . $url . '">' . get_capability_string($capability->name) . '</a>';
}

/**
 * This function pulls out all the resolved capabilities (overrides and
 * defaults) of a role used in capability overrides in contexts at a given
 * context.
 *
 * @param int $roleid
 * @param context $context
 * @param string $cap capability, optional, defaults to ''
 * @return array Array of capabilities
 */
function role_context_capabilities($roleid, context $context, $cap = '') {
    global $DB;

    $contexts = $context->get_parent_context_ids(true);
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
 * Constructs array with contextids as first parameter and context paths,
 * in both cases bottom top including self.
 *
 * @access private
 * @param context $context
 * @return array
 */
function get_context_info_list(context $context) {
    $contextids = explode('/', ltrim($context->path, '/'));
    $contextpaths = array();
    $contextids2 = $contextids;
    while ($contextids2) {
        $contextpaths[] = '/' . implode('/', $contextids2);
        array_pop($contextids2);
    }
    return array($contextids, $contextpaths);
}

/**
 * Check if context is the front page context or a context inside it
 *
 * Returns true if this context is the front page context, or a context inside it,
 * otherwise false.
 *
 * @param context $context a context object.
 * @return bool
 */
function is_inside_frontpage(context $context) {
    $frontpagecontext = context_course::instance(SITEID);
    return strpos($context->path . '/', $frontpagecontext->path . '/') === 0;
}

/**
 * Returns capability information (cached)
 *
 * @param string $capabilityname
 * @return stdClass or null if capability not found
 */
function get_capability_info($capabilityname) {
    global $ACCESSLIB_PRIVATE, $DB; // one request per page only

    //TODO: MUC - this could be cached in shared memory, it would eliminate 1 query per page

    if (empty($ACCESSLIB_PRIVATE->capabilities)) {
        $ACCESSLIB_PRIVATE->capabilities = array();
        $caps = $DB->get_records('capabilities', array(), 'id, name, captype, riskbitmask');
        foreach ($caps as $cap) {
            $capname = $cap->name;
            unset($cap->id);
            unset($cap->name);
            $cap->riskbitmask = (int)$cap->riskbitmask;
            $ACCESSLIB_PRIVATE->capabilities[$capname] = $cap;
        }
    }

    return isset($ACCESSLIB_PRIVATE->capabilities[$capabilityname]) ? $ACCESSLIB_PRIVATE->capabilities[$capabilityname] : null;
}

/**
 * Returns the human-readable, translated version of the capability.
 * Basically a big switch statement.
 *
 * @param string $capabilityname e.g. mod/choice:readresponses
 * @return string
 */
function get_capability_string($capabilityname) {

    // Typical capability name is 'plugintype/pluginname:capabilityname'
    list($type, $name, $capname) = preg_split('|[/:]|', $capabilityname);

    if ($type === 'moodle') {
        $component = 'core_role';
    } else if ($type === 'quizreport') {
        //ugly hack!!
        $component = 'quiz_'.$name;
    } else {
        $component = $type.'_'.$name;
    }

    $stringname = $name.':'.$capname;

    if ($component === 'core_role' or get_string_manager()->string_exists($stringname, $component)) {
        return get_string($stringname, $component);
    }

    $dir = get_component_directory($component);
    if (!file_exists($dir)) {
        // plugin broken or does not exist, do not bother with printing of debug message
        return $capabilityname.' ???';
    }

    // something is wrong in plugin, better print debug
    return get_string($stringname, $component);
}

/**
 * This gets the mod/block/course/core etc strings.
 *
 * @param string $component
 * @param int $contextlevel
 * @return string|bool String is success, false if failed
 */
function get_component_string($component, $contextlevel) {

    if ($component === 'moodle' or $component === 'core') {
        switch ($contextlevel) {
            // TODO: this should probably use context level names instead
            case CONTEXT_SYSTEM:    return get_string('coresystem');
            case CONTEXT_USER:      return get_string('users');
            case CONTEXT_COURSECAT: return get_string('categories');
            case CONTEXT_COURSE:    return get_string('course');
            case CONTEXT_MODULE:    return get_string('activities');
            case CONTEXT_BLOCK:     return get_string('block');
            default:                print_error('unknowncontext');
        }
    }

    list($type, $name) = normalize_component($component);
    $dir = get_plugin_directory($type, $name);
    if (!file_exists($dir)) {
        // plugin not installed, bad luck, there is no way to find the name
        return $component.' ???';
    }

    switch ($type) {
        // TODO: this is really hacky, anyway it should be probably moved to lib/pluginlib.php
        case 'quiz':         return get_string($name.':componentname', $component);// insane hack!!!
        case 'repository':   return get_string('repository', 'repository').': '.get_string('pluginname', $component);
        case 'gradeimport':  return get_string('gradeimport', 'grades').': '.get_string('pluginname', $component);
        case 'gradeexport':  return get_string('gradeexport', 'grades').': '.get_string('pluginname', $component);
        case 'gradereport':  return get_string('gradereport', 'grades').': '.get_string('pluginname', $component);
        case 'webservice':   return get_string('webservice', 'webservice').': '.get_string('pluginname', $component);
        case 'block':        return get_string('block').': '.get_string('pluginname', basename($component));
        case 'mod':
            if (get_string_manager()->string_exists('pluginname', $component)) {
                return get_string('activity').': '.get_string('pluginname', $component);
            } else {
                return get_string('activity').': '.get_string('modulename', $component);
            }
        default: return get_string('pluginname', $component);
    }
}

/**
 * Gets the list of roles assigned to this context and up (parents)
 * from the list of roles that are visible on user profile page
 * and participants page.
 *
 * @param context $context
 * @return array
 */
function get_profile_roles(context $context) {
    global $CFG, $DB;

    if (empty($CFG->profileroles)) {
        return array();
    }

    list($rallowed, $params) = $DB->get_in_or_equal(explode(',', $CFG->profileroles), SQL_PARAMS_NAMED, 'a');
    list($contextlist, $cparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'p');
    $params = array_merge($params, $cparams);

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0;
    }

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder, rn.name AS coursealias
              FROM {role_assignments} ra, {role} r
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
                   AND r.id $rallowed
          ORDER BY r.sortorder ASC";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Gets the list of roles assigned to this context and up (parents)
 *
 * @param context $context
 * @return array
 */
function get_roles_used_in_context(context $context) {
    global $DB;

    list($contextlist, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'cl');

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0;
    }

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder, rn.name AS coursealias
              FROM {role_assignments} ra, {role} r
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
          ORDER BY r.sortorder ASC";

    return $DB->get_records_sql($sql, $params);
}

/**
 * This function is used to print roles column in user profile page.
 * It is using the CFG->profileroles to limit the list to only interesting roles.
 * (The permission tab has full details of user role assignments.)
 *
 * @param int $userid
 * @param int $courseid
 * @return string
 */
function get_user_roles_in_course($userid, $courseid) {
    global $CFG, $DB;

    if (empty($CFG->profileroles)) {
        return '';
    }

    if ($courseid == SITEID) {
        $context = context_system::instance();
    } else {
        $context = context_course::instance($courseid);
    }

    if (empty($CFG->profileroles)) {
        return array();
    }

    list($rallowed, $params) = $DB->get_in_or_equal(explode(',', $CFG->profileroles), SQL_PARAMS_NAMED, 'a');
    list($contextlist, $cparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'p');
    $params = array_merge($params, $cparams);

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0;
    }

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder, rn.name AS coursealias
              FROM {role_assignments} ra, {role} r
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
                   AND r.id $rallowed
                   AND ra.userid = :userid
          ORDER BY r.sortorder ASC";
    $params['userid'] = $userid;

    $rolestring = '';

    if ($roles = $DB->get_records_sql($sql, $params)) {
        $rolenames = role_fix_names($roles, $context, ROLENAME_ALIAS, true);   // Substitute aliases

        foreach ($rolenames as $roleid => $rolename) {
            $rolenames[$roleid] = '<a href="'.$CFG->wwwroot.'/user/index.php?contextid='.$context->id.'&amp;roleid='.$roleid.'">'.$rolename.'</a>';
        }
        $rolestring = implode(',', $rolenames);
    }

    return $rolestring;
}

/**
 * Checks if a user can assign users to a particular role in this context
 *
 * @param context $context
 * @param int $targetroleid - the id of the role you want to assign users to
 * @return boolean
 */
function user_can_assign(context $context, $targetroleid) {
    global $DB;

    // First check to see if the user is a site administrator.
    if (is_siteadmin()) {
        return true;
    }

    // Check if user has override capability.
    // If not return false.
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
 * @param context $context optional context for course role name aliases
 * @return array of role records with optional coursealias property
 */
function get_all_roles(context $context = null) {
    global $DB;

    if (!$context or !$coursecontext = $context->get_course_context(false)) {
        $coursecontext = null;
    }

    if ($coursecontext) {
        $sql = "SELECT r.*, rn.name AS coursealias
                  FROM {role} r
             LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
              ORDER BY r.sortorder ASC";
        return $DB->get_records_sql($sql, array('coursecontext'=>$coursecontext->id));

    } else {
        return $DB->get_records('role', array(), 'sortorder ASC');
    }
}

/**
 * Returns roles of a specified archetype
 *
 * @param string $archetype
 * @return array of full role records
 */
function get_archetype_roles($archetype) {
    global $DB;
    return $DB->get_records('role', array('archetype'=>$archetype), 'sortorder ASC');
}

/**
 * Gets all the user roles assigned in this context, or higher contexts
 * this is mainly used when checking if a user can assign a role, or overriding a role
 * i.e. we need to know what this user holds, in order to verify against allow_assign and
 * allow_override tables
 *
 * @param context $context
 * @param int $userid
 * @param bool $checkparentcontexts defaults to true
 * @param string $order defaults to 'c.contextlevel DESC, r.sortorder ASC'
 * @return array
 */
function get_user_roles(context $context, $userid = 0, $checkparentcontexts = true, $order = 'c.contextlevel DESC, r.sortorder ASC') {
    global $USER, $DB;

    if (empty($userid)) {
        if (empty($USER->id)) {
            return array();
        }
        $userid = $USER->id;
    }

    if ($checkparentcontexts) {
        $contextids = $context->get_parent_context_ids();
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
          ORDER BY $order";

    return $DB->get_records_sql($sql ,$params);
}

/**
 * Like get_user_roles, but adds in the authenticated user role, and the front
 * page roles, if applicable.
 *
 * @param context $context the context.
 * @param int $userid optional. Defaults to $USER->id
 * @return array of objects with fields ->userid, ->contextid and ->roleid.
 */
function get_user_roles_with_special(context $context, $userid = 0) {
    global $CFG, $USER;

    if (empty($userid)) {
        if (empty($USER->id)) {
            return array();
        }
        $userid = $USER->id;
    }

    $ras = get_user_roles($context, $userid);

    // Add front-page role if relevant.
    $defaultfrontpageroleid = isset($CFG->defaultfrontpageroleid) ? $CFG->defaultfrontpageroleid : 0;
    $isfrontpage = ($context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID) ||
            is_inside_frontpage($context);
    if ($defaultfrontpageroleid && $isfrontpage) {
        $frontpagecontext = context_course::instance(SITEID);
        $ra = new stdClass();
        $ra->userid = $userid;
        $ra->contextid = $frontpagecontext->id;
        $ra->roleid = $defaultfrontpageroleid;
        $ras[] = $ra;
    }

    // Add authenticated user role if relevant.
    $defaultuserroleid      = isset($CFG->defaultuserroleid) ? $CFG->defaultuserroleid : 0;
    if ($defaultuserroleid && !isguestuser($userid)) {
        $systemcontext = context_system::instance();
        $ra = new stdClass();
        $ra->userid = $userid;
        $ra->contextid = $systemcontext->id;
        $ra->roleid = $defaultuserroleid;
        $ras[] = $ra;
    }

    return $ras;
}

/**
 * Creates a record in the role_allow_override table
 *
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
 * @return void
 */
function allow_override($sroleid, $troleid) {
    global $DB;

    $record = new stdClass();
    $record->roleid        = $sroleid;
    $record->allowoverride = $troleid;
    $DB->insert_record('role_allow_override', $record);
}

/**
 * Creates a record in the role_allow_assign table
 *
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 */
function allow_assign($fromroleid, $targetroleid) {
    global $DB;

    $record = new stdClass();
    $record->roleid      = $fromroleid;
    $record->allowassign = $targetroleid;
    $DB->insert_record('role_allow_assign', $record);
}

/**
 * Creates a record in the role_allow_switch table
 *
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 */
function allow_switch($fromroleid, $targetroleid) {
    global $DB;

    $record = new stdClass();
    $record->roleid      = $fromroleid;
    $record->allowswitch = $targetroleid;
    $DB->insert_record('role_allow_switch', $record);
}

/**
 * Gets a list of roles that this user can assign in this context
 *
 * @param context $context the context.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @param bool $withusercounts if true, count the number of users with each role.
 * @param integer|object $user A user id or object. By default (null) checks the permissions of the current user.
 * @return array if $withusercounts is false, then an array $roleid => $rolename.
 *      if $withusercounts is true, returns a list of three arrays,
 *      $rolenames, $rolecounts, and $nameswithcounts.
 */
function get_assignable_roles(context $context, $rolenamedisplay = ROLENAME_ALIAS, $withusercounts = false, $user = null) {
    global $USER, $DB;

    // make sure there is a real user specified
    if ($user === null) {
        $userid = isset($USER->id) ? $USER->id : 0;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }

    if (!has_capability('moodle/role:assign', $context, $userid)) {
        if ($withusercounts) {
            return array(array(), array(), array());
        } else {
            return array();
        }
    }

    $params = array();
    $extrafields = '';

    if ($withusercounts) {
        $extrafields = ', (SELECT count(u.id)
                             FROM {role_assignments} cra JOIN {user} u ON cra.userid = u.id
                            WHERE cra.roleid = r.id AND cra.contextid = :conid AND u.deleted = 0
                          ) AS usercount';
        $params['conid'] = $context->id;
    }

    if (is_siteadmin($userid)) {
        // show all roles allowed in this context to admins
        $assignrestriction = "";
    } else {
        $parents = $context->get_parent_context_ids(true);
        $contexts = implode(',' , $parents);
        $assignrestriction = "JOIN (SELECT DISTINCT raa.allowassign AS id
                                      FROM {role_allow_assign} raa
                                      JOIN {role_assignments} ra ON ra.roleid = raa.roleid
                                     WHERE ra.userid = :userid AND ra.contextid IN ($contexts)
                                   ) ar ON ar.id = r.id";
        $params['userid'] = $userid;
    }
    $params['contextlevel'] = $context->contextlevel;

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0; // no course aliases
        $coursecontext = null;
    }
    $sql = "SELECT r.id, r.name, r.shortname, rn.name AS coursealias $extrafields
              FROM {role} r
              $assignrestriction
              JOIN {role_context_levels} rcl ON (rcl.contextlevel = :contextlevel AND r.id = rcl.roleid)
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
          ORDER BY r.sortorder ASC";
    $roles = $DB->get_records_sql($sql, $params);

    $rolenames = role_fix_names($roles, $coursecontext, $rolenamedisplay, true);

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
 * @param context $context a context.
 * @return array an array $roleid => $rolename.
 */
function get_switchable_roles(context $context) {
    global $USER, $DB;

    $params = array();
    $extrajoins = '';
    $extrawhere = '';
    if (!is_siteadmin()) {
        // Admins are allowed to switch to any role with.
        // Others are subject to the additional constraint that the switch-to role must be allowed by
        // 'role_allow_switch' for some role they have assigned in this context or any parent.
        $parents = $context->get_parent_context_ids(true);
        $contexts = implode(',' , $parents);

        $extrajoins = "JOIN {role_allow_switch} ras ON ras.allowswitch = rc.roleid
        JOIN {role_assignments} ra ON ra.roleid = ras.roleid";
        $extrawhere = "WHERE ra.userid = :userid AND ra.contextid IN ($contexts)";
        $params['userid'] = $USER->id;
    }

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0; // no course aliases
        $coursecontext = null;
    }

    $query = "
        SELECT r.id, r.name, r.shortname, rn.name AS coursealias
          FROM (SELECT DISTINCT rc.roleid
                  FROM {role_capabilities} rc
                  $extrajoins
                  $extrawhere) idlist
          JOIN {role} r ON r.id = idlist.roleid
     LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
      ORDER BY r.sortorder";
    $roles = $DB->get_records_sql($query, $params);

    return role_fix_names($roles, $context, ROLENAME_ALIAS, true);
}

/**
 * Gets a list of roles that this user can override in this context.
 *
 * @param context $context the context.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @param bool $withcounts if true, count the number of overrides that are set for each role.
 * @return array if $withcounts is false, then an array $roleid => $rolename.
 *      if $withusercounts is true, returns a list of three arrays,
 *      $rolenames, $rolecounts, and $nameswithcounts.
 */
function get_overridable_roles(context $context, $rolenamedisplay = ROLENAME_ALIAS, $withcounts = false) {
    global $USER, $DB;

    if (!has_any_capability(array('moodle/role:safeoverride', 'moodle/role:override'), $context)) {
        if ($withcounts) {
            return array(array(), array(), array());
        } else {
            return array();
        }
    }

    $parents = $context->get_parent_context_ids(true);
    $contexts = implode(',' , $parents);

    $params = array();
    $extrafields = '';

    $params['userid'] = $USER->id;
    if ($withcounts) {
        $extrafields = ', (SELECT COUNT(rc.id) FROM {role_capabilities} rc
                WHERE rc.roleid = ro.id AND rc.contextid = :conid) AS overridecount';
        $params['conid'] = $context->id;
    }

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0; // no course aliases
        $coursecontext = null;
    }

    if (is_siteadmin()) {
        // show all roles to admins
        $roles = $DB->get_records_sql("
            SELECT ro.id, ro.name, ro.shortname, rn.name AS coursealias $extrafields
              FROM {role} ro
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = ro.id)
          ORDER BY ro.sortorder ASC", $params);

    } else {
        $roles = $DB->get_records_sql("
            SELECT ro.id, ro.name, ro.shortname, rn.name AS coursealias $extrafields
              FROM {role} ro
              JOIN (SELECT DISTINCT r.id
                      FROM {role} r
                      JOIN {role_allow_override} rao ON r.id = rao.allowoverride
                      JOIN {role_assignments} ra ON rao.roleid = ra.roleid
                     WHERE ra.userid = :userid AND ra.contextid IN ($contexts)
                   ) inline_view ON ro.id = inline_view.id
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = ro.id)
          ORDER BY ro.sortorder ASC", $params);
    }

    $rolenames = role_fix_names($roles, $context, $rolenamedisplay, true);

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
 * Create a role menu suitable for default role selection in enrol plugins.
 *
 * @package    core_enrol
 *
 * @param context $context
 * @param int $addroleid current or default role - always added to list
 * @return array roleid=>localised role name
 */
function get_default_enrol_roles(context $context, $addroleid = null) {
    global $DB;

    $params = array('contextlevel'=>CONTEXT_COURSE);

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0; // no course names
        $coursecontext = null;
    }

    if ($addroleid) {
        $addrole = "OR r.id = :addroleid";
        $params['addroleid'] = $addroleid;
    } else {
        $addrole = "";
    }

    $sql = "SELECT r.id, r.name, r.shortname, rn.name AS coursealias
              FROM {role} r
         LEFT JOIN {role_context_levels} rcl ON (rcl.roleid = r.id AND rcl.contextlevel = :contextlevel)
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
             WHERE rcl.id IS NOT NULL $addrole
          ORDER BY sortorder DESC";

    $roles = $DB->get_records_sql($sql, $params);

    return role_fix_names($roles, $context, ROLENAME_BOTH, true);
}

/**
 * Return context levels where this role is assignable.
 *
 * @param integer $roleid the id of a role.
 * @return array list of the context levels at which this role may be assigned.
 */
function get_role_contextlevels($roleid) {
    global $DB;
    return $DB->get_records_menu('role_context_levels', array('roleid' => $roleid),
            'contextlevel', 'id,contextlevel');
}

/**
 * Return roles suitable for assignment at the specified context level.
 *
 * NOTE: this function name looks like a typo, should be probably get_roles_for_contextlevel()
 *
 * @param integer $contextlevel a contextlevel.
 * @return array list of role ids that are assignable at this context level.
 */
function get_roles_for_contextlevels($contextlevel) {
    global $DB;
    return $DB->get_records_menu('role_context_levels', array('contextlevel' => $contextlevel),
            '', 'id,roleid');
}

/**
 * Returns default context levels where roles can be assigned.
 *
 * @param string $rolearchetype one of the role archetypes - that is, one of the keys
 *      from the array returned by get_role_archetypes();
 * @return array list of the context levels at which this type of role may be assigned by default.
 */
function get_default_contextlevels($rolearchetype) {
    static $defaults = array(
        'manager'        => array(CONTEXT_SYSTEM, CONTEXT_COURSECAT, CONTEXT_COURSE),
        'coursecreator'  => array(CONTEXT_SYSTEM, CONTEXT_COURSECAT),
        'editingteacher' => array(CONTEXT_COURSE, CONTEXT_MODULE),
        'teacher'        => array(CONTEXT_COURSE, CONTEXT_MODULE),
        'student'        => array(CONTEXT_COURSE, CONTEXT_MODULE),
        'guest'          => array(),
        'user'           => array(),
        'frontpage'      => array());

    if (isset($defaults[$rolearchetype])) {
        return $defaults[$rolearchetype];
    } else {
        return array();
    }
}

/**
 * Set the context levels at which a particular role can be assigned.
 * Throws exceptions in case of error.
 *
 * @param integer $roleid the id of a role.
 * @param array $contextlevels the context levels at which this role should be assignable,
 *      duplicate levels are removed.
 * @return void
 */
function set_role_contextlevels($roleid, array $contextlevels) {
    global $DB;
    $DB->delete_records('role_context_levels', array('roleid' => $roleid));
    $rcl = new stdClass();
    $rcl->roleid = $roleid;
    $contextlevels = array_unique($contextlevels);
    foreach ($contextlevels as $level) {
        $rcl->contextlevel = $level;
        $DB->insert_record('role_context_levels', $rcl, false, true);
    }
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
 * @param context $context
 * @param string|array $capability - capability name(s)
 * @param string $fields - fields to be pulled. The user table is aliased to 'u'. u.id MUST be included.
 * @param string $sort - the sort order. Default is lastaccess time.
 * @param mixed $limitfrom - number of records to skip (offset)
 * @param mixed $limitnum - number of records to fetch
 * @param string|array $groups - single group or array of groups - only return
 *               users who are in one of these group(s).
 * @param string|array $exceptions - list of users to exclude, comma separated or array
 * @param bool $doanything_ignored not used any more, admin accounts are never returned
 * @param bool $view_ignored - use get_enrolled_sql() instead
 * @param bool $useviewallgroups if $groups is set the return users who
 *               have capability both $capability and moodle/site:accessallgroups
 *               in this context, as well as users who have $capability and who are
 *               in $groups.
 * @return array of user records
 */
function get_users_by_capability(context $context, $capability, $fields = '', $sort = '', $limitfrom = '', $limitnum = '',
                                 $groups = '', $exceptions = '', $doanything_ignored = null, $view_ignored = null, $useviewallgroups = false) {
    global $CFG, $DB;

    $defaultuserroleid      = isset($CFG->defaultuserroleid) ? $CFG->defaultuserroleid : 0;
    $defaultfrontpageroleid = isset($CFG->defaultfrontpageroleid) ? $CFG->defaultfrontpageroleid : 0;

    $ctxids = trim($context->path, '/');
    $ctxids = str_replace('/', ',', $ctxids);

    // Context is the frontpage
    $iscoursepage = false; // coursepage other than fp
    $isfrontpage = false;
    if ($context->contextlevel == CONTEXT_COURSE) {
        if ($context->instanceid == SITEID) {
            $isfrontpage = true;
        } else {
            $iscoursepage = true;
        }
    }
    $isfrontpage = ($isfrontpage || is_inside_frontpage($context));

    $caps = (array)$capability;

    // construct list of context paths bottom-->top
    list($contextids, $paths) = get_context_info_list($context);

    // we need to find out all roles that have these capabilities either in definition or in overrides
    $defs = array();
    list($incontexts, $params) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED, 'con');
    list($incaps, $params2) = $DB->get_in_or_equal($caps, SQL_PARAMS_NAMED, 'cap');
    $params = array_merge($params, $params2);
    $sql = "SELECT rc.id, rc.roleid, rc.permission, rc.capability, ctx.path
              FROM {role_capabilities} rc
              JOIN {context} ctx on rc.contextid = ctx.id
             WHERE rc.contextid $incontexts AND rc.capability $incaps";

    $rcs = $DB->get_records_sql($sql, $params);
    foreach ($rcs as $rc) {
        $defs[$rc->capability][$rc->path][$rc->roleid] = $rc->permission;
    }

    // go through the permissions bottom-->top direction to evaluate the current permission,
    // first one wins (prohibit is an exception that always wins)
    $access = array();
    foreach ($caps as $cap) {
        foreach ($paths as $path) {
            if (empty($defs[$cap][$path])) {
                continue;
            }
            foreach($defs[$cap][$path] as $roleid => $perm) {
                if ($perm == CAP_PROHIBIT) {
                    $access[$cap][$roleid] = CAP_PROHIBIT;
                    continue;
                }
                if (!isset($access[$cap][$roleid])) {
                    $access[$cap][$roleid] = (int)$perm;
                }
            }
        }
    }

    // make lists of roles that are needed and prohibited in this context
    $needed = array(); // one of these is enough
    $prohibited = array(); // must not have any of these
    foreach ($caps as $cap) {
        if (empty($access[$cap])) {
            continue;
        }
        foreach ($access[$cap] as $roleid => $perm) {
            if ($perm == CAP_PROHIBIT) {
                unset($needed[$cap][$roleid]);
                $prohibited[$cap][$roleid] = true;
            } else if ($perm == CAP_ALLOW and empty($prohibited[$cap][$roleid])) {
                $needed[$cap][$roleid] = true;
            }
        }
        if (empty($needed[$cap]) or !empty($prohibited[$cap][$defaultuserroleid])) {
            // easy, nobody has the permission
            unset($needed[$cap]);
            unset($prohibited[$cap]);
        } else if ($isfrontpage and !empty($prohibited[$cap][$defaultfrontpageroleid])) {
            // everybody is disqualified on the frontpage
            unset($needed[$cap]);
            unset($prohibited[$cap]);
        }
        if (empty($prohibited[$cap])) {
            unset($prohibited[$cap]);
        }
    }

    if (empty($needed)) {
        // there can not be anybody if no roles match this request
        return array();
    }

    if (empty($prohibited)) {
        // we can compact the needed roles
        $n = array();
        foreach ($needed as $cap) {
            foreach ($cap as $roleid=>$unused) {
                $n[$roleid] = true;
            }
        }
        $needed = array('any'=>$n);
        unset($n);
    }

    // ***** Set up default fields ******
    if (empty($fields)) {
        if ($iscoursepage) {
            $fields = 'u.*, ul.timeaccess AS lastaccess';
        } else {
            $fields = 'u.*';
        }
    } else {
        if (debugging('', DEBUG_DEVELOPER) && strpos($fields, 'u.*') === false && strpos($fields, 'u.id') === false) {
            debugging('u.id must be included in the list of fields passed to get_users_by_capability().', DEBUG_DEVELOPER);
        }
    }

    // Set up default sort
    if (empty($sort)) { // default to course lastaccess or just lastaccess
        if ($iscoursepage) {
            $sort = 'ul.timeaccess';
        } else {
            $sort = 'u.lastaccess';
        }
    }

    // Prepare query clauses
    $wherecond = array();
    $params    = array();
    $joins     = array();

    // User lastaccess JOIN
    if ((strpos($sort, 'ul.timeaccess') === false) and (strpos($fields, 'ul.timeaccess') === false)) {
         // user_lastaccess is not required MDL-13810
    } else {
        if ($iscoursepage) {
            $joins[] = "LEFT OUTER JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = {$context->instanceid})";
        } else {
            throw new coding_exception('Invalid sort in get_users_by_capability(), ul.timeaccess allowed only for course contexts.');
        }
    }

    // We never return deleted users or guest account.
    $wherecond[] = "u.deleted = 0 AND u.id <> :guestid";
    $params['guestid'] = $CFG->siteguest;

    // Groups
    if ($groups) {
        $groups = (array)$groups;
        list($grouptest, $grpparams) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED, 'grp');
        $grouptest = "u.id IN (SELECT userid FROM {groups_members} gm WHERE gm.groupid $grouptest)";
        $params = array_merge($params, $grpparams);

        if ($useviewallgroups) {
            $viewallgroupsusers = get_users_by_capability($context, 'moodle/site:accessallgroups', 'u.id, u.id', '', '', '', '', $exceptions);
            if (!empty($viewallgroupsusers)) {
                $wherecond[] =  "($grouptest OR u.id IN (" . implode(',', array_keys($viewallgroupsusers)) . '))';
            } else {
                $wherecond[] =  "($grouptest)";
            }
        } else {
            $wherecond[] =  "($grouptest)";
        }
    }

    // User exceptions
    if (!empty($exceptions)) {
        $exceptions = (array)$exceptions;
        list($exsql, $exparams) = $DB->get_in_or_equal($exceptions, SQL_PARAMS_NAMED, 'exc', false);
        $params = array_merge($params, $exparams);
        $wherecond[] = "u.id $exsql";
    }

    // now add the needed and prohibited roles conditions as joins
    if (!empty($needed['any'])) {
        // simple case - there are no prohibits involved
        if (!empty($needed['any'][$defaultuserroleid]) or ($isfrontpage and !empty($needed['any'][$defaultfrontpageroleid]))) {
            // everybody
        } else {
            $joins[] = "JOIN (SELECT DISTINCT userid
                                FROM {role_assignments}
                               WHERE contextid IN ($ctxids)
                                     AND roleid IN (".implode(',', array_keys($needed['any'])) .")
                             ) ra ON ra.userid = u.id";
        }
    } else {
        $unions = array();
        $everybody = false;
        foreach ($needed as $cap=>$unused) {
            if (empty($prohibited[$cap])) {
                if (!empty($needed[$cap][$defaultuserroleid]) or ($isfrontpage and !empty($needed[$cap][$defaultfrontpageroleid]))) {
                    $everybody = true;
                    break;
                } else {
                    $unions[] = "SELECT userid
                                   FROM {role_assignments}
                                  WHERE contextid IN ($ctxids)
                                        AND roleid IN (".implode(',', array_keys($needed[$cap])) .")";
                }
            } else {
                if (!empty($prohibited[$cap][$defaultuserroleid]) or ($isfrontpage and !empty($prohibited[$cap][$defaultfrontpageroleid]))) {
                    // nobody can have this cap because it is prevented in default roles
                    continue;

                } else if (!empty($needed[$cap][$defaultuserroleid]) or ($isfrontpage and !empty($needed[$cap][$defaultfrontpageroleid]))) {
                    // everybody except the prohibitted - hiding does not matter
                    $unions[] = "SELECT id AS userid
                                   FROM {user}
                                  WHERE id NOT IN (SELECT userid
                                                     FROM {role_assignments}
                                                    WHERE contextid IN ($ctxids)
                                                          AND roleid IN (".implode(',', array_keys($prohibited[$cap])) ."))";

                } else {
                    $unions[] = "SELECT userid
                                   FROM {role_assignments}
                                  WHERE contextid IN ($ctxids)
                                        AND roleid IN (".implode(',', array_keys($needed[$cap])) .")
                                        AND roleid NOT IN (".implode(',', array_keys($prohibited[$cap])) .")";
                }
            }
        }
        if (!$everybody) {
            if ($unions) {
                $joins[] = "JOIN (SELECT DISTINCT userid FROM ( ".implode(' UNION ', $unions)." ) us) ra ON ra.userid = u.id";
            } else {
                // only prohibits found - nobody can be matched
                $wherecond[] = "1 = 2";
            }
        }
    }

    // Collect WHERE conditions and needed joins
    $where = implode(' AND ', $wherecond);
    if ($where !== '') {
        $where = 'WHERE ' . $where;
    }
    $joins = implode("\n", $joins);

    // Ok, let's get the users!
    $sql = "SELECT $fields
              FROM {user} u
            $joins
            $where
          ORDER BY $sort";

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
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
 * variables like $CFG->coursecontact .
 *
 * @param array $users Users array, keyed on userid
 * @param context $context
 * @param array $roles ids of the roles to include, optional
 * @param string $sortpolicy defaults to locality, more about
 * @return array sorted copy of the array
 */
function sort_by_roleassignment_authority($users, context $context, $roles = array(), $sortpolicy = 'locality') {
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
 * @param int $roleid (can also be an array of ints!)
 * @param context $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @param string $fields fields from user (u.) , role assignment (ra) or role (r.)
 * @param string $sort sort from user (u.) , role assignment (ra.) or role (r.).
 *      null => use default sort from users_order_by_sql.
 * @param bool $gethidden_ignored use enrolments instead
 * @param string $group defaults to ''
 * @param mixed $limitfrom defaults to ''
 * @param mixed $limitnum defaults to ''
 * @param string $extrawheretest defaults to ''
 * @param array $whereorsortparams any paramter values used by $sort or $extrawheretest.
 * @return array
 */
function get_role_users($roleid, context $context, $parent = false, $fields = '',
        $sort = null, $gethidden_ignored = null, $group = '',
        $limitfrom = '', $limitnum = '', $extrawheretest = '', $whereorsortparams = array()) {
    global $DB;

    if (empty($fields)) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.emailstop, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.lang, u.timezone, u.lastaccess, u.mnethostid, r.name AS rolename, r.sortorder, '.
                  'r.shortname AS roleshortname, rn.name AS rolecoursealias';
    }

    $parentcontexts = '';
    if ($parent) {
        $parentcontexts = substr($context->path, 1); // kill leading slash
        $parentcontexts = str_replace('/', ',', $parentcontexts);
        if ($parentcontexts !== '') {
            $parentcontexts = ' OR ra.contextid IN ('.$parentcontexts.' )';
        }
    }

    if ($roleid) {
        list($rids, $params) = $DB->get_in_or_equal($roleid, SQL_PARAMS_NAMED, 'r');
        $roleselect = "AND ra.roleid $rids";
    } else {
        $params = array();
        $roleselect = '';
    }

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0;
    }

    if ($group) {
        $groupjoin   = "JOIN {groups_members} gm ON gm.userid = u.id";
        $groupselect = " AND gm.groupid = :groupid ";
        $params['groupid'] = $group;
    } else {
        $groupjoin   = '';
        $groupselect = '';
    }

    $params['contextid'] = $context->id;

    if ($extrawheretest) {
        $extrawheretest = ' AND ' . $extrawheretest;
    }

    if ($whereorsortparams) {
        $params = array_merge($params, $whereorsortparams);
    }

    if (!$sort) {
        list($sort, $sortparams) = users_order_by_sql('u');
        $params = array_merge($params, $sortparams);
    }

    $sql = "SELECT DISTINCT $fields, ra.roleid
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
              JOIN {role} r ON ra.roleid = r.id
         LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
        $groupjoin
             WHERE (ra.contextid = :contextid $parentcontexts)
                   $roleselect
                   $groupselect
                   $extrawheretest
          ORDER BY $sort";                  // join now so that we can just use fullname() later

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Counts all the users assigned this role in this context or higher
 *
 * @param int|array $roleid either int or an array of ints
 * @param context $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @return int Returns the result count
 */
function count_role_users($roleid, context $context, $parent = false) {
    global $DB;

    if ($parent) {
        if ($contexts = $context->get_parent_context_ids()) {
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

    $sql = "SELECT COUNT(u.id)
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
 * @param string $capability Capability in question
 * @param int $userid User ID or null for current user
 * @param bool $doanything True if 'doanything' is permitted (default)
 * @param string $fieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id
 * @param string $orderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed
 * @return array Array of courses, may have zero entries. Or false if query failed.
 */
function get_user_capability_course($capability, $userid = null, $doanything = true, $fieldsexceptid = '', $orderby = '') {
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
            if ($orderby) {
                $orderby .= ',';
            }
            $orderby .= 'c.'.$field;
        }
        $orderby = 'ORDER BY '.$orderby;
    }

    // Obtain a list of everything relevant about all courses including context.
    // Note the result can be used directly as a context (we are going to), the course
    // fields are just appended.

    $contextpreload = context_helper::get_preload_record_columns_sql('x');

    $courses = array();
    $rs = $DB->get_recordset_sql("SELECT c.id $fieldlist, $contextpreload
                                    FROM {course} c
                                    JOIN {context} x ON (c.id=x.instanceid AND x.contextlevel=".CONTEXT_COURSE.")
                                $orderby");
    // Check capability for each course in turn
    foreach ($rs as $course) {
        context_helper::preload_from_record($course);
        $context = context_course::instance($course->id);
        if (has_capability($capability, $context, $userid, $doanything)) {
            // We've got the capability. Make the record look like a course record
            // and store it
            $courses[] = $course;
        }
    }
    $rs->close();
    return empty($courses) ? false : $courses;
}

/**
 * This function finds the roles assigned directly to this context only
 * i.e. no roles in parent contexts
 *
 * @param context $context
 * @return array
 */
function get_roles_on_exact_context(context $context) {
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
 * @param integer $roleid the role to switch to.
 * @param context $context the context in which to perform the switch.
 * @return bool success or failure.
 */
function role_switch($roleid, context $context) {
    global $USER;

    //
    // Plan of action
    //
    // - Add the ghost RA to $USER->access
    //   as $USER->access['rsw'][$path] = $roleid
    //
    // - Make sure $USER->access['rdef'] has the roledefs
    //   it needs to honour the switcherole
    //
    // Roledefs will get loaded "deep" here - down to the last child
    // context. Note that
    //
    // - When visiting subcontexts, our selective accessdata loading
    //   will still work fine - though those ra/rdefs will be ignored
    //   appropriately while the switch is in place
    //
    // - If a switcherole happens at a category with tons of courses
    //   (that have many overrides for switched-to role), the session
    //   will get... quite large. Sometimes you just can't win.
    //
    // To un-switch just unset($USER->access['rsw'][$path])
    //
    // Note: it is not possible to switch to roles that do not have course:view

    if (!isset($USER->access)) {
        load_all_capabilities();
    }


    // Add the switch RA
    if ($roleid == 0) {
        unset($USER->access['rsw'][$context->path]);
        return true;
    }

    $USER->access['rsw'][$context->path] = $roleid;

    // Load roledefs
    load_role_access_by_context($roleid, $context, $USER->access);

    return true;
}

/**
 * Checks if the user has switched roles within the given course.
 *
 * Note: You can only switch roles within the course, hence it takes a course id
 * rather than a context. On that note Petr volunteered to implement this across
 * all other contexts, all requests for this should be forwarded to him ;)
 *
 * @param int $courseid The id of the course to check
 * @return bool True if the user has switched roles within the course.
 */
function is_role_switched($courseid) {
    global $USER;
    $context = context_course::instance($courseid, MUST_EXIST);
    return (!empty($USER->access['rsw'][$context->path]));
}

/**
 * Get any role that has an override on exact context
 *
 * @param context $context The context to check
 * @return array An array of roles
 */
function get_roles_with_override_on_context(context $context) {
    global $DB;

    return $DB->get_records_sql("SELECT r.*
                                   FROM {role_capabilities} rc, {role} r
                                  WHERE rc.roleid = r.id AND rc.contextid = ?",
                                array($context->id));
}

/**
 * Get all capabilities for this role on this context (overrides)
 *
 * @param stdClass $role
 * @param context $context
 * @return array
 */
function get_capabilities_from_role_on_context($role, context $context) {
    global $DB;

    return $DB->get_records_sql("SELECT *
                                   FROM {role_capabilities}
                                  WHERE contextid = ? AND roleid = ?",
                                array($context->id, $role->id));
}

/**
 * Find out which roles has assignment on this context
 *
 * @param context $context
 * @return array
 *
 */
function get_roles_with_assignment_on_context(context $context) {
    global $DB;

    return $DB->get_records_sql("SELECT r.*
                                   FROM {role_assignments} ra, {role} r
                                  WHERE ra.roleid = r.id AND ra.contextid = ?",
                                array($context->id));
}

/**
 * Find all user assignment of users for this role, on this context
 *
 * @param stdClass $role
 * @param context $context
 * @return array
 */
function get_users_from_role_on_context($role, context $context) {
    global $DB;

    return $DB->get_records_sql("SELECT *
                                   FROM {role_assignments}
                                  WHERE contextid = ? AND roleid = ?",
                                array($context->id, $role->id));
}

/**
 * Simple function returning a boolean true if user has roles
 * in context or parent contexts, otherwise false.
 *
 * @param int $userid
 * @param int $roleid
 * @param int $contextid empty means any context
 * @return bool
 */
function user_has_role_assignment($userid, $roleid, $contextid = 0) {
    global $DB;

    if ($contextid) {
        if (!$context = context::instance_by_id($contextid, IGNORE_MISSING)) {
            return false;
        }
        $parents = $context->get_parent_context_ids(true);
        list($contexts, $params) = $DB->get_in_or_equal($parents, SQL_PARAMS_NAMED, 'r');
        $params['userid'] = $userid;
        $params['roleid'] = $roleid;

        $sql = "SELECT COUNT(ra.id)
                  FROM {role_assignments} ra
                 WHERE ra.userid = :userid AND ra.roleid = :roleid AND ra.contextid $contexts";

        $count = $DB->get_field_sql($sql, $params);
        return ($count > 0);

    } else {
        return $DB->record_exists('role_assignments', array('userid'=>$userid, 'roleid'=>$roleid));
    }
}

/**
 * Get role name or alias if exists and format the text.
 *
 * @param stdClass $role role object
 *      - optional 'coursealias' property should be included for performance reasons if course context used
 *      - description property is not required here
 * @param context|bool $context empty means system context
 * @param int $rolenamedisplay type of role name
 * @return string localised role name or course role name alias
 */
function role_get_name(stdClass $role, $context = null, $rolenamedisplay = ROLENAME_ALIAS) {
    global $DB;

    if ($rolenamedisplay == ROLENAME_SHORT) {
        return $role->shortname;
    }

    if (!$context or !$coursecontext = $context->get_course_context(false)) {
        $coursecontext = null;
    }

    if ($coursecontext and !property_exists($role, 'coursealias') and ($rolenamedisplay == ROLENAME_ALIAS or $rolenamedisplay == ROLENAME_BOTH or $rolenamedisplay == ROLENAME_ALIAS_RAW)) {
        $role = clone($role); // Do not modify parameters.
        if ($r = $DB->get_record('role_names', array('roleid'=>$role->id, 'contextid'=>$coursecontext->id))) {
            $role->coursealias = $r->name;
        } else {
            $role->coursealias = null;
        }
    }

    if ($rolenamedisplay == ROLENAME_ALIAS_RAW) {
        if ($coursecontext) {
            return $role->coursealias;
        } else {
            return null;
        }
    }

    if (trim($role->name) !== '') {
        // For filtering always use context where was the thing defined - system for roles here.
        $original = format_string($role->name, true, array('context'=>context_system::instance()));

    } else {
        // Empty role->name means we want to see localised role name based on shortname,
        // only default roles are supposed to be localised.
        switch ($role->shortname) {
            case 'manager':         $original = get_string('manager', 'role'); break;
            case 'coursecreator':   $original = get_string('coursecreators'); break;
            case 'editingteacher':  $original = get_string('defaultcourseteacher'); break;
            case 'teacher':         $original = get_string('noneditingteacher'); break;
            case 'student':         $original = get_string('defaultcoursestudent'); break;
            case 'guest':           $original = get_string('guest'); break;
            case 'user':            $original = get_string('authenticateduser'); break;
            case 'frontpage':       $original = get_string('frontpageuser', 'role'); break;
            // We should not get here, the role UI should require the name for custom roles!
            default:                $original = $role->shortname; break;
        }
    }

    if ($rolenamedisplay == ROLENAME_ORIGINAL) {
        return $original;
    }

    if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
        return "$original ($role->shortname)";
    }

    if ($rolenamedisplay == ROLENAME_ALIAS) {
        if ($coursecontext and trim($role->coursealias) !== '') {
            return format_string($role->coursealias, true, array('context'=>$coursecontext));
        } else {
            return $original;
        }
    }

    if ($rolenamedisplay == ROLENAME_BOTH) {
        if ($coursecontext and trim($role->coursealias) !== '') {
            return format_string($role->coursealias, true, array('context'=>$coursecontext)) . " ($original)";
        } else {
            return $original;
        }
    }

    throw new coding_exception('Invalid $rolenamedisplay parameter specified in role_get_name()');
}

/**
 * Returns localised role description if available.
 * If the name is empty it tries to find the default role name using
 * hardcoded list of default role names or other methods in the future.
 *
 * @param stdClass $role
 * @return string localised role name
 */
function role_get_description(stdClass $role) {
    if (!html_is_blank($role->description)) {
        return format_text($role->description, FORMAT_HTML, array('context'=>context_system::instance()));
    }

    switch ($role->shortname) {
        case 'manager':         return get_string('managerdescription', 'role');
        case 'coursecreator':   return get_string('coursecreatorsdescription');
        case 'editingteacher':  return get_string('defaultcourseteacherdescription');
        case 'teacher':         return get_string('noneditingteacherdescription');
        case 'student':         return get_string('defaultcoursestudentdescription');
        case 'guest':           return get_string('guestdescription');
        case 'user':            return get_string('authenticateduserdescription');
        case 'frontpage':       return get_string('frontpageuserdescription', 'role');
        default:                return '';
    }
}

/**
 * Get all the localised role names for a context.
 * @param context $context the context
 * @param array of role objects with a ->localname field containing the context-specific role name.
 */
function role_get_names(context $context) {
    return role_fix_names(get_all_roles(), $context);
}

/**
 * Prepare list of roles for display, apply aliases and format text
 *
 * @param array $roleoptions array roleid => roleobject (with optional coursealias), strings are accepted for backwards compatibility only
 * @param context|bool $context a context
 * @param int $rolenamedisplay
 * @param bool $returnmenu null means keep the same format as $roleoptions, true means id=>localname, false means id=>rolerecord
 * @return array Array of context-specific role names, or role objects with a ->localname field added.
 */
function role_fix_names($roleoptions, $context = null, $rolenamedisplay = ROLENAME_ALIAS, $returnmenu = null) {
    global $DB;

    if (empty($roleoptions)) {
        return array();
    }

    if (!$context or !$coursecontext = $context->get_course_context(false)) {
        $coursecontext = null;
    }

    // We usually need all role columns...
    $first = reset($roleoptions);
    if ($returnmenu === null) {
        $returnmenu = !is_object($first);
    }

    if (!is_object($first) or !property_exists($first, 'shortname')) {
        $allroles = get_all_roles($context);
        foreach ($roleoptions as $rid => $unused) {
            $roleoptions[$rid] = $allroles[$rid];
        }
    }

    // Inject coursealias if necessary.
    if ($coursecontext and ($rolenamedisplay == ROLENAME_ALIAS_RAW or $rolenamedisplay == ROLENAME_ALIAS or $rolenamedisplay == ROLENAME_BOTH)) {
        $first = reset($roleoptions);
        if (!property_exists($first, 'coursealias')) {
            $aliasnames = $DB->get_records('role_names', array('contextid'=>$coursecontext->id));
            foreach ($aliasnames as $alias) {
                if (isset($roleoptions[$alias->roleid])) {
                    $roleoptions[$alias->roleid]->coursealias = $alias->name;
                }
            }
        }
    }

    // Add localname property.
    foreach ($roleoptions as $rid => $role) {
        $roleoptions[$rid]->localname = role_get_name($role, $coursecontext, $rolenamedisplay);
    }

    if (!$returnmenu) {
        return $roleoptions;
    }

    $menu = array();
    foreach ($roleoptions as $rid => $role) {
        $menu[$rid] = $role->localname;
    }

    return $menu;
}

/**
 * Aids in detecting if a new line is required when reading a new capability
 *
 * This function helps admin/roles/manage.php etc to detect if a new line should be printed
 * when we read in a new capability.
 * Most of the time, if the 2 components are different we should print a new line, (e.g. course system->rss client)
 * but when we are in grade, all reports/import/export capabilities should be together
 *
 * @param string $cap component string a
 * @param string $comp component string b
 * @param int $contextlevel
 * @return bool whether 2 component are in different "sections"
 */
function component_level_changed($cap, $comp, $contextlevel) {

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
 * Fix the roles.sortorder field in the database, so it contains sequential integers,
 * and return an array of roleids in order.
 *
 * @param array $allroles array of roles, as returned by get_all_roles();
 * @return array $role->sortorder =-> $role->id with the keys in ascending order.
 */
function fix_role_sortorder($allroles) {
    global $DB;

    $rolesort = array();
    $i = 0;
    foreach ($allroles as $role) {
        $rolesort[$i] = $role->id;
        if ($role->sortorder != $i) {
            $r = new stdClass();
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
 * @param stdClass $first The first role. Actually, only ->sortorder is used.
 * @param stdClass $second The second role. Actually, only ->sortorder is used.
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
 * Duplicates all the base definitions of a role
 *
 * @param stdClass $sourcerole role to copy from
 * @param int $targetrole id of role to copy to
 */
function role_cap_duplicate($sourcerole, $targetrole) {
    global $DB;

    $systemcontext = context_system::instance();
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

/**
 * Returns two lists, this can be used to find out if user has capability.
 * Having any needed role and no forbidden role in this context means
 * user has this capability in this context.
 * Use get_role_names_with_cap_in_context() if you need role names to display in the UI
 *
 * @param stdClass $context
 * @param string $capability
 * @return array($neededroles, $forbiddenroles)
 */
function get_roles_with_cap_in_context($context, $capability) {
    global $DB;

    $ctxids = trim($context->path, '/'); // kill leading slash
    $ctxids = str_replace('/', ',', $ctxids);

    $sql = "SELECT rc.id, rc.roleid, rc.permission, ctx.depth
              FROM {role_capabilities} rc
              JOIN {context} ctx ON ctx.id = rc.contextid
             WHERE rc.capability = :cap AND ctx.id IN ($ctxids)
          ORDER BY rc.roleid ASC, ctx.depth DESC";
    $params = array('cap'=>$capability);

    if (!$capdefs = $DB->get_records_sql($sql, $params)) {
        // no cap definitions --> no capability
        return array(array(), array());
    }

    $forbidden = array();
    $needed    = array();
    foreach($capdefs as $def) {
        if (isset($forbidden[$def->roleid])) {
            continue;
        }
        if ($def->permission == CAP_PROHIBIT) {
            $forbidden[$def->roleid] = $def->roleid;
            unset($needed[$def->roleid]);
            continue;
        }
        if (!isset($needed[$def->roleid])) {
            if ($def->permission == CAP_ALLOW) {
                $needed[$def->roleid] = true;
            } else if ($def->permission == CAP_PREVENT) {
                $needed[$def->roleid] = false;
            }
        }
    }
    unset($capdefs);

    // remove all those roles not allowing
    foreach($needed as $key=>$value) {
        if (!$value) {
            unset($needed[$key]);
        } else {
            $needed[$key] = $key;
        }
    }

    return array($needed, $forbidden);
}

/**
 * Returns an array of role IDs that have ALL of the the supplied capabilities
 * Uses get_roles_with_cap_in_context(). Returns $allowed minus $forbidden
 *
 * @param stdClass $context
 * @param array $capabilities An array of capabilities
 * @return array of roles with all of the required capabilities
 */
function get_roles_with_caps_in_context($context, $capabilities) {
    $neededarr = array();
    $forbiddenarr = array();
    foreach($capabilities as $caprequired) {
        list($neededarr[], $forbiddenarr[]) = get_roles_with_cap_in_context($context, $caprequired);
    }

    $rolesthatcanrate = array();
    if (!empty($neededarr)) {
        foreach ($neededarr as $needed) {
            if (empty($rolesthatcanrate)) {
                $rolesthatcanrate = $needed;
            } else {
                //only want roles that have all caps
                $rolesthatcanrate = array_intersect_key($rolesthatcanrate,$needed);
            }
        }
    }
    if (!empty($forbiddenarr) && !empty($rolesthatcanrate)) {
        foreach ($forbiddenarr as $forbidden) {
           //remove any roles that are forbidden any of the caps
           $rolesthatcanrate = array_diff($rolesthatcanrate, $forbidden);
        }
    }
    return $rolesthatcanrate;
}

/**
 * Returns an array of role names that have ALL of the the supplied capabilities
 * Uses get_roles_with_caps_in_context(). Returns $allowed minus $forbidden
 *
 * @param stdClass $context
 * @param array $capabilities An array of capabilities
 * @return array of roles with all of the required capabilities
 */
function get_role_names_with_caps_in_context($context, $capabilities) {
    global $DB;

    $rolesthatcanrate = get_roles_with_caps_in_context($context, $capabilities);
    $allroles = $DB->get_records('role', null, 'sortorder DESC');

    $roles = array();
    foreach ($rolesthatcanrate as $r) {
        $roles[$r] = $allroles[$r];
    }

    return role_fix_names($roles, $context, ROLENAME_ALIAS, true);
}

/**
 * This function verifies the prohibit comes from this context
 * and there are no more prohibits in parent contexts.
 *
 * @param int $roleid
 * @param context $context
 * @param string $capability name
 * @return bool
 */
function prohibit_is_removable($roleid, context $context, $capability) {
    global $DB;

    $ctxids = trim($context->path, '/'); // kill leading slash
    $ctxids = str_replace('/', ',', $ctxids);

    $params = array('roleid'=>$roleid, 'cap'=>$capability, 'prohibit'=>CAP_PROHIBIT);

    $sql = "SELECT ctx.id
              FROM {role_capabilities} rc
              JOIN {context} ctx ON ctx.id = rc.contextid
             WHERE rc.roleid = :roleid AND rc.permission = :prohibit AND rc.capability = :cap AND ctx.id IN ($ctxids)
          ORDER BY ctx.depth DESC";

    if (!$prohibits = $DB->get_records_sql($sql, $params)) {
        // no prohibits == nothing to remove
        return true;
    }

    if (count($prohibits) > 1) {
        // more prohibits can not be removed
        return false;
    }

    return !empty($prohibits[$context->id]);
}

/**
 * More user friendly role permission changing,
 * it should produce as few overrides as possible.
 *
 * @param int $roleid
 * @param stdClass $context
 * @param string $capname capability name
 * @param int $permission
 * @return void
 */
function role_change_permission($roleid, $context, $capname, $permission) {
    global $DB;

    if ($permission == CAP_INHERIT) {
        unassign_capability($capname, $roleid, $context->id);
        $context->mark_dirty();
        return;
    }

    $ctxids = trim($context->path, '/'); // kill leading slash
    $ctxids = str_replace('/', ',', $ctxids);

    $params = array('roleid'=>$roleid, 'cap'=>$capname);

    $sql = "SELECT ctx.id, rc.permission, ctx.depth
              FROM {role_capabilities} rc
              JOIN {context} ctx ON ctx.id = rc.contextid
             WHERE rc.roleid = :roleid AND rc.capability = :cap AND ctx.id IN ($ctxids)
          ORDER BY ctx.depth DESC";

    if ($existing = $DB->get_records_sql($sql, $params)) {
        foreach($existing as $e) {
            if ($e->permission == CAP_PROHIBIT) {
                // prohibit can not be overridden, no point in changing anything
                return;
            }
        }
        $lowest = array_shift($existing);
        if ($lowest->permission == $permission) {
            // permission already set in this context or parent - nothing to do
            return;
        }
        if ($existing) {
            $parent = array_shift($existing);
            if ($parent->permission == $permission) {
                // permission already set in parent context or parent - just unset in this context
                // we do this because we want as few overrides as possible for performance reasons
                unassign_capability($capname, $roleid, $context->id);
                $context->mark_dirty();
                return;
            }
        }

    } else {
        if ($permission == CAP_PREVENT) {
            // nothing means role does not have permission
            return;
        }
    }

    // assign the needed capability
    assign_capability($capname, $permission, $roleid, $context->id, true);

    // force cap reloading
    $context->mark_dirty();
}


/**
 * Basic moodle context abstraction class.
 *
 * Google confirms that no other important framework is using "context" class,
 * we could use something else like mcontext or moodle_context, but we need to type
 * this very often which would be annoying and it would take too much space...
 *
 * This class is derived from stdClass for backwards compatibility with
 * odl $context record that was returned from DML $DB->get_record()
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 *
 * @property-read int $id context id
 * @property-read int $contextlevel CONTEXT_SYSTEM, CONTEXT_COURSE, etc.
 * @property-read int $instanceid id of related instance in each context
 * @property-read string $path path to context, starts with system context
 * @property-read int $depth
 */
abstract class context extends stdClass implements IteratorAggregate {

    /**
     * The context id
     * Can be accessed publicly through $context->id
     * @var int
     */
    protected $_id;

    /**
     * The context level
     * Can be accessed publicly through $context->contextlevel
     * @var int One of CONTEXT_* e.g. CONTEXT_COURSE, CONTEXT_MODULE
     */
    protected $_contextlevel;

    /**
     * Id of the item this context is related to e.g. COURSE_CONTEXT => course.id
     * Can be accessed publicly through $context->instanceid
     * @var int
     */
    protected $_instanceid;

    /**
     * The path to the context always starting from the system context
     * Can be accessed publicly through $context->path
     * @var string
     */
    protected $_path;

    /**
     * The depth of the context in relation to parent contexts
     * Can be accessed publicly through $context->depth
     * @var int
     */
    protected $_depth;

    /**
     * @var array Context caching info
     */
    private static $cache_contextsbyid = array();

    /**
     * @var array Context caching info
     */
    private static $cache_contexts     = array();

    /**
     * Context count
     * Why do we do count contexts? Because count($array) is horribly slow for large arrays
     * @var int
     */
    protected static $cache_count      = 0;

    /**
     * @var array Context caching info
     */
    protected static $cache_preloaded  = array();

    /**
     * @var context_system The system context once initialised
     */
    protected static $systemcontext    = null;

    /**
     * Resets the cache to remove all data.
     * @static
     */
    protected static function reset_caches() {
        self::$cache_contextsbyid = array();
        self::$cache_contexts     = array();
        self::$cache_count        = 0;
        self::$cache_preloaded    = array();

        self::$systemcontext = null;
    }

    /**
     * Adds a context to the cache. If the cache is full, discards a batch of
     * older entries.
     *
     * @static
     * @param context $context New context to add
     * @return void
     */
    protected static function cache_add(context $context) {
        if (isset(self::$cache_contextsbyid[$context->id])) {
            // already cached, no need to do anything - this is relatively cheap, we do all this because count() is slow
            return;
        }

        if (self::$cache_count >= CONTEXT_CACHE_MAX_SIZE) {
            $i = 0;
            foreach(self::$cache_contextsbyid as $ctx) {
                $i++;
                if ($i <= 100) {
                    // we want to keep the first contexts to be loaded on this page, hopefully they will be needed again later
                    continue;
                }
                if ($i > (CONTEXT_CACHE_MAX_SIZE / 3)) {
                    // we remove oldest third of the contexts to make room for more contexts
                    break;
                }
                unset(self::$cache_contextsbyid[$ctx->id]);
                unset(self::$cache_contexts[$ctx->contextlevel][$ctx->instanceid]);
                self::$cache_count--;
            }
        }

        self::$cache_contexts[$context->contextlevel][$context->instanceid] = $context;
        self::$cache_contextsbyid[$context->id] = $context;
        self::$cache_count++;
    }

    /**
     * Removes a context from the cache.
     *
     * @static
     * @param context $context Context object to remove
     * @return void
     */
    protected static function cache_remove(context $context) {
        if (!isset(self::$cache_contextsbyid[$context->id])) {
            // not cached, no need to do anything - this is relatively cheap, we do all this because count() is slow
            return;
        }
        unset(self::$cache_contexts[$context->contextlevel][$context->instanceid]);
        unset(self::$cache_contextsbyid[$context->id]);

        self::$cache_count--;

        if (self::$cache_count < 0) {
            self::$cache_count = 0;
        }
    }

    /**
     * Gets a context from the cache.
     *
     * @static
     * @param int $contextlevel Context level
     * @param int $instance Instance ID
     * @return context|bool Context or false if not in cache
     */
    protected static function cache_get($contextlevel, $instance) {
        if (isset(self::$cache_contexts[$contextlevel][$instance])) {
            return self::$cache_contexts[$contextlevel][$instance];
        }
        return false;
    }

    /**
     * Gets a context from the cache based on its id.
     *
     * @static
     * @param int $id Context ID
     * @return context|bool Context or false if not in cache
     */
    protected static function cache_get_by_id($id) {
        if (isset(self::$cache_contextsbyid[$id])) {
            return self::$cache_contextsbyid[$id];
        }
        return false;
    }

    /**
     * Preloads context information from db record and strips the cached info.
     *
     * @static
     * @param stdClass $rec
     * @return void (modifies $rec)
     */
     protected static function preload_from_record(stdClass $rec) {
         if (empty($rec->ctxid) or empty($rec->ctxlevel) or empty($rec->ctxinstance) or empty($rec->ctxpath) or empty($rec->ctxdepth)) {
             // $rec does not have enough data, passed here repeatedly or context does not exist yet
             return;
         }

         // note: in PHP5 the objects are passed by reference, no need to return $rec
         $record = new stdClass();
         $record->id           = $rec->ctxid;       unset($rec->ctxid);
         $record->contextlevel = $rec->ctxlevel;    unset($rec->ctxlevel);
         $record->instanceid   = $rec->ctxinstance; unset($rec->ctxinstance);
         $record->path         = $rec->ctxpath;     unset($rec->ctxpath);
         $record->depth        = $rec->ctxdepth;    unset($rec->ctxdepth);

         return context::create_instance_from_record($record);
     }


    // ====== magic methods =======

    /**
     * Magic setter method, we do not want anybody to modify properties from the outside
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging('Can not change context instance properties!');
    }

    /**
     * Magic method getter, redirects to read only values.
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'id':           return $this->_id;
            case 'contextlevel': return $this->_contextlevel;
            case 'instanceid':   return $this->_instanceid;
            case 'path':         return $this->_path;
            case 'depth':        return $this->_depth;

            default:
                debugging('Invalid context property accessed! '.$name);
                return null;
        }
    }

    /**
     * Full support for isset on our magic read only properties.
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        switch ($name) {
            case 'id':           return isset($this->_id);
            case 'contextlevel': return isset($this->_contextlevel);
            case 'instanceid':   return isset($this->_instanceid);
            case 'path':         return isset($this->_path);
            case 'depth':        return isset($this->_depth);

            default: return false;
        }

    }

    /**
     * ALl properties are read only, sorry.
     * @param string $name
     */
    public function __unset($name) {
        debugging('Can not unset context instance properties!');
    }

    // ====== implementing method from interface IteratorAggregate ======

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     *
     * Now we can convert context object to array using convert_to_array(),
     * and feed it properly to json_encode().
     */
    public function getIterator() {
        $ret = array(
            'id'           => $this->id,
            'contextlevel' => $this->contextlevel,
            'instanceid'   => $this->instanceid,
            'path'         => $this->path,
            'depth'        => $this->depth
        );
        return new ArrayIterator($ret);
    }

    // ====== general context methods ======

    /**
     * Constructor is protected so that devs are forced to
     * use context_xxx::instance() or context::instance_by_id().
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        $this->_id           = $record->id;
        $this->_contextlevel = (int)$record->contextlevel;
        $this->_instanceid   = $record->instanceid;
        $this->_path         = $record->path;
        $this->_depth        = $record->depth;
    }

    /**
     * This function is also used to work around 'protected' keyword problems in context_helper.
     * @static
     * @param stdClass $record
     * @return context instance
     */
    protected static function create_instance_from_record(stdClass $record) {
        $classname = context_helper::get_class_for_level($record->contextlevel);

        if ($context = context::cache_get_by_id($record->id)) {
            return $context;
        }

        $context = new $classname($record);
        context::cache_add($context);

        return $context;
    }

    /**
     * Copy prepared new contexts from temp table to context table,
     * we do this in db specific way for perf reasons only.
     * @static
     */
    protected static function merge_context_temp_table() {
        global $DB;

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
                             SET ct.path     = temp.path,
                                 ct.depth    = temp.depth
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
                             SET path     = temp.path,
                                 depth    = temp.depth
                            FROM {context_temp} temp
                           WHERE temp.id={context}.id";
        } else {
            // sqlite and others
            $updatesql = "UPDATE {context}
                             SET path     = (SELECT path FROM {context_temp} WHERE id = {context}.id),
                                 depth    = (SELECT depth FROM {context_temp} WHERE id = {context}.id)
                             WHERE id IN (SELECT id FROM {context_temp})";
        }

        $DB->execute($updatesql);
    }

   /**
    * Get a context instance as an object, from a given context id.
    *
    * @static
    * @param int $id context id
    * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
    *                        MUST_EXIST means throw exception if no record found
    * @return context|bool the context object or false if not found
    */
    public static function instance_by_id($id, $strictness = MUST_EXIST) {
        global $DB;

        if (get_called_class() !== 'context' and get_called_class() !== 'context_helper') {
            // some devs might confuse context->id and instanceid, better prevent these mistakes completely
            throw new coding_exception('use only context::instance_by_id() for real context levels use ::instance() methods');
        }

        if ($id == SYSCONTEXTID) {
            return context_system::instance(0, $strictness);
        }

        if (is_array($id) or is_object($id) or empty($id)) {
            throw new coding_exception('Invalid context id specified context::instance_by_id()');
        }

        if ($context = context::cache_get_by_id($id)) {
            return $context;
        }

        if ($record = $DB->get_record('context', array('id'=>$id), '*', $strictness)) {
            return context::create_instance_from_record($record);
        }

        return false;
    }

    /**
     * Update context info after moving context in the tree structure.
     *
     * @param context $newparent
     * @return void
     */
    public function update_moved(context $newparent) {
        global $DB;

        $frompath = $this->_path;
        $newpath  = $newparent->path . '/' . $this->_id;

        $trans = $DB->start_delegated_transaction();

        $this->mark_dirty();

        $setdepth = '';
        if (($newparent->depth +1) != $this->_depth) {
            $diff = $newparent->depth - $this->_depth + 1;
            $setdepth = ", depth = depth + $diff";
        }
        $sql = "UPDATE {context}
                   SET path = ?
                       $setdepth
                 WHERE id = ?";
        $params = array($newpath, $this->_id);
        $DB->execute($sql, $params);

        $this->_path  = $newpath;
        $this->_depth = $newparent->depth + 1;

        $sql = "UPDATE {context}
                   SET path = ".$DB->sql_concat("?", $DB->sql_substr("path", strlen($frompath)+1))."
                       $setdepth
                 WHERE path LIKE ?";
        $params = array($newpath, "{$frompath}/%");
        $DB->execute($sql, $params);

        $this->mark_dirty();

        context::reset_caches();

        $trans->allow_commit();
    }

    /**
     * Remove all context path info and optionally rebuild it.
     *
     * @param bool $rebuild
     * @return void
     */
    public function reset_paths($rebuild = true) {
        global $DB;

        if ($this->_path) {
            $this->mark_dirty();
        }
        $DB->set_field_select('context', 'depth', 0, "path LIKE '%/$this->_id/%'");
        $DB->set_field_select('context', 'path', NULL, "path LIKE '%/$this->_id/%'");
        if ($this->_contextlevel != CONTEXT_SYSTEM) {
            $DB->set_field('context', 'depth', 0, array('id'=>$this->_id));
            $DB->set_field('context', 'path', NULL, array('id'=>$this->_id));
            $this->_depth = 0;
            $this->_path = null;
        }

        if ($rebuild) {
            context_helper::build_all_paths(false);
        }

        context::reset_caches();
    }

    /**
     * Delete all data linked to content, do not delete the context record itself
     */
    public function delete_content() {
        global $CFG, $DB;

        blocks_delete_all_for_context($this->_id);
        filter_delete_all_for_context($this->_id);

        require_once($CFG->dirroot . '/comment/lib.php');
        comment::delete_comments(array('contextid'=>$this->_id));

        require_once($CFG->dirroot.'/rating/lib.php');
        $delopt = new stdclass();
        $delopt->contextid = $this->_id;
        $rm = new rating_manager();
        $rm->delete_ratings($delopt);

        // delete all files attached to this context
        $fs = get_file_storage();
        $fs->delete_area_files($this->_id);

        // Delete all repository instances attached to this context.
        require_once($CFG->dirroot . '/repository/lib.php');
        repository::delete_all_for_context($this->_id);

        // delete all advanced grading data attached to this context
        require_once($CFG->dirroot.'/grade/grading/lib.php');
        grading_manager::delete_all_for_context($this->_id);

        // now delete stuff from role related tables, role_unassign_all
        // and unenrol should be called earlier to do proper cleanup
        $DB->delete_records('role_assignments', array('contextid'=>$this->_id));
        $DB->delete_records('role_capabilities', array('contextid'=>$this->_id));
        $DB->delete_records('role_names', array('contextid'=>$this->_id));
    }

    /**
     * Delete the context content and the context record itself
     */
    public function delete() {
        global $DB;

        // double check the context still exists
        if (!$DB->record_exists('context', array('id'=>$this->_id))) {
            context::cache_remove($this);
            return;
        }

        $this->delete_content();
        $DB->delete_records('context', array('id'=>$this->_id));
        // purge static context cache if entry present
        context::cache_remove($this);

        // do not mark dirty contexts if parents unknown
        if (!is_null($this->_path) and $this->_depth > 0) {
            $this->mark_dirty();
        }
    }

    // ====== context level related methods ======

    /**
     * Utility method for context creation
     *
     * @static
     * @param int $contextlevel
     * @param int $instanceid
     * @param string $parentpath
     * @return stdClass context record
     */
    protected static function insert_context_record($contextlevel, $instanceid, $parentpath) {
        global $DB;

        $record = new stdClass();
        $record->contextlevel = $contextlevel;
        $record->instanceid   = $instanceid;
        $record->depth        = 0;
        $record->path         = null; //not known before insert

        $record->id = $DB->insert_record('context', $record);

        // now add path if known - it can be added later
        if (!is_null($parentpath)) {
            $record->path = $parentpath.'/'.$record->id;
            $record->depth = substr_count($record->path, '/');
            $DB->update_record('context', $record);
        }

        return $record;
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with the
     *      type of context, e.g. User, Course, Forum, etc.
     * @param boolean $short whether to use the short name of the thing. Only applies
     *      to course contexts
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        // must be implemented in all context levels
        throw new coding_exception('can not get name of abstract context');
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public abstract function get_url();

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public abstract function get_capabilities();

    /**
     * Recursive function which, given a context, find all its children context ids.
     *
     * For course category contexts it will return immediate children and all subcategory contexts.
     * It will NOT recurse into courses or subcategories categories.
     * If you want to do that, call it on the returned courses/categories.
     *
     * When called for a course context, it will return the modules and blocks
     * displayed in the course page and blocks displayed on the module pages.
     *
     * If called on a user/course/module context it _will_ populate the cache with the appropriate
     * contexts ;-)
     *
     * @return array Array of child records
     */
    public function get_child_contexts() {
        global $DB;

        $sql = "SELECT ctx.*
                  FROM {context} ctx
                 WHERE ctx.path LIKE ?";
        $params = array($this->_path.'/%');
        $records = $DB->get_records_sql($sql, $params);

        $result = array();
        foreach ($records as $record) {
            $result[$record->id] = context::create_instance_from_record($record);
        }

        return $result;
    }

    /**
     * Returns parent contexts of this context in reversed order, i.e. parent first,
     * then grand parent, etc.
     *
     * @param bool $includeself tre means include self too
     * @return array of context instances
     */
    public function get_parent_contexts($includeself = false) {
        if (!$contextids = $this->get_parent_context_ids($includeself)) {
            return array();
        }

        $result = array();
        foreach ($contextids as $contextid) {
            $parent = context::instance_by_id($contextid, MUST_EXIST);
            $result[$parent->id] = $parent;
        }

        return $result;
    }

    /**
     * Returns parent contexts of this context in reversed order, i.e. parent first,
     * then grand parent, etc.
     *
     * @param bool $includeself tre means include self too
     * @return array of context ids
     */
    public function get_parent_context_ids($includeself = false) {
        if (empty($this->_path)) {
            return array();
        }

        $parentcontexts = trim($this->_path, '/'); // kill leading slash
        $parentcontexts = explode('/', $parentcontexts);
        if (!$includeself) {
            array_pop($parentcontexts); // and remove its own id
        }

        return array_reverse($parentcontexts);
    }

    /**
     * Returns parent context
     *
     * @return context
     */
    public function get_parent_context() {
        if (empty($this->_path) or $this->_id == SYSCONTEXTID) {
            return false;
        }

        $parentcontexts = trim($this->_path, '/'); // kill leading slash
        $parentcontexts = explode('/', $parentcontexts);
        array_pop($parentcontexts); // self
        $contextid = array_pop($parentcontexts); // immediate parent

        return context::instance_by_id($contextid, MUST_EXIST);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course_context context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        if ($strict) {
            throw new coding_exception('Context does not belong to any course.');
        } else {
            return false;
        }
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        throw new coding_exception('get_cleanup_sql() method must be implemented in all context levels');
    }

    /**
     * Rebuild context paths and depths at context level.
     *
     * @static
     * @param bool $force
     * @return void
     */
    protected static function build_paths($force) {
        throw new coding_exception('build_paths() method must be implemented in all context levels');
    }

    /**
     * Create missing context instances at given level
     *
     * @static
     * @return void
     */
    protected static function create_level_instances() {
        throw new coding_exception('create_level_instances() method must be implemented in all context levels');
    }

    /**
     * Reset all cached permissions and definitions if the necessary.
     * @return void
     */
    public function reload_if_dirty() {
        global $ACCESSLIB_PRIVATE, $USER;

        // Load dirty contexts list if needed
        if (CLI_SCRIPT) {
            if (!isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
                // we do not load dirty flags in CLI and cron
                $ACCESSLIB_PRIVATE->dirtycontexts = array();
            }
        } else {
            if (!isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
                if (!isset($USER->access['time'])) {
                    // nothing was loaded yet, we do not need to check dirty contexts now
                    return;
                }
                // no idea why -2 is there, server cluster time difference maybe... (skodak)
                $ACCESSLIB_PRIVATE->dirtycontexts = get_cache_flags('accesslib/dirtycontexts', $USER->access['time']-2);
            }
        }

        foreach ($ACCESSLIB_PRIVATE->dirtycontexts as $path=>$unused) {
            if ($path === $this->_path or strpos($this->_path, $path.'/') === 0) {
                // reload all capabilities of USER and others - preserving loginas, roleswitches, etc
                // and then cleanup any marks of dirtyness... at least from our short term memory! :-)
                reload_all_capabilities();
                break;
            }
        }
    }

    /**
     * Mark a context as dirty (with timestamp) so as to force reloading of the context.
     */
    public function mark_dirty() {
        global $CFG, $USER, $ACCESSLIB_PRIVATE;

        if (during_initial_install()) {
            return;
        }

        // only if it is a non-empty string
        if (is_string($this->_path) && $this->_path !== '') {
            set_cache_flag('accesslib/dirtycontexts', $this->_path, 1, time()+$CFG->sessiontimeout);
            if (isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
                $ACCESSLIB_PRIVATE->dirtycontexts[$this->_path] = 1;
            } else {
                if (CLI_SCRIPT) {
                    $ACCESSLIB_PRIVATE->dirtycontexts = array($this->_path => 1);
                } else {
                    if (isset($USER->access['time'])) {
                        $ACCESSLIB_PRIVATE->dirtycontexts = get_cache_flags('accesslib/dirtycontexts', $USER->access['time']-2);
                    } else {
                        $ACCESSLIB_PRIVATE->dirtycontexts = array($this->_path => 1);
                    }
                    // flags not loaded yet, it will be done later in $context->reload_if_dirty()
                }
            }
        }
    }
}


/**
 * Context maintenance and helper methods.
 *
 * This is "extends context" is a bloody hack that tires to work around the deficiencies
 * in the "protected" keyword in PHP, this helps us to hide all the internals of context
 * level implementation from the rest of code, the code completion returns what developers need.
 *
 * Thank you Tim Hunt for helping me with this nasty trick.
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_helper extends context {

    /**
     * @var array An array mapping context levels to classes
     */
    private static $alllevels = array(
            CONTEXT_SYSTEM    => 'context_system',
            CONTEXT_USER      => 'context_user',
            CONTEXT_COURSECAT => 'context_coursecat',
            CONTEXT_COURSE    => 'context_course',
            CONTEXT_MODULE    => 'context_module',
            CONTEXT_BLOCK     => 'context_block',
    );

    /**
     * Instance does not make sense here, only static use
     */
    protected function __construct() {
    }

    /**
     * Returns a class name of the context level class
     *
     * @static
     * @param int $contextlevel (CONTEXT_SYSTEM, etc.)
     * @return string class name of the context class
     */
    public static function get_class_for_level($contextlevel) {
        if (isset(self::$alllevels[$contextlevel])) {
            return self::$alllevels[$contextlevel];
        } else {
            throw new coding_exception('Invalid context level specified');
        }
    }

    /**
     * Returns a list of all context levels
     *
     * @static
     * @return array int=>string (level=>level class name)
     */
    public static function get_all_levels() {
        return self::$alllevels;
    }

    /**
     * Remove stale contexts that belonged to deleted instances.
     * Ideally all code should cleanup contexts properly, unfortunately accidents happen...
     *
     * @static
     * @return void
     */
    public static function cleanup_instances() {
        global $DB;
        $sqls = array();
        foreach (self::$alllevels as $level=>$classname) {
            $sqls[] = $classname::get_cleanup_sql();
        }

        $sql = implode(" UNION ", $sqls);

        // it is probably better to use transactions, it might be faster too
        $transaction = $DB->start_delegated_transaction();

        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $record) {
            $context = context::create_instance_from_record($record);
            $context->delete();
        }
        $rs->close();

        $transaction->allow_commit();
    }

    /**
     * Create all context instances at the given level and above.
     *
     * @static
     * @param int $contextlevel null means all levels
     * @param bool $buildpaths
     * @return void
     */
    public static function create_instances($contextlevel = null, $buildpaths = true) {
        foreach (self::$alllevels as $level=>$classname) {
            if ($contextlevel and $level > $contextlevel) {
                // skip potential sub-contexts
                continue;
            }
            $classname::create_level_instances();
            if ($buildpaths) {
                $classname::build_paths(false);
            }
        }
    }

    /**
     * Rebuild paths and depths in all context levels.
     *
     * @static
     * @param bool $force false means add missing only
     * @return void
     */
    public static function build_all_paths($force = false) {
        foreach (self::$alllevels as $classname) {
            $classname::build_paths($force);
        }

        // reset static course cache - it might have incorrect cached data
        accesslib_clear_all_caches(true);
    }

    /**
     * Resets the cache to remove all data.
     * @static
     */
    public static function reset_caches() {
        context::reset_caches();
    }

    /**
     * Returns all fields necessary for context preloading from user $rec.
     *
     * This helps with performance when dealing with hundreds of contexts.
     *
     * @static
     * @param string $tablealias context table alias in the query
     * @return array (table.column=>alias, ...)
     */
    public static function get_preload_record_columns($tablealias) {
        return array("$tablealias.id"=>"ctxid", "$tablealias.path"=>"ctxpath", "$tablealias.depth"=>"ctxdepth", "$tablealias.contextlevel"=>"ctxlevel", "$tablealias.instanceid"=>"ctxinstance");
    }

    /**
     * Returns all fields necessary for context preloading from user $rec.
     *
     * This helps with performance when dealing with hundreds of contexts.
     *
     * @static
     * @param string $tablealias context table alias in the query
     * @return string
     */
    public static function get_preload_record_columns_sql($tablealias) {
        return "$tablealias.id AS ctxid, $tablealias.path AS ctxpath, $tablealias.depth AS ctxdepth, $tablealias.contextlevel AS ctxlevel, $tablealias.instanceid AS ctxinstance";
    }

    /**
     * Preloads context information from db record and strips the cached info.
     *
     * The db request has to contain all columns from context_helper::get_preload_record_columns().
     *
     * @static
     * @param stdClass $rec
     * @return void (modifies $rec)
     */
     public static function preload_from_record(stdClass $rec) {
         context::preload_from_record($rec);
     }

    /**
     * Preload all contexts instances from course.
     *
     * To be used if you expect multiple queries for course activities...
     *
     * @static
     * @param int $courseid
     */
    public static function preload_course($courseid) {
        // Users can call this multiple times without doing any harm
        if (isset(context::$cache_preloaded[$courseid])) {
            return;
        }
        $coursecontext = context_course::instance($courseid);
        $coursecontext->get_child_contexts();

        context::$cache_preloaded[$courseid] = true;
    }

    /**
     * Delete context instance
     *
     * @static
     * @param int $contextlevel
     * @param int $instanceid
     * @return void
     */
    public static function delete_instance($contextlevel, $instanceid) {
        global $DB;

        // double check the context still exists
        if ($record = $DB->get_record('context', array('contextlevel'=>$contextlevel, 'instanceid'=>$instanceid))) {
            $context = context::create_instance_from_record($record);
            $context->delete();
        } else {
            // we should try to purge the cache anyway
        }
    }

    /**
     * Returns the name of specified context level
     *
     * @static
     * @param int $contextlevel
     * @return string name of the context level
     */
    public static function get_level_name($contextlevel) {
        $classname = context_helper::get_class_for_level($contextlevel);
        return $classname::get_level_name();
    }

    /**
     * not used
     */
    public function get_url() {
    }

    /**
     * not used
     */
    public function get_capabilities() {
    }
}


/**
 * System context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_system extends context {
    /**
     * Please use context_system::instance() if you need the instance of context.
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != CONTEXT_SYSTEM) {
            throw new coding_exception('Invalid $record->contextlevel in context_system constructor.');
        }
    }

    /**
     * Returns human readable context level name.
     *
     * @static
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('coresystem');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix does not apply to system context
     * @param boolean $short does not apply to system context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        return self::get_level_name();
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        return new moodle_url('/');
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities() {
        global $DB;

        $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

        $params = array();
        $sql = "SELECT *
                  FROM {capabilities}";

        return $DB->get_records_sql($sql.' '.$sort, $params);
    }

    /**
     * Create missing context instances at system context
     * @static
     */
    protected static function create_level_instances() {
        // nothing to do here, the system context is created automatically in installer
        self::instance(0);
    }

    /**
     * Returns system context instance.
     *
     * @static
     * @param int $instanceid
     * @param int $strictness
     * @param bool $cache
     * @return context_system context instance
     */
    public static function instance($instanceid = 0, $strictness = MUST_EXIST, $cache = true) {
        global $DB;

        if ($instanceid != 0) {
            debugging('context_system::instance(): invalid $id parameter detected, should be 0');
        }

        if (defined('SYSCONTEXTID') and $cache) { // dangerous: define this in config.php to eliminate 1 query/page
            if (!isset(context::$systemcontext)) {
                $record = new stdClass();
                $record->id           = SYSCONTEXTID;
                $record->contextlevel = CONTEXT_SYSTEM;
                $record->instanceid   = 0;
                $record->path         = '/'.SYSCONTEXTID;
                $record->depth        = 1;
                context::$systemcontext = new context_system($record);
            }
            return context::$systemcontext;
        }


        try {
            // we ignore the strictness completely because system context must except except during install
            $record = $DB->get_record('context', array('contextlevel'=>CONTEXT_SYSTEM), '*', MUST_EXIST);
        } catch (dml_exception $e) {
            //table or record does not exist
            if (!during_initial_install()) {
                // do not mess with system context after install, it simply must exist
                throw $e;
            }
            $record = null;
        }

        if (!$record) {
            $record = new stdClass();
            $record->contextlevel = CONTEXT_SYSTEM;
            $record->instanceid   = 0;
            $record->depth        = 1;
            $record->path         = null; //not known before insert

            try {
                if ($DB->count_records('context')) {
                    // contexts already exist, this is very weird, system must be first!!!
                    return null;
                }
                if (defined('SYSCONTEXTID')) {
                    // this would happen only in unittest on sites that went through weird 1.7 upgrade
                    $record->id = SYSCONTEXTID;
                    $DB->import_record('context', $record);
                    $DB->get_manager()->reset_sequence('context');
                } else {
                    $record->id = $DB->insert_record('context', $record);
                }
            } catch (dml_exception $e) {
                // can not create context - table does not exist yet, sorry
                return null;
            }
        }

        if ($record->instanceid != 0) {
            // this is very weird, somebody must be messing with context table
            debugging('Invalid system context detected');
        }

        if ($record->depth != 1 or $record->path != '/'.$record->id) {
            // fix path if necessary, initial install or path reset
            $record->depth = 1;
            $record->path  = '/'.$record->id;
            $DB->update_record('context', $record);
        }

        if (!defined('SYSCONTEXTID')) {
            define('SYSCONTEXTID', $record->id);
        }

        context::$systemcontext = new context_system($record);
        return context::$systemcontext;
    }

    /**
     * Returns all site contexts except the system context, DO NOT call on production servers!!
     *
     * Contexts are not cached.
     *
     * @return array
     */
    public function get_child_contexts() {
        global $DB;

        debugging('Fetching of system context child courses is strongly discouraged on production servers (it may eat all available memory)!');

        // Just get all the contexts except for CONTEXT_SYSTEM level
        // and hope we don't OOM in the process - don't cache
        $sql = "SELECT c.*
                  FROM {context} c
                 WHERE contextlevel > ".CONTEXT_SYSTEM;
        $records = $DB->get_records_sql($sql);

        $result = array();
        foreach ($records as $record) {
            $result[$record->id] = context::create_instance_from_record($record);
        }

        return $result;
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
                   WHERE 1=2
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at system context level.
     *
     * @static
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        /* note: ignore $force here, we always do full test of system context */

        // exactly one record must exist
        $record = $DB->get_record('context', array('contextlevel'=>CONTEXT_SYSTEM), '*', MUST_EXIST);

        if ($record->instanceid != 0) {
            debugging('Invalid system context detected');
        }

        if (defined('SYSCONTEXTID') and $record->id != SYSCONTEXTID) {
            debugging('Invalid SYSCONTEXTID detected');
        }

        if ($record->depth != 1 or $record->path != '/'.$record->id) {
            // fix path if necessary, initial install or path reset
            $record->depth    = 1;
            $record->path     = '/'.$record->id;
            $DB->update_record('context', $record);
        }
    }
}


/**
 * User context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_user extends context {
    /**
     * Please use context_user::instance($userid) if you need the instance of context.
     * Alternatively if you know only the context id use context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != CONTEXT_USER) {
            throw new coding_exception('Invalid $record->contextlevel in context_user constructor.');
        }
    }

    /**
     * Returns human readable context level name.
     *
     * @static
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('user');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with User
     * @param boolean $short does not apply to user context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        global $DB;

        $name = '';
        if ($user = $DB->get_record('user', array('id'=>$this->_instanceid, 'deleted'=>0))) {
            if ($withprefix){
                $name = get_string('user').': ';
            }
            $name .= fullname($user);
        }
        return $name;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        global $COURSE;

        if ($COURSE->id == SITEID) {
            $url = new moodle_url('/user/profile.php', array('id'=>$this->_instanceid));
        } else {
            $url = new moodle_url('/user/view.php', array('id'=>$this->_instanceid, 'courseid'=>$COURSE->id));
        }
        return $url;
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities() {
        global $DB;

        $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

        $extracaps = array('moodle/grade:viewall');
        list($extra, $params) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap');
        $sql = "SELECT *
                  FROM {capabilities}
                 WHERE contextlevel = ".CONTEXT_USER."
                       OR name $extra";

        return $records = $DB->get_records_sql($sql.' '.$sort, $params);
    }

    /**
     * Returns user context instance.
     *
     * @static
     * @param int $instanceid
     * @param int $strictness
     * @return context_user context instance
     */
    public static function instance($instanceid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(CONTEXT_USER, $instanceid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel'=>CONTEXT_USER, 'instanceid'=>$instanceid))) {
            if ($user = $DB->get_record('user', array('id'=>$instanceid, 'deleted'=>0), 'id', $strictness)) {
                $record = context::insert_context_record(CONTEXT_USER, $user->id, '/'.SYSCONTEXTID, 0);
            }
        }

        if ($record) {
            $context = new context_user($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Create missing context instances at user context level
     * @static
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_USER.", u.id
                  FROM {user} u
                 WHERE u.deleted = 0
                       AND NOT EXISTS (SELECT 'x'
                                         FROM {context} cx
                                        WHERE u.id = cx.instanceid AND cx.contextlevel=".CONTEXT_USER.")";
        $DB->execute($sql);
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
         LEFT OUTER JOIN {user} u ON (c.instanceid = u.id AND u.deleted = 0)
                   WHERE u.id IS NULL AND c.contextlevel = ".CONTEXT_USER."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at user context level.
     *
     * @static
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        // First update normal users.
        $path = $DB->sql_concat('?', 'id');
        $pathstart = '/' . SYSCONTEXTID . '/';
        $params = array($pathstart);

        if ($force) {
            $where = "depth <> 2 OR path IS NULL OR path <> ({$path})";
            $params[] = $pathstart;
        } else {
            $where = "depth = 0 OR path IS NULL";
        }

        $sql = "UPDATE {context}
                   SET depth = 2,
                       path = {$path}
                 WHERE contextlevel = " . CONTEXT_USER . "
                   AND ($where)";
        $DB->execute($sql, $params);
    }
}


/**
 * Course category context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_coursecat extends context {
    /**
     * Please use context_coursecat::instance($coursecatid) if you need the instance of context.
     * Alternatively if you know only the context id use context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != CONTEXT_COURSECAT) {
            throw new coding_exception('Invalid $record->contextlevel in context_coursecat constructor.');
        }
    }

    /**
     * Returns human readable context level name.
     *
     * @static
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('category');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with Category
     * @param boolean $short does not apply to course categories
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        global $DB;

        $name = '';
        if ($category = $DB->get_record('course_categories', array('id'=>$this->_instanceid))) {
            if ($withprefix){
                $name = get_string('category').': ';
            }
            $name .= format_string($category->name, true, array('context' => $this));
        }
        return $name;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        return new moodle_url('/course/category.php', array('id'=>$this->_instanceid));
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities() {
        global $DB;

        $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

        $params = array();
        $sql = "SELECT *
                  FROM {capabilities}
                 WHERE contextlevel IN (".CONTEXT_COURSECAT.",".CONTEXT_COURSE.",".CONTEXT_MODULE.",".CONTEXT_BLOCK.")";

        return $DB->get_records_sql($sql.' '.$sort, $params);
    }

    /**
     * Returns course category context instance.
     *
     * @static
     * @param int $instanceid
     * @param int $strictness
     * @return context_coursecat context instance
     */
    public static function instance($instanceid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(CONTEXT_COURSECAT, $instanceid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel'=>CONTEXT_COURSECAT, 'instanceid'=>$instanceid))) {
            if ($category = $DB->get_record('course_categories', array('id'=>$instanceid), 'id,parent', $strictness)) {
                if ($category->parent) {
                    $parentcontext = context_coursecat::instance($category->parent);
                    $record = context::insert_context_record(CONTEXT_COURSECAT, $category->id, $parentcontext->path);
                } else {
                    $record = context::insert_context_record(CONTEXT_COURSECAT, $category->id, '/'.SYSCONTEXTID, 0);
                }
            }
        }

        if ($record) {
            $context = new context_coursecat($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Returns immediate child contexts of category and all subcategories,
     * children of subcategories and courses are not returned.
     *
     * @return array
     */
    public function get_child_contexts() {
        global $DB;

        $sql = "SELECT ctx.*
                  FROM {context} ctx
                 WHERE ctx.path LIKE ? AND (ctx.depth = ? OR ctx.contextlevel = ?)";
        $params = array($this->_path.'/%', $this->depth+1, CONTEXT_COURSECAT);
        $records = $DB->get_records_sql($sql, $params);

        $result = array();
        foreach ($records as $record) {
            $result[$record->id] = context::create_instance_from_record($record);
        }

        return $result;
    }

    /**
     * Create missing context instances at course category context level
     * @static
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_COURSECAT.", cc.id
                  FROM {course_categories} cc
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE cc.id = cx.instanceid AND cx.contextlevel=".CONTEXT_COURSECAT.")";
        $DB->execute($sql);
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
         LEFT OUTER JOIN {course_categories} cc ON c.instanceid = cc.id
                   WHERE cc.id IS NULL AND c.contextlevel = ".CONTEXT_COURSECAT."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at course category context level.
     *
     * @static
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force or $DB->record_exists_select('context', "contextlevel = ".CONTEXT_COURSECAT." AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = $emptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
                $emptyclause    = "AND ({context}.path IS NULL OR {context}.depth = 0)";
            }

            $base = '/'.SYSCONTEXTID;

            // Normal top level categories
            $sql = "UPDATE {context}
                       SET depth=2,
                           path=".$DB->sql_concat("'$base/'", 'id')."
                     WHERE contextlevel=".CONTEXT_COURSECAT."
                           AND EXISTS (SELECT 'x'
                                         FROM {course_categories} cc
                                        WHERE cc.id = {context}.instanceid AND cc.depth=1)
                           $emptyclause";
            $DB->execute($sql);

            // Deeper categories - one query per depthlevel
            $maxdepth = $DB->get_field_sql("SELECT MAX(depth) FROM {course_categories}");
            for ($n=2; $n<=$maxdepth; $n++) {
                $sql = "INSERT INTO {context_temp} (id, path, depth)
                        SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
                          FROM {context} ctx
                          JOIN {course_categories} cc ON (cc.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_COURSECAT." AND cc.depth = $n)
                          JOIN {context} pctx ON (pctx.instanceid = cc.parent AND pctx.contextlevel = ".CONTEXT_COURSECAT.")
                         WHERE pctx.path IS NOT NULL AND pctx.depth > 0
                               $ctxemptyclause";
                $trans = $DB->start_delegated_transaction();
                $DB->delete_records('context_temp');
                $DB->execute($sql);
                context::merge_context_temp_table();
                $DB->delete_records('context_temp');
                $trans->allow_commit();

            }
        }
    }
}


/**
 * Course context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_course extends context {
    /**
     * Please use context_course::instance($courseid) if you need the instance of context.
     * Alternatively if you know only the context id use context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != CONTEXT_COURSE) {
            throw new coding_exception('Invalid $record->contextlevel in context_course constructor.');
        }
    }

    /**
     * Returns human readable context level name.
     *
     * @static
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('course');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with Course
     * @param boolean $short whether to use the short name of the thing.
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        global $DB;

        $name = '';
        if ($this->_instanceid == SITEID) {
            $name = get_string('frontpage', 'admin');
        } else {
            if ($course = $DB->get_record('course', array('id'=>$this->_instanceid))) {
                if ($withprefix){
                    $name = get_string('course').': ';
                }
                if ($short){
                    $name .= format_string($course->shortname, true, array('context' => $this));
                } else {
                    $name .= format_string(get_course_display_name_for_list($course));
               }
            }
        }
        return $name;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        if ($this->_instanceid != SITEID) {
            return new moodle_url('/course/view.php', array('id'=>$this->_instanceid));
        }

        return new moodle_url('/');
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities() {
        global $DB;

        $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

        $params = array();
        $sql = "SELECT *
                  FROM {capabilities}
                 WHERE contextlevel IN (".CONTEXT_COURSE.",".CONTEXT_MODULE.",".CONTEXT_BLOCK.")";

        return $DB->get_records_sql($sql.' '.$sort, $params);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course_context context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        return $this;
    }

    /**
     * Returns course context instance.
     *
     * @static
     * @param int $instanceid
     * @param int $strictness
     * @return context_course context instance
     */
    public static function instance($instanceid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(CONTEXT_COURSE, $instanceid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel'=>CONTEXT_COURSE, 'instanceid'=>$instanceid))) {
            if ($course = $DB->get_record('course', array('id'=>$instanceid), 'id,category', $strictness)) {
                if ($course->category) {
                    $parentcontext = context_coursecat::instance($course->category);
                    $record = context::insert_context_record(CONTEXT_COURSE, $course->id, $parentcontext->path);
                } else {
                    $record = context::insert_context_record(CONTEXT_COURSE, $course->id, '/'.SYSCONTEXTID, 0);
                }
            }
        }

        if ($record) {
            $context = new context_course($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Create missing context instances at course context level
     * @static
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_COURSE.", c.id
                  FROM {course} c
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE c.id = cx.instanceid AND cx.contextlevel=".CONTEXT_COURSE.")";
        $DB->execute($sql);
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
         LEFT OUTER JOIN {course} co ON c.instanceid = co.id
                   WHERE co.id IS NULL AND c.contextlevel = ".CONTEXT_COURSE."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at course context level.
     *
     * @static
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force or $DB->record_exists_select('context', "contextlevel = ".CONTEXT_COURSE." AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = $emptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
                $emptyclause    = "AND ({context}.path IS NULL OR {context}.depth = 0)";
            }

            $base = '/'.SYSCONTEXTID;

            // Standard frontpage
            $sql = "UPDATE {context}
                       SET depth = 2,
                           path = ".$DB->sql_concat("'$base/'", 'id')."
                     WHERE contextlevel = ".CONTEXT_COURSE."
                           AND EXISTS (SELECT 'x'
                                         FROM {course} c
                                        WHERE c.id = {context}.instanceid AND c.category = 0)
                           $emptyclause";
            $DB->execute($sql);

            // standard courses
            $sql = "INSERT INTO {context_temp} (id, path, depth)
                    SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
                      FROM {context} ctx
                      JOIN {course} c ON (c.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_COURSE." AND c.category <> 0)
                      JOIN {context} pctx ON (pctx.instanceid = c.category AND pctx.contextlevel = ".CONTEXT_COURSECAT.")
                     WHERE pctx.path IS NOT NULL AND pctx.depth > 0
                           $ctxemptyclause";
            $trans = $DB->start_delegated_transaction();
            $DB->delete_records('context_temp');
            $DB->execute($sql);
            context::merge_context_temp_table();
            $DB->delete_records('context_temp');
            $trans->allow_commit();
        }
    }
}


/**
 * Course module context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_module extends context {
    /**
     * Please use context_module::instance($cmid) if you need the instance of context.
     * Alternatively if you know only the context id use context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != CONTEXT_MODULE) {
            throw new coding_exception('Invalid $record->contextlevel in context_module constructor.');
        }
    }

    /**
     * Returns human readable context level name.
     *
     * @static
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('activitymodule');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with the
     *      module name, e.g. Forum, Glossary, etc.
     * @param boolean $short does not apply to module context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        global $DB;

        $name = '';
        if ($cm = $DB->get_record_sql("SELECT cm.*, md.name AS modname
                                         FROM {course_modules} cm
                                         JOIN {modules} md ON md.id = cm.module
                                        WHERE cm.id = ?", array($this->_instanceid))) {
            if ($mod = $DB->get_record($cm->modname, array('id' => $cm->instance))) {
                    if ($withprefix){
                        $name = get_string('modulename', $cm->modname).': ';
                    }
                    $name .= format_string($mod->name, true, array('context' => $this));
                }
            }
        return $name;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        global $DB;

        if ($modname = $DB->get_field_sql("SELECT md.name AS modname
                                             FROM {course_modules} cm
                                             JOIN {modules} md ON md.id = cm.module
                                            WHERE cm.id = ?", array($this->_instanceid))) {
            return new moodle_url('/mod/' . $modname . '/view.php', array('id'=>$this->_instanceid));
        }

        return new moodle_url('/');
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities() {
        global $DB, $CFG;

        $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

        $cm = $DB->get_record('course_modules', array('id'=>$this->_instanceid));
        $module = $DB->get_record('modules', array('id'=>$cm->module));

        $subcaps = array();
        $subpluginsfile = "$CFG->dirroot/mod/$module->name/db/subplugins.php";
        if (file_exists($subpluginsfile)) {
            $subplugins = array();  // should be redefined in the file
            include($subpluginsfile);
            if (!empty($subplugins)) {
                foreach (array_keys($subplugins) as $subplugintype) {
                    foreach (array_keys(get_plugin_list($subplugintype)) as $subpluginname) {
                        $subcaps = array_merge($subcaps, array_keys(load_capability_def($subplugintype.'_'.$subpluginname)));
                    }
                }
            }
        }

        $modfile = "$CFG->dirroot/mod/$module->name/lib.php";
        $extracaps = array();
        if (file_exists($modfile)) {
            include_once($modfile);
            $modfunction = $module->name.'_get_extra_capabilities';
            if (function_exists($modfunction)) {
                $extracaps = $modfunction();
            }
        }

        $extracaps = array_merge($subcaps, $extracaps);
        $extra = '';
        list($extra, $params) = $DB->get_in_or_equal(
            $extracaps, SQL_PARAMS_NAMED, 'cap0', true, '');
        if (!empty($extra)) {
            $extra = "OR name $extra";
        }
        $sql = "SELECT *
                  FROM {capabilities}
                 WHERE (contextlevel = ".CONTEXT_MODULE."
                       AND (component = :component OR component = 'moodle'))
                       $extra";
        $params['component'] = "mod_$module->name";

        return $DB->get_records_sql($sql.' '.$sort, $params);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course_context context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        return $this->get_parent_context();
    }

    /**
     * Returns module context instance.
     *
     * @static
     * @param int $instanceid
     * @param int $strictness
     * @return context_module context instance
     */
    public static function instance($instanceid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(CONTEXT_MODULE, $instanceid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel'=>CONTEXT_MODULE, 'instanceid'=>$instanceid))) {
            if ($cm = $DB->get_record('course_modules', array('id'=>$instanceid), 'id,course', $strictness)) {
                $parentcontext = context_course::instance($cm->course);
                $record = context::insert_context_record(CONTEXT_MODULE, $cm->id, $parentcontext->path);
            }
        }

        if ($record) {
            $context = new context_module($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Create missing context instances at module context level
     * @static
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_MODULE.", cm.id
                  FROM {course_modules} cm
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE cm.id = cx.instanceid AND cx.contextlevel=".CONTEXT_MODULE.")";
        $DB->execute($sql);
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
         LEFT OUTER JOIN {course_modules} cm ON c.instanceid = cm.id
                   WHERE cm.id IS NULL AND c.contextlevel = ".CONTEXT_MODULE."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at module context level.
     *
     * @static
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force or $DB->record_exists_select('context', "contextlevel = ".CONTEXT_MODULE." AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
            }

            $sql = "INSERT INTO {context_temp} (id, path, depth)
                    SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
                      FROM {context} ctx
                      JOIN {course_modules} cm ON (cm.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_MODULE.")
                      JOIN {context} pctx ON (pctx.instanceid = cm.course AND pctx.contextlevel = ".CONTEXT_COURSE.")
                     WHERE pctx.path IS NOT NULL AND pctx.depth > 0
                           $ctxemptyclause";
            $trans = $DB->start_delegated_transaction();
            $DB->delete_records('context_temp');
            $DB->execute($sql);
            context::merge_context_temp_table();
            $DB->delete_records('context_temp');
            $trans->allow_commit();
        }
    }
}


/**
 * Block context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     2.2
 */
class context_block extends context {
    /**
     * Please use context_block::instance($blockinstanceid) if you need the instance of context.
     * Alternatively if you know only the context id use context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != CONTEXT_BLOCK) {
            throw new coding_exception('Invalid $record->contextlevel in context_block constructor');
        }
    }

    /**
     * Returns human readable context level name.
     *
     * @static
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('block');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with Block
     * @param boolean $short does not apply to block context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false) {
        global $DB, $CFG;

        $name = '';
        if ($blockinstance = $DB->get_record('block_instances', array('id'=>$this->_instanceid))) {
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

        return $name;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        $parentcontexts = $this->get_parent_context();
        return $parentcontexts->get_url();
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @return array
     */
    public function get_capabilities() {
        global $DB;

        $sort = 'ORDER BY contextlevel,component,name';   // To group them sensibly for display

        $params = array();
        $bi = $DB->get_record('block_instances', array('id' => $this->_instanceid));

        $extra = '';
        $extracaps = block_method_result($bi->blockname, 'get_extra_capabilities');
        if ($extracaps) {
            list($extra, $params) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap');
            $extra = "OR name $extra";
        }

        $sql = "SELECT *
                  FROM {capabilities}
                 WHERE (contextlevel = ".CONTEXT_BLOCK."
                       AND component = :component)
                       $extra";
        $params['component'] = 'block_' . $bi->blockname;

        return $DB->get_records_sql($sql.' '.$sort, $params);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course_context context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        $parentcontext = $this->get_parent_context();
        return $parentcontext->get_course_context($strict);
    }

    /**
     * Returns block context instance.
     *
     * @static
     * @param int $instanceid
     * @param int $strictness
     * @return context_block context instance
     */
    public static function instance($instanceid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(CONTEXT_BLOCK, $instanceid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel'=>CONTEXT_BLOCK, 'instanceid'=>$instanceid))) {
            if ($bi = $DB->get_record('block_instances', array('id'=>$instanceid), 'id,parentcontextid', $strictness)) {
                $parentcontext = context::instance_by_id($bi->parentcontextid);
                $record = context::insert_context_record(CONTEXT_BLOCK, $bi->id, $parentcontext->path);
            }
        }

        if ($record) {
            $context = new context_block($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Block do not have child contexts...
     * @return array
     */
    public function get_child_contexts() {
        return array();
    }

    /**
     * Create missing context instances at block context level
     * @static
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT ".CONTEXT_BLOCK.", bi.id
                  FROM {block_instances} bi
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE bi.id = cx.instanceid AND cx.contextlevel=".CONTEXT_BLOCK.")";
        $DB->execute($sql);
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @static
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
         LEFT OUTER JOIN {block_instances} bi ON c.instanceid = bi.id
                   WHERE bi.id IS NULL AND c.contextlevel = ".CONTEXT_BLOCK."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at block context level.
     *
     * @static
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force or $DB->record_exists_select('context', "contextlevel = ".CONTEXT_BLOCK." AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
            }

            // pctx.path IS NOT NULL prevents fatal problems with broken block instances that point to invalid context parent
            $sql = "INSERT INTO {context_temp} (id, path, depth)
                    SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
                      FROM {context} ctx
                      JOIN {block_instances} bi ON (bi.id = ctx.instanceid AND ctx.contextlevel = ".CONTEXT_BLOCK.")
                      JOIN {context} pctx ON (pctx.id = bi.parentcontextid)
                     WHERE (pctx.path IS NOT NULL AND pctx.depth > 0)
                           $ctxemptyclause";
            $trans = $DB->start_delegated_transaction();
            $DB->delete_records('context_temp');
            $DB->execute($sql);
            context::merge_context_temp_table();
            $DB->delete_records('context_temp');
            $trans->allow_commit();
        }
    }
}


// ============== DEPRECATED FUNCTIONS ==========================================
// Old context related functions were deprecated in 2.0, it is recommended
// to use context classes in new code. Old function can be used when
// creating patches that are supposed to be backported to older stable branches.
// These deprecated functions will not be removed in near future,
// before removing devs will be warned with a debugging message first,
// then we will add error message and only after that we can remove the functions
// completely.


/**
 * Not available any more, use load_temp_course_role() instead.
 *
 * @deprecated since 2.2
 * @param stdClass $context
 * @param int $roleid
 * @param array $accessdata
 * @return array
 */
function load_temp_role($context, $roleid, array $accessdata) {
    debugging('load_temp_role() is deprecated, please use load_temp_course_role() instead, temp role not loaded.');
    return $accessdata;
}

/**
 * Not available any more, use remove_temp_course_roles() instead.
 *
 * @deprecated since 2.2
 * @param stdClass $context
 * @param array $accessdata
 * @return array access data
 */
function remove_temp_roles($context, array $accessdata) {
    debugging('remove_temp_role() is deprecated, please use remove_temp_course_roles() instead.');
    return $accessdata;
}

/**
 * Returns system context or null if can not be created yet.
 *
 * @deprecated since 2.2, use context_system::instance()
 * @param bool $cache use caching
 * @return context system context (null if context table not created yet)
 */
function get_system_context($cache = true) {
    return context_system::instance(0, IGNORE_MISSING, $cache);
}

/**
 * Get the context instance as an object. This function will create the
 * context instance if it does not exist yet.
 *
 * @deprecated since 2.2, use context_course::instance() or other relevant class instead
 * @param integer $contextlevel The context level, for example CONTEXT_COURSE, or CONTEXT_MODULE.
 * @param integer $instance The instance id. For $level = CONTEXT_COURSE, this would be $course->id,
 *      for $level = CONTEXT_MODULE, this would be $cm->id. And so on. Defaults to 0
 * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
 *      MUST_EXIST means throw exception if no record or multiple records found
 * @return context The context object.
 */
function get_context_instance($contextlevel, $instance = 0, $strictness = IGNORE_MISSING) {
    $instances = (array)$instance;
    $contexts = array();

    $classname = context_helper::get_class_for_level($contextlevel);

    // we do not load multiple contexts any more, PAGE should be responsible for any preloading
    foreach ($instances as $inst) {
        $contexts[$inst] = $classname::instance($inst, $strictness);
    }

    if (is_array($instance)) {
        return $contexts;
    } else {
        return $contexts[$instance];
    }
}

/**
 * Get a context instance as an object, from a given context id.
 *
 * @deprecated since 2.2, use context::instance_by_id($id) instead
 * @param int $id context id
 * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
 *                        MUST_EXIST means throw exception if no record or multiple records found
 * @return context|bool the context object or false if not found.
 */
function get_context_instance_by_id($id, $strictness = IGNORE_MISSING) {
    return context::instance_by_id($id, $strictness);
}

/**
 * Recursive function which, given a context, find all parent context ids,
 * and return the array in reverse order, i.e. parent first, then grand
 * parent, etc.
 *
 * @deprecated since 2.2, use $context->get_parent_context_ids() instead
 * @param context $context
 * @param bool $includeself optional, defaults to false
 * @return array
 */
function get_parent_contexts(context $context, $includeself = false) {
    return $context->get_parent_context_ids($includeself);
}

/**
 * Return the id of the parent of this context, or false if there is no parent (only happens if this
 * is the site context.)
 *
 * @deprecated since 2.2, use $context->get_parent_context() instead
 * @param context $context
 * @return integer the id of the parent context.
 */
function get_parent_contextid(context $context) {
    if ($parent = $context->get_parent_context()) {
        return $parent->id;
    } else {
        return false;
    }
}

/**
 * Recursive function which, given a context, find all its children context ids.
 *
 * For course category contexts it will return immediate children only categories and courses.
 * It will NOT recurse into courses or child categories.
 * If you want to do that, call it on the returned courses/categories.
 *
 * When called for a course context, it will return the modules and blocks
 * displayed in the course page.
 *
 * If called on a user/course/module context it _will_ populate the cache with the appropriate
 * contexts ;-)
 *
 * @deprecated since 2.2, use $context->get_child_contexts() instead
 * @param context $context
 * @return array Array of child records
 */
function get_child_contexts(context $context) {
    return $context->get_child_contexts();
}

/**
 * Precreates all contexts including all parents
 *
 * @deprecated since 2.2
 * @param int $contextlevel empty means all
 * @param bool $buildpaths update paths and depths
 * @return void
 */
function create_contexts($contextlevel = null, $buildpaths = true) {
    context_helper::create_instances($contextlevel, $buildpaths);
}

/**
 * Remove stale context records
 *
 * @deprecated since 2.2, use context_helper::cleanup_instances() instead
 * @return bool
 */
function cleanup_contexts() {
    context_helper::cleanup_instances();
    return true;
}

/**
 * Populate context.path and context.depth where missing.
 *
 * @deprecated since 2.2, use context_helper::build_all_paths() instead
 * @param bool $force force a complete rebuild of the path and depth fields, defaults to false
 * @return void
 */
function build_context_path($force = false) {
    context_helper::build_all_paths($force);
}

/**
 * Rebuild all related context depth and path caches
 *
 * @deprecated since 2.2
 * @param array $fixcontexts array of contexts, strongtyped
 * @return void
 */
function rebuild_contexts(array $fixcontexts) {
    foreach ($fixcontexts as $fixcontext) {
        $fixcontext->reset_paths(false);
    }
    context_helper::build_all_paths(false);
}

/**
 * Preloads all contexts relating to a course: course, modules. Block contexts
 * are no longer loaded here. The contexts for all the blocks on the current
 * page are now efficiently loaded by {@link block_manager::load_blocks()}.
 *
 * @deprecated since 2.2
 * @param int $courseid Course ID
 * @return void
 */
function preload_course_contexts($courseid) {
    context_helper::preload_course($courseid);
}

/**
 * Preloads context information together with instances.
 * Use context_instance_preload() to strip the context info from the record and cache the context instance.
 *
 * @deprecated since 2.2
 * @param string $joinon for example 'u.id'
 * @param string $contextlevel context level of instance in $joinon
 * @param string $tablealias context table alias
 * @return array with two values - select and join part
 */
function context_instance_preload_sql($joinon, $contextlevel, $tablealias) {
    $select = ", ".context_helper::get_preload_record_columns_sql($tablealias);
    $join = "LEFT JOIN {context} $tablealias ON ($tablealias.instanceid = $joinon AND $tablealias.contextlevel = $contextlevel)";
    return array($select, $join);
}

/**
 * Preloads context information from db record and strips the cached info.
 * The db request has to contain both the $join and $select from context_instance_preload_sql()
 *
 * @deprecated since 2.2
 * @param stdClass $rec
 * @return void (modifies $rec)
 */
function context_instance_preload(stdClass $rec) {
    context_helper::preload_from_record($rec);
}

/**
 * Mark a context as dirty (with timestamp) so as to force reloading of the context.
 *
 * @deprecated since 2.2, use $context->mark_dirty() instead
 * @param string $path context path
 */
function mark_context_dirty($path) {
    global $CFG, $USER, $ACCESSLIB_PRIVATE;

    if (during_initial_install()) {
        return;
    }

    // only if it is a non-empty string
    if (is_string($path) && $path !== '') {
        set_cache_flag('accesslib/dirtycontexts', $path, 1, time()+$CFG->sessiontimeout);
        if (isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
            $ACCESSLIB_PRIVATE->dirtycontexts[$path] = 1;
        } else {
            if (CLI_SCRIPT) {
                $ACCESSLIB_PRIVATE->dirtycontexts = array($path => 1);
            } else {
                if (isset($USER->access['time'])) {
                    $ACCESSLIB_PRIVATE->dirtycontexts = get_cache_flags('accesslib/dirtycontexts', $USER->access['time']-2);
                } else {
                    $ACCESSLIB_PRIVATE->dirtycontexts = array($path => 1);
                }
                // flags not loaded yet, it will be done later in $context->reload_if_dirty()
            }
        }
    }
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
 * @deprecated since 2.2
 * @param context $context context obj
 * @param context $newparent new parent obj
 * @return void
 */
function context_moved(context $context, context $newparent) {
    $context->update_moved($newparent);
}

/**
 * Remove a context record and any dependent entries,
 * removes context from static context cache too
 *
 * @deprecated since 2.2, use $context->delete_content() instead
 * @param int $contextlevel
 * @param int $instanceid
 * @param bool $deleterecord false means keep record for now
 * @return bool returns true or throws an exception
 */
function delete_context($contextlevel, $instanceid, $deleterecord = true) {
    if ($deleterecord) {
        context_helper::delete_instance($contextlevel, $instanceid);
    } else {
        $classname = context_helper::get_class_for_level($contextlevel);
        if ($context = $classname::instance($instanceid, IGNORE_MISSING)) {
            $context->delete_content();
        }
    }

    return true;
}

/**
 * Returns context level name
 *
 * @deprecated since 2.2
 * @param integer $contextlevel $context->context level. One of the CONTEXT_... constants.
 * @return string the name for this type of context.
 */
function get_contextlevel_name($contextlevel) {
    return context_helper::get_level_name($contextlevel);
}

/**
 * Prints human readable context identifier.
 *
 * @deprecated since 2.2
 * @param context $context the context.
 * @param boolean $withprefix whether to prefix the name of the context with the
 *      type of context, e.g. User, Course, Forum, etc.
 * @param boolean $short whether to user the short name of the thing. Only applies
 *      to course contexts
 * @return string the human readable context name.
 */
function print_context_name(context $context, $withprefix = true, $short = false) {
    return $context->get_context_name($withprefix, $short);
}

/**
 * Get a URL for a context, if there is a natural one. For example, for
 * CONTEXT_COURSE, this is the course page. For CONTEXT_USER it is the
 * user profile page.
 *
 * @deprecated since 2.2
 * @param context $context the context.
 * @return moodle_url
 */
function get_context_url(context $context) {
    return $context->get_url();
}

/**
 * Is this context part of any course? if yes return course context,
 * if not return null or throw exception.
 *
 * @deprecated since 2.2, use $context->get_course_context() instead
 * @param context $context
 * @return course_context context of the enclosing course, null if not found or exception
 */
function get_course_context(context $context) {
    return $context->get_course_context(true);
}

/**
 * Returns current course id or null if outside of course based on context parameter.
 *
 * @deprecated since 2.2, use  $context->get_course_context instead
 * @param context $context
 * @return int|bool related course id or false
 */
function get_courseid_from_context(context $context) {
    if ($coursecontext = $context->get_course_context(false)) {
        return $coursecontext->instanceid;
    } else {
        return false;
    }
}

/**
 * Get an array of courses where cap requested is available
 * and user is enrolled, this can be relatively slow.
 *
 * @deprecated since 2.2, use enrol_get_users_courses() instead
 * @param int    $userid A user id. By default (null) checks the permissions of the current user.
 * @param string $cap - name of the capability
 * @param array  $accessdata_ignored
 * @param bool   $doanything_ignored
 * @param string $sort - sorting fields - prefix each fieldname with "c."
 * @param array  $fields - additional fields you are interested in...
 * @param int    $limit_ignored
 * @return array $courses - ordered array of course objects - see notes above
 */
function get_user_courses_bycap($userid, $cap, $accessdata_ignored, $doanything_ignored, $sort = 'c.sortorder ASC', $fields = null, $limit_ignored = 0) {

    $courses = enrol_get_users_courses($userid, true, $fields, $sort);
    foreach ($courses as $id=>$course) {
        $context = context_course::instance($id);
        if (!has_capability($cap, $context, $userid)) {
            unset($courses[$id]);
        }
    }

    return $courses;
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
 * @deprecated since 2.2
 * @param context $context
 * @return array
 */
function fetch_context_capabilities(context $context) {
    return $context->get_capabilities();
}

/**
 * Runs get_records select on context table and returns the result
 * Does get_records_select on the context table, and returns the results ordered
 * by contextlevel, and then the natural sort order within each level.
 * for the purpose of $select, you need to know that the context table has been
 * aliased to ctx, so for example, you can call get_sorted_contexts('ctx.depth = 3');
 *
 * @deprecated since 2.2
 * @param string $select the contents of the WHERE clause. Remember to do ctx.fieldname.
 * @param array $params any parameters required by $select.
 * @return array the requested context records.
 */
function get_sorted_contexts($select, $params = array()) {

    //TODO: we should probably rewrite all the code that is using this thing, the trouble is we MUST NOT modify the context instances...

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
 * This is really slow!!! do not use above course context level
 *
 * @deprecated since 2.2
 * @param int $roleid
 * @param context $context
 * @return array
 */
function get_role_context_caps($roleid, context $context) {
    global $DB;

    //this is really slow!!!! - do not use above course context level!
    $result = array();
    $result[$context->id] = array();

    // first emulate the parent context capabilities merging into context
    $searchcontexts = array_reverse($context->get_parent_context_ids(true));
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

    // now go through the contexts below given context
    $searchcontexts = array_keys($context->get_child_contexts());
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
 * Gets a string for sql calls, searching for stuff in this context or above
 *
 * NOTE: use $DB->get_in_or_equal($context->get_parent_context_ids()...
 *
 * @deprecated since 2.2, $context->use get_parent_context_ids() instead
 * @param context $context
 * @return string
 */
function get_related_contexts_string(context $context) {

    if ($parents = $context->get_parent_context_ids()) {
        return (' IN ('.$context->id.','.implode(',', $parents).')');
    } else {
        return (' ='.$context->id);
    }
}

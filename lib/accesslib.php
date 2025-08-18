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
 * - get_user_roles_sitewide_accessdata()
 * - etc.
 *
 * <b>Name conventions</b>
 *
 * "ctx" means context
 * "ra" means role assignment
 * "rdef" means role definition
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
 * role assignments (RAs), role switches and initialization time.
 *
 * Things are keyed on "contextpaths" (the path field of
 * the context table) for fast walking up/down the tree.
 * <code>
 * $accessdata['ra'][$contextpath] = array($roleid=>$roleid)
 *                  [$contextpath] = array($roleid=>$roleid)
 *                  [$contextpath] = array($roleid=>$roleid)
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

/** Capability allow management of trusts - NOT IMPLEMENTED YET - see {@link https://moodledev.io/docs/apis/subsystems/roles} */
define('RISK_MANAGETRUST', 0x0001);
/** Capability allows changes in system configuration - see {@link https://moodledev.io/docs/apis/subsystems/roles} */
define('RISK_CONFIG',      0x0002);
/** Capability allows user to add scripted content - see {@link https://moodledev.io/docs/apis/subsystems/roles} */
define('RISK_XSS',         0x0004);
/** Capability allows access to personal user information - see {@link https://moodledev.io/docs/apis/subsystems/roles} */
define('RISK_PERSONAL',    0x0008);
/** Capability allows users to add content others may see - see {@link https://moodledev.io/docs/apis/subsystems/roles} */
define('RISK_SPAM',        0x0010);
/** capability allows mass delete of data belonging to other users - see {@link https://moodledev.io/docs/apis/subsystems/roles} */
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

/** Performance hint for assign_capability: the contextid is known to exist */
define('ACCESSLIB_HINT_CONTEXT_EXISTS', 'contextexists');
/** Performance hint for assign_capability: there is no existing entry in role_capabilities */
define('ACCESSLIB_HINT_NO_EXISTING', 'notexists');

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
$ACCESSLIB_PRIVATE->cacheroledefs    = array(); // Holds site-wide role definitions.
$ACCESSLIB_PRIVATE->dirtycontexts    = null;    // Dirty contexts cache, loaded from DB once per page
$ACCESSLIB_PRIVATE->dirtyusers       = null;    // Dirty users cache, loaded from DB once per $USER->id
$ACCESSLIB_PRIVATE->accessdatabyuser = array(); // Holds the cache of $accessdata structure for users (including $USER)

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
    accesslib_reset_role_cache();

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
    $ACCESSLIB_PRIVATE->dirtyusers       = null;
    $ACCESSLIB_PRIVATE->accessdatabyuser = array();

    if ($resetcontexts) {
        context_helper::reset_caches();
    }
}

/**
 * Full reset of accesslib's private role cache. ONLY TO BE USED FROM THIS LIBRARY FILE!
 *
 * This reset does not touch global $USER.
 *
 * Note: Only use this when the roles that need a refresh are unknown.
 *
 * @see accesslib_clear_role_cache()
 *
 * @access private
 * @return void
 */
function accesslib_reset_role_cache() {
    global $ACCESSLIB_PRIVATE;

    $ACCESSLIB_PRIVATE->cacheroledefs = array();
    $cache = cache::make('core', 'roledefs');
    $cache->purge();
}

/**
 * Clears accesslib's private cache of a specific role or roles. ONLY BE USED FROM THIS LIBRARY FILE!
 *
 * This reset does not touch global $USER.
 *
 * @access private
 * @param int|array $roles
 * @return void
 */
function accesslib_clear_role_cache($roles) {
    global $ACCESSLIB_PRIVATE;

    if (!is_array($roles)) {
        $roles = [$roles];
    }

    foreach ($roles as $role) {
        if (isset($ACCESSLIB_PRIVATE->cacheroledefs[$role])) {
            unset($ACCESSLIB_PRIVATE->cacheroledefs[$role]);
        }
    }

    $cache = cache::make('core', 'roledefs');
    $cache->delete_many($roles);
}

/**
 * Role is assigned at system context.
 *
 * @access private
 * @param int $roleid
 * @return array
 */
function get_role_access($roleid) {
    $accessdata = get_empty_accessdata();
    $accessdata['ra']['/'.SYSCONTEXTID] = array((int)$roleid => (int)$roleid);
    return $accessdata;
}

/**
 * Fetch raw "site wide" role definitions.
 * Even MUC static acceleration cache appears a bit slow for this.
 * Important as can be hit hundreds of times per page.
 *
 * @param array $roleids List of role ids to fetch definitions for.
 * @return array Complete definition for each requested role.
 */
function get_role_definitions(array $roleids) {
    global $ACCESSLIB_PRIVATE;

    if (empty($roleids)) {
        return array();
    }

    // Grab all keys we have not yet got in our static cache.
    if ($uncached = array_diff($roleids, array_keys($ACCESSLIB_PRIVATE->cacheroledefs))) {
        $cache = cache::make('core', 'roledefs');
        foreach ($cache->get_many($uncached) as $roleid => $cachedroledef) {
            if (is_array($cachedroledef)) {
                $ACCESSLIB_PRIVATE->cacheroledefs[$roleid] = $cachedroledef;
            }
        }

        // Check we have the remaining keys from the MUC.
        if ($uncached = array_diff($roleids, array_keys($ACCESSLIB_PRIVATE->cacheroledefs))) {
            $uncached = get_role_definitions_uncached($uncached);
            $ACCESSLIB_PRIVATE->cacheroledefs += $uncached;
            $cache->set_many($uncached);
        }
    }

    // Return just the roles we need.
    return array_intersect_key($ACCESSLIB_PRIVATE->cacheroledefs, array_flip($roleids));
}

/**
 * Query raw "site wide" role definitions.
 *
 * @param array $roleids List of role ids to fetch definitions for.
 * @return array Complete definition for each requested role.
 */
function get_role_definitions_uncached(array $roleids) {
    global $DB;

    if (empty($roleids)) {
        return array();
    }

    // Create a blank results array: even if a role has no capabilities,
    // we need to ensure it is included in the results to show we have
    // loaded all the capabilities that there are.
    $rdefs = array();
    foreach ($roleids as $roleid) {
        $rdefs[$roleid] = array();
    }

    // Load all the capabilities for these roles in all contexts.
    list($sql, $params) = $DB->get_in_or_equal($roleids);
    $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
              FROM {role_capabilities} rc
              JOIN {context} ctx ON rc.contextid = ctx.id
              JOIN {capabilities} cap ON rc.capability = cap.name
             WHERE rc.roleid $sql";
    $rs = $DB->get_recordset_sql($sql, $params);

    // Store the capabilities into the expected data structure.
    foreach ($rs as $rd) {
        if (!isset($rdefs[$rd->roleid][$rd->path])) {
            $rdefs[$rd->roleid][$rd->path] = array();
        }
        $rdefs[$rd->roleid][$rd->path][$rd->capability] = (int) $rd->permission;
    }

    $rs->close();

    // Sometimes (e.g. get_user_capability_course_helper::get_capability_info_at_each_context)
    // we process role definitinons in a way that requires we see parent contexts
    // before child contexts. This sort ensures that works (and is faster than
    // sorting in the SQL query).
    foreach ($rdefs as $roleid => $rdef) {
        ksort($rdefs[$roleid]);
    }

    return $rdefs;
}

/**
 * Get the default guest role, this is used for guest account,
 * search engine spiders, etc.
 *
 * @return stdClass|false role record
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
        if ($SCRIPT === "/$CFG->admin/index.php"
                or $SCRIPT === "/$CFG->admin/cli/install.php"
                or $SCRIPT === "/$CFG->admin/cli/install_database.php"
                or (defined('BEHAT_UTIL') and BEHAT_UTIL)
                or (defined('PHPUNIT_UTIL') and PHPUNIT_UTIL)) {
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
        debugging('Capability check being performed on a user with no ID.', DEBUG_DEVELOPER);
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

    // Check whether context locking is enabled.
    if (!empty($CFG->contextlocking)) {
        if ($capinfo->captype === 'write' && $context->locked) {
            // Context locking applies to any write capability in a locked context.
            // It does not apply to moodle/site:managecontextlocks - this is to allow context locking to be unlocked.
            if ($capinfo->name !== 'moodle/site:managecontextlocks') {
                // It applies to all users who are not site admins.
                // It also applies to site admins when contextlockappliestoadmin is set.
                if (!is_siteadmin($userid) || !empty($CFG->contextlockappliestoadmin)) {
                    return false;
                }
            }
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
        debugging('Context id '.$context->id.' does not have valid path, please use context_helper::build_all_paths()');
        if (is_siteadmin($userid)) {
            return true;
        } else {
            return false;
        }
    }

    if (!empty($USER->loginascontext)) {
        // The current user is logged in as another user and can assume their identity at or below the `loginascontext`
        // defined in the USER session.
        // The user may not assume their identity at any other location.
        if (!$USER->loginascontext->is_parent_of($context, true)) {
            // The context being checked is not the specified context, or one of its children.
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
 * Is course creator going to have capability in a new course?
 *
 * This is intended to be used in enrolment plugins before or during course creation,
 * do not use after the course is fully created.
 *
 * @category access
 *
 * @param string $capability the name of the capability to check.
 * @param context $context course or category context where is course going to be created
 * @param integer|stdClass $user A user id or object. By default (null) checks the permissions of the current user.
 * @return boolean true if the user will have this capability.
 *
 * @throws coding_exception if different type of context submitted
 */
function guess_if_creator_will_have_course_capability($capability, context $context, $user = null) {
    global $CFG;

    if ($context->contextlevel != CONTEXT_COURSE and $context->contextlevel != CONTEXT_COURSECAT) {
        throw new coding_exception('Only course or course category context expected');
    }

    if (has_capability($capability, $context, $user)) {
        // User already has the capability, it could be only removed if CAP_PROHIBIT
        // was involved here, but we ignore that.
        return true;
    }

    if (!has_capability('moodle/course:create', $context, $user)) {
        return false;
    }

    if (!enrol_is_enabled('manual')) {
        return false;
    }

    if (empty($CFG->creatornewroleid)) {
        return false;
    }

    if ($context->contextlevel == CONTEXT_COURSE) {
        if (is_viewing($context, $user, 'moodle/role:assign') or is_enrolled($context, $user, 'moodle/role:assign')) {
            return false;
        }
    } else {
        if (has_capability('moodle/course:view', $context, $user) and has_capability('moodle/role:assign', $context, $user)) {
            return false;
        }
    }

    // Most likely they will be enrolled after the course creation is finished,
    // does the new role have the required capability?
    list($neededroles, $forbiddenroles) = get_roles_with_cap_in_context($context, $capability);
    return isset($neededroles[$CFG->creatornewroleid]);
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
    while ($path = rtrim($path, '0123456789')) {
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
    $rdefs = get_role_definitions(array_keys($roles));
    $allowed = false;

    foreach ($roles as $roleid => $ignored) {
        foreach ($paths as $path) {
            if (isset($rdefs[$roleid][$path][$capability])) {
                $perm = (int)$rdefs[$roleid][$path][$capability];
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
 * @param context $context the context to check the capability in. You normally get this with context_xxxx::instance().
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
 * A convenience function that tests has_capability for a list of capabilities, and displays an error if
 * the user does not have that capability.
 *
 * This is just a utility method that calls has_capability in a loop. Try to put
 * the capabilities that fewest users are likely to have first in the list for best
 * performance.
 *
 * @category access
 * @see has_capability()
 *
 * @param array $capabilities an array of capability names.
 * @param context $context the context to check the capability in. You normally get this with context_xxxx::instance().
 * @param int $userid A user id. By default (null) checks the permissions of the current user.
 * @param bool $doanything If false, ignore effect of admin role assignment
 * @param string $errormessage The error string to to user. Defaults to 'nopermissions'.
 * @param string $stringfile The language file to load the error string from. Defaults to 'error'.
 * @return void terminates with an error if the user does not have the given capability.
 */
function require_all_capabilities(array $capabilities, context $context, $userid = null, $doanything = true,
                                  $errormessage = 'nopermissions', $stringfile = ''): void {
    foreach ($capabilities as $capability) {
        if (!has_capability($capability, $context, $userid, $doanything)) {
            throw new required_capability_exception($context, $capability, $errormessage, $stringfile);
        }
    }
}

/**
 * Return a nested array showing all role assignments for the user.
 * [ra] => [contextpath][roleid] = roleid
 *
 * @access private
 * @param int $userid - the id of the user
 * @return array access info array
 */
function get_user_roles_sitewide_accessdata($userid) {
    global $CFG, $DB;

    $accessdata = get_empty_accessdata();

    // start with the default role
    if (!empty($CFG->defaultuserroleid)) {
        $syscontext = context_system::instance();
        $accessdata['ra'][$syscontext->path][(int)$CFG->defaultuserroleid] = (int)$CFG->defaultuserroleid;
    }

    // load the "default frontpage role"
    if (!empty($CFG->defaultfrontpageroleid)) {
        $frontpagecontext = context_course::instance(get_site()->id);
        if ($frontpagecontext->path) {
            $accessdata['ra'][$frontpagecontext->path][(int)$CFG->defaultfrontpageroleid] = (int)$CFG->defaultfrontpageroleid;
        }
    }

    // Preload every assigned role.
    $sql = "SELECT ctx.path, ra.roleid, ra.contextid
              FROM {role_assignments} ra
              JOIN {context} ctx ON ctx.id = ra.contextid
             WHERE ra.userid = :userid";

    $rs = $DB->get_recordset_sql($sql, array('userid' => $userid));

    foreach ($rs as $ra) {
        // RAs leafs are arrays to support multi-role assignments...
        $accessdata['ra'][$ra->path][(int)$ra->roleid] = (int)$ra->roleid;
    }

    $rs->close();

    return $accessdata;
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
 * @return ?array accessdata
 */
function get_user_accessdata($userid, $preloadonly=false) {
    global $CFG, $ACCESSLIB_PRIVATE, $USER;

    if (isset($USER->access)) {
        $ACCESSLIB_PRIVATE->accessdatabyuser[$USER->id] = $USER->access;
    }

    // Unfortunately, we can't use the $ACCESSLIB_PRIVATE->dirtyusers array because it is not available in CLI.
    // So we need to check if the user has been marked as dirty or not in the cache directly.
    // This will add additional queries to the database, but it is the best we can do.
    if (CLI_SCRIPT && !empty($ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
        if (get_cache_flag('accesslib/dirtyusers', $userid, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid]['time'])) {
            unset($ACCESSLIB_PRIVATE->accessdatabyuser[$userid]);
        }
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
            // Includes default role and frontpage role.
            $accessdata = get_user_roles_sitewide_accessdata($userid);
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

    // Prevent dirty flags refetching on this page.
    $ACCESSLIB_PRIVATE->dirtycontexts = array();
    $ACCESSLIB_PRIVATE->dirtyusers    = array($USER->id => false);

    load_all_capabilities();

    foreach ($sw as $path => $roleid) {
        if ($record = $DB->get_record('context', array('path'=>$path))) {
            $context = context::instance_by_id($record->id);
            if (has_capability('moodle/role:switchroles', $context)) {
                role_switch($roleid, $context);
            }
        }
    }
}

/**
 * Adds a temp role to current USER->access array.
 *
 * Useful for the "temporary guest" access we grant to logged-in users.
 * This is useful for enrol plugins only.
 *
 * @since Moodle 2.2
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

    $USER->access['ra'][$coursecontext->path][(int)$roleid] = (int)$roleid;
}

/**
 * Removes any extra guest roles from current USER->access array.
 * This is useful for enrol plugins only.
 *
 * @since Moodle 2.2
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
    foreach ($ras as $r) {
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
            throw new \moodle_exception('invalidlegacy', '', '', $type);
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

    return $DB->get_record_sql("
        SELECT rc.*
          FROM {role_capabilities} rc
          JOIN {capability} cap ON rc.capability = cap.name
         WHERE rc.roleid = :roleid AND rc.capability = :capability AND rc.contextid = :contextid", [
            'roleid' => $roleid,
            'contextid' => $contextid,
            'capability' => $capability,

        ]);
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
    $role->id = $DB->insert_record('role', $role);
    $event = \core\event\role_created::create([
        'objectid' => $role->id,
        'context' => context_system::instance(),
        'other' => [
            'name' => $role->name,
            'shortname' => $role->shortname,
            'archetype' => $role->archetype,
        ]
    ]);

    $event->add_record_snapshot('role', $role);
    $event->trigger();

    return $role->id;
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

    // Get role record before it's deleted.
    $role = $DB->get_record('role', array('id'=>$roleid));

    // Finally delete the role itself.
    $DB->delete_records('role', array('id'=>$roleid));

    // Trigger event.
    $event = \core\event\role_deleted::create(
        array(
            'context' => context_system::instance(),
            'objectid' => $roleid,
            'other' =>
                array(
                    'shortname' => $role->shortname,
                    'description' => $role->description,
                    'archetype' => $role->archetype
                )
            )
        );
    $event->add_record_snapshot('role', $role);
    $event->trigger();

    // Reset any cache of this role, including MUC.
    accesslib_clear_role_cache($roleid);

    return true;
}

/**
 * Function to write context specific overrides, or default capabilities.
 *
 * The $performancehints array can currently contain two values intended to make this faster when
 * this function is being called in a loop, if you have already checked certain details:
 * 'contextexists' - if we already know the contextid exists in context table
 * ASSIGN_HINT_NO_EXISTING - if we already know there is no entry in role_capabilities matching
 *   contextid, roleid, and capability
 *
 * @param string $capability string name
 * @param int $permission CAP_ constants
 * @param int $roleid role id
 * @param int|context $contextid context id
 * @param bool $overwrite
 * @param string[] $performancehints Performance hints - leave blank unless needed
 * @return bool always true or exception
 */
function assign_capability($capability, $permission, $roleid, $contextid, $overwrite = false, array $performancehints = []) {
    global $USER, $DB;

    if ($contextid instanceof context) {
        $context = $contextid;
    } else {
        $context = context::instance_by_id($contextid);
    }

    // Capability must exist.
    if (!$capinfo = get_capability_info($capability)) {
        throw new coding_exception("Capability '{$capability}' was not found! This has to be fixed in code.");
    }

    if (empty($permission) || $permission == CAP_INHERIT) { // if permission is not set
        unassign_capability($capability, $roleid, $context->id);
        return true;
    }

    if (in_array(ACCESSLIB_HINT_NO_EXISTING, $performancehints)) {
        $existing = false;
    } else {
        $existing = $DB->get_record('role_capabilities',
                ['contextid' => $context->id, 'roleid' => $roleid, 'capability' => $capability]);
    }

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
        if (in_array(ACCESSLIB_HINT_CONTEXT_EXISTS, $performancehints) ||
                $DB->record_exists('context', ['id' => $context->id])) {
            $DB->insert_record('role_capabilities', $cap);
        }
    }

    // Trigger capability_assigned event.
    \core\event\capability_assigned::create([
        'userid' => $cap->modifierid,
        'context' => $context,
        'objectid' => $roleid,
        'other' => [
            'capability' => $capability,
            'oldpermission' => $existing->permission ?? CAP_INHERIT,
            'permission' => $permission
        ]
    ])->trigger();

    // Reset any cache of this role, including MUC.
    accesslib_clear_role_cache($roleid);

    return true;
}

/**
 * Unassign a capability from a role.
 *
 * @param string $capability the name of the capability
 * @param int $roleid the role id
 * @param int|context $contextid null means all contexts
 * @param bool $showdebug if true, will show debugging messages
 * @return boolean true or exception
 */
function unassign_capability($capability, $roleid, $contextid = null, bool $showdebug = true) {
    global $DB, $USER;

    // Capability must exist.
    if (!get_capability_info($capability, $showdebug)) {
        throw new coding_exception("Capability '{$capability}' was not found! This has to be fixed in code.");
    }

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

    // Trigger capability_assigned event.
    \core\event\capability_unassigned::create([
        'userid' => $USER->id,
        'context' => $context ?? context_system::instance(),
        'objectid' => $roleid,
        'other' => [
            'capability' => $capability,
        ]
    ])->trigger();

    // Reset any cache of this role, including MUC.
    accesslib_clear_role_cache($roleid);

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
 * @param context|null $context null means any
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
                              JOIN {capabilities} cap ON rc.capability = cap.name
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
    $ra->sortorder    = 0;

    $ra->id = $DB->insert_record('role_assignments', $ra);

    // Role assignments have changed, so mark user as dirty.
    mark_user_dirty($userid);

    core_course_category::role_assignment_changed($roleid, $context);

    $event = \core\event\role_assigned::create(array(
        'context' => $context,
        'objectid' => $ra->roleid,
        'relateduserid' => $ra->userid,
        'other' => array(
            'id' => $ra->id,
            'component' => $ra->component,
            'itemid' => $ra->itemid
        )
    ));
    $event->add_record_snapshot('role_assignments', $ra);
    $event->trigger();

    // Dispatch the hook for post role assignment actions.
    $hook = new \core\hook\access\after_role_assigned(
        context: $context,
        userid: $userid,
    );
    \core\di::get(\core\hook\manager::class)->dispatch($hook);

    return $ra->id;
}

/**
 * Removes one role assignment
 *
 * @param int $roleid
 * @param int  $userid
 * @param int  $contextid
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

    $ras = $DB->get_records('role_assignments', $params);
    foreach ($ras as $ra) {
        $DB->delete_records('role_assignments', array('id'=>$ra->id));
        if ($context = context::instance_by_id($ra->contextid, IGNORE_MISSING)) {
            // Role assignments have changed, so mark user as dirty.
            mark_user_dirty($ra->userid);

            $event = \core\event\role_unassigned::create(array(
                'context' => $context,
                'objectid' => $ra->roleid,
                'relateduserid' => $ra->userid,
                'other' => array(
                    'id' => $ra->id,
                    'component' => $ra->component,
                    'itemid' => $ra->itemid
                )
            ));
            $event->add_record_snapshot('role_assignments', $ra);
            $event->trigger();
            core_course_category::role_assignment_changed($ra->roleid, $context);

            // Dispatch the hook for post role assignment actions.
            $hook = new \core\hook\access\after_role_unassigned(
                context: $context,
                userid: $ra->userid,
            );
            \core\di::get(\core\hook\manager::class)->dispatch($hook);
        }
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
            foreach ($contexts as $context) {
                $mparams['contextid'] = $context->id;
                $ras = $DB->get_records('role_assignments', $mparams);
                foreach ($ras as $ra) {
                    $DB->delete_records('role_assignments', array('id'=>$ra->id));
                    // Role assignments have changed, so mark user as dirty.
                    mark_user_dirty($ra->userid);

                    $event = \core\event\role_unassigned::create(
                        array('context'=>$context, 'objectid'=>$ra->roleid, 'relateduserid'=>$ra->userid,
                            'other'=>array('id'=>$ra->id, 'component'=>$ra->component, 'itemid'=>$ra->itemid)));
                    $event->add_record_snapshot('role_assignments', $ra);
                    $event->trigger();
                    core_course_category::role_assignment_changed($ra->roleid, $context);
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
 * Mark a user as dirty (with timestamp) so as to force reloading of the user session.
 *
 * @param int $userid
 * @return void
 */
function mark_user_dirty($userid) {
    global $CFG, $ACCESSLIB_PRIVATE;

    if (during_initial_install()) {
        return;
    }

    // Throw exception if invalid userid is provided.
    if (empty($userid)) {
        throw new coding_exception('Invalid user parameter supplied for mark_user_dirty() function!');
    }

    // Set dirty flag in database, set dirty field locally, and clear local accessdata cache.
    set_cache_flag('accesslib/dirtyusers', $userid, 1, time() + $CFG->sessiontimeout);
    $ACCESSLIB_PRIVATE->dirtyusers[$userid] = 1;
    unset($ACCESSLIB_PRIVATE->accessdatabyuser[$userid]);
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
        debugging('Course access check being performed on a user with no ID.', DEBUG_DEVELOPER);
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

    if (!core_course_category::can_view_course_info($course)) {
        // No guest access if user does not have capability to browse courses.
        return false;
    }

    // if not enrolled try to gain temporary guest access
    $instances = $DB->get_records('enrol', array('courseid'=>$course->id, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder, id ASC');
    $enrols = enrol_get_plugins(true);
    foreach ($instances as $instance) {
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
    $defpath = core_component::get_component_directory($component).'/db/access.php';

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
    $caps = get_all_capabilities();
    $componentcaps = array();
    foreach ($caps as $cap) {
        if ($cap['component'] == $component) {
            $componentcaps[] = (object) $cap;
        }
    }
    return $componentcaps;
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
    $allcaps = get_all_capabilities();

    foreach ($allcaps as $cap) {
        if (!in_array($cap['component'], $components)) {
            $components[] = $cap['component'];
            $alldefs = array_merge($alldefs, load_capability_def($cap['component']));
        }
    }
    foreach ($alldefs as $name=>$def) {
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
 * Return default roles that can be assigned, overridden or switched
 * by give role archetype.
 *
 * @param string $type  assign|override|switch|view
 * @param string $archetype
 * @return array of role ids
 */
function get_default_role_archetype_allows($type, $archetype) {
    global $DB;

    if (empty($archetype)) {
        return array();
    }

    $roles = $DB->get_records('role');
    $archetypemap = array();
    foreach ($roles as $role) {
        if ($role->archetype) {
            $archetypemap[$role->archetype][$role->id] = $role->id;
        }
    }

    $defaults = array(
        'assign' => array(
            'manager'        => array('manager', 'coursecreator', 'editingteacher', 'teacher', 'student'),
            'coursecreator'  => array(),
            'editingteacher' => array('teacher', 'student'),
            'teacher'        => array(),
            'student'        => array(),
            'guest'          => array(),
            'user'           => array(),
            'frontpage'      => array(),
        ),
        'override' => array(
            'manager'        => array('manager', 'coursecreator', 'editingteacher', 'teacher', 'student', 'guest', 'user', 'frontpage'),
            'coursecreator'  => array(),
            'editingteacher' => array('teacher', 'student', 'guest'),
            'teacher'        => array(),
            'student'        => array(),
            'guest'          => array(),
            'user'           => array(),
            'frontpage'      => array(),
        ),
        'switch' => array(
            'manager'        => array('editingteacher', 'teacher', 'student', 'guest'),
            'coursecreator'  => array(),
            'editingteacher' => array('teacher', 'student', 'guest'),
            'teacher'        => array('student', 'guest'),
            'student'        => array(),
            'guest'          => array(),
            'user'           => array(),
            'frontpage'      => array(),
        ),
        'view' => array(
            'manager'        => array('manager', 'coursecreator', 'editingteacher', 'teacher', 'student', 'guest', 'user', 'frontpage'),
            'coursecreator'  => array('coursecreator', 'editingteacher', 'teacher', 'student'),
            'editingteacher' => array('coursecreator', 'editingteacher', 'teacher', 'student'),
            'teacher'        => array('coursecreator', 'editingteacher', 'teacher', 'student'),
            'student'        => array('coursecreator', 'editingteacher', 'teacher', 'student'),
            'guest'          => array(),
            'user'           => array(),
            'frontpage'      => array(),
        ),
    );

    if (!isset($defaults[$type][$archetype])) {
        debugging("Unknown type '$type'' or archetype '$archetype''");
        return array();
    }

    $return = array();
    foreach ($defaults[$type][$archetype] as $at) {
        if (isset($archetypemap[$at])) {
            foreach ($archetypemap[$at] as $roleid) {
                $return[$roleid] = $roleid;
            }
        }
    }

    return $return;
}

/**
 * Reset role capabilities to default according to selected role archetype.
 * If no archetype selected, removes all capabilities.
 *
 * This applies to capabilities that are assigned to the role (that you could
 * edit in the 'define roles' interface), and not to any capability overrides
 * in different locations.
 *
 * @param int $roleid ID of role to reset capabilities for
 */
function reset_role_capabilities($roleid) {
    global $DB;

    $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);
    $defaultcaps = get_default_capabilities($role->archetype);

    $systemcontext = context_system::instance();

    $DB->delete_records('role_capabilities',
            array('roleid' => $roleid, 'contextid' => $systemcontext->id));

    foreach ($defaultcaps as $cap=>$permission) {
        assign_capability($cap, $permission, $roleid, $systemcontext->id);
    }

    // Reset any cache of this role, including MUC.
    accesslib_clear_role_cache($roleid);
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
 * @param string $component examples: 'moodle', 'mod_forum', 'block_activity_results'
 * @return boolean true if success, exception in case of any problems
 */
function update_capabilities($component = 'moodle') {
    global $DB, $OUTPUT;

    // Allow temporary caches to be used during install, dramatically boosting performance.
    $token = new \core_cache\allow_temporary_caches();

    $storedcaps = array();

    $filecaps = load_capability_def($component);
    foreach ($filecaps as $capname=>$unused) {
        if (!preg_match('|^[a-z]+/[a-z_0-9]+:[a-z_0-9]+$|', $capname)) {
            debugging("Coding problem: Invalid capability name '$capname', use 'clonepermissionsfrom' field for migration.");
        }
    }

    // It is possible somebody directly modified the DB (according to accesslib_test anyway).
    // So ensure our updating is based on fresh data.
    cache::make('core', 'capabilities')->delete('core_capabilities');

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

    // Flush the cached again, as we have changed DB.
    cache::make('core', 'capabilities')->delete('core_capabilities');

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
    $capabilityobjects = [];
    foreach ($newcaps as $capname => $capdef) {
        $capability = new stdClass();
        $capability->name         = $capname;
        $capability->captype      = $capdef['captype'];
        $capability->contextlevel = $capdef['contextlevel'];
        $capability->component    = $component;
        $capability->riskbitmask  = $capdef['riskbitmask'];
        $capabilityobjects[] = $capability;
    }
    $DB->insert_records('capabilities', $capabilityobjects);

    // Flush the cache, as we have changed DB.
    cache::make('core', 'capabilities')->delete('core_capabilities');

    foreach ($newcaps as $capname => $capdef) {
        if (isset($capdef['clonepermissionsfrom']) && in_array($capdef['clonepermissionsfrom'], $existingcaps)){
            if ($rolecapabilities = $DB->get_records_sql('
                    SELECT rc.*,
                           CASE WHEN EXISTS(SELECT 1
                                    FROM {role_capabilities} rc2
                                   WHERE rc2.capability = ?
                                         AND rc2.contextid = rc.contextid
                                         AND rc2.roleid = rc.roleid) THEN 1 ELSE 0 END AS entryexists,
                            ' . context_helper::get_preload_record_columns_sql('x') .'
                      FROM {role_capabilities} rc
                      JOIN {context} x ON x.id = rc.contextid
                     WHERE rc.capability = ?',
                    [$capname, $capdef['clonepermissionsfrom']])) {
                foreach ($rolecapabilities as $rolecapability) {
                    // Preload the context and add performance hints based on the SQL query above.
                    context_helper::preload_from_record($rolecapability);
                    $performancehints = [ACCESSLIB_HINT_CONTEXT_EXISTS];
                    if (!$rolecapability->entryexists) {
                        $performancehints[] = ACCESSLIB_HINT_NO_EXISTING;
                    }
                    //assign_capability will update rather than insert if capability exists
                    if (!assign_capability($capname, $rolecapability->permission,
                            $rolecapability->roleid, $rolecapability->contextid, true, $performancehints)) {
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
    accesslib_reset_role_cache();

    // Flush the cached again, as we have changed DB.
    cache::make('core', 'capabilities')->delete('core_capabilities');

    return true;
}

/**
 * Deletes cached capabilities that are no longer needed by the component.
 * Also unassigns these capabilities from any roles that have them.
 * NOTE: this function is called from lib/db/upgrade.php
 *
 * @access private
 * @param string $component examples: 'moodle', 'mod_forum', 'block_activity_results'
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

                // Delete from roles.
                if ($roles = get_roles_with_capability($cachedcap->name)) {
                    foreach ($roles as $role) {
                        if (!unassign_capability(
                            capability: $cachedcap->name,
                            roleid: $role->id,
                            showdebug: false, // Suppress debugging messages in the get_capability_info().
                        )) {
                            throw new \moodle_exception('cannotunassigncap', 'error', '',
                                (object)array('cap' => $cachedcap->name, 'role' => $role->name));
                        }
                    }
                }

                // Remove from role_capabilities for any old ones.
                $DB->delete_records('role_capabilities', array('capability' => $cachedcap->name));

                // Remove from capabilities cache.
                $DB->delete_records('capabilities', array('name' => $cachedcap->name));
                $removedcount++;
            } // End if.
        }
    }
    if ($removedcount) {
        cache::make('core', 'capabilities')->delete('core_capabilities');
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
              FROM {role_capabilities} rc
              JOIN {context} c ON rc.contextid = c.id
              JOIN {capabilities} cap ON rc.capability = cap.name
             WHERE rc.contextid in $contexts
                   AND rc.roleid = ?
                   $search
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
 * @param string $capabilityname the capability name.
 * @param bool $showdebug if true, will show debugging messages.
 * @return ?stdClass object or null if capability not found
 */
function get_capability_info(string $capabilityname, bool $showdebug = true): ?stdClass {
    $caps = get_all_capabilities();

    // Check for deprecated capability.
    if ($deprecatedinfo = get_deprecated_capability_info($capabilityname)) {
        if (!empty($deprecatedinfo['replacement'])) {
            // Let's try again with this capability if it exists.
            if (isset($caps[$deprecatedinfo['replacement']])) {
                $capabilityname = $deprecatedinfo['replacement'];
            } else {
                if ($showdebug) {
                    debugging("Capability '{$capabilityname}' was supposed to be replaced with ".
                        "'{$deprecatedinfo['replacement']}', which does not exist !");
                }
            }
        }

        if ($showdebug) {
            $fullmessage = $deprecatedinfo['fullmessage'];
            debugging($fullmessage, DEBUG_DEVELOPER);
        }
    }
    if (!isset($caps[$capabilityname])) {
        return null;
    }

    return (object) $caps[$capabilityname];
}

/**
 * Returns deprecation info for this particular capabilty (cached)
 *
 * Do not use this function except in the get_capability_info
 *
 * @param string $capabilityname
 * @return array|null with deprecation message and potential replacement if not null
 */
function get_deprecated_capability_info($capabilityname) {
    $cache = cache::make('core', 'capabilities');
    $alldeprecatedcaps = $cache->get('deprecated_capabilities');
    if ($alldeprecatedcaps === false) {
        // Look for deprecated capabilities in each component.
        $allcaps = get_all_capabilities();
        $components = [];
        $alldeprecatedcaps = [];
        foreach ($allcaps as $cap) {
            if (!in_array($cap['component'], $components)) {
                $components[] = $cap['component'];

                $componentdir = core_component::get_component_directory($cap['component']);
                if ($componentdir === null) {
                    continue;
                }

                $defpath = "{$componentdir}/db/access.php";
                if (file_exists($defpath)) {
                    $deprecatedcapabilities = [];
                    require($defpath);
                    if (!empty($deprecatedcapabilities)) {
                        foreach ($deprecatedcapabilities as $cname => $cdef) {
                            $alldeprecatedcaps[$cname] = $cdef;
                        }
                    }
                }
            }
        }
        $cache->set('deprecated_capabilities', $alldeprecatedcaps);
    }

    if (!isset($alldeprecatedcaps[$capabilityname])) {
        return null;
    }
    $deprecatedinfo = $alldeprecatedcaps[$capabilityname];
    $deprecatedinfo['fullmessage'] = "The capability '{$capabilityname}' is deprecated.";
    if (!empty($deprecatedinfo['message'])) {
        $deprecatedinfo['fullmessage'] .= $deprecatedinfo['message'];
    }
    if (!empty($deprecatedinfo['replacement'])) {
        $deprecatedinfo['fullmessage'] .=
            "It will be replaced by '{$deprecatedinfo['replacement']}'.";
    }
    return $deprecatedinfo;
}

/**
 * Returns all capabilitiy records, preferably from MUC and not database.
 *
 * @return array All capability records indexed by capability name
 */
function get_all_capabilities() {
    global $DB;
    $cache = cache::make('core', 'capabilities');
    if (!$allcaps = $cache->get('core_capabilities')) {
        $rs = $DB->get_recordset('capabilities');
        $allcaps = array();
        foreach ($rs as $capability) {
            $capability->riskbitmask = (int) $capability->riskbitmask;
            $allcaps[$capability->name] = (array) $capability;
        }
        $rs->close();
        $cache->set('core_capabilities', $allcaps);
    }
    return $allcaps;
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

    $dir = core_component::get_component_directory($component);
    if (!isset($dir) || !file_exists($dir)) {
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

    if ($component === 'moodle' || $component === 'core') {
        return context_helper::get_level_name($contextlevel);
    }

    list($type, $name) = core_component::normalize_component($component);
    $dir = core_component::get_plugin_directory($type, $name);
    if (!isset($dir) || !file_exists($dir)) {
        // plugin not installed, bad luck, there is no way to find the name
        return $component . ' ???';
    }

    // Some plugin types need an extra prefix to make the name easy to understand.
    switch ($type) {
        case 'quiz':
            $prefix = get_string('quizreport', 'quiz') . ': ';
            break;
        case 'repository':
            $prefix = get_string('repository', 'repository') . ': ';
            break;
        case 'gradeimport':
            $prefix = get_string('gradeimport', 'grades') . ': ';
            break;
        case 'gradeexport':
            $prefix = get_string('gradeexport', 'grades') . ': ';
            break;
        case 'gradereport':
            $prefix = get_string('gradereport', 'grades') . ': ';
            break;
        case 'webservice':
            $prefix = get_string('webservice', 'webservice') . ': ';
            break;
        case 'block':
            $prefix = get_string('block') . ': ';
            break;
        case 'mod':
            $prefix = get_string('activity') . ': ';
            break;

        // Default case, just use the plugin name.
        default:
            $prefix = '';
    }
    return $prefix . get_string('pluginname', $component);
}

/**
 * Gets the list of roles assigned to this context and up (parents)
 * from the aggregation of:
 * a) the list of roles that are visible on user profile page and participants page (profileroles setting) and;
 * b) if applicable, those roles that are assigned in the context.
 *
 * @param context $context
 * @return array
 */
function get_profile_roles(context $context) {
    global $CFG, $DB;
    // If the current user can assign roles, then they can see all roles on the profile and participants page,
    // provided the roles are assigned to at least 1 user in the context. If not, only the policy-defined roles.
    if (has_capability('moodle/role:assign', $context)) {
        $rolesinscope = array_keys(get_all_roles($context));
    } else {
        $rolesinscope = empty($CFG->profileroles) ? [] : array_map('trim', explode(',', $CFG->profileroles));
    }

    if (empty($rolesinscope)) {
        return [];
    }

    list($rallowed, $params) = $DB->get_in_or_equal($rolesinscope, SQL_PARAMS_NAMED, 'a');
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
 * @param boolean $includeparents, false means without parents.
 * @return array
 */
function get_roles_used_in_context(context $context, $includeparents = true) {
    global $DB;

    if ($includeparents === true) {
        list($contextlist, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(true), SQL_PARAMS_NAMED, 'cl');
    } else {
        list($contextlist, $params) = $DB->get_in_or_equal($context->id, SQL_PARAMS_NAMED, 'cl');
    }

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
    if ($courseid == SITEID) {
        $context = context_system::instance();
    } else {
        $context = context_course::instance($courseid);
    }
    // If the current user can assign roles, then they can see all roles on the profile and participants page,
    // provided the roles are assigned to at least 1 user in the context. If not, only the policy-defined roles.
    if (has_capability('moodle/role:assign', $context)) {
        $rolesinscope = array_keys(get_all_roles($context));
    } else {
        $rolesinscope = empty($CFG->profileroles) ? [] : array_map('trim', explode(',', $CFG->profileroles));
    }
    if (empty($rolesinscope)) {
        return '';
    }

    list($rallowed, $params) = $DB->get_in_or_equal($rolesinscope, SQL_PARAMS_NAMED, 'a');
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
        $viewableroles = get_viewable_roles($context, $userid);

        $rolenames = array();
        foreach ($roles as $roleid => $unused) {
            if (isset($viewableroles[$roleid])) {
                $url = new moodle_url('/user/index.php', ['contextid' => $context->id, 'roleid' => $roleid]);
                $rolenames[] = '<a href="' . $url . '">' . $viewableroles[$roleid] . '</a>';
            }
        }
        $rolestring = implode(', ', $rolenames);
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
 * Note: this method does not localise role names or descriptions,
 *       use role_get_names() if you need role names.
 *
 * @param context $context optional context for course role name aliases
 * @return array of role records with optional coursealias property
 */
function get_all_roles(?context $context = null) {
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
 * Gets all the user roles assigned in this context, or higher contexts for a list of users.
 *
 * If you try using the combination $userids = [], $checkparentcontexts = true then this is likely
 * to cause an out-of-memory error on large Moodle sites, so this combination is deprecated and
 * outputs a warning, even though it is the default.
 *
 * @param context $context
 * @param array $userids. An empty list means fetch all role assignments for the context.
 * @param bool $checkparentcontexts defaults to true
 * @param string $order defaults to 'c.contextlevel DESC, r.sortorder ASC'
 * @return array
 */
function get_users_roles(context $context, $userids = [], $checkparentcontexts = true, $order = 'c.contextlevel DESC, r.sortorder ASC') {
    global $DB;

    if (!$userids && $checkparentcontexts) {
        debugging('Please do not call get_users_roles() with $checkparentcontexts = true ' .
                'and $userids array not set. This combination causes large Moodle sites ' .
                'with lots of site-wide role assignemnts to run out of memory.', DEBUG_DEVELOPER);
    }

    if ($checkparentcontexts) {
        $contextids = $context->get_parent_context_ids();
    } else {
        $contextids = array();
    }
    $contextids[] = $context->id;

    list($contextids, $params) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED, 'con');

    // If userids was passed as an empty array, we fetch all role assignments for the course.
    if (empty($userids)) {
        $useridlist = ' IS NOT NULL ';
        $uparams = [];
    } else {
        list($useridlist, $uparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uids');
    }

    $sql = "SELECT ra.*, r.name, r.shortname, ra.userid
              FROM {role_assignments} ra, {role} r, {context} c
             WHERE ra.userid $useridlist
                   AND ra.roleid = r.id
                   AND ra.contextid = c.id
                   AND ra.contextid $contextids
          ORDER BY $order";

    $all = $DB->get_records_sql($sql , array_merge($params, $uparams));

    // Return results grouped by userid.
    $result = [];
    foreach ($all as $id => $record) {
        if (!isset($result[$record->userid])) {
            $result[$record->userid] = [];
        }
        $result[$record->userid][$record->id] = $record;
    }

    // Make sure all requested users are included in the result, even if they had no role assignments.
    foreach ($userids as $id) {
        if (!isset($result[$id])) {
            $result[$id] = [];
        }
    }

    return $result;
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
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 */
function core_role_set_override_allowed($fromroleid, $targetroleid) {
    global $DB;

    $record = new stdClass();
    $record->roleid        = $fromroleid;
    $record->allowoverride = $targetroleid;
    $DB->insert_record('role_allow_override', $record);
}

/**
 * Creates a record in the role_allow_assign table
 *
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 */
function core_role_set_assign_allowed($fromroleid, $targetroleid) {
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
function core_role_set_switch_allowed($fromroleid, $targetroleid) {
    global $DB;

    $record = new stdClass();
    $record->roleid      = $fromroleid;
    $record->allowswitch = $targetroleid;
    $DB->insert_record('role_allow_switch', $record);
}

/**
 * Creates a record in the role_allow_view table
 *
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 */
function core_role_set_view_allowed($fromroleid, $targetroleid) {
    global $DB;

    $record = new stdClass();
    $record->roleid      = $fromroleid;
    $record->allowview = $targetroleid;
    $DB->insert_record('role_allow_view', $record);
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
        $extrafields = ', (SELECT COUNT(DISTINCT u.id)
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
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @return array an array $roleid => $rolename.
 */
function get_switchable_roles(context $context, $rolenamedisplay = ROLENAME_ALIAS) {
    global $USER, $DB;

    // You can't switch roles without this capability.
    if (!has_capability('moodle/role:switchroles', $context)) {
        return [];
    }

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

    return role_fix_names($roles, $context, $rolenamedisplay, true);
}

/**
 * Gets a list of roles that this user can view in a context
 *
 * @param context $context a context.
 * @param int $userid id of user.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @return array an array $roleid => $rolename.
 */
function get_viewable_roles(context $context, $userid = null, $rolenamedisplay = ROLENAME_ALIAS) {
    global $USER, $DB;

    if ($userid == null) {
        $userid = $USER->id;
    }

    $params = array();
    $extrajoins = '';
    $extrawhere = '';
    if (!is_siteadmin()) {
        // Admins are allowed to view any role.
        // Others are subject to the additional constraint that the view role must be allowed by
        // 'role_allow_view' for some role they have assigned in this context or any parent.
        $contexts = $context->get_parent_context_ids(true);
        list($insql, $inparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);

        $extrajoins = "JOIN {role_allow_view} ras ON ras.allowview = r.id
                       JOIN {role_assignments} ra ON ra.roleid = ras.roleid";
        $extrawhere = "WHERE ra.userid = :userid AND ra.contextid $insql";

        $params += $inparams;
        $params['userid'] = $userid;
    }

    if ($coursecontext = $context->get_course_context(false)) {
        $params['coursecontext'] = $coursecontext->id;
    } else {
        $params['coursecontext'] = 0; // No course aliases.
        $coursecontext = null;
    }

    $query = "
        SELECT r.id, r.name, r.shortname, rn.name AS coursealias, r.sortorder
          FROM {role} r
          $extrajoins
     LEFT JOIN {role_names} rn ON (rn.contextid = :coursecontext AND rn.roleid = r.id)
          $extrawhere
      GROUP BY r.id, r.name, r.shortname, rn.name, r.sortorder
      ORDER BY r.sortorder";
    $roles = $DB->get_records_sql($query, $params);

    return role_fix_names($roles, $context, $rolenamedisplay, true);
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
    return \context_helper::get_compatible_levels($rolearchetype);
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
 * Gets sql joins for finding users with capability in the given context.
 *
 * @param context $context Context for the join.
 * @param string|array $capability Capability name or array of names.
 *      If an array is provided then this is the equivalent of a logical 'OR',
 *      i.e. the user needs to have one of these capabilities.
 * @param string $useridcolumn e.g. 'u.id'.
 * @return \core\dml\sql_join Contains joins, wheres, params.
 *      This function will set ->cannotmatchanyrows if applicable.
 *      This may let you skip doing a DB query.
 */
function get_with_capability_join(context $context, $capability, $useridcolumn) {
    global $CFG, $DB;

    // Add a unique prefix to param names to ensure they are unique.
    static $i = 0;
    $i++;
    $paramprefix = 'eu' . $i . '_';

    $defaultuserroleid      = isset($CFG->defaultuserroleid) ? $CFG->defaultuserroleid : 0;
    $defaultfrontpageroleid = isset($CFG->defaultfrontpageroleid) ? $CFG->defaultfrontpageroleid : 0;

    $ctxids = trim($context->path, '/');
    $ctxids = str_replace('/', ',', $ctxids);

    // Context is the frontpage
    $isfrontpage = $context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID;
    $isfrontpage = $isfrontpage || is_inside_frontpage($context);

    $caps = (array) $capability;

    // Construct list of context paths bottom --> top.
    list($contextids, $paths) = get_context_info_list($context);

    // We need to find out all roles that have these capabilities either in definition or in overrides.
    $defs = [];
    list($incontexts, $params) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED, $paramprefix . 'con');
    list($incaps, $params2) = $DB->get_in_or_equal($caps, SQL_PARAMS_NAMED, $paramprefix . 'cap');

    // Check whether context locking is enabled.
    // Filter out any write capability if this is the case.
    $excludelockedcaps = '';
    $excludelockedcapsparams = [];
    if (!empty($CFG->contextlocking) && $context->locked) {
        $excludelockedcaps = 'AND (cap.captype = :capread OR cap.name = :managelockscap)';
        $excludelockedcapsparams['capread'] = 'read';
        $excludelockedcapsparams['managelockscap'] = 'moodle/site:managecontextlocks';
    }

    $params = array_merge($params, $params2, $excludelockedcapsparams);
    $sql = "SELECT rc.id, rc.roleid, rc.permission, rc.capability, ctx.path
              FROM {role_capabilities} rc
              JOIN {capabilities} cap ON rc.capability = cap.name
              JOIN {context} ctx on rc.contextid = ctx.id
             WHERE rc.contextid $incontexts AND rc.capability $incaps $excludelockedcaps";

    $rcs = $DB->get_records_sql($sql, $params);
    foreach ($rcs as $rc) {
        $defs[$rc->capability][$rc->path][$rc->roleid] = $rc->permission;
    }

    // Go through the permissions bottom-->top direction to evaluate the current permission,
    // first one wins (prohibit is an exception that always wins).
    $access = [];
    foreach ($caps as $cap) {
        foreach ($paths as $path) {
            if (empty($defs[$cap][$path])) {
                continue;
            }
            foreach ($defs[$cap][$path] as $roleid => $perm) {
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

    // Make lists of roles that are needed and prohibited in this context.
    $needed = []; // One of these is enough.
    $prohibited = []; // Must not have any of these.
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
            // Easy, nobody has the permission.
            unset($needed[$cap]);
            unset($prohibited[$cap]);
        } else if ($isfrontpage and !empty($prohibited[$cap][$defaultfrontpageroleid])) {
            // Everybody is disqualified on the frontpage.
            unset($needed[$cap]);
            unset($prohibited[$cap]);
        }
        if (empty($prohibited[$cap])) {
            unset($prohibited[$cap]);
        }
    }

    if (empty($needed)) {
        // There can not be anybody if no roles match this request.
        return new \core\dml\sql_join('', '1 = 2', [], true);
    }

    if (empty($prohibited)) {
        // We can compact the needed roles.
        $n = [];
        foreach ($needed as $cap) {
            foreach ($cap as $roleid => $unused) {
                $n[$roleid] = true;
            }
        }
        $needed = ['any' => $n];
        unset($n);
    }

    // Prepare query clauses.
    $wherecond = [];
    $params    = [];
    $joins     = [];
    $cannotmatchanyrows = false;

    // We never return deleted users or guest account.
    // Use a hack to get the deleted user column without an API change.
    $deletedusercolumn = substr($useridcolumn, 0, -2) . 'deleted';
    $wherecond[] = "$deletedusercolumn = 0 AND $useridcolumn <> :{$paramprefix}guestid";
    $params[$paramprefix . 'guestid'] = $CFG->siteguest;

    // Now add the needed and prohibited roles conditions as joins.
    if (!empty($needed['any'])) {
        // Simple case - there are no prohibits involved.
        if (!empty($needed['any'][$defaultuserroleid]) ||
                ($isfrontpage && !empty($needed['any'][$defaultfrontpageroleid]))) {
            // Everybody.
        } else {
            $joins[] = "JOIN (SELECT DISTINCT userid
                                FROM {role_assignments}
                               WHERE contextid IN ($ctxids)
                                     AND roleid IN (" . implode(',', array_keys($needed['any'])) . ")
                             ) ra ON ra.userid = $useridcolumn";
        }
    } else {
        $unions = [];
        $everybody = false;
        foreach ($needed as $cap => $unused) {
            if (empty($prohibited[$cap])) {
                if (!empty($needed[$cap][$defaultuserroleid]) ||
                        ($isfrontpage && !empty($needed[$cap][$defaultfrontpageroleid]))) {
                    $everybody = true;
                    break;
                } else {
                    $unions[] = "SELECT userid
                                   FROM {role_assignments}
                                  WHERE contextid IN ($ctxids)
                                        AND roleid IN (".implode(',', array_keys($needed[$cap])) .")";
                }
            } else {
                if (!empty($prohibited[$cap][$defaultuserroleid]) ||
                        ($isfrontpage && !empty($prohibited[$cap][$defaultfrontpageroleid]))) {
                    // Nobody can have this cap because it is prohibited in default roles.
                    continue;

                } else if (!empty($needed[$cap][$defaultuserroleid]) ||
                        ($isfrontpage && !empty($needed[$cap][$defaultfrontpageroleid]))) {
                    // Everybody except the prohibited - hiding does not matter.
                    $unions[] = "SELECT id AS userid
                                   FROM {user}
                                  WHERE id NOT IN (SELECT userid
                                                     FROM {role_assignments}
                                                    WHERE contextid IN ($ctxids)
                                                          AND roleid IN (" . implode(',', array_keys($prohibited[$cap])) . "))";

                } else {
                    $unions[] = "SELECT ra.userid
                                   FROM {role_assignments} ra
                              LEFT JOIN {role_assignments} rap ON (rap.userid = ra.userid
                                        AND rap.contextid IN ($ctxids)
                                        AND rap.roleid IN (" . implode(',', array_keys($prohibited[$cap])) . "))
                                  WHERE ra.contextid IN ($ctxids) AND ra.roleid IN (" . implode(',', array_keys($needed[$cap])) . ")
                                        AND rap.id IS NULL";
                }
            }
        }

        if (!$everybody) {
            if ($unions) {
                $joins[] = "JOIN (
                                  SELECT DISTINCT userid
                                    FROM (
                                            " . implode("\n UNION \n", $unions) . "
                                         ) us
                                 ) ra ON ra.userid = $useridcolumn";
            } else {
                // Only prohibits found - nobody can be matched.
                $wherecond[] = "1 = 2";
                $cannotmatchanyrows = true;
            }
        }
    }

    return new \core\dml\sql_join(implode("\n", $joins), implode(" AND ", $wherecond), $params, $cannotmatchanyrows);
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
 * @param bool $notuseddoanything not used any more, admin accounts are never returned
 * @param bool $notusedview - use get_enrolled_sql() instead
 * @param bool $useviewallgroups if $groups is set the return users who
 *               have capability both $capability and moodle/site:accessallgroups
 *               in this context, as well as users who have $capability and who are
 *               in $groups.
 * @return array of user records
 */
function get_users_by_capability(context $context, $capability, $fields = '', $sort = '', $limitfrom = '', $limitnum = '',
        $groups = '', $exceptions = '', $notuseddoanything = null, $notusedview = null, $useviewallgroups = false) {
    global $CFG, $DB;

    // Context is a course page other than the frontpage.
    $iscoursepage = $context->contextlevel == CONTEXT_COURSE && $context->instanceid != SITEID;

    // Set up default fields list if necessary.
    if (empty($fields)) {
        if ($iscoursepage) {
            $fields = 'u.*, ul.timeaccess AS lastaccess';
        } else {
            $fields = 'u.*';
        }
    } else {
        if ($CFG->debugdeveloper && strpos($fields, 'u.*') === false && strpos($fields, 'u.id') === false) {
            debugging('u.id must be included in the list of fields passed to get_users_by_capability().', DEBUG_DEVELOPER);
        }
    }

    // Set up default sort if necessary.
    if (empty($sort)) { // default to course lastaccess or just lastaccess
        if ($iscoursepage) {
            $sort = 'ul.timeaccess';
        } else {
            $sort = 'u.lastaccess';
        }
    }

    // Get the bits of SQL relating to capabilities.
    $sqljoin = get_with_capability_join($context, $capability, 'u.id');
    if ($sqljoin->cannotmatchanyrows) {
        return [];
    }

    // Prepare query clauses.
    $wherecond = [$sqljoin->wheres];
    $params    = $sqljoin->params;
    $joins     = [$sqljoin->joins];

    // Add user lastaccess JOIN, if required.
    if ((strpos($sort, 'ul.timeaccess') === false) and (strpos($fields, 'ul.timeaccess') === false)) {
         // Here user_lastaccess is not required MDL-13810.
    } else {
        if ($iscoursepage) {
            $joins[] = "LEFT OUTER JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = {$context->instanceid})";
        } else {
            throw new coding_exception('Invalid sort in get_users_by_capability(), ul.timeaccess allowed only for course contexts.');
        }
    }

    // Groups.
    if ($groups) {
        $groups = (array)$groups;
        list($grouptest, $grpparams) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED, 'grp');
        $joins[] = "LEFT OUTER JOIN (SELECT DISTINCT userid
                                       FROM {groups_members}
                                      WHERE groupid $grouptest
                                    ) gm ON gm.userid = u.id";

        $params = array_merge($params, $grpparams);

        $grouptest = 'gm.userid IS NOT NULL';
        if ($useviewallgroups) {
            $viewallgroupsusers = get_users_by_capability($context, 'moodle/site:accessallgroups', 'u.id, u.id', '', '', '', '', $exceptions);
            if (!empty($viewallgroupsusers)) {
                $grouptest .= ' OR u.id IN (' . implode(',', array_keys($viewallgroupsusers)) . ')';
            }
        }
        $wherecond[] = "($grouptest)";
    }

    // User exceptions.
    if (!empty($exceptions)) {
        $exceptions = (array)$exceptions;
        list($exsql, $exparams) = $DB->get_in_or_equal($exceptions, SQL_PARAMS_NAMED, 'exc', false);
        $params = array_merge($params, $exparams);
        $wherecond[] = "u.id $exsql";
    }

    // Collect WHERE conditions and needed joins.
    $where = implode(' AND ', $wherecond);
    if ($where !== '') {
        $where = 'WHERE ' . $where;
    }
    $joins = implode("\n", $joins);

    // Finally! we have all the bits, run the query.
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
 * Note that moodle is based on capabilities and it is usually better
 * to check permissions than to check role ids as the capabilities
 * system is more flexible. If you really need, you can to use this
 * function but consider has_capability() as a possible substitute.
 *
 * All $sort fields are added into $fields if not present there yet.
 *
 * If $roleid is an array or is empty (all roles) you need to set $fields
 * (and $sort by extension) params according to it, as the first field
 * returned by the database should be unique (ra.id is the best candidate).
 *
 * @param int|array $roleid (can also be an array of ints!)
 * @param context $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @param string $fields fields from user (u.) , role assignment (ra) or role (r.)
 * @param string $sort sort from user (u.) , role assignment (ra.) or role (r.).
 *      null => use default sort from users_order_by_sql.
 * @param bool $all true means all, false means limit to enrolled users
 * @param string $group defaults to ''
 * @param mixed $limitfrom defaults to ''
 * @param mixed $limitnum defaults to ''
 * @param string $extrawheretest defaults to ''
 * @param array $whereorsortparams any paramter values used by $sort or $extrawheretest.
 * @return array
 */
function get_role_users($roleid, context $context, $parent = false, $fields = '',
        $sort = null, $all = true, $group = '',
        $limitfrom = '', $limitnum = '', $extrawheretest = '', $whereorsortparams = array()) {
    global $DB;

    if (empty($fields)) {
        $userfieldsapi = \core_user\fields::for_name();
        $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $fields = 'u.id, u.confirmed, u.username, '. $allnames . ', ' .
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.emailstop, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.lang, u.timezone, u.lastaccess, u.mnethostid, r.name AS rolename, r.sortorder, '.
                  'r.shortname AS roleshortname, rn.name AS rolecoursealias';
    }

    // Prevent wrong function uses.
    if ((empty($roleid) || is_array($roleid)) && strpos($fields, 'ra.id') !== 0) {
        debugging('get_role_users() without specifying one single roleid needs to be called prefixing ' .
            'role assignments id (ra.id) as unique field, you can use $fields param for it.');

        if (!empty($roleid)) {
            // Solving partially the issue when specifying multiple roles.
            $users = array();
            foreach ($roleid as $id) {
                // Ignoring duplicated keys keeping the first user appearance.
                $users = $users + get_role_users($id, $context, $parent, $fields, $sort, $all, $group,
                    $limitfrom, $limitnum, $extrawheretest, $whereorsortparams);
            }
            return $users;
        }
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

    // Adding the fields from $sort that are not present in $fields.
    $sortarray = preg_split('/,\s*/', $sort);
    $fieldsarray = preg_split('/,\s*/', $fields);

    // Discarding aliases from the fields.
    $fieldnames = array();
    foreach ($fieldsarray as $key => $field) {
        list($fieldnames[$key]) = explode(' ', $field);
    }

    $addedfields = array();
    foreach ($sortarray as $sortfield) {
        // Throw away any additional arguments to the sort (e.g. ASC/DESC).
        list($sortfield) = explode(' ', $sortfield);
        list($tableprefix) = explode('.', $sortfield);
        $fieldpresent = false;
        foreach ($fieldnames as $fieldname) {
            if ($fieldname === $sortfield || $fieldname === $tableprefix.'.*') {
                $fieldpresent = true;
                break;
            }
        }

        if (!$fieldpresent) {
            $fieldsarray[] = $sortfield;
            $addedfields[] = $sortfield;
        }
    }

    $fields = implode(', ', $fieldsarray);
    if (!empty($addedfields)) {
        $addedfields = implode(', ', $addedfields);
        debugging('get_role_users() adding '.$addedfields.' to the query result because they were required by $sort but missing in $fields');
    }

    if ($all === null) {
        // Previously null was used to indicate that parameter was not used.
        $all = true;
    }
    if (!$all and $coursecontext) {
        // Do not use get_enrolled_sql() here for performance reasons.
        $ejoin = "JOIN {user_enrolments} ue ON ue.userid = u.id
                  JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :ecourseid)";
        $params['ecourseid'] = $coursecontext->instanceid;
    } else {
        $ejoin = "";
    }

    $sql = "SELECT DISTINCT $fields, ra.roleid
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
              JOIN {role} r ON ra.roleid = r.id
            $ejoin
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

    $sql = "SELECT COUNT(DISTINCT u.id)
              FROM {role_assignments} r
              JOIN {user} u ON u.id = r.userid
             WHERE (r.contextid = ? $parentcontexts)
                   $roleselect
                   AND u.deleted = 0";

    return $DB->count_records_sql($sql, $params);
}

/**
 * This function gets the list of course and course category contexts that this user has a particular capability in.
 *
 * It is now reasonably efficient, but bear in mind that if there are users who have the capability
 * everywhere, it may return an array of all contexts.
 *
 * @param string $capability Capability in question
 * @param int $userid User ID or null for current user
 * @param bool $getcategories Wether to return also course_categories
 * @param bool $doanything True if 'doanything' is permitted (default)
 * @param string $coursefieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id.
 *   Add ctxid, ctxpath, ctxdepth etc to return course context information for preloading.
 * @param string $categoryfieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id.
 *   Add ctxid, ctxpath, ctxdepth etc to return course context information for preloading.
 * @param string $courseorderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed
 * @param string $categoryorderby If set, use a comma-separated list of fields from course_category
 *   table with sql modifiers (DESC) if needed
 * @param int $limit Limit the number of courses to return on success. Zero equals all entries.
 * @return array Array of categories and courses.
 */
function get_user_capability_contexts(string $capability, bool $getcategories, $userid = null, $doanything = true,
                                      $coursefieldsexceptid = '', $categoryfieldsexceptid = '', $courseorderby = '',
                                      $categoryorderby = '', $limit = 0): array {
    global $DB, $USER;

    // Default to current user.
    if (!$userid) {
        $userid = $USER->id;
    }

    if (!$capinfo = get_capability_info($capability)) {
        debugging('Capability "'.$capability.'" was not found! This has to be fixed in code.');
        return [false, false];
    }

    if ($doanything && is_siteadmin($userid)) {
        // If the user is a site admin and $doanything is enabled then there is no need to restrict
        // the list of courses.
        $contextlimitsql = '';
        $contextlimitparams = [];
    } else {
        // Gets SQL to limit contexts ('x' table) to those where the user has this capability.
        list ($contextlimitsql, $contextlimitparams) = \core\access\get_user_capability_course_helper::get_sql(
            $userid, $capinfo->name);
        if (!$contextlimitsql) {
            // If the does not have this capability in any context, return false without querying.
            return [false, false];
        }

        $contextlimitsql = 'WHERE' . $contextlimitsql;
    }

    $categories = [];
    if ($getcategories) {
        $fieldlist = \core\access\get_user_capability_course_helper::map_fieldnames($categoryfieldsexceptid);
        if ($categoryorderby) {
            $fields = explode(',', $categoryorderby);
            $categoryorderby = '';
            foreach ($fields as $field) {
                if ($categoryorderby) {
                    $categoryorderby .= ',';
                }
                $categoryorderby .= 'c.'.$field;
            }
            $categoryorderby = 'ORDER BY '.$categoryorderby;
        }
        $rs = $DB->get_recordset_sql("
            SELECT c.id $fieldlist
              FROM {course_categories} c
               JOIN {context} x ON c.id = x.instanceid AND x.contextlevel = ?
            $contextlimitsql
            $categoryorderby", array_merge([CONTEXT_COURSECAT], $contextlimitparams));
        $basedlimit = $limit;
        foreach ($rs as $category) {
            $categories[] = $category;
            $basedlimit--;
            if ($basedlimit == 0) {
                break;
            }
        }
        $rs->close();
    }

    $courses = [];
    $fieldlist = \core\access\get_user_capability_course_helper::map_fieldnames($coursefieldsexceptid);
    if ($courseorderby) {
        $fields = explode(',', $courseorderby);
        $courseorderby = '';
        foreach ($fields as $field) {
            if ($courseorderby) {
                $courseorderby .= ',';
            }
            $courseorderby .= 'c.'.$field;
        }
        $courseorderby = 'ORDER BY '.$courseorderby;
    }
    $rs = $DB->get_recordset_sql("
            SELECT c.id $fieldlist
              FROM {course} c
               JOIN {context} x ON c.id = x.instanceid AND x.contextlevel = ?
            $contextlimitsql
            $courseorderby", array_merge([CONTEXT_COURSE], $contextlimitparams));
    foreach ($rs as $course) {
        $courses[] = $course;
        $limit--;
        if ($limit == 0) {
            break;
        }
    }
    $rs->close();
    return [$categories, $courses];
}

/**
 * This function gets the list of courses that this user has a particular capability in.
 *
 * It is now reasonably efficient, but bear in mind that if there are users who have the capability
 * everywhere, it may return an array of all courses.
 *
 * @param string $capability Capability in question
 * @param int $userid User ID or null for current user
 * @param bool $doanything True if 'doanything' is permitted (default)
 * @param string $fieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id.
 *   Add ctxid, ctxpath, ctxdepth etc to return course context information for preloading.
 * @param string $orderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed
 * @param int $limit Limit the number of courses to return on success. Zero equals all entries.
 * @return array|bool Array of courses, if none found false is returned.
 */
function get_user_capability_course($capability, $userid = null, $doanything = true, $fieldsexceptid = '',
                                    $orderby = '', $limit = 0) {
    list($categories, $courses) = get_user_capability_contexts(
        $capability,
        false,
        $userid,
        $doanything,
        $fieldsexceptid,
        '',
        $orderby,
        '',
        $limit
    );
    return $courses;
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

    // Add the ghost RA to $USER->access as $USER->access['rsw'][$path] = $roleid.
    // To un-switch just unset($USER->access['rsw'][$path]).
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

    // Dispatch the hook for post user switch.
    $hook = new \core\hook\access\after_role_switched(
            context: $context,
            roleid: $roleid
        );
    \core\di::get(\core\hook\manager::class)->dispatch($hook);
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
 * Get localised role name or alias if exists and format the text.
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
        if ($coursecontext && $role->coursealias && trim($role->coursealias) !== '') {
            return format_string($role->coursealias, true, array('context'=>$coursecontext));
        } else {
            return $original;
        }
    }

    if ($rolenamedisplay == ROLENAME_BOTH) {
        if ($coursecontext && $role->coursealias && trim($role->coursealias) !== '') {
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
 *
 * In new installs default roles have empty names, this function
 * add localised role names using current language pack.
 *
 * @param context $context the context, null means system context
 * @param array of role objects with a ->localname field containing the context-specific role name.
 * @param int $rolenamedisplay
 * @param bool $returnmenu true means id=>localname, false means id=>rolerecord
 * @return array Array of context-specific role names, or role objects with a ->localname field added.
 */
function role_get_names(?context $context = null, $rolenamedisplay = ROLENAME_ALIAS, $returnmenu = null) {
    return role_fix_names(get_all_roles($context), $context, $rolenamedisplay, $returnmenu);
}

/**
 * Prepare list of roles for display, apply aliases and localise default role names.
 *
 * @param array $roleoptions array roleid => roleobject (with optional coursealias), strings are accepted for backwards compatibility only
 * @param context $context the context, null means system context
 * @param int $rolenamedisplay
 * @param bool $returnmenu null means keep the same format as $roleoptions, true means id=>localname, false means id=>rolerecord
 * @return array Array of context-specific role names, or role objects with a ->localname field added.
 */
function role_fix_names($roleoptions, ?context $context = null, $rolenamedisplay = ROLENAME_ALIAS, $returnmenu = null) {
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
 * @param stdClass $cap component string a
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

    // Reset any cache of this role, including MUC.
    accesslib_clear_role_cache($targetrole);
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
              JOIN {capabilities} cap ON rc.capability = cap.name
             WHERE rc.capability = :cap AND ctx.id IN ($ctxids)
          ORDER BY rc.roleid ASC, ctx.depth DESC";
    $params = array('cap'=>$capability);

    if (!$capdefs = $DB->get_records_sql($sql, $params)) {
        // no cap definitions --> no capability
        return array(array(), array());
    }

    $forbidden = array();
    $needed    = array();
    foreach ($capdefs as $def) {
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
    foreach ($needed as $key=>$value) {
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
    foreach ($capabilities as $caprequired) {
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
              JOIN {capabilities} cap ON rc.capability = cap.name
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
 * @param stdClass|context $context
 * @param string $capname capability name
 * @param int $permission
 * @return void
 */
function role_change_permission($roleid, $context, $capname, $permission) {
    global $DB;

    if ($permission == CAP_INHERIT) {
        unassign_capability($capname, $roleid, $context->id);
        return;
    }

    $ctxids = trim($context->path, '/'); // kill leading slash
    $ctxids = str_replace('/', ',', $ctxids);

    $params = array('roleid'=>$roleid, 'cap'=>$capname);

    $sql = "SELECT ctx.id, rc.permission, ctx.depth
              FROM {role_capabilities} rc
              JOIN {context} ctx ON ctx.id = rc.contextid
              JOIN {capabilities} cap ON rc.capability = cap.name
             WHERE rc.roleid = :roleid AND rc.capability = :cap AND ctx.id IN ($ctxids)
          ORDER BY ctx.depth DESC";

    if ($existing = $DB->get_records_sql($sql, $params)) {
        foreach ($existing as $e) {
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
}

/* ============== DEPRECATED FUNCTIONS ========================================== */
// Old context related functions were deprecated in 2.0, it is recommended
// to use context classes in new code. Old function can be used when
// creating patches that are supposed to be backported to older stable branches.
// These deprecated functions will not be removed in near future,
// before removing devs will be warned with a debugging message first,
// then we will add error message and only after that we can remove the functions
// completely.

// Renamed context class do not use lib/db/renamedclasses.php because we cannot
// ask everybody to update all code, so let's keep this here for the next few decades.
// Another benefit is that PHPStorm understands this and stops complaining.
class_alias(core\context_helper::class, 'context_helper', true);
class_alias(core\context::class, 'context', true);
class_alias(core\context\block::class, 'context_block');
class_alias(core\context\course::class, 'context_course', true);
class_alias(core\context\coursecat::class, 'context_coursecat');
class_alias(core\context\module::class, 'context_module', true);
class_alias(core\context\system::class, 'context_system', true);
class_alias(core\context\user::class, 'context_user', true);

/**
 * Runs get_records select on context table and returns the result
 * Does get_records_select on the context table, and returns the results ordered
 * by contextlevel, and then the natural sort order within each level.
 * for the purpose of $select, you need to know that the context table has been
 * aliased to ctx, so for example, you can call get_sorted_contexts('ctx.depth = 3');
 *
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
 * Given context and array of users, returns array of users whose enrolment status is suspended,
 * or enrolment has expired or has not started. Also removes those users from the given array
 *
 * @param context $context context in which suspended users should be extracted.
 * @param array $users list of users.
 * @param array $ignoreusers array of user ids to ignore, e.g. guest
 * @return array list of suspended users.
 */
function extract_suspended_users($context, &$users, $ignoreusers=array()) {
    global $DB;

    // Get active enrolled users.
    list($sql, $params) = get_enrolled_sql($context, null, null, true);
    $activeusers = $DB->get_records_sql($sql, $params);

    // Move suspended users to a separate array & remove from the initial one.
    $susers = array();
    if (sizeof($activeusers)) {
        foreach ($users as $userid => $user) {
            if (!array_key_exists($userid, $activeusers) && !in_array($userid, $ignoreusers)) {
                $susers[$userid] = $user;
                unset($users[$userid]);
            }
        }
    }
    return $susers;
}

/**
 * Given context and array of users, returns array of user ids whose enrolment status is suspended,
 * or enrolment has expired or not started.
 *
 * @param context $context context in which user enrolment is checked.
 * @param bool $usecache Enable or disable (default) the request cache
 * @return array list of suspended user id's.
 */
function get_suspended_userids(context $context, $usecache = false) {
    global $DB;

    if ($usecache) {
        $cache = cache::make('core', 'suspended_userids');
        $susers = $cache->get($context->id);
        if ($susers !== false) {
            return $susers;
        }
    }

    $coursecontext = $context->get_course_context();
    $susers = array();

    // Front page users are always enrolled, so suspended list is empty.
    if ($coursecontext->instanceid != SITEID) {
        list($sql, $params) = get_enrolled_sql($context, null, null, false, true);
        $susers = $DB->get_fieldset_sql($sql, $params);
        $susers = array_combine($susers, $susers);
    }

    // Cache results for the remainder of this request.
    if ($usecache) {
        $cache->set($context->id, $susers);
    }

    return $susers;
}

/**
 * Gets sql for finding users with capability in the given context
 *
 * @param context $context
 * @param string|array $capability Capability name or array of names.
 *      If an array is provided then this is the equivalent of a logical 'OR',
 *      i.e. the user needs to have one of these capabilities.
 * @return array($sql, $params)
 */
function get_with_capability_sql(context $context, $capability) {
    static $i = 0;
    $i++;
    $prefix = 'cu' . $i . '_';

    $capjoin = get_with_capability_join($context, $capability, $prefix . 'u.id');

    $sql = "SELECT DISTINCT {$prefix}u.id
              FROM {user} {$prefix}u
            $capjoin->joins
             WHERE {$prefix}u.deleted = 0 AND $capjoin->wheres";

    return array($sql, $capjoin->params);
}

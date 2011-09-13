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
 * @package    core
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

/** capability risks - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_MANAGETRUST', 0x0001);
/** capability risks - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_CONFIG',      0x0002);
/** capability risks - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_XSS',         0x0004);
/** capability risks - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_PERSONAL',    0x0008);
/** capability risks - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
define('RISK_SPAM',        0x0010);
/** capability risks - see {@link http://docs.moodle.org/dev/Hardening_new_Roles_system} */
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
/** rolename displays - the name is simply short role name*/
define('ROLENAME_SHORT', 5);

/**
 * Internal class provides a cache of context information. The cache is
 * restricted in size.
 *
 * This cache should NOT be used outside accesslib.php!
 *
 * @private
 * @author Sam Marshall
 */
class context_cache {
    private $contextsbyid;
    private $contexts;
    private $count;

    /**
     * @var int Maximum number of contexts that will be cached.
     */
    const MAX_SIZE = 2500;
    /**
     * @var int Once contexts reach maximum number, this many will be removed from cache.
     */
    const REDUCE_SIZE = 1000;

    /**
     * Initialises (empty)
     */
    public function __construct() {
        $this->reset();
    }

    /**
     * Resets the cache to remove all data.
     */
    public function reset() {
        $this->contexts     = array();
        $this->contextsbyid = array();
        $this->count        = 0;
    }

    /**
     * Adds a context to the cache. If the cache is full, discards a batch of
     * older entries.
     * @param stdClass $context New context to add
     */
    public function add(stdClass $context) {
        if ($this->count >= self::MAX_SIZE) {
            for ($i=0; $i<self::REDUCE_SIZE; $i++) {
                if ($first = reset($this->contextsbyid)) {
                    unset($this->contextsbyid[$first->id]);
                    unset($this->contexts[$first->contextlevel][$first->instanceid]);
                }
            }
            $this->count -= self::REDUCE_SIZE;
            if ($this->count < 0) {
                // most probably caused by the drift, the reset() above
                // might have returned false because there might not be any more elements
                $this->count = 0;
            }
        }

        $this->contexts[$context->contextlevel][$context->instanceid] = $context;
        $this->contextsbyid[$context->id] = $context;

        // Note the count may get out of synch slightly if you cache a context
        // that is already cached, but it doesn't really matter much and I
        // didn't think it was worth the performance hit.
        $this->count++;
    }

    /**
     * Removes a context from the cache.
     * @param stdClass $context Context object to remove (must include fields
     *   ->id, ->contextlevel, ->instanceid at least)
     */
    public function remove(stdClass $context) {
        unset($this->contexts[$context->contextlevel][$context->instanceid]);
        unset($this->contextsbyid[$context->id]);

        // Again the count may get a bit out of synch if you remove things
        // that don't exist
        $this->count--;

        if ($this->count < 0) {
            $this->count = 0;
        }
    }

    /**
     * Gets a context from the cache.
     * @param int $contextlevel Context level
     * @param int $instance Instance ID
     * @return stdClass|bool Context or false if not in cache
     */
    public function get($contextlevel, $instance) {
        if (isset($this->contexts[$contextlevel][$instance])) {
            return $this->contexts[$contextlevel][$instance];
        }
        return false;
    }

    /**
     * Gets a context from the cache based on its id.
     * @param int $id Context ID
     * @return stdClass|bool Context or false if not in cache
     */
    public function get_by_id($id) {
        if (isset($this->contextsbyid[$id])) {
            return $this->contextsbyid[$id];
        }
        return false;
    }

    /**
     * @return int Count of contexts in cache (approximately)
     */
    public function get_approx_count() {
        return $this->count;
    }
}

/**
 * Although this looks like a global variable, it isn't really.
 *
 * It is just a private implementation detail to accesslib that MUST NOT be used elsewhere.
 * It is used to cache various bits of data between function calls for performance reasons.
 * Sadly, a PHP global variable is the only way to implement this, without rewriting everything
 * as methods of a class, instead of functions.
 *
 * @global stdClass $ACCESSLIB_PRIVATE
 * @name $ACCESSLIB_PRIVATE
 */
global $ACCESSLIB_PRIVATE;
$ACCESSLIB_PRIVATE = new stdClass();
$ACCESSLIB_PRIVATE->contexcache      = new context_cache();
$ACCESSLIB_PRIVATE->systemcontext    = null; // Used in get_system_context
$ACCESSLIB_PRIVATE->dirtycontexts    = null; // Dirty contexts cache
$ACCESSLIB_PRIVATE->accessdatabyuser = array(); // Holds the $accessdata structure for users other than $USER
$ACCESSLIB_PRIVATE->roledefinitions  = array(); // role definitions cache - helps a lot with mem usage in cron
$ACCESSLIB_PRIVATE->croncache        = array(); // Used in get_role_access
$ACCESSLIB_PRIVATE->preloadedcourses = array(); // Used in preload_course_contexts.
$ACCESSLIB_PRIVATE->capabilities     = null; // detailed information about the capabilities

/**
 * Clears accesslib's private caches. ONLY BE USED BY UNIT TESTS
 *
 * This method should ONLY BE USED BY UNIT TESTS. It clears all of
 * accesslib's private caches. You need to do this before setting up test data,
 * and also at the end of the tests.
 */
function accesslib_clear_all_caches_for_unit_testing() {
    global $UNITTEST, $USER, $ACCESSLIB_PRIVATE;
    if (empty($UNITTEST->running)) {
        throw new coding_exception('You must not call clear_all_caches outside of unit tests.');
    }
    $ACCESSLIB_PRIVATE->contexcache      = new context_cache();
    $ACCESSLIB_PRIVATE->systemcontext    = null;
    $ACCESSLIB_PRIVATE->dirtycontexts    = null;
    $ACCESSLIB_PRIVATE->accessdatabyuser = array();
    $ACCESSLIB_PRIVATE->roledefinitions  = array();
    $ACCESSLIB_PRIVATE->croncache        = array();
    $ACCESSLIB_PRIVATE->preloadedcourses = array();
    $ACCESSLIB_PRIVATE->capabilities     = null;

    unset($USER->access);
}

/**
 * This is really slow!!! do not use above course context level
 *
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
 * @param int $roleid
 * @param array $accessdata defaults to null
 * @return array
 */
function get_role_access($roleid, $accessdata = null) {
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
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $rd) {
                $ACCESSLIB_PRIVATE->croncache[$roleid][] = $rd;
            }
            $rs->close();
        }

        foreach ($ACCESSLIB_PRIVATE->croncache[$roleid] as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }

    } else {
        $rs = $DB->get_recordset_sql($sql, $params);
        if ($rs->valid()) {
            foreach ($rs as $rd) {
                $k = "{$rd->path}:{$roleid}";
                $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
            }
            unset($rd);
        }
        $rs->close();
    }

    return $accessdata;
}

/**
 * Gets the accessdata for role "sitewide" (system down to course)
 *
 * @param int $roleid
 * @param array $accessdata defaults to null
 * @return array
 */
function get_default_frontpage_role_access($roleid, $accessdata = null) {

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

    $rs = $DB->get_recordset_sql($sql, $params);
    if ($rs->valid()) {
        foreach ($rs as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
    }
    $rs->close();

    return $accessdata;
}


/**
 * Get the default guest role
 *
 * @return stdClass role
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
            //somebody is messing with guest roles, remove incorrect setting and try to find a new one
            set_config('guestroleid', '');
            return get_guest_role();
        }
    }
}

/**
 * Check whether a user has a particular capability in a given context.
 *
 * For example::
 *      $context = get_context_instance(CONTEXT_MODULE, $cm->id);
 *      has_capability('mod/forum:replypost',$context)
 *
 * By default checks the capabilities of the current user, but you can pass a
 * different userid. By default will return true for admin users, but you can override that with the fourth argument.
 *
 * Guest and not-logged-in users can never get any dangerous capability - that is any write capability
 * or capabilities with XSS, config or data loss risks.
 *
 * @param string $capability the name of the capability to check. For example mod/forum:view
 * @param object $context the context to check the capability in. You normally get this with {@link get_context_instance}.
 * @param integer|object $user A user id or object. By default (null) checks the permissions of the current user.
 * @param boolean $doanything If false, ignores effect of admin role assignment
 * @return boolean true if the user has this capability. Otherwise false.
 */
function has_capability($capability, $context, $user = null, $doanything = true) {
    global $USER, $CFG, $DB, $SCRIPT, $ACCESSLIB_PRIVATE;

    if (during_initial_install()) {
        if ($SCRIPT === "/$CFG->admin/index.php" or $SCRIPT === "/$CFG->admin/cliupgrade.php") {
            // we are in an installer - roles can not work yet
            return true;
        } else {
            return false;
        }
    }

    if (strpos($capability, 'moodle/legacy:') === 0) {
        throw new coding_exception('Legacy capabilities can not be used any more!');
    }

    // the original $CONTEXT here was hiding serious errors
    // for security reasons do not reuse previous context
    if (empty($context)) {
        debugging('Incorrect context specified');
        return false;
    }
    if (!is_bool($doanything)) {
        throw new coding_exception('Capability parameter "doanything" is wierd ("'.$doanything.'"). This has to be fixed in code.');
    }

    // make sure there is a real user specified
    if ($user === null) {
        $userid = isset($USER->id) ? $USER->id : 0;
    } else {
        $userid = is_object($user) ? $user->id : $user;
    }

    // capability must exist
    if (!$capinfo = get_capability_info($capability)) {
        debugging('Capability "'.$capability.'" was not found! This should be fixed in code.');
        return false;
    }
    // make sure the guest account and not-logged-in users never get any risky caps no matter what the actual settings are.
    if (($capinfo->captype === 'write') or ((int)$capinfo->riskbitmask & (RISK_XSS | RISK_CONFIG | RISK_DATALOSS))) {
        if (isguestuser($userid) or $userid == 0) {
            return false;
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
            $ACCESSLIB_PRIVATE->dirtycontexts = null; //load fresh dirty contexts
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
            return has_capability_in_accessdata($capability, $context, $USER->access);
        }
        // Load accessdata for below-the-course contexts
        if (!path_inaccessdata($context->path,$USER->access)) {
            // error_log("loading access for context {$context->path} for $capability at {$context->contextlevel} {$context->id}");
            // $bt = debug_backtrace();
            // error_log("bt {$bt[0]['file']} {$bt[0]['line']}");
            load_subcontext($USER->id, $context, $USER->access);
        }
        return has_capability_in_accessdata($capability, $context, $USER->access);
    }

    if (!isset($ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
        load_user_accessdata($userid);
    }

    if ($context->contextlevel <= CONTEXT_COURSE) {
        // Course and above are always preloaded
        return has_capability_in_accessdata($capability, $context, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid]);
    }
    // Load accessdata for below-the-course contexts as needed
    if (!path_inaccessdata($context->path, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid])) {
        // error_log("loading access for context {$context->path} for $capability at {$context->contextlevel} {$context->id}");
        // $bt = debug_backtrace();
        // error_log("bt {$bt[0]['file']} {$bt[0]['line']}");
        load_subcontext($userid, $context, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid]);
    }
    return has_capability_in_accessdata($capability, $context, $ACCESSLIB_PRIVATE->accessdatabyuser[$userid]);
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
 * @param boolean $doanything If false, ignore effect of admin role assignment
 * @return boolean true if the user has any of these capabilities. Otherwise false.
 */
function has_any_capability($capabilities, $context, $userid = null, $doanything = true) {
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
 * @param boolean $doanything If false, ignore effect of admin role assignment
 * @return boolean true if the user has all of these capabilities. Otherwise false.
 */
function has_all_capabilities($capabilities, $context, $userid = null, $doanything = true) {
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
 * Check if the user is an admin at the site level.
 *
 * Please note that use of proper capabilities is always encouraged,
 * this function is supposed to be used from core or for temporary hacks.
 *
 * @param   int|object  $user_or_id user id or user object
 * @returns bool true if user is one of the administrators, false otherwise
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
        // we support
        $userid = $user_or_id->id;
    } else {
        $userid = $user_or_id;
    }

    $siteadmins = explode(',', $CFG->siteadmins);
    return in_array($userid, $siteadmins);
}

/**
 * Returns true if user has at least one role assign
 * of 'coursecontact' role (is potentially listed in some course descriptions).
 *
 * @param $userid
 * @return stdClass
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
 * @param string $path
 * @return string
 */
function get_course_from_path($path) {
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
    if (empty($accessdata['loaded'])) {
        return false;
    }

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
 * Does the user have a capability to do something?
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
 * ------------------------
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
 * Permission evaluation
 * ---------------------
 * Originally there was an extremely complicated way
 * to determine the user access that dealt with
 * "locality" or role assignments and role overrides.
 * Now we simply evaluate access for each role separately
 * and then verify if user has at least one role with allow
 * and at the same time no role with prohibit.
 *
 * @param string $capability
 * @param object $context
 * @param array $accessdata
 * @return bool
 */
function has_capability_in_accessdata($capability, $context, array $accessdata) {
    global $CFG;

    if (empty($context->id)) {
        throw new coding_exception('Invalid context specified');
    }

    // Build $paths as a list of current + all parent "paths" with order bottom-to-top
    $contextids = explode('/', trim($context->path, '/'));
    $paths = array($context->path);
    while ($contextids) {
        array_pop($contextids);
        $paths[] = '/' . implode('/', $contextids);
    }
    unset($contextids);

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
    foreach ($roles as $roleid => $ignored) {
        foreach ($paths as $path) {
            if (isset($accessdata['rdef']["{$path}:$roleid"][$capability])) {
                $perm = (int)$accessdata['rdef']["{$path}:$roleid"][$capability];
                if ($perm === CAP_PROHIBIT or is_null($roles[$roleid])) {
                    $roles[$roleid] = $perm;
                }
            }
        }
    }
    // any CAP_PROHIBIT found means no permission for the user
    if (array_search(CAP_PROHIBIT, $roles) !== false) {
        return false;
    }

    // at least one CAP_ALLOW means the user has a permission
    return (array_search(CAP_ALLOW, $roles) !== false);
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
    for ($n=$cc-1; $n>=0; $n--) {
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
 * @param bool $doanything If false, ignore effect of admin role assignment
 * @param string $errorstring The error string to to user. Defaults to 'nopermissions'.
 * @param string $stringfile The language file to load the error string from. Defaults to 'error'.
 * @return void terminates with an error if the user does not have the given capability.
 */
function require_capability($capability, $context, $userid = null, $doanything = true,
                            $errormessage = 'nopermissions', $stringfile = '') {
    if (!has_capability($capability, $context, $userid, $doanything)) {
        throw new required_capability_exception($context, $capability, $errormessage, $stringfile);
    }
}

/**
 * Get an array of courses where cap requested is available
 * and user is enrolled, this can be relatively slow.
 *
 * @param string $capability - name of the capability
 * @param array  $accessdata_ignored
 * @param bool   $doanything_ignored
 * @param string $sort - sorting fields - prefix each fieldname with "c."
 * @param array  $fields - additional fields you are interested in...
 * @param int    $limit_ignored
 * @return array $courses - ordered array of course objects - see notes above
 */
function get_user_courses_bycap($userid, $cap, $accessdata_ignored, $doanything_ignored, $sort = 'c.sortorder ASC', $fields = null, $limit_ignored = 0) {

    //TODO: this should be most probably deprecated

    $courses = enrol_get_users_courses($userid, true, $fields, $sort);
    foreach ($courses as $id=>$course) {
        $context = get_context_instance(CONTEXT_COURSE, $id);
        if (!has_capability($cap, $context, $userid)) {
            unset($courses[$id]);
        }
    }

    return $courses;
}


/**
 * Return a nested array showing role assignments
 * all relevant role capabilities for the user at
 * site/course_category/course levels
 *
 * We do _not_ delve deeper than courses because the number of
 * overrides at the module/block levels is HUGE.
 *
 * [ra]   => [/path/][]=roleid
 * [rdef] => [/path/:roleid][capability]=permission
 * [loaded] => array('/path', '/path')
 *
 * @param int $userid - the id of the user
 * @return array
 */
function get_user_access_sitewide($userid) {
    global $CFG, $DB;

    /* Get in 3 cheap DB queries...
     * - role assignments
     * - relevant role caps
     *   - above and within this user's RAs
     *   - below this user's RAs - limited to course level
     */

    $accessdata = array(); // named list
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
            $accessdata['ra'][$ra->path][$ra->roleid] = $ra->roleid;

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
                  JOIN {context} ctx ON rc.contextid=ctx.id
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
 * @param integer $userid the id of the user
 * @param object $context needs path!
 * @param array $accessdata accessdata array
 * @return void
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
                $accessdata['ra'][$ra->path][$ra->roleid] = $ra->roleid;
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
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $rd) {
        $k = "{$rd->path}:{$rd->roleid}";
        if (!array_key_exists($k, $newrdefs)) {
            $newrdefs[$k] = array();
        }
        $newrdefs[$k][$rd->capability] = $rd->permission;
    }
    $rs->close();

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
 * @param integer $roleid the id of the user
 * @param object $context needs path!
 * @param array $accessdata accessdata array null by default
 * @return array
 */
function get_role_access_bycontext($roleid, $context, $accessdata = null) {
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

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $rd) {
        $k = "{$rd->path}:{$roleid}";
        $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
    }
    $rs->close();

    return $accessdata;
}

/**
 * Load accessdata for a user into the $ACCESSLIB_PRIVATE->accessdatabyuser global
 *
 * Used by has_capability() - but feel free
 * to call it if you are about to run a BIG
 * cron run across a bazillion users.
 *
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
            $accessdata['ra'][$base] = array();
        }
        $accessdata['ra'][$base][$CFG->defaultuserroleid] = $CFG->defaultuserroleid;
        $accessdata['dr'] = $CFG->defaultuserroleid;
    }

    //
    // provide "default frontpage role"
    //
    if (!empty($CFG->defaultfrontpageroleid)) {
        $base = '/'. SYSCONTEXTID .'/'. $frontpagecontext->id;
        $accessdata = get_default_frontpage_role_access($CFG->defaultfrontpageroleid, $accessdata);
        if (!isset($accessdata['ra'][$base])) {
            $accessdata['ra'][$base] = array();
        }
        $accessdata['ra'][$base][$CFG->defaultfrontpageroleid] = $CFG->defaultfrontpageroleid;
    }
    // for dirty timestamps in cron
    $accessdata['time'] = time();

    $ACCESSLIB_PRIVATE->accessdatabyuser[$userid] = $accessdata;
    compact_rdefs($ACCESSLIB_PRIVATE->accessdatabyuser[$userid]['rdef']);

    return $ACCESSLIB_PRIVATE->accessdatabyuser[$userid];
}

/**
 * Use shared copy of role definitions stored in ACCESSLIB_PRIVATE->roledefinitions;
 *
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
 * @return void
 */
function load_all_capabilities() {
    global $CFG, $ACCESSLIB_PRIVATE;

    //NOTE: we can not use $USER here because it may no be linked to $_SESSION['USER'] yet!

    // roles not installed yet - we are in the middle of installation
    if (during_initial_install()) {
        return;
    }

    $base = '/'.SYSCONTEXTID;

    if (isguestuser($_SESSION['USER'])) {
        $guest = get_guest_role();

        // Load the rdefs
        $_SESSION['USER']->access = get_role_access($guest->id);
        // Put the ghost enrolment in place...
        $_SESSION['USER']->access['ra'][$base] = array($guest->id => $guest->id);


    } else if (!empty($_SESSION['USER']->id)) { // can not use isloggedin() yet

        $accessdata = get_user_access_sitewide($_SESSION['USER']->id);

        //
        // provide "default role" & set 'dr'
        //
        if (!empty($CFG->defaultuserroleid)) {
            $accessdata = get_role_access($CFG->defaultuserroleid, $accessdata);
            if (!isset($accessdata['ra'][$base])) {
                $accessdata['ra'][$base] = array();
            }
            $accessdata['ra'][$base][$CFG->defaultuserroleid] = $CFG->defaultuserroleid;
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
                $accessdata['ra'][$base] = array();
            }
            $accessdata['ra'][$base][$CFG->defaultfrontpageroleid] = $CFG->defaultfrontpageroleid;
        }
        $_SESSION['USER']->access = $accessdata;

    } else if (!empty($CFG->notloggedinroleid)) {
        $_SESSION['USER']->access = get_role_access($CFG->notloggedinroleid);
        $_SESSION['USER']->access['ra'][$base] = array($CFG->notloggedinroleid => $CFG->notloggedinroleid);
    }

    // Timestamp to read dirty context timestamps later
    $_SESSION['USER']->access['time'] = time();
    $ACCESSLIB_PRIVATE->dirtycontexts = array();

    // Clear to force a refresh
    unset($_SESSION['USER']->mycourses);
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
 * @return void
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
 * @param object $content
 * @param int $roleid
 * @param array $accessdata
 * @return array Returns access data
 */
function load_temp_role($context, $roleid, array $accessdata) {
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
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $rd) {
        $k = "{$rd->path}:{$roleid}";
        $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
    }
    $rs->close();

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
    if (!isset($accessdata['ra'][$context->path])) {
        $accessdata['ra'][$context->path] = array();
    }
    $accessdata['ra'][$context->path][$roleid] = $roleid;

    return $accessdata;
}

/**
 * Removes any extra guest roles from accessdata
 * @param object $context
 * @param array $accessdata
 * @return array access data
 */
function remove_temp_roles($context, array $accessdata) {
    global $DB, $USER;
    $sql = "SELECT DISTINCT ra.roleid AS id
              FROM {role_assignments} ra
             WHERE ra.contextid = :contextid AND ra.userid = :userid";
    $ras = $DB->get_records_sql($sql, array('contextid'=>$context->id, 'userid'=>$USER->id));

    if ($ras) {
        $accessdata['ra'][$context->path] = array_combine(array_keys($ras), array_keys($ras));
    } else {
        $accessdata['ra'][$context->path] = array();
    }

    return $accessdata;
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

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
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
 * @param object $capability a capability - a row from the capabilities table.
 * @return boolean whether this capability is safe - that is, whether people with the
 *      safeoverrides capability should be allowed to change it.
 */
function is_safe_capability($capability) {
    return !((RISK_DATALOSS | RISK_MANAGETRUST | RISK_CONFIG | RISK_XSS | RISK_PERSONAL) & $capability->riskbitmask);
}

/**********************************
 * Context Manipulation functions *
 **********************************/

/**
 * Context creation - internal implementation.
 *
 * Create a new context record for use by all roles-related stuff
 * assumes that the caller has done the homework.
 *
 * DO NOT CALL THIS DIRECTLY, instead use {@link get_context_instance}!
 *
 * @param int $contextlevel
 * @param int $instanceid
 * @param int $strictness
 * @return object newly created context
 */
function create_context($contextlevel, $instanceid, $strictness = IGNORE_MISSING) {
    global $CFG, $DB;

    if ($contextlevel == CONTEXT_SYSTEM) {
        return get_system_context();
    }

    $context = new stdClass();
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
            } else if ($category = $DB->get_record('course_categories', array('id'=>$instanceid), '*', $strictness)) {
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
            } else if ($course = $DB->get_record('course', array('id'=>$instanceid), '*', $strictness)) {
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
            } else if ($cm = $DB->get_record('course_modules', array('id'=>$instanceid), '*', $strictness)) {
                if ($parent = get_context_instance(CONTEXT_COURSE, $cm->course, $strictness)) {
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
            if ($p = $DB->get_record_sql($sql, $params, '*', $strictness)) {
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

    if (!$result) {
        debugging('Error: could not insert new context level "'.
                  s($contextlevel).'", instance "'.
                  s($instanceid).'". ' . $error_message);

        return false;
    }

    $id = $DB->insert_record('context', $context);
    // can't set the full path till we know the id!
    if ($basedepth != 0 and !empty($basepath)) {
        $DB->set_field('context', 'path', $basepath.'/'. $id, array('id'=>$id));
    }
    return get_context_instance_by_id($id);
}

/**
 * Returns system context or null if can not be created yet.
 *
 * @param bool $cache use caching
 * @return mixed system context or null
 */
function get_system_context($cache = true) {
    global $DB, $ACCESSLIB_PRIVATE;
    if ($cache and defined('SYSCONTEXTID')) {
        if (is_null($ACCESSLIB_PRIVATE->systemcontext)) {
            $ACCESSLIB_PRIVATE->systemcontext = new stdClass();
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
        $context = new stdClass();
        $context->contextlevel = CONTEXT_SYSTEM;
        $context->instanceid   = 0;
        $context->depth        = 1;
        $context->path         = null; //not known before insert

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
 * @param int $level
 * @param int $instanceid
 * @param bool $deleterecord false means keep record for now
 * @return bool returns true or throws an exception
 */
function delete_context($contextlevel, $instanceid, $deleterecord = true) {
    global $DB, $ACCESSLIB_PRIVATE, $CFG;

    // do not use get_context_instance(), because the related object might not exist,
    // or the context does not exist yet and it would be created now
    if ($context = $DB->get_record('context', array('contextlevel'=>$contextlevel, 'instanceid'=>$instanceid))) {
        // delete these first because they might fetch the context and try to recreate it!
        blocks_delete_all_for_context($context->id);
        filter_delete_all_for_context($context->id);

        require_once($CFG->dirroot . '/comment/lib.php');
        comment::delete_comments(array('contextid'=>$context->id));

        require_once($CFG->dirroot.'/rating/lib.php');
        $delopt = new stdclass();
        $delopt->contextid = $context->id;
        $rm = new rating_manager();
        $rm->delete_ratings($delopt);

        // delete all files attached to this context
        $fs = get_file_storage();
        $fs->delete_area_files($context->id);

        // now delete stuff from role related tables, role_unassign_all
        // and unenrol should be called earlier to do proper cleanup
        $DB->delete_records('role_assignments', array('contextid'=>$context->id));
        $DB->delete_records('role_capabilities', array('contextid'=>$context->id));
        $DB->delete_records('role_names', array('contextid'=>$context->id));

        // and finally it is time to delete the context record if requested
        if ($deleterecord) {
            $DB->delete_records('context', array('id'=>$context->id));
            // purge static context cache if entry present
            $ACCESSLIB_PRIVATE->contexcache->remove($context);
        }

        // do not mark dirty contexts if parents unknown
        if (!is_null($context->path) and $context->depth > 0) {
            mark_context_dirty($context->path);
        }
    }

    return true;
}

/**
 * Precreates all contexts including all parents
 *
 * @param int $contextlevel empty means all
 * @param bool $buildpaths update paths and depths
 * @return void
 */
function create_contexts($contextlevel = null, $buildpaths = true) {
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

    // transactions used only for performance reasons here
    $transaction = $DB->start_delegated_transaction();

    $rs = $DB->get_recordset_sql($sql);
    foreach ($rs as $ctx) {
        delete_context($ctx->contextlevel, $ctx->instanceid);
    }
    $rs->close();

    $transaction->allow_commit();
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
        $ACCESSLIB_PRIVATE->contexcache->add($context);
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
 * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
 *      MUST_EXIST means throw exception if no record or multiple records found
 * @return object The context object.
 */
function get_context_instance($contextlevel, $instance = 0, $strictness = IGNORE_MISSING) {
    global $DB, $ACCESSLIB_PRIVATE;
    static $allowed_contexts = array(CONTEXT_SYSTEM, CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE, CONTEXT_BLOCK);

/// System context has special cache
    if ($contextlevel == CONTEXT_SYSTEM) {
        return get_system_context();
    }

/// check allowed context levels
    if (!in_array($contextlevel, $allowed_contexts)) {
        // fatal error, code must be fixed - probably typo or switched parameters
        print_error('invalidcourselevel');
    }

    // Various operations rely on context cache
    $cache = $ACCESSLIB_PRIVATE->contexcache;

    if (!is_array($instance)) {
    /// Check the cache
        $context = $cache->get($contextlevel, $instance);
        if ($context) {
            return $context;
        }

    /// Get it from the database, or create it
        if (!$context = $DB->get_record('context', array('contextlevel'=>$contextlevel, 'instanceid'=>$instance))) {
            $context = create_context($contextlevel, $instance, $strictness);
        }

    /// Only add to cache if context isn't empty.
        if (!empty($context)) {
            $cache->add($context);
        }

        return $context;
    }


/// ok, somebody wants to load several contexts to save some db queries ;-)
    $instances = $instance;
    $result = array();

    foreach ($instances as $key=>$instance) {
    /// Check the cache first
        if ($context = $cache->get($contextlevel, $instance)) {  // Already cached
            $result[$instance] = $context;
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
                $cache->add($context);
            }

            $result[$instance] = $context;
        }
    }

    return $result;
}


/**
 * Get a context instance as an object, from a given context id.
 *
 * @param int $id context id
 * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
 *                        MUST_EXIST means throw exception if no record or multiple records found
 * @return stdClass|bool the context object or false if not found.
 */
function get_context_instance_by_id($id, $strictness = IGNORE_MISSING) {
    global $DB, $ACCESSLIB_PRIVATE;

    if ($id == SYSCONTEXTID) {
        return get_system_context();
    }

    $cache = $ACCESSLIB_PRIVATE->contexcache;
    if ($context = $cache->get_by_id($id)) {
        return $context;
    }

    if ($context = $DB->get_record('context', array('id'=>$id), '*', $strictness)) {
        $cache->add($context);
        return $context;
    }

    return false;
}


/**
 * Get the local override (if any) for a given capability in a role in a context
 *
 * @param int $roleid
 * @param int $contextid
 * @param string $capability
 */
function get_local_override($roleid, $contextid, $capability) {
    global $DB;
    return $DB->get_record('role_capabilities', array('roleid'=>$roleid, 'capability'=>$capability, 'contextid'=>$contextid));
}

/**
 * Returns context instance plus related course and cm instances
 * @param int $contextid
 * @return array of ($context, $course, $cm)
 */
function get_context_info_array($contextid) {
    global $DB;

    $context = get_context_instance_by_id($contextid, MUST_EXIST);
    $course  = null;
    $cm      = null;

    if ($context->contextlevel == CONTEXT_COURSE) {
        $course = $DB->get_record('course', array('id'=>$context->instanceid), '*', MUST_EXIST);

    } else if ($context->contextlevel == CONTEXT_MODULE) {
        $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

    } else if ($context->contextlevel == CONTEXT_BLOCK) {
        $parentcontexts = get_parent_contexts($context, false);
        $parent = reset($parentcontexts);
        $parent = get_context_instance_by_id($parent);

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
 * Returns current course id or null if outside of course based on context parameter.
 * @param object $context
 * @return int|bool related course id or false
 */
function get_courseid_from_context($context) {
    if (empty($context->contextlevel)) {
        debugging('Invalid context object specified in get_courseid_from_context() call');
        return false;
    }
    if ($context->contextlevel == CONTEXT_COURSE) {
        return $context->instanceid;
    }

    if ($context->contextlevel < CONTEXT_COURSE) {
        return false;
    }

    if ($context->contextlevel == CONTEXT_MODULE) {
        $parentcontexts = get_parent_contexts($context, false);
        $parent = reset($parentcontexts);
        $parent = get_context_instance_by_id($parent);
        return $parent->instanceid;
    }

    if ($context->contextlevel == CONTEXT_BLOCK) {
        $parentcontexts = get_parent_contexts($context, false);
        $parent = reset($parentcontexts);
        return get_courseid_from_context(get_context_instance_by_id($parent));
    }

    return false;
}


//////////////////////////////////////
//    DB TABLE RELATED FUNCTIONS    //
//////////////////////////////////////

/**
 * function that creates a role
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

    // Get the system context.
    $context = get_context_instance(CONTEXT_SYSTEM);

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
    global $CFG, $DB;

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
 * @param string $capability string name
 * @param int $permission CAP_ constants
 * @param int $roleid role id
 * @param int $contextid context id
 * @param bool $overwrite
 * @return bool always true or exception
 */
function assign_capability($capability, $permission, $roleid, $contextid, $overwrite = false) {
    global $USER, $DB;

    if (empty($permission) || $permission == CAP_INHERIT) { // if permission is not set
        unassign_capability($capability, $roleid, $contextid);
        return true;
    }

    $existing = $DB->get_record('role_capabilities', array('contextid'=>$contextid, 'roleid'=>$roleid, 'capability'=>$capability));

    if ($existing and !$overwrite) {   // We want to keep whatever is there already
        return true;
    }

    $cap = new stdClass();
    $cap->contextid    = $contextid;
    $cap->roleid       = $roleid;
    $cap->capability   = $capability;
    $cap->permission   = $permission;
    $cap->timemodified = time();
    $cap->modifierid   = empty($USER->id) ? 0 : $USER->id;

    if ($existing) {
        $cap->id = $existing->id;
        $DB->update_record('role_capabilities', $cap);
    } else {
        $c = $DB->get_record('context', array('id'=>$contextid));
        $DB->insert_record('role_capabilities', $cap);
    }
    return true;
}

/**
 * Unassign a capability from a role.
 *
 * @param string $capability the name of the capability
 * @param int $roleid the role id
 * @param int $contextid null means all contexts
 * @return boolean success or failure
 */
function unassign_capability($capability, $roleid, $contextid = null) {
    global $DB;

    if (!empty($contextid)) {
        // delete from context rel, if this is the last override in this context
        $DB->delete_records('role_capabilities', array('capability'=>$capability, 'roleid'=>$roleid, 'contextid'=>$contextid));
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
 * @param string $capability - capability name (string)
 * @param string $permission - optional, the permission defined for this capability
 *                      either CAP_ALLOW, CAP_PREVENT or CAP_PROHIBIT. Defaults to null which means any.
 * @param stdClass $context, null means any
 * @return array of role objects
 */
function get_roles_with_capability($capability, $permission = null, $context = null) {
    global $DB;

    if ($context) {
        $contexts = get_parent_contexts($context, true);
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
 * @param int $contextid id of the context
 * @param string $component example 'enrol_ldap', defaults to '' which means manual assignment,
 * @prama int $itemid id of enrolment/auth plugin
 * @param string $timemodified defaults to current time
 * @return int new/existing id of the assignment
 */
function role_assign($roleid, $userid, $contextid, $component = '', $itemid = 0, $timemodified = '') {
    global $USER, $CFG, $DB;

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

    $context = get_context_instance_by_id($contextid, MUST_EXIST);

    if (!$timemodified) {
        $timemodified = time();
    }

/// Check for existing entry
    $ras = $DB->get_records('role_assignments', array('roleid'=>$roleid, 'contextid'=>$context->id, 'userid'=>$userid, 'component'=>$component, 'itemid'=>$itemid), 'id');

    if ($ras) {
        // role already assigned - this should not happen
        if (count($ras) > 1) {
            //very weird - remove all duplicates!
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
    mark_context_dirty($context->path);

    if (!empty($USER->id) && $USER->id == $userid) {
        // If the user is the current user, then do full reload of capabilities too.
        load_all_capabilities();
    }

    events_trigger('role_assigned', $ra);

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
    global $USER, $CFG, $DB;

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
 * @param bool $includmanual include manual role assignments too
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
    foreach($ras as $ra) {
        $DB->delete_records('role_assignments', array('id'=>$ra->id));
        if ($context = get_context_instance_by_id($ra->contextid)) {
            // this is a bit expensive but necessary
            mark_context_dirty($context->path);
            /// If the user is the current user, then do full reload of capabilities too.
            if (!empty($USER->id) && $USER->id == $ra->userid) {
                load_all_capabilities();
            }
        }
        events_trigger('role_unassigned', $ra);
    }
    unset($ras);

    // process subcontexts
    if ($subcontexts and $context = get_context_instance_by_id($params['contextid'])) {
        $contexts = get_child_contexts($context);
        $mparams = $params;
        foreach($contexts as $context) {
            $mparams['contextid'] = $context->id;
            $ras = $DB->get_records('role_assignments', $mparams);
            foreach($ras as $ra) {
                $DB->delete_records('role_assignments', array('id'=>$ra->id));
                // this is a bit expensive but necessary
                mark_context_dirty($context->path);
                /// If the user is the current user, then do full reload of capabilities too.
                if (!empty($USER->id) && $USER->id == $ra->userid) {
                    load_all_capabilities();
                }
                events_trigger('role_unassigned', $ra);
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
 * @return bool
 */
function isloggedin() {
    global $USER;

    return (!empty($USER->id));
}

/**
 * Determines if a user is logged in as real guest user with username 'guest'.
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
 * @param stdClass $context
 * @param stdClass|int $user
 * @return bool
 */
function is_guest($context, $user = null) {
    global $USER;

    // first find the course context
    $coursecontext = get_course_context($context);

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
 * @param stdClass $context
 * @param int|object $user, if null $USER is used
 * @param string $withcapability extra capability name
 * @return bool
 */
function is_viewing($context, $user = null, $withcapability = '') {
    // first find the course context
    $coursecontext = get_course_context($context);

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
 * @param object $context
 * @param int|object $user, if null $USER is used, otherwise user object or id expected
 * @param string $withcapability extra capability name
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return bool
 */
function is_enrolled($context, $user = null, $withcapability = '', $onlyactive = false) {
    global $USER, $DB;

    // first find the course context
    $coursecontext = get_course_context($context);

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
        if ($onlyactive) {
            $sql = "SELECT ue.*
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
                      JOIN {user} u ON u.id = ue.userid
                     WHERE ue.userid = :userid AND ue.status = :active AND e.status = :enabled AND u.deleted = 0";
            $params = array('enabled'=>ENROL_INSTANCE_ENABLED, 'active'=>ENROL_USER_ACTIVE, 'userid'=>$userid, 'courseid'=>$coursecontext->instanceid);
            // this result should be very small, better not do the complex time checks in sql for now ;-)
            $enrolments = $DB->get_records_sql($sql, $params);
            $now = time();
            // make sure the enrol period is ok
            $result = false;
            foreach ($enrolments as $e) {
                if ($e->timestart > $now) {
                    continue;
                }
                if ($e->timeend and $e->timeend < $now) {
                    continue;
                }
                $result = true;
                break;
            }
            if (!$result) {
                return false;
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
 * @global moodle_database $DB
 * @param stdClass $context
 * @param stdClass|null $user
 * @param string $withcapability Check for this capability as well.
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @param boolean $trustcache If set to false guest access will always be checked
 *                             against the enrolment plugins from the course, rather
 *                             than the cache generated by require_login.
 * @return boolean Returns true if the user is able to access the course
 */
function can_access_course($context, $user = null, $withcapability = '', $onlyactive = false, $trustcache = true) {
    global $DB, $USER;

    $coursecontext = get_course_context($context);
    $courseid = $coursecontext->instanceid;

    // First check the obvious, is the user viewing or is the user enrolled.
    if (is_viewing($coursecontext, $user, $withcapability) || is_enrolled($coursecontext, $user, $withcapability, $onlyactive)) {
        // How easy was that!
        return true;
    }

    $access = false;
    if (!isset($USER->enrol)) {
        // Cache hasn't been generated yet so we can't trust it
        $trustcache = false;
        /**
         * These flags within the $USER object should NEVER be used outside of this
         * function can_access_course and the function require_login.
         * Doing so WILL break future versions!!!!
         */
        $USER->enrol = array();
        $USER->enrol['enrolled'] = array();
        $USER->enrol['tempguest'] = array();
    }

    // If we don't trust the cache we need to check with the courses enrolment
    // plugin instances to see if the user can access the course as a guest.
    if (!$trustcache) {
        // Ok, off to the database we go!
        $instances = $DB->get_records('enrol', array('courseid'=>$courseid, 'status'=>ENROL_INSTANCE_ENABLED), 'sortorder, id ASC');
        $enrols = enrol_get_plugins(true);
        foreach($instances as $instance) {
            if (!isset($enrols[$instance->enrol])) {
                continue;
            }
            $until = $enrols[$instance->enrol]->try_guestaccess($instance);
            if ($until !== false) {
                // Never use me anywhere but here and require_login
                $USER->enrol['tempguest'][$courseid] = $until;
                $access = true;
                break;
            }
        }
    }

    // If we don't already have access (from above) check the cache and see whether
    // there is record of it in there.
    if (!$access && isset($USER->enrol['tempguest'][$courseid])) {
        // Never use me anywhere but here and require_login
        if ($USER->enrol['tempguest'][$courseid] == 0) {
            $access = true;
        } else if ($USER->enrol['tempguest'][$courseid] > time()) {
            $access = true;
        } else {
            //expired
            unset($USER->enrol['tempguest'][$courseid]);
        }
    }
    return $access;
}

/**
 * Returns array with sql code and parameters returning all ids
 * of users enrolled into course.
 *
 * This function is using 'eu[0-9]+_' prefix for table names and parameters.
 *
 * @param object $context
 * @param string $withcapability
 * @param int $groupid 0 means ignore groups, any other value limits the result by group id
 * @param bool $onlyactive consider only active enrolments in enabled plugins and time restrictions
 * @return array list($sql, $params)
 */
function get_enrolled_sql($context, $withcapability = '', $groupid = 0, $onlyactive = false) {
    global $DB, $CFG;

    // use unique prefix just in case somebody makes some SQL magic with the result
    static $i = 0;
    $i++;
    $prefix = 'eu'.$i.'_';

    // first find the course context
    $coursecontext = get_course_context($context);

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
 * @param object $context
 * @param string $withcapability
 * @param int $groupid 0 means ignore groups, any other value limits the result by group id
 * @param string $userfields requested user record fields
 * @param string $orderby
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return array of user records
 */
function get_enrolled_users($context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0) {
    global $DB;

    list($esql, $params) = get_enrolled_sql($context, $withcapability, $groupid);
    $sql = "SELECT $userfields
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
             WHERE u.deleted = 0";

    if ($orderby) {
        $sql = "$sql ORDER BY $orderby";
    } else {
        $sql = "$sql ORDER BY u.lastname ASC, u.firstname ASC";
    }

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Counts list of users enrolled into course (as per above function)
 * @param object $context
 * @param string $withcapability
 * @param int $groupid 0 means ignore groups, any other value limits the result by group id
 * @return array of user records
 */
function count_enrolled_users($context, $withcapability = '', $groupid = 0) {
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
            debugging('componentname_capabilities array is deprecated, please use capabilities array only in access.php files');
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
function get_cached_capabilities($component = 'moodle') {
    global $DB;
    return $DB->get_records('capabilities', array('component'=>$component));
}

/**
 * Returns default capabilities for given role archetype.
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
 * @param int $roleid
 * @return void
 */
function reset_role_capabilities($roleid) {
    global $DB;

    $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);
    $defaultcaps = get_default_capabilities($role->archetype);

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    $DB->delete_records('role_capabilities', array('roleid'=>$roleid));

    foreach($defaultcaps as $cap=>$permission) {
        assign_capability($cap, $permission, $roleid, $sitecontext->id);
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
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean true if success, exception in case of any problems
 */
function update_capabilities($component = 'moodle') {
    global $DB, $OUTPUT, $ACCESSLIB_PRIVATE;

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
    foreach ($newcaps as $capname => $capdef) {
        $capability = new stdClass();
        $capability->name         = $capname;
        $capability->captype      = $capdef['captype'];
        $capability->contextlevel = $capdef['contextlevel'];
        $capability->component    = $component;
        $capability->riskbitmask  = $capdef['riskbitmask'];

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
    $ACCESSLIB_PRIVATE->capabilities = null;

    return true;
}


/**
 * Deletes cached capabilities that are no longer needed by the component.
 * Also unassigns these capabilities from any roles that have them.
 *
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
            CONTEXT_SYSTEM    => get_string('coresystem'),
            CONTEXT_USER      => get_string('user'),
            CONTEXT_COURSECAT => get_string('category'),
            CONTEXT_COURSE    => get_string('course'),
            CONTEXT_MODULE    => get_string('activitymodule'),
            CONTEXT_BLOCK     => get_string('block')
        );
    }
    return $strcontextlevels[$contextlevel];
}

/**
 * Prints human readable context identifier.
 *
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
                $name .= format_string($category->name, true, array('context' => $context));
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
                        $name .= format_string($course->shortname, true, array('context' => $context));
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
 * @param object $context the context.
 * @return moodle_url
 */
function get_context_url($context) {
    global $COURSE, $DB;

    switch ($context->contextlevel) {
        case CONTEXT_USER:
            if ($COURSE->id == SITEID) {
                $url = new moodle_url('/user/profile.php', array('id'=>$context->instanceid));
            } else {
                $url = new moodle_url('/user/view.php', array('id'=>$context->instanceid, 'courseid'=>$COURSE->id));
            }
            return $url;;

        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            return new moodle_url('/course/category.php', array('id'=>$context->instanceid));

        case CONTEXT_COURSE: // 1 to 1 to course cat
            if ($context->instanceid != SITEID) {
                return new moodle_url('/course/view.php', array('id'=>$context->instanceid));
            }
            break;

        case CONTEXT_MODULE: // 1 to 1 to course
            if ($modname = $DB->get_field_sql('SELECT md.name AS modname FROM {course_modules} cm ' .
                    'JOIN {modules} md ON md.id = cm.module WHERE cm.id = ?', array($context->instanceid))) {
                return new moodle_url('/mod/' . $modname . '/view.php', array('id'=>$context->instanceid));
            }
            break;

        case CONTEXT_BLOCK:
            $parentcontexts = get_parent_contexts($context, false);
            $parent = reset($parentcontexts);
            $parent = get_context_instance_by_id($parent);
            return get_context_url($parent);
    }

    return new moodle_url('/');
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
            list($extra, $params) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap');
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
            if (file_exists($modfile)) {
                include_once($modfile);
                $modfunction = $module->name.'_get_extra_capabilities';
                if (function_exists($modfunction)) {
                    $extracaps = $modfunction();
                }
            }
            if (empty($extracaps)) {
                $extracaps = array();
            }

            $extracaps = array_merge($subcaps, $extracaps);

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
            $params['component'] = "mod_$module->name";
        break;

        case CONTEXT_BLOCK: // block caps
            $bi = $DB->get_record('block_instances', array('id' => $context->instanceid));

            $extra = '';
            $extracaps = block_method_result($bi->blockname, 'get_extra_capabilities');
            if ($extracaps) {
                list($extra, $params) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap');
                $extra = "OR name $extra";
            }

            $SQL = "SELECT *
                      FROM {capabilities}
                     WHERE (contextlevel = ".CONTEXT_BLOCK."
                           AND component = :component)
                           $extra";
            $params['component'] = 'block_' . $bi->blockname;
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
 * @param obj $context
 * @param int $roleid
 * @param string $cap capability, optional, defaults to ''
 * @return array of capabilities
 */
function role_context_capabilities($roleid, $context, $cap = '') {
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
 * Constructs array with contextids as first parameter and context paths,
 * in both cases bottom top including self.
 *
 * @param object $context
 * @return array
 */
function get_context_info_list($context) {
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
 * Find course context
 * @param object $context - course or lower context
 * @return object context of the enclosing course, throws exception when related course context can not be found
 */
function get_course_context($context) {
    if (empty($context->contextlevel)) {
        throw new coding_exception('Invalid context parameter.');

    } if ($context->contextlevel == CONTEXT_COURSE) {
        return $context;

    } else if ($context->contextlevel == CONTEXT_MODULE) {
        return get_context_instance_by_id(get_parent_contextid($context, MUST_EXIST));

    } else if ($context->contextlevel == CONTEXT_BLOCK) {
        $parentcontext = get_context_instance_by_id(get_parent_contextid($context, MUST_EXIST));
        if ($parentcontext->contextlevel == CONTEXT_COURSE) {
            return $parentcontext;
        } else if ($parentcontext->contextlevel == CONTEXT_MODULE) {
            return get_context_instance_by_id(get_parent_contextid($parentcontext, MUST_EXIST));
        } else {
            throw new coding_exception('Invalid level of block context parameter.');
        }
    }

    throw new coding_exception('Invalid context level of parameter.');
}

/**
 * Check if context is the front page context or a context inside it
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

    $array = array();
    $cache = $ACCESSLIB_PRIVATE->contexcache;

    switch ($context->contextlevel) {

        case CONTEXT_BLOCK:
            // No children.
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
                $cache->add($rec);
                $array[$rec->id] = $rec;
            }
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
                $cache->add($rec);
                $array[$rec->id] = $rec;
            }
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
                $cache->add($rec);
                $array[$rec->id] = $rec;
            }
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
                $cache->add($rec);
                $array[$rec->id] = $rec;
            }
            break;

        case CONTEXT_SYSTEM:
            // Just get all the contexts except for CONTEXT_SYSTEM level
            // and hope we don't OOM in the process - don't cache
            $sql = "SELECT c.*
                      FROM {context} c
                     WHERE contextlevel != ".CONTEXT_SYSTEM;

            $records = $DB->get_records_sql($sql);
            foreach ($records as $rec) {
                $array[$rec->id] = $rec;
            }
        break;

        default:
            print_error('unknowcontext', '', '', $context->contextlevel);
            return false;
    }
    return $array;
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
 * Returns capability information (cached)
 *
 * @param string $capabilityname
 * @return object or null if capability not found
 */
function get_capability_info($capabilityname) {
    global $ACCESSLIB_PRIVATE, $DB; // one request per page only

    // TODO: cache this in shared memory if available, use new $CFG->roledefrev for version check

    if (empty($ACCESSLIB_PRIVATE->capabilities)) {
        $ACCESSLIB_PRIVATE->capabilities = array();
        $caps = $DB->get_records('capabilities', array(), 'id, name, captype, riskbitmask');
        foreach ($caps as $cap) {
            $capname = $cap->name;
            unset($cap->id);
            unset($cap->name);
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
        // TODO this is really hacky
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
 * @param object $context
 * @return array
 */
function get_profile_roles($context) {
    global $CFG, $DB;

    if (empty($CFG->profileroles)) {
        return array();
    }

    $allowed = explode(',', $CFG->profileroles);
    list($rallowed, $params) = $DB->get_in_or_equal($allowed, SQL_PARAMS_NAMED);

    $contextlist = get_related_contexts_string($context);

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder
              FROM {role_assignments} ra, {role} r
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
                   AND r.id $rallowed
          ORDER BY r.sortorder ASC";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Gets the list of roles assigned to this context and up (parents)
 *
 * @param object $context
 * @return array
 */
function get_roles_used_in_context($context) {
    global $DB;

    $contextlist = get_related_contexts_string($context);

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder
              FROM {role_assignments} ra, {role} r
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
          ORDER BY r.sortorder ASC";

    return $DB->get_records_sql($sql);
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
    global $CFG, $DB,$USER;

    if (empty($CFG->profileroles)) {
        return '';
    }

    if ($courseid == SITEID) {
        $context = get_context_instance(CONTEXT_SYSTEM);
    } else {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    }

    if (empty($CFG->profileroles)) {
        return array();
    }

    $allowed = explode(',', $CFG->profileroles);
    list($rallowed, $params) = $DB->get_in_or_equal($allowed, SQL_PARAMS_NAMED);

    $contextlist = get_related_contexts_string($context);

    $sql = "SELECT DISTINCT r.id, r.name, r.shortname, r.sortorder
              FROM {role_assignments} ra, {role} r
             WHERE r.id = ra.roleid
                   AND ra.contextid $contextlist
                   AND r.id $rallowed
                   AND ra.userid = :userid
          ORDER BY r.sortorder ASC";
    $params['userid'] = $userid;

    $rolestring = '';

    if ($roles = $DB->get_records_sql($sql, $params)) {
        foreach ($roles as $userrole) {
            $rolenames[$userrole->id] = $userrole->name;
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
 * Checks if a user can assign users to a particular role in this context
 *
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
 * @return array
 */
function get_all_roles() {
    global $DB;
    return $DB->get_records('role', null, 'sortorder ASC');
}

/**
 * Returns roles of a specified archetype
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
 * @param object $context
 * @param int $userid
 * @param bool $checkparentcontexts defaults to true
 * @param string $order defaults to 'c.contextlevel DESC, r.sortorder ASC'
 * @return array
 */
function get_user_roles($context, $userid = 0, $checkparentcontexts = true, $order = 'c.contextlevel DESC, r.sortorder ASC') {
    global $USER, $DB;

    if (empty($userid)) {
        if (empty($USER->id)) {
            return array();
        }
        $userid = $USER->id;
    }

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
          ORDER BY $order";

    return $DB->get_records_sql($sql ,$params);
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
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
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
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
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
 * @param object $context the context.
 * @param int $rolenamedisplay the type of role name to display. One of the
 *      ROLENAME_X constants. Default ROLENAME_ALIAS.
 * @param bool $withusercounts if true, count the number of users with each role.
 * @param integer|object $user A user id or object. By default (null) checks the permissions of the current user.
 * @return array if $withusercounts is false, then an array $roleid => $rolename.
 *      if $withusercounts is true, returns a list of three arrays,
 *      $rolenames, $rolecounts, and $nameswithcounts.
 */
function get_assignable_roles($context, $rolenamedisplay = ROLENAME_ALIAS, $withusercounts = false, $user = null) {
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

    $parents = get_parent_contexts($context, true);
    $contexts = implode(',' , $parents);

    $params = array();
    $extrafields = '';
    if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT or $rolenamedisplay == ROLENAME_SHORT) {
        $extrafields .= ', r.shortname';
    }

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
        $assignrestriction = "JOIN (SELECT DISTINCT raa.allowassign AS id
                                      FROM {role_allow_assign} raa
                                      JOIN {role_assignments} ra ON ra.roleid = raa.roleid
                                     WHERE ra.userid = :userid AND ra.contextid IN ($contexts)
                                   ) ar ON ar.id = r.id";
        $params['userid'] = $userid;
    }
    $params['contextlevel'] = $context->contextlevel;
    $sql = "SELECT r.id, r.name $extrafields
              FROM {role} r
              $assignrestriction
              JOIN {role_context_levels} rcl ON r.id = rcl.roleid
             WHERE rcl.contextlevel = :contextlevel
          ORDER BY r.sortorder ASC";
    $roles = $DB->get_records_sql($sql, $params);

    $rolenames = array();
    foreach ($roles as $role) {
        if ($rolenamedisplay == ROLENAME_SHORT) {
            $rolenames[$role->id] = $role->shortname;
            continue;
        }
        $rolenames[$role->id] = $role->name;
        if ($rolenamedisplay == ROLENAME_ORIGINALANDSHORT) {
            $rolenames[$role->id] .= ' (' . $role->shortname . ')';
        }
    }
    if ($rolenamedisplay != ROLENAME_ORIGINALANDSHORT and $rolenamedisplay != ROLENAME_SHORT) {
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
 * @param object $context a context.
 * @return array an array $roleid => $rolename.
 */
function get_switchable_roles($context) {
    global $USER, $DB;

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    $params = array();
    $extrajoins = '';
    $extrawhere = '';
    if (!is_siteadmin()) {
        // Admins are allowed to switch to any role with.
        // Others are subject to the additional constraint that the switch-to role must be allowed by
        // 'role_allow_switch' for some role they have assigned in this context or any parent.
        $parents = get_parent_contexts($context);
        $parents[] = $context->id;
        $contexts = implode(',' , $parents);

        $extrajoins = "JOIN {role_allow_switch} ras ON ras.allowswitch = rc.roleid
        JOIN {role_assignments} ra ON ra.roleid = ras.roleid";
        $extrawhere = "WHERE ra.userid = :userid AND ra.contextid IN ($contexts)";
        $params['userid'] = $USER->id;
    }

    $query = "
        SELECT r.id, r.name
          FROM (SELECT DISTINCT rc.roleid
                  FROM {role_capabilities} rc
                  $extrajoins
                  $extrawhere) idlist
          JOIN {role} r ON r.id = idlist.roleid
      ORDER BY r.sortorder";

    $rolenames = $DB->get_records_sql_menu($query, $params);
    return role_fix_names($rolenames, $context, ROLENAME_ALIAS);
}

/**
 * Gets a list of roles that this user can override in this context.
 *
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

    if (is_siteadmin()) {
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
 * Create a role menu suitable for default role selection in enrol plugins.
 * @param object $context
 * @param int $addroleid current or default role - always added to list
 * @return array roleid=>localised role name
 */
function get_default_enrol_roles($context, $addroleid = null) {
    global $DB;

    $params = array('contextlevel'=>CONTEXT_COURSE);
    if ($addroleid) {
        $addrole = "OR r.id = :addroleid";
        $params['addroleid'] = $addroleid;
    } else {
        $addrole = "";
    }
    $sql = "SELECT r.id, r.name
              FROM {role} r
         LEFT JOIN {role_context_levels} rcl ON (rcl.roleid = r.id AND rcl.contextlevel = :contextlevel)
             WHERE rcl.id IS NOT NULL $addrole
          ORDER BY sortorder DESC";

    $roles = $DB->get_records_sql_menu($sql, $params);
    $roles = role_fix_names($roles, $context, ROLENAME_BOTH);

    return $roles;
}

/**
 * @param integer $roleid the id of a role.
 * @return array list of the context levels at which this role may be assigned.
 */
function get_role_contextlevels($roleid) {
    global $DB;
    return $DB->get_records_menu('role_context_levels', array('roleid' => $roleid),
            'contextlevel', 'id,contextlevel');
}

/**
 * @param integer $contextlevel a contextlevel.
 * @return array list of role ids that are assignable at this context level.
 */
function get_roles_for_contextlevels($contextlevel) {
    global $DB;
    return $DB->get_records_menu('role_context_levels', array('contextlevel' => $contextlevel),
            '', 'id,roleid');
}

/**
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
 * @param object $context
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
 * @return mixed
 */
function get_users_by_capability($context, $capability, $fields = '', $sort = '', $limitfrom = '', $limitnum = '',
                                 $groups = '', $exceptions = '', $doanything_ignored = null, $view_ignored = null, $useviewallgroups = false) {
    global $CFG, $DB;

    if (empty($context->id)) {
        throw new coding_exception('Invalid context specified');
    }

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
            // everybody is disqualified on the frontapge
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

    /// ***** Set up default fields ******
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

    /// Set up default sort
    if (empty($sort)) { // default to course lastaccess or just lastaccess
        if ($iscoursepage) {
            $sort = 'ul.timeaccess';
        } else {
            $sort = 'u.lastaccess';
        }
    }
    $sortby = "ORDER BY $sort";

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

    /// We never return deleted users or guest account.
    $wherecond[] = "u.deleted = 0 AND u.id <> :guestid";
    $params['guestid'] = $CFG->siteguest;

    /// Groups
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

    /// User exceptions
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

    /// Ok, let's get the users!
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
 * @param object $context
 * @param array $roles ids of the roles to include, optional
 * @param string $policy defaults to locality, more about
 * @return array sorted copy of the array
 */
function sort_by_roleassignment_authority($users, $context, $roles = array(), $sortpolicy = 'locality') {
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
 * @param stdClass $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @param string $fields fields from user (u.) , role assignment (ra) or role (r.)
 * @param string $sort sort from user (u.) , role assignment (ra) or role (r.)
 * @param bool $gethidden_ignored use enrolments instead
 * @param string $group defaults to ''
 * @param mixed $limitfrom defaults to ''
 * @param mixed $limitnum defaults to ''
 * @param string $extrawheretest defaults to ''
 * @param string|array $whereparams defaults to ''
 * @return array
 */
function get_role_users($roleid, $context, $parent = false, $fields = '',
        $sort = 'u.lastname, u.firstname', $gethidden_ignored = null, $group = '',
        $limitfrom = '', $limitnum = '', $extrawheretest = '', $whereparams = array()) {
    global $DB;

    if (empty($fields)) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.lang, u.timezone, u.lastaccess, u.mnethostid, r.name AS rolename, r.sortorder';
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

    $sql = "SELECT DISTINCT $fields, ra.roleid
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
              JOIN {role} r ON ra.roleid = r.id
        $groupjoin
             WHERE (ra.contextid = ? $parentcontexts)
                   $roleselect
                   $groupselect
                   $extrawheretest
          ORDER BY $sort";                  // join now so that we can just use fullname() later

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Counts all the users assigned this role in this context or higher
 *
 * @param mixed $roleid either int or an array of ints
 * @param object $context
 * @param bool $parent if true, get list of users assigned in higher context too
 * @return int Returns the result count
 */
function count_role_users($roleid, $context, $parent = false) {
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

    $courses = array();
    $rs = $DB->get_recordset_sql("SELECT x.*, c.id AS courseid $fieldlist
                                    FROM {course} c
                                   INNER JOIN {context} x
                                         ON (c.id=x.instanceid AND x.contextlevel=".CONTEXT_COURSE.")
                                $orderby");
    // Check capability for each course in turn
    foreach ($rs as $coursecontext) {
        if (has_capability($capability, $coursecontext, $userid, $doanything)) {
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
    return empty($courses) ? false : $courses;
}

/**
 * This function finds the roles assigned directly to this context only
 * i.e. no parents role
 *
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
 * Checks if the user has switched roles within the given course.
 *
 * Note: You can only switch roles within the course, hence it takes a courseid
 * rather than a context. On that note Petr volunteered to implement this across
 * all other contexts, all requests for this should be forwarded to him ;)
 *
 * @param int $courseid The id of the course to check
 * @return bool True if the user has switched roles within the course.
 */
function is_role_switched($courseid) {
    global $USER;
    $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);
    return (!empty($USER->access['rsw'][$context->path]));
}

/**
 * Get any role that has an override on exact context
 *
 * @global moodle_database
 * @param stdClass $context The context to check
 * @return array An array of roles
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
 * Find all user assignment of users for this role, on this context
 *
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
        if (!$context = get_context_instance_by_id($contextid)) {
            return false;
        }
        $parents = get_parent_contexts($context, true);
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
 * @param array $roleoptions array roleid => rolename or roleid => roleobject
 * @param object $context a context
 * @return array Array of context-specific role names, or role objexts with a ->localname field added.
 */
function role_fix_names($roleoptions, $context, $rolenamedisplay = ROLENAME_ALIAS) {
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
 * but when we are in grade, all reports/import/export capabilities should be together
 *
 * @param string $cap component string a
 * @param string $comp component string b
 * @param mixed $contextlevel
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
 * Rebuild all related context depth and path caches
 *
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
function build_context_path($force = false) {
    global $CFG, $DB, $ACCESSLIB_PRIVATE;

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
                         WHERE id IN (SELECT id FROM {context_temp})";
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
    // pctx.path IS NOT NULL prevents fatal problems with broken block instances that point to invalid context parent
    $sql = "INSERT INTO {context_temp} (id, path, depth)
            SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {context} ctx
              JOIN {block_instances} bi ON ctx.instanceid = bi.id
              JOIN {context} pctx ON bi.parentcontextid = pctx.id
             WHERE ctx.contextlevel=".CONTEXT_BLOCK."
                   AND pctx.path IS NOT NULL
                   AND NOT EXISTS (SELECT 'x'
                                   FROM {context_temp} temp
                                   WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    $DB->execute($sql);

    $DB->execute($updatesql);
    $DB->delete_records('context_temp');

    // reset static course cache - it might have incorrect cached data
    $ACCESSLIB_PRIVATE->contexcache->reset();
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
 * @param stdClass $current context obj
 * @param stdClass $newparent new parent obj
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
 * Preloads context information together with instances.
 * NOTE: in future this function may return empty strings
 *       if we implement different caching.
 *
 * @param string $joinon for example 'u.id'
 * @param string $contextlevel context level of instance in $joinon
 * @param string $tablealias context table alias
 * @return array with two values - select and join part
 */
function context_instance_preload_sql($joinon, $contextlevel, $tablealias) {
    $select = ", $tablealias.id AS ctxid, $tablealias.path AS ctxpath, $tablealias.depth AS ctxdepth, $tablealias.contextlevel AS ctxlevel, $tablealias.instanceid AS ctxinstance";
    $join = "LEFT JOIN {context} $tablealias ON ($tablealias.instanceid = $joinon AND $tablealias.contextlevel = $contextlevel)";
    return array($select, $join);
}

/**
 * Preloads context information from db record and strips the cached info.
 * The db request has to ontain both the $join and $select from context_instance_preload_sql()
 *
 * @param object $rec
 * @return void (modifies $rec)
 */
function context_instance_preload(stdClass $rec) {
    global $ACCESSLIB_PRIVATE;
    if (empty($rec->ctxid)) {
        // $rec does not have enough data, passed here repeatedly or context does not exist yet
        return;
    }

    // note: in PHP5 the objects are passed by reference, no need to return $rec
    $context = new stdClass();
    $context->id           = $rec->ctxid;       unset($rec->ctxid);
    $context->path         = $rec->ctxpath;     unset($rec->ctxpath);
    $context->depth        = $rec->ctxdepth;    unset($rec->ctxdepth);
    $context->contextlevel = $rec->ctxlevel;    unset($rec->ctxlevel);
    $context->instanceid   = $rec->ctxinstance; unset($rec->ctxinstance);

    $ACCESSLIB_PRIVATE->contexcache->add($context);
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


/**
 * Returns two lists, this can be used to find out if user has capability.
 * Having any needed role and no forbidden role in this context means
 * user has this capability in this context.
 * Use get_role_names_with_cap_in_context() if you need role names to display in the UI
 *
 * @param object $context
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
 * @param object $context
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
 * @param object $context
 * @param array $capabilities An array of capabilities
 * @return array of roles with all of the required capabilities
 */
function get_role_names_with_caps_in_context($context, $capabilities) {
    global $DB;

    $rolesthatcanrate = get_roles_with_caps_in_context($context, $capabilities);

    $allroles = array();
    $roles = $DB->get_records('role', null, 'sortorder DESC');
    foreach ($roles as $roleid=>$role) {
        $allroles[$roleid] = $role->name;
    }

    $rolenames = array();
    foreach ($rolesthatcanrate as $r) {
        $rolenames[$r] = $allroles[$r];
    }
    $rolenames = role_fix_names($rolenames, $context);
    return $rolenames;
}

/**
 * This function verifies the prohibit comes from this context
 * and there are no more prohibits in parent contexts.
 * @param object $context
 * @param string $capability name
 * @return bool
 */
function prohibit_is_removable($roleid, $context, $capability) {
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
        // more prohibints can not be removed
        return false;
    }

    return !empty($prohibits[$context->id]);
}

/**
 * More user friendly role permission changing,
 * it should produce as few overrides as possible.
 * @param int $roleid
 * @param object $context
 * @param string $capname capability name
 * @param int $permission
 * @return void
 */
function role_change_permission($roleid, $context, $capname, $permission) {
    global $DB;

    if ($permission == CAP_INHERIT) {
        unassign_capability($capname, $roleid, $context->id);
        mark_context_dirty($context->path);
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
                mark_context_dirty($context->path);
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
    mark_context_dirty($context->path);
}


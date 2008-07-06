<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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
 * Public API vs internals 
 * -----------------------
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
 * - $ACCESS global
 * - has_capability_in_accessdata()
 * - is_siteadmin()
 * - get_user_access_sitewide()
 * - load_subcontext()
 * - get_role_access_bycontext()
 *
 * Name conventions
 * ----------------
 * 
 * - "ctx" means context
 *
 * accessdata
 * ----------
 *
 * Access control data is held in the "accessdata" array
 * which - for the logged-in user, will be in $USER->access
 * 
 * For other users can be generated and passed around (but see
 * the $ACCESS global).
 *
 * $accessdata is a multidimensional array, holding
 * role assignments (RAs), role-capabilities-perm sets 
 * (role defs) and a list of courses we have loaded
 * data for.
 *
 * Things are keyed on "contextpaths" (the path field of 
 * the context table) for fast walking up/down the tree.
 * 
 * $accessdata[ra][$contextpath]= array($roleid)
 *                [$contextpath]= array($roleid)
 *                [$contextpath]= array($roleid) 
 *
 * Role definitions are stored like this
 * (no cap merge is done - so it's compact)
 *
 * $accessdata[rdef][$contextpath:$roleid][mod/forum:viewpost] = 1
 *                                        [mod/forum:editallpost] = -1
 *                                        [mod/forum:startdiscussion] = -1000
 *
 * See how has_capability_in_accessdata() walks up/down the tree.
 *
 * Normally - specially for the logged-in user, we only load
 * rdef and ra down to the course level, but not below. This
 * keeps accessdata small and compact. Below-the-course ra/rdef
 * are loaded as needed. We keep track of which courses we
 * have loaded ra/rdef in 
 *
 * $accessdata[loaded] = array($contextpath, $contextpath) 
 *
 * Stale accessdata
 * ----------------
 *
 * For the logged-in user, accessdata is long-lived.
 *
 * On each pageload we load $DIRTYPATHS which lists
 * context paths affected by changes. Any check at-or-below
 * a dirty context will trigger a transparent reload of accessdata.
 * 
 * Changes at the sytem level will force the reload for everyone.
 *
 * Default role caps
 * -----------------
 * The default role assignment is not in the DB, so we 
 * add it manually to accessdata. 
 *
 * This means that functions that work directly off the
 * DB need to ensure that the default role caps
 * are dealt with appropriately. 
 *
 */

require_once $CFG->dirroot.'/lib/blocklib.php';

// permission definitions
define('CAP_INHERIT', 0);
define('CAP_ALLOW', 1);
define('CAP_PREVENT', -1);
define('CAP_PROHIBIT', -1000);

// context definitions
define('CONTEXT_SYSTEM', 10);
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

// rolename displays
define('ROLENAME_ORIGINAL', 0);// the name as defined in the role definition
define('ROLENAME_ALIAS', 1);   // the name as defined by a role alias 
define('ROLENAME_BOTH', 2);    // Both, like this:  Role alias (Original)

require_once($CFG->dirroot.'/group/lib.php');

$context_cache    = array();    // Cache of all used context objects for performance (by level and instance)
$context_cache_id = array();    // Index to above cache by id

$DIRTYCONTEXTS = null; // dirty contexts cache
$ACCESS = array(); // cache of caps for cron user switching and has_capability for other users (==not $USER)
$RDEFS = array(); // role definitions cache - helps a lot with mem usage in cron

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
    $searchcontexts = array_keys(get_child_contexts($context));
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

/**
 * Gets the accessdata for role "sitewide" 
 * (system down to course)
 *
 * @return array
 */
function get_role_access($roleid, $accessdata=NULL) {

    global $CFG;

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
            FROM {$CFG->prefix}context ctx
            JOIN {$CFG->prefix}role_capabilities rc
              ON rc.contextid=ctx.id
            WHERE rc.roleid = {$roleid}
                  AND ctx.contextlevel <= ".CONTEXT_COURSE."
            ORDER BY ctx.depth, ctx.path";

    // we need extra caching in cron only
    if (defined('FULLME') and FULLME === 'cron') {
        static $cron_cache = array();

        if (!isset($cron_cache[$roleid])) {
            $cron_cache[$roleid] = array();
            if ($rs = get_recordset_sql($sql)) {
                while ($rd = rs_fetch_next_record($rs)) {
                    $cron_cache[$roleid][] = $rd;
                }
                rs_close($rs);
            }
        }

        foreach ($cron_cache[$roleid] as $rd) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        
    } else {
        if ($rs = get_recordset_sql($sql)) {
            while ($rd = rs_fetch_next_record($rs)) {
                $k = "{$rd->path}:{$roleid}";
                $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
            }
            unset($rd);
            rs_close($rs);
        }
    }

    return $accessdata;
}

/**
 * Gets the accessdata for role "sitewide" 
 * (system down to course)
 *
 * @return array
 */
function get_default_frontpage_role_access($roleid, $accessdata=NULL) {

    global $CFG;
    
    $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
    $base = '/'. SYSCONTEXTID .'/'. $frontpagecontext->id;
 
    //
    // Overrides for the role in any contexts related to the course
    //
    $sql = "SELECT ctx.path,
                   rc.capability, rc.permission
            FROM {$CFG->prefix}context ctx
            JOIN {$CFG->prefix}role_capabilities rc
              ON rc.contextid=ctx.id
            WHERE rc.roleid = {$roleid}
                  AND (ctx.id = ".SYSCONTEXTID." OR ctx.path LIKE '$base/%')
                  AND ctx.contextlevel <= ".CONTEXT_COURSE."
            ORDER BY ctx.depth, ctx.path";             
            
    if ($rs = get_recordset_sql($sql)) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
        rs_close($rs);
    }

    return $accessdata;
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
 * This function returns whether the current user has the capability of performing a function
 * For example, we can do has_capability('mod/forum:replypost',$context) in forum
 * @param string $capability - name of the capability (or debugcache or clearcache)
 * @param object $context - a context object (record from context table)
 * @param integer $userid - a userid number, empty if current $USER
 * @param bool $doanything - if false, ignore do anything
 * @return bool
 */
function has_capability($capability, $context, $userid=NULL, $doanything=true) {
    global $USER, $ACCESS, $CFG, $DIRTYCONTEXTS;

    // the original $CONTEXT here was hiding serious errors
    // for security reasons do not reuse previous context
    if (empty($context)) {
        debugging('Incorrect context specified');
        return false;
    }

/// Some sanity checks
    if (debugging('',DEBUG_DEVELOPER)) {
        static $capsnames = null; // one request per page only
        
        if (is_null($capsnames)) {
            if ($caps = get_records('capabilities', '', '', '', 'id, name')) {
                $capsnames = array();
                foreach ($caps as $cap) {
                    $capsnames[$cap->name] = true;
                }
            }
        }
        if ($capsnames) { // ignore if can not fetch caps
            if (!isset($capsnames[$capability])) {
                debugging('Capability "'.$capability.'" was not found! This should be fixed in code.');
            }
        }
        if (!is_bool($doanything)) {
            debugging('Capability parameter "doanything" is wierd ("'.$doanything.'"). This should be fixed in code.');
        }
    }

    if (empty($userid)) { // we must accept null, 0, '0', '' etc. in $userid
        $userid = $USER->id;
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

    if (defined('FULLME') && FULLME === 'cron' && !isset($USER->access)) {
        // In cron, some modules setup a 'fake' $USER,
        // ensure we load the appropriate accessdata.
        if (isset($ACCESS[$userid])) {
            $DIRTYCONTEXTS = NULL; //load fresh dirty contexts
        } else {
            load_user_accessdata($userid);
            $DIRTYCONTEXTS = array();
        }
        $USER->access = $ACCESS[$userid];

    } else if ($USER->id == $userid && !isset($USER->access)) {
        // caps not loaded yet - better to load them to keep BC with 1.8
        // not-logged-in user or $USER object set up manually first time here
        load_all_capabilities();
        $ACCESS = array(); // reset the cache for other users too, the dirty contexts are empty now
        $RDEFS = array();
    }

    // Load dirty contexts list if needed
    if (!isset($DIRTYCONTEXTS)) {
        if (isset($USER->access['time'])) {
            $DIRTYCONTEXTS = get_dirty_contexts($USER->access['time']);
        }
        else {
            $DIRTYCONTEXTS = array();
        }
    }

    // Careful check for staleness...
    if (count($DIRTYCONTEXTS) !== 0 and is_contextpath_dirty($contexts, $DIRTYCONTEXTS)) {
        // reload all capabilities - preserving loginas, roleswitches, etc
        // and then cleanup any marks of dirtyness... at least from our short
        // term memory! :-)
        $ACCESS = array();
        $RDEFS = array();

        if (defined('FULLME') && FULLME === 'cron') {
            load_user_accessdata($userid);
            $USER->access = $ACCESS[$userid];
            $DIRTYCONTEXTS = array();

        } else {
            reload_all_capabilities();
        }
    }

    // divulge how many times we are called
    //// error_log("has_capability: id:{$context->id} path:{$context->path} userid:$userid cap:$capability");

    if ($USER->id == $userid) { // we must accept strings and integers in $userid
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

    if (!isset($ACCESS[$userid])) {
        load_user_accessdata($userid);
    }
    if ($context->contextlevel <= CONTEXT_COURSE) {
        // Course and above are always preloaded
        return has_capability_in_accessdata($capability, $context, $ACCESS[$userid], $doanything);
    }
    // Load accessdata for below-the-course contexts as needed
    if (!path_inaccessdata($context->path, $ACCESS[$userid])) {
        // error_log("loading access for context {$context->path} for $capability at {$context->contextlevel} {$context->id}");
        // $bt = debug_backtrace();
        // error_log("bt {$bt[0]['file']} {$bt[0]['line']}");
        load_subcontext($userid, $context, $ACCESS[$userid]);
    }
    return has_capability_in_accessdata($capability, $context, $ACCESS[$userid], $doanything);
}

/**
 * This function returns whether the current user has any of the capabilities in the
 * $capabilities array. This is a simple wrapper around has_capability for convinience.
 *
 * There are probably tricks that could be done to improve the performance here, for example,
 * check the capabilities that are already cached first.
 *
 * @param array $capabilities - an array of capability names.
 * @param object $context - a context object (record from context table)
 * @param integer $userid - a userid number, empty if current $USER
 * @param bool $doanything - if false, ignore do anything
 * @return bool
 */
function has_any_capability($capabilities, $context, $userid=NULL, $doanything=true) {
    foreach ($capabilities as $capability) {
        if (has_capability($capability, $context, $userid, $doanything)) {
            return true;
        }
    }
    return false;
}

/**
 * Uses 1 DB query to answer whether a user is an admin at the sitelevel.
 * It depends on DB schema >=1.7 but does not depend on the new datastructures
 * in v1.9 (context.path, or $USER->access)
 *
 * Will return true if the userid has any of
 *  - moodle/site:config
 *  - moodle/legacy:admin
 *  - moodle/site:doanything
 *
 * @param   int  $userid
 * @returns bool $isadmin
 */
function is_siteadmin($userid) {
    global $CFG;

    $sql = "SELECT SUM(rc.permission)
            FROM " . $CFG->prefix . "role_capabilities rc
            JOIN " . $CFG->prefix . "context ctx 
              ON ctx.id=rc.contextid
            JOIN " . $CFG->prefix . "role_assignments ra
              ON ra.roleid=rc.roleid AND ra.contextid=ctx.id
            WHERE ctx.contextlevel=10
              AND ra.userid={$userid}
              AND rc.capability IN ('moodle/site:config', 'moodle/legacy:admin', 'moodle/site:doanything')       
            GROUP BY rc.capability
            HAVING SUM(rc.permission) > 0";

    $isadmin = record_exists_sql($sql);
    return $isadmin;
}

function get_course_from_path ($path) {
    // assume that nothing is more than 1 course deep
    if (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        return $matches[1];
    }
    return false;
}

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
 *
 * To Do:
 *
 * - Document how it works
 * - Rewrite in ASM :-)
 *
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
                        // but maybe the user candoanything in this context...
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
            // but maybe the user candoanything in this context...
            return has_capability_in_accessdata('moodle/site:doanything', $context, $accessdata, false);
        } else {
            return false;
        }
    } else {
        return true;
    }

}

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
 * This is an easy to use function, combining has_capability() with require_course_login().
 * And will call those where needed.
 * 
 * It checks for a capability assertion being true.  If it isn't
 * then the page is terminated neatly with a standard error message.
 *
 * If the user is not logged in, or is using 'guest' access or other special "users,
 * it provides a logon prompt.
 *
 * @param string $capability - name of the capability
 * @param object $context - a context object (record from context table)
 * @param integer $userid - a userid number
 * @param bool $doanything - if false, ignore do anything
 * @param string $errorstring - an errorstring
 * @param string $stringfile - which stringfile to get it from
 */
function require_capability($capability, $context, $userid=NULL, $doanything=true,
                            $errormessage='nopermissions', $stringfile='') {

    global $USER, $CFG;

    /* Empty $userid means current user, if the current user is not logged in,
     * then make sure they are (if needed).
     * Originally there was a check for loaded permissions - it is not needed here.
     * Context is now required parameter, the cached $CONTEXT was only hiding errors.
     */
    $errorlink = '';

    if (empty($userid)) {
        if ($context->contextlevel == CONTEXT_COURSE) {
            require_login($context->instanceid);

        } else if ($context->contextlevel == CONTEXT_MODULE) {
            if (!$cm = get_record('course_modules', 'id', $context->instanceid)) {
                error('Incorrect module');
            }
            if (!$course = get_record('course', 'id', $cm->course)) {
                error('Incorrect course.');
            }
            require_course_login($course, true, $cm);
            $errorlink = $CFG->wwwroot.'/course/view.php?id='.$cm->course;

        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
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
        print_error($errormessage, $stringfile, $errorlink, $capabilityname);
    }
}

/**
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
 * @param string $capability - name of the capability
 * @param array  $accessdata - accessdata session array
 * @param bool   $doanything - if false, ignore do anything
 * @param string $sort - sorting fields - prefix each fieldname with "c."
 * @param array  $fields - additional fields you are interested in...
 * @param int    $limit  - set if you want to limit the number of courses
 * @return array $courses - ordered array of course objects - see notes above
 *
 */
function get_user_courses_bycap($userid, $cap, $accessdata, $doanything, $sort='c.sortorder ASC', $fields=NULL, $limit=0) {

    global $CFG;

    // Slim base fields, let callers ask for what they need...
    $basefields = array('id', 'sortorder', 'shortname', 'idnumber');

    if (!is_null($fields)) {
        $fields = array_merge($basefields, $fields);
        $fields = array_unique($fields);
    } else {
        $fields = $basefields;
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
                FROM {$CFG->prefix}course c
                JOIN {$CFG->prefix}course_categories cc
                  ON c.category=cc.id
                JOIN {$CFG->prefix}context ctx 
                  ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                $sort ";
        $rs = get_recordset_sql($sql);
    } else {
        //
        // narrow down where we have the caps to a few contexts
        // this will be a combination of
        // - categories where we have the rights
        // - courses    where we have an explicit enrolment OR that have an override
        // 
        $sql = "SELECT ctx.*
                FROM   {$CFG->prefix}context ctx
                WHERE  ctx.contextlevel=".CONTEXT_COURSECAT."
                ORDER BY ctx.depth";
        $rs = get_recordset_sql($sql);
        $catpaths = array();
        while ($catctx = rs_fetch_next_record($rs)) {
            if ($catctx->path != '' 
                && has_capability_in_accessdata($cap, $catctx, $accessdata, $doanything)) {
                $catpaths[] = $catctx->path;
            }
        }
        rs_close($rs);
        $catclause = '';
        if (count($catpaths)) {
            $cc = count($catpaths);
            for ($n=0;$n<$cc;$n++) {
                $catpaths[$n] = "ctx.path LIKE '{$catpaths[$n]}/%'";
            }
            $catclause = 'OR (' . implode(' OR ', $catpaths) .')';
        }
        unset($catpaths);

        $capany = '';
        if ($doanything) {
            $capany = " OR rc.capability='moodle/site:doanything'";
        }
        //
        // Note here that we *have* to have the compound clauses
        // in the LEFT OUTER JOIN condition for them to return NULL
        // appropriately and narrow things down...
        //
        $sql = "SELECT $coursefields,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel,
                       cc.path AS categorypath
                FROM {$CFG->prefix}course c
                JOIN {$CFG->prefix}course_categories cc
                  ON c.category=cc.id
                JOIN {$CFG->prefix}context ctx 
                  ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                LEFT OUTER JOIN {$CFG->prefix}role_assignments ra
                  ON (ra.contextid=ctx.id AND ra.userid=$userid)
                LEFT OUTER JOIN {$CFG->prefix}role_capabilities rc
                  ON (rc.contextid=ctx.id AND (rc.capability='$cap' $capany))
                WHERE    ra.id IS NOT NULL
                      OR rc.id IS NOT NULL
                      $catclause
                $sort ";
        $rs = get_recordset_sql($sql);
    }
    $courses = array();
    $cc = 0; // keep count
    while ($c = rs_fetch_next_record($rs)) {
        // build the context obj
        $c = make_context_subobj($c);

        if (has_capability_in_accessdata($cap, $c->context, $accessdata, $doanything)) {
            $courses[] = $c;
            if ($limit > 0 && $cc++ > $limit) {
                break;
            }
        }
    }
    rs_close($rs);
    return $courses;
}


/**
 * It will return a nested array showing role assignments
 * all relevant role capabilities for the user at
 * site/metacourse/course_category/course levels
 *
 * We do _not_ delve deeper than courses because the number of
 * overrides at the module/block levels is HUGE.
 *
 * [ra]   => [/path/] = array(roleid, roleid)
 * [rdef] => [/path/:roleid][capability]=permission
 * [loaded] => array('/path', '/path')
 *
 * @param $userid integer - the id of the user
 *
 */
function get_user_access_sitewide($userid) {

    global $CFG;

    // this flag has not been set!
    // (not clean install, or upgraded successfully to 1.7 and up)
    if (empty($CFG->rolesactive)) {
        return false;
    }

    /* Get in 3 cheap DB queries...
     * - role assignments - with role_caps
     * - relevant role caps
     *   - above this user's RAs
     *   - below this user's RAs - limited to course level
     */

    $accessdata           = array(); // named list
    $accessdata['ra']     = array();
    $accessdata['rdef']   = array();
    $accessdata['loaded'] = array();

    $sitectx = get_system_context();
    $base = '/'.$sitectx->id;

    //
    // Role assignments - and any rolecaps directly linked
    // because it's cheap to read rolecaps here over many
    // RAs
    //
    $sql = "SELECT ctx.path, ra.roleid, rc.capability, rc.permission
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}context ctx
               ON ra.contextid=ctx.id
            LEFT OUTER JOIN {$CFG->prefix}role_capabilities rc
               ON (rc.roleid=ra.roleid AND rc.contextid=ra.contextid)
            WHERE ra.userid = $userid AND ctx.contextlevel <= ".CONTEXT_COURSE."
            ORDER BY ctx.depth, ctx.path";
    $rs = get_recordset_sql($sql);
    //
    // raparents collects paths & roles we need to walk up
    // the parenthood to build the rdef
    //
    // the array will bulk up a bit with dups
    // which we'll later clear up
    //
    $raparents = array();
    $lastseen  = '';
    if ($rs) {
        while ($ra = rs_fetch_next_record($rs)) {
            // RAs leafs are arrays to support multi
            // role assignments...
            if (!isset($accessdata['ra'][$ra->path])) {
                $accessdata['ra'][$ra->path] = array();
            }
            // only add if is not a repeat caused
            // by capability join...
            // (this check is cheaper than in_array())
            if ($lastseen !== $ra->path.':'.$ra->roleid) {
                $lastseen = $ra->path.':'.$ra->roleid;
                array_push($accessdata['ra'][$ra->path], $ra->roleid);
                $parentids = explode('/', $ra->path);
                array_shift($parentids); // drop empty leading "context"
                array_pop($parentids);   // drop _this_ context

                if (isset($raparents[$ra->roleid])) {
                    $raparents[$ra->roleid] = array_merge($raparents[$ra->roleid],
                                                          $parentids);
                } else {
                    $raparents[$ra->roleid] = $parentids;
                }
            }
            // Always add the roleded
            if (!empty($ra->capability)) {
                $k = "{$ra->path}:{$ra->roleid}";
                $accessdata['rdef'][$k][$ra->capability] = $ra->permission;
            }
        }
        unset($ra);
        rs_close($rs);
    }

    // Walk up the tree to grab all the roledefs
    // of interest to our user...
    // NOTE: we use a series of IN clauses here - which
    // might explode on huge sites with very convoluted nesting of
    // categories... - extremely unlikely that the number of categories
    // and roletypes is so large that we hit the limits of IN()
    $clauses = array();
    foreach ($raparents as $roleid=>$contexts) {
        $contexts = implode(',', array_unique($contexts));
        if ($contexts ==! '') {
            $clauses[] = "(roleid=$roleid AND contextid IN ($contexts))";
        }
    }
    $clauses = implode(" OR ", $clauses);
    if ($clauses !== '') {
        $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
                FROM {$CFG->prefix}role_capabilities rc
                JOIN {$CFG->prefix}context ctx
                  ON rc.contextid=ctx.id
                WHERE $clauses
                ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";

        $rs = get_recordset_sql($sql);
        unset($clauses);

        if ($rs) {
            while ($rd = rs_fetch_next_record($rs)) {
                $k = "{$rd->path}:{$rd->roleid}";
                $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
            }
            unset($rd);
            rs_close($rs);
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
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}context ctx
              ON ra.contextid=ctx.id
            JOIN {$CFG->prefix}context sctx
              ON (sctx.path LIKE " . sql_concat('ctx.path',"'/%'"). " )
            JOIN {$CFG->prefix}role_capabilities rco
              ON (rco.roleid=ra.roleid AND rco.contextid=sctx.id)
            WHERE ra.userid = $userid
                  AND sctx.contextlevel <= ".CONTEXT_COURSE."
            ORDER BY sctx.depth, sctx.path, ra.roleid";

    $rs = get_recordset_sql($sql);
    if ($rs) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$rd->roleid}";
            $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
        rs_close($rs);
    }
    return $accessdata;
}

/**
 * It add to the access ctrl array the data
 * needed by a user for a given context
 *
 * @param $userid  integer - the id of the user
 * @param $context context obj - needs path!
 * @param $accessdata array  accessdata array
 */
function load_subcontext($userid, $context, &$accessdata) {

    global $CFG;



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
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}context ctx
               ON ra.contextid=ctx.id
            WHERE ra.userid = $userid
                  AND (ctx.path = '{$context->path}' OR ctx.path LIKE '{$context->path}/%')
            ORDER BY ctx.depth, ctx.path";
    $rs = get_recordset_sql($sql);

    // 
    // Read in the RAs
    //
    $localroles = array();
    while ($ra = rs_fetch_next_record($rs)) {
        if (!isset($accessdata['ra'][$ra->path])) {
            $accessdata['ra'][$ra->path] = array();
        }
        array_push($accessdata['ra'][$ra->path], $ra->roleid);
        array_push($localroles,           $ra->roleid);
    }
    rs_close($rs);

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
            FROM {$CFG->prefix}role_capabilities rc
            JOIN {$CFG->prefix}context ctx
             ON rc.contextid=ctx.id
            WHERE ($whereroles
                    (ctx.id={$context->id} OR ctx.path LIKE '{$context->path}/%'))
                    $wherelocalroles
            ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";

    $newrdefs = array();
    if ($rs = get_recordset_sql($sql)) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$rd->roleid}";
            if (!array_key_exists($k, $newrdefs)) {
                $newrdefs[$k] = array();
            }
            $newrdefs[$k][$rd->capability] = $rd->permission;
        }
        rs_close($rs);
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
 * It add to the access ctrl array the data
 * needed by a role for a given context.
 *
 * The data is added in the rdef key.
 *
 * This role-centric function is useful for role_switching
 * and to get an overview of what a role gets under a
 * given context and below...
 *
 * @param $roleid  integer - the id of the user
 * @param $context context obj - needs path!
 * @param $accessdata      accessdata array
 *
 */
function get_role_access_bycontext($roleid, $context, $accessdata=NULL) {

    global $CFG;

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
            FROM {$CFG->prefix}role_capabilities rc
            JOIN {$CFG->prefix}context ctx
              ON rc.contextid=ctx.id
            WHERE rc.roleid=$roleid AND
                  ( ctx.id IN ($contexts) OR 
                    ctx.path LIKE '{$context->path}/%' )
            ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";

    $rs = get_recordset_sql($sql);
    while ($rd = rs_fetch_next_record($rs)) {
        $k = "{$rd->path}:{$roleid}";
        $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
    }
    rs_close($rs);

    return $accessdata;
}

/**
 * Load accessdata for a user
 * into the $ACCESS global
 *
 * Used by has_capability() - but feel free
 * to call it if you are about to run a BIG 
 * cron run across a bazillion users.
 *
 */ 
function load_user_accessdata($userid) {
    global $ACCESS,$CFG;

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

    $ACCESS[$userid] = $accessdata;
    compact_rdefs($ACCESS[$userid]['rdef']);

    return true;
}

/**
 * Use shared copy of role definistions stored in $RDEFS;
 * @param array $rdefs array of role definitions in contexts
 */
function compact_rdefs(&$rdefs) {
    global $RDEFS;

    /*
     * This is a basic sharing only, we could also
     * use md5 sums of values. The main purpose is to
     * reduce mem in cron jobs - many users in $ACCESS array.
     */

    foreach ($rdefs as $key => $value) {
        if (!array_key_exists($key, $RDEFS)) {
            $RDEFS[$key] = $rdefs[$key];
        }
        $rdefs[$key] =& $RDEFS[$key];
    }
}

/**
 *  A convenience function to completely load all the capabilities 
 *  for the current user.   This is what gets called from complete_user_login()
 *  for example. Call it only _after_ you've setup $USER and called
 *  check_enrolment_plugins();
 *
 */
function load_all_capabilities() {
    global $USER, $CFG, $DIRTYCONTEXTS;

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
    $DIRTYCONTEXTS = array();

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
 */
function reload_all_capabilities() {
    global $USER,$CFG;

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
        $context = get_record('context', 'path', $path);
        role_switch($roleid, $context);
    }

}

/*
 * Adds a temp role to an accessdata array.
 *
 * Useful for the "temporary guest" access
 * we grant to logged-in users.
 *
 * Note - assumes a course context!
 *
 */
function load_temp_role($context, $roleid, $accessdata) {

    global $CFG;

    //
    // Load rdefs for the role in -
    // - this context
    // - all the parents
    // - and below - IOWs overrides...
    //
    
    // turn the path into a list of context ids
    $contexts = substr($context->path, 1); // kill leading slash
    $contexts = str_replace('/', ',', $contexts);

    $sql = "SELECT ctx.path,
                   rc.capability, rc.permission
            FROM {$CFG->prefix}context ctx
            JOIN {$CFG->prefix}role_capabilities rc
              ON rc.contextid=ctx.id
            WHERE (ctx.id IN ($contexts)
                   OR ctx.path LIKE '{$context->path}/%')
                  AND rc.roleid = {$roleid}
            ORDER BY ctx.depth, ctx.path";
    $rs = get_recordset_sql($sql);
    while ($rd = rs_fetch_next_record($rs)) {
        $k = "{$rd->path}:{$roleid}";
        $accessdata['rdef'][$k][$rd->capability] = $rd->permission;
    }
    rs_close($rs);

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
                    role_assign($editteacherrole, $teacher->userid, 0, $coursecontext->id, $teacher->timestart, $teacher->timeend, $hiddenteacher, $teacher->enrol, $teacher->timemodified);
                } else {
                    role_assign($noneditteacherrole, $teacher->userid, 0, $coursecontext->id, $teacher->timestart, $teacher->timeend, $hiddenteacher, $teacher->enrol, $teacher->timemodified);
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
                role_assign($studentrole, $student->userid, 0, $coursecontext->id, $student->timestart, $student->timeend, 0, $student->enrol, $student->time);
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

    $tables = array('user_students', 'user_teachers', 'user_coursecreators', 'user_admins');  
    foreach ($tables as $tablename) {
        $table = new XMLDBTable($tablename);
        if (table_exists($table)) {
            drop_table($table);
        }
    }
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
 * assumes that the caller has done the homework.
 *
 * @param $level
 * @param $instanceid
 *
 * @return object newly created context
 */
function create_context($contextlevel, $instanceid) {

    global $CFG;

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

    switch ($contextlevel) {
        case CONTEXT_COURSECAT:
            $sql = "SELECT ctx.path, ctx.depth 
                    FROM {$CFG->prefix}context           ctx
                    JOIN {$CFG->prefix}course_categories cc
                      ON (cc.parent=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT.")
                    WHERE cc.id={$instanceid}";
            if ($p = get_record_sql($sql)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($category = get_record('course_categories', 'id', $instanceid)) {
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
                $result = false;
            }
            break;

        case CONTEXT_COURSE:
            $sql = "SELECT ctx.path, ctx.depth
                    FROM {$CFG->prefix}context           ctx
                    JOIN {$CFG->prefix}course            c
                      ON (c.category=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT.")
                    WHERE c.id={$instanceid} AND c.id !=" . SITEID;
            if ($p = get_record_sql($sql)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($course = get_record('course', 'id', $instanceid)) {
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
                $result = false;
            }
            break;

        case CONTEXT_MODULE:
            $sql = "SELECT ctx.path, ctx.depth
                    FROM {$CFG->prefix}context           ctx
                    JOIN {$CFG->prefix}course_modules    cm
                      ON (cm.course=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    WHERE cm.id={$instanceid}";
            if ($p = get_record_sql($sql)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($cm = get_record('course_modules', 'id', $instanceid)) {
                if ($parent = get_context_instance(CONTEXT_COURSE, $cm->course)) {
                    $basepath  = $parent->path;
                    $basedepth = $parent->depth;
                } else {
                    // course does not exist - modules can not exist without a course
                    $result = false;
                }
            } else {
                // cm does not exist
                $result = false;
            }
            break;

        case CONTEXT_BLOCK:
            // Only non-pinned & course-page based
            $sql = "SELECT ctx.path, ctx.depth
                    FROM {$CFG->prefix}context           ctx
                    JOIN {$CFG->prefix}block_instance    bi
                      ON (bi.pageid=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    WHERE bi.id={$instanceid} AND bi.pagetype='course-view'";
            if ($p = get_record_sql($sql)) {
                $basepath  = $p->path;
                $basedepth = $p->depth;
            } else if ($bi = get_record('block_instance', 'id', $instanceid)) {
                if ($bi->pagetype != 'course-view') {
                    // ok - not a course block
                } else if ($parent = get_context_instance(CONTEXT_COURSE, $bi->pageid)) {
                    $basepath  = $parent->path;
                    $basedepth = $parent->depth;
                } else {
                    // parent course does not exist - course blocks can not exist without a course
                    $result = false;
                }
            } else {
                // block does not exist
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

    if ($result and $id = insert_record('context', $context)) {
        // can't set the full path till we know the id!
        if ($basedepth != 0 and !empty($basepath)) {
            set_field('context', 'path', $basepath.'/'. $id, 'id', $id);
        }
        return get_context_instance_by_id($id);

    } else {
        debugging('Error: could not insert new context level "'.
                  s($contextlevel).'", instance "'.
                  s($instanceid).'".');
        return false;
    }
}

/**
 * This hacky function is needed because we can not change system context instanceid using normal upgrade routine.
 */
function get_system_context($cache=true) {
    static $cached = null;
    if ($cache and defined('SYSCONTEXTID')) {
        if (is_null($cached)) {
            $cached = new object();
            $cached->id           = SYSCONTEXTID;
            $cached->contextlevel = CONTEXT_SYSTEM;
            $cached->instanceid   = 0;
            $cached->path         = '/'.SYSCONTEXTID;
            $cached->depth        = 1;
        }
        return $cached;
    }

    if (!$context = get_record('context', 'contextlevel', CONTEXT_SYSTEM)) {
        $context = new object();
        $context->contextlevel = CONTEXT_SYSTEM;
        $context->instanceid   = 0;
        $context->depth        = 1;
        $context->path         = NULL; //not known before insert

        if (!$context->id = insert_record('context', $context)) {
            // better something than nothing - let's hope it will work somehow
            if (!defined('SYSCONTEXTID')) {
                define('SYSCONTEXTID', 1);
            }
            debugging('Can not create system context');
            $context->id   = SYSCONTEXTID;
            $context->path = '/'.SYSCONTEXTID;
            return $context;
        }
    }

    if (!isset($context->depth) or $context->depth != 1 or $context->instanceid != 0 or $context->path != '/'.$context->id) {
        $context->instanceid   = 0;
        $context->path         = '/'.$context->id;
        $context->depth        = 1;
        update_record('context', $context);
    }

    if (!defined('SYSCONTEXTID')) {
        define('SYSCONTEXTID', $context->id);
    }

    $cached = $context;
    return $cached;
}

/**
 * Remove a context record and any dependent entries,
 * removes context from static context cache too
 * @param $level
 * @param $instanceid
 *
 * @return bool properly deleted
 */
function delete_context($contextlevel, $instanceid) {
    global $context_cache, $context_cache_id;

    // do not use get_context_instance(), because the related object might not exist,
    // or the context does not exist yet and it would be created now
    if ($context = get_record('context', 'contextlevel', $contextlevel, 'instanceid', $instanceid)) {
        $result = delete_records('role_assignments', 'contextid', $context->id) &&
                  delete_records('role_capabilities', 'contextid', $context->id) &&
                  delete_records('context', 'id', $context->id);

        // do not mark dirty contexts if parents unknown
        if (!is_null($context->path) and $context->depth > 0) {
            mark_context_dirty($context->path);
        }

        // purge static context cache if entry present
        unset($context_cache[$contextlevel][$instanceid]);
        unset($context_cache_id[$context->id]);

        return $result;
    } else {

        return true;
    }
}

/**
 * Precreates all contexts including all parents
 * @param int $contextlevel, empty means all
 * @param bool $buildpaths update paths and depths
 * @param bool $feedback show sql feedback
 * @return void
 */
function create_contexts($contextlevel=null, $buildpaths=true, $feedback=false) {
    global $CFG;

    //make sure system context exists
    $syscontext = get_system_context(false);

    if (empty($contextlevel) or $contextlevel == CONTEXT_COURSECAT
                             or $contextlevel == CONTEXT_COURSE
                             or $contextlevel == CONTEXT_MODULE
                             or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {$CFG->prefix}context (contextlevel, instanceid)
                SELECT ".CONTEXT_COURSECAT.", cc.id
                  FROM  {$CFG->prefix}course_categories cc
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {$CFG->prefix}context cx
                                    WHERE cc.id = cx.instanceid AND cx.contextlevel=".CONTEXT_COURSECAT.")";
        execute_sql($sql, $feedback);

    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_COURSE
                             or $contextlevel == CONTEXT_MODULE
                             or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {$CFG->prefix}context (contextlevel, instanceid)
                SELECT ".CONTEXT_COURSE.", c.id
                  FROM  {$CFG->prefix}course c
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {$CFG->prefix}context cx
                                    WHERE c.id = cx.instanceid AND cx.contextlevel=".CONTEXT_COURSE.")";
        execute_sql($sql, $feedback);

    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_MODULE) {
        $sql = "INSERT INTO {$CFG->prefix}context (contextlevel, instanceid)
                SELECT ".CONTEXT_MODULE.", cm.id
                  FROM  {$CFG->prefix}course_modules cm
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {$CFG->prefix}context cx
                                    WHERE cm.id = cx.instanceid AND cx.contextlevel=".CONTEXT_MODULE.")";
        execute_sql($sql, $feedback);
    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_BLOCK) {
        $sql = "INSERT INTO {$CFG->prefix}context (contextlevel, instanceid)
                SELECT ".CONTEXT_BLOCK.", bi.id
                  FROM  {$CFG->prefix}block_instance bi
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {$CFG->prefix}context cx
                                    WHERE bi.id = cx.instanceid AND cx.contextlevel=".CONTEXT_BLOCK.")";
        execute_sql($sql, $feedback);
    }

    if (empty($contextlevel) or $contextlevel == CONTEXT_USER) {
        $sql = "INSERT INTO {$CFG->prefix}context (contextlevel, instanceid)
                SELECT ".CONTEXT_USER.", u.id
                  FROM  {$CFG->prefix}user u
                 WHERE u.deleted=0
                   AND NOT EXISTS (SELECT 'x'
                                     FROM {$CFG->prefix}context cx
                                    WHERE u.id = cx.instanceid AND cx.contextlevel=".CONTEXT_USER.")";
        execute_sql($sql, $feedback);

    }

    if ($buildpaths) {
        build_context_path(false, $feedback);
    }
}

/**
 * Remove stale context records
 *
 * @return bool
 */
function cleanup_contexts() {
    global $CFG;

    $sql = "  SELECT c.contextlevel,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}course_categories t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_COURSECAT . "
            UNION
              SELECT c.contextlevel,
                     c.instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}course t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_COURSE . "
            UNION
              SELECT c.contextlevel,
                     c.instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}course_modules t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_MODULE . "
            UNION
              SELECT c.contextlevel,
                     c.instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}user t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_USER . "
            UNION
              SELECT c.contextlevel,
                     c.instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}block_instance t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_BLOCK . "
            UNION
              SELECT c.contextlevel,
                     c.instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}groups t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_GROUP . "
           ";
    if ($rs = get_recordset_sql($sql)) {
        begin_sql();
        $tx = true;
        while ($tx && $ctx = rs_fetch_next_record($rs)) {
            $tx = $tx && delete_context($ctx->contextlevel, $ctx->instanceid);
        }
        rs_close($rs);
        if ($tx) {
            commit_sql();
            return true;
        }
        rollback_sql();
        return false;
        rs_close($rs);
    }
    return true;
}

/**
 * Get the context instance as an object. This function will create the
 * context instance if it does not exist yet.
 * @param integer $level The context level, for example CONTEXT_COURSE, or CONTEXT_MODULE.
 * @param integer $instance The instance id. For $level = CONTEXT_COURSE, this would be $course->id,
 *      for $level = CONTEXT_MODULE, this would be $cm->id. And so on.
 * @return object The context object.
 */
function get_context_instance($contextlevel, $instance=0) {

    global $context_cache, $context_cache_id, $CFG;
    static $allowed_contexts = array(CONTEXT_SYSTEM, CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_GROUP, CONTEXT_MODULE, CONTEXT_BLOCK);

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
        error('Error: get_context_instance() called with incorrect context level "'.s($contextlevel).'"');
    }

    if (!is_array($instance)) {
    /// Check the cache
        if (isset($context_cache[$contextlevel][$instance])) {  // Already cached
            return $context_cache[$contextlevel][$instance];
        }

    /// Get it from the database, or create it
        if (!$context = get_record('context', 'contextlevel', $contextlevel, 'instanceid', $instance)) {
            $context = create_context($contextlevel, $instance);
        }

    /// Only add to cache if context isn't empty.
        if (!empty($context)) {
            $context_cache[$contextlevel][$instance] = $context;    // Cache it for later
            $context_cache_id[$context->id]          = $context;    // Cache it for later
        }

        return $context;
    }


/// ok, somebody wants to load several contexts to save some db queries ;-)
    $instances = $instance;
    $result = array();

    foreach ($instances as $key=>$instance) {
    /// Check the cache first
        if (isset($context_cache[$contextlevel][$instance])) {  // Already cached
            $result[$instance] = $context_cache[$contextlevel][$instance];
            unset($instances[$key]);
            continue;
        }
    }

    if ($instances) {
        if (count($instances) > 1) {
            $instanceids = implode(',', $instances);
            $instanceids = "instanceid IN ($instanceids)";
        } else {
            $instance = reset($instances);
            $instanceids = "instanceid = $instance";
        }
        
        if (!$contexts = get_records_sql("SELECT instanceid, id, contextlevel, path, depth
                                            FROM {$CFG->prefix}context
                                           WHERE contextlevel=$contextlevel AND $instanceids")) {
            $contexts = array();
        }

        foreach ($instances as $instance) {
            if (isset($contexts[$instance])) {
                $context = $contexts[$instance];
            } else {
                $context = create_context($contextlevel, $instance);
            }

            if (!empty($context)) {
                $context_cache[$contextlevel][$instance] = $context;    // Cache it for later
                $context_cache_id[$context->id] = $context;             // Cache it for later
            }

            $result[$instance] = $context;
        }
    }

    return $result;
}


/**
 * Get a context instance as an object, from a given context id.
 * @param mixed $id a context id or array of ids.
 * @return mixed object or array of the context object.
 */
function get_context_instance_by_id($id) {

    global $context_cache, $context_cache_id;

    if ($id == SYSCONTEXTID) {
        return get_system_context();
    }

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
    global $CFG;
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

        // MDL-10679 find all contexts where this role has an override
        $contexts = get_records_sql("SELECT contextid, contextid
                                     FROM {$CFG->prefix}role_capabilities
                                     WHERE roleid = $roleid");

        delete_records('role_capabilities', 'roleid', $roleid);

        delete_records('role_allow_assign', 'roleid', $roleid);
        delete_records('role_allow_assign', 'allowassign', $roleid);
        delete_records('role_allow_override', 'roleid', $roleid);
        delete_records('role_allow_override', 'allowoverride', $roleid);
        delete_records('role_names', 'roleid', $roleid);
    }

// finally delete the role itself
    // get this before the name is gone for logging
    $rolename = get_field('role', 'name', 'id', $roleid);
    
    if ($success and !delete_records('role', 'id', $roleid)) {
        debugging("Could not delete role record with ID $roleid!");
        $success = false;
    }
    
    if ($success) {
        add_to_log(SITEID, 'role', 'delete', 'admin/roles/action=delete&roleid='.$roleid, $rolename, '', $USER->id);
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
        $c = get_record('context', 'id', $contextid);
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
        // delete from context rel, if this is the last override in this context
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

        if (!$ra->id = insert_record('role_assignments', $ra)) {
            return false;
        }

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

        if (!update_record('role_assignments', $ra)) {
            return false;
        }
    }

/// mark context as dirty - modules might use has_capability() in xxx_role_assing()
/// again expensive, but needed
    mark_context_dirty($context->path);

    if (!empty($USER->id) && $USER->id == $userid) {
/// If the user is the current user, then do full reload of capabilities too.
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

    /// now handle metacourse role assignments if in course context
    if ($context->contextlevel == CONTEXT_COURSE) {
        if ($parents = get_records('course_meta', 'child_course', $context->instanceid)) {
            foreach ($parents as $parent) {
                sync_metacourse($parent->parent_course);
            }
        }
    }

    events_trigger('role_assigned', $ra);

    return true;
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
    require_once($CFG->dirroot.'/group/lib.php');

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
                $fireevent = false;
                /// infinite loop protection when deleting recursively
                if (!$ra = get_record('role_assignments', 'id', $ra->id)) {
                    continue;
                }
                if (delete_records('role_assignments', 'id', $ra->id)) {
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
                foreach ($mods as $mod) {
                    include_once($CFG->dirroot.'/mod/'.$mod.'/lib.php');
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
                        delete_records('user_lastaccess', 'userid', $ra->userid, 'courseid', $context->instanceid);
                    }

                    //unassign roles in metacourses if needed
                    if ($parents = get_records('course_meta', 'child_course', $context->instanceid)) {
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
 * A convenience function to take care of the common case where you
 * just want to enrol someone using the default role into a course
 *
 * @param object $course
 * @param object $user
 * @param string $enrol - the plugin used to do this enrolment
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

        } else if ($compparts[0] == 'gradeimport') {
            $defpath = $CFG->dirroot.'/grade/import/'.$compparts[1].'/db/access.php';
            $varprefix = $compparts[0].'_'.$compparts[1];

        } else if ($compparts[0] == 'gradeexport') {
            $defpath = $CFG->dirroot.'/grade/export/'.$compparts[1].'/db/access.php';
            $varprefix = $compparts[0].'_'.$compparts[1];

        } else if ($compparts[0] == 'gradereport') {
            $defpath = $CFG->dirroot.'/grade/report/'.$compparts[1].'/db/access.php';
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
    } else if ($component == 'local') {
        $storedcaps = get_records_select('capabilities', 
                        "name LIKE 'moodle/local:%'");
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


        if (isset($capdef['clonepermissionsfrom']) && in_array($capdef['clonepermissionsfrom'], $storedcaps)){
            if ($rolecapabilities = get_records('role_capabilities', 'capability', $capdef['clonepermissionsfrom'])){
                foreach ($rolecapabilities as $rolecapability){
                    //assign_capability will update rather than insert if capability exists
                    if (!assign_capability($capname, $rolecapability->permission,
                                            $rolecapability->roleid, $rolecapability->contextid, true)){
                         notify('Could not clone capabilities for '.$capname);
                    }
                }
            }
        // Do we need to assign the new capabilities to roles that have the
        // legacy capabilities moodle/legacy:* as well?
        // we ignore legacy key if we have cloned permissions
        } else if (isset($capdef['legacy']) && is_array($capdef['legacy']) &&
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
function print_context_name($context, $withprefix = true, $short = false) {

    $name = '';
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM: // by now it's a definite an inherit
            $name = get_string('coresystem');
            break;

        case CONTEXT_USER:
            if ($user = get_record('user', 'id', $context->instanceid)) {
                if ($withprefix){
                    $name = get_string('user').': ';
                }
                $name .= fullname($user);
            }
            break;

        case CONTEXT_COURSECAT: // Coursecat -> coursecat or site
            if ($category = get_record('course_categories', 'id', $context->instanceid)) {
                if ($withprefix){
                    $name = get_string('category').': ';
                }
                $name .=format_string($category->name);
            }
            break;

        case CONTEXT_COURSE: // 1 to 1 to course cat
            if ($course = get_record('course', 'id', $context->instanceid)) {
                if ($withprefix){
                    if ($context->instanceid == SITEID) {
                        $name = get_string('site').': ';
                    } else {
                        $name = get_string('course').': ';
                    }
                }
                if ($short){
                    $name .=format_string($course->shortname);
                } else {
                    $name .=format_string($course->fullname);
               }

            }
            break;

        case CONTEXT_GROUP: // 1 to 1 to course
            if ($name = groups_get_group_name($context->instanceid)) {
                if ($withprefix){
                    $name = get_string('group').': '. $name;
                }
            }
            break;

        case CONTEXT_MODULE: // 1 to 1 to course
            if ($cm = get_record('course_modules','id',$context->instanceid)) {
                if ($module = get_record('modules','id',$cm->module)) {
                    if ($mod = get_record($module->name, 'id', $cm->instance)) {
                        if ($withprefix){
                            $name = get_string('activitymodule').': ';
                        }
                        $name .= $mod->name;
                    }
                }
            }
            break;

        case CONTEXT_BLOCK: // not necessarily 1 to 1 to course
            if ($blockinstance = get_record('block_instance','id',$context->instanceid)) {
                if ($block = get_record('block','id',$blockinstance->blockid)) {
                    global $CFG;
                    require_once("$CFG->dirroot/blocks/moodleblock.class.php");
                    require_once("$CFG->dirroot/blocks/$block->name/block_$block->name.php");
                    $blockname = "block_$block->name";
                    if ($blockobject = new $blockname()) {
                        if ($withprefix){
                            $name = get_string('block').': ';
                        }
                        $name .= $blockobject->title;
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

            $SQL = "select * from {$CFG->prefix}capabilities where (contextlevel = ".CONTEXT_BLOCK." AND component = 'moodle')
                    OR (component = 'block/$block->name')";
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
 *
 * @param object $context
 * @return array()
 */
function get_parent_contexts($context) {

    if ($context->path == '') {
        return array();
    }

    $parentcontexts = substr($context->path, 1); // kill leading slash
    $parentcontexts = explode('/', $parentcontexts);
    array_pop($parentcontexts); // and remove its own id

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
 * Recursive function which, given a context, find all its children context ids.
 *
 * When called for a course context, it will return the modules and blocks
 * displayed in the course page.
 *
 * For course category contexts it will return categories and courses. It will
 * NOT recurse into courses - if you want to do that, call it on the returned
 * courses.
 *
 * If called on a course context it _will_ populate the cache with the appropriate
 * contexts ;-)
 *
 * @param object $context.
 * @return array of child records
 */
function get_child_contexts($context) {

    global $CFG, $context_cache;

    // We *MUST* populate the context_cache as the callers
    // will probably ask for the full record anyway soon after
    // soon after calling us ;-)

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
            // Find
            // - module instances - easy
            // - groups
            // - blocks assigned to the course-view page explicitly - easy
            // - blocks pinned (note! we get all of them here, regardless of vis)
            $sql = " SELECT ctx.*
                     FROM {$CFG->prefix}context ctx
                     WHERE ctx.path LIKE '{$context->path}/%'
                           AND ctx.contextlevel IN (".CONTEXT_MODULE.",".CONTEXT_BLOCK.")
                    UNION
                     SELECT ctx.*
                     FROM {$CFG->prefix}context ctx
                     JOIN {$CFG->prefix}groups  g
                       ON (ctx.instanceid=g.id AND ctx.contextlevel=".CONTEXT_GROUP.")
                     WHERE g.courseid={$context->instanceid}
                    UNION
                     SELECT ctx.*
                     FROM {$CFG->prefix}context ctx
                     JOIN {$CFG->prefix}block_pinned  b
                       ON (ctx.instanceid=b.blockid AND ctx.contextlevel=".CONTEXT_BLOCK.")
                     WHERE b.pagetype='course-view'
            ";
            $rs  = get_recordset_sql($sql);
            $records = array();
            while ($rec = rs_fetch_next_record($rs)) {
                $records[$rec->id] = $rec;
                $context_cache[$rec->contextlevel][$rec->instanceid] = $rec;
            }
            rs_close($rs);
            return $records;
        break;

        case CONTEXT_COURSECAT:
            // Find
            // - categories
            // - courses
            $sql = " SELECT ctx.*
                     FROM {$CFG->prefix}context ctx
                     WHERE ctx.path LIKE '{$context->path}/%'
                           AND ctx.contextlevel IN (".CONTEXT_COURSECAT.",".CONTEXT_COURSE.")
            ";
            $rs  = get_recordset_sql($sql);
            $records = array();
            while ($rec = rs_fetch_next_record($rs)) {
                $records[$rec->id] = $rec;
                $context_cache[$rec->contextlevel][$rec->instanceid] = $rec;
            }
            rs_close($rs);
            return $records;
        break;

        case CONTEXT_USER:
            // No children.
            return array();
        break;

        case CONTEXT_SYSTEM:
            // Just get all the contexts except for CONTEXT_SYSTEM level
            // and hope we don't OOM in the process - don't cache
            $sql = 'SELECT c.*'.
                     'FROM '.$CFG->prefix.'context c '.
                    'WHERE contextlevel != '.CONTEXT_SYSTEM;

            return get_records_sql($sql);
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

        case 'gradeexport':
            $string = get_string($stringname, 'gradeexport_'.$componentname);
        break;

        case 'gradeimport':
            $string = get_string($stringname, 'gradeimport_'.$componentname);
        break;

        case 'gradereport':
            $string = get_string($stringname, 'gradereport_'.$componentname);
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
            } else if (preg_match('|^local|', $component)) {
                $langname = str_replace('/', '_', $component);
                $string = get_string('local');
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
            } else {
                $string = get_string('course');
            }
        break;

        case CONTEXT_GROUP:
            $string = get_string('group');
        break;

        case CONTEXT_MODULE:
            $string = get_string('modulename', basename($component));
        break;

        case CONTEXT_BLOCK:
            if( $component == 'moodle' ){
                $string = get_string('block');
            }else{
                $string = get_string('blockname', 'block_'.basename($component));
            }
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

/**
 * This function is used to print roles column in user profile page.
 * @param int userid
 * @param object context
 * @return string
 */
function get_user_roles_in_context($userid, $context, $view=true){
    global $CFG, $USER;

    $rolestring = '';
    $SQL = 'select * from '.$CFG->prefix.'role_assignments ra, '.$CFG->prefix.'role r where ra.userid='.$userid.' and ra.contextid='.$context->id.' and ra.roleid = r.id';
    $rolenames = array();
    if ($roles = get_records_sql($SQL)) {
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

    if (!$return = get_records_sql('SELECT ra.*, r.name, r.shortname
                                      FROM '.$CFG->prefix.'role_assignments ra,
                                           '.$CFG->prefix.'role r,
                                           '.$CFG->prefix.'context c
                                     WHERE ra.userid = '.$userid.'
                                           AND ra.roleid = r.id
                                           AND ra.contextid = c.id
                                           AND '.$contexts . $hiddensql .'
                                  ORDER BY '.$order)) {
        $return = array();
    }

    return $return;
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
 * @param string $field
 * @return array
 */
function get_assignable_roles ($context, $field='name', $rolenamedisplay=ROLENAME_ALIAS) {

    global $CFG;

    // this users RAs
    $ras = get_user_roles($context);
    $roleids = array();
    foreach ($ras as $ra) {
        $roleids[] = $ra->roleid;
    }
    unset($ra);

    if (count($roleids)===0) {
        return array();
    }

    $roleids = implode(',',$roleids);

    // The subselect scopes the DISTINCT down to
    // the role ids - a DISTINCT over the whole of
    // the role table is much more expensive on some DBs
    $sql = "SELECT r.id, r.$field
              FROM {$CFG->prefix}role r
                   JOIN ( SELECT DISTINCT allowassign as allowedrole 
                            FROM  {$CFG->prefix}role_allow_assign raa
                           WHERE raa.roleid IN ($roleids) ) ar
                   ON r.id=ar.allowedrole
            ORDER BY sortorder ASC";

    $rs = get_recordset_sql($sql);
    $roles = array();
    while ($r = rs_fetch_next_record($rs)) {
        $roles[$r->id] = $r->{$field};
    }
    rs_close($rs);

    return role_fix_names($roles, $context, $rolenamedisplay);
}

/**
 * Gets a list of roles that this user can assign in this context, for the switchrole menu
 *
 * This is a quick-fix for MDL-13459 until MDL-8312 is sorted out...
 * @param object $context
 * @param string $field
 * @return array
 */
function get_assignable_roles_for_switchrole ($context, $field='name', $rolenamedisplay=ROLENAME_ALIAS) {

    global $CFG;

    // this users RAs
    $ras = get_user_roles($context);
    $roleids = array();
    foreach ($ras as $ra) {
        $roleids[] = $ra->roleid;
    }
    unset($ra);

    if (count($roleids)===0) {
        return array();
    }

    $roleids = implode(',',$roleids);

    // The subselect scopes the DISTINCT down to
    // the role ids - a DISTINCT over the whole of
    // the role table is much more expensive on some DBs
    $sql = "SELECT r.id, r.$field
             FROM {$CFG->prefix}role r
                  JOIN ( SELECT DISTINCT allowassign as allowedrole 
                           FROM  {$CFG->prefix}role_allow_assign raa
                           WHERE raa.roleid IN ($roleids) ) ar
                  ON r.id=ar.allowedrole
                  JOIN {$CFG->prefix}role_capabilities rc
                  ON (r.id = rc.roleid AND rc.capability = 'moodle/course:view' 
                      AND rc.capability != 'moodle/site:doanything') 
         ORDER BY sortorder ASC";

    $rs = get_recordset_sql($sql);
    $roles = array();
    while ($r = rs_fetch_next_record($rs)) {
        $roles[$r->id] = $r->{$field};
    }
    rs_close($rs);

    return role_fix_names($roles, $context, $rolenamedisplay);
}

/**
 * Gets a list of roles that this user can override in this context
 * @param object $context
 * @return array
 */
function get_overridable_roles($context, $field='name', $rolenamedisplay=ROLENAME_ALIAS) {

    $options = array();

    if ($roles = get_all_roles()) {
        foreach ($roles as $role) {
            if (user_can_override($context, $role->id)) {
                $options[$role->id] = $role->$field;
            }
        }
    }

    return role_fix_names($options, $context, $rolenamedisplay);
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
 * Who has this capability in this context?
 *
 * This can be a very expensive call - use sparingly and keep
 * the results if you are going to need them again soon.
 * 
 * Note if $fields is empty this function attempts to get u.*
 * which can get rather large - and has a serious perf impact
 * on some DBs.
 *
 * @param $context - object
 * @param $capability - string capability
 * @param $fields - fields to be pulled
 * @param $sort - the sort order
 * @param $limitfrom - number of records to skip (offset)
 * @param $limitnum - number of records to fetch
 * @param $groups - single group or array of groups - only return
 *               users who are in one of these group(s).
 * @param $exceptions - list of users to exclude
 * @param view - set to true when roles are pulled for display only
 *               this is so that we can filter roles with no visible
 *               assignment, for example, you might want to "hide" all
 *               course creators when browsing the course participants
 *               list.
 * @param boolean $useviewallgroups if $groups is set the return users who
 *               have capability both $capability and moodle/site:accessallgroups
 *               in this context, as well as users who have $capability and who are
 *               in $groups.
 */
function get_users_by_capability($context, $capability, $fields='', $sort='',
        $limitfrom='', $limitnum='', $groups='', $exceptions='', $doanything=true,
        $view=false, $useviewallgroups=false) {
    global $CFG;

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
    $caps = "'$capability'";
    if ($doanything===true) {
        $caps.=",'moodle/site:doanything'";
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
                              FROM {$CFG->prefix}role_capabilities rc
                              WHERE rc.capability='moodle/site:doanything'
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

    $sql = "SELECT rc.id, rc.roleid, rc.permission, rc.capability,
                   ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
            FROM {$CFG->prefix}role_capabilities rc
            JOIN {$CFG->prefix}context ctx on rc.contextid = ctx.id
            $doanything_join
            WHERE rc.capability IN ($caps) AND ctx.id IN ($ctxids)
                  $doanything_cond
            ORDER BY rc.roleid ASC, ctx.depth ASC";
    if ($capdefs = get_records_sql($sql)) {
        foreach ($capdefs AS $rcid=>$rc) {
            $roleids[] = (int)$rc->roleid;
            if ($rc->permission < 0) {
                $negperm = true;
            }
        }
    }
        
    $roleids = array_unique($roleids);

    if (count($roleids)===0) { // noone here!
        return false;
    }

    // is the default role interesting? does it have
    // a relevant rolecap? (we use this a lot later)
    if (in_array((int)$CFG->defaultuserroleid, $roleids, true)) {
        $defaultroleinteresting = true;
    } else {
        $defaultroleinteresting = false;
    }

    //
    // Prepare query clauses
    //
    $wherecond = array();
    /// Groups
    if ($groups) {
        if (is_array($groups)) {
            $grouptest = 'gm.groupid IN (' . implode(',', $groups) . ')';
        } else {
            $grouptest = 'gm.groupid = ' . $groups;
        }
        $grouptest = 'ra.userid IN (SELECT userid FROM ' .
            $CFG->prefix . 'groups_members gm WHERE ' . $grouptest . ')';

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
        $uljoin = "LEFT OUTER JOIN {$CFG->prefix}user_lastaccess ul 
                         ON (ul.userid = u.id AND ul.courseid = {$context->instanceid})";
    }

    //
    // Simple cases - No negative permissions means we can take shortcuts
    //
    if (!$negperm) { 

        // at the frontpage, and all site users have it - easy!
        if ($isfrontpage && !empty($CFG->defaultfrontpageroleid)
            && in_array((int)$CFG->defaultfrontpageroleid, $roleids, true)) {
            
            return get_records_sql("SELECT $fields
                                    FROM {$CFG->prefix}user u
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
                    FROM {$CFG->prefix}user u
                    $uljoin
                    $where
                    ORDER BY $sort";
            return get_records_sql($sql, $limitfrom, $limitnum);
        }

        /// Simple SQL assuming no negative rolecaps.
        /// We use a subselect to grab the role assignments
        /// ensuring only one row per user -- even if they
        /// have many "relevant" role assignments.
        $select = " SELECT $fields";
        $from   = " FROM {$CFG->prefix}user u
                    JOIN (SELECT DISTINCT ssra.userid
                          FROM {$CFG->prefix}role_assignments ssra
                          WHERE ssra.contextid IN ($ctxids)
                                AND ssra.roleid IN (".implode(',',$roleids) .")
                                $sscondhiddenra
                          ) ra ON ra.userid = u.id
                    $uljoin ";
        $where  = " WHERE u.deleted = 0 ";
        if (count(array_keys($wherecond))) {
            $where .= ' AND ' . implode(' AND ', array_values($wherecond));
        }
        return get_records_sql($select.$from.$where.$sortby, $limitfrom, $limitnum);
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
               FROM {$CFG->prefix}user u
               LEFT OUTER JOIN {$CFG->prefix}role_assignments ra 
                 ON (ra.userid = u.id
                     AND ra.contextid IN ($ctxids)
                     AND ra.roleid IN (".implode(',',$roleids) .")
                     $condhiddenra)
               LEFT OUTER JOIN {$CFG->prefix}context ctx
                 ON ra.contextid=ctx.id
               WHERE u.deleted=0";
    } else {
        // "Normal complex case" - the rolecaps we are after will
        // be defined in a role assignment somewhere.
        $ss = "SELECT ra.userid as userid, ra.roleid,
                      ctx.depth
               FROM {$CFG->prefix}role_assignments ra 
               JOIN {$CFG->prefix}context ctx
                 ON ra.contextid=ctx.id
               WHERE ra.contextid IN ($ctxids)
                     $condhiddenra
                     AND ra.roleid IN (".implode(',',$roleids) .")";
    }

    $select = "SELECT $fields ,ra.roleid, ra.depth ";
    $from   = "FROM ($ss) ra
               JOIN {$CFG->prefix}user u
                 ON ra.userid=u.id
               $uljoin ";
    $where  = "WHERE u.deleted = 0 ";
    if (count(array_keys($wherecond))) {
        $where .= ' AND ' . implode(' AND ', array_values($wherecond));
    }

    // Each user's entries MUST come clustered together
    // and RAs ordered in depth DESC - the role/cap resolution
    // code depends on this.
    $sort .= ' , ra.userid ASC, ra.depth DESC';
    $sortby .= ' , ra.userid ASC, ra.depth DESC ';

    $rs = get_recordset_sql($select.$from.$where.$sortby);

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
    //   - call has_capability_from_rarc(), which based on RAs and RCs will return a bool
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
    while ($user = rs_fetch_next_record($rs)) {

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
                if ($defaultroleinteresting) {
                    // add the role at the end of $ras
                    $ras[] = array( 'roleid' => $CFG->defaultuserroleid,
                                    'depth'  => 1 );
                }
                if (has_capability_from_rarc($ras, $roleperms, $capability, $doanything)) {
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

    // Prune last entry if necessary
    if ($lastuserid !=0) {
        if ($defaultroleinteresting) {
            // add the role at the end of $ras
            $ras[] = array( 'roleid' => $CFG->defaultuserroleid,
                            'depth'  => 1 );
        }
        if (!has_capability_from_rarc($ras, $roleperms, $capability, $doanything)) {
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

/*
 * Fast (fast!) utility function to resolve if a capability is granted,
 * based on Role Assignments and Role Capabilities.
 *
 * Used (at least) by get_users_by_capability().
 *
 * If PHP had fast built-in memoize functions, we could
 * add a $contextid parameter and memoize the return values.
 *
 * @param array $ras - role assignments
 * @param array $roleperms - role permissions
 * @param string $capability - name of the capability
 * @param bool $doanything
 * @return boolean
 * 
 */
function has_capability_from_rarc($ras, $roleperms, $capability, $doanything) {
    // Mini-state machine, using $hascap
    // $hascap[ 'moodle/foo:bar' ]->perm = CAP_SOMETHING (numeric constant)
    // $hascap[ 'moodle/foo:bar' ]->radepth = depth of the role assignment that set it
    // $hascap[ 'moodle/foo:bar' ]->rcdepth = depth of the rolecap that set it
    // -- when resolving conflicts, we need to look into radepth first, if unresolved
    
    $caps = array($capability);
    if ($doanything) {
        $caps[] = 'moodle/site:candoanything';
    }

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
    if ($hascap[$capability]->perm > 0
        || ($doanything && isset($hascap['moodle/site:candoanything'])
            && $hascap['moodle/site:candoanything']->perm > 0)) {
        return true;
    }
    return false;
}

/**
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
 * @param array users Users' array, keyed on userid
 * @param object context
 * @param array roles - ids of the roles to include, optional
 * @param string policy - defaults to locality, more about
 * @return array - sorted copy of the array
 */
function sort_by_roleassignment_authority($users, $context, $roles=array(), $sortpolicy='locality') {
    global $CFG;

    $userswhere = ' ra.userid IN (' . implode(',',array_keys($users)) . ')';
    $contextwhere = ' ra.contextid IN ('.str_replace('/', ',',substr($context->path, 1)).')';
    if (empty($roles)) {
        $roleswhere = '';
    } else {
        $roleswhere = ' AND ra.roleid IN ('.implode(',',$roles).')';
    }

    $sql = "SELECT ra.userid
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}role r
              ON ra.roleid=r.id
            JOIN {$CFG->prefix}context ctx
              ON ra.contextid=ctx.id
            WHERE
                    $userswhere
                AND $contextwhere
                $roleswhere
            ";

    // Default 'locality' policy -- read PHPDoc notes
    // about sort policies...
    $orderby = 'ORDER BY
                    ctx.depth DESC, /* locality wins */
                    r.sortorder ASC, /* rolesorting 2nd criteria */
                    ra.id           /* role assignment order tie-breaker */';
    if ($sortpolicy === 'sortorder') {
        $orderby = 'ORDER BY
                        r.sortorder ASC, /* rolesorting 2nd criteria */
                        ra.id           /* role assignment order tie-breaker */';
    }

    $sortedids = get_fieldset_sql($sql . $orderby);
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
 * gets all the users assigned this role in this context or higher
 * @param int roleid (can also be an array of ints!)
 * @param int contextid
 * @param bool parent if true, get list of users assigned in higher context too
 * @param string fields - fields from user (u.) , role assignment (ra) or role (r.)
 * @param string sort  - sort from user (u.) , role assignment (ra) or role (r.)
 * @param bool gethidden - whether to fetch hidden enrolments too
 * @return array()
 */
function get_role_users($roleid, $context, $parent=false, $fields='', $sort='u.lastname ASC', $gethidden=true, $group='', $limitfrom='', $limitnum='') {
    global $CFG;

    if (empty($fields)) {
        $fields = 'u.id, u.confirmed, u.username, u.firstname, u.lastname, '.
                  'u.maildisplay, u.mailformat, u.maildigest, u.email, u.city, '.
                  'u.country, u.picture, u.idnumber, u.department, u.institution, '.
                  'u.emailstop, u.lang, u.timezone, r.name as rolename';
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

    if (is_array($roleid)) {
        $roleselect = ' AND ra.roleid IN (' . implode(',',$roleid) .')';
    } elseif (!empty($roleid)) { // should not test for int, because it can come in as a string
        $roleselect = "AND ra.roleid = $roleid";
    } else {
        $roleselect = '';
    }

    if ($group) {
        $groupjoin   = "JOIN {$CFG->prefix}groups_members gm
                          ON gm.userid = u.id";
        $groupselect = " AND gm.groupid = $group ";
    } else {
        $groupjoin   = '';
        $groupselect = '';
    }

    $SQL = "SELECT $fields, ra.roleid
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}user u
              ON u.id = ra.userid
            JOIN {$CFG->prefix}role r
              ON ra.roleid = r.id
            $groupjoin
            WHERE (ra.contextid = $context->id $parentcontexts)
            $roleselect
            $groupselect
            $hiddensql
            ORDER BY $sort
            ";                  // join now so that we can just use fullname() later

    return get_records_sql($SQL, $limitfrom, $limitnum);
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

    $SQL = "SELECT count(u.id)
        FROM {$CFG->prefix}role_assignments r
        JOIN {$CFG->prefix}user u 
          ON u.id = r.userid
        WHERE (r.contextid = $context->id $parentcontexts)
        AND r.roleid = $roleid
        AND u.deleted = 0";

    return count_records_sql($SQL);
}

/**
 * This function gets the list of courses that this user has a particular capability in.
 * It is still not very efficient.
 * @param string $capability Capability in question
 * @param int $userid User ID or null for current user
 * @param bool $doanything True if 'doanything' is permitted (default)
 * @param string $fieldsexceptid Leave blank if you only need 'id' in the course records;
 *   otherwise use a comma-separated list of the fields you require, not including id
 * @param string $orderby If set, use a comma-separated list of fields from course
 *   table with sql modifiers (DESC) if needed
 * @return array Array of courses, may have zero entries. Or false if query failed.
 */
function get_user_capability_course($capability, $userid=NULL,$doanything=true,$fieldsexceptid='',$orderby='') {
    // Convert fields list and ordering
    $fieldlist='';
    if($fieldsexceptid) {
        $fields=explode(',',$fieldsexceptid);
        foreach($fields as $field) {
            $fieldlist.=',c.'.$field;
        }
    }
    if($orderby) {
        $fields=explode(',',$orderby);
        $orderby='';
        foreach($fields as $field) {
            if($orderby) {
                $orderby.=',';
            }
            $orderby.='c.'.$field;
        }
        $orderby='ORDER BY '.$orderby;
    }

    // Obtain a list of everything relevant about all courses including context.
    // Note the result can be used directly as a context (we are going to), the course
    // fields are just appended.
    global $CFG;
    $rs=get_recordset_sql("
SELECT
    x.*,c.id AS courseid$fieldlist
FROM
    {$CFG->prefix}course c
    INNER JOIN {$CFG->prefix}context x ON c.id=x.instanceid AND x.contextlevel=".CONTEXT_COURSE."
$orderby
");
    if(!$rs) {
        return false;
    }

    // Check capability for each course in turn
    $courses=array();
    while($coursecontext=rs_fetch_next_record($rs)) {
        if(has_capability($capability,$coursecontext,$userid,$doanything)) {
            // We've got the capability. Make the record look like a course record
            // and store it
            $coursecontext->id=$coursecontext->courseid;
            unset($coursecontext->courseid);
            unset($coursecontext->contextlevel);
            unset($coursecontext->instanceid);
            $courses[]=$coursecontext;
        }
    }
    return $courses;
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
 * in the given context.
 *
 * The caller *must* check
 * - that this op is allowed
 * - that the requested role can be assigned in this ctx
 *   (hint, use get_assignable_roles())
 * - that the requested role is NOT $CFG->defaultuserroleid
 *
 * To "unswitch" pass 0 as the roleid.
 *
 * This function *will* modify $USER->access - beware
 * 
 * @param integer $roleid
 * @param object $context
 * @return bool
 */
function role_switch($roleid, $context) {
    global $USER, $CFG;

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

    /* DO WE NEED THIS AT ALL???
    // Add some permissions we are really going 
    // to always need, even if the role doesn't have them!

    $USER->capabilities[$context->id]['moodle/course:view'] = CAP_ALLOW;
    */

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
 * Get role name or alias if exists and format the text.
 * @param object $role role object
 * @param object $coursecontext
 * @return $string name of role in course context
 */
function role_get_name($role, $coursecontext) {
    if ($r = get_record('role_names','roleid', $role->id,'contextid', $coursecontext->id)) {
        return strip_tags(format_string($r->name));
    } else {
        return strip_tags(format_string($role->name));
    }
}

/**
 * Prepare list of roles for display, apply aliases and format text
 * @param array $roleoptions array roleid=>rolename
 * @param object $context
 * @return array of role names
 */
function role_fix_names($roleoptions, $context, $rolenamedisplay=ROLENAME_ALIAS) {
    if ($rolenamedisplay != ROLENAME_ORIGINAL && !empty($context->id)) {
        if ($context->contextlevel == CONTEXT_MODULE || $context->contextlevel == CONTEXT_BLOCK) {  // find the parent course context
            if ($parentcontextid = array_shift(get_parent_contexts($context))) {
                $context = get_context_instance_by_id($parentcontextid);
            }
        }
        if ($aliasnames = get_records('role_names', 'contextid', $context->id)) {
            if ($rolenamedisplay == ROLENAME_ALIAS) {
                foreach ($aliasnames as $alias) {
                    if (isset($roleoptions[$alias->roleid])) {
                        $roleoptions[$alias->roleid] = format_string($alias->name);
                    }
                }
            } else if ($rolenamedisplay == ROLENAME_BOTH) {
                foreach ($aliasnames as $alias) {
                    if (isset($roleoptions[$alias->roleid])) {
                        $roleoptions[$alias->roleid] = format_string($alias->name).' ('.format_string($roleoptions[$alias->roleid]).')';
                    }
                }
            }
        }
    }
    foreach ($roleoptions as $rid => $name) {
        $roleoptions[$rid] = strip_tags($name);
    }
    return $roleoptions;
}

/**
 * This function helps admin/roles/manage.php etc to detect if a new line should be printed
 * when we read in a new capability
 * most of the time, if the 2 components are different we should print a new line, (e.g. course system->rss client)
 * but when we are in grade, all reports/import/export capabilites should be together
 * @param string a - component string a
 * @param string b - component string b
 * @return bool - whether 2 component are in different "sections"
 */
function component_level_changed($cap, $comp, $contextlevel) {

    if ($cap->component == 'enrol/authorize' && $comp =='enrol/authorize') {
        return false;
    }

    if (strstr($cap->component, '/') && strstr($comp, '/')) {
        $compsa = explode('/', $cap->component);
        $compsb = explode('/', $comp);



        // we are in gradebook, still
        if (($compsa[0] == 'gradeexport' || $compsa[0] == 'gradeimport' || $compsa[0] == 'gradereport') &&
            ($compsb[0] == 'gradeexport' || $compsb[0] == 'gradeimport' || $compsb[0] == 'gradereport')) {
            return false;
        }
    }

    return ($cap->component != $comp || $cap->contextlevel != $contextlevel);
}

/**
 * Populate context.path and context.depth where missing.
 * @param bool $force force a complete rebuild of the path and depth fields.
 * @param bool $feedback display feedback (during upgrade usually)
 * @return void
 */
function build_context_path($force=false, $feedback=false) {
    global $CFG;
    require_once($CFG->libdir.'/ddllib.php');

    // System context
    $sitectx = get_system_context(!$force);
    $base    = '/'.$sitectx->id;

    // Sitecourse
    $sitecoursectx = get_record('context',
                                'contextlevel', CONTEXT_COURSE,
                                'instanceid', SITEID);
    if ($force || $sitecoursectx->path !== "$base/{$sitecoursectx->id}") {
        set_field('context', 'path',  "$base/{$sitecoursectx->id}",
                  'id', $sitecoursectx->id);
        set_field('context', 'depth', 2,
                  'id', $sitecoursectx->id);
        $sitecoursectx = get_record('context',
                                    'contextlevel', CONTEXT_COURSE,
                                    'instanceid', SITEID);
    }

    $ctxemptyclause = " AND (ctx.path IS NULL
                              OR ctx.depth=0) ";
    $emptyclause    = " AND ({$CFG->prefix}context.path IS NULL
                              OR {$CFG->prefix}context.depth=0) ";
    if ($force) {
        $ctxemptyclause = $emptyclause = '';
    }

    /* MDL-11347:
     *  - mysql does not allow to use FROM in UPDATE statements
     *  - using two tables after UPDATE works in mysql, but might give unexpected
     *    results in pg 8 (depends on configuration)
     *  - using table alias in UPDATE does not work in pg < 8.2
     */
    if ($CFG->dbfamily == 'mysql') {
        $updatesql = "UPDATE {$CFG->prefix}context ct, {$CFG->prefix}context_temp temp
                         SET ct.path  = temp.path,
                             ct.depth = temp.depth
                       WHERE ct.id = temp.id";
    } else if ($CFG->dbfamily == 'oracle') {
        $updatesql = "UPDATE {$CFG->prefix}context ct
                         SET (ct.path, ct.depth) =
                             (SELECT temp.path, temp.depth
                                FROM {$CFG->prefix}context_temp temp
                               WHERE temp.id=ct.id)
                       WHERE EXISTS (SELECT 'x'
                                       FROM {$CFG->prefix}context_temp temp
                                       WHERE temp.id = ct.id)";
    } else {
        $updatesql = "UPDATE {$CFG->prefix}context
                         SET path  = temp.path,
                             depth = temp.depth
                        FROM {$CFG->prefix}context_temp temp
                       WHERE temp.id={$CFG->prefix}context.id";
    }

    $udelsql = "TRUNCATE TABLE {$CFG->prefix}context_temp";

    // Top level categories
    $sql = "UPDATE {$CFG->prefix}context
               SET depth=2, path=" . sql_concat("'$base/'", 'id') . "
             WHERE contextlevel=".CONTEXT_COURSECAT."
                   AND EXISTS (SELECT 'x'
                                 FROM {$CFG->prefix}course_categories cc
                                WHERE cc.id = {$CFG->prefix}context.instanceid
                                      AND cc.depth=1)
                   $emptyclause";

    execute_sql($sql, $feedback);

    execute_sql($udelsql, $feedback);

    // Deeper categories - one query per depthlevel
    $maxdepth = get_field_sql("SELECT MAX(depth)
                               FROM {$CFG->prefix}course_categories");
    for ($n=2;$n<=$maxdepth;$n++) {
        $sql = "INSERT INTO {$CFG->prefix}context_temp (id, path, depth)
                SELECT ctx.id, ".sql_concat('pctx.path', "'/'", 'ctx.id').", $n+1
                  FROM {$CFG->prefix}context ctx
                  JOIN {$CFG->prefix}course_categories c ON ctx.instanceid=c.id
                  JOIN {$CFG->prefix}context pctx ON c.parent=pctx.instanceid
                 WHERE ctx.contextlevel=".CONTEXT_COURSECAT."
                       AND pctx.contextlevel=".CONTEXT_COURSECAT."
                       AND c.depth=$n
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {$CFG->prefix}context_temp temp
                                       WHERE temp.id = ctx.id)
                       $ctxemptyclause";
        execute_sql($sql, $feedback);
        
        // this is needed after every loop
        // MDL-11532
        execute_sql($updatesql, $feedback);
        execute_sql($udelsql, $feedback);
    }

    // Courses -- except sitecourse
    $sql = "INSERT INTO {$CFG->prefix}context_temp (id, path, depth)
            SELECT ctx.id, ".sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {$CFG->prefix}context ctx
              JOIN {$CFG->prefix}course c ON ctx.instanceid=c.id
              JOIN {$CFG->prefix}context pctx ON c.category=pctx.instanceid
             WHERE ctx.contextlevel=".CONTEXT_COURSE."
                   AND c.id!=".SITEID."
                   AND pctx.contextlevel=".CONTEXT_COURSECAT."
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {$CFG->prefix}context_temp temp
                                       WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    execute_sql($sql, $feedback);

    execute_sql($updatesql, $feedback);
    execute_sql($udelsql, $feedback);

    // Module instances
    $sql = "INSERT INTO {$CFG->prefix}context_temp (id, path, depth)
            SELECT ctx.id, ".sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {$CFG->prefix}context ctx
              JOIN {$CFG->prefix}course_modules cm ON ctx.instanceid=cm.id
              JOIN {$CFG->prefix}context pctx ON cm.course=pctx.instanceid
             WHERE ctx.contextlevel=".CONTEXT_MODULE."
                   AND pctx.contextlevel=".CONTEXT_COURSE."
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {$CFG->prefix}context_temp temp
                                       WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    execute_sql($sql, $feedback);

    execute_sql($updatesql, $feedback);
    execute_sql($udelsql, $feedback);

    // Blocks - non-pinned course-view only
    $sql = "INSERT INTO {$CFG->prefix}context_temp (id, path, depth)
            SELECT ctx.id, ".sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1
              FROM {$CFG->prefix}context ctx
              JOIN {$CFG->prefix}block_instance bi ON ctx.instanceid = bi.id
              JOIN {$CFG->prefix}context pctx ON bi.pageid=pctx.instanceid
             WHERE ctx.contextlevel=".CONTEXT_BLOCK."
                   AND pctx.contextlevel=".CONTEXT_COURSE."
                   AND bi.pagetype='course-view'
                       AND NOT EXISTS (SELECT 'x'
                                       FROM {$CFG->prefix}context_temp temp
                                       WHERE temp.id = ctx.id)
                   $ctxemptyclause";
    execute_sql($sql, $feedback);

    execute_sql($updatesql, $feedback);
    execute_sql($udelsql, $feedback);

    // Blocks - others
    $sql = "UPDATE {$CFG->prefix}context
               SET depth=2, path=".sql_concat("'$base/'", 'id')."
             WHERE contextlevel=".CONTEXT_BLOCK."
                   AND EXISTS (SELECT 'x'
                                 FROM {$CFG->prefix}block_instance bi
                                WHERE bi.id = {$CFG->prefix}context.instanceid
                                      AND bi.pagetype!='course-view')
                   $emptyclause ";
    execute_sql($sql, $feedback);

    // User
    $sql = "UPDATE {$CFG->prefix}context
               SET depth=2, path=".sql_concat("'$base/'", 'id')."
             WHERE contextlevel=".CONTEXT_USER."
                   AND EXISTS (SELECT 'x'
                                 FROM {$CFG->prefix}user u
                                WHERE u.id = {$CFG->prefix}context.instanceid)
                   $emptyclause ";
    execute_sql($sql, $feedback);

    // Personal TODO

    //TODO: fix group contexts

    // reset static course cache - it might have incorrect cached data
    global $context_cache, $context_cache_id;
    $context_cache    = array();
    $context_cache_id = array();

}

/**
 * Update the path field of the context and
 * all the dependent subcontexts that follow
 * the move. 
 *
 * The most important thing here is to be as
 * DB efficient as possible. This op can have a
 * massive impact in the DB.
 *
 * @param obj current   context obj
 * @param obj newparent new parent obj
 *
 */
function context_moved($context, $newparent) {
    global $CFG;

    $frompath = $context->path;
    $newpath  = $newparent->path . '/' . $context->id;

    $setdepth = '';
    if (($newparent->depth +1) != $context->depth) {
        $setdepth = ", depth= depth + ({$newparent->depth} - {$context->depth}) + 1";
    }
    $sql = "UPDATE {$CFG->prefix}context 
            SET path='$newpath'
                $setdepth
            WHERE path='$frompath'";
    execute_sql($sql,false);

    $len = strlen($frompath);
    $sql = "UPDATE {$CFG->prefix}context
            SET path = ".sql_concat("'$newpath'", 'SUBSTR(path, '.$len.' +1)')."
                $setdepth
            WHERE path LIKE '{$frompath}/%'";
    execute_sql($sql,false);

    mark_context_dirty($frompath);
    mark_context_dirty($newpath);
}


/**
 * Turn the ctx* fields in an objectlike record
 * into a context subobject. This allows
 * us to SELECT from major tables JOINing with 
 * context at no cost, saving a ton of context
 * lookups...
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
 * Fetch recent dirty contexts to know cheaply whether our $USER->access
 * is stale and needs to be reloaded.
 *
 * Uses cache_flags
 * @param int $time
 * @return array of dirty contexts
 */
function get_dirty_contexts($time) {
    return get_cache_flags('accesslib/dirtycontexts', $time-2);
}

/**
 * Mark a context as dirty (with timestamp)
 * so as to force reloading of the context.
 * @param string $path context path
 */
function mark_context_dirty($path) {
    global $CFG, $DIRTYCONTEXTS;
    // only if it is a non-empty string
    if (is_string($path) && $path !== '') {
        set_cache_flag('accesslib/dirtycontexts', $path, 1, time()+$CFG->sessiontimeout);
        if (isset($DIRTYCONTEXTS)) {
            $DIRTYCONTEXTS[$path] = 1;
        }
    }
}

/**
 * Will walk the contextpath to answer whether
 * the contextpath is dirty
 *
 * @param array $contexts array of strings
 * @param obj/array dirty contexts from get_dirty_contexts()
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
 * 
 * switch role order (used in admin/roles/manage.php)
 *
 * @param int $first id of role to move down
 * @param int $second id of role to move up
 *
 * @return bool success or failure
 */
function switch_roles($first, $second) {
    $status = true;
    //first find temorary sortorder number
    $tempsort = count_records('role') + 3;
    while (get_record('role','sortorder', $tempsort)) {
        $tempsort += 3;
    }

    $r1 = new object();
    $r1->id = $first->id;
    $r1->sortorder = $tempsort;
    $r2 = new object();
    $r2->id = $second->id;
    $r2->sortorder = $first->sortorder;

    if (!update_record('role', $r1)) {
        debugging("Can not update role with ID $r1->id!");
        $status = false;
    }

    if (!update_record('role', $r2)) {
        debugging("Can not update role with ID $r2->id!");
        $status = false;
    }

    $r1->sortorder = $second->sortorder;
    if (!update_record('role', $r1)) {
        debugging("Can not update role with ID $r1->id!");
        $status = false;
    }

    return $status;
}

/**
 * duplicates all the base definitions of a role
 *
 * @param object $sourcerole role to copy from
 * @param int $targetrole id of role to copy to
 *
 * @return void
 */
function role_cap_duplicate($sourcerole, $targetrole) {
    global $CFG;
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);
    $caps = get_records_sql("SELECT * FROM {$CFG->prefix}role_capabilities
                             WHERE roleid = $sourcerole->id
                             AND contextid = $systemcontext->id");
    // adding capabilities
    foreach ($caps as $cap) {
        unset($cap->id);
        $cap->roleid = $targetrole;
        insert_record('role_capabilities', $cap);
    }
}?>

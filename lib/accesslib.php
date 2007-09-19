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
 * Public API vs internals 
 * -----------------------
 * 
 * General users probably only care about
 *
 * - get_context_instance() 
 * - has_capability()
 * - require_capability()
 * - get_user_courses_bycap()
 * - get_context_users_bycap()
 * - get_parent_contexts()
 * - enrol_into_course()
 * - role_assign()/role_unassign()
 * - more?
 *
 * Advanced use
 * - $ACCESS global
 * - has_cap_fad()
 * - more?
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
 * accessdata ($ad) is a multidimensional array, holding
 * role assignments (RAs), role-capabilities-perm sets 
 * (role defs) and a list of courses we have loaded
 * data for.
 *
 * Things are keyed on "contextpaths" (the path field of 
 * the context table) for fast walking up/down the tree.
 * 
 * $ad[ra][$contextpath]= array($roleid)
 *        [$contextpath]= array($roleid)
 *        [$contextpath]= array($roleid) 
 *
 * Role definitions are stored like this
 * (no cap merge is done - so it's compact)
 *
 * $ad[rdef][$contextpath:$roleid][mod/forum:viewpost] = 1
 *                                [mod/forum:editallpost] = -1
 *                                [mod/forum:startdiscussion] = -1000
 *
 * See how has_cap_fad() walks up/down the tree.
 *
 * Normally - specially for the logged-in user, we only load
 * rdef and ra down to the course level, but not below. This
 * keeps accessdata small and compact. Below-the-course ra/rdef
 * are loaded as needed. We keep track of which courses we
 * have loaded ra/rdef in 
 *
 * $ad[loaded] = array($contextpath, $contextpath) 
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
function get_role_access($roleid, $acc=NULL) {

    global $CFG;

    /* Get it in 1 cheap DB query...
     * - relevant role caps at the root and down
     *   to the course level - but not below
     */
    if (is_null($acc)) {
        $acc           = array(); // named list
        $acc['ra']     = array();
        $acc['rdef']   = array();
        $acc['loaded'] = array();
    }

    $base = '/' . SYSCONTEXTID;

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
    $rs = get_recordset_sql($sql);
    if ($rs->RecordCount()) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$roleid}";
            $acc['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
    }
    rs_close($rs);

    return $acc;
}

/**
 * Get the id for the not-logged-in role - or set it up if needed
 * @return bool
 */
function get_notloggedin_roleid($return=false) {
    global $CFG, $USER;

    if (empty($CFG->notloggedinroleid)) {    // Let's set the default to the guest role
        if ($role = get_guest_role()) {
            set_config('notloggedinroleid', $role->id);
            return $role->id;
        } else {
            return false;
        }
    } else {
        return $CFG->notloggedinroleid;
    }

    return (get_record('role','id', $CFG->notloggedinas));
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

function has_capability($capability, $context=NULL, $userid=NULL, $doanything=true) {
    global $USER, $CONTEXT, $ACCESS, $CFG, $DIRTYCONTEXTS;

    /// Make sure we know the current context
    if (empty($context)) {              // Use default CONTEXT if none specified
        if (empty($CONTEXT)) {
            return false;
        } else {
            $context = $CONTEXT;
        }
    }
    if (empty($CONTEXT)) {
        $CONTEXT = $context;
    }

    if (is_null($userid) || $userid===0) {
        $userid = $USER->id;
    }

    $contexts = array();
    $basepath = '/' . SYSCONTEXTID;
    if (empty($context->path)) {
        $contexts[] = SYSCONTEXTID;
        $context->path = $basepath;
        if (isset($context->id) && $context->id ==! SYSCONTEXTID) {
            $contexts[] = $context->id;
            $context->path .= '/' . $context->id;
        }
    } else {
        $contexts = explode('/', $context->path);
        array_shift($contexts);
    }

    if ($USER->id === 0 && !isset($USER->access)) {
        load_all_capabilities();
    }

    if (defined('FULLME') && FULLME === 'cron' && !isset($USER->access)) {
        //
        // In cron, some modules setup a 'fake' $USER,
        // ensure we load the appropriate accessdata.
        // Also: set $DIRTYCONTEXTS to empty
        // 
        if (!isset($ACCESS)) {
            $ACCESS = array();
        }
        if (!isset($ACCESS[$userid])) {
            load_user_accessdata($userid);
        }
        $USER->access = $ACCESS[$userid];
        $DIRTYCONTEXTS = array();
    }

    // Careful check for staleness...
    $clean = true;
    if (!isset($DIRTYCONTEXTS)) {
        // Load dirty contexts list
        $DIRTYCONTEXTS = get_dirty_contexts($USER->access['time']);

        // Check basepath only once, when
        // we load the dirty contexts...
        if (isset($DIRTYCONTEXTS[$basepath])) {
            // sitewide change, dirty
            $clean = false;
        }
    }
    // Check for staleness in the whole parenthood
    if ($clean && !is_contextpath_clean($context->path, $DIRTYCONTEXTS)) {
        $clean = false;
    }
    if (!$clean) {
        // reload all capabilities - preserving loginas, roleswitches, etc
        // and then cleanup any marks of dirtyness... at least from our short
        // term memory! :-)
        reload_all_capabilities();
        $DIRTYCONTEXTS = array();
        $clean = true;
    }
    
    // divulge how many times we are called
    //// error_log("has_capability: id:{$context->id} path:{$context->path} userid:$userid cap:$capability");

    if ($USER->id === $userid) {
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
            return has_cap_fad($capability, $context,
                               $USER->access, $doanything);
        }
        // Load it as needed
        if (!path_inaccessdata($context->path,$USER->access)) {
            error_log("loading access for context {$context->path} for $capability at {$context->contextlevel} {$context->id}");
            // $bt = debug_backtrace();
            // error_log("bt {$bt[0]['file']} {$bt[0]['line']}");
            $USER->access = get_user_access_bycontext($USER->id, $context,
                                                      $USER->access);
        }
        return has_cap_fad($capability, $context,
                           $USER->access, $doanything);


    }
    if (!isset($ACCESS)) {
        $ACCESS = array();
    }
    if (!isset($ACCESS[$userid])) {
        load_user_accessdata($userid);
    }
    return has_cap_fad($capability, $context,
                       $ACCESS[$userid], $doanything);
}

function get_course_from_path ($path) {
    // assume that nothing is more than 1 course deep
    if (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        return $matches[1];
    }
    return false;
}

function path_inaccessdata($path, $ad) {

    // assume that contexts hang from sys or from a course
    // this will only work well with stuff that hangs from a course
    if (in_array($path, $ad['loaded'], true)) {
            error_log("found it!");
        return true;
    }
    $base = '/' . SYSCONTEXTID;
    while (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        $path = $matches[1];
        if ($path === $base) {
            return false;
        }
        if (in_array($path, $ad['loaded'], true)) {
            return true;
        }
    }
    return false;
}

/*
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
function has_cap_fad($capability, $context, $ad, $doanything) {

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
    if (isset($ad['dr'])
        && ($capability    == 'moodle/course:view'
            || $capability == 'moodle/legacy:guest')) {
        // At the base, ignore rdefs where moodle/legacy:guest
        // is set
        $ignoreguest = $ad['dr'];
    }


    $cc = count($contexts);

    $can = false;

    //
    // role-switches loop
    //
    if (isset($ad['rsw'])) {
        // check for isset() is fast 
        // empty() is slow...
        if (empty($ad['rsw'])) {
            unset($ad['rsw']); // keep things fast and unambiguous
            break;
        }
        // From the bottom up...
        for ($n=$cc-1;$n>=0;$n--) {
            $ctxp = $contexts[$n];
            if (isset($ad['rsw'][$ctxp])) {
                // Found a switchrole assignment
                // check for that role _plus_ the default user role
                $ras = array($ad['rsw'][$ctxp],$CFG->defaultuserroleid);
                for ($rn=0;$rn<2;$rn++) {
                    $roleid = $ras[$rn];
                    // Walk the path for capabilities
                    // from the bottom up...
                    for ($m=$cc-1;$m>=0;$m--) {
                        $capctxp = $contexts[$m];
                        if (isset($ad['rdef']["{$capctxp}:$roleid"][$capability])) {
                            $perm = $ad['rdef']["{$capctxp}:$roleid"][$capability];
                            if ($perm === CAP_PROHIBIT) {
                                return false;
                            } else {
                                $can += $perm;
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
                        return has_cap_fad('moodle/site:doanything', $context,
                                                $ad, false);
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
        if (isset($ad['ra'][$ctxp])) {
            // Found role assignments on this leaf
            $ras = $ad['ra'][$ctxp];
            $rc  = count($ras);
            for ($rn=0;$rn<$rc;$rn++) {
                $roleid = $ras[$rn];
                // Walk the path for capabilities
                // from the bottom up...
                for ($m=$cc-1;$m>=0;$m--) {
                    $capctxp = $contexts[$m];
                    // ignore some guest caps
                    // at base ra and rdef
                    if ($ignoreguest == $roleid
                        && $n === 0
                        && $m === 0
                        && isset($ad['rdef']["{$capctxp}:$roleid"]['moodle/legacy:guest'])
                        && $ad['rdef']["{$capctxp}:$roleid"]['moodle/legacy:guest'] > 0) {
                            continue;
                    }
                    if (isset($ad['rdef']["{$capctxp}:$roleid"][$capability])) {
                        $perm = $ad['rdef']["{$capctxp}:$roleid"][$capability];
                        if ($perm === CAP_PROHIBIT) {
                            return false;
                        } else {
                            $can += $perm;
                        }
                    }
                }
            }
        }
    }

    if ($can < 1) {
        if ($doanything) {
            // didn't find it as an explicit cap,
            // but maybe the user candoanything in this context...
            return has_cap_fad('moodle/site:doanything', $context,
                                    $ad, false);
        } else {
            return false;
        }
    } else {
        return true;
    }

}

function aggr_roles_fad($context, $ad) {

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
        if (isset($ad['ra'][$ctxp]) && count($ad['ra'][$ctxp])) {
            // Found assignments on this leaf
            $addroles = $ad['ra'][$ctxp];
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
function require_capability($capability, $context=NULL, $userid=NULL, $doanything=true,
                            $errormessage='nopermissions', $stringfile='') {

    global $USER, $CFG;

/// If the current user is not logged in, then make sure they are (if needed)

    if (is_null($userid) && !isset($USER->access)) {
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

/*
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
 *     has_cap_fad() code to speed it up)
 *
 * @param string $capability - name of the capability
 * @param array  $accessdata - access session array
 * @param bool   $doanything - if false, ignore do anything
 * @param string $sort - sorting fields - prefix each fieldname with "c."
 * @param array  $fields - additional fields you are interested in...
 * @param int    $limit  - set if you want to limit the number of courses
 * @return array $courses - ordered array of course objects - see notes above
 *
 */
function get_user_courses_bycap($userid, $cap, $ad, $doanything, $sort='c.sortorder ASC', $fields=NULL, $limit=0) {

    global $CFG;

    // Slim base fields, let callers ask for what they need...
    $basefields = array('id', 'sortorder', 'shortname', 'idnumber');

    if (!is_null($fields)) {
        $fields = array_merge($basefields, $fields);
        $fields = array_unique($fields);
    } else {
        $fields = $basefields;
    }
    $coursefields = 'c.' .join(',c.', $fields);

    $sysctx = get_context_instance(CONTEXT_SYSTEM);
    if (has_cap_fad($cap, $sysctx, $ad, $doanything)) {
        //
        // Apparently the user has the cap sitewide, so walk *every* course
        // (the cap checks are moderately fast, but this moves massive bandwidth w the db)
        // Yuck.
        //
        $sql = "SELECT $coursefields,
                       ctx.id AS ctxid, ctx.path AS ctxpath, ctx.depth as ctxdepth,
                       cc.path AS categorypath
                FROM {$CFG->prefix}course c
                JOIN {$CFG->prefix}course_categories cc
                  ON c.category=cc.id
                JOIN {$CFG->prefix}context ctx 
                  ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                ORDER BY $sort ";
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
        if ($rs->RecordCount()) {
            while ($catctx = rs_fetch_next_record($rs)) {
                if ($catctx->path != '' 
                    && has_cap_fad($cap, $catctx, $ad, $doanything)) {
                    $catpaths[] = $catctx->path;
                }
            }
        }
        rs_close($rs);
        $catclause = '';
        if (count($catpaths)) {
            $cc = count($catpaths);
            for ($n=0;$n<$cc;$n++) {
                $catpaths[$n] = "ctx.path LIKE '{$catpaths[$n]}/%'";
            }
            $catclause = 'OR (' . join(' OR ', $catpaths) .')';
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
                       ctx.id AS ctxid, ctx.path AS ctxpath, ctx.depth as ctxdepth,
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
                ORDER BY $sort ";
        $rs = get_recordset_sql($sql);
    }
    $courses = array();
    $cc = 0; // keep count
    if ($rs->RecordCount()) {
        while ($c = rs_fetch_next_record($rs)) {
            // build the context obj
            $c = make_context_subobj($c);

            if (has_cap_fad($cap, $c->context, $ad, $doanything)) {
                $courses[] = $c;
                if ($limit > 0 && $cc++ > $limit) {
                    break;
                }
            }
        }
    }
    rs_close($rs);
    return $courses;
}

/*
 * Draft - use for the course participants list page 
 *
 * Uses 1 DB query (cheap too - 2~7ms).
 *
 * TODO:
 * - implement additional where clauses
 * - sorting
 * - get course participants list to use it!
 *
 * returns a users array, both sorted _and_ keyed
 * on id (as get_my_courses() does)
 *
 * as a bonus, every user record comes with its own
 * personal context, as our callers need it straight away
 * {save 1 dbquery per user! yay!}
 *
 */
function get_context_users_byrole ($context, $roleid, $fields=NULL, $where=NULL, $sort=NULL, $limit=0) {

    global $CFG;
    // Slim base fields, let callers ask for what they need...
    $basefields = array('id', 'username');

    if (!is_null($fields)) {
        $fields = array_merge($basefields, $fields);
        $fields = array_unique($fields);
    } else {
        $fields = $basefields;
    }
    $userfields = 'u.' .join(',u.', $fields);

    $contexts = substr($context->path, 1); // kill leading slash
    $contexts = str_replace('/', ',', $contexts);

    $sql = "SELECT $userfields,
                   ctx.id AS ctxid, ctx.path AS ctxpath, ctx.depth as ctxdepth
            FROM {$CFG->prefix}user u
            JOIN {$CFG->prefix}context ctx 
              ON (u.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_USER.")
            JOIN {$CFG->prefix}role_assignments ra
              ON u.id = ra.userid
            WHERE ra.roleid = $roleid 
                  AND ra.contextid IN ($contexts)";

    $rs = get_recordset_sql($sql);
    
    $users = array();
    $cc = 0; // keep count
    if ($rs->RecordCount()) {
        while ($u = rs_fetch_next_record($rs)) {
            // build the context obj
            $u = make_context_subobj($u);

            $users[] = $u;
            if ($limit > 0 && $cc++ > $limit) {
                break;
            }
        }
    }
    rs_close($rs);
    return $users;
}

/*
 * Draft - use for the course participants list page 
 *
 * Uses 2 fast DB queries
 *
 * TODO:
 * - automagically exclude roles that can-doanything sitewide (See callers)
 *   - perhaps also allow sitewide do-anything via flag
 * - implement additional where clauses
 * - sorting
 * - get course participants list to use it!
 *
 * returns a users array, both sorted _and_ keyed
 * on id (as get_my_courses() does)
 *
 * as a bonus, every user record comes with its own
 * personal context, as our callers need it straight away
 * {save 1 dbquery per user! yay!}
 *
 */
function get_context_users_bycap ($context, $capability='moodle/course:view', $fields=NULL, $where=NULL, $sort=NULL, $limit=0) {
    global $CFG;

    // Plan
    // 
    // - Get all the *interesting* roles -- those that
    //   have some rolecap entry in our ctx.path contexts
    //
    // - Get all RAs for any of those roles in any of our 
    //   interesting contexts, with userid & perm data
    //   in a nice (per user?) order
    // 
    // - Walk the resultset, computing the permissions
    //   - actually - this is all a SQL subselect
    // 
    // - Fetch user records against the subselect
    //

    // Slim base fields, let callers ask for what they need...
    $basefields = array('id', 'username');

    if (!is_null($fields)) {
        $fields = array_merge($basefields, $fields);
        $fields = array_unique($fields);
    } else {
        $fields = $basefields;
    }
    $userfields = 'u.' .join(',u.', $fields);

    $contexts = substr($context->path, 1); // kill leading slash
    $contexts = str_replace('/', ',', $contexts);

    $roles = array();
    $sql = "SELECT DISTINCT rc.roleid
            FROM {$CFG->prefix}role_capabilities rc
            WHERE rc.capability = '$capability'
                  AND rc.contextid IN ($contexts)";
    $rs = get_recordset_sql($sql);
    if ($rs->RecordCount()) {
        while ($u = rs_fetch_next_record($rs)) {
            $roles[] = $u->roleid;
        }
    }
    rs_close($rs);
    $roles = join(',', $roles);

    //
    // User permissions subselect SQL
    //
    // - the open join condition to
    //   role_capabilities
    //
    // - because both rc and ra entries are
    //   _at or above_ our context, we don't care
    //   about their depth, we just need to sum them
    // 
    $sql = "SELECT ra.userid, SUM(rc.permission) AS permission
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}role_capabilities rc
              ON (ra.roleid = rc.roleid AND rc.contextid IN ($contexts))
            WHERE     ra.contextid  IN ($contexts)
                  AND ra.roleid IN ($roles)
            GROUP BY ra.userid";

    // Get users
    $sql = "SELECT $userfields,
                   ctx.id AS ctxid, ctx.path AS ctxpath, ctx.depth as ctxdepth
            FROM {$CFG->prefix}user u
            JOIN {$CFG->prefix}context ctx 
              ON (u.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_USER.")
            JOIN ($sql) up
              ON u.id = up.userid
            WHERE up.permission > 0 AND u.username != 'guest'";

    $rs = get_recordset_sql($sql);
    
    $users = array();
    $cc = 0; // keep count
    if ($rs->RecordCount()) {
        while ($u = rs_fetch_next_record($rs)) {
            // build the context obj
            $u = make_context_subobj($u);

            $users[] = $u;
            if ($limit > 0 && $cc++ > $limit) {
                break;
            }
        }
    }
    rs_close($rs);
    return $users;
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

    $acc           = array(); // named list
    $acc['ra']     = array();
    $acc['rdef']   = array();
    $acc['loaded'] = array();

    $sitectx = get_field('context', 'id','contextlevel', CONTEXT_SYSTEM);
    $base = "/$sitectx";

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
    if ($rs->RecordCount()) {
        while ($ra = rs_fetch_next_record($rs)) {
            // RAs leafs are arrays to support multi
            // role assignments...
            if (!isset($acc['ra'][$ra->path])) {
                $acc['ra'][$ra->path] = array();
            }
            // only add if is not a repeat caused
            // by capability join...
            // (this check is cheaper than in_array())
            if ($lastseen !== $ra->path.':'.$ra->roleid) {
                $lastseen = $ra->path.':'.$ra->roleid;
                array_push($acc['ra'][$ra->path], $ra->roleid);
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
                $acc['rdef'][$k][$ra->capability] = $ra->permission;
            }
        }
        unset($ra);
    }
    rs_close($rs);

    // Walk up the tree to grab all the roledefs
    // of interest to our user...
    // NOTE: we use a series of IN clauses here - which
    // might explode on huge sites with very convoluted nesting of
    // categories... - extremely unlikely that the number of categories
    // and roletypes is so large that we hit the limits of IN()
    $clauses = array();
    foreach ($raparents as $roleid=>$contexts) {
        $contexts = sql_intarray_to_in(array_unique($contexts));
        if ($contexts ==! '') {
            $clauses[] = "(roleid=$roleid AND contextid IN ($contexts))";
        }
    }
    $clauses = join(" OR ", $clauses);
    if ($clauses !== '') {
        $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
                FROM {$CFG->prefix}role_capabilities rc
                JOIN {$CFG->prefix}context ctx
                  ON rc.contextid=ctx.id
                WHERE $clauses
                ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";

        $rs = get_recordset_sql($sql);
        unset($clauses);

        if ($rs->RecordCount()) {
            while ($rd = rs_fetch_next_record($rs)) {
                $k = "{$rd->path}:{$rd->roleid}";
                $acc['rdef'][$k][$rd->capability] = $rd->permission;
            }
            unset($rd);
        }
        rs_close($rs);
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
              ON (sctx.path LIKE ctx.path||'/%')
            JOIN {$CFG->prefix}role_capabilities rco
              ON (rco.roleid=ra.roleid AND rco.contextid=sctx.id)
            WHERE ra.userid = $userid
                  AND sctx.contextlevel <= ".CONTEXT_COURSE."
            ORDER BY sctx.depth, sctx.path, ra.roleid";
    if ($rs->RecordCount()) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$rd->roleid}";
            $acc['rdef'][$k][$rd->capability] = $rd->permission;
        }
        unset($rd);
    }
    rs_close($rs);

    return $acc;
}

/**
 * It add to the access ctrl array the data
 * needed by a user for a given context
 *
 * @param $userid  integer - the id of the user
 * @param $context context obj - needs path!
 * @param $acc     access array
 *
 */
function get_user_access_bycontext($userid, $context, $acc=NULL) {

    global $CFG;



    /* Get the additional RAs and relevant rolecaps
     * - role assignments - with role_caps
     * - relevant role caps
     *   - above this user's RAs
     *   - below this user's RAs - limited to course level
     */

    // Roles already in use in this context
    $knownroles = array();
    if (is_null($acc)) {
        $acc           = array(); // named list
        $acc['ra']     = array();
        $acc['rdef']   = array();
        $acc['loaded'] = array();
    } else {
        $knownroles = aggr_roles_fad($context, $acc);
    }

    $base = "/" . SYSCONTEXTID;

    // Determine the course context we'll go
    // after, though we are usually called
    // with a lower ctx. We have 3 easy cases
    //
    // - Course
    // - BLOCK/PERSON/USER/COURSE(sitecourse) hanging from SYSTEM
    // - BLOCK/MODULE/GROUP hanging from a course
    //
    // For course contexts, we _already_ have the RAs
    // but the cost of re-fetching is minimal so we don't care.
    // ... for now!
    //
    $targetpath;
    $targetlevel;
    if ($context->contextlevel === CONTEXT_COURSE) {
        $targetpath  = $context->path;
        $targetlevel = $context->contextlevel;
    } elseif ($context->path === "$base/{$context->id}") {
        $targetpath  = $context->path;
        $targetlevel = $context->contextlevel;
    } else {
        // Assumption: the course _must_ be our parent
        // If we ever see stuff nested further this needs to
        // change to do 1 query over the exploded path to
        // find out which one is the course
        $targetpath  = get_course_from_path($context->path);
        $targetlevel = CONTEXT_COURSE;
    }

    //
    // Role assignments in the context and below - and any rolecaps directly linked
    // because it's cheap to read rolecaps here over many
    // RAs
    //
    $sql = "SELECT ctx.path, ra.roleid, rc.capability, rc.permission
            FROM {$CFG->prefix}role_assignments ra
            JOIN {$CFG->prefix}context ctx
               ON ra.contextid=ctx.id
            LEFT OUTER JOIN {$CFG->prefix}role_capabilities rc
               ON (rc.roleid=ra.roleid AND rc.contextid=ra.contextid)
            WHERE ra.userid = $userid
                  AND (ctx.path = '$targetpath' OR ctx.path LIKE '{$targetpath}/%')
            ORDER BY ctx.depth, ctx.path";
    $rs = get_recordset_sql($sql);

    //
    // raparent collects paths & roles we need to walk up
    //
    // Here we only collect "different" role assignments
    // that - if found - we have to walk up the parenthood
    // to build the rdef.
    //
    // raparents array might have a few duplicates
    // which we'll later clear up
    //
    $raparents = array();
    $newroles  = array();
    $lastseen  = '';
    if ($rs->RecordCount()) {
        while ($ra = rs_fetch_next_record($rs)) {
            if ($lastseen !== $ra->path.':'.$ra->roleid) {
                // only add if is not a repeat caused
                // by capability join...
                // (this check is cheaper than in_array())
                $lastseen = $ra->path.':'.$ra->roleid;
                if (!isset($acc['ra'][$ra->path])) {
                    $acc['ra'][$ra->path] = array();
                }
                array_push($acc['ra'][$ra->path], $ra->roleid);
                if (!in_array($ra->roleid, $knownroles)) {
                    $newroles[] = $ra->roleid;
                    $parentids = explode('/', $ra->path);
                    array_pop($parentids); array_shift($parentids);
                    if (isset($raparents[$ra->roleid])) {
                        $raparents[$ra->roleid] = array_merge($raparents[$ra->roleid], $parentids);
                    } else {
                        $raparents[$ra->roleid] = $parentids;
                    }
                }
            }
            if (!empty($ra->capability)) {
                $k = "{$ra->path}:{$ra->roleid}";
                $acc['rdef'][$k][$ra->capability] = $ra->permission;
            }
        }
        $newroles = array_unique($newroles);
    }
    rs_close($rs);

    //
    // Walk up the tree to grab all the roledefs
    // of interest to our user...
    // NOTE: we use a series of IN clauses here - which
    // might explode on huge sites with very convoluted nesting of
    // categories... - extremely unlikely that the number of categories
    // and roletypes is so large that we hit the limits of IN()
    //
    if (count($raparents)) {
        $clauses = array();
        foreach ($raparents as $roleid=>$contexts) {
            $contexts = sql_intarray_to_in(array_unique($contexts));
            if ($contexts ==! '') {
                $clauses[] = "(roleid=$roleid AND contextid IN ($contexts))";
            }
        }
        $clauses = join(" OR ", $clauses);
        $sql = "SELECT ctx.path, rc.roleid, rc.capability, rc.permission
                FROM {$CFG->prefix}role_capabilities rc
                JOIN {$CFG->prefix}context ctx
                  ON rc.contextid=ctx.id
                WHERE $clauses
                ORDER BY ctx.depth ASC, ctx.path DESC, rc.roleid ASC ";

        $rs = get_recordset_sql($sql);

        if ($rs->RecordCount()) {
            while ($rd = rs_fetch_next_record($rs)) {
                $k = "{$rd->path}:{$rd->roleid}";
                $acc['rdef'][$k][$rd->capability] = $rd->permission;
            }
        }
        rs_close($rs);
    }

    //
    // Overrides for the relevant roles IN SUBCONTEXTS
    //
    // NOTE that we use IN() but the number of roles is
    // very limited.
    //
    $roleids = sql_intarray_to_in(array_merge($newroles, $knownroles));
    $sql = "SELECT ctx.path, rc.roleid,
                   rc.capability, rc.permission
            FROM {$CFG->prefix}context ctx
            JOIN {$CFG->prefix}role_capabilities rc
              ON rc.contextid=ctx.id
            WHERE ctx.path LIKE '{$targetpath}/%'
                  AND rc.roleid IN ($roleids)
            ORDER BY ctx.depth, ctx.path, rc.roleid";
    $rs = get_recordset_sql($sql);
    if ($rs->RecordCount()) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$rd->roleid}";
            $acc['rdef'][$k][$rd->capability] = $rd->permission;
        }
    }
    rs_close($rs);

    // TODO: compact capsets?

    error_log("loaded $targetpath");
    $acc['loaded'][] = $targetpath;

    return $acc;
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
 * @param $acc     access array
 *
 */
function get_role_access_bycontext($roleid, $context, $acc=NULL) {

    global $CFG;

    /* Get the relevant rolecaps into rdef
     * - relevant role caps
     *   - at ctx and above
     *   - below this ctx
     */

    if (is_null($acc)) {
        $acc           = array(); // named list
        $acc['ra']     = array();
        $acc['rdef']   = array();
        $acc['loaded'] = array();
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
    if ($rs->RecordCount()) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$roleid}";
            $acc['rdef'][$k][$rd->capability] = $rd->permission;
        }
    }
    rs_close($rs);

    return $acc;
}

/*
 * Load accessdata for a user
 * into the $ACCESS global
 *
 * Used by has_capability() - but feel free
 * to call it if you are about to run a BIG 
 * cron run across a bazillion users.
 *
 * TODO: share rdef tree to save mem
 *
 */ 
function load_user_accessdata($userid) {
    global $ACCESS,$CFG;

    if (!isset($ACCESS)) {
        $ACCESS = array();
    }
    $base = '/'.SYSCONTEXTID;

    $ad = get_user_access_sitewide($userid);
        
    //
    // provide "default role" & set 'dr'
    //
    $ad = get_role_access($CFG->defaultuserroleid, $ad);
    if (!isset($ad['ra'][$base])) {
        $ad['ra'][$base] = array($CFG->defaultuserroleid);
    } else {
        array_push($ad['ra'][$base], $CFG->defaultuserroleid);
    }
    $ad['dr'] = $CFG->defaultuserroleid;

    $ACCESS[$userid] = $ad;
    return true;
}

/**
 *  A convenience function to completely load all the capabilities 
 *  for the current user.   This is what gets called from login, for example.
 */
function load_all_capabilities() {
    global $USER,$CFG;

    $base = '/'.SYSCONTEXTID;

    if (isguestuser()) {
        $guest = get_guest_role();

        // Load the rdefs
        $USER->access = get_role_access($guest->id);
        // Put the ghost enrolment in place...
        $USER->access['ra'][$base] = array($guest->id);


    } else if (isloggedin()) {


        $ad = get_user_access_sitewide($USER->id);

        //
        // provide "default role" & set 'dr'
        //
        $ad = get_role_access($CFG->defaultuserroleid, $ad);
        if (!isset($ad['ra'][$base])) {
            $ad['ra'][$base] = array($CFG->defaultuserroleid);
        } else {
            array_push($ad['ra'][$base], $CFG->defaultuserroleid);
        }
        $ad['dr'] = $CFG->defaultuserroleid;

        $USER->access = $ad;

    } else {
        if ($roleid = get_notloggedin_roleid()) {
            $USER->access = get_role_access($roleid);
            $USER->access['ra'][$base] = array($roleid);
        }
    }

    // Timestamp to read 
    // dirty context timestamps
    $USER->access['time'] = time();

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

    error_log("reloading");
    // copy switchroles
    $sw = array();
    if (isset($USER->access['rsw'])) {
        $sw = $USER->access['rsw'];
        error_log(print_r($sw,1));
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
function load_temp_role($context, $roleid, $ad) {

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
    if ($rs->RecordCount()) {
        while ($rd = rs_fetch_next_record($rs)) {
            $k = "{$rd->path}:{$roleid}";
            $ad['rdef'][$k][$rd->capability] = $rd->permission;
        }
    }
    rs_close($rs);

    //
    // Say we loaded everything for the course context
    // - which we just did - if the user gets a proper
    // RA in this session, this data will need to be reloaded,
    // but that is handled by the complete accessdata reload
    //
    array_push($ad['loaded'], $context->path);

    //
    // Add the ghost RA
    //
    if (isset($ad['ra'][$context->path])) {
        array_push($ad['ra'][$context->path], $roleid);
    } else {
        $ad['ra'][$context->path] = array($roleid);
    }

    return $ad;
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
            }
            break;

        case CONTEXT_MODULE:
            $sql = "SELECT ctx.path, ctx.depth
                    FROM {$CFG->prefix}context           ctx
                    JOIN {$CFG->prefix}course_modules    cm
                      ON (cm.course=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    WHERE cm.id={$instanceid}";
            $p = get_record_sql($sql);
            $basepath  = $p->path;
            $basedepth = $p->depth;
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
            }
            break;
        case CONTEXT_USER:
            // default to basepath
            break;
        case CONTEXT_PERSONAL:
            // default to basepath
            break;
    }

    $context->depth = $basedepth+1;

    if ($id = insert_record('context',$context)) {
        // can't set the path till we know the id!
        set_field('context', 'path', $basepath . '/' . $id,
                  'id', $id);
        $c = get_context_instance_by_id($id);
        return $c;
    } else {
        debugging('Error: could not insert new context level "'.
                  s($contextlevel).'", instance "'.
                  s($instanceid).'".');
        return NULL;
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
            return $context;
        } else {
            debugging('Can not create system context');
            return NULL;
        }
    }
}
/**
 * Remove a context record and any dependent entries
 * @param $level
 * @param $instanceid
 *
 * @return bool properly deleted
 */
function delete_context($contextlevel, $instanceid) {
    if ($context = get_context_instance($contextlevel, $instanceid)) {
        mark_context_dirty($context->path);
        return delete_records('context', 'id', $context->id) &&
               delete_records('role_assignments', 'contextid', $context->id) &&
               delete_records('role_capabilities', 'contextid', $context->id);
    }
    return true;
}

/**
 * Remove stale context records
 *
 * @return bool
 */
function cleanup_contexts() {
    global $CFG;

    $sql = "  SELECT " . CONTEXT_COURSECAT . " AS level,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}course_categories AS t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_COURSECAT . "
            UNION
              SELECT " . CONTEXT_COURSE . " AS level,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}course AS t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_COURSE . "
            UNION
              SELECT " . CONTEXT_MODULE . " AS level,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}course_modules AS t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_MODULE . "
            UNION
              SELECT " . CONTEXT_USER . " AS level,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}user AS t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_USER . "
            UNION
              SELECT " . CONTEXT_BLOCK . " AS level,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}block_instance AS t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_BLOCK . "
            UNION
              SELECT " . CONTEXT_GROUP . " AS level,
                     c.instanceid AS instanceid
              FROM {$CFG->prefix}context c
              LEFT OUTER JOIN {$CFG->prefix}groups AS t
                ON c.instanceid = t.id
              WHERE t.id IS NULL AND c.contextlevel = " . CONTEXT_GROUP . "
           ";
    $rs = get_recordset_sql($sql);
    if ($rs->RecordCount()) {
        begin_sql();
        $tx = true;
        while ($tx && $ctx = rs_fetch_next_record($rs)) {
            $tx = $tx && delete_context($ctx->level, $ctx->instanceid);
        }
        rs_close($rs);
        if ($tx) {
            commit_sql();
            return true;
        }
        rollback_sql();
        return false;
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
function get_context_instance($contextlevel=NULL, $instance=0) {

    global $context_cache, $context_cache_id, $CONTEXT;
    static $allowed_contexts = array(CONTEXT_SYSTEM, CONTEXT_PERSONAL, CONTEXT_USER, CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_GROUP, CONTEXT_MODULE, CONTEXT_BLOCK);

    if ($contextlevel === 'clearcache') {
        // TODO: Remove for v2.0
        // No longer needed, but we'll catch it to avoid erroring out on custom code. 
        // This used to be a fix for MDL-9016 
        // "Restoring into existing course, deleting first 
        //  deletes context and doesn't recreate it"
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

        // MDL-10679, delete from context_rel if this role holds the last override in these contexts
        if ($contexts) {
            foreach ($contexts as $context) {
                if (!record_exists('role_capabilities', 'contextid', $context->contextid)) {
                    delete_records('context_rel', 'c1', $context->contextid);
                }
            }
        }

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
        $c = get_record('context', 'id', $contextid);
        /// MDL-10679 insert context rel here
        insert_context_rel ($c);
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

        // MDL-10679, if this is no more overrides for this context
        // delete entries from context where this context is a child
        if (!record_exists('role_capabilities', 'contextid', $contextid)) {
            delete_records('context_rel', 'c1', $contextid);
        }

    } else {
        // There is no need to delete from context_rel here because
        // this is only used for legacy, for now
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
                        if ($groups = groups_get_all_groups($context->instanceid)) {
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

        add_to_log($course->id, 'course', 'enrol', 'view.php?id='.$course->id, $user->id);

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

        case CONTEXT_PERSONAL:
            $name = get_string('personal');
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
                    and ( component = 'block/$block->name' or component = 'moodle')";
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
    $parentcontexts = explode(',', $parentcontexts);
    array_pop($parentcontexts); // and remove its own id

    return array_reverse($parentcontexts);
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
            if ($rs->RecordCount()) {
                while ($rec = rs_fetch_next_record($rs)) {
                    $records[$rec->id] = $rec;
                    $context_cache[$rec->contextlevel][$rec->instanceid] = $rec;
                }
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
            if ($rs->RecordCount()) {
                while ($rec = rs_fetch_next_record($rs)) {
                    $records[$rec->id] = $rec;
                    $context_cache[$rec->contextlevel][$rec->instanceid] = $rec;
                }
            }
            rs_close($rs);
            return $records;
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
            // Just get all the contexts except for CONTEXT_SYSTEM level
            // and hope we don't OOM in the process - don't cache
            $sql = 'SELECT c.*'.
                     'FROM '.$CFG->prefix.'context AS c '.
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
            $string = get_string($stringname, 'role');
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

/// Sorting out groups
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
            $groupsql = ' AND (' . $grouptest . ' OR ra.userid IN (' .
                    implode(',', array_keys($viewallgroupsusers)) . '))';
        } else {
            $groupsql = ' AND ' . $grouptest;
        }
    } else {
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
                LEFT OUTER JOIN {$CFG->prefix}user_lastaccess ul ON (ul.userid = u.id $coursesql1)";
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
 * @param int roleid (can also be an array of ints!)
 * @param int contextid
 * @param bool parent if true, get list of users assigned in higher context too
 * @param string fields - fields from user (u.) , role assignment (ra) or role (r.)
 * @param string sort  - sort from user (u.) , role assignment (ra) or role (r.)
 * @param bool gethidden - whether to fetch hidden enrolments too
 * @return array()
 */
function get_role_users($roleid, $context, $parent=false, $fields='', $sort='u.lastname ASC', $gethidden=true) {
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
        $roleselect = ' AND ra.roleid IN (' . join(',',$roleid) .')';
    } elseif (is_int($roleid)) {
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

// gets the custom name of the role in course
// TODO: proper documentation
function role_get_name($role, $context) {

    if ($r = get_record('role_names','roleid', $role->id,'contextid', $context->id)) {
        return format_string($r->text);
    } else {
        return format_string($role->name);
    }
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
 * Populate context.path and context.depth
 */
function build_context_path() {
    global $CFG;

    // Site
    $sitectx = get_field('context', 'id','contextlevel', CONTEXT_SYSTEM);
    $base = "/$sitectx";
    set_field('context', 'path',  $base, 'id', $sitectx);
    set_field('context', 'depth', 1,     'id', $sitectx);

    // Sitecourse
    $ctxid = get_field('context', 'id','contextlevel', CONTEXT_COURSE,
                       'instanceid', SITEID);
    set_field('context', 'path',  "$base/$ctxid", 'id', $ctxid);
    set_field('context', 'depth', 2,              'id', $ctxid);

    // Top level categories
    $sql = "UPDATE {$CFG->prefix}context
              SET depth=2, path='$base/' || id
            WHERE contextlevel=".CONTEXT_COURSECAT."
                  AND instanceid IN
               (SELECT id
                FROM {$CFG->prefix}course_categories
                WHERE depth=1)";
    execute_sql($sql, false);

    // Deeper categories - one query per depthlevel
    $maxdepth = get_field_sql("SELECT MAX(depth)
                               FROM {$CFG->prefix}course_categories");
    for ($n=2;$n<=$maxdepth;$n++) {
        $sql = "UPDATE {$CFG->prefix}context
                  SET depth=$n+1, path=it.ppath || '/' || id
                FROM (SELECT c.id AS instanceid, pctx.path AS ppath
                      FROM {$CFG->prefix}course_categories c
                      JOIN {$CFG->prefix}context pctx
                        ON (c.parent=pctx.instanceid
                            AND pctx.contextlevel=".CONTEXT_COURSECAT.")
                      WHERE c.depth=$n) it
                WHERE contextlevel=".CONTEXT_COURSECAT."
                      AND {$CFG->prefix}context.instanceid=it.instanceid";
        execute_sql($sql, false);
    }

    // Courses -- except sitecourse
    $sql = "UPDATE {$CFG->prefix}context
                  SET depth=it.pdepth+1, path=it.ppath || '/' || id
                FROM (SELECT c.id AS instanceid, pctx.path AS ppath,
                             pctx.depth as pdepth
                      FROM {$CFG->prefix}course c
                      JOIN {$CFG->prefix}context pctx
                        ON (c.category=pctx.instanceid
                            AND pctx.contextlevel=".CONTEXT_COURSECAT.")
                      WHERE c.id != ".SITEID.") it
                WHERE contextlevel=".CONTEXT_COURSE."
                      AND {$CFG->prefix}context.instanceid=it.instanceid";
        execute_sql($sql, false);

    // Module instances
    $sql = "UPDATE {$CFG->prefix}context
                  SET depth=it.pdepth+1, path=it.ppath || '/' || id
            FROM (SELECT cm.id AS instanceid, pctx.path AS ppath,
                         pctx.depth as pdepth
                  FROM {$CFG->prefix}course_modules cm
                  JOIN {$CFG->prefix}context pctx
                    ON (cm.course=pctx.instanceid
                        AND pctx.contextlevel=".CONTEXT_COURSE.")
                  ) it
            WHERE contextlevel=".CONTEXT_MODULE."
                  AND {$CFG->prefix}context.instanceid=it.instanceid";
        execute_sql($sql, false);

    // Blocks - non-pinned only
    $sql = "UPDATE {$CFG->prefix}context
              SET depth=it.pdepth+1, path=it.ppath || '/' || id
            FROM (SELECT bi.id AS instanceid, pctx.path AS ppath,
                         pctx.depth as pdepth
                  FROM {$CFG->prefix}block_instance bi
                  JOIN {$CFG->prefix}context pctx
                    ON (bi.pageid=pctx.instanceid
                        AND bi.pagetype='course-view'
                        AND pctx.contextlevel=".CONTEXT_COURSE.")
                  ) it
            WHERE contextlevel=".CONTEXT_BLOCK."
                  AND {$CFG->prefix}context.instanceid=it.instanceid";
    execute_sql($sql, false);

    // User
    $sql = "UPDATE {$CFG->prefix}context
              SET depth=2, path='$base/' || id
            WHERE contextlevel=".CONTEXT_USER."
                  AND instanceid IN
               (SELECT id
                FROM {$CFG->prefix}user)";
    execute_sql($sql, false);

    // Personal TODO

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
            SET path = '$newpath' || SUBSTR(path, {$len} +1)
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
    $ctx->instanceid   = $rec->id;
    $ctx->contextlevel = CONTEXT_COURSE;
    $rec->context = $ctx;
    return $rec;
}

/*
 * Fetch recent dirty contexts to know cheaply whether our $USER->access
 * is stale and needs to be reloaded.
 *
 * Uses config_plugins.
 *
 */
function get_dirty_contexts($time) {
    global $CFG;

    $sql = "SELECT name, value 
            FROM {$CFG->prefix}config_plugins
            WHERE plugin='accesslib/dirtycontexts'
                  AND CAST(value AS integer) > $time";
    if ($ctx = get_records_sql($sql)) {
        return $ctx;
    }
    return array();
}

/*
 * Mark a context as dirty (with timestamp)
 * so as to force reloading of the context.
 *
 */
function mark_context_dirty($path) {

    // only if it is a non-empty string
    if (is_string($path) && $path !== '') {
        // The timestamp is 2s in the past to cover for
        // - race conditions within the 1s granularity
        // - very small clock offsets in clusters (use ntpd!)
        set_config($path, time()-2, 'accesslib/dirtycontexts');
    }
}

/*
 * Cleanup all the old/stale dirty contexts.
 * Any context exceeding our session
 * timeout is stale. We only keep these for ongoing
 * sessions.
 *
 */
function cleanup_dirty_contexts() {
    global $CFG;
    
    $sql = "plugin='accesslib/dirtycontexts' AND
                  CAST(value to integer) < " . time() - $CFG->sessiontimeout;
    delete_records_select('config_plugins', $sql);
}

/*
 * Will walk the contextpath to answer whether
 * the contextpath is clean
 *
 * NOTE: it will *NOT* test the base path
 * as it assumes that the caller has checked
 * that beforehand.
 *
 * @param string path
 * @param obj/array dirty from get_dirty_contexts()
 *
 */
function is_contextpath_clean($path, $dirty) {

    $basepath = '/' . SYSCONTEXTID;

    // all clean, no dirt!
    if (count($dirty) === 0) {
        return true;
    }

    // is _this_ context dirty?
    if (isset($dirty[$path])) {
        return false;
    }
    while (preg_match('!^(/.+)/\d+$!', $path, $matches)) {
        $path = $matches[1];
        if ($path === $basepath) { 
            // we don't test basepath
            // assume caller did it already
            return true;
        }
        if (isset($dirty[$path])) {
            return false;
        }
    }    
    return true;
}

?>

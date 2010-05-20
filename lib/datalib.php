<?php // $Id$
/**
 * Library of functions for database manipulation.
 *
 * Other main libraries:
 * - weblib.php - functions that produce web output
 * - moodlelib.php - general-purpose Moodle functions
 * @author Martin Dougiamas and many others
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

 /// Some constants
 define('LASTACCESS_UPDATE_SECS', 60); /// Number of seconds to wait before
                                       /// updating lastaccess information in DB.

/**
 * Escape all dangerous characters in a data record
 *
 * $dataobject is an object containing needed data
 * Run over each field exectuting addslashes() function
 * to escape SQL unfriendly characters (e.g. quotes)
 * Handy when writing back data read from the database
 *
 * @param $dataobject Object containing the database record
 * @return object Same object with neccessary characters escaped
 */
function addslashes_object( $dataobject ) {
    $a = get_object_vars( $dataobject);
    foreach ($a as $key=>$value) {
      $a[$key] = addslashes( $value );
    }
    return (object)$a;
}

/// USER DATABASE ////////////////////////////////////////////////

/**
 * Returns $user object of the main admin user
 * primary admin = admin with lowest role_assignment id among admins
 * @uses $CFG
 * @return object(admin) An associative array representing the admin user.
 */
function get_admin () {
    static $myadmin;

    if (! isset($admin)) {
        if (! $admins = get_admins()) {
            return false;
        }
        $admin = reset($admins);//reset returns first element
    }
    return $admin;
}

/**
 * Returns list of all admins, using 1 DB query. It depends on DB schema v1.7
 * but does not depend on the v1.9 datastructures (context.path, etc).
 *
 * @uses $CFG
 * @return object
 */
function get_admins() {

    global $CFG;

    $sql = "SELECT ra.userid, SUM(rc.permission) AS permission, MIN(ra.id) AS adminid
            FROM " . $CFG->prefix . "role_capabilities rc
            JOIN " . $CFG->prefix . "context ctx
              ON ctx.id=rc.contextid
            JOIN " . $CFG->prefix . "role_assignments ra
              ON ra.roleid=rc.roleid AND ra.contextid=ctx.id
            WHERE ctx.contextlevel=10
              AND rc.capability IN ('moodle/site:config',
                                    'moodle/legacy:admin',
                                    'moodle/site:doanything')       
            GROUP BY ra.userid
            HAVING SUM(rc.permission) > 0";

    $sql = "SELECT u.*, ra.adminid
            FROM  " . $CFG->prefix . "user u
            JOIN ($sql) ra
              ON u.id=ra.userid
            ORDER BY ra.adminid ASC";

    return get_records_sql($sql);
}


function get_courses_in_metacourse($metacourseid) {
    global $CFG;

    $sql = "SELECT c.id,c.shortname,c.fullname FROM {$CFG->prefix}course c, {$CFG->prefix}course_meta mc WHERE mc.parent_course = $metacourseid
        AND mc.child_course = c.id ORDER BY c.shortname";

    return get_records_sql($sql);
}

function get_courses_notin_metacourse($metacourseid,$count=false) {

    global $CFG;

    if ($count) {
        $sql  = "SELECT COUNT(c.id)";
    } else {
        $sql = "SELECT c.id,c.shortname,c.fullname";
    }

    $alreadycourses = get_courses_in_metacourse($metacourseid);

    $sql .= " FROM {$CFG->prefix}course c WHERE ".((!empty($alreadycourses)) ? "c.id NOT IN (".implode(',',array_keys($alreadycourses)).")
    AND " : "")." c.id !=$metacourseid and c.id != ".SITEID." and c.metacourse != 1 ".((empty($count)) ? " ORDER BY c.shortname" : "");

    return get_records_sql($sql);
}

function count_courses_notin_metacourse($metacourseid) {
    global $CFG;

    $alreadycourses = get_courses_in_metacourse($metacourseid);

    $sql = "SELECT COUNT(c.id) AS notin FROM {$CFG->prefix}course c
             WHERE ".((!empty($alreadycourses)) ? "c.id NOT IN (".implode(',',array_keys($alreadycourses)).")
              AND " : "")." c.id !=$metacourseid and c.id != ".SITEID." and c.metacourse != 1";

    if (!$count = get_record_sql($sql)) {
        return 0;
    }

    return $count->notin;
}

/**
 * Search through course users
 *
 * If $coursid specifies the site course then this function searches
 * through all undeleted and confirmed users
 *
 * @uses $CFG
 * @uses SITEID
 * @param int $courseid The course in question.
 * @param int $groupid The group in question.
 * @param string $searchtext ?
 * @param string $sort ?
 * @param string $exceptions ?
 * @return object
 */
function search_users($courseid, $groupid, $searchtext, $sort='', $exceptions='') {
    global $CFG;

    $LIKE      = sql_ilike();
    $fullname  = sql_fullname('u.firstname', 'u.lastname');

    if (!empty($exceptions)) {
        $except = ' AND u.id NOT IN ('. $exceptions .') ';
    } else {
        $except = '';
    }

    if (!empty($sort)) {
        $order = ' ORDER BY '. $sort;
    } else {
        $order = '';
    }

    $select = 'u.deleted = \'0\' AND u.confirmed = \'1\'';

    if (!$courseid or $courseid == SITEID) {
        return get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                      FROM {$CFG->prefix}user u
                      WHERE $select
                          AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                          $except $order");
    } else {

        if ($groupid) {
//TODO:check. Remove group DB dependencies.
            return get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                          FROM {$CFG->prefix}user u,
                               {$CFG->prefix}groups_members gm
                          WHERE $select AND gm.groupid = '$groupid' AND gm.userid = u.id
                              AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                              $except $order");
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $courseid);
            $contextlists = get_related_contexts_string($context);
            $users = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email
                          FROM {$CFG->prefix}user u,
                               {$CFG->prefix}role_assignments ra
                          WHERE $select AND ra.contextid $contextlists AND ra.userid = u.id
                              AND ($fullname $LIKE '%$searchtext%' OR u.email $LIKE '%$searchtext%')
                              $except $order");
        }
        return $users;
    }
}


/**
 * Returns a list of all site users
 * Obsolete, just calls get_course_users(SITEID)
 *
 * @uses SITEID
 * @deprecated Use {@link get_course_users()} instead.
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @param string $exceptions A comma separated list of user->id to be skiped in the result returned by the function
 * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return object|false  {@link $USER} records or false if error.
 */
function get_site_users($sort='u.lastaccess DESC', $fields='*', $exceptions='', $limitfrom='', $limitnum='') {

    return get_course_users(SITEID, $sort, $exceptions, $fields, $limitfrom, $limitnum);
}


/**
 * Returns a subset of users
 *
 * @uses $CFG
 * @param bool $get If false then only a count of the records is returned
 * @param string $search A simple string to search for
 * @param bool $confirmed A switch to allow/disallow unconfirmed users
 * @param array(int) $exceptions A list of IDs to ignore, eg 2,4,5,8,9,10
 * @param string $sort A SQL snippet for the sorting criteria to use
 * @param string $firstinitial ?
 * @param string $lastinitial ?
 * @param string $page ?
 * @param string $recordsperpage ?
 * @param string $fields A comma separated list of fields to be returned from the chosen table.
 * @return object|false|int  {@link $USER} records unless get is false in which case the integer count of the records found is returned. False is returned if an error is encountered.
 */
function get_users($get=true, $search='', $confirmed=false, $exceptions='', $sort='firstname ASC',
                   $firstinitial='', $lastinitial='', $page='', $recordsperpage='', $fields='*', $extraselect='') {

    global $CFG;

    if ($get && !$recordsperpage) {
        debugging('Call to get_users with $get = true no $recordsperpage limit. ' .
                'On large installations, this will probably cause an out of memory error. ' .
                'Please think again and change your code so that it does not try to ' .
                'load so much data into memory.', DEBUG_DEVELOPER);
    }

    $LIKE      = sql_ilike();
    $fullname  = sql_fullname();

    $select = 'username <> \'guest\' AND deleted = 0';

    if (!empty($search)){
        $search = trim($search);
        $select .= " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%') ";
    }

    if ($confirmed) {
        $select .= ' AND confirmed = \'1\' ';
    }

    if ($exceptions) {
        $select .= ' AND id NOT IN ('. $exceptions .') ';
    }

    if ($firstinitial) {
        $select .= ' AND firstname '. $LIKE .' \''. $firstinitial .'%\'';
    }
    if ($lastinitial) {
        $select .= ' AND lastname '. $LIKE .' \''. $lastinitial .'%\'';
    }

    if ($extraselect) {
        $select .= " AND $extraselect ";
    }

    if ($get) {
        return get_records_select('user', $select, $sort, $fields, $page, $recordsperpage);
    } else {
        return count_records_select('user', $select);
    }
}


/**
 * shortdesc (optional)
 *
 * longdesc
 *
 * @uses $CFG
 * @param string $sort ?
 * @param string $dir ?
 * @param int $categoryid ?
 * @param int $categoryid ?
 * @param string $search ?
 * @param string $firstinitial ?
 * @param string $lastinitial ?
 * @returnobject {@link $USER} records
 * @todo Finish documenting this function
 */

function get_users_listing($sort='lastaccess', $dir='ASC', $page=0, $recordsperpage=0,
                           $search='', $firstinitial='', $lastinitial='', $extraselect='') {

    global $CFG;

    $LIKE      = sql_ilike();
    $fullname  = sql_fullname();

    $select = "deleted <> '1'";

    if (!empty($search)) {
        $search = trim($search);
        $select .= " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%' OR username='$search') ";
    }

    if ($firstinitial) {
        $select .= ' AND firstname '. $LIKE .' \''. $firstinitial .'%\' ';
    }

    if ($lastinitial) {
        $select .= ' AND lastname '. $LIKE .' \''. $lastinitial .'%\' ';
    }

    if ($extraselect) {
        $select .= " AND $extraselect ";
    }

    if ($sort) {
        $sort = ' ORDER BY '. $sort .' '. $dir;
    }

/// warning: will return UNCONFIRMED USERS
    return get_records_sql("SELECT id, username, email, firstname, lastname, city, country, lastaccess, confirmed, mnethostid
                              FROM {$CFG->prefix}user
                             WHERE $select $sort", $page, $recordsperpage);

}


/**
 * Full list of users that have confirmed their accounts.
 *
 * @uses $CFG
 * @return object
 */
function get_users_confirmed() {
    global $CFG;
    return get_records_sql("SELECT *
                              FROM {$CFG->prefix}user
                             WHERE confirmed = 1
                               AND deleted = 0
                               AND username <> 'guest'");
}


/// OTHER SITE AND COURSE FUNCTIONS /////////////////////////////////////////////


/**
 * Returns $course object of the top-level site.
 *
 * @return course  A {@link $COURSE} object for the site
 */
function get_site() {

    global $SITE;

    if (!empty($SITE->id)) {   // We already have a global to use, so return that
        return $SITE;
    }

    if ($course = get_record('course', 'category', 0)) {
        return $course;
    } else {
        return false;
    }
}

/**
 * Returns list of courses, for whole site, or category
 *
 * Returns list of courses, for whole site, or category
 * Important: Using c.* for fields is extremely expensive because
 *            we are using distinct. You almost _NEVER_ need all the fields
 *            in such a large SELECT
 *
 * @param    type description
 *
 */
function get_courses($categoryid="all", $sort="c.sortorder ASC", $fields="c.*") {

    global $USER, $CFG;

    if ($categoryid != "all" && is_numeric($categoryid)) {
        $categoryselect = "WHERE c.category = '$categoryid'";
    } else {
        $categoryselect = "";
    }

    if (empty($sort)) {
        $sortstatement = "";
    } else {
        $sortstatement = "ORDER BY $sort";
    }

    $visiblecourses = array();

    // pull out all course matching the cat
    if ($courses = get_records_sql("SELECT $fields,
                                    ctx.id AS ctxid, ctx.path AS ctxpath,
                                    ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                                    FROM {$CFG->prefix}course c
                                    JOIN {$CFG->prefix}context ctx
                                      ON (c.id = ctx.instanceid 
                                          AND ctx.contextlevel=".CONTEXT_COURSE.")
                                    $categoryselect
                                    $sortstatement")) {

        // loop throught them
        foreach ($courses as $course) {
            $course = make_context_subobj($course);
            if (isset($course->visible) && $course->visible <= 0) {
                // for hidden courses, require visibility check
                if (has_capability('moodle/course:viewhiddencourses', $course->context)) {
                    $visiblecourses [] = $course;
                }
            } else {
                $visiblecourses [] = $course;
            }
        }
    }
    return $visiblecourses;

/*
    $teachertable = "";
    $visiblecourses = "";
    $sqland = "";
    if (!empty($categoryselect)) {
        $sqland = "AND ";
    }
    if (!empty($USER->id)) {  // May need to check they are a teacher
        if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
            $visiblecourses = "$sqland ((c.visible > 0) OR t.userid = '$USER->id')";
            $teachertable = "LEFT JOIN {$CFG->prefix}user_teachers t ON t.course = c.id";
        }
    } else {
        $visiblecourses = "$sqland c.visible > 0";
    }

    if ($categoryselect or $visiblecourses) {
        $selectsql = "{$CFG->prefix}course c $teachertable WHERE $categoryselect $visiblecourses";
    } else {
        $selectsql = "{$CFG->prefix}course c $teachertable";
    }

    $extrafield = str_replace('ASC','',$sort);
    $extrafield = str_replace('DESC','',$extrafield);
    $extrafield = trim($extrafield);
    if (!empty($extrafield)) {
        $extrafield = ','.$extrafield;
    }
    return get_records_sql("SELECT ".((!empty($teachertable)) ? " DISTINCT " : "")." $fields $extrafield FROM $selectsql ".((!empty($sort)) ? "ORDER BY $sort" : ""));
    */
}


/**
 * Returns list of courses, for whole site, or category
 *
 * Similar to get_courses, but allows paging
 * Important: Using c.* for fields is extremely expensive because
 *            we are using distinct. You almost _NEVER_ need all the fields
 *            in such a large SELECT
 *
 * @param    type description
 *
 */
function get_courses_page($categoryid="all", $sort="c.sortorder ASC", $fields="c.*",
                          &$totalcount, $limitfrom="", $limitnum="") {

    global $USER, $CFG;

    $categoryselect = "";
    if ($categoryid != "all" && is_numeric($categoryid)) {
        $categoryselect = "WHERE c.category = '$categoryid'";
    } else {
        $categoryselect = "";
    }

    // pull out all course matching the cat
    $visiblecourses = array();
    if (!($rs = get_recordset_sql("SELECT $fields,
                                          ctx.id AS ctxid, ctx.path AS ctxpath,
                                          ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                                   FROM {$CFG->prefix}course c
                                   JOIN {$CFG->prefix}context ctx
                                     ON (c.id = ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                                   $categoryselect
                                   ORDER BY $sort"))) {
        return $visiblecourses;
    }
    $totalcount = 0;

    if (!$limitfrom) {
        $limitfrom = 0;
    }

    // iteration will have to be done inside loop to keep track of the limitfrom and limitnum
    while ($course = rs_fetch_next_record($rs)) {
        $course = make_context_subobj($course);
        if ($course->visible <= 0) {
            // for hidden courses, require visibility check
            if (has_capability('moodle/course:viewhiddencourses', $course->context)) {
                $totalcount++;
                if ($totalcount > $limitfrom && (!$limitnum or count($visiblecourses) < $limitnum)) {
                    $visiblecourses [] = $course;
                }
            }
        } else {
            $totalcount++;
            if ($totalcount > $limitfrom && (!$limitnum or count($visiblecourses) < $limitnum)) {
                $visiblecourses [] = $course;
            }
        }
    }
    rs_close($rs);
    return $visiblecourses;

/**

    $categoryselect = "";
    if ($categoryid != "all" && is_numeric($categoryid)) {
        $categoryselect = "c.category = '$categoryid'";
    }

    $teachertable = "";
    $visiblecourses = "";
    $sqland = "";
    if (!empty($categoryselect)) {
        $sqland = "AND ";
    }
    if (!empty($USER) and !empty($USER->id)) {  // May need to check they are a teacher
        if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
            $visiblecourses = "$sqland ((c.visible > 0) OR t.userid = '$USER->id')";
            $teachertable = "LEFT JOIN {$CFG->prefix}user_teachers t ON t.course=c.id";
        }
    } else {
        $visiblecourses = "$sqland c.visible > 0";
    }

    if ($limitfrom !== "") {
        $limit = sql_paging_limit($limitfrom, $limitnum);
    } else {
        $limit = "";
    }

    $selectsql = "{$CFG->prefix}course c $teachertable WHERE $categoryselect $visiblecourses";

    $totalcount = count_records_sql("SELECT COUNT(DISTINCT c.id) FROM $selectsql");

    return get_records_sql("SELECT $fields FROM $selectsql ".((!empty($sort)) ? "ORDER BY $sort" : "")." $limit");
    */
}

/*
 * Retrieve course records with the course managers and other related records
 * that we need for print_course(). This allows print_courses() to do its job
 * in a constant number of DB queries, regardless of the number of courses,
 * role assignments, etc.
 *
 * The returned array is indexed on c.id, and each course will have
 * - $course->context - a context obj
 * - $course->managers - array containing RA objects that include a $user obj
 *                       with the minimal fields needed for fullname()
 *
 */
function get_courses_wmanagers($categoryid=0, $sort="c.sortorder ASC", $fields=array()) {
    /*
     * The plan is to
     *
     * - Grab the courses JOINed w/context
     *
     * - Grab the interesting course-manager RAs
     *   JOINed with a base user obj and add them to each course
     *
     * So as to do all the work in 2 DB queries. The RA+user JOIN
     * ends up being pretty expensive if it happens over _all_
     * courses on a large site. (Are we surprised!?)
     *
     * So this should _never_ get called with 'all' on a large site.
     *
     */
    global $USER, $CFG;

    $allcats = false; // bool flag
    if ($categoryid === 'all') {
        $categoryclause   = '';
        $allcats = true;
    } elseif (is_numeric($categoryid)) {
        $categoryclause = "c.category = $categoryid";
    } else {
        debugging("Could not recognise categoryid = $categoryid");
        $categoryclause = '';
    }

    $basefields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'teacher', 'teachers', 'student', 'students',
                        'guest', 'startdate', 'visible',
                        'newsitems',  'cost', 'enrol',
                        'groupmode', 'groupmodeforce');

    if (!is_null($fields) && is_string($fields)) {
        if (empty($fields)) {
            $fields = $basefields;
        } else {
            // turn the fields from a string to an array that
            // get_user_courses_bycap() will like...
            $fields = explode(',',$fields);
            $fields = array_map('trim', $fields);
            $fields = array_unique(array_merge($basefields, $fields));
        }
    } elseif (is_array($fields)) {
        $fields = array_merge($basefields,$fields);
    }
    $coursefields = 'c.' .join(',c.', $fields);

    if (empty($sort)) {
        $sortstatement = "";
    } else {
        $sortstatement = "ORDER BY $sort";
    }

    $where = 'WHERE c.id != ' . SITEID;
    if ($categoryclause !== ''){
        $where = "$where AND $categoryclause";
    }

    // pull out all courses matching the cat
    $sql = "SELECT $coursefields,
                   ctx.id AS ctxid, ctx.path AS ctxpath,
                   ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
            FROM {$CFG->prefix}course c
            JOIN {$CFG->prefix}context ctx
                 ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                 $where
                 $sortstatement";

    $catpaths = array();
    $catpath  = NULL;
    if ($courses = get_records_sql($sql)) {
        // loop on courses materialising
        // the context, and prepping data to fetch the
        // managers efficiently later...
        foreach ($courses as $k => $course) {
            $courses[$k] = make_context_subobj($courses[$k]);
            $courses[$k]->managers = array();
            if ($allcats === false) {
                // single cat, so take just the first one...
                if ($catpath === NULL) {
                    $catpath = preg_replace(':/\d+$:', '',$courses[$k]->context->path);
                }
            } else {
                // chop off the contextid of the course itself
                // like dirname() does...
                $catpaths[] = preg_replace(':/\d+$:', '',$courses[$k]->context->path);
            }
        }
    } else {
        return array(); // no courses!
    }

    $CFG->coursemanager = trim($CFG->coursemanager);
    if (empty($CFG->coursemanager)) {
        return $courses;
    }

    $managerroles = split(',', $CFG->coursemanager);
    $catctxids = '';
    if (count($managerroles)) {
        if ($allcats === true) {
            $catpaths  = array_unique($catpaths);
            $ctxids = array();
            foreach ($catpaths as $cpath) {
                $ctxids = array_merge($ctxids, explode('/',substr($cpath,1)));
            }
            $ctxids = array_unique($ctxids);
            $catctxids = implode( ',' , $ctxids);
            unset($catpaths);
            unset($cpath);
        } else {
            // take the ctx path from the first course
            // as all categories will be the same...
            $catpath = substr($catpath,1);
            $catpath = preg_replace(':/\d+$:','',$catpath);
            $catctxids = str_replace('/',',',$catpath);
        }
        if ($categoryclause !== '') {
            $categoryclause = "AND $categoryclause";
        }
        /*
         * Note: Here we use a LEFT OUTER JOIN that can
         * "optionally" match to avoid passing a ton of context
         * ids in an IN() clause. Perhaps a subselect is faster.
         *
         * In any case, this SQL is not-so-nice over large sets of
         * courses with no $categoryclause.
         *
         */
        $sql = "SELECT ctx.path, ctx.instanceid, ctx.contextlevel,
                       ra.hidden,
                       r.id AS roleid, r.name as rolename,
                       u.id AS userid, u.firstname, u.lastname
                FROM {$CFG->prefix}role_assignments ra
                JOIN {$CFG->prefix}context ctx
                  ON ra.contextid = ctx.id
                JOIN {$CFG->prefix}user u
                  ON ra.userid = u.id
                JOIN {$CFG->prefix}role r
                  ON ra.roleid = r.id
                LEFT OUTER JOIN {$CFG->prefix}course c
                  ON (ctx.instanceid=c.id AND ctx.contextlevel=".CONTEXT_COURSE.")
                WHERE ( c.id IS NOT NULL";
        // under certain conditions, $catctxids is NULL
        if($catctxids == NULL){
            $sql .= ") ";
        }else{
            $sql .= " OR ra.contextid  IN ($catctxids) )";
        }

        $sql .= "AND ra.roleid IN ({$CFG->coursemanager})
                      $categoryclause
                ORDER BY r.sortorder ASC, ctx.contextlevel ASC, ra.sortorder ASC";
        $rs = get_recordset_sql($sql);

        // This loop is fairly stupid as it stands - might get better
        // results doing an initial pass clustering RAs by path.
        while ($ra = rs_fetch_next_record($rs)) {
            $user = new StdClass;
            $user->id        = $ra->userid;    unset($ra->userid);
            $user->firstname = $ra->firstname; unset($ra->firstname);
            $user->lastname  = $ra->lastname;  unset($ra->lastname);
            $ra->user = $user;
            if ($ra->contextlevel == CONTEXT_SYSTEM) {
                foreach ($courses as $k => $course) {
                    $courses[$k]->managers[] = $ra;
                }
            } elseif ($ra->contextlevel == CONTEXT_COURSECAT) {
                if ($allcats === false) {
                    // It always applies
                    foreach ($courses as $k => $course) {
                        $courses[$k]->managers[] = $ra;
                    }
                } else {
                    foreach ($courses as $k => $course) {
                        // Note that strpos() returns 0 as "matched at pos 0"
                        if (strpos($course->context->path, $ra->path.'/')===0) {
                            // Only add it to subpaths
                            $courses[$k]->managers[] = $ra;
                        }
                    }
                }
            } else { // course-level
                if(!array_key_exists($ra->instanceid, $courses)) {
                    //this course is not in a list, probably a frontpage course
                    continue;
                }
                $courses[$ra->instanceid]->managers[] = $ra;
            }
        }
        rs_close($rs);
    }

    return $courses;
}

/**
 * Convenience function - lists courses that a user has access to view.
 *
 * For admins and others with access to "every" course in the system, we should
 * try to get courses with explicit RAs.
 *
 * NOTE: this function is heavily geared towards the perspective of the user
 *       passed in $userid. So it will hide courses that the user cannot see
 *       (for any reason) even if called from cron or from another $USER's
 *       perspective.
 *
 *       If you really want to know what courses are assigned to the user,
 *       without any hiding or scheming, call the lower-level
 *       get_user_courses_bycap().
 *
 *
 * Notes inherited from get_user_courses_bycap():
 *
 * - $fields is an array of fieldnames to ADD
 *   so name the fields you really need, which will
 *   be added and uniq'd
 *
 * - the course records have $c->context which is a fully
 *   valid context object. Saves you a query per course!
 *
 * @uses $CFG,$USER
 * @param int $userid The user of interest
 * @param string $sort the sortorder in the course table
 * @param array $fields - names of _additional_ fields to return (also accepts a string)
 * @param bool $doanything True if using the doanything flag
 * @param int $limit Maximum number of records to return, or 0 for unlimited
 * @return array {@link $COURSE} of course objects
 */
function get_my_courses($userid, $sort='visible DESC,sortorder ASC', $fields=NULL, $doanything=false,$limit=0) {

    global $CFG,$USER;

    // Guest's do not have any courses
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    if (has_capability('moodle/legacy:guest',$sitecontext,$userid,false)) {
        return(array());
    }

    $basefields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'teacher', 'teachers', 'student', 'students',
                        'guest', 'startdate', 'visible',
                        'newsitems',  'cost', 'enrol',
                        'groupmode', 'groupmodeforce');

    if (!is_null($fields) && is_string($fields)) {
        if (empty($fields)) {
            $fields = $basefields;
        } else {
            // turn the fields from a string to an array that
            // get_user_courses_bycap() will like...
            $fields = explode(',',$fields);
            $fields = array_map('trim', $fields);
            $fields = array_unique(array_merge($basefields, $fields));
        }
    } elseif (is_array($fields)) {
        $fields = array_unique(array_merge($basefields, $fields));
    } else {
        $fields = $basefields;
    }

    $orderby = '';
    $sort    = trim($sort);
    if (!empty($sort)) {
        $rawsorts = explode(',', $sort);
        $sorts = array();
        foreach ($rawsorts as $rawsort) {
            $rawsort = trim($rawsort);
            if (strpos($rawsort, 'c.') === 0) {
                $rawsort = substr($rawsort, 2);
            }
            $sorts[] = trim($rawsort);
        }
        $sort = 'c.'.implode(',c.', $sorts);
        $orderby = "ORDER BY $sort";
    }

    //
    // Logged-in user - Check cached courses
    //
    // NOTE! it's a _string_ because
    // - it's all we'll ever use
    // - it serialises much more compact than an array
    //   this a big concern here - cost of serialise
    //   and unserialise gets huge as the session grows
    //
    // If the courses are too many - it won't be set
    // for large numbers of courses, caching in the session
    // has marginal benefits (costs too much, not
    // worthwhile...) and we may hit SQL parser limits
    // because we use IN()
    //
    if ($userid === $USER->id) {
        if (isset($USER->loginascontext)
            && $USER->loginascontext->contextlevel == CONTEXT_COURSE) {
            // list _only_ this course
            // anything else is asking for trouble...
            $courseids = $USER->loginascontext->instanceid;
        } elseif (isset($USER->mycourses)
                  && is_string($USER->mycourses)) {
            if ($USER->mycourses === '') {
                // empty str means: user has no courses
                // ... so do the easy thing...
                return array();
            } else {
                $courseids = $USER->mycourses;
            }
        }
        if (isset($courseids)) {
            // The data massaging here MUST be kept in sync with
            // get_user_courses_bycap() so we return
            // the same...
            // (but here we don't need to check has_cap)
            $coursefields = 'c.' .join(',c.', $fields);
            $sql = "SELECT $coursefields,
                           ctx.id AS ctxid, ctx.path AS ctxpath,
                           ctx.depth as ctxdepth, ctx.contextlevel AS ctxlevel,
                           cc.path AS categorypath
                    FROM {$CFG->prefix}course c
                    JOIN {$CFG->prefix}course_categories cc
                      ON c.category=cc.id
                    JOIN {$CFG->prefix}context ctx
                      ON (c.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
                    WHERE c.id IN ($courseids)
                    $orderby";
            $rs = get_recordset_sql($sql);
            $courses = array();
            $cc = 0; // keep count
            while ($c = rs_fetch_next_record($rs)) {
                // build the context obj
                $c = make_context_subobj($c);

                if ($limit > 0 && $cc >= $limit) {
                    break;
                }
                
                $courses[$c->id] = $c;
                $cc++;
            }
            rs_close($rs);
            return $courses;
        }
    }

    // Non-cached - get accessinfo
    if ($userid === $USER->id && isset($USER->access)) {
        $accessinfo = $USER->access;
    } else {
        $accessinfo = get_user_access_sitewide($userid);
    }


    $courses = get_user_courses_bycap($userid, 'moodle/course:view', $accessinfo,
                                      $doanything, $sort, $fields,
                                      $limit);

    $cats = NULL;
    // If we have to walk category visibility
    // to eval course visibility, get the categories
    if (empty($CFG->allowvisiblecoursesinhiddencategories)) {
        $sql = "SELECT cc.id, cc.path, cc.visible,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth as ctxdepth, ctx.contextlevel AS ctxlevel
                 FROM {$CFG->prefix}course_categories cc
                 JOIN {$CFG->prefix}context ctx ON (cc.id = ctx.instanceid)
                WHERE ctx.contextlevel = ".CONTEXT_COURSECAT."
             ORDER BY cc.id";
        $rs = get_recordset_sql($sql);

        // Using a temporary array instead of $cats here, to avoid a "true" result when isnull($cats) further down
        $categories = array();
        while ($course_cat = rs_fetch_next_record($rs)) {
            // build the context obj
            $course_cat = make_context_subobj($course_cat);
            $categories[$course_cat->id] = $course_cat;
        }
        rs_close($rs);

        if (!empty($categories)) {
            $cats = $categories;
        }

        unset($course_cat);
    }
    //
    // Strangely, get_my_courses() is expected to return the
    // array keyed on id, which messes up the sorting
    // So do that, and also cache the ids in the session if appropriate
    //
    $kcourses = array();
    $courses_count = count($courses);
    $cacheids = NULL;
    $vcatpaths = array();
    if ($userid === $USER->id && $courses_count < 500) {
        $cacheids = array();
    }
    for ($n=0; $n<$courses_count; $n++) {

        //
        // Check whether $USER (not $userid) can _actually_ see them
        // Easy if $CFG->allowvisiblecoursesinhiddencategories
        // is set, and we don't have to care about categories.
        // Lots of work otherwise... (all in mem though!)
        //
        $cansee = false;
        if (is_null($cats)) { // easy rules!
            if ($courses[$n]->visible == true) {
                $cansee = true;
            } elseif (has_capability('moodle/course:viewhiddencourses',
                                     $courses[$n]->context, $USER->id)) {
                $cansee = true;
            }
        } else {
            //
            // Is the cat visible?
            // we have to assume it _is_ visible
            // so we can shortcut when we find a hidden one
            //
            $viscat = true;
            $cpath = $courses[$n]->categorypath;
            if (isset($vcatpaths[$cpath])) {
                $viscat = $vcatpaths[$cpath];
            } else {
                $cpath = substr($cpath,1); // kill leading slash
                $cpath = explode('/',$cpath);
                $ccct  = count($cpath);
                for ($m=0;$m<$ccct;$m++) {
                    $ccid = $cpath[$m];
                    if ($cats[$ccid]->visible==false) {
                        $viscat = false;
                        break;
                    }
                }
                $vcatpaths[$courses[$n]->categorypath] = $viscat;
            }

            //
            // Perhaps it's actually visible to $USER
            // check moodle/category:viewhiddencategories
            //
            // The name isn't obvious, but the description says
            // "See hidden categories" so the user shall see...
            // But also check if the allowvisiblecoursesinhiddencategories setting is true, and check for course visibility
            if ($viscat === false) {
                $catctx = $cats[$courses[$n]->category]->context;
                if (has_capability('moodle/category:viewhiddencategories', $catctx, $USER->id)) {
                    $vcatpaths[$courses[$n]->categorypath] = true;
                    $viscat = true;
                } elseif ($CFG->allowvisiblecoursesinhiddencategories && $courses[$n]->visible == true) {
                    $viscat = true;
                }
            }

            //
            // Decision matrix
            //
            if ($viscat === true) {
                if ($courses[$n]->visible == true) {
                    $cansee = true;
                } elseif (has_capability('moodle/course:viewhiddencourses',
                                        $courses[$n]->context, $USER->id)) {
                    $cansee = true;
                }
            }
        }
        if ($cansee === true) {
            $kcourses[$courses[$n]->id] = $courses[$n];
            if (is_array($cacheids)) {
                $cacheids[] = $courses[$n]->id;
            }
        }
    }
    if (is_array($cacheids)) {
        // Only happens
        // - for the logged in user
        // - below the threshold (500)
        // empty string is _valid_
        $USER->mycourses = join(',',$cacheids);
    } elseif ($userid === $USER->id && isset($USER->mycourses)) {
        // cheap sanity check
        unset($USER->mycourses);
    }

    return $kcourses;
}

/**
 * A list of courses that match a search
 *
 * @uses $CFG
 * @param array $searchterms ?
 * @param string $sort ?
 * @param int $page ?
 * @param int $recordsperpage ?
 * @param int $totalcount Passed in by reference. ?
 * @return object {@link $COURSE} records
 */
function get_courses_search($searchterms, $sort='fullname ASC', $page=0, $recordsperpage=50, &$totalcount) {

    global $CFG;

    //to allow case-insensitive search for postgesql
    if ($CFG->dbfamily == 'postgres') {
        $LIKE = 'ILIKE';
        $NOTLIKE = 'NOT ILIKE';   // case-insensitive
        $REGEXP = '~*';
        $NOTREGEXP = '!~*';
    } else {
        $LIKE = 'LIKE';
        $NOTLIKE = 'NOT LIKE';
        $REGEXP = 'REGEXP';
        $NOTREGEXP = 'NOT REGEXP';
    }

    $fullnamesearch = '';
    $summarysearch = '';
    $idnumbersearch = '';
    $shortnamesearch = '';

    foreach ($searchterms as $searchterm) {

        $NOT = ''; /// Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle
                   /// will use it to simulate the "-" operator with LIKE clause

    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE (or NOT LIKE) queries
        if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql') {
            if (substr($searchterm, 0, 1) == '-') {
                $NOT = ' NOT ';
            }
            $searchterm = trim($searchterm, '+-');
        }

        if ($fullnamesearch) {
            $fullnamesearch .= ' AND ';
        }
        if ($summarysearch) {
            $summarysearch .= ' AND ';
        }
        if ($idnumbersearch) {
            $idnumbersearch .= ' AND ';
        }
        if ($shortnamesearch) {
            $shortnamesearch .= ' AND ';
        }

        if (substr($searchterm,0,1) == '+') {
            $searchterm      = substr($searchterm,1);
            $summarysearch  .= " c.summary $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $fullnamesearch .= " c.fullname $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $idnumbersearch  .= " c.idnumber $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $shortnamesearch  .= " c.shortname $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm      = substr($searchterm,1);
            $summarysearch  .= " c.summary $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $fullnamesearch .= " c.fullname $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $idnumbersearch .= " c.idnumber $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $shortnamesearch .= " c.shortname $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $summarysearch .= ' summary '. $NOT . $LIKE .' \'%'. $searchterm .'%\' ';
            $fullnamesearch .= ' fullname '. $NOT . $LIKE .' \'%'. $searchterm .'%\' ';
            $idnumbersearch .= ' idnumber '. $NOT . $LIKE .' \'%'. $searchterm .'%\' ';
            $shortnamesearch .= ' shortname '. $NOT . $LIKE .' \'%'. $searchterm .'%\' ';
        }

    }

    $sql = "SELECT c.*,
                   ctx.id AS ctxid, ctx.path AS ctxpath,
                   ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
            FROM {$CFG->prefix}course c
            JOIN {$CFG->prefix}context ctx
             ON (c.id = ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSE.")
            WHERE (( $fullnamesearch ) OR ( $summarysearch ) OR ( $idnumbersearch ) OR ( $shortnamesearch ))
                  AND category > 0
            ORDER BY " . $sort;

    $courses = array();

    if ($rs = get_recordset_sql($sql)) {


        // Tiki pagination
        $limitfrom = $page * $recordsperpage;
        $limitto   = $limitfrom + $recordsperpage;
        $c = 0; // counts how many visible courses we've seen

        while ($course = rs_fetch_next_record($rs)) {
            $course = make_context_subobj($course);
            if ($course->visible || has_capability('moodle/course:viewhiddencourses', $course->context)) {
                // Don't exit this loop till the end
                // we need to count all the visible courses
                // to update $totalcount
                if ($c >= $limitfrom && $c < $limitto) {
                    $courses[] = $course;
                }
                $c++;
            }
        }
    }

    // our caller expects 2 bits of data - our return
    // array, and an updated $totalcount
    $totalcount = $c;
    return $courses;
}


/**
 * Returns a sorted list of categories. Each category object has a context
 * property that is a context object.
 *
 * When asking for $parent='none' it will return all the categories, regardless
 * of depth. Wheen asking for a specific parent, the default is to return
 * a "shallow" resultset. Pass false to $shallow and it will return all
 * the child categories as well.
 *
 *
 * @param string $parent The parent category if any
 * @param string $sort the sortorder
 * @param bool   $shallow - set to false to get the children too
 * @return array of categories
 */
function get_categories($parent='none', $sort=NULL, $shallow=true) {
    global $CFG;

    if ($sort === NULL) {
        $sort = 'ORDER BY cc.sortorder ASC';
    } elseif ($sort ==='') {
        // leave it as empty
    } else {
        $sort = "ORDER BY $sort";
    }

    if ($parent === 'none') {
        $sql = "SELECT cc.*,
                      ctx.id AS ctxid, ctx.path AS ctxpath,
                      ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                FROM {$CFG->prefix}course_categories cc
                JOIN {$CFG->prefix}context ctx
                  ON cc.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT."
                $sort";
    } elseif ($shallow) {
        $parent = (int)$parent;
        $sql = "SELECT cc.*,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                FROM {$CFG->prefix}course_categories cc
                JOIN {$CFG->prefix}context ctx
                  ON cc.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT."
                WHERE cc.parent=$parent
                $sort";
    } else {
        $parent = (int)$parent;
        $sql = "SELECT cc.*,
                       ctx.id AS ctxid, ctx.path AS ctxpath,
                       ctx.depth AS ctxdepth, ctx.contextlevel AS ctxlevel
                FROM {$CFG->prefix}course_categories cc
                JOIN {$CFG->prefix}context ctx
                  ON cc.id=ctx.instanceid AND ctx.contextlevel=".CONTEXT_COURSECAT."
                JOIN {$CFG->prefix}course_categories ccp
                     ON (cc.path LIKE ".sql_concat('ccp.path',"'%'").")
                WHERE ccp.id=$parent
                $sort";
    }
    $categories = array();

    if( $rs = get_recordset_sql($sql) ){
        while ($cat = rs_fetch_next_record($rs)) {
            $cat = make_context_subobj($cat);
            if ($cat->visible || has_capability('moodle/category:viewhiddencategories',$cat->context)) {
                $categories[$cat->id] = $cat;
            }
        }
    }
    return $categories;
}


/**
 * Returns an array of category ids of all the subcategories for a given
 * category.
 * @param $catid - The id of the category whose subcategories we want to find.
 * @return array of category ids.
 */
function get_all_subcategories($catid) {

    $subcats = array();

    if ($categories = get_records('course_categories', 'parent', $catid)) {
        foreach ($categories as $cat) {
            array_push($subcats, $cat->id);
            $subcats = array_merge($subcats, get_all_subcategories($cat->id));
        }
    }
    return $subcats;
}


/**
* This recursive function makes sure that the courseorder is consecutive
*
* @param    type description
*
* $n is the starting point, offered only for compatilibity -- will be ignored!
* $safe (bool) prevents it from assuming category-sortorder is unique, used to upgrade
*       safely from 1.4 to 1.5
*/
function fix_course_sortorder($categoryid=0, $n=0, $safe=0, $depth=0, $path='') {

    global $CFG;

    $count = 0;

    $catgap    = 1000; // "standard" category gap
    $tolerance = 200;  // how "close" categories can get

    if ($categoryid > 0){
        // update depth and path
        $cat   = get_record('course_categories', 'id', $categoryid);
        if ($cat->parent == 0) {
            $depth = 0;
            $path  = '';
        } else if ($depth == 0 ) { // doesn't make sense; get from DB
            // this is only called if the $depth parameter looks dodgy
            $parent = get_record('course_categories', 'id', $cat->parent);
            $path  = $parent->path;
            $depth = $parent->depth;
        }
        $path  = $path . '/' . $categoryid;
        $depth = $depth + 1;

        if ($cat->path !== $path) {
            set_field('course_categories', 'path',  addslashes($path),  'id', $categoryid);
        }
        if ($cat->depth != $depth) {
            set_field('course_categories', 'depth', $depth, 'id', $categoryid);
        }
    }

    // get some basic info about courses in the category
    $info = get_record_sql('SELECT MIN(sortorder) AS min,
                                   MAX(sortorder) AS max,
                                   COUNT(sortorder)  AS count
                            FROM ' . $CFG->prefix . 'course
                            WHERE category=' . $categoryid);
    if (is_object($info)) { // no courses?
        $max   = $info->max;
        $count = $info->count;
        $min   = $info->min;
        unset($info);
    }

    if ($categoryid > 0 && $n==0) { // only passed category so don't shift it
        $n = $min;
    }

    // $hasgap flag indicates whether there's a gap in the sequence
    $hasgap    = false;
    if ($max-$min+1 != $count) {
        $hasgap = true;
    }

    // $mustshift indicates whether the sequence must be shifted to
    // meet its range
    $mustshift = false;
    if ($min < $n-$tolerance || $min > $n+$tolerance+$catgap ) {
        $mustshift = true;
    }

    // actually sort only if there are courses,
    // and we meet one ofthe triggers:
    //  - safe flag
    //  - they are not in a continuos block
    //  - they are too close to the 'bottom'
    if ($count && ( $safe || $hasgap || $mustshift ) ) {
        // special, optimized case where all we need is to shift
        if ( $mustshift && !$safe && !$hasgap) {
            $shift = $n + $catgap - $min;
            if ($shift < $count) {
                $shift = $count + $catgap;
            }
            // UPDATE course SET sortorder=sortorder+$shift
            execute_sql("UPDATE {$CFG->prefix}course
                         SET sortorder=sortorder+$shift
                         WHERE category=$categoryid", 0);
            $n = $n + $catgap + $count;

        } else { // do it slowly
            $n = $n + $catgap;
            // if the new sequence overlaps the current sequence, lack of transactions
            // will stop us -- shift things aside for a moment...
            if ($safe || ($n >= $min && $n+$count+1 < $min && $CFG->dbfamily==='mysql')) {
                $shift = $max + $n + 1000;
                execute_sql("UPDATE {$CFG->prefix}course
                         SET sortorder=sortorder+$shift
                         WHERE category=$categoryid", 0);
            }

            $courses = get_courses($categoryid, 'c.sortorder ASC', 'c.id,c.sortorder');
            begin_sql();
            $tx = true; // transaction sanity
            foreach ($courses as $course) {
                if ($tx && $course->sortorder != $n ) { // save db traffic
                    $tx = $tx && set_field('course', 'sortorder', $n,
                                           'id', $course->id);
                }
                $n++;
            }
            if ($tx) {
                commit_sql();
            } else {
                rollback_sql();
                if (!$safe) {
                    // if we failed when called with !safe, try
                    // to recover calling self with safe=true
                    return fix_course_sortorder($categoryid, $n, true, $depth, $path);
                }
            }
        }
    }
    set_field('course_categories', 'coursecount', $count, 'id', $categoryid);

    // $n could need updating
    $max = get_field_sql("SELECT MAX(sortorder) from {$CFG->prefix}course WHERE category=$categoryid");
    if ($max > $n) {
        $n = $max;
    }

    if ($categories = get_categories($categoryid)) {
        foreach ($categories as $category) {
            $n = fix_course_sortorder($category->id, $n, $safe, $depth, $path);
        }
    }

    return $n+1;
}

/**
 * Ensure all courses have a valid course category
 * useful if a category has been removed manually
 **/
function fix_coursecategory_orphans() {

    global $CFG;

    // Note: the handling of sortorder here is arguably
    // open to race conditions. Hard to fix here, unlikely
    // to hit anyone in production.

    $sql = "SELECT c.id, c.category, c.shortname
            FROM {$CFG->prefix}course c
            LEFT OUTER JOIN {$CFG->prefix}course_categories cc ON c.category=cc.id
            WHERE cc.id IS NULL AND c.id != " . SITEID;

    $rs = get_recordset_sql($sql);

    if (!rs_EOF($rs)) { // we have some orphans

        // the "default" category is the lowest numbered...
        $default   = get_field_sql("SELECT MIN(id)
                                    FROM {$CFG->prefix}course_categories");
        $sortorder = get_field_sql("SELECT MAX(sortorder)
                                    FROM {$CFG->prefix}course
                                    WHERE category=$default");


        begin_sql();
        $tx = true;
        while ($tx && $course = rs_fetch_next_record($rs)) {
            $tx = $tx && set_field('course', 'category',  $default,     'id', $course->id);
            $tx = $tx && set_field('course', 'sortorder', ++$sortorder, 'id', $course->id);
        }
        if ($tx) {
            commit_sql();
        } else {
            rollback_sql();
        }
    }
    rs_close($rs);
}

/**
 * List of remote courses that a user has access to via MNET.
 * Works only on the IDP
 *
 * @uses $CFG, $USER
 * @return array {@link $COURSE} of course objects
 */
function get_my_remotecourses($userid=0) {
    global $CFG, $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $sql = "SELECT c.id, c.remoteid, c.shortname, c.fullname,
                   c.hostid, c.summary, c.cat_name,
                   h.name AS hostname
            FROM   {$CFG->prefix}mnet_enrol_course c
            JOIN   {$CFG->prefix}mnet_enrol_assignments a ON c.id=a.courseid
            JOIN   {$CFG->prefix}mnet_host h        ON c.hostid=h.id
            WHERE  a.userid={$userid}";

    return get_records_sql($sql);
}

/**
 * List of remote hosts that a user has access to via MNET.
 * Works on the SP
 *
 * @uses $CFG, $USER
 * @return array of host objects
 */
function get_my_remotehosts() {
    global $CFG, $USER;

    if ($USER->mnethostid == $CFG->mnet_localhost_id) {
        return false; // Return nothing on the IDP
    }
    if (!empty($USER->mnet_foreign_host_array) && is_array($USER->mnet_foreign_host_array)) {
        return $USER->mnet_foreign_host_array;
    }
    return false;
}

/**
 * This function creates a default separated/connected scale
 *
 * This function creates a default separated/connected scale
 * so there's something in the database.  The locations of
 * strings and files is a bit odd, but this is because we
 * need to maintain backward compatibility with many different
 * existing language translations and older sites.
 *
 * @uses $CFG
 */
function make_default_scale() {

    global $CFG;

    $defaultscale = NULL;
    $defaultscale->courseid = 0;
    $defaultscale->userid = 0;
    $defaultscale->name  = get_string('separateandconnected');
    $defaultscale->scale = get_string('postrating1', 'forum').','.
                           get_string('postrating2', 'forum').','.
                           get_string('postrating3', 'forum');
    $defaultscale->timemodified = time();

    /// Read in the big description from the file.  Note this is not
    /// HTML (despite the file extension) but Moodle format text.
    $parentlang = get_string('parentlanguage');
    if ($parentlang[0] == '[') {
        $parentlang = '';
    }
    if (is_readable($CFG->dataroot .'/lang/'. $CFG->lang .'/help/forum/ratings.html')) {
        $file = file($CFG->dataroot .'/lang/'. $CFG->lang .'/help/forum/ratings.html');
    } else if (is_readable($CFG->dirroot .'/lang/'. $CFG->lang .'/help/forum/ratings.html')) {
        $file = file($CFG->dirroot .'/lang/'. $CFG->lang .'/help/forum/ratings.html');
    } else if ($parentlang and is_readable($CFG->dataroot .'/lang/'. $parentlang .'/help/forum/ratings.html')) {
        $file = file($CFG->dataroot .'/lang/'. $parentlang .'/help/forum/ratings.html');
    } else if ($parentlang and is_readable($CFG->dirroot .'/lang/'. $parentlang .'/help/forum/ratings.html')) {
        $file = file($CFG->dirroot .'/lang/'. $parentlang .'/help/forum/ratings.html');
    } else if (is_readable($CFG->dirroot .'/lang/en_utf8/help/forum/ratings.html')) {
        $file = file($CFG->dirroot .'/lang/en_utf8/help/forum/ratings.html');
    } else {
        $file = '';
    }

    $defaultscale->description = addslashes(implode('', $file));

    if ($defaultscale->id = insert_record('scale', $defaultscale)) {
        execute_sql('UPDATE '. $CFG->prefix .'forum SET scale = \''. $defaultscale->id .'\'', false);
    }
}


/**
 * Returns a menu of all available scales from the site as well as the given course
 *
 * @uses $CFG
 * @param int $courseid The id of the course as found in the 'course' table.
 * @return object
 */
function get_scales_menu($courseid=0) {

    global $CFG;

    $sql = "SELECT id, name FROM {$CFG->prefix}scale
             WHERE courseid = '0' or courseid = '$courseid'
          ORDER BY courseid ASC, name ASC";

    if ($scales = get_records_sql_menu($sql)) {
        return $scales;
    }

    make_default_scale();

    return get_records_sql_menu($sql);
}



/**
 * Given a set of timezone records, put them in the database,  replacing what is there
 *
 * @uses $CFG
 * @param array $timezones An array of timezone records
 */
function update_timezone_records($timezones) {
/// Given a set of timezone records, put them in the database

    global $CFG;

/// Clear out all the old stuff
    execute_sql('TRUNCATE TABLE '.$CFG->prefix.'timezone', false);

/// Insert all the new stuff
    foreach ($timezones as $timezone) {
        if (is_array($timezone)) {
            $timezone = (object)$timezone;
        }
        insert_record('timezone', $timezone);
    }
}


/// MODULE FUNCTIONS /////////////////////////////////////////////////

/**
 * Just gets a raw list of all modules in a course
 *
 * @uses $CFG
 * @param int $courseid The id of the course as found in the 'course' table.
 * @return object
 */
function get_course_mods($courseid) {
    global $CFG;

    if (empty($courseid)) {
        return false; // avoid warnings
    }

    return get_records_sql("SELECT cm.*, m.name as modname
                            FROM {$CFG->prefix}modules m,
                                 {$CFG->prefix}course_modules cm
                            WHERE cm.course = ".intval($courseid)."
                            AND cm.module = m.id AND m.visible = 1"); // no disabled mods
}


/**
 * Given an id of a course module, finds the coursemodule description
 *
 * @param string $modulename name of module type, eg. resource, assignment,...
 * @param int $cmid course module id (id in course_modules table)
 * @param int $courseid optional course id for extra validation
 * @return object course module instance with instance and module name
 */
function get_coursemodule_from_id($modulename, $cmid, $courseid=0) {

    global $CFG;

    $courseselect = ($courseid) ? 'cm.course = '.intval($courseid).' AND ' : '';

    return get_record_sql("SELECT cm.*, m.name, md.name as modname
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md,
                                {$CFG->prefix}$modulename m
                           WHERE $courseselect
                                 cm.id = ".intval($cmid)." AND
                                 cm.instance = m.id AND
                                 md.name = '$modulename' AND
                                 md.id = cm.module");
}

/**
 * Given an instance number of a module, finds the coursemodule description
 *
 * @param string $modulename name of module type, eg. resource, assignment,...
 * @param int $instance module instance number (id in resource, assignment etc. table)
 * @param int $courseid optional course id for extra validation
 * @return object course module instance with instance and module name
 */
function get_coursemodule_from_instance($modulename, $instance, $courseid=0) {

    global $CFG;

    $courseselect = ($courseid) ? 'cm.course = '.intval($courseid).' AND ' : '';

    return get_record_sql("SELECT cm.*, m.name, md.name as modname
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md,
                                {$CFG->prefix}$modulename m
                           WHERE $courseselect
                                 cm.instance = m.id AND
                                 md.name = '$modulename' AND
                                 md.id = cm.module AND
                                 m.id = ".intval($instance));

}

/**
 * Returns all course modules of given activity in course
 * @param string $modulename (forum, quiz, etc.)
 * @param int $courseid
 * @param string $extrafields extra fields starting with m.
 * @return array of cm objects, false if not found or error
 */
function get_coursemodules_in_course($modulename, $courseid, $extrafields='') {
    global $CFG;

    if (!empty($extrafields)) {
        $extrafields = ", $extrafields";
    }
    return get_records_sql("SELECT cm.*, m.name, md.name as modname $extrafields
                              FROM {$CFG->prefix}course_modules cm,
                                   {$CFG->prefix}modules md,
                                   {$CFG->prefix}$modulename m
                             WHERE cm.course = $courseid AND
                                   cm.instance = m.id AND
                                   md.name = '$modulename' AND
                                   md.id = cm.module");
}

/**
 * Returns an array of all the active instances of a particular module in given courses, sorted in the order they are defined
 *
 * Returns an array of all the active instances of a particular
 * module in given courses, sorted in the order they are defined
 * in the course. Returns an empty array on any errors.
 *
 * The returned objects includle the columns cw.section, cm.visible,
 * cm.groupmode and cm.groupingid, cm.groupmembersonly, and are indexed by cm.id.
 *
 * @param string $modulename The name of the module to get instances for
 * @param array $courses an array of course objects.
 * @return array of module instance objects, including some extra fields from the course_modules
 *          and course_sections tables, or an empty array if an error occurred.
 */
function get_all_instances_in_courses($modulename, $courses, $userid=NULL, $includeinvisible=false) {
    global $CFG;

    $outputarray = array();

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return $outputarray;
    }

    if (!$rawmods = get_records_sql("SELECT cm.id AS coursemodule, m.*, cw.section, cm.visible AS visible,
                                            cm.groupmode, cm.groupingid, cm.groupmembersonly
                                       FROM {$CFG->prefix}course_modules cm,
                                            {$CFG->prefix}course_sections cw,
                                            {$CFG->prefix}modules md,
                                            {$CFG->prefix}$modulename m
                                      WHERE cm.course IN (".implode(',',array_keys($courses)).") AND
                                            cm.instance = m.id AND
                                            cm.section = cw.id AND
                                            md.name = '$modulename' AND
                                            md.id = cm.module")) {
        return $outputarray;
    }

    require_once($CFG->dirroot.'/course/lib.php');

    foreach ($courses as $course) {
        $modinfo = get_fast_modinfo($course, $userid);

        if (empty($modinfo->instances[$modulename])) {
            continue;
        }

        foreach ($modinfo->instances[$modulename] as $cm) {
            if (!$includeinvisible and !$cm->uservisible) {
                continue;
            }
            if (!isset($rawmods[$cm->id])) {
                continue;
            }
            $instance = $rawmods[$cm->id];
            if (!empty($cm->extra)) {
                $instance->extra = urlencode($cm->extra); // bc compatibility
            }
            $outputarray[] = $instance;
        }
    }

    return $outputarray;
}

/**
 * Returns an array of all the active instances of a particular module in a given course,
 * sorted in the order they are defined.
 *
 * Returns an array of all the active instances of a particular
 * module in a given course, sorted in the order they are defined
 * in the course. Returns an empty array on any errors.
 *
 * The returned objects includle the columns cw.section, cm.visible,
 * cm.groupmode and cm.groupingid, cm.groupmembersonly, and are indexed by cm.id.
 *
 * @param string $modulename The name of the module to get instances for
 * @param object $course The course obect.
 * @return array of module instance objects, including some extra fields from the course_modules
 *          and course_sections tables, or an empty array if an error occurred.
 */
function get_all_instances_in_course($modulename, $course, $userid=NULL, $includeinvisible=false) {
    return get_all_instances_in_courses($modulename, array($course->id => $course), $userid, $includeinvisible);
}


/**
 * Determine whether a module instance is visible within a course
 *
 * Given a valid module object with info about the id and course,
 * and the module's type (eg "forum") returns whether the object
 * is visible or not, groupmembersonly visibility not tested
 *
 * @uses $CFG
 * @param $moduletype Name of the module eg 'forum'
 * @param $module Object which is the instance of the module
 * @return bool
 */
function instance_is_visible($moduletype, $module) {

    global $CFG;

    if (!empty($module->id)) {
        if ($records = get_records_sql("SELECT cm.instance, cm.visible, cm.groupingid, cm.id, cm.groupmembersonly, cm.course
                                        FROM {$CFG->prefix}course_modules cm,
                                             {$CFG->prefix}modules m
                                       WHERE cm.course = '$module->course' AND
                                             cm.module = m.id AND
                                             m.name = '$moduletype' AND
                                             cm.instance = '$module->id'")) {

            foreach ($records as $record) { // there should only be one - use the first one
                return $record->visible;
            }
        }
    }
    return true;  // visible by default!
}

/**
 * Determine whether a course module is visible within a course,
 * this is different from instance_is_visible() - faster and visibility for user
 *
 * @param object $cm object
 * @param int $userid empty means current user
 * @return bool
 */
function coursemodule_visible_for_user($cm, $userid=0) {
    global $USER;

    if (empty($cm->id)) {
        debugging("Incorrect course module parameter!", DEBUG_DEVELOPER);
        return false;
    }
    if (empty($userid)) {
        $userid = $USER->id;
    }
    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id), $userid)) {
        return false;
    }
    return groups_course_module_visible($cm, $userid);
}




/// LOG FUNCTIONS /////////////////////////////////////////////////////


/**
 * Add an entry to the log table.
 *
 * Add an entry to the log table.  These are "action" focussed rather
 * than web server hits, and provide a way to easily reconstruct what
 * any particular student has been doing.
 *
 * @uses $CFG
 * @uses $USER
 * @uses $db
 * @uses $REMOTE_ADDR
 * @uses SITEID
 * @param    int     $courseid  The course id
 * @param    string  $module  The module name - e.g. forum, journal, resource, course, user etc
 * @param    string  $action  'view', 'update', 'add' or 'delete', possibly followed by another word to clarify.
 * @param    string  $url     The file and parameters used to see the results of the action
 * @param    string  $info    Additional description information
 * @param    string  $cm      The course_module->id if there is one
 * @param    string  $user    If log regards $user other than $USER
 */
function add_to_log($courseid, $module, $action, $url='', $info='', $cm=0, $user=0) {
    // Note that this function intentionally does not follow the normal Moodle DB access idioms.
    // This is for a good reason: it is the most frequently used DB update function,
    // so it has been optimised for speed.
    global $db, $CFG, $USER;

    // sanitize all incoming data
    $courseid = clean_param($courseid, PARAM_INT);
    $module   = clean_param($module, PARAM_SAFEDIR);
    $action   = addslashes($action);
    // url cleaned bellow
    // info cleaned bellow
    $cm       = clean_param($cm, PARAM_INT);
    $user     = clean_param($user, PARAM_INT);

    if ($user) {
        $userid = $user;
    } else {
        if (!empty($USER->realuser)) {  // Don't log
            return;
        }
        $userid = empty($USER->id) ? '0' : $USER->id;
    }

    $REMOTE_ADDR = getremoteaddr();
    if (empty($REMOTE_ADDR)) {
        $REMOTE_ADDR = '0.0.0.0';
    }

    $timenow = time();
    if (!empty($url)) { // could break doing html_entity_decode on an empty var.
        $url = html_entity_decode($url); // for php < 4.3.0 this is defined in moodlelib.php
    }

    // Restrict length of log lines to the space actually available in the
    // database so that it doesn't cause a DB error. Log a warning so that
    // developers can avoid doing things which are likely to cause this on a
    // routine basis.
    $tl=textlib_get_instance();
    if(!empty($info) && $tl->strlen($info)>255) {
        $info=$tl->substr($info,0,252).'...';
        debugging('Warning: logged very long info',DEBUG_DEVELOPER);
    }
    $info = addslashes($info);
    // Note: Unlike $info, URL appears to be already slashed before this function
    // is called. Since database limits are for the data before slashes, we need
    // to remove them...
    $url=stripslashes($url);
    // If the 100 field size is changed, also need to alter print_log in course/lib.php
    if(!empty($url) && $tl->strlen($url)>100) {
        $url=$tl->substr($url,0,97).'...';
        debugging('Warning: logged very long URL',DEBUG_DEVELOPER);
    }
    $url=addslashes($url);

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; $PERF->logwrites++;};

    $info = empty($info) ? sql_empty() : $info; // Use proper empties for each database
    $url  = empty($url)  ? sql_empty() : $url;
    $sql ='INSERT INTO '. $CFG->prefix .'log (time, userid, course, ip, module, cmid, action, url, info)
        VALUES (' . "'$timenow', '$userid', '$courseid', '$REMOTE_ADDR', '$module', '$cm', '$action', '$url', '$info')";

    $result = $db->Execute($sql);

    // MDL-11893, alert $CFG->supportemail if insert into log failed
    if (!$result && $CFG->supportemail) {
        $site = get_site();
        $subject = 'Insert into log failed at your moodle site '.$site->fullname;
        $message = "Insert into log table failed at ". date('l dS \of F Y h:i:s A') .".\n It is possible that your disk is full.\n\n";
        $message .= "The failed SQL is:\n\n" . $sql;

        // email_to_user is not usable because email_to_user tries to write to the logs table,
        // and this will get caught in an infinite loop, if disk is full
        if (empty($CFG->noemailever)) {
            $lasttime = get_config('admin', 'lastloginserterrormail');
            if(empty($lasttime) || time() - $lasttime > 60*60*24) { // limit to 1 email per day
                mail($CFG->supportemail, $subject, $message);
                set_config('lastloginserterrormail', time(), 'admin');
            }
        }
    }

    if (!$result) {
        debugging('Error: Could not insert a new entry to the Moodle log', DEBUG_ALL);
    }

}

/**
 * Store user last access times - called when use enters a course or site
 *
 * Note: we use ADOdb code directly in this function to save some CPU
 * cycles here and there. They are simple operations not needing any
 * of the postprocessing performed by dmllib.php
 *
 * @param int $courseid, empty means site
 * @return void
 */
function user_accesstime_log($courseid=0) {

    global $USER, $CFG, $PERF, $db;

    if (!isloggedin() or !empty($USER->realuser)) {
        // no access tracking
        return;
    }

    if (empty($courseid)) {
        $courseid = SITEID;
    }

    $timenow = time();

/// Store site lastaccess time for the current user
    if ($timenow - $USER->lastaccess > LASTACCESS_UPDATE_SECS) {
    /// Update $USER->lastaccess for next checks
        $USER->lastaccess = $timenow;
        if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++;};

        $remoteaddr = getremoteaddr();
        if (empty($remoteaddr)) {
            $remoteaddr = '0.0.0.0';
        }
        if ($db->Execute("UPDATE {$CFG->prefix}user
                             SET lastip = '$remoteaddr', lastaccess = $timenow
                           WHERE id = $USER->id")) {
        } else {
            debugging('Error: Could not update global user lastaccess information');  // Don't throw an error
        }
    /// Remove this record from record cache since it will change
        if (!empty($CFG->rcache)) {
            rcache_unset('user', $USER->id);
        }
    }

    if ($courseid == SITEID) {
    ///  no user_lastaccess for frontpage
        return;
    }

/// Store course lastaccess times for the current user
    if (empty($USER->currentcourseaccess[$courseid]) or ($timenow - $USER->currentcourseaccess[$courseid] > LASTACCESS_UPDATE_SECS)) {
        if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

        $exists = false; // To detect if the user_lastaccess record exists or no
        if ($rs = $db->Execute("SELECT timeaccess
                                  FROM {$CFG->prefix}user_lastaccess
                                 WHERE userid = $USER->id AND courseid = $courseid")) {
            if (!$rs->EOF) {
                $exists = true;
                $lastaccess = reset($rs->fields);
                if ($timenow - $lastaccess <  LASTACCESS_UPDATE_SECS) {
                /// no need to update now, it was updated recently in concurrent login ;-)
                    $rs->Close();
                    return;
                }
            }
            $rs->Close();
        }

    /// Update course lastaccess for next checks
        $USER->currentcourseaccess[$courseid] = $timenow;
        if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; };

        if ($exists) { // user_lastaccess record exists, update it
            if ($db->Execute("UPDATE {$CFG->prefix}user_lastaccess
                                 SET timeaccess = $timenow
                               WHERE userid = $USER->id AND courseid = $courseid")) {
            } else {
                debugging('Error: Could not update course user lastacess information');  // Don't throw an error
            }

        } else { // user lastaccess record doesn't exist, insert it
            if ($db->Execute("INSERT INTO {$CFG->prefix}user_lastaccess
                                     (userid, courseid, timeaccess)
                              VALUES ($USER->id, $courseid, $timenow)")) {
            } else {
                debugging('Error: Could not insert course user lastaccess information');  // Don't throw an error
            }
        }
    }
}

/**
 * Select all log records based on SQL criteria
 *
 * @uses $CFG
 * @param string $select SQL select criteria
 * @param string $order SQL order by clause to sort the records returned
 * @param string $limitfrom ?
 * @param int $limitnum ?
 * @param int $totalcount Passed in by reference.
 * @return object
 * @todo Finish documenting this function
 */
function get_logs($select, $order='l.time DESC', $limitfrom='', $limitnum='', &$totalcount) {
    global $CFG;

    if ($order) {
        $order = 'ORDER BY '. $order;
    }

    $selectsql = $CFG->prefix .'log l LEFT JOIN '. $CFG->prefix .'user u ON l.userid = u.id '. ((strlen($select) > 0) ? 'WHERE '. $select : '');
    $countsql = $CFG->prefix.'log l '.((strlen($select) > 0) ? ' WHERE '. $select : '');

    $totalcount = count_records_sql("SELECT COUNT(*) FROM $countsql");

    return get_records_sql('SELECT l.*, u.firstname, u.lastname, u.picture
                                FROM '. $selectsql .' '. $order, $limitfrom, $limitnum) ;
}


/**
 * Select all log records for a given course and user
 *
 * @uses $CFG
 * @uses DAYSECS
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $courseid The id of the course as found in the 'course' table.
 * @param string $coursestart ?
 * @todo Finish documenting this function
 */
function get_logs_usercourse($userid, $courseid, $coursestart) {
    global $CFG;

    if ($courseid) {
        $courseselect = ' AND course = \''. $courseid .'\' ';
    } else {
        $courseselect = '';
    }

    return get_records_sql("SELECT floor((time - $coursestart)/". DAYSECS .") as day, count(*) as num
                            FROM {$CFG->prefix}log
                           WHERE userid = '$userid'
                             AND time > '$coursestart' $courseselect
                        GROUP BY floor((time - $coursestart)/". DAYSECS .") ");
}

/**
 * Select all log records for a given course, user, and day
 *
 * @uses $CFG
 * @uses HOURSECS
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $courseid The id of the course as found in the 'course' table.
 * @param string $daystart ?
 * @return object
 * @todo Finish documenting this function
 */
function get_logs_userday($userid, $courseid, $daystart) {
    global $CFG;

    if ($courseid) {
        $courseselect = ' AND course = \''. $courseid .'\' ';
    } else {
        $courseselect = '';
    }

    return get_records_sql("SELECT floor((time - $daystart)/". HOURSECS .") as hour, count(*) as num
                            FROM {$CFG->prefix}log
                           WHERE userid = '$userid'
                             AND time > '$daystart' $courseselect
                        GROUP BY floor((time - $daystart)/". HOURSECS .") ");
}

/**
 * Returns an object with counts of failed login attempts
 *
 * Returns information about failed login attempts.  If the current user is
 * an admin, then two numbers are returned:  the number of attempts and the
 * number of accounts.  For non-admins, only the attempts on the given user
 * are shown.
 *
 * @param string $mode Either 'admin', 'teacher' or 'everybody'
 * @param string $username The username we are searching for
 * @param string $lastlogin The date from which we are searching
 * @return int
 */
function count_login_failures($mode, $username, $lastlogin) {

    $select = 'module=\'login\' AND action=\'error\' AND time > '. $lastlogin;

    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {    // Return information about all accounts
        if ($count->attempts = count_records_select('log', $select)) {
            $count->accounts = count_records_select('log', $select, 'COUNT(DISTINCT info)');
            return $count;
        }
    } else if ($mode == 'everybody' or ($mode == 'teacher' and isteacherinanycourse())) {
        if ($count->attempts = count_records_select('log', $select .' AND info = \''. $username .'\'')) {
            return $count;
        }
    }
    return NULL;
}


/// GENERAL HELPFUL THINGS  ///////////////////////////////////

/**
 * Dump a given object's information in a PRE block.
 *
 * Mostly just used for debugging.
 *
 * @param mixed $object The data to be printed
 */
function print_object($object) {
    echo '<pre class="notifytiny">' . htmlspecialchars(print_r($object,true)) . '</pre>';
}

/*
 * Check whether a course is visible through its parents
 * path.
 *
 * Notes:
 *
 * - All we need from the course is ->category. _However_
 *   if the course object has a categorypath property,
 *   we'll save a dbquery
 *
 * - If we return false, you'll still need to check if
 *   the user can has the 'moodle/category:viewhiddencategories'
 *   capability...
 *
 * - Will generate 2 DB calls.
 *
 * - It does have a small local cache, however...
 *
 * - Do NOT call this over many courses as it'll generate
 *   DB traffic. Instead, see what get_my_courses() does.
 *
 * @param mixed $object A course object
 * @return bool
 */
function course_parent_visible($course = null) {
    global $CFG;
    //return true;
    static $mycache;

    if (!is_object($course)) {
        return true;
    }
    if (!empty($CFG->allowvisiblecoursesinhiddencategories)) {
        return true;
    }

    if (!isset($mycache)) {
        $mycache = array();
    } else {
        // cast to force assoc array
        $k = (string)$course->category;
        if (isset($mycache[$k])) {
            return $mycache[$k];
        }
    }

    if (isset($course->categorypath)) {
        $path = $course->categorypath;
    } else {
        $path = get_field('course_categories', 'path',
                          'id', $course->category);
    }
    $catids = substr($path,1); // strip leading slash
    $catids = str_replace('/',',',$catids);

    $sql = "SELECT MIN(visible)
            FROM {$CFG->prefix}course_categories
            WHERE id IN ($catids)";
    $vis = get_field_sql($sql);

    // cast to force assoc array
    $k = (string)$course->category;
    $mycache[$k] = $vis;

    return $vis;
}

/**
 * This function is the official hook inside XMLDB stuff to delegate its debug to one
 * external function.
 *
 * Any script can avoid calls to this function by defining XMLDB_SKIP_DEBUG_HOOK before
 * using XMLDB classes. Obviously, also, if this function doesn't exist, it isn't invoked ;-)
 *
 * @param $message string contains the error message
 * @param $object object XMLDB object that fired the debug
 */
function xmldb_debug($message, $object) {

    debugging($message, DEBUG_DEVELOPER);
}

/**
 * true or false function to see if user can create any courses at all
 * @return bool
 */
function user_can_create_courses() {
    global $USER;
    // if user has course creation capability at any site or course cat, then return true;

    if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM))) {
        return true;
    }
    if ($cats = get_records('course_categories')) {
        foreach ($cats as $cat) {
            if (has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $cat->id))) {
                return true;
            }
        }
    }
    return false;
}

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>

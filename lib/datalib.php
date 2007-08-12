<?php // $Id$

/**
 * Library of functions for database manipulation.
 *
 * Other main libraries:
 * - weblib.php - functions that produce web output
 * - moodlelib.php - general-purpose Moodle functions
 * @author Martin Dougiamas and many others
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */


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

    global $CFG;

    if ( $admins = get_admins() ) {
        foreach ($admins as $admin) {
            return $admin;   // ie the first one
        }
    } else {
        return false;
    }
}

/**
 * Returns list of all admins
 *
 * @uses $CFG
 * @return object
 */
function get_admins() {

    global $CFG;

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    return get_users_by_capability($context, 'moodle/site:doanything', 'u.*, ra.id as adminid', 'ra.id ASC'); // only need first one

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
                               ".groups_members_from_sql()."
                          WHERE $select AND ".groups_members_where_sql($groupid, 'u.id')."
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
 * @return object|false  {@link $USER} records or false if error.
 */
function get_site_users($sort='u.lastaccess DESC', $fields='*', $exceptions='') {

    return get_course_users(SITEID, $sort, $exceptions, $fields);
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
                   $firstinitial='', $lastinitial='', $page='', $recordsperpage='', $fields='*') {

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
                           $search='', $firstinitial='', $lastinitial='', $remotewhere='') {

    global $CFG;

    $LIKE      = sql_ilike();
    $fullname  = sql_fullname();

    $select = "deleted <> '1'";

    if (!empty($search)) {
        $search = trim($search);
        $select .= " AND ($fullname $LIKE '%$search%' OR email $LIKE '%$search%') ";
    }

    if ($firstinitial) {
        $select .= ' AND firstname '. $LIKE .' \''. $firstinitial .'%\' ';
    }

    if ($lastinitial) {
        $select .= ' AND lastname '. $LIKE .' \''. $lastinitial .'%\' ';
    }

    $select .= $remotewhere;

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


/**
 * Full list of users that have not yet confirmed their accounts.
 *
 * @uses $CFG
 * @param string $cutofftime ?
 * @return object  {@link $USER} records
 */
function get_users_unconfirmed($cutofftime=2000000000) {
    global $CFG;
    return get_records_sql("SELECT *
                             FROM {$CFG->prefix}user
                            WHERE confirmed = 0
                              AND firstaccess > 0
                              AND firstaccess < $cutofftime");
}

/**
 * All users that we have not seen for a really long time (ie dead accounts)
 *
 * @uses $CFG
 * @param string $cutofftime ?
 * @return object  {@link $USER} records
 */
function get_users_longtimenosee($cutofftime) {
    global $CFG;
    return get_records_sql("SELECT userid as id, courseid
                              FROM {$CFG->prefix}user_lastaccess
                             WHERE courseid != ".SITEID."
                               AND timeaccess > 0
                               AND timeaccess < $cutofftime ");
}

/**
 * Full list of bogus accounts that are probably not ever going to be used
 *
 * @uses $CFG
 * @param string $cutofftime ?
 * @return object  {@link $USER} records
 */

function get_users_not_fully_set_up($cutofftime=2000000000) {
    global $CFG;
    return get_records_sql("SELECT *
                             FROM {$CFG->prefix}user
                            WHERE confirmed = 1
                             AND lastaccess > 0
                             AND lastaccess < $cutofftime
                             AND deleted = 0
                             AND (lastname = '' OR firstname = '' OR email = '')");
}


/** TODO: functions now in /group/lib/legacylib.php (3)
get_groups
get_group_users
user_group

 * Returns an array of group objects that the user is a member of
 * in the given course.  If userid isn't specified, then return a
 * list of all groups in the course.
 *
 * @uses $CFG
 * @param int $courseid The id of the course in question.
 * @param int $userid The id of the user in question as found in the 'user' table 'id' field.
 * @return object
 *
function get_groups($courseid, $userid=0) {
    global $CFG;

    if ($userid) {
        $dbselect = ', '. $CFG->prefix .'groups_members m';
        $userselect = 'AND m.groupid = g.id AND m.userid = \''. $userid .'\'';
    } else {
        $dbselect = '';
        $userselect = '';
    }

    return get_records_sql("SELECT g.*
                              FROM {$CFG->prefix}groups g $dbselect
                             WHERE g.courseid = '$courseid' $userselect ");
}


/**
 * Returns an array of user objects that belong to a given group
 *
 * @uses $CFG
 * @param int $groupid The group in question.
 * @param string $sort ?
 * @param string $exceptions ?
 * @return object
 *
function get_group_users($groupid, $sort='u.lastaccess DESC', $exceptions='', $fields='u.*') {
    global $CFG;
    if (!empty($exceptions)) {
        $except = ' AND u.id NOT IN ('. $exceptions .') ';
    } else {
        $except = '';
    }
    // in postgres, you can't have things in sort that aren't in the select, so...
    $extrafield = str_replace('ASC','',$sort);
    $extrafield = str_replace('DESC','',$extrafield);
    $extrafield = trim($extrafield);
    if (!empty($extrafield)) {
        $extrafield = ','.$extrafield;
    }
    return get_records_sql("SELECT $fields $extrafield
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}groups_members m
                             WHERE m.groupid = '$groupid'
                               AND m.userid = u.id $except
                          ORDER BY $sort");
}

/**
 * Returns the user's group in a particular course
 *
 * @uses $CFG
 * @param int $courseid The course in question.
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $groupid The id of the group the user is in.
 * @return object
 *
function user_group($courseid, $userid) {
    global $CFG;

    return get_records_sql("SELECT g.*
                             FROM {$CFG->prefix}groups g,
                                  {$CFG->prefix}groups_members m
                             WHERE g.courseid = '$courseid'
                               AND g.id = m.groupid
                               AND m.userid = '$userid'
                               ORDER BY name ASC");
}
*/



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
    if ($courses = get_records_sql("SELECT $fields
                                FROM {$CFG->prefix}course c
                                $categoryselect
                                $sortstatement")) {

        // loop throught them
        foreach ($courses as $course) {

            if (isset($course->visible) && $course->visible <= 0) {
                // for hidden courses, require visibility check
                if (has_capability('moodle/course:viewhiddencourses',
                        get_context_instance(CONTEXT_COURSE, $course->id))) {
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
        if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
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
    if (!($courses = get_records_sql("SELECT $fields
                                FROM {$CFG->prefix}course c
                                $categoryselect
                                ORDER BY $sort"))) {
        return $visiblecourses;
    }
    $totalcount = 0;

    if (!$limitnum) {
        $limitnum = count($courses);
    }

    if (!$limitfrom) {
        $limitfrom = 0;
    }

    // iteration will have to be done inside loop to keep track of the limitfrom and limitnum
    foreach ($courses as $course) {
        if ($course->visible <= 0) {
            // for hidden courses, require visibility check
            if (has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course->id))) {
                $totalcount++;
                if ($totalcount > $limitfrom && count($visiblecourses) < $limitnum) {
                    $visiblecourses [] = $course;
                }
            }
        } else {
            $totalcount++;
            if ($totalcount > $limitfrom && count($visiblecourses) < $limitnum) {
                $visiblecourses [] = $course;
            }
        }
    }

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
        if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
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


/**
 * List of courses that a user has access to view. Note that for admins,
 * this usually includes every course on the system.
 *
 * @uses $CFG
 * @param int $userid The user of interest
 * @param string $sort the sortorder in the course table
 * @param string $fields  the fields to return
 * @param bool $doanything True if using the doanything flag
 * @param int $limit Maximum number of records to return, or 0 for unlimited
 * @return array {@link $COURSE} of course objects
 */
function get_my_courses($userid, $sort=NULL, $fields=NULL, $doanything=false,$limit=0) {

    global $CFG, $USER;

    // Default parameters
    $d_sort   = 'visible DESC,sortorder ASC';
    $d_fields = 'id, category, sortorder, shortname, fullname, idnumber, newsitems, teacher, teachers, student, students, guest, startdate, visible, cost, enrol, summary, groupmode, groupmodeforce';

    $usingdefaults = true;
    if (is_null($sort)   || $sort === $d_sort) {
        $sort = $d_sort;
    } else {
        $usingdefaults = false;
    }
    if (is_null($fields) || $fields === $d_fields) {
        $fields = $d_fields;
    } else {
        $usingdefaults = false;
    }

    $reallimit = 0; // this is only set if we are using a limit on the first call

    // If using default params, we may have it cached...
    if (!empty($USER->id) && ($USER->id == $userid) && $usingdefaults) {
        if (!empty($USER->mycourses[$doanything])) {
            if ($limit && $limit < count($USER->mycourses[$doanything])) {
                return array_slice($USER->mycourses[$doanything], 0, $limit, true);
            } else {
                return $USER->mycourses[$doanything];
            }
        } else {
            // now, this is the first call, i.e. no cache, and we are using defaults, with a limit supplied,
            // we need to store the limit somewhere, retrieve all, cache properly and then slice the array
            // to return the proper number of entries. This is so that we don't keep missing calls like limit 20,20,20 
            if ($limit) {
                $reallimit = $limit;
                $limit = 0;
            }
        }
    }

    $mycourses = array();

    // Fix fields to refer to the course table c
    $fields=preg_replace('/([a-z0-9*]+)/','c.$1',$fields);

    // Attempt to filter the list of courses in order to reduce the number
    // of queries in the next part.

    // Check root permissions
    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

    // Guest's do not have any courses
    if (has_capability('moodle/legacy:guest',$sitecontext,$userid,false)) {
        return(array());
    }

    // we can optimise some things for true admins
    $candoanything = false;
    if ($doanything && has_capability('moodle/site:doanything',$sitecontext,$userid,true)) {
        $candoanything = true;
    }

    if ($candoanything || has_capability('moodle/course:view',$sitecontext,$userid,$doanything)) {
        // User can view all courses, although there might be exceptions
        // which we will filter later.
        $rs = get_recordset('course c', '', '', $sort, $fields);
    } else {
        // The only other context level above courses that applies to moodle/course:view
        // is category. So we consider:
        // 1. All courses in which the user is assigned a role
        // 2. All courses in categories in which the user is assigned a role
        // 2BIS. All courses in subcategories in which the user gets assignment because he is assigned in one of its ascendant categories
        // 3. All courses which have overrides for moodle/course:view
        // Remember that this is just a filter. We check each individual course later.
        // However for a typical student on a large system this can reduce the
        // number of courses considered from around 2,000 to around 2, with corresponding
        // reduction in the number of queries needed.
        $rs=get_recordset_sql("
            SELECT $fields
            FROM {$CFG->prefix}course c, (
                SELECT
                    c.id
                FROM
                    {$CFG->prefix}role_assignments ra
                    INNER JOIN {$CFG->prefix}context x ON x.id         = ra.contextid
                    INNER JOIN {$CFG->prefix}course c  ON x.instanceid = c.id
                WHERE
                    ra.userid      = $userid AND
                    x.contextlevel = 50
                UNION
                SELECT
                    c.id
                FROM
                    {$CFG->prefix}role_assignments ra
                    INNER JOIN {$CFG->prefix}context x ON x.id = ra.contextid
                    INNER JOIN {$CFG->prefix}course_categories a ON a.path LIKE ".sql_concat("'%/'", 'x.instanceid', "'/%'")." OR x.instanceid = a.id
                    INNER JOIN {$CFG->prefix}course c ON c.category = a.id
                WHERE
                    ra.userid = $userid AND
                    x.contextlevel = 40
                UNION
                SELECT
                    c.id
                FROM
                    {$CFG->prefix}role_capabilities ca
                    INNER JOIN {$CFG->prefix}context x ON x.id = ca.contextid
                    INNER JOIN {$CFG->prefix}course c  ON c.id = x.instanceid
                WHERE
                    ca.capability  = 'moodle/course:view' AND
                    ca.contextid  != {$sitecontext->id} AND
                    x.contextlevel = 50
            ) cids
            WHERE c.id = cids.id
            ORDER BY $sort"
        );
    }

    if ($rs && $rs->RecordCount() > 0) {
        while ($course = rs_fetch_next_record($rs)) {
            if ($course->id != SITEID) {

                if ($candoanything) { // no need for further checks...
                    $mycourses[$course->id] = $course;
                    continue;
                }

                // users with moodle/course:view are considered course participants
                // the course needs to be visible, or user must have moodle/course:viewhiddencourses
                // capability set to view hidden courses
                $context = get_context_instance(CONTEXT_COURSE, $course->id);
                if ( has_capability('moodle/course:view', $context, $userid, $doanything) &&
                    !has_capability('moodle/legacy:guest', $context, $userid, false) &&
                    ($course->visible || has_capability('moodle/course:viewhiddencourses', $context, $userid))) {
                    $mycourses[$course->id] = $course;

                    // Only return a limited number of courses if limit is set
                    if($limit>0) {
                        $limit--;
                        if($limit==0) {
                            break;
                        }
                    }
                }
            }
        }
    }

    // Cache if using default params...
    if (!empty($USER->id) && ($USER->id == $userid) && $usingdefaults && $limit == 0) {
        $USER->mycourses[$doanything] = $mycourses;
    }

    if (!empty($mycourses) && $reallimit) {
        return array_slice($mycourses, 0, $reallimit, true);
    } else {
        return $mycourses;
    }
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

    foreach ($searchterms as $searchterm) {

    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE search
        if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql') {
            $searchterm = trim($searchterm, '+-');
        }

        if ($fullnamesearch) {
            $fullnamesearch .= ' AND ';
        }
        if ($summarysearch) {
            $summarysearch .= ' AND ';
        }

        if (substr($searchterm,0,1) == '+') {
            $searchterm = substr($searchterm,1);
            $summarysearch .= " summary $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $fullnamesearch .= " fullname $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $summarysearch .= " summary $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $fullnamesearch .= " fullname $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $summarysearch .= ' summary '. $LIKE .' \'%'. $searchterm .'%\' ';
            $fullnamesearch .= ' fullname '. $LIKE .' \'%'. $searchterm .'%\' ';
        }

    }

    $selectsql = $CFG->prefix .'course WHERE ('. $fullnamesearch .' OR '. $summarysearch .') AND category > \'0\'';

    $totalcount = count_records_sql('SELECT COUNT(*) FROM '. $selectsql);

    $courses = get_records_sql('SELECT * FROM '. $selectsql .' ORDER BY '. $sort, $page, $recordsperpage);

    if ($courses) {  /// Remove unavailable courses from the list
        foreach ($courses as $key => $course) {
            if (!$course->visible) {
                if (!has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    unset($courses[$key]);
                    $totalcount--;
                }
            }
        }
    }

    return $courses;
}


/**
 * Returns a sorted list of categories
 *
 * @param string $parent The parent category if any
 * @param string $sort the sortorder
 * @return array of categories
 */
function get_categories($parent='none', $sort='sortorder ASC') {

    if ($parent === 'none') {
        $categories = get_records('course_categories', '', '', $sort);
    } else {
        $categories = get_records('course_categories', 'parent', $parent, $sort);
    }
    if ($categories) {  /// Remove unavailable categories from the list
        foreach ($categories as $key => $category) {
            if (!$category->visible) {
                if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $category->id))) {
                    unset($categories[$key]);
                }
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

        set_field('course_categories', 'path',  addslashes($path),  'id', $categoryid);
        set_field('course_categories', 'depth', $depth, 'id', $categoryid);
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
    if ($min < $n+$tolerance || $min > $n+$tolerance+$catgap ) {
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
            foreach ($courses as $course) {
                if ($course->sortorder != $n ) { // save db traffic
                    set_field('course', 'sortorder', $n, 'id', $course->id);
                }
                $n++;
            }
            commit_sql();
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

    $sql = "SELECT c.remoteid, c.shortname, c.fullname,
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
    $parentlang = get_string('parentlang');
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
                            WHERE cm.course = '$courseid'
                            AND cm.module = m.id ");
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

    $courseselect = ($courseid) ? "cm.course = '$courseid' AND " : '';

    return get_record_sql("SELECT cm.*, m.name, md.name as modname
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md,
                                {$CFG->prefix}$modulename m
                           WHERE $courseselect
                                 cm.id = '$cmid' AND
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

    $courseselect = ($courseid) ? "cm.course = '$courseid' AND " : '';

    return get_record_sql("SELECT cm.*, m.name, md.name as modname
                           FROM {$CFG->prefix}course_modules cm,
                                {$CFG->prefix}modules md,
                                {$CFG->prefix}$modulename m
                           WHERE $courseselect
                                 cm.instance = m.id AND
                                 md.name = '$modulename' AND
                                 md.id = cm.module AND
                                 m.id = '$instance'");

}

/**
 * Returns an array of all the active instances of a particular module in given courses, sorted in the order they are defined
 *
 * Returns an array of all the active instances of a particular
 * module in given courses, sorted in the order they are defined
 * in the course.   Returns false on any errors.
 *
 * @uses $CFG
 * @param string  $modulename The name of the module to get instances for
 * @param array  $courses This depends on an accurate $course->modinfo
 * @return array of instances
 */
function get_all_instances_in_courses($modulename, $courses, $userid=NULL, $includeinvisible=false) {
    global $CFG;
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }
    if (!$rawmods = get_records_sql("SELECT cm.id as coursemodule, m.*,cw.section,cm.visible as visible,cm.groupmode, cm.course
                            FROM {$CFG->prefix}course_modules cm,
                                 {$CFG->prefix}course_sections cw,
                                 {$CFG->prefix}modules md,
                                 {$CFG->prefix}$modulename m
                            WHERE cm.course IN (".implode(',',array_keys($courses)).") AND
                                  cm.instance = m.id AND
                                  cm.section = cw.id AND
                                  md.name = '$modulename' AND
                                  md.id = cm.module")) {
        return array();
    }

    $outputarray = array();

    foreach ($courses as $course) {
        if ($includeinvisible) {
            $invisible = -1;
        } else if (has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course->id), $userid)) {
            // Usually hide non-visible instances from students
            $invisible = -1;
        } else {
            $invisible = 0;
        }

   /// Casting $course->modinfo to string prevents one notice when the field is null
        if (!$modinfo = unserialize((string)$course->modinfo)) {
            continue;
        }
        foreach ($modinfo as $mod) {
            if ($mod->mod == $modulename and $mod->visible > $invisible) {
                $instance = $rawmods[$mod->cm];
                if (!empty($mod->extra)) {
                    $instance->extra = $mod->extra;
                }
                $outputarray[] = $instance;
            }
        }
    }

    return $outputarray;

}

/**
 * Returns an array of all the active instances of a particular module in a given course, sorted in the order they are defined
 *
 * Returns an array of all the active instances of a particular
 * module in a given course, sorted in the order they are defined
 * in the course.   Returns false on any errors.
 *
 * @uses $CFG
 * @param string  $modulename The name of the module to get instances for
 * @param object(course)  $course This depends on an accurate $course->modinfo
 */
function get_all_instances_in_course($modulename, $course, $userid=NULL, $includeinvisible=false) {

    global $CFG;

    if (empty($course->modinfo)) {
        return array();
    }

    if (!$modinfo = unserialize((string)$course->modinfo)) {
        return array();
    }

    if (!$rawmods = get_records_sql("SELECT cm.id as coursemodule, m.*,cw.section,cm.visible as visible,cm.groupmode
                            FROM {$CFG->prefix}course_modules cm,
                                 {$CFG->prefix}course_sections cw,
                                 {$CFG->prefix}modules md,
                                 {$CFG->prefix}$modulename m
                            WHERE cm.course = '$course->id' AND
                                  cm.instance = m.id AND
                                  cm.section = cw.id AND
                                  md.name = '$modulename' AND
                                  md.id = cm.module")) {
        return array();
    }

    if ($includeinvisible) {
        $invisible = -1;
    } else if (has_capability('moodle/course:viewhiddencourses', get_context_instance(CONTEXT_COURSE, $course->id), $userid)) {
        // Usually hide non-visible instances from students
        $invisible = -1;
    } else {
        $invisible = 0;
    }

    $outputarray = array();

    foreach ($modinfo as $mod) {
        if ($mod->mod == $modulename and $mod->visible > $invisible) {
            $instance = $rawmods[$mod->cm];
            if (!empty($mod->extra)) {
                $instance->extra = $mod->extra;
            }
            $outputarray[] = $instance;
        }
    }

    return $outputarray;

}


/**
 * Determine whether a module instance is visible within a course
 *
 * Given a valid module object with info about the id and course,
 * and the module's type (eg "forum") returns whether the object
 * is visible or not
 *
 * @uses $CFG
 * @param $moduletype Name of the module eg 'forum'
 * @param $module Object which is the instance of the module
 * @return bool
 */
function instance_is_visible($moduletype, $module) {

    global $CFG;

    if (!empty($module->id)) {
        if ($records = get_records_sql("SELECT cm.instance, cm.visible
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

    if ($cm === '' || is_null($cm)) { // postgres won't translate empty string to its default
        $cm = 0;
    }

    if ($user) {
        $userid = $user;
    } else {
        if (!empty($USER->realuser)) {  // Don't log
            return;
        }
        $userid = empty($USER->id) ? '0' : $USER->id;
    }

    $REMOTE_ADDR = getremoteaddr();

    $timenow = time();
    $info = addslashes($info);
    if (!empty($url)) { // could break doing html_entity_decode on an empty var.
        $url = html_entity_decode($url); // for php < 4.3.0 this is defined in moodlelib.php
    }

    if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++; $PERF->logwrites++;};

    if ($CFG->type = 'oci8po') {
        if (empty($info)) {
            $info = ' ';
        }
    }

    $result = $db->Execute('INSERT INTO '. $CFG->prefix .'log (time, userid, course, ip, module, cmid, action, url, info)
        VALUES (' . "'$timenow', '$userid', '$courseid', '$REMOTE_ADDR', '$module', '$cm', '$action', '$url', '$info')");

    if (!$result and debugging()) {
        echo '<p>Error: Could not insert a new entry to the Moodle log</p>';  // Don't throw an error
    }

/// Store lastaccess times for the current user, do not use in cron and other commandline scripts

    if (!empty($USER->id) && ($userid == $USER->id) && !defined('FULLME')) {
        $db->Execute('UPDATE '. $CFG->prefix .'user
                         SET lastip=\''. $REMOTE_ADDR .'\', lastaccess=\''. $timenow .'\'
                       WHERE id = \''. $userid .'\' ');
        if ($courseid != SITEID && !empty($courseid)) {
            if (defined('MDL_PERFDB')) { global $PERF ; $PERF->dbqueries++;};

            if ($record = get_record('user_lastaccess', 'userid', $userid, 'courseid', $courseid)) {
                $record->timeaccess = $timenow;
                return update_record('user_lastaccess', $record);
            } else {
                $record = new object;
                $record->userid = $userid;
                $record->courseid = $courseid;
                $record->timeaccess = $timenow;
                return insert_record('user_lastaccess', $record);
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
                        GROUP BY day ");
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
                        GROUP BY hour ");
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

    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {    // Return information about all accounts
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

function course_parent_visible($course = null) {
    global $CFG;

    if (empty($course)) {
        return true;
    }
    if (!empty($CFG->allowvisiblecoursesinhiddencategories)) {
        return true;
    }
    return category_parent_visible($course->category);
}

function category_parent_visible($parent = 0) {

    static $visible;

    if (!$parent) {
        return true;
    }

    if (empty($visible)) {
        $visible = array(); // initialize
    }

    if (array_key_exists($parent,$visible)) {
        return $visible[$parent];
    }

    $category = get_record('course_categories', 'id', $parent);
    $list = explode('/', preg_replace('/^\/(.*)$/', '$1', $category->path));
    $list[] = $parent;
    $parents = get_records_list('course_categories', 'id', implode(',', $list), 'depth DESC');
    $v = true;
    foreach ($parents as $p) {
        if (!$p->visible) {
            $v = false;
        }
    }
    $visible[$parent] = $v; // now cache it
    return $v;
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
 * Get the lists of courses the current user has $cap capability in
 * I am not sure if this is needed, it loops through all courses so
 * could cause performance problems.
 * If it's not used, we can use a faster function to detect
 * capability in restorelib.php
 * @param string $cap
 * @return array
 */
function get_capability_courses($cap) {
    global $USER;

    $mycourses = array();
    if ($courses = get_records('course')) {
        foreach ($courses as $course) {
            if (has_capability($cap, get_context_instance(CONTEXT_COURSE, $course->id))) {
                $mycourses[] = $course->id;
            }
        }
    }

    return $mycourses;
}

/**
 * true or false function to see if user can create any courses at all
 * @return bool
 */
function user_can_create_courses() {
    global $USER;
    // if user has course creation capability at any site or course cat, then return true;

    if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        return true;
    } else {
        return (bool) count(get_creatable_categories());
    }

}

/**
 * get the list of categories the current user can create courses in
 * @return array
 */
function get_creatable_categories() {

    $creatablecats = array();
    if ($cats = get_records('course_categories')) {
        foreach ($cats as $cat) {
            if (has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $cat->id))) {
                $creatablecats[$cat->id] = $cat->name;
            }
        }
    }
    return $creatablecats;
}

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>

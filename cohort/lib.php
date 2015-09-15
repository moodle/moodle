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
 * Cohort related management functions, this file needs to be included manually.
 *
 * @package    core_cohort
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('COHORT_ALL', 0);
define('COHORT_COUNT_MEMBERS', 1);
define('COHORT_COUNT_ENROLLED_MEMBERS', 3);
define('COHORT_WITH_MEMBERS_ONLY', 5);
define('COHORT_WITH_ENROLLED_MEMBERS_ONLY', 17);
define('COHORT_WITH_NOTENROLLED_MEMBERS_ONLY', 23);

/**
 * Add new cohort.
 *
 * @param  stdClass $cohort
 * @return int new cohort id
 */
function cohort_add_cohort($cohort) {
    global $DB;

    if (!isset($cohort->name)) {
        throw new coding_exception('Missing cohort name in cohort_add_cohort().');
    }
    if (!isset($cohort->idnumber)) {
        $cohort->idnumber = NULL;
    }
    if (!isset($cohort->description)) {
        $cohort->description = '';
    }
    if (!isset($cohort->descriptionformat)) {
        $cohort->descriptionformat = FORMAT_HTML;
    }
    if (!isset($cohort->visible)) {
        $cohort->visible = 1;
    }
    if (empty($cohort->component)) {
        $cohort->component = '';
    }
    if (!isset($cohort->timecreated)) {
        $cohort->timecreated = time();
    }
    if (!isset($cohort->timemodified)) {
        $cohort->timemodified = $cohort->timecreated;
    }

    $cohort->id = $DB->insert_record('cohort', $cohort);

    $event = \core\event\cohort_created::create(array(
        'context' => context::instance_by_id($cohort->contextid),
        'objectid' => $cohort->id,
    ));
    $event->add_record_snapshot('cohort', $cohort);
    $event->trigger();

    return $cohort->id;
}

/**
 * Update existing cohort.
 * @param  stdClass $cohort
 * @return void
 */
function cohort_update_cohort($cohort) {
    global $DB;
    if (property_exists($cohort, 'component') and empty($cohort->component)) {
        // prevent NULLs
        $cohort->component = '';
    }
    $cohort->timemodified = time();
    $DB->update_record('cohort', $cohort);

    $event = \core\event\cohort_updated::create(array(
        'context' => context::instance_by_id($cohort->contextid),
        'objectid' => $cohort->id,
    ));
    $event->trigger();
}

/**
 * Delete cohort.
 * @param  stdClass $cohort
 * @return void
 */
function cohort_delete_cohort($cohort) {
    global $DB;

    if ($cohort->component) {
        // TODO: add component delete callback
    }

    $DB->delete_records('cohort_members', array('cohortid'=>$cohort->id));
    $DB->delete_records('cohort', array('id'=>$cohort->id));

    $event = \core\event\cohort_deleted::create(array(
        'context' => context::instance_by_id($cohort->contextid),
        'objectid' => $cohort->id,
    ));
    $event->add_record_snapshot('cohort', $cohort);
    $event->trigger();
}

/**
 * Somehow deal with cohorts when deleting course category,
 * we can not just delete them because they might be used in enrol
 * plugins or referenced in external systems.
 * @param  stdClass|coursecat $category
 * @return void
 */
function cohort_delete_category($category) {
    global $DB;
    // TODO: make sure that cohorts are really, really not used anywhere and delete, for now just move to parent or system context

    $oldcontext = context_coursecat::instance($category->id);

    if ($category->parent and $parent = $DB->get_record('course_categories', array('id'=>$category->parent))) {
        $parentcontext = context_coursecat::instance($parent->id);
        $sql = "UPDATE {cohort} SET contextid = :newcontext WHERE contextid = :oldcontext";
        $params = array('oldcontext'=>$oldcontext->id, 'newcontext'=>$parentcontext->id);
    } else {
        $syscontext = context_system::instance();
        $sql = "UPDATE {cohort} SET contextid = :newcontext WHERE contextid = :oldcontext";
        $params = array('oldcontext'=>$oldcontext->id, 'newcontext'=>$syscontext->id);
    }

    $DB->execute($sql, $params);
}

/**
 * Add cohort member
 * @param  int $cohortid
 * @param  int $userid
 * @return void
 */
function cohort_add_member($cohortid, $userid) {
    global $DB;
    if ($DB->record_exists('cohort_members', array('cohortid'=>$cohortid, 'userid'=>$userid))) {
        // No duplicates!
        return;
    }
    $record = new stdClass();
    $record->cohortid  = $cohortid;
    $record->userid    = $userid;
    $record->timeadded = time();
    $DB->insert_record('cohort_members', $record);

    $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);

    $event = \core\event\cohort_member_added::create(array(
        'context' => context::instance_by_id($cohort->contextid),
        'objectid' => $cohortid,
        'relateduserid' => $userid,
    ));
    $event->add_record_snapshot('cohort', $cohort);
    $event->trigger();
}

/**
 * Remove cohort member
 * @param  int $cohortid
 * @param  int $userid
 * @return void
 */
function cohort_remove_member($cohortid, $userid) {
    global $DB;
    $DB->delete_records('cohort_members', array('cohortid'=>$cohortid, 'userid'=>$userid));

    $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);

    $event = \core\event\cohort_member_removed::create(array(
        'context' => context::instance_by_id($cohort->contextid),
        'objectid' => $cohortid,
        'relateduserid' => $userid,
    ));
    $event->add_record_snapshot('cohort', $cohort);
    $event->trigger();
}

/**
 * Is this user a cohort member?
 * @param int $cohortid
 * @param int $userid
 * @return bool
 */
function cohort_is_member($cohortid, $userid) {
    global $DB;

    return $DB->record_exists('cohort_members', array('cohortid'=>$cohortid, 'userid'=>$userid));
}

/**
 * Returns the list of cohorts visible to the current user in the given course.
 *
 * The following fields are returned in each record: id, name, contextid, idnumber, visible
 * Fields memberscnt and enrolledcnt will be also returned if requested
 *
 * @param context $currentcontext
 * @param int $withmembers one of the COHORT_XXX constants that allows to return non empty cohorts only
 *      or cohorts with enroled/not enroled users, or just return members count
 * @param int $offset
 * @param int $limit
 * @param string $search
 * @return array
 */
function cohort_get_available_cohorts($currentcontext, $withmembers = 0, $offset = 0, $limit = 25, $search = '') {
    global $DB;

    $params = array();

    // Build context subquery. Find the list of parent context where user is able to see any or visible-only cohorts.
    // Since this method is normally called for the current course all parent contexts are already preloaded.
    $contextsany = array_filter($currentcontext->get_parent_context_ids(),
        create_function('$a', 'return has_capability("moodle/cohort:view", context::instance_by_id($a));'));
    $contextsvisible = array_diff($currentcontext->get_parent_context_ids(), $contextsany);
    if (empty($contextsany) && empty($contextsvisible)) {
        // User does not have any permissions to view cohorts.
        return array();
    }
    $subqueries = array();
    if (!empty($contextsany)) {
        list($parentsql, $params1) = $DB->get_in_or_equal($contextsany, SQL_PARAMS_NAMED, 'ctxa');
        $subqueries[] = 'c.contextid ' . $parentsql;
        $params = array_merge($params, $params1);
    }
    if (!empty($contextsvisible)) {
        list($parentsql, $params1) = $DB->get_in_or_equal($contextsvisible, SQL_PARAMS_NAMED, 'ctxv');
        $subqueries[] = '(c.visible = 1 AND c.contextid ' . $parentsql. ')';
        $params = array_merge($params, $params1);
    }
    $wheresql = '(' . implode(' OR ', $subqueries) . ')';

    // Build the rest of the query.
    $fromsql = "";
    $fieldssql = 'c.id, c.name, c.contextid, c.idnumber, c.visible';
    $groupbysql = '';
    $havingsql = '';
    if ($withmembers) {
        $fieldssql .= ', s.memberscnt';
        $subfields = "c.id, COUNT(DISTINCT cm.userid) AS memberscnt";
        $groupbysql = " GROUP BY c.id";
        $fromsql = " LEFT JOIN {cohort_members} cm ON cm.cohortid = c.id ";
        if (in_array($withmembers,
                array(COHORT_COUNT_ENROLLED_MEMBERS, COHORT_WITH_ENROLLED_MEMBERS_ONLY, COHORT_WITH_NOTENROLLED_MEMBERS_ONLY))) {
            list($esql, $params2) = get_enrolled_sql($currentcontext);
            $fromsql .= " LEFT JOIN ($esql) u ON u.id = cm.userid ";
            $params = array_merge($params2, $params);
            $fieldssql .= ', s.enrolledcnt';
            $subfields .= ', COUNT(DISTINCT u.id) AS enrolledcnt';
        }
        if ($withmembers == COHORT_WITH_MEMBERS_ONLY) {
            $havingsql = " HAVING COUNT(DISTINCT cm.userid) > 0";
        } else if ($withmembers == COHORT_WITH_ENROLLED_MEMBERS_ONLY) {
            $havingsql = " HAVING COUNT(DISTINCT u.id) > 0";
        } else if ($withmembers == COHORT_WITH_NOTENROLLED_MEMBERS_ONLY) {
            $havingsql = " HAVING COUNT(DISTINCT cm.userid) > COUNT(DISTINCT u.id)";
        }
    }
    if ($search) {
        list($searchsql, $searchparams) = cohort_get_search_query($search);
        $wheresql .= ' AND ' . $searchsql;
        $params = array_merge($params, $searchparams);
    }

    if ($withmembers) {
        $sql = "SELECT " . str_replace('c.', 'cohort.', $fieldssql) . "
                  FROM {cohort} cohort
                  JOIN (SELECT $subfields
                          FROM {cohort} c $fromsql
                         WHERE $wheresql $groupbysql $havingsql
                        ) s ON cohort.id = s.id
              ORDER BY cohort.name, cohort.idnumber";
    } else {
        $sql = "SELECT $fieldssql
                  FROM {cohort} c $fromsql
                 WHERE $wheresql
              ORDER BY c.name, c.idnumber";
    }

    return $DB->get_records_sql($sql, $params, $offset, $limit);
}

/**
 * Check if cohort exists and user is allowed to access it from the given context.
 *
 * @param stdClass|int $cohortorid cohort object or id
 * @param context $currentcontext current context (course) where visibility is checked
 * @return boolean
 */
function cohort_can_view_cohort($cohortorid, $currentcontext) {
    global $DB;
    if (is_numeric($cohortorid)) {
        $cohort = $DB->get_record('cohort', array('id' => $cohortorid), 'id, contextid, visible');
    } else {
        $cohort = $cohortorid;
    }

    if ($cohort && in_array($cohort->contextid, $currentcontext->get_parent_context_ids())) {
        if ($cohort->visible) {
            return true;
        }
        $cohortcontext = context::instance_by_id($cohort->contextid);
        if (has_capability('moodle/cohort:view', $cohortcontext)) {
            return true;
        }
    }
    return false;
}

/**
 * Produces a part of SQL query to filter cohorts by the search string
 *
 * Called from {@link cohort_get_cohorts()}, {@link cohort_get_all_cohorts()} and {@link cohort_get_available_cohorts()}
 *
 * @access private
 *
 * @param string $search search string
 * @param string $tablealias alias of cohort table in the SQL query (highly recommended if other tables are used in query)
 * @return array of two elements - SQL condition and array of named parameters
 */
function cohort_get_search_query($search, $tablealias = '') {
    global $DB;
    $params = array();
    if (empty($search)) {
        // This function should not be called if there is no search string, just in case return dummy query.
        return array('1=1', $params);
    }
    if ($tablealias && substr($tablealias, -1) !== '.') {
        $tablealias .= '.';
    }
    $searchparam = '%' . $DB->sql_like_escape($search) . '%';
    $conditions = array();
    $fields = array('name', 'idnumber', 'description');
    $cnt = 0;
    foreach ($fields as $field) {
        $conditions[] = $DB->sql_like($tablealias . $field, ':csearch' . $cnt, false);
        $params['csearch' . $cnt] = $searchparam;
        $cnt++;
    }
    $sql = '(' . implode(' OR ', $conditions) . ')';
    return array($sql, $params);
}

/**
 * Get all the cohorts defined in given context.
 *
 * The function does not check user capability to view/manage cohorts in the given context
 * assuming that it has been already verified.
 *
 * @param int $contextid
 * @param int $page number of the current page
 * @param int $perpage items per page
 * @param string $search search string
 * @return array    Array(totalcohorts => int, cohorts => array, allcohorts => int)
 */
function cohort_get_cohorts($contextid, $page = 0, $perpage = 25, $search = '') {
    global $DB;

    $fields = "SELECT *";
    $countfields = "SELECT COUNT(1)";
    $sql = " FROM {cohort}
             WHERE contextid = :contextid";
    $params = array('contextid' => $contextid);
    $order = " ORDER BY name ASC, idnumber ASC";

    if (!empty($search)) {
        list($searchcondition, $searchparams) = cohort_get_search_query($search);
        $sql .= ' AND ' . $searchcondition;
        $params = array_merge($params, $searchparams);
    }

    $totalcohorts = $allcohorts = $DB->count_records('cohort', array('contextid' => $contextid));
    if (!empty($search)) {
        $totalcohorts = $DB->count_records_sql($countfields . $sql, $params);
    }
    $cohorts = $DB->get_records_sql($fields . $sql . $order, $params, $page*$perpage, $perpage);

    return array('totalcohorts' => $totalcohorts, 'cohorts' => $cohorts, 'allcohorts' => $allcohorts);
}

/**
 * Get all the cohorts defined anywhere in system.
 *
 * The function assumes that user capability to view/manage cohorts on system level
 * has already been verified. This function only checks if such capabilities have been
 * revoked in child (categories) contexts.
 *
 * @param int $page number of the current page
 * @param int $perpage items per page
 * @param string $search search string
 * @return array    Array(totalcohorts => int, cohorts => array, allcohorts => int)
 */
function cohort_get_all_cohorts($page = 0, $perpage = 25, $search = '') {
    global $DB;

    $fields = "SELECT c.*, ".context_helper::get_preload_record_columns_sql('ctx');
    $countfields = "SELECT COUNT(*)";
    $sql = " FROM {cohort} c
             JOIN {context} ctx ON ctx.id = c.contextid ";
    $params = array();
    $wheresql = '';

    if ($excludedcontexts = cohort_get_invisible_contexts()) {
        list($excludedsql, $excludedparams) = $DB->get_in_or_equal($excludedcontexts, SQL_PARAMS_NAMED, 'excl', false);
        $wheresql = ' WHERE c.contextid '.$excludedsql;
        $params = array_merge($params, $excludedparams);
    }

    $totalcohorts = $allcohorts = $DB->count_records_sql($countfields . $sql . $wheresql, $params);

    if (!empty($search)) {
        list($searchcondition, $searchparams) = cohort_get_search_query($search, 'c');
        $wheresql .= ($wheresql ? ' AND ' : ' WHERE ') . $searchcondition;
        $params = array_merge($params, $searchparams);
        $totalcohorts = $DB->count_records_sql($countfields . $sql . $wheresql, $params);
    }

    $order = " ORDER BY c.name ASC, c.idnumber ASC";
    $cohorts = $DB->get_records_sql($fields . $sql . $wheresql . $order, $params, $page*$perpage, $perpage);

    // Preload used contexts, they will be used to check view/manage/assign capabilities and display categories names.
    foreach (array_keys($cohorts) as $key) {
        context_helper::preload_from_record($cohorts[$key]);
    }

    return array('totalcohorts' => $totalcohorts, 'cohorts' => $cohorts, 'allcohorts' => $allcohorts);
}

/**
 * Returns list of contexts where cohorts are present but current user does not have capability to view/manage them.
 *
 * This function is called from {@link cohort_get_all_cohorts()} to ensure correct pagination in rare cases when user
 * is revoked capability in child contexts. It assumes that user's capability to view/manage cohorts on system
 * level has already been verified.
 *
 * @access private
 *
 * @return array array of context ids
 */
function cohort_get_invisible_contexts() {
    global $DB;
    if (is_siteadmin()) {
        // Shortcut, admin can do anything and can not be prohibited from any context.
        return array();
    }
    $records = $DB->get_recordset_sql("SELECT DISTINCT ctx.id, ".context_helper::get_preload_record_columns_sql('ctx')." ".
        "FROM {context} ctx JOIN {cohort} c ON ctx.id = c.contextid ");
    $excludedcontexts = array();
    foreach ($records as $ctx) {
        context_helper::preload_from_record($ctx);
        if (!has_any_capability(array('moodle/cohort:manage', 'moodle/cohort:view'), context::instance_by_id($ctx->id))) {
            $excludedcontexts[] = $ctx->id;
        }
    }
    return $excludedcontexts;
}

/**
 * Returns navigation controls (tabtree) to be displayed on cohort management pages
 *
 * @param context $context system or category context where cohorts controls are about to be displayed
 * @param moodle_url $currenturl
 * @return null|renderable
 */
function cohort_edit_controls(context $context, moodle_url $currenturl) {
    $tabs = array();
    $currenttab = 'view';
    $viewurl = new moodle_url('/cohort/index.php', array('contextid' => $context->id));
    if (($searchquery = $currenturl->get_param('search'))) {
        $viewurl->param('search', $searchquery);
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $tabs[] = new tabobject('view', new moodle_url($viewurl, array('showall' => 0)), get_string('systemcohorts', 'cohort'));
        $tabs[] = new tabobject('viewall', new moodle_url($viewurl, array('showall' => 1)), get_string('allcohorts', 'cohort'));
        if ($currenturl->get_param('showall')) {
            $currenttab = 'viewall';
        }
    } else {
        $tabs[] = new tabobject('view', $viewurl, get_string('cohorts', 'cohort'));
    }
    if (has_capability('moodle/cohort:manage', $context)) {
        $addurl = new moodle_url('/cohort/edit.php', array('contextid' => $context->id));
        $tabs[] = new tabobject('addcohort', $addurl, get_string('addcohort', 'cohort'));
        if ($currenturl->get_path() === $addurl->get_path() && !$currenturl->param('id')) {
            $currenttab = 'addcohort';
        }

        $uploadurl = new moodle_url('/cohort/upload.php', array('contextid' => $context->id));
        $tabs[] = new tabobject('uploadcohorts', $uploadurl, get_string('uploadcohorts', 'cohort'));
        if ($currenturl->get_path() === $uploadurl->get_path()) {
            $currenttab = 'uploadcohorts';
        }
    }
    if (count($tabs) > 1) {
        return new tabtree($tabs, $currenttab);
    }
    return null;
}

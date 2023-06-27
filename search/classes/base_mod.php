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
 * Search area base class for areas working at module level.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Base implementation for search areas working at module level.
 *
 * Even if the search area works at multiple levels, if module is one of these levels
 * it should extend this class, as this class provides helper methods for module level search management.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_mod extends base {

    /**
     * The context levels the search area is working on.
     *
     * This can be overwriten by the search area if it works at multiple
     * levels.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_MODULE];

    /**
     * Returns the module name.
     *
     * @return string
     */
    protected function get_module_name() {
        return substr($this->componentname, 4);
    }

    /**
     * Gets the course module for the required instanceid + modulename.
     *
     * The returned data depends on the logged user, when calling this through
     * self::get_document the admin user is used so everything would be returned.
     *
     * No need more internal caching here, modinfo is already cached.
     *
     * @throws \dml_missing_record_exception
     * @param string $modulename The module name
     * @param int $instanceid Module instance id (depends on the module)
     * @param int $courseid Helps speeding up things
     * @return \cm_info
     */
    protected function get_cm($modulename, $instanceid, $courseid) {
        $modinfo = get_fast_modinfo($courseid);

        // Hopefully not many, they are indexed by cmid.
        $instances = $modinfo->get_instances_of($modulename);
        foreach ($instances as $cminfo) {
            if ($cminfo->instance == $instanceid) {
                return $cminfo;
            }
        }

        // Nothing found.
        throw new \dml_missing_record_exception($modulename);
    }

    /**
     * Helper function that gets SQL useful for restricting a search query given a passed-in
     * context.
     *
     * The SQL returned will be zero or more JOIN statements, surrounded by whitespace, which act
     * as restrictions on the query based on the rows in a module table.
     *
     * You can pass in a null or system context, which will both return an empty string and no
     * params.
     *
     * Returns an array with two nulls if there can be no results for the activity within this
     * context (e.g. it is a block context).
     *
     * If named parameters are used, these will be named gcrs0, gcrs1, etc. The table aliases used
     * in SQL also all begin with gcrs, to avoid conflicts.
     *
     * @param \context|null $context Context to restrict the query
     * @param string $modname Name of module e.g. 'forum'
     * @param string $modtable Alias of table containing module id
     * @param int $paramtype Type of SQL parameters to use (default question mark)
     * @return array Array with SQL and parameters; both null if no need to query
     * @throws \coding_exception If called with invalid params
     */
    protected function get_context_restriction_sql(\context $context = null, $modname, $modtable,
            $paramtype = SQL_PARAMS_QM) {
        global $DB;

        if (!$context) {
            return ['', []];
        }

        switch ($paramtype) {
            case SQL_PARAMS_QM:
                $param1 = '?';
                $param2 = '?';
                $param3 = '?';
                $key1 = 0;
                $key2 = 1;
                $key3 = 2;
                break;
            case SQL_PARAMS_NAMED:
                $param1 = ':gcrs0';
                $param2 = ':gcrs1';
                $param3 = ':gcrs2';
                $key1 = 'gcrs0';
                $key2 = 'gcrs1';
                $key3 = 'gcrs2';
                break;
            default:
                throw new \coding_exception('Unexpected $paramtype: ' . $paramtype);
        }

        $params = [];
        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                $sql = '';
                break;

            case CONTEXT_COURSECAT:
                // Find all activities of this type within the specified category or any
                // sub-category.
                $pathmatch = $DB->sql_like('gcrscc2.path', $DB->sql_concat('gcrscc1.path', $param3));
                $sql = " JOIN {course_modules} gcrscm ON gcrscm.instance = $modtable.id
                              AND gcrscm.module = (SELECT id FROM {modules} WHERE name = $param1)
                         JOIN {course} gcrsc ON gcrsc.id = gcrscm.course
                         JOIN {course_categories} gcrscc1 ON gcrscc1.id = $param2
                         JOIN {course_categories} gcrscc2 ON gcrscc2.id = gcrsc.category AND
                              (gcrscc2.id = gcrscc1.id OR $pathmatch) ";
                $params[$key1] = $modname;
                $params[$key2] = $context->instanceid;
                // Note: This param is a bit annoying as it obviously never changes, but sql_like
                // throws a debug warning if you pass it anything with quotes in, so it has to be
                // a bound parameter.
                $params[$key3] = '/%';
                break;

            case CONTEXT_COURSE:
                // Find all activities of this type within the course.
                $sql = " JOIN {course_modules} gcrscm ON gcrscm.instance = $modtable.id
                              AND gcrscm.course = $param1
                              AND gcrscm.module = (SELECT id FROM {modules} WHERE name = $param2) ";
                $params[$key1] = $context->instanceid;
                $params[$key2] = $modname;
                break;

            case CONTEXT_MODULE:
                // Find only the specified activity of this type.
                $sql = " JOIN {course_modules} gcrscm ON gcrscm.instance = $modtable.id
                              AND gcrscm.id = $param1
                              AND gcrscm.module = (SELECT id FROM {modules} WHERE name = $param2) ";
                $params[$key1] = $context->instanceid;
                $params[$key2] = $modname;
                break;

            case CONTEXT_BLOCK:
            case CONTEXT_USER:
                // These contexts cannot contain any activities, so return null.
                return [null, null];

            default:
                throw new \coding_exception('Unexpected contextlevel: ' . $context->contextlevel);
        }

        return [$sql, $params];
    }

    /**
     * This can be used in subclasses to change ordering within the get_contexts_to_reindex
     * function.
     *
     * It returns 2 values:
     * - Extra SQL joins (tables course_modules 'cm' and context 'x' already exist).
     * - An ORDER BY value which must use aggregate functions, by default 'MAX(cm.added) DESC'.
     *
     * Note the query already includes a GROUP BY on the context fields, so if your joins result
     * in multiple rows, you can use aggregate functions in the ORDER BY. See forum for an example.
     *
     * @return string[] Array with 2 elements; extra joins for the query, and ORDER BY value
     */
    protected function get_contexts_to_reindex_extra_sql() {
        return ['', 'MAX(cm.added) DESC'];
    }

    /**
     * Gets a list of all contexts to reindex when reindexing this search area.
     *
     * For modules, the default is to return all contexts for modules of that type, in order of
     * time added (most recent first).
     *
     * @return \Iterator Iterator of contexts to reindex
     * @throws \moodle_exception If any DB error
     */
    public function get_contexts_to_reindex() {
        global $DB;

        list ($extrajoins, $dborder) = $this->get_contexts_to_reindex_extra_sql();
        $contexts = [];
        $selectcolumns = \context_helper::get_preload_record_columns_sql('x');
        $groupbycolumns = '';
        foreach (\context_helper::get_preload_record_columns('x') as $column => $thing) {
            if ($groupbycolumns !== '') {
                $groupbycolumns .= ',';
            }
            $groupbycolumns .= $column;
        }
        $rs = $DB->get_recordset_sql("
                SELECT $selectcolumns
                  FROM {course_modules} cm
                  JOIN {context} x ON x.instanceid = cm.id AND x.contextlevel = ?
                       $extrajoins
                 WHERE cm.module = (SELECT id FROM {modules} WHERE name = ?)
              GROUP BY $groupbycolumns
              ORDER BY $dborder", [CONTEXT_MODULE, $this->get_module_name()]);
        return new \core\dml\recordset_walk($rs, function($rec) {
            $id = $rec->ctxid;
            \context_helper::preload_from_record($rec);
            return \context::instance_by_id($id);
        });
    }

    /**
     * Indicates whether this search area may restrict access by group.
     *
     * This should return true if the search area (sometimes) sets the 'groupid' schema field, and
     * false if it never sets that field.
     *
     * (If this function returns false, but the field is set, then results may be restricted
     * unintentionally.)
     *
     * If this returns true, the search engine will automatically apply group restrictions in some
     * cases (by default, where a module is configured to use separate groups). See function
     * restrict_cm_access_by_group().
     *
     * @return bool
     */
    public function supports_group_restriction() {
        return false;
    }

    /**
     * Checks whether the content of this search area should be restricted by group for a
     * specific module. Called at query time.
     *
     * The default behaviour simply checks if the effective group mode is SEPARATEGROUPS, which
     * is probably correct for most cases.
     *
     * If restricted by group, the search query will (where supported by the engine) filter out
     * results for groups the user does not belong to, unless the user has 'access all groups'
     * for the activity. This affects only documents which set the 'groupid' field; results with no
     * groupid will not be restricted.
     *
     * Even if you return true to this function, you may still need to do group access checks in
     * check_access, because the search engine may not support group restrictions.
     *
     * @param \cm_info $cm
     * @return bool True to restrict by group
     */
    public function restrict_cm_access_by_group(\cm_info $cm) {
        return $cm->effectivegroupmode == SEPARATEGROUPS;
    }

    /**
     * Returns an icon instance for the document.
     *
     * @param \core_search\document $doc
     * @return \core_search\document_icon
     */
    public function get_doc_icon(document $doc) : document_icon {
        return new document_icon('icon', $this->get_module_name());
    }

    /**
     * Returns a list of category names associated with the area.
     *
     * @return array
     */
    public function get_category_names() {
        return [manager::SEARCH_AREA_CATEGORY_COURSE_CONTENT];
    }
}

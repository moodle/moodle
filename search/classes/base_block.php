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
 * Search area base class for blocks.
 *
 * Note: Only blocks within courses are supported.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area base class for blocks.
 *
 * Note: Only blocks within courses are supported.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_block extends base {
    /** @var string Cache name used for block instances */
    const CACHE_INSTANCES = 'base_block_instances';

    /**
     * The context levels the search area is working on.
     *
     * This can be overwriten by the search area if it works at multiple
     * levels.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_BLOCK];

    /**
     * Gets the block name only.
     *
     * @return string Block name e.g. 'html'
     */
    public function get_block_name() {
        // Remove 'block_' text.
        return substr($this->get_component_name(), 6);
    }

    /**
     * Returns restrictions on which block_instances rows to return. By default, excludes rows
     * that have empty configdata.
     *
     * If no restriction is required, you could return ['', []].
     *
     * @return array 2-element array of SQL restriction and params for it
     */
    protected function get_indexing_restrictions() {
        global $DB;

        // This includes completely empty configdata, and also three other values that are
        // equivalent to empty:
        // - A serialized completely empty object.
        // - A serialized object with one field called '0' (string not int) set to boolean false
        //   (this can happen after backup and restore, at least historically).
        // - A serialized null.
        $stupidobject = (object)[];
        $zero = '0';
        $stupidobject->{$zero} = false;
        return [$DB->sql_compare_text('bi.configdata') . " != ? AND " .
                $DB->sql_compare_text('bi.configdata') . " != ? AND " .
                $DB->sql_compare_text('bi.configdata') . " != ? AND " .
                $DB->sql_compare_text('bi.configdata') . " != ?",
                ['', base64_encode(serialize((object)[])), base64_encode(serialize($stupidobject)),
                base64_encode(serialize(null))]];
    }

    /**
     * Gets recordset of all blocks of this type modified since given time within the given context.
     *
     * See base class for detailed requirements. This implementation includes the key fields
     * from block_instances.
     *
     * This can be overridden to do something totally different if the block's data is stored in
     * other tables.
     *
     * If there are certain instances of the block which should not be included in the search index
     * then you can override get_indexing_restrictions; by default this excludes rows with empty
     * configdata.
     *
     * @param int $modifiedfrom Return only records modified after this date
     * @param \context|null $context Context to find blocks within
     * @return false|\moodle_recordset|null
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        // Get context restrictions.
        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql($context, 'bi');

        // Get custom restrictions for block type.
        list ($restrictions, $restrictionparams) = $this->get_indexing_restrictions();
        if ($restrictions) {
            $restrictions = 'AND ' . $restrictions;
        }

        // Query for all entries in block_instances for this type of block, within the specified
        // context. The query is based on the one from get_recordset_by_timestamp and applies the
        // same restrictions.
        return $DB->get_recordset_sql("
                SELECT bi.id, bi.timemodified, bi.timecreated, bi.configdata,
                       c.id AS courseid, x.id AS contextid
                  FROM {block_instances} bi
                       $contextjoin
                  JOIN {context} x ON x.instanceid = bi.id AND x.contextlevel = ?
                  JOIN {context} parent ON parent.id = bi.parentcontextid
             LEFT JOIN {course_modules} cm ON cm.id = parent.instanceid AND parent.contextlevel = ?
                  JOIN {course} c ON c.id = cm.course
                       OR (c.id = parent.instanceid AND parent.contextlevel = ?)
                 WHERE bi.timemodified >= ?
                       AND bi.blockname = ?
                       AND (parent.contextlevel = ? AND (" . $DB->sql_like('bi.pagetypepattern', '?') . "
                           OR bi.pagetypepattern IN ('site-index', 'course-*', '*')))
                       $restrictions
              ORDER BY bi.timemodified ASC",
                array_merge($contextparams, [CONTEXT_BLOCK, CONTEXT_MODULE, CONTEXT_COURSE,
                    $modifiedfrom, $this->get_block_name(), CONTEXT_COURSE, 'course-view-%'],
                $restrictionparams));
    }

    public function get_doc_url(\core_search\document $doc) {
        // Load block instance and find cmid if there is one.
        $blockinstanceid = preg_replace('~^.*-~', '', $doc->get('id'));
        $instance = $this->get_block_instance($blockinstanceid);
        $courseid = $doc->get('courseid');
        $anchor = 'inst' . $blockinstanceid;

        // Check if the block is at course or module level.
        if ($instance->cmid) {
            // No module-level page types are supported at present so the search system won't return
            // them. But let's put some example code here to indicate how it could work.
            debugging('Unexpected module-level page type for block ' . $blockinstanceid . ': ' .
                    $instance->pagetypepattern, DEBUG_DEVELOPER);
            $modinfo = get_fast_modinfo($courseid);
            $cm = $modinfo->get_cm($instance->cmid);
            return new \moodle_url($cm->url, null, $anchor);
        } else {
            // The block is at course level. Let's check the page type, although in practice we
            // currently only support the course main page.
            if ($instance->pagetypepattern === '*' || $instance->pagetypepattern === 'course-*' ||
                    preg_match('~^course-view-(.*)$~', $instance->pagetypepattern)) {
                return new \moodle_url('/course/view.php', ['id' => $courseid], $anchor);
            } else if ($instance->pagetypepattern === 'site-index') {
                return new \moodle_url('/', ['redirect' => 0], $anchor);
            } else {
                debugging('Unexpected page type for block ' . $blockinstanceid . ': ' .
                        $instance->pagetypepattern, DEBUG_DEVELOPER);
                return new \moodle_url('/course/view.php', ['id' => $courseid], $anchor);
            }
        }
    }

    public function get_context_url(\core_search\document $doc) {
        return $this->get_doc_url($doc);
    }

    /**
     * Checks access for a document in this search area.
     *
     * If you override this function for a block, you should call this base class version first
     * as it will check that the block is still visible to users in a supported location.
     *
     * @param int $id Document id
     * @return int manager:ACCESS_xx constant
     */
    public function check_access($id) {
        $instance = $this->get_block_instance($id, IGNORE_MISSING);
        if (!$instance) {
            // This generally won't happen because if the block has been deleted then we won't have
            // included its context in the search area list, but just in case.
            return manager::ACCESS_DELETED;
        }

        // Check block has not been moved to an unsupported area since it was indexed. (At the
        // moment, only blocks within site and course context are supported, also only certain
        // page types.)
        if (!$instance->courseid ||
                !self::is_supported_page_type_at_course_context($instance->pagetypepattern)) {
            return manager::ACCESS_DELETED;
        }

        // Note we do not need to check if the block was hidden or if the user has access to the
        // context, because those checks are included in the list of search contexts user can access
        // that is calculated in manager.php every time they do a query.
        return manager::ACCESS_GRANTED;
    }

    /**
     * Checks if a page type is supported for blocks when at course (or also site) context. This
     * function should be consistent with the SQL in get_recordset_by_timestamp.
     *
     * @param string $pagetype Page type
     * @return bool True if supported
     */
    protected static function is_supported_page_type_at_course_context($pagetype) {
        if (in_array($pagetype, ['site-index', 'course-*', '*'])) {
            return true;
        }
        if (preg_match('~^course-view-~', $pagetype)) {
            return true;
        }
        return false;
    }

    /**
     * Gets a block instance with given id.
     *
     * Returns the fields id, pagetypepattern, subpagepattern from block_instances and also the
     * cmid (if parent context is an activity module).
     *
     * @param int $id ID of block instance
     * @param int $strictness MUST_EXIST or IGNORE_MISSING
     * @return false|mixed Block instance data (may be false if strictness is IGNORE_MISSING)
     */
    protected function get_block_instance($id, $strictness = MUST_EXIST) {
        global $DB;

        $cache = \cache::make_from_params(\cache_store::MODE_REQUEST, 'core_search',
                self::CACHE_INSTANCES, [], ['simplekeys' => true]);
        $id = (int)$id;
        $instance = $cache->get($id);
        if (!$instance) {
            $instance = $DB->get_record_sql("
                    SELECT bi.id, bi.pagetypepattern, bi.subpagepattern,
                           c.id AS courseid, cm.id AS cmid
                      FROM {block_instances} bi
                      JOIN {context} parent ON parent.id = bi.parentcontextid
                 LEFT JOIN {course} c ON c.id = parent.instanceid AND parent.contextlevel = ?
                 LEFT JOIN {course_modules} cm ON cm.id = parent.instanceid AND parent.contextlevel = ?
                     WHERE bi.id = ?",
                    [CONTEXT_COURSE, CONTEXT_MODULE, $id], $strictness);
            $cache->set($id, $instance);
        }
        return $instance;
    }

    /**
     * Clears static cache. This function can be removed (with calls to it in the test script
     * replaced with cache_helper::purge_all) if MDL-59427 is fixed.
     */
    public static function clear_static() {
        \cache::make_from_params(\cache_store::MODE_REQUEST, 'core_search',
                self::CACHE_INSTANCES, [], ['simplekeys' => true])->purge();
    }

    /**
     * Helper function that gets SQL useful for restricting a search query given a passed-in
     * context.
     *
     * The SQL returned will be one or more JOIN statements, surrounded by whitespace, which act
     * as restrictions on the query based on the rows in the block_instances table.
     *
     * We assume the block instances have already been restricted by blockname.
     *
     * Returns null if there can be no results for this block within this context.
     *
     * If named parameters are used, these will be named gcrs0, gcrs1, etc. The table aliases used
     * in SQL also all begin with gcrs, to avoid conflicts.
     *
     * @param \context|null $context Context to restrict the query
     * @param string $blocktable Alias of block_instances table
     * @param int $paramtype Type of SQL parameters to use (default question mark)
     * @return array Array with SQL and parameters
     * @throws \coding_exception If called with invalid params
     */
    protected function get_context_restriction_sql(\context $context = null, $blocktable = 'bi',
            $paramtype = SQL_PARAMS_QM) {
        global $DB;

        if (!$context) {
            return ['', []];
        }

        switch ($paramtype) {
            case SQL_PARAMS_QM:
                $param1 = '?';
                $param2 = '?';
                $key1 = 0;
                $key2 = 1;
                break;
            case SQL_PARAMS_NAMED:
                $param1 = ':gcrs0';
                $param2 = ':gcrs1';
                $key1 = 'gcrs0';
                $key2 = 'gcrs1';
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
            case CONTEXT_COURSE:
            case CONTEXT_MODULE:
            case CONTEXT_USER:
                // Find all blocks whose parent is within the specified context.
                $sql = " JOIN {context} gcrsx ON gcrsx.id = $blocktable.parentcontextid
                              AND (gcrsx.id = $param1 OR " . $DB->sql_like('gcrsx.path', $param2) . ") ";
                $params[$key1] = $context->id;
                $params[$key2] = $context->path . '/%';
                break;

            case CONTEXT_BLOCK:
                // Find only the specified block of this type. Since we are generating JOINs
                // here, we do this by joining again to the block_instances table with the same ID.
                $sql = " JOIN {block_instances} gcrsbi ON gcrsbi.id = $blocktable.id
                              AND gcrsbi.id = $param1 ";
                $params[$key1] = $context->instanceid;
                break;

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
     * - Extra SQL joins (tables block_instances 'bi' and context 'x' already exist).
     * - An ORDER BY value which must use aggregate functions, by default 'MAX(bi.timemodified) DESC'.
     *
     * Note the query already includes a GROUP BY on the context fields, so if your joins result
     * in multiple rows, you can use aggregate functions in the ORDER BY. See forum for an example.
     *
     * @return string[] Array with 2 elements; extra joins for the query, and ORDER BY value
     */
    protected function get_contexts_to_reindex_extra_sql() {
        return ['', 'MAX(bi.timemodified) DESC'];
    }

    /**
     * Gets a list of all contexts to reindex when reindexing this search area.
     *
     * For blocks, the default is to return all contexts for blocks of that type, that are on a
     * course page, in order of time added (most recent first).
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
                  FROM {block_instances} bi
                  JOIN {context} x ON x.instanceid = bi.id AND x.contextlevel = ?
                  JOIN {context} parent ON parent.id = bi.parentcontextid
                       $extrajoins
                 WHERE bi.blockname = ? AND parent.contextlevel = ?
              GROUP BY $groupbycolumns
              ORDER BY $dborder", [CONTEXT_BLOCK, $this->get_block_name(), CONTEXT_COURSE]);
        return new \core\dml\recordset_walk($rs, function($rec) {
            $id = $rec->ctxid;
            \context_helper::preload_from_record($rec);
            return \context::instance_by_id($id);
        });
    }

    /**
     * Returns an icon instance for the document.
     *
     * @param \core_search\document $doc
     * @return \core_search\document_icon
     */
    public function get_doc_icon(document $doc): document_icon {
        return new document_icon('e/anchor');
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

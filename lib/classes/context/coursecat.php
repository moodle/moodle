<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\context;

use core\context;
use stdClass;
use coding_exception, moodle_url;

/**
 * Course category context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 */
class coursecat extends context {
    /** @var int numeric context level value matching legacy CONTEXT_COURSECAT */
    public const LEVEL = 40;

    /**
     * Please use \core\context\coursecat::instance($coursecatid) if you need the instance of context.
     * Alternatively if you know only the context id use \core\context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != self::LEVEL) {
            throw new coding_exception('Invalid $record->contextlevel in core\context\coursecat constructor.');
        }
    }

    /**
     * Returns short context name.
     *
     * @since Moodle 4.2
     *
     * @return string
     */
    public static function get_short_name(): string {
        return 'coursecat';
    }

    /**
     * Returns human readable context level name.
     *
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
     * @param boolean $escape Whether the returned name of the context is to be HTML escaped or not.
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        global $DB;

        $name = '';
        if ($category = $DB->get_record('course_categories', array('id' => $this->_instanceid))) {
            if ($withprefix) {
                $name = get_string('category').': ';
            }
            if (!$escape) {
                $name .= format_string($category->name, true, array('context' => $this, 'escape' => false));
            } else {
                $name .= format_string($category->name, true, array('context' => $this));
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
        return new moodle_url('/course/index.php', array('categoryid' => $this->_instanceid));
    }

    /**
     * Returns context instance database name.
     *
     * @return string|null table name for all levels except system.
     */
    protected static function get_instance_table(): ?string {
        return 'course_categories';
    }

    /**
     * Returns list of columns that can be used from behat
     * to look up context by reference.
     *
     * @return array list of column names from instance table
     */
    protected static function get_behat_reference_columns(): array {
        return ['idnumber'];
    }

    /**
     * Returns list of all role archetypes that are compatible
     * with role assignments in context level.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    protected static function get_compatible_role_archetypes(): array {
        return ['manager', 'coursecreator'];
    }

    /**
     * Returns list of all possible parent context levels.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    public static function get_possible_parent_levels(): array {
        return [system::LEVEL, self::LEVEL];
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @param string $sort
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        global $DB;

        $levels = \core\context_helper::get_child_levels(self::LEVEL);
        $levels[] = self::LEVEL;

        return $DB->get_records_list('capabilities', 'contextlevel', $levels, $sort);
    }

    /**
     * Returns course category context instance.
     *
     * @param int $categoryid id from {course_categories} table
     * @param int $strictness
     * @return coursecat|false context instance
     */
    public static function instance($categoryid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(self::LEVEL, $categoryid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel' => self::LEVEL, 'instanceid' => $categoryid))) {
            if ($category = $DB->get_record('course_categories', array('id' => $categoryid), 'id,parent', $strictness)) {
                if ($category->parent) {
                    $parentcontext = self::instance($category->parent);
                    $record = context::insert_context_record(self::LEVEL, $category->id, $parentcontext->path);
                } else {
                    $record = context::insert_context_record(self::LEVEL, $category->id, '/'.SYSCONTEXTID, 0);
                }
            }
        }

        if ($record) {
            $context = new coursecat($record);
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

        if (empty($this->_path) || empty($this->_depth)) {
            debugging('Can not find child contexts of context '.$this->_id.' try rebuilding of context paths');
            return array();
        }

        $sql = "SELECT ctx.*
                  FROM {context} ctx
                 WHERE ctx.path LIKE ? AND (ctx.depth = ? OR ctx.contextlevel = ?)";
        $params = array($this->_path.'/%', $this->depth + 1, self::LEVEL);
        $records = $DB->get_records_sql($sql, $params);

        $result = array();
        foreach ($records as $record) {
            $result[$record->id] = context::create_instance_from_record($record);
        }

        return $result;
    }

    /**
     * Create missing context instances at course category context level
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "SELECT ".self::LEVEL.", cc.id
                  FROM {course_categories} cc
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE cc.id = cx.instanceid AND cx.contextlevel=".self::LEVEL.")";
        $contextdata = $DB->get_recordset_sql($sql);
        foreach ($contextdata as $context) {
            context::insert_context_record(self::LEVEL, $context->id, null);
        }
        $contextdata->close();
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        $sql = "
                  SELECT c.*
                    FROM {context} c
         LEFT OUTER JOIN {course_categories} cc ON c.instanceid = cc.id
                   WHERE cc.id IS NULL AND c.contextlevel = ".self::LEVEL."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at course category context level.
     *
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force || $DB->record_exists_select('context', "contextlevel = ".self::LEVEL." AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = $emptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
                $emptyclause = "AND ({context}.path IS NULL OR {context}.depth = 0)";
            }

            $base = '/'.SYSCONTEXTID;

            // Normal top level categories.
            $sql = "UPDATE {context}
                       SET depth=2,
                           path=".$DB->sql_concat("'$base/'", 'id')."
                     WHERE contextlevel=".self::LEVEL."
                           AND EXISTS (SELECT 'x'
                                         FROM {course_categories} cc
                                        WHERE cc.id = {context}.instanceid AND cc.depth=1)
                           $emptyclause";
            $DB->execute($sql);

            // Deeper categories - one query per depthlevel.
            $maxdepth = $DB->get_field_sql("SELECT MAX(depth) FROM {course_categories}");
            for ($n = 2; $n <= $maxdepth; $n++) {
                $sql = "INSERT INTO {context_temp} (id, path, depth, locked)
                        SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1, ctx.locked
                          FROM {context} ctx
                          JOIN {course_categories} cc ON (cc.id = ctx.instanceid AND ctx.contextlevel = ".self::LEVEL."
                               AND cc.depth = $n)
                          JOIN {context} pctx ON (pctx.instanceid = cc.parent AND pctx.contextlevel = ".self::LEVEL.")
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

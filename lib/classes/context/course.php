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
 * Course context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 */
class course extends context {
    /** @var int numeric context level value matching legacy CONTEXT_COURSE */
    public const LEVEL = 50;

    /**
     * Please use \core\context\course::instance($courseid) if you need the instance of context.
     * Alternatively if you know only the context id use \core\context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != self::LEVEL) {
            throw new coding_exception('Invalid $record->contextlevel in core\context\course constructor.');
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
        return 'course';
    }

    /**
     * Returns human readable context level name.
     *
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('course');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with Course
     * @param boolean $short whether to use the short name of the thing.
     * @param bool $escape Whether the returned category name is to be HTML escaped or not.
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        global $DB;

        $name = '';
        if ($this->_instanceid == SITEID) {
            $name = get_string('frontpage', 'admin');
        } else {
            if ($course = $DB->get_record('course', array('id' => $this->_instanceid))) {
                if ($withprefix) {
                    $name = get_string('course').': ';
                }
                if ($short) {
                    if (!$escape) {
                        $name .= format_string($course->shortname, true, array('context' => $this, 'escape' => false));
                    } else {
                        $name .= format_string($course->shortname, true, array('context' => $this));
                    }
                } else {
                    if (!$escape) {
                        $name .= format_string(get_course_display_name_for_list($course), true, array('context' => $this,
                            'escape' => false));
                    } else {
                        $name .= format_string(get_course_display_name_for_list($course), true, array('context' => $this));
                    }
                }
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
        if ($this->_instanceid != SITEID) {
            return new moodle_url('/course/view.php', array('id' => $this->_instanceid));
        }

        return new moodle_url('/');
    }

    /**
     * Returns context instance database name.
     *
     * @return string|null table name for all levels except system.
     */
    protected static function get_instance_table(): ?string {
        return 'course';
    }

    /**
     * Returns list of columns that can be used from behat
     * to look up context by reference.
     *
     * @return array list of column names from instance table
     */
    protected static function get_behat_reference_columns(): array {
        return ['shortname'];
    }

    /**
     * Returns list of all role archetypes that are compatible
     * with role assignments in context level.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    protected static function get_compatible_role_archetypes(): array {
        return ['manager', 'editingteacher', 'teacher', 'student'];
    }

    /**
     * Returns list of all possible parent context levels.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    public static function get_possible_parent_levels(): array {
        return [coursecat::LEVEL];
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
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        return $this;
    }

    /**
     * Returns course context instance.
     *
     * @param int $courseid id from {course} table
     * @param int $strictness
     * @return course|false context instance
     */
    public static function instance($courseid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(self::LEVEL, $courseid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel' => self::LEVEL, 'instanceid' => $courseid))) {
            if ($course = $DB->get_record('course', array('id' => $courseid), 'id,category', $strictness)) {
                if ($course->category) {
                    $parentcontext = coursecat::instance($course->category);
                    $record = context::insert_context_record(self::LEVEL, $course->id, $parentcontext->path);
                } else {
                    $record = context::insert_context_record(self::LEVEL, $course->id, '/'.SYSCONTEXTID, 0);
                }
            }
        }

        if ($record) {
            $context = new course($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Create missing context instances at course context level
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "SELECT ".self::LEVEL.", c.id
                  FROM {course} c
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE c.id = cx.instanceid AND cx.contextlevel=".self::LEVEL.")";
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
         LEFT OUTER JOIN {course} co ON c.instanceid = co.id
                   WHERE co.id IS NULL AND c.contextlevel = ".self::LEVEL."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at course context level.
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

            // Standard frontpage.
            $sql = "UPDATE {context}
                       SET depth = 2,
                           path = ".$DB->sql_concat("'$base/'", 'id')."
                     WHERE contextlevel = ".self::LEVEL."
                           AND EXISTS (SELECT 'x'
                                         FROM {course} c
                                        WHERE c.id = {context}.instanceid AND c.category = 0)
                           $emptyclause";
            $DB->execute($sql);

            // Standard courses.
            $sql = "INSERT INTO {context_temp} (id, path, depth, locked)
                    SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1, ctx.locked
                      FROM {context} ctx
                      JOIN {course} c ON (c.id = ctx.instanceid AND ctx.contextlevel = ".self::LEVEL." AND c.category <> 0)
                      JOIN {context} pctx ON (pctx.instanceid = c.category AND pctx.contextlevel = ".coursecat::LEVEL.")
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

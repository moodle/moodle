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
 * Block context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 */
class block extends context {
    /** @var int numeric context level value matching legacy CONTEXT_BLOCK */
    public const LEVEL = 80;

    /**
     * Please use \core\context\block::instance($blockinstanceid) if you need the instance of context.
     * Alternatively if you know only the context id use \core\context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != self::LEVEL) {
            throw new coding_exception('Invalid $record->contextlevel in core\context\block constructor');
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
        return 'block';
    }

    /**
     * Returns human readable context level name.
     *
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('block');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with Block
     * @param boolean $short does not apply to block context
     * @param boolean $escape does not apply to block context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        global $DB, $CFG;

        $name = '';
        if ($blockinstance = $DB->get_record('block_instances', array('id' => $this->_instanceid))) {
            global $CFG;
            require_once("$CFG->dirroot/blocks/moodleblock.class.php");
            require_once("$CFG->dirroot/blocks/$blockinstance->blockname/block_$blockinstance->blockname.php");
            $blockname = "block_$blockinstance->blockname";
            if ($blockobject = new $blockname()) {
                if ($withprefix) {
                    $name = get_string('block').': ';
                }
                $name .= $blockobject->title;
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
        $parentcontexts = $this->get_parent_context();
        return $parentcontexts->get_url();
    }

    /**
     * Returns list of all possible parent context levels.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    public static function get_possible_parent_levels(): array {
        // Blocks may be added to any other context instance.
        $alllevels = \core\context_helper::get_all_levels();
        unset($alllevels[self::LEVEL]);
        return array_keys($alllevels);
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @param string $sort
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        global $DB;

        $bi = $DB->get_record('block_instances', array('id' => $this->_instanceid));

        $select = '(contextlevel = :level AND component = :component)';
        $params = [
            'level' => self::LEVEL,
            'component' => 'block_' . $bi->blockname,
        ];

        $extracaps = block_method_result($bi->blockname, 'get_extra_capabilities');
        if ($extracaps) {
            list($extra, $extraparams) = $DB->get_in_or_equal($extracaps, SQL_PARAMS_NAMED, 'cap');
            $select .= " OR name $extra";
            $params = array_merge($params, $extraparams);
        }

        return $DB->get_records_select('capabilities', $select, $params, $sort);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        $parentcontext = $this->get_parent_context();
        return $parentcontext->get_course_context($strict);
    }

    /**
     * Returns block context instance.
     *
     * @param int $blockinstanceid id from {block_instances} table.
     * @param int $strictness
     * @return block|false context instance
     */
    public static function instance($blockinstanceid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(self::LEVEL, $blockinstanceid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel' => self::LEVEL, 'instanceid' => $blockinstanceid))) {
            if ($bi = $DB->get_record('block_instances', array('id' => $blockinstanceid), 'id,parentcontextid', $strictness)) {
                $parentcontext = context::instance_by_id($bi->parentcontextid);
                $record = context::insert_context_record(self::LEVEL, $bi->id, $parentcontext->path);
            }
        }

        if ($record) {
            $context = new block($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Block do not have child contexts...
     * @return array
     */
    public function get_child_contexts() {
        return array();
    }

    /**
     * Create missing context instances at block context level
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = <<<EOF
            INSERT INTO {context} (
                contextlevel,
                instanceid
            ) SELECT
                :contextlevel,
                bi.id as instanceid
               FROM {block_instances} bi
               WHERE NOT EXISTS (
                   SELECT 'x' FROM {context} cx WHERE bi.id = cx.instanceid AND cx.contextlevel = :existingcontextlevel
               )
        EOF;

        $DB->execute($sql, [
            'contextlevel' => self::LEVEL,
            'existingcontextlevel' => self::LEVEL,
        ]);
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
         LEFT OUTER JOIN {block_instances} bi ON c.instanceid = bi.id
                   WHERE bi.id IS NULL AND c.contextlevel = ".self::LEVEL."
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at block context level.
     *
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force || $DB->record_exists_select('context', "contextlevel = ".self::LEVEL." AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
            }

            // The pctx.path IS NOT NULL prevents fatal problems with broken block instances that point to invalid context parent.
            $sql = "INSERT INTO {context_temp} (id, path, depth, locked)
                    SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1, ctx.locked
                      FROM {context} ctx
                      JOIN {block_instances} bi ON (bi.id = ctx.instanceid AND ctx.contextlevel = " . self::LEVEL . ")
                      JOIN {context} pctx ON (pctx.id = bi.parentcontextid)
                     WHERE (pctx.path IS NOT NULL AND pctx.depth > 0)
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

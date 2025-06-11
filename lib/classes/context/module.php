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
 * Course module context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 */
class module extends context {
    /** @var int numeric context level value matching legacy CONTEXT_MODULE */
    public const LEVEL = 70;

    /**
     * Please use \core\context\module::instance($cmid) if you need the instance of context.
     * Alternatively if you know only the context id use \core\context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != self::LEVEL) {
            throw new coding_exception('Invalid $record->contextlevel in core\context\module constructor.');
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
        return 'module';
    }

    /**
     * Returns human readable context level name.
     *
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('activitymodule');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with the
     *      module name, e.g. Forum, Glossary, etc.
     * @param boolean $short does not apply to module context
     * @param boolean $escape Whether the returned name of the context is to be HTML escaped or not.
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        global $DB;

        $name = '';
        if ($cm = $DB->get_record_sql("SELECT cm.*, md.name AS modname
                                         FROM {course_modules} cm
                                         JOIN {modules} md ON md.id = cm.module
                                        WHERE cm.id = ?", array($this->_instanceid))) {
            if ($mod = $DB->get_record($cm->modname, array('id' => $cm->instance))) {
                if ($withprefix) {
                    $name = get_string('modulename', $cm->modname).': ';
                }
                if (!$escape) {
                    $name .= format_string($mod->name, true, array('context' => $this, 'escape' => false));
                } else {
                    $name .= format_string($mod->name, true, array('context' => $this));
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
        global $DB;

        if ($modname = $DB->get_field_sql("SELECT md.name AS modname
                                             FROM {course_modules} cm
                                             JOIN {modules} md ON md.id = cm.module
                                            WHERE cm.id = ?", array($this->_instanceid))) {
            return new moodle_url('/mod/' . $modname . '/view.php', array('id' => $this->_instanceid));
        }

        return new moodle_url('/');
    }

    /**
     * Returns context instance database name.
     *
     * @return string|null table name for all levels except system.
     */
    protected static function get_instance_table(): ?string {
        return 'course_modules';
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
        return ['editingteacher', 'teacher', 'student'];
    }

    /**
     * Returns list of all possible parent context levels.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    public static function get_possible_parent_levels(): array {
        return [course::LEVEL];
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @param string $sort
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        global $DB, $CFG;

        $cm = $DB->get_record('course_modules', array('id' => $this->_instanceid));
        $module = $DB->get_record('modules', array('id' => $cm->module));

        $subcaps = array();

        $modulepath = "{$CFG->dirroot}/mod/{$module->name}";
        $subplugins = \core\component::get_subplugins("mod_{$module->name}");

        if (!empty($subplugins)) {
            foreach (array_keys($subplugins) as $subplugintype) {
                foreach (array_keys(\core_component::get_plugin_list($subplugintype)) as $subpluginname) {
                    $subcaps = array_merge($subcaps, array_keys(load_capability_def($subplugintype.'_'.$subpluginname)));
                }
            }
        }

        $modfile = "{$modulepath}/lib.php";
        $extracaps = array();
        if (file_exists($modfile)) {
            include_once($modfile);
            $modfunction = $module->name.'_get_extra_capabilities';
            if (function_exists($modfunction)) {
                $extracaps = $modfunction();
            }
        }

        $extracaps = array_merge($subcaps, $extracaps);
        $extra = '';
        list($extra, $params) = $DB->get_in_or_equal(
            $extracaps, SQL_PARAMS_NAMED, 'cap0', true, '');
        if (!empty($extra)) {
            $extra = "OR name $extra";
        }

        // Fetch the list of modules, and remove this one.
        $components = \core_component::get_component_list();
        $componentnames = $components['mod'];
        unset($componentnames["mod_{$module->name}"]);
        $componentnames = array_keys($componentnames);

        // Exclude all other modules.
        list($notcompsql, $notcompparams) = $DB->get_in_or_equal($componentnames, SQL_PARAMS_NAMED, 'notcomp', false);
        $params = array_merge($params, $notcompparams);

        // Exclude other component submodules.
        $i = 0;
        $ignorecomponents = [];
        foreach ($componentnames as $mod) {
            if ($subplugins = \core_component::get_subplugins($mod)) {
                foreach (array_keys($subplugins) as $subplugintype) {
                    $paramname = "notlike{$i}";
                    $ignorecomponents[] = $DB->sql_like('component', ":{$paramname}", true, true, true);
                    $params[$paramname] = "{$subplugintype}_%";
                    $i++;
                }
            }
        }
        $notlikesql = "(" . implode(' AND ', $ignorecomponents) . ")";

        $sql = "SELECT *
                  FROM {capabilities}
                 WHERE (contextlevel = ".self::LEVEL."
                   AND component {$notcompsql}
                   AND {$notlikesql})
                       $extra
              ORDER BY $sort";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return course|false context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        return $this->get_parent_context();
    }

    /**
     * Returns module context instance.
     *
     * @param int $cmid id of the record from {course_modules} table; pass cmid there, NOT id in the instance column
     * @param int $strictness
     * @return module|false context instance
     */
    public static function instance($cmid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(self::LEVEL, $cmid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel' => self::LEVEL, 'instanceid' => $cmid))) {
            if ($cm = $DB->get_record('course_modules', array('id' => $cmid), 'id,course', $strictness)) {
                $parentcontext = course::instance($cm->course);
                $record = context::insert_context_record(self::LEVEL, $cm->id, $parentcontext->path);
            }
        }

        if ($record) {
            $context = new module($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Create missing context instances at module context level
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "SELECT " . self::LEVEL . ", cm.id
                  FROM {course_modules} cm
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} cx
                                    WHERE cm.id = cx.instanceid AND cx.contextlevel=" . self::LEVEL . ")";
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
         LEFT OUTER JOIN {course_modules} cm ON c.instanceid = cm.id
                   WHERE cm.id IS NULL AND c.contextlevel = " . self::LEVEL . "
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at module context level.
     *
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        if ($force || $DB->record_exists_select('context', "contextlevel = " . self::LEVEL . " AND (depth = 0 OR path IS NULL)")) {
            if ($force) {
                $ctxemptyclause = '';
            } else {
                $ctxemptyclause = "AND (ctx.path IS NULL OR ctx.depth = 0)";
            }

            $sql = "INSERT INTO {context_temp} (id, path, depth, locked)
                    SELECT ctx.id, ".$DB->sql_concat('pctx.path', "'/'", 'ctx.id').", pctx.depth+1, ctx.locked
                      FROM {context} ctx
                      JOIN {course_modules} cm ON (cm.id = ctx.instanceid AND ctx.contextlevel = " . self::LEVEL . ")
                      JOIN {context} pctx ON (pctx.instanceid = cm.course AND pctx.contextlevel = " . course::LEVEL . ")
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

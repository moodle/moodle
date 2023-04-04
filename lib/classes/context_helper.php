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

namespace core;

use stdClass;
use coding_exception;

/**
 * Context maintenance and helper methods.
 *
 * This is "extends context" is a bloody hack that tires to work around the deficiencies
 * in the "protected" keyword in PHP, this helps us to hide all the internals of context
 * level implementation from the rest of code, the code completion returns what developers need.
 *
 * Thank you Tim Hunt for helping me with this nasty trick.
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 */
abstract class context_helper extends context {

    /**
     * @var array An array definitions of all context levels
     */
    private static $alllevels;

    /**
     * Reset internal context levels array.
     */
    public static function reset_levels() {
        self::$alllevels = null;
    }

    /**
     * Initialise context levels, call before using self::$alllevels.
     */
    private static function init_levels():void {
        global $CFG;

        if (isset(self::$alllevels)) {
            return;
        }
        self::$alllevels = array(
            CONTEXT_SYSTEM => \core\context\system::class,
            CONTEXT_USER => \core\context\user::class,
            CONTEXT_COURSECAT => \core\context\coursecat::class,
            CONTEXT_COURSE => \core\context\course::class,
            CONTEXT_MODULE => \core\context\module::class,
            CONTEXT_BLOCK => \core\context\block::class,
        );

        if (empty($CFG->custom_context_classes)) {
            return;
        }

        $levels = $CFG->custom_context_classes;
        if (!is_array($levels)) {
            $levels = @unserialize($levels);
        }
        if (!is_array($levels)) {
            debugging('Invalid $CFG->custom_context_classes detected, value ignored.', DEBUG_DEVELOPER);
            return;
        }

        // Unsupported custom levels, use with care!!!
        foreach ($levels as $level => $classname) {
            self::$alllevels[$level] = $classname;
        }
        ksort(self::$alllevels);
    }

    /**
     * Converts legacy context_* class name to new class name.
     *
     * NOTE: this is needed for external API which uses short context names.
     * @since Moodle 4.2
     *
     * @param int|string $extlevel
     * @return string|null context class name or null if not found
     */
    public static function parse_external_level($extlevel): ?string {
        self::init_levels();
        if (is_number($extlevel)) {
            if (isset(self::$alllevels[$extlevel])) {
                return self::$alllevels[$extlevel];
            } else {
                return null;
            }
        }
        if ($extlevel && is_string($extlevel)) {
            $found = null;
            foreach (self::$alllevels as $classname) {
                if ($classname::get_short_name() === $extlevel) {
                    if ($found) {
                        debugging("Duplicate short context level name found '$extlevel', use numeric value instead",
                            DEBUG_DEVELOPER);
                    } else {
                        $found = $classname;
                    }
                }
            }
            return $found;
        }
        return null;
    }

    /**
     * Resolve reference to context used in behat feature files.
     *
     * @param string $level
     * @param string $reference
     * @return context|null
     */
    public static function resolve_behat_reference(string $level, string $reference): ?context {
        global $DB;

        if (!PHPUNIT_TEST && !defined('BEHAT_SITE_RUNNING')) {
            throw new coding_exception('resolve_behat_reference() cannot be used outside of tests');
        }
        self::init_levels();

        $classname = null;
        if (is_number($level)) {
            if (isset(self::$alllevels[$level])) {
                $classname = self::$alllevels[$level];
            }
        } else {
            foreach (self::$alllevels as $levelclassname) {
                if ($level === $levelclassname::get_level_name()) {
                    $classname = $levelclassname;
                    break;
                }
                if ($level === $levelclassname::get_short_name()) {
                    $classname = $levelclassname;
                    break;
                }
            }
        }
        if (!$classname) {
            return null;
        }

        if ($classname::LEVEL === context\system::LEVEL) {
            return context\system::instance();
        }

        if (trim($reference) === '') {
            return null;
        }

        $table = $classname::get_instance_table();
        if (!$table) {
            return null;
        }

        $columns = $classname::get_behat_reference_columns();
        foreach ($columns as $column) {
            $instance = $DB->get_record($table, [$column => $reference]);
            if ($instance) {
                $context = $classname::instance($instance->id, IGNORE_MISSING);
                if ($context) {
                    return $context;
                }
                return null;
            }
        }

        return null;
    }

    /**
     * Returns a class name of the context level class
     *
     * @param int $contextlevel (CONTEXT_SYSTEM, etc.)
     * @return string class name of the context class
     * @throws coding_exception if level does not exist
     */
    public static function get_class_for_level(int $contextlevel): string {
        self::init_levels();
        if (isset(self::$alllevels[$contextlevel])) {
            return self::$alllevels[$contextlevel];
        } else {
            throw new coding_exception('Invalid context level specified');
        }
    }

    /**
     * Returns a list of all context levels
     *
     * @return array int=>string (level=>level class name)
     */
    public static function get_all_levels(): array {
        self::init_levels();
        return self::$alllevels;
    }

    /**
     * Get list of possible child levels for given level.
     * @since Moodle 4.2
     *
     * @param int $parentlevel
     * @return int[] list of context levels that my be children of given context level.
     */
    public static function get_child_levels(int $parentlevel): array {
        self::init_levels();
        $result = [];
        $definitions = self::$alllevels;

        $recursion = function(int $pl) use (&$result, $definitions, &$recursion): void {
            foreach ($definitions as $contextlevel => $classname) {
                $parentlevels = $classname::get_possible_parent_levels();
                if (in_array($pl, $parentlevels)) {
                    if (isset($result[$contextlevel])) {
                        continue;
                    }
                    $result[$contextlevel] = $contextlevel;
                    $recursion($contextlevel);
                }
            }
        };
        $recursion($parentlevel);

        $classname = self::get_class_for_level($parentlevel);
        $parentlevels = $classname::get_possible_parent_levels();
        if (!in_array($parentlevel, $parentlevels)) {
            unset($result[$parentlevel]);
        }

        return array_values($result);
    }

    /**
     * Returns context levels that compatible with role archetype assignments.
     * @since Moodle 4.2
     *
     * @param string $archetype
     * @return array
     */
    public static function get_compatible_levels(string $archetype): array {
        self::init_levels();
        $result = [];

        foreach (self::$alllevels as $contextlevel => $classname) {
            $compatiblearchetypes = $classname::get_compatible_role_archetypes();
            foreach ($compatiblearchetypes as $at) {
                if ($at === $archetype) {
                    $result[] = $contextlevel;
                }
            }
        }

        return $result;
    }

    /**
     * Remove stale contexts that belonged to deleted instances.
     * Ideally all code should cleanup contexts properly, unfortunately accidents happen...
     *
     * @return void
     */
    public static function cleanup_instances() {
        global $DB;
        self::init_levels();

        $sqls = array();
        foreach (self::$alllevels as $classname) {
            $sqls[] = $classname::get_cleanup_sql();
        }

        $sql = implode(" UNION ", $sqls);

        // It is probably better to use transactions, it might be faster too.
        $transaction = $DB->start_delegated_transaction();

        $rs = $DB->get_recordset_sql($sql);
        foreach ($rs as $record) {
            $context = context::create_instance_from_record($record);
            $context->delete();
        }
        $rs->close();

        $transaction->allow_commit();
    }

    /**
     * Create all context instances at the given level and above.
     *
     * @param int $contextlevel null means all levels
     * @param bool $buildpaths
     * @return void
     */
    public static function create_instances($contextlevel = null, $buildpaths = true) {
        self::init_levels();
        foreach (self::$alllevels as $level => $classname) {
            if ($contextlevel && $contextlevel != context\block::LEVEL && $level > $contextlevel) {
                // Skip potential sub-contexts,
                // in case of blocks build all contexts because plugin contexts may have higher levels.
                continue;
            }
            $classname::create_level_instances();
            if ($buildpaths) {
                $classname::build_paths(false);
            }
        }
    }

    /**
     * Rebuild paths and depths in all context levels.
     *
     * @param bool $force false means add missing only
     * @return void
     */
    public static function build_all_paths($force = false) {
        self::init_levels();
        foreach (self::$alllevels as $classname) {
            $classname::build_paths($force);
        }

        // Reset static course cache - it might have incorrect cached data.
        accesslib_clear_all_caches(true);
    }

    /**
     * Resets the cache to remove all data.
     */
    public static function reset_caches() {
        context::reset_caches();
    }

    /**
     * Returns all fields necessary for context preloading from user $rec.
     *
     * This helps with performance when dealing with hundreds of contexts.
     *
     * @param string $tablealias context table alias in the query
     * @return array (table.column=>alias, ...)
     */
    public static function get_preload_record_columns($tablealias) {
        return [
            "$tablealias.id" => "ctxid",
            "$tablealias.path" => "ctxpath",
            "$tablealias.depth" => "ctxdepth",
            "$tablealias.contextlevel" => "ctxlevel",
            "$tablealias.instanceid" => "ctxinstance",
            "$tablealias.locked" => "ctxlocked",
        ];
    }

    /**
     * Returns all fields necessary for context preloading from user $rec.
     *
     * This helps with performance when dealing with hundreds of contexts.
     *
     * @param string $tablealias context table alias in the query
     * @return string
     */
    public static function get_preload_record_columns_sql($tablealias) {
        return "$tablealias.id AS ctxid, " .
            "$tablealias.path AS ctxpath, " .
            "$tablealias.depth AS ctxdepth, " .
            "$tablealias.contextlevel AS ctxlevel, " .
            "$tablealias.instanceid AS ctxinstance, " .
            "$tablealias.locked AS ctxlocked";
    }

    /**
     * Preloads context cache with information from db record and strips the cached info.
     *
     * The db request has to contain all columns from context_helper::get_preload_record_columns().
     *
     * @param stdClass $rec
     * @return void This is intentional. See MDL-37115. You will need to get the context
     *      in the normal way, but it is now cached, so that will be fast.
     */
    public static function preload_from_record(stdClass $rec): void {
        context::preload_from_record($rec);
    }

    /**
     * Preload a set of contexts using their contextid.
     *
     * @param   array $contextids
     */
    public static function preload_contexts_by_id(array $contextids): void {
        global $DB;

        // Determine which contexts are not already cached.
        $tofetch = [];
        foreach ($contextids as $contextid) {
            if (!self::cache_get_by_id($contextid)) {
                $tofetch[] = $contextid;
            }
        }

        if (count($tofetch) > 1) {
            // There are at least two to fetch.
            // There is no point only fetching a single context as this would be no more efficient than calling the existing code.
            list($insql, $inparams) = $DB->get_in_or_equal($tofetch, SQL_PARAMS_NAMED);
            $ctxs = $DB->get_records_select('context', "id {$insql}", $inparams, '',
                self::get_preload_record_columns_sql('{context}'));
            foreach ($ctxs as $ctx) {
                self::preload_from_record($ctx);
            }
        }
    }

    /**
     * Preload all contexts instances from course.
     *
     * To be used if you expect multiple queries for course activities...
     *
     * @param int $courseid
     */
    public static function preload_course($courseid) {
        // Users can call this multiple times without doing any harm.
        if (isset(context::$cache_preloaded[$courseid])) {
            return;
        }
        $coursecontext = context\course::instance($courseid);
        $coursecontext->get_child_contexts();

        context::$cache_preloaded[$courseid] = true;
    }

    /**
     * Delete context instance
     *
     * @param int $contextlevel
     * @param int $instanceid
     * @return void
     */
    public static function delete_instance($contextlevel, $instanceid) {
        global $DB;

        // Double check the context still exists.
        if ($record = $DB->get_record('context', array('contextlevel' => $contextlevel, 'instanceid' => $instanceid))) {
            $context = context::create_instance_from_record($record);
            $context->delete();
        }
    }

    /**
     * Returns the name of specified context level
     *
     * @param int $contextlevel
     * @return string name of the context level
     */
    public static function get_level_name($contextlevel) {
        $classname = self::get_class_for_level($contextlevel);
        return $classname::get_level_name();
    }

    /**
     * Gets the current context to be used for navigation tree filtering.
     *
     * @param context|null $context The current context to be checked against.
     * @return context|null the context that navigation tree filtering should use.
     */
    public static function get_navigation_filter_context(?context $context): ?context {
        global $CFG;
        if (!empty($CFG->filternavigationwithsystemcontext)) {
            return context\system::instance();
        } else {
            return $context;
        }
    }
}

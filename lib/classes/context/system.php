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
 * System context class
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 */
class system extends context {
    /** @var int numeric context level value matching legacy CONTEXT_SYSTEM */
    public const LEVEL = 10;

    /**
     * Please use \core\context\system::instance() if you need the instance of context.
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != self::LEVEL) {
            throw new coding_exception('Invalid $record->contextlevel in core\context\system constructor.');
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
        return 'system';
    }

    /**
     * Returns human readable context level name.
     *
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('coresystem');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix does not apply to system context
     * @param boolean $short does not apply to system context
     * @param boolean $escape does not apply to system context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        return self::get_level_name();
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        return new moodle_url('/');
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
        return [];
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @param string $sort
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        global $DB;

        return $DB->get_records('capabilities', [], $sort);
    }

    /**
     * Create missing context instances at system context
     */
    protected static function create_level_instances() {
        // Nothing to do here, the system context is created automatically in installer.
        self::instance(0);
    }

    /**
     * Returns system context instance.
     *
     * @param int $instanceid should be 0
     * @param int $strictness
     * @param bool $cache
     * @return system context instance
     */
    public static function instance($instanceid = 0, $strictness = MUST_EXIST, $cache = true) {
        global $DB;

        if ($instanceid != 0) {
            debugging('context_system::instance(): invalid $id parameter detected, should be 0');
        }

        // SYSCONTEXTID is cached in local cache to eliminate 1 query per page.
        if (defined('SYSCONTEXTID') && $cache) {
            if (!isset(context::$systemcontext)) {
                $record = new stdClass();
                $record->id = SYSCONTEXTID;
                $record->contextlevel = self::LEVEL;
                $record->instanceid = 0;
                $record->path = '/'.SYSCONTEXTID;
                $record->depth = 1;
                $record->locked = 0;
                context::$systemcontext = new system($record);
            }
            return context::$systemcontext;
        }

        try {
            // We ignore the strictness completely because system context must exist except during install.
            $record = $DB->get_record('context', array('contextlevel' => self::LEVEL), '*', MUST_EXIST);
        } catch (\dml_exception $e) {
            // Table or record does not exist.
            if (!during_initial_install()) {
                // Do not mess with system context after install, it simply must exist.
                throw $e;
            }
            $record = null;
        }

        if (!$record) {
            $record = new stdClass();
            $record->contextlevel = self::LEVEL;
            $record->instanceid = 0;
            $record->depth = 1;
            $record->path = null; // Not known before insert.
            $record->locked = 0;

            try {
                if ($DB->count_records('context')) {
                    // Contexts already exist, this is very weird, system must be first!!!
                    return null;
                }
                if (defined('SYSCONTEXTID')) {
                    // This would happen only in unittest on sites that went through weird 1.7 upgrade.
                    $record->id = SYSCONTEXTID;
                    $DB->import_record('context', $record);
                    $DB->get_manager()->reset_sequence('context');
                } else {
                    $record->id = $DB->insert_record('context', $record);
                }
            } catch (\dml_exception $e) {
                // Can not create context - table does not exist yet, sorry.
                return null;
            }
        }

        if ($record->instanceid != 0) {
            // This is very weird, somebody must be messing with context table.
            debugging('Invalid system context detected');
        }

        if ($record->depth != 1 || $record->path != '/'.$record->id) {
            // Fix path if necessary, initial install or path reset.
            $record->depth = 1;
            $record->path = '/'.$record->id;
            $DB->update_record('context', $record);
        }

        if (empty($record->locked)) {
            $record->locked = 0;
        }

        if (!defined('SYSCONTEXTID')) {
            define('SYSCONTEXTID', $record->id);
        }

        context::$systemcontext = new system($record);
        return context::$systemcontext;
    }

    /**
     * Returns all site contexts except the system context, DO NOT call on production servers!!
     *
     * Contexts are not cached.
     *
     * @return array
     */
    public function get_child_contexts() {
        global $DB;

        debugging('Fetching of system context child courses is strongly discouraged'
            . ' on production servers (it may eat all available memory)!');

        // Just get all the contexts except for system level
        // and hope we don't OOM in the process - don't cache.
        $sql = "SELECT c.*
                  FROM {context} c
                 WHERE contextlevel > " . self::LEVEL;
        $records = $DB->get_records_sql($sql);

        $result = array();
        foreach ($records as $record) {
            $result[$record->id] = context::create_instance_from_record($record);
        }

        return $result;
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
                   WHERE 1=2
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at system context level.
     *
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        /* note: ignore $force here, we always do full test of system context */

        // Exactly one record must exist.
        $record = $DB->get_record('context', array('contextlevel' => self::LEVEL), '*', MUST_EXIST);

        if ($record->instanceid != 0) {
            debugging('Invalid system context detected');
        }

        if (defined('SYSCONTEXTID') && $record->id != SYSCONTEXTID) {
            debugging('Invalid SYSCONTEXTID detected');
        }

        if ($record->depth != 1 || $record->path != '/'.$record->id) {
            // Fix path if necessary, initial install or path reset.
            $record->depth = 1;
            $record->path = '/'.$record->id;
            $DB->update_record('context', $record);
        }
    }

    /**
     * Set whether this context has been locked or not.
     *
     * @param   bool    $locked
     * @return  $this
     */
    public function set_locked(bool $locked) {
        if ($locked) {
            throw new \coding_exception('It is not possible to lock the system context');
        }
        return parent::set_locked($locked);
    }
}

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
 * User context class
 *
 * @package   core_access
 * @category  access
 * @copyright Derick Turner
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.3
 */
class company extends context {
    /** @var int numeric context level value matching legacy CONTEXT_USER */
    public const LEVEL = 13;

    /**
     * Please use \core\context\company::instance($companyid) if you need the instance of context.
     * Alternatively if you know only the context id use \core\context::instance_by_id($contextid)
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        parent::__construct($record);
        if ($record->contextlevel != self::LEVEL) {
            throw new coding_exception('Invalid $record->contextlevel in core\context\company constructor.');
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
        return 'company';
    }

    /**
     * Returns human readable context level name.
     *
     * @return string the human readable context level name.
     */
    public static function get_level_name() {
        return get_string('company', 'block_iomad_company_admin');
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with User
     * @param boolean $short does not apply to company context
     * @param boolean $escape does not apply to company context
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        global $DB;

        $name = '';
        if ($company = $DB->get_record('company', array('id' => $this->_instanceid))) {
            if ($withprefix) {
                $name = get_string('company', 'block_iomad_company_admin').': ';
            }
            $name .= format_string($company->name);
        }
        return $name;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    public function get_url() {
        $url = new moodle_url('/');
        return $url;
    }

    /**
     * Returns list of all possible parent context levels.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    public static function get_possible_parent_levels(): array {
        return [system::LEVEL];
    }

    /**
     * Returns context instance database name.
     *
     * @return string|null table name for all levels except system.
     */
    protected static function get_instance_table(): ?string {
        return 'company';
    }

    /**
     * Returns list of columns that can be used from behat
     * to look up context by reference.
     *
     * @return array list of column names from instance table
     */
    protected static function get_behat_reference_columns(): array {
        return ['name', 'shortname'];
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @param string $sort
     * @return array
     */
    public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT) {
        global $DB;

        return $DB->get_records_select('capabilities', "contextlevel = :level", ['level' => self::LEVEL], $sort);
    }

    /**
     * Returns company context instance.
     *
     * @param int $companyid id from {company} table
     * @param int $strictness
     * @return company|false context instance
     */
    public static function instance($companyid, $strictness = MUST_EXIST) {
        global $DB;

        if ($context = context::cache_get(self::LEVEL, $companyid)) {
            return $context;
        }

        if (!$record = $DB->get_record('context', array('contextlevel' => self::LEVEL, 'instanceid' => $companyid))) {
            if ($company = $DB->get_record('company', array('id' => $companyid), 'id', $strictness)) {
                $record = context::insert_context_record(self::LEVEL, $company->id, '/'.SYSCONTEXTID, 0);
            }
        }

        if ($record) {
            $context = new company($record);
            context::cache_add($context);
            return $context;
        }

        return false;
    }

    /**
     * Create missing context instances at company context level
     */
    protected static function create_level_instances() {
        global $DB;

        $sql = "SELECT " . self::LEVEL . ", c.id
                  FROM {company} c
                 WHERE 1=1
                       AND NOT EXISTS (SELECT 'x'
                                         FROM {context} cx
                                        WHERE u.id = cx.instanceid AND cx.contextlevel=" . self::LEVEL . ")";
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
         LEFT OUTER JOIN {company} u ON (c.instanceid = u.id)
                   WHERE u.id IS NULL AND c.contextlevel = " . self::LEVEL . "
               ";

        return $sql;
    }

    /**
     * Rebuild context paths and depths at company context level.
     *
     * @param bool $force
     */
    protected static function build_paths($force) {
        global $DB;

        // First update normal companys.
        $path = $DB->sql_concat('?', 'id');
        $pathstart = '/' . SYSCONTEXTID . '/';
        $params = array($pathstart);

        if ($force) {
            $where = "depth <> 2 OR path IS NULL OR path <> ({$path})";
            $params[] = $pathstart;
        } else {
            $where = "depth = 0 OR path IS NULL";
        }

        $sql = "UPDATE {context}
                   SET depth = 2,
                       path = {$path}
                 WHERE contextlevel = " . self::LEVEL . "
                   AND ($where)";
        $DB->execute($sql, $params);
    }
}

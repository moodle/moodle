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
 * Backup implementation for the (tool_log) logstore_database nested element.
 *
 * @package    logstore_database
 * @category   backup
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Custom subclass of backup_nested_element that iterates over an external DB connection.
 *
 * @package    logstore_database
 * @category   backup
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_logstore_database_nested_element extends backup_nested_element {

    /**
     * @var \moodle_database $sourcedb
     */
    protected $sourcedb;

    /**
     * Constructor - instantiates one backup_nested_element, specifying its basic info.
     *
     * @param string $name name of the element
     * @param array  $attributes attributes this element will handle (optional, defaults to null)
     * @param array  $finalelements this element will handle (optional, defaults to null)
     */
    public function __construct($name, $attributes = null, $finalelements = null) {
        global $DB;

        parent::__construct($name, $attributes, $finalelements);
        $this->sourcedb = $DB;
    }

    /**
     * For sql or table datasources, this will iterate over the "external" DB connection
     * stored in this class instead of the default $DB. All other cases use the parent default.
     * @param object $processor the processor
     */
    protected function get_iterator($processor) {
        if ($this->get_source_table() !== null) { // It's one table, return recordset iterator.
            return $this->get_source_db()->get_recordset(
                $this->get_source_table(),
                backup_structure_dbops::convert_params_to_values($this->procparams, $processor),
                $this->get_source_table_sortby()
            );

        } else if ($this->get_source_sql() !== null) { // It's one sql, return recordset iterator.
            return $this->get_source_db()->get_recordset_sql(
                $this->get_source_sql(),
                backup_structure_dbops::convert_params_to_values($this->procparams, $processor)
            );
        }

        return parent::get_iterator($processor);
    }

    /**
     * Set the database we want to use.
     *
     * @param \moodle_database $db
     */
    public function set_source_db($db) {
        $this->sourcedb = $db;
    }

    /**
     * Get the database we want to use.
     *
     * @return \moodle_database $db
     */
    public function get_source_db() {
        return $this->sourcedb;
    }

}

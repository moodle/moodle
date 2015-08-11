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
 * Abstract class for tool_lp objects saved to the DB.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use external_function_parameters;
use external_value;

/**
 * Abstract class for tool_lp objects saved to the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class persistent {

    /** @var int|null $id Database id for this framework */
    private $id = null;

    /** @var int $timecreated Creation timestamp */
    private $timecreated = 0;

    /** @var int $timemodified Modification timestamp */
    private $timemodified = 0;

    /** @var int $usermodified The user who last modified this framework */
    private $usermodified = 0;

    /**
     * Abstract method that provides the table name matching this class.
     *
     * @return string
     */
    abstract public function get_table_name();

    /**
     * Create an instance of this class.
     * @param int $id If set, this is the id of an existing record, used to load the data.
     * @param \stdClass $record If set, the data for this class will be taken from the record.
     */
    public function __construct($id = 0, $record = null) {
        if ($id > 0) {
            $this->id = $id;
            $this->read();
        }
        if (!empty($record)) {
            $this->from_record($record);
        }
    }

    /**
     * Get the record id
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Set the record id
     *
     * @param int $id The record id
     */
    public function set_id($id) {
        $this->id = $id;
    }

    /**
     * Get the time the record was created.
     *
     * @return string The time the record was created.
     */
    public function get_timecreated() {
        return $this->timecreated;
    }

    /**
     * Set the time created timestamp
     *
     * @param int $timecreated The timestamp
     */
    public function set_timecreated($timecreated) {
        $this->timecreated = $timecreated;
    }

    /**
     * Get the last time the record was modified.
     *
     * @return string The last time the record was modified
     */
    public function get_timemodified() {
        return $this->timemodified;
    }

    /**
     * Set the last modified timestamp
     *
     * @param int $timemodified The timestamp
     */
    public function set_timemodified($timemodified) {
        $this->timemodified = $timemodified;
    }

    /**
     * Get the user who last modified the record.
     *
     * @return string The user who last modified the record
     */
    public function get_usermodified() {
        return $this->usermodified;
    }

    /**
     * Set the user id that last modified the record.
     *
     * @param int $usermodified The user id
     */
    public function set_usermodified($usermodified) {
        $this->usermodified = $usermodified;
    }

    /**
     * Populate this class with data from a DB record.
     *
     * @param \stdClass $record A DB record.
     * @return persistent
     */
    abstract public function from_record($record);

    /**
     * Create a DB record from this class.
     *
     * @return \stdClass
     */
    abstract public function to_record();

    /**
     * Reload the data for this class from the DB.
     *
     * @return persistent
     */
    public function read() {
        global $DB;

        if ($this->id <= 0) {
            throw new \coding_exception('id is required to load');
        }
        $record = $DB->get_record($this->get_table_name(), array('id' => $this->id), '*', MUST_EXIST);
        return $this->from_record($record);
    }

    /**
     * Insert a record in the DB
     *
     * @return persistent
     */
    public function create() {
        global $DB, $USER;

        $this->id = 0;
        $this->timecreated = $this->timemodified = time();
        $this->usermodified = $USER->id;
        $record = $this->to_record();

        $id = $DB->insert_record($this->get_table_name(), $record);
        $this->set_id($id);
        return $this;
    }

    /**
     * Update the existing record in the DB.
     *
     * @return bool Success
     */
    public function update() {
        global $DB, $USER;

        if ($this->id <= 0) {
            throw new \coding_exception('id is required to update');
        }
        $record = $this->to_record();
        unset($record->timecreated);
        $record->timemodified = time();
        $record->usermodified = $USER->id;
        $record = (array) $record;
        return $DB->update_record($this->get_table_name(), $record);
    }

    /**
     * Delete the existing record in the DB.
     *
     * @return bool Success
     */
    public function delete() {
        global $DB;

        if ($this->id <= 0) {
            throw new \coding_exception('id is required to delete');
        }
        return $DB->delete_records($this->get_table_name(), array('id' => $this->id));
    }

    /**
     * Load a list of records.
     *
     * @param array $filters Filters to apply.
     * @param string $sort Field to sort by.
     * @param string $order Sort order.
     * @param int $skip Limitstart.
     * @param int $limit Number of rows to return.
     *
     * @return persistent[]
     */
    public function get_records($filters = array(), $sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        global $DB;

        $orderby = '';
        if (!empty($sort)) {
            $orderby = $sort . ' ' . $order;
        }

        $records = $DB->get_records($this->get_table_name(), $filters, $orderby, '*', $skip, $limit);
        $instances = array();

        foreach ($records as $record) {
            $newrecord = new static(0, $record);
            array_push($instances, $newrecord);
        }
        return $instances;
    }

    /**
     * Load a list of records based on a select query.
     *
     * @param string $select
     * @param array $params
     * @param string $sort
     * @param string $fields
     * @param int $limitfrom
     * @param int $limitnum
     * @return \tool_lp\plan[]
     */
    public function get_records_select($select, $params = null, $sort = '', $fields = '*', $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $records = $DB->get_records_select($this->get_table_name(), $select, $params, $sort, $fields, $limitfrom, $limitnum);

        // We return class instances.
        $instances = array();
        foreach ($records as $record) {
            array_push($instances, new static(0, $record));
        }

        return $instances;

    }

    /**
     * Count a list of records.
     *
     * @param array $filters Filters to apply.
     * @return int
     */
    public function count_records($filters = array()) {
        global $DB;

        $count = $DB->count_records($this->get_table_name(), $filters);
        return $count;
    }
}

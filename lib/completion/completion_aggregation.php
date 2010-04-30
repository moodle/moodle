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
 * Course completion critieria aggregation
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir.'/completion/data_object.php');

/**
 * Course completion critieria aggregation
 */
class completion_aggregation extends data_object {

    /**
     * DB Table
     * @var string $table
     */
    public $table = 'course_completion_aggr_methd';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'course', 'criteriatype', 'method', 'value');

    /**
     * Course id
     * @access  public
     * @var     int
     */
    public $course;

    /**
     * Criteria type this aggregation method applies to, or NULL for overall course aggregation
     * @access  public
     * @var     int
     */
    public $criteriatype;

    /**
     * Aggregation method (COMPLETION_AGGREGATION_* constant)
     * @access  public
     * @var     int
     */
    public $method;

    /**
     * Method value
     * @access  public
     * @var     mixed
     */
    public $value;


    /**
     * Finds and returns a data_object instance based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        return self::fetch_helper('course_completion_aggr_methd', __CLASS__, $params);
    }


    /**
     * Finds and returns all data_object instances based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return array array of data_object insatnces or false if none found.
     */
    public static function fetch_all($params) {}

    /**
     * Set the aggregation method
     * @access  public
     * @param   $method     int
     * @return  void
     */
    public function setMethod($method) {
        $methods = array(
            COMPLETION_AGGREGATION_ALL,
            COMPLETION_AGGREGATION_ANY,
        );

        if (in_array($method, $methods)) {
            $this->method = $method;
        } else {
            $this->method = COMPLETION_AGGREGATION_ALL;
        }
    }
}

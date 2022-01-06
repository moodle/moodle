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
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/completion/data_object.php');

/**
 * Course completion critieria aggregation
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_aggregation extends data_object {

    /* @var string Database table name that stores completion aggregation information */
    public $table = 'course_completion_aggr_methd';

    /**
     * Array of required table fields, must start with 'id'.
     * Defaults to id, course, criteriatype, method, value
     * @var array
     */
    public $required_fields = array('id', 'course', 'criteriatype', 'method', 'value');

    /* @var array Array of unique fields, used in where clauses */
    public $unique_fields = array('course', 'criteriatype');

    /* @var int Course id */
    public $course;

    /* @var int Criteria type this aggregation method applies to, or NULL for overall course aggregation */
    public $criteriatype;

    /* @var int Aggregation method (COMPLETION_AGGREGATION_* constant) */
    public $method;

    /* @var mixed Method value */
    public $value;


    /**
     * Finds and returns a data_object instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return data_object instance of data_object or false if none found.
     */
    public static function fetch($params) {
        return self::fetch_helper('course_completion_aggr_methd', __CLASS__, $params);
    }


    /**
     * Finds and returns all data_object instances based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return array array of data_object insatnces or false if none found.
     */
    public static function fetch_all($params) {}

    /**
     * Set the aggregation method
     *
     * @param int $method One of COMPLETION_AGGREGATION_ALL or COMPLETION_AGGREGATION_ANY
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


    /**
     * Save aggregation method to database
     *
     * @access  public
     * @return  boolean
     */
    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
}

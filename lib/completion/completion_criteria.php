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
 * Course completion criteria
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir.'/completion/data_object.php');
require_once($CFG->libdir.'/completion/completion_criteria_completion.php');


/**
 * Criteria type constants
 * Primarily for storing criteria type in the database
 */
define('COMPLETION_CRITERIA_TYPE_SELF',         1);
define('COMPLETION_CRITERIA_TYPE_DATE',         2);
define('COMPLETION_CRITERIA_TYPE_UNENROL',      3);
define('COMPLETION_CRITERIA_TYPE_ACTIVITY',     4);
define('COMPLETION_CRITERIA_TYPE_DURATION',     5);
define('COMPLETION_CRITERIA_TYPE_GRADE',        6);
define('COMPLETION_CRITERIA_TYPE_ROLE',         7);
define('COMPLETION_CRITERIA_TYPE_COURSE',       8);

/**
 * Criteria type constant to class name mapping
 */
global $COMPLETION_CRITERIA_TYPES;
$COMPLETION_CRITERIA_TYPES = array(
    COMPLETION_CRITERIA_TYPE_SELF       => 'self',
    COMPLETION_CRITERIA_TYPE_DATE       => 'date',
    COMPLETION_CRITERIA_TYPE_UNENROL    => 'unenrol',
    COMPLETION_CRITERIA_TYPE_ACTIVITY   => 'activity',
    COMPLETION_CRITERIA_TYPE_DURATION   => 'duration',
    COMPLETION_CRITERIA_TYPE_GRADE      => 'grade',
    COMPLETION_CRITERIA_TYPE_ROLE       => 'role',
    COMPLETION_CRITERIA_TYPE_COURSE     => 'course',
);


/**
 * Completion criteria abstract definition
 */
abstract class completion_criteria extends data_object {
    /**
     * DB Table
     * @var string $table
     */
    public $table = 'course_completion_criteria';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'course', 'criteriatype', 'module', 'moduleinstance', 'courseinstance', 'enrolperiod', 'timeend', 'gradepass', 'role');

    /**
     * Course id
     * @var     int
     */
    public $course;

    /**
     * Criteria type
     * One of the COMPLETION_CRITERIA_TYPE_* constants
     * @var     int
     */
    public $criteriatype;

    /**
     * Module type this criteria relates to (for activity criteria)
     * @var     string
     */
    public $module;

    /**
     * Course module instance id this criteria relates to (for activity criteria)
     * @var     int
     */
    public $moduleinstance;

    /**
     * Period after enrolment completion will be triggered (for period criteria)
     * @var     int     (days)
     */
    public $enrolperiod;

    /**
     * Date of course completion (for date criteria)
     * @var     int     (timestamp)
     */
    public $date;

    /**
     * Passing grade required to complete course (for grade completion)
     * @var     float
     */
    public $gradepass;

    /**
     * Role ID that has the ability to mark a user as complete (for role completion)
     * @var     int
     */
    public $role;

    /**
     * Finds and returns all data_object instances based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return array array of data_object insatnces or false if none found.
     */
    public static function fetch_all($params) {}

    /**
     * Factory method for creating correct class object
     * @static
     * @param   array
     * @return  object
     */
    public static function factory($params) {
        global $CFG, $COMPLETION_CRITERIA_TYPES;

        if (!isset($params['criteriatype']) || !isset($COMPLETION_CRITERIA_TYPES[$params['criteriatype']])) {
            error('invalidcriteriatype', 'completion');
        }

        $class = 'completion_criteria_'.$COMPLETION_CRITERIA_TYPES[$params['criteriatype']];
        require_once($CFG->libdir.'/completion/'.$class.'.php');

        return new $class($params, false);
    }

    /**
     * Add appropriate form elements to the critieria form
     * @access  public
     * @param   object  $mform  Moodle forms object
     * @param   mixed   $data   optional
     * @return  void
     */
    abstract public function config_form_display(&$mform, $data = null);

    /**
     * Update the criteria information stored in the database
     * @access  public
     * @param   array   $data   Form data
     * @return  void
     */
    abstract public function update_config(&$data);

    /**
     * Review this criteria and decide if the user has completed
     * @access  public
     * @param   object  $completion     The user's completion record
     * @param   boolean $mark           Optionally set false to not save changes to database
     * @return  boolean
     */
    abstract public function review($completion, $mark = true);

    /**
     * Return criteria title for display in reports
     * @access  public
     * @return  string
     */
    abstract public function get_title();

    /**
     * Return a more detailed criteria title for display in reports
     * @access  public
     * @return  string
     */
    abstract public function get_title_detailed();

    /**
     * Return criteria type title for display in reports
     * @access  public
     * @return  string
     */
    abstract public function get_type_title();

    /**
     * Return criteria progress details for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  array
     */
    abstract public function get_details($completion);

    /**
     * Return criteria status text for display in reports
     * @access  public
     * @param   object  $completion     The user's completion record
     * @return  string
     */
    public function get_status($completion) {
        return $completion->is_complete() ? get_string('yes') : get_string('no');
    }

    /**
     * Return true if the criteria's current status is different to what is sorted
     * in the database, e.g. pending an update
     *
     * @param object $completion The user's criteria completion record
     * @return bool
     */
    public function is_pending($completion) {
        $review = $this->review($completion, false);

        return $review !== $completion->is_complete();
    }
}

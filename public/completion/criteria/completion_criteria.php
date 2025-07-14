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
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/completion/data_object.php');
require_once($CFG->dirroot.'/completion/completion_criteria_completion.php');

/**
 * Self completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_SELF',         1);

/**
 * Date completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_DATE',         2);

/**
 * Unenrol completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_UNENROL',      3);

/**
 * Activity completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_ACTIVITY',     4);

/**
 * Duration completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_DURATION',     5);

/**
 * Grade completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_GRADE',        6);

/**
 * Role completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_ROLE',         7);

/**
 * Course completion criteria type
 * Criteria type constant, primarily for storing criteria type in the database.
 */
define('COMPLETION_CRITERIA_TYPE_COURSE',       8);

/**
 * Criteria type constant to class name mapping.
 *
 * This global variable would be improved if it was implemented as a cache.
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
 *
 * @package core_completion
 * @category completion
 * @copyright 2009 Catalyst IT Ltd
 * @author Aaron Barnes <aaronb@catalyst.net.nz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class completion_criteria extends data_object {

    /* @var string Database table name that stores completion criteria information  */
    public $table = 'course_completion_criteria';

    /**
     * Array of required table fields, must start with 'id'.
     * Defaults to id, course, criteriatype, module, moduleinstane, courseinstance,
     * enrolperiod, timeend, gradepass, role
     * @var array
     */
    public $required_fields = array('id', 'course', 'criteriatype', 'module', 'moduleinstance', 'courseinstance', 'enrolperiod', 'timeend', 'gradepass', 'role');

    /* @var int Course id  */
    public $course;

    /**
     * Criteria type
     * One of the COMPLETION_CRITERIA_TYPE_* constants
     * @var int
     */
    public $criteriatype;

    /* @var string Module type this criteria relates to (for activity criteria)  */
    public $module;

    /* @var int Course module instance id this criteria relates to (for activity criteria) */
    public $moduleinstance;

    /**
     * Period after enrolment completion will be triggered (for period criteria)
     * The value here is the number of days as an int.
     * @var int
     */
    public $enrolperiod;

    /**
     * Date of course completion (for date criteria)
     * This is a timestamp value
     * @var int
     */
    public $date;

    /* @var float Passing grade required to complete course (for grade completion) */
    public $gradepass;

    /* @var int Role ID that has the ability to mark a user as complete (for role completion) */
    public $role;

    /** @var string course instance. */
    public $courseinstance;

    /** @var mixed time end. */
    public $timeend;

    /**
     * Finds and returns all data_object instances based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return array array of data_object insatnces or false if none found.
     */
    public static function fetch_all($params) {}

    /**
     * Factory method for creating correct class object
     *
     * @param array $params associative arrays varname=>value
     * @return completion_criteria
     */
    public static function factory($params) {
        global $CFG, $COMPLETION_CRITERIA_TYPES;

        if (!isset($params['criteriatype']) || !isset($COMPLETION_CRITERIA_TYPES[$params['criteriatype']])) {
            throw new \moodle_exception('invalidcriteriatype', 'completion');
        }

        $class = 'completion_criteria_'.$COMPLETION_CRITERIA_TYPES[$params['criteriatype']];
        require_once($CFG->dirroot.'/completion/criteria/'.$class.'.php');

        return new $class($params, false);
    }

    /**
     * Add appropriate form elements to the critieria form
     *
     * @param moodleform $mform Moodle forms object
     * @param mixed $data optional Any additional data that can be used to set default values in the form
     * @return void
     */
    abstract public function config_form_display(&$mform, $data = null);

    /**
     * Update the criteria information stored in the database
     *
     * @param array $data Form data
     * @return void
     */
    abstract public function update_config(&$data);

    /**
     * Review this criteria and decide if the user has completed
     *
     * @param object $completion The user's completion record
     * @param boolean $mark Optionally set false to not save changes to database
     * @return boolean
     */
    abstract public function review($completion, $mark = true);

    /**
     * Return criteria title for display in reports
     *
     * @return string
     */
    abstract public function get_title();

    /**
     * Return a more detailed criteria title for display in reports
     *
     * @return string
     */
    abstract public function get_title_detailed();

    /**
     * Return criteria type title for display in reports
     *
     * @return string
     */
    abstract public function get_type_title();

    /**
     * Return criteria progress details for display in reports
     *
     * @param completion_completion $completion The user's completion record
     * @return array
     */
    abstract public function get_details($completion);

    /**
     * Return pix_icon for display in reports.
     *
     * @param string $alt The alt text to use for the icon
     * @param array $attributes html attributes
     * @return pix_icon
     */
    public function get_icon($alt, ?array $attributes = null) {
        global $COMPLETION_CRITERIA_TYPES;

        $criteriatype = $COMPLETION_CRITERIA_TYPES[$this->criteriatype];
        return new pix_icon('i/'.$criteriatype, $alt, 'moodle', $attributes);
    }

    /**
     * Return criteria status text for display in reports
     *
     * @param completion_completion $completion The user's completion record
     * @return string
     */
    public function get_status($completion) {
        return $completion->is_complete() ? get_string('yes') : get_string('no');
    }

    /**
     * Return true if the criteria's current status is different to what is sorted
     * in the database, e.g. pending an update
     *
     * @param completion_completion $completion The user's criteria completion record
     * @return bool
     */
    public function is_pending($completion) {
        $review = $this->review($completion, false);

        return $review !== $completion->is_complete();
    }
}

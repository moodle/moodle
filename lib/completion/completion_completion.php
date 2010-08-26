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
 * Course completion status for a particular user/course
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir.'/completion/data_object.php');


/**
 * Course completion status for a particular user/course
 */
class completion_completion extends data_object {

    /**
     * DB Table
     * @var string $table
     */
    public $table = 'course_completions';

    /**
     * Array of required table fields, must start with 'id'.
     * @var array $required_fields
     */
    public $required_fields = array('id', 'userid', 'course', 'deleted', 'timenotified',
        'timeenrolled', 'timestarted', 'timecompleted', 'reaggregate');

    /**
     * User ID
     * @access  public
     * @var     int
     */
    public $userid;

    /**
     * Course ID
     * @access  public
     * @var     int
     */
    public $course;

    /**
     * Set to 1 if this record has been deleted
     * @access  public
     * @var     int
     */
    public $deleted;

    /**
     * Timestamp the interested parties were notified
     * of this user's completion
     * @access  public
     * @var     int
     */
    public $timenotified;

    /**
     * Time of course enrolment
     * @see     completion_completion::mark_enrolled()
     * @access  public
     * @var     int
     */
    public $timeenrolled;

    /**
     * Time the user started their course completion
     * @see     completion_completion::mark_inprogress()
     * @access  public
     * @var     int
     */
    public $timestarted;

    /**
     * Timestamp of course completion
     * @see     completion_completion::mark_complete()
     * @access  public
     * @var     int
     */
    public $timecompleted;

    /**
     * Flag to trigger cron aggregation (timestamp)
     * @access  public
     * @var     int
     */
    public $reaggregate;


    /**
     * Finds and returns a data_object instance based on params.
     * @static abstract
     *
     * @param array $params associative arrays varname=>value
     * @return object data_object instance or false if none found.
     */
    public static function fetch($params) {
        $params['deleted'] = null;
        return self::fetch_helper('course_completions', __CLASS__, $params);
    }

    /**
     * Return status of this completion
     * @access  public
     * @return  boolean
     */
    public function is_complete() {
        return (bool) $this->timecompleted;
    }

    /**
     * Mark this user as started (or enrolled) in this course
     *
     * If the user is already marked as started, no change will occur
     *
     * @access  public
     * @param   integer $timeenrolled Time enrolled (optional)
     * @return  void
     */
    public function mark_enrolled($timeenrolled = null) {

        if ($this->timeenrolled === null) {

            if ($timeenrolled === null) {
                $timeenrolled = time();
            }

            $this->timeenrolled = $timeenrolled;
        }

        $this->_save();
    }

    /**
     * Mark this user as inprogress in this course
     *
     * If the user is already marked as inprogress,
     * the time will not be changed
     *
     * @access  public
     * @param   integer $timestarted Time started (optional)
     * @return  void
     */
    public function mark_inprogress($timestarted = null) {

        $timenow = time();

        // Set reaggregate flag
        $this->reaggregate = $timenow;

        if (!$this->timestarted) {

            if (!$timestarted) {
                $timestarted = $timenow;
            }

            $this->timestarted = $timestarted;
        }

        $this->_save();
    }

    /**
     * Mark this user complete in this course
     *
     * This generally happens when the required completion criteria
     * in the course are complete.
     *
     * @access  public
     * @param   integer $timecomplete Time completed (optional)
     * @return  void
     */
    public function mark_complete($timecomplete = null) {

        // Never change a completion time
        if ($this->timecompleted) {
            return;
        }

        // Use current time if nothing supplied
        if (!$timecomplete) {
            $timecomplete = time();
        }

        // Set time complete
        $this->timecompleted = $timecomplete;

        // Save record
        $this->_save();
    }

    /**
     * Save course completion status
     *
     * This method creates a course_completions record if none exists
     * @access  public
     * @return  void
     */
    private function _save() {

        global $DB;

        if ($this->timeenrolled === null) {
            $this->timeenrolled = 0;
        }

        // Save record
        if ($this->id) {
            $this->update();
        } else {
            // Make sure reaggregate field is not null
            if (!$this->reaggregate) {
                $this->reaggregate = 0;
            }

			// Make sure timestarted is not null
			if (!$this->timestarted) {
				$this->timestarted = 0;
			}
			
            $this->insert();
        }
    }
}

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
 * File containing the bulk enrollment class.
 *
 * @package    tool_bulkenrol
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Course class.
 *
 * @package    tool_bulkenrol
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_bulkenrol_enrollment {
    

    /** @var array final import data. */
    protected $data = array();

    /** @var array errors. */
    protected $errors = array();

    /** @var array enrollment import options. */
    protected $options = array();

    /** @var bool set to true once we have prepared the enrollment */
    protected $prepared = false;

    /** @var bool set to true once we have started the process of the enrollment */
    protected $processstarted = false;

    /** @var array enrollment import data. */
    protected $rawdata = array();

    /** @var array errors. */
    protected $statuses = array();

    /** @var array fields allowed as enrollment data. */
    static protected $validfields = array('user', 'course', 'role');

    /** @var array fields required on enrollment creation. */
    static protected $mandatoryfields = array('user', 'course', 'role');

    /**
     * Constructor
     *
     * @param int $mode import mode, constant matching tool_bulkenrol_processor::MODE_*
     * @param int $updatemode update mode, constant matching tool_bulkenrol_processor::UPDATE_*
     * @param array $rawdata raw enrollment data.
     * @param array $defaults default enrollment data.
     * @param array $importoptions import options.
     */
    public function __construct($rawdata, $options = array()) {

        $this->rawdata = $rawdata;
        $this->options = $options;
    }




    /**
     * Log an error
     *
     * @param string $code error code.
     * @param lang_string $message error message.
     * @return void
     */
    protected function error($code, lang_string $message): void {
        if (array_key_exists($code, $this->errors)) {
            throw new coding_exception('Error code already defined');
        }
        $this->errors[$code] = $message;
    }



    /**
     * Return the data that will be used upon saving.
     *
     * @return null|array
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Return the errors found during preparation.
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Return array of valid fields for default values
     *
     * @return array
     */
    protected function get_valid_fields() {
        return  self::$validfields;
    }



    /**
     * Return the errors found during preparation.
     *
     * @return array
     */
    public function get_statuses() {
        return $this->statuses;
    }

    /**
     * Return whether there were errors with this enrollment.
     *
     * @return boolean
     */
    public function has_errors() {
        return !empty($this->errors);
    }


    /**
     * Validates and prepares the data.
     *
     * @return bool false is any error occured.
     */
    public function prepare() {
        global $DB, $SITE, $CFG;

        $this->prepared = true;


        // Basic data.
        $enrollmentdata = array();
        foreach ($this->rawdata as $field => $value) {
            if (!in_array($field, self::$validfields)) {
                continue;
            }
            $enrollmentdata[$field] = $value;
        }


        // Resolve the category, and fail if not found.
        $errors = array();
        $user = tool_bulkenrol_helper::resolve_user($this->rawdata['user'], $this->options['resolveuser'], $errors);
        $course = tool_bulkenrol_helper::resolve_course($this->rawdata['course'], $this->options['resolvecourse'], $errors);
        $role = tool_bulkenrol_helper::resolve_role($this->rawdata['role'], $this->options['resolverole'], $errors);
        if (empty($errors)) {
            $enrollmentdata['userid'] = $user->id;
            $enrollmentdata['courseid'] = $course->id;
            $enrollmentdata['roleid'] = $role->id;

            $enrollmentdata['user_more'] = ' - '. $user->firstname;
            $enrollmentdata['course_more'] = ' - '. $course->fullname;
            $enrollmentdata['role_more'] = ' - '. $role->shortname;

            $this->data = $enrollmentdata;
        } else {

            $enrollmentdata['user_more'] = $user ? ' - '. $user->firstname: '';
            $enrollmentdata['course_more'] = $course? ' - '. $course->fullname: '';
            $enrollmentdata['role_more'] = $role? ' - '. $role->shortname:'';

            $this->data = $enrollmentdata;
            foreach ($errors as $key => $message) {
                $this->error($key, $message);
            }
            return false;
        }

        // check if enrollment exists
        $enrol = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        // check if enrollment exists
        $user_enrollment = $DB->get_record('user_enrolments', array(
            'enrolid' => $enrol->id,
            'userid' => $user->id,
        ));

        if($user_enrollment) {
            $this->error('enrollmentexists', new lang_string('enrollmentexists', 'tool_bulkenrol'));
            return false;
        }

        return true;
    }

    /**
     * Proceed with the import of the enrollment.
     *
     * @return void
     */
    public function proceed()
    {
        global $DB, $CFG, $USER;

        if (!$this->prepared) {
            throw new coding_exception('The enrollment has not been prepared.');
        } else if ($this->has_errors()) {
            throw new moodle_exception('Cannot proceed, errors were detected.');
        } else if ($this->processstarted) {
            throw new coding_exception('The process has already been started.');
        }
        $this->processstarted = true;

        $enrol = $DB->get_record('enrol', array(
            'courseid' => $this->data['courseid'],
            'enrol' => 'manual',
        ));

        $plugin = enrol_get_plugin('manual');
        $plugin->enrol_user($enrol, $this->data['userid'], $this->data['roleid']);

        $this->status('enrollmentcreated', new lang_string('enrollmentcreated', 'tool_bulkenrol'));

    }


   /**
    * Log a status
    *
    * @param string $code status code.
    * @param lang_string $message status message.
    * @return void
    */
   protected function status($code, lang_string $message) {
       if (array_key_exists($code, $this->statuses)) {
           throw new coding_exception('Status code already defined');
       }
       $this->statuses[$code] = $message;
   }

}

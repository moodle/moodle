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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\validators;

defined('MOODLE_INTERNAL') || die();

use block_quickmail_config;

abstract class validator {

    public $form_data;
    public $extra_params;
    public $errors;
    public $course;

    /**
     * Constructs a validator
     *
     * @param object $formdata  post data submission object
     * @param array  $extraparams     an array of extra params that may be necessary for validation
     */
    public function __construct($formdata, $extraparams = []) {
        $this->form_data = $formdata;
        $this->extra_params = $extraparams;
        $this->errors = [];
        $this->course = null;
    }

    /**
     * Sets the given course on the validator object
     *
     * @param  object  $course  moodle course
     * @return void
     */
    public function for_course($course) {
        $this->course = $course;
    }

    /**
     * Performs validation against the validator's set form data
     *
     * @return void
     */
    public function validate() {
        $this->validator_rules();
    }

    /**
     * Includes the given error message in the stack of errors
     *
     * @param string  $message
     * @return void
     */
    public function add_error($message) {
        $this->errors[] = $message;
    }

    /**
     * Reports whether or not this validator has any errors
     *
     * @return bool
     */
    public function has_errors() {
        return (bool) count($this->errors);
    }

    /**
     * Reports whether or not the given form data key has any value or not
     *
     * @param  string  $key  a key on the form_data object
     * @return bool
     */
    public function is_missing($key) {
        return empty($this->form_data->$key);
    }

    /**
     * Returns the configuration array or value with respect to the set course (if any)
     *
     * @param  string  $key
     * @return mixed
     */
    public function get_config($key = '') {
        $courseid = empty($this->course) ? 0 : $this->course->id;

        return block_quickmail_config::get($key, $courseid);
    }

    /**
     * Reports whether or not this validator contains any extra params with the given key/value
     *
     * If no value is passed in the check, this will return true if the param was set
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return bool
     */
    public function check_extra_params_value($key, $value = null) {
        // If the key doesn't exists in extra_params, return false.
        if (!array_key_exists($key, $this->extra_params)) {
            return false;
        }

        // If the key does exist, but no value was specified, then any value will do.
        if (is_null($value)) {
            return true;
        }

        return $this->get_extra_param_value($key) == $value;
    }

    /**
     * Returns the value of the given extra param key
     *
     * @param  string  $key
     * @param  mixed  $default    value to return as default
     * @return mixed
     */
    public function get_extra_param_value($key, $default = null) {
        return array_key_exists($key, $this->extra_params)
            ? $this->extra_params[$key]
            : $default;
    }

}

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
 * The mod_scorm status submitted event.
 *
 * @package    mod_scorm
 * @copyright  2016 onwards Matteo Scaramuccia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_scorm\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_scorm status submitted event class.
 *
 * @property-read array $other {
 *      Extra information about event properties.
 *
 *      - int attemptid: Attempt id.
 *      - string cmielement: CMI element representing a status.
 *      - string cmivalue: CMI value.
 * }
 *
 * @package    mod_scorm
 * @since      Moodle 3.1
 * @copyright  2016 onwards Matteo Scaramuccia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class status_submitted extends cmielement_submitted {

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventstatussubmitted', 'mod_scorm');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!in_array($this->other['cmielement'],
                array('cmi.completion_status', 'cmi.core.lesson_status', 'cmi.success_status'))) {
            throw new \coding_exception(
                "The 'cmielement' must represents a valid CMI status element ({$this->other['cmielement']}).");
        }

        if (!in_array($this->other['cmivalue'],
                array('passed', 'completed', 'failed', 'incomplete', 'browsed', 'not attempted', 'unknown'))) {
            throw new \coding_exception(
                "The 'cmivalue' must represents a valid CMI status value ({$this->other['cmivalue']}).");
        }
    }
}

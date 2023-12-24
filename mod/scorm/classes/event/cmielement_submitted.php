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
 * The mod_scorm generic CMI element submitted event.
 *
 * @package    mod_scorm
 * @copyright  2016 onwards Matteo Scaramuccia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_scorm\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_scorm generic CMI element submitted event class.
 *
 * @property-read array $other {
 *      Extra information about event properties.
 *
 *      - int attemptid: Attempt id.
 *      - string cmielement: CMI element.
 *      - string cmivalue: CMI value.
 * }
 *
 * @package    mod_scorm
 * @since      Moodle 3.1
 * @copyright  2016 onwards Matteo Scaramuccia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class cmielement_submitted extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'scorm_scoes_value';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with the id '$this->userid' submitted the element '{$this->other['cmielement']}' " .
                "with the value of '{$this->other['cmivalue']}' " .
                "for the attempt with the id '{$this->other['attemptid']}' " .
                "for a scorm activity with the course module id '$this->contextinstanceid'.";
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/scorm/report/userreport.php',
                array('id' => $this->contextinstanceid, 'user' => $this->userid, 'attempt' => $this->other['attemptid']));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (empty($this->other['attemptid'])) {
            throw new \coding_exception("The 'attemptid' must be set in other.");
        }

        if (empty($this->other['cmielement'])) {
            throw new \coding_exception("The 'cmielement' must be set in other.");
        }
        // Trust that 'cmielement' represents a valid CMI datamodel element:
        // just check that the given value starts with 'cmi.'.
        if (strpos($this->other['cmielement'], 'cmi.', 0) !== 0) {
            throw new \coding_exception(
                "A valid 'cmielement' must start with 'cmi.' ({$this->other['cmielement']}).");
        }

        // Warning: 'cmivalue' could be also "0" e.g. when 'cmielement' represents a score.
        if (!isset($this->other['cmivalue'])) {
            throw new \coding_exception("The 'cmivalue' must be set in other.");
        }
    }
}

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
 * The assignment_submitted event
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_panoptosubmission\event;

/**
 * The assignment_submitted event class.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignment_submitted extends \core\event\base {

    /**
     * Initializes eventdata
     *
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'panoptosubmission_submission';
    }

    /**
     * This function returns the name of the event
     * @return string the name of the event
     */
    public static function get_name() {
        return get_string('eventassignment_submitted', 'panoptosubmission');
    }

    /**
     * Returns a descriptions of what triggered the event
     * @return string a description of what triggered the event
     */
    public function get_description() {
        return "The user with id '{$this->userid}' made a submission to the"
        . " Panopto Student Submission activity with the course module id of '{$this->contextinstanceid}'.";
    }

    /**
     * Returns a url to the page to grade the submission
     * @return string a url to the grade submission page
     */
    public function get_url() {
        return new \moodle_url('/mod/panoptosubmission/view.php', ['id' => $this->contextinstanceid]);
    }
}

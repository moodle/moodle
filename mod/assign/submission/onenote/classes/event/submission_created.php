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
 * The assignsubmission_onenote submission_created event.
 *
 * @package    assignsubmission_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc. (based on files by NetSpot {@link http://www.netspot.com.au})
 */

namespace assignsubmission_onenote\event;

use moodle_exception;

/**
 * The assignsubmission_onenote submission_created event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *      - int filesubmissioncount: The number of files uploaded.
 * }
 * @package    assignsubmission_onenote
 * @since      Moodle 2.7
 */
class submission_created extends \mod_assign\event\submission_created {

    /**
     * Init method.
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'assignsubmission_onenote';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $descriptionstring = "The user with id '$this->userid' created a file submission and uploaded " .
            "'{$this->other['filesubmissioncount']}' file/s in the assignment with the course module id " .
            "'$this->contextinstanceid'";
        if (!empty($this->other['groupid'])) {
            $descriptionstring .= " for the group with id '{$this->other['groupid']}'.";
        } else {
            $descriptionstring .= ".";
        }

        return $descriptionstring;
    }

    /**
     * Custom validation.
     *
     * @return void
     * @throws moodle_exception
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['filesubmissioncount'])) {
            throw new moodle_exception('erroremptyothervalue', 'assignsubmission_onenote');
        }
    }
}

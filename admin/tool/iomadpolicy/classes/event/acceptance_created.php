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
 * Provides {@link tool_iomadpolicy\event\acceptance_created} class.
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * Event acceptance_created
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class acceptance_created extends acceptance_base {

    /**
     * Initialise the event.
     */
    protected function init() {
        parent::init();
        $this->data['crud'] = 'c';
    }

    /**
     * Returns event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_acceptance_created', 'tool_iomadpolicy');
    }

    /**
     * Get the event description.
     *
     * @return string
     */
    public function get_description() {
        if ($this->other['status'] == 1) {
            $action = 'added consent to';
        } else if ($this->other['status'] == -1) {
            $action = 'revoked consent to';
        } else {
            $action = 'created an empty consent record for';
        }
        return "The user with id '{$this->userid}' $action the iomadpolicy with revision {$this->other['iomadpolicyversionid']} ".
            "for the user with id '{$this->relateduserid}'";
    }
}
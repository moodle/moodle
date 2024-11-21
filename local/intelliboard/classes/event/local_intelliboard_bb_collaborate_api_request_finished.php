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
 * @package    local_intelliboard
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class local_intelliboard_bb_collaborate_api_request_finished extends base {
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = null;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string(
            'local_intelliboard_bb_collaborate_api_request_finished',
            'local_intelliboard'
        );
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $otherdata = (is_array($this->other) || is_object($this->other)) ? json_encode($this->other) : $this->other;
        return "The system finished API request to Blackboard Collaborate. Data: {$otherdata}";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/');
    }

    public function get_id() {
        return $this->objectid;
    }

    public function get_userid() {
        return $this->userid;
    }
}

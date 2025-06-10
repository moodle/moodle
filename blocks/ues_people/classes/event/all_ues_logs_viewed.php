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
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @copyright  2014 Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ues_people\event;

defined('MOODLE_INTERNAL') || die();

class all_ues_logs_viewed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('eventalllogsviewed', 'block_ues_people');
    }

    public function get_description() {
        return "User {$this->userid} has viewed all UES Logs for course {$this->courseid}.";
    }

    public function get_url() {
        return new \moodle_url('/block/ues_people/index.php', array('id' => $this->courseid));
    }

    public function get_legacy_logdata() {
        // Override if you are migrating an add_to_log() call.
        return array($this->courseid, 'ues_people', 'view all', 'index.php?id='.$this->courseid, '');
    }
}

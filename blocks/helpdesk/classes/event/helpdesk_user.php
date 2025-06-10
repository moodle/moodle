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
 * The helpdesk_user event.
 *
 * @package    blocks_helpdesk
 * @copyright  2019, Louisiana State University
 * @author     Troy Kammerdiener
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace blocks_helpdesk\event;

defined('MOODLE_INTERNAL') || die();
/**
 * The helpdesk_user event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - PUT INFO HERE
 * }
 *
 * @since     Moodle 3.7
 * @copyright 2019, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

class helpdesk_user extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = NULL;
    }
 
    public static function get_name() {
        return get_string('eventhelpdeskuser', 'blocks_helpdesk');
    }
 
    public function get_description() {
        return "The user with id {$this->userid} accessed help info with id {$this->objectid}.";
    }
 
    public function get_url() {
        return new \moodle_url('/blocks/helpdesk/index.php', array('mode' => 'user'));
    }
 
    public static function get_legacy_eventname() {
        // Override ONLY if you are migrating events_trigger() call.
        return 'helpdesk_user';
    }
 
    protected function get_legacy_eventdata() {
        // Override if you migrating events_trigger() call.
        $cloneddata = new \stdClass();
        $cloneddata = unserialize(serialize($this->data));
        foreach($this->data->other as $key => $value) {
            $cloneddata[$key] -> $value;
        }
        unset($cloneddata['other']);
        return $cloneddata;
    }
}

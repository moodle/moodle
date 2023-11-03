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
 * The create_report event.
 *
 * @package    block_learnerscript
 * @copyright  2014 YOUR NAME
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_learnerscript\event;
defined('MOODLE_INTERNAL') || die();
/**
 * The create_report event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - PUT INFO HERE
 * }
 *
 * @since     Moodle MOODLEVERSION
 * @copyright 2014 YOUR NAME
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class update_report extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'block_learnerscript';
    }

    public static function get_name() {
        return get_string('eventupdate_report', 'block_learnerscript');
    }

    public function get_description() {
        return "The user with id {$this->userid} Updated report with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/learnerscript/editreport.php', array('id' => $this->objectid));
    }

}
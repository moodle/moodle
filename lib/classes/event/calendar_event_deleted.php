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
 * Calendar event deleted event.
 *
 * @package    core
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Calendar event deleted event.
 *
 * @property-read array $other Extra information about the event.
 *     -int timestart: timestamp for event time start.
 *     -string name: Name of the event.
 *     -int repeatid: Id of the parent event if present, else 0.
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_event_deleted extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'event';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcalendareventdeleted', 'core_calendar');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "An event '" . s($this->other['name']) . "' was deleted by user with id {$this->userid}";
    }

    /**
     * custom validations
     *
     * Throw \coding_exception notice in case of any problems.
     */
    protected function validate_data() {
        if (!isset($this->other['repeatid'])) {
            throw new \coding_exception("Field other['repeatid'] must be set");
        }
        if (empty($this->other['name'])) {
            throw new \coding_exception("Field other['name'] cannot be empty");
        }
        if (empty($this->other['timestart'])) {
            throw new \coding_exception("Field other['timestart'] cannot be empty");
        }

        parent::validate_data();
    }
}

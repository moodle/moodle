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
 * The mod_dataform field base event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      string fieldname the name of the field.
 *      int dataid the id of the dataform activity.
 * }
 *
 * @package    mod_dataform
 * @copyright  2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\event;

defined('MOODLE_INTERNAL') || die();

abstract class field_base extends \core\event\base {

    /**
     * Return raw event name.
     *
     * @return string
     */
    public static function get_event_name() {
        list(, , $eventname) = explode('\\', get_called_class());
        return $eventname;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        $eventname = self::get_event_name();
        return get_string("event_$eventname" , 'mod_dataform');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        list(, $action) = explode('_', self::get_event_name());
        $description = 'The field '. $this->objectid.
            ' belonging to the dataform activity '. $this->other['dataid'].
            ' has been '. $action. '.';
        return $description;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['fieldname'])) {
            throw new \coding_exception('The fieldname must be set in $other.');
        }

        if (!isset($this->other['dataid'])) {
            throw new \coding_exception('The dataid must be set in $other.');
        }
    }
}

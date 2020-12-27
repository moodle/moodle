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
 * The database text field content replaced event.
 *
 * @package   core
 * @copyright 2020 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The database text field content replaced event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string search: The value being searched for.
 *      - string replace: The replacement value that replaces found search value.
 * }
 *
 * @package   core
 * @copyright 2020 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_text_field_content_replaced extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventdatabasetextfieldcontentreplaced');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' replaced the string '" . $this->other['search'] . "' " .
            "with the string '" . $this->other['replace'] . "' in the database.";
    }

    /**
     * Custom validation.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['search'])) {
            throw new \coding_exception('The \'search\' value must be set in other.');
        }
        if (!isset($this->other['replace'])) {
            throw new \coding_exception('The \'replace\' value must be set in other.');
        }
    }
}

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
 * User profile field created event.
 *
 * @package    core
 * @copyright  2017 Web Courseworks, Ltd. {@link http://www.webcourseworks.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * User profile info field created event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string shortname: the shortname of the field.
 *      - string name: the name of the field.
 *      - string datatype: the data type of the field.
 * }
 *
 * @package    core
 * @copyright  2017 Web Courseworks, Ltd. {@link http://www.webcourseworks.com}
 * @since      Moodle 3.4
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_info_field_created extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'user_info_field';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an event from a profile field.
     *
     * @since Moodle 3.4
     * @param \stdClass $field A snapshot of the created field.
     * @return \core\event\base
     */
    public static function create_from_field($field) {
        $event = self::create(array(
            'objectid' => $field->id,
            'context' => \context_system::instance(),
            'other' => array(
                'shortname' => $field->shortname,
                'name'      => $field->name,
                'datatype'  => $field->datatype,
            )
        ));

        $event->add_record_snapshot('user_info_field', $field);

        return $event;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserinfofieldcreated');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        $name = s($this->other['name']);
        return "The user with id '$this->userid' created the user profile field '$name' with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/profile/index.php', array(
            'action' => 'editfield',
            'id' => $this->objectid,
            'datatype' => $this->other['datatype']
        ));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['shortname'])) {
            throw new \coding_exception('The \'shortname\' value must be set in other.');
        }

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }

        if (!isset($this->other['datatype'])) {
            throw new \coding_exception('The \'datatype\' value must be set in other.');
        }
    }

    /**
     * Get the backup/restore table mapping for this event.
     *
     * @return string
     */
    public static function get_objectid_mapping() {
        return base::NOT_MAPPED;
    }
}

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

namespace core\event;

/**
 * Role create event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string name: The name of role.
 *      - string shortname: The shortname of role.
 *      - string archetype: The archetype.
 * }
 *
 * @package    core
 * @since      Moodle 4.2
 * @copyright  2023 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class role_created extends base {
    protected function init() {
        $this->data['objecttable'] = 'role';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('eventrolecreated', 'role');
    }

    public function get_url() {
        return new \moodle_url('/admin/roles/define.php', ['action' => 'view', 'roleid' => $this->objectid]);
    }

    public function get_description() {
        return "The user with id '$this->userid' created the role with id '$this->objectid'.";
    }

    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }

        if (!isset($this->other['shortname'])) {
            throw new \coding_exception('The \'shortname\' value must be set in other.');
        }

        if (!isset($this->other['archetype'])) {
            throw new \coding_exception('The \'archetype\' value must be set in other.');
        }
    }
}

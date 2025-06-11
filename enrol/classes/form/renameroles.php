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

namespace core_enrol\form;

/**
 * Form to customise the course role names.
 *
 * @package    core_enrol
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renameroles extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $courseid = $this->_customdata['id'];
        $roles = $this->_customdata['roles'] ?? [];

        $formdata = new \stdClass();

        $mform->addElement('hidden', 'id', $courseid);
        $mform->setType('id', PARAM_INT);

        foreach ($roles as $role) {
            $settingname = 'role_' . $role->id;
            $mform->addElement('text', $settingname, get_string('yourwordforx', '', $role->localname));
            $mform->setType($settingname, PARAM_TEXT);
            $formdata->{$settingname} = $role->coursealias;
        }

        $mform->addElement('submit', 'submit', get_string('save'));

        $this->set_data($formdata);
    }
}

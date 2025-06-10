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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $fields = $DB->get_records_menu('user_info_field', null, '', 'id, name');

    if (!empty($fields)) {
        $default = key($fields);

        $settings->add(new admin_setting_configselect('smart_import/keypadprofile',
            get_string('keypadprofile', 'gradeimport_smart'),
            get_string('keypadprofile_help', 'gradeimport_smart'),
            $default, $fields)
        );
    }
}

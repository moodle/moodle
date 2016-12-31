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

if ($hassiteconfig) {

    // Basic navigation settings
    require($CFG->dirroot . '/local/iomad/lib/basicsettings.php');

    $settings = new admin_settingpage('local_iomad_settings', get_string('pluginname', 'local_iomad_settings'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext('establishment_code',
                                                get_string('establishment_code', 'local_iomad_settings'),
                                                get_string('establishment_code_help', 'local_iomad_settings'),
                                                '',
                                                PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('iomad_use_email_as_username',
                                                get_string('iomad_use_email_as_username', 'local_iomad_settings'),
                                                get_string('iomad_use_email_as_username_help', 'local_iomad_settings'),
                                                0));
}

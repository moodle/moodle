<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     mod_jupyter
 * @category    admin
 * @copyright   KIB3 StuPro SS2022 Development Team of the University of Stuttgart
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    if ($ADMIN->fulltree) {
        // Defines the plugin settings page - {@link https://docs.moodle.org/dev/Admin_settings}.

        $settings->add(new admin_setting_heading(
            'jupyter_settings_heading',
            get_string('generalconfig', 'jupyter'),
            get_string('generalconfig_desc', 'jupyter')
        ));

        $settings->add(new admin_setting_configtext(
            'mod_jupyter/jupyterhub_url',
            get_string('jupyterhub_url', 'jupyter'),
            get_string('jupyterhub_url_desc', 'jupyter'),
            null,
        ));

        $settings->add(new admin_setting_configtext(
            'mod_jupyter/gradeservice_url',
            get_string('gradeservice_url', 'jupyter'),
            get_string('gradeservice_url_desc', 'jupyter'),
            null,
        ));

        $settings->add(new admin_setting_configpasswordunmask(
            'mod_jupyter/jupyterhub_jwt_secret',
            get_string('jupyterhub_jwt_secret', 'jupyter'),
            get_string('jupyterhub_jwt_secret_desc', 'jupyter'),
            'your-256-bit-secret'
        ));

        $settings->add(new admin_setting_configpasswordunmask(
            'mod_jupyter/jupyterhub_api_token',
            get_string('jupyterhub_api_token', 'jupyter'),
            get_string('jupyterhub_api_token_desc', 'jupyter'),
            "secret-token"
        ));
    }
}

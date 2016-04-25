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
 * Settings
 *
 * This file contains settings used by tool_mobile
 *
 * @package    tool_mobile
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $temp = new admin_settingpage('mobile', new lang_string('mobile', 'admin'), 'moodle/site:config', false);

    // We should wait to the installation to finish since we depend on some configuration values that are set once
    // the admin user profile is configured.
    if (!during_initial_install()) {
        $enablemobiledocurl = new moodle_url(get_docs_url('Enable_mobile_web_services'));
        $enablemobiledoclink = html_writer::link($enablemobiledocurl, new lang_string('documentation'));
        $default = is_https() ? 1 : 0;
        $temp->add(new admin_setting_enablemobileservice('enablemobilewebservice',
                new lang_string('enablemobilewebservice', 'admin'),
                new lang_string('configenablemobilewebservice', 'admin', $enablemobiledoclink), $default));
    }

    $temp->add(new admin_setting_configtext('mobilecssurl', new lang_string('mobilecssurl', 'admin'),
                new lang_string('configmobilecssurl', 'admin'), '', PARAM_URL));
    $ADMIN->add('webservicesettings', $temp);
}

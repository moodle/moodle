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
 * Adds privacy and policies links to admin tree.
 *
 * @package   core_privacy
 * @copyright 2018 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Privacy settings.
    $temp = new admin_settingpage('privacysettings', new lang_string('privacysettings', 'admin'));

    $options = array(
        0 => get_string('no'),
        1 => get_string('yes')
    );
    $url = new moodle_url('/admin/settings.php?section=supportcontact');
    $url = $url->out();
    $setting = new admin_setting_configselect('agedigitalconsentverification',
        new lang_string('agedigitalconsentverification', 'admin'),
        new lang_string('agedigitalconsentverification_desc', 'admin', $url), 0, $options);
    $setting->set_force_ltr(true);
    $temp->add($setting);

    $setting = new admin_setting_agedigitalconsentmap('agedigitalconsentmap',
        new lang_string('ageofdigitalconsentmap', 'admin'),
        new lang_string('ageofdigitalconsentmap_desc', 'admin'),
        // See {@link https://gdpr-info.eu/art-8-gdpr/}.
        implode(PHP_EOL, [
            '*, 16',
            'AT, 14',
            'CZ, 13',
            'DE, 14',
            'DK, 13',
            'ES, 13',
            'FI, 15',
            'GB, 13',
            'HU, 14',
            'IE, 13',
            'LT, 16',
            'LU, 16',
            'NL, 16',
            'PL, 13',
            'SE, 13',
        ]),
        PARAM_RAW
    );
    $temp->add($setting);

    $ADMIN->add('privacy', $temp);

    // Policy settings.
    $temp = new admin_settingpage('policysettings', new lang_string('policysettings', 'admin'));
    $temp->add(new admin_settings_sitepolicy_handler_select('sitepolicyhandler', new lang_string('sitepolicyhandler', 'core_admin'),
        new lang_string('sitepolicyhandler_desc', 'core_admin')));
    $temp->add(new admin_setting_configtext('sitepolicy', new lang_string('sitepolicy', 'core_admin'),
        new lang_string('sitepolicy_help', 'core_admin'), '', PARAM_RAW));
    $temp->add(new admin_setting_configtext('sitepolicyguest', new lang_string('sitepolicyguest', 'core_admin'),
        new lang_string('sitepolicyguest_help', 'core_admin'), (isset($CFG->sitepolicy) ? $CFG->sitepolicy : ''), PARAM_RAW));

    $ADMIN->add('privacy', $temp);
}

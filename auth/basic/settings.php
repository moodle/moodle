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
 * @package    auth_basic
 * @copyright  Dimitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('authsettings', new admin_category('auth_basic', get_string('pluginname', 'auth_basic')));
$settings = new admin_settingpage($section, get_string('menusettings', 'auth_basic'), 'moodle/site:config');

if ($ADMIN->fulltree) {

    $yesno = array(get_string('no'), get_string('yes'));

    $settings->add(new admin_setting_configselect('auth_basic/send401',
        new lang_string('send401', 'auth_basic'),
        new lang_string('send401_help', 'auth_basic'), 0, $yesno)
    );

    $settings->add(new admin_setting_configselect('auth_basic/onlybasic',
            new lang_string('onlybasic', 'auth_basic'),
            new lang_string('onlybasic_help', 'auth_basic'), 0, $yesno)
    );

    $settings->add(new admin_setting_configselect('auth_basic/debug',
            new lang_string('debug', 'auth_basic'),
            new lang_string('debug_help', 'auth_basic'), 0, $yesno)
    );

}
$ADMIN->add('auth_basic', $settings);
$settings = null;

$temp = new admin_externalpage(
    'auth_basic_masterpassword',
    get_string('masterpassword', 'auth_basic'),
    new moodle_url($CFG->wwwroot.'/auth/basic/masterpassword.php')
);

$ADMIN->add('auth_basic', $temp);

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

defined('MOODLE_INTERNAL') || die();


// Checkbox to enable/disable all the Wiris Quizzes question types.
$qtypes = array('essay', 'match', 'multianswer', 'multichoice', 'shortanswer', 'truefalse');
$quizzesdisabled = get_config('question', 'wq_disabled');

if ($quizzesdisabled == '1') {
    foreach ($qtypes as $key => $value) {
        if (get_config('question', $value . 'wiris_disabled') != 1) {
            set_config($value . 'wiris_disabled', 1, 'question');
            set_config('wq_disabled', 1, 'question');
        }
    }
} else {
    foreach ($qtypes as $key => $value) {
        if (get_config('question', $value . 'wiris_disabled') == 1) {
            set_config($value . 'wiris_disabled', 0, 'question');
            set_config('wq_disabled', 0, 'question');
        }
    }
}

$settings->add(new admin_setting_heading(
    'qtype_wq/connectionsettings',
    get_string('connectionsettings', 'qtype_wq'),
    get_string('connectionsettings_text', 'qtype_wq')
));

$settings->add(new admin_setting_configtext(
    'qtype_wq/quizzesserviceurl',
    get_string('quizzesserviceurl', 'qtype_wq'),
    get_string('quizzesserviceurl_help', 'qtype_wq'),
    'http://www.wiris.net/demo/quizzes',
    PARAM_URL
));

$settings->add(new admin_setting_configtext(
    'qtype_wq/quizzeseditorurl',
    get_string('quizzeseditorurl', 'qtype_wq'),
    get_string('quizzeseditorurl_help', 'qtype_wq'),
    'http://www.wiris.net/demo/editor',
    PARAM_URL
));

$settings->add(new admin_setting_configtext(
    'qtype_wq/quizzeshandurl',
    get_string('quizzeshandurl', 'qtype_wq'),
    get_string('quizzeshandurl_help', 'qtype_wq'),
    'http://www.wiris.net/demo/hand',
    PARAM_URL
));

$settings->add(new admin_setting_configtext(
    'qtype_wq/quizzeswirislauncherurl',
    get_string('quizzeswirislauncherurl', 'qtype_wq'),
    get_string('quizzeswirislauncherurl_help', 'qtype_wq'),
    'http://stateful.wiris.net/demo/wiris',
    PARAM_URL
));

$settings->add(new admin_setting_configtext(
    'qtype_wq/quizzeswirisurl',
    get_string('quizzeswirisurl', 'qtype_wq'),
    get_string('quizzeswirisurl_help', 'qtype_wq'),
    'http://www.wiris.net/demo/wiris',
    PARAM_URL
));

// Access provider option. If enabled only loged users can access to Wiris Quizzes services.
$settings->add(new admin_setting_configcheckbox(
    'qtype_wq/access_provider_enabled',
    get_string('access_provider_enabled', 'qtype_wq'),
    get_string('access_provider_enabled_help', 'qtype_wq'),
    '0'
));

$settings->add(new admin_setting_heading(
    'qtype_wq/compatibility_settings',
    get_string('compatibility_settings', 'qtype_wq'),
    get_string('compatibility_settings_text', 'qtype_wq')
));

$settings->add(new admin_setting_configcheckbox(
    'qtype_wq/filtercodes_compatibility',
    get_string('filtercodes_compatibility_enabled', 'qtype_wq'),
    get_string('filtercodes_compatibility_enabled_help', 'qtype_wq'),
    '0'
));

$settings->add(new admin_setting_heading(
    'qtype_wq/troubleshooting_settings',
    get_string('troubleshooting_settings', 'qtype_wq'),
    get_string('troubleshooting_settings_text', 'qtype_wq')
));

$settings->add(new admin_setting_configcheckbox(
    'qtype_wq/debug_mode_enabled',
    get_string('debug_mode_enabled', 'qtype_wq'),
    get_string('debug_mode_enabled_help', 'qtype_wq'),
    '0'
));

$settings->add(new admin_setting_configcheckbox(
    'qtype_wq/log_server_errors',
    get_string('log_server_errors', 'qtype_wq'),
    get_string('log_server_errors_help', 'qtype_wq'),
    '0'
));


if ($CFG->version >= 2012120300 && $CFG->version < 2013051400) {
    $settingslink = 'filtersettingfilterwiris';
} else {
    $settingslink = 'filtersettingwiris';
}
$url = $CFG->wwwroot . '/admin/settings.php?section=' . $settingslink;
$url = '<a href="' . $url . '">MathType filter settings</a>';
$settings->add(new admin_setting_heading('filter_wirisfilterheading', $url, ''));
